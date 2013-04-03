<?php

if (CModule::IncludeModule("iblock")) {
    
};

/* TODO
 * 
 * подгрузка разноименных классов
 * сложные запросы на поиск
 * создание новых
 * проверки, перехват и генерация ошибок
 */

class ORM {

    protected $IBlockID;
    protected $IBlockName;
    protected $arFilter = array();
    protected $arFilterBase = array();
    protected $arOrder = Array("ID" => "ASC");
    protected $arGroupBy = false;
    protected $arNavStartParams = false;
    protected $arSelectFields = Array();
    protected $_res = false;
    protected $_data;
    protected $_data_props;
    protected $_loaded = false;
    protected $_changed_fields = array();
    protected $_changed_props = array();
    protected $_tmp_el = false;
    protected $_error_text = "";
    protected $nochange_name = Array("ID", "IBLOCK_ID");
    protected $standart_fields = Array("ID", "TIMESTAMP_X", "TIMESTAMP_X_UNIX", "MODIFIED_BY",
        "DATE_CREATE", "DATE_CREATE_UNIX", "CREATED_BY", "IBLOCK_ID", "IBLOCK_SECTION_ID",
        "ACTIVE", "ACTIVE_FROM", "ACTIVE_TO", "DATE_ACTIVE_FROM", "DATE_ACTIVE_TO", "SORT",
        "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_PICTURE",
        "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "SEARCHABLE_CONTENT", "WF_STATUS_ID",
        "WF_PARENT_ELEMENT_ID", "WF_LAST_HISTORY_ID", "WF_NEW", "LOCK_STATUS", "WF_LOCKED_BY",
        "WF_DATE_LOCK", "WF_COMMENTS", "IN_SECTIONS", "SHOW_COUNTER", "SHOW_COUNTER_START",
        "CODE", "TAGS", "XML_ID", "EXTERNAL_ID", "TMP_ID", "USER_NAME", "LOCKED_USER_NAME",
        "CREATED_USER_NAME", "LANG_DIR", "LID", "IBLOCK_TYPE_ID", "IBLOCK_CODE", "IBLOCK_NAME",
        "IBLOCK_EXTERNAL_ID", "DETAIL_PAGE_URL", "LIST_PAGE_URL", "CREATED_DATE", "BP_PUBLISHED");

    public function ClearGroup() {
        $this->arGroupBy = false;
        return $this;
    }

    public function Group($by) {
        if (!in_array($by, $this->arGroupBy)) {
            $this->arGroupBy[] = $by;
        }
        return $this;
    }

    public function GetCount() {
        return $this->_res->SelectedRowsCount();
    }

    public function ClearLimit() {
        $this->arNavStartParams = false;
        return $this;
    }

    public function Limit($count, $page = 1) {
        $this->arNavStartParams = Array("nPageSize" => $count, "iNumPage" => $page);
        return $this;
    }

    public function ClearOrder() {
        $this->arOrder = array();
        return $this;
    }

    public function ClearFilter() {
        $this->arFilter = array();
        return $this;
    }

    protected function _PrepareOrderHow($how) {
        $true_array = Array("asc", "nulls,asc", "asc,nulls", "desc", "nulls,desc", "desc,null");

        if (!in_array(strtolower($how), $true_array)) {
            return "ASC";
        }
        return $how;
    }

    protected function _PrepareWhereHow($how) {
        $true_array = Array("", "!", "><", "!><", "=", "%", "?", ">", "<", ">=", "<=");
        if (!in_array($how, $true_array)) {
            return "";
        }
        if ($how == "=")
            return "";
        return $how;
    }

    public function Order($what, $how) {
        $how = $this->_PrepareOrderHow($how);
        $this->arOrder[$what] = $how;
        return $this;
    }

    public function Where($what, $how, $where) {
        $how = $this->_PrepareWhereHow($how);
        if (!in_array($what, $this->standart_fields)) {
            if (strpos("PROPERTY_", $what) === FALSE) {
                $what = "PROPERTY_" . $what . "_VALUE";
            };
        };

        $this->arFilterBase[] = Array(
            "what" => $what,
            "how" => $how,
            "where" => $where
        );
        return $this;
    }

    public function AndWhere($what, $how, $where) {
        return $this->Where($what, $how, $where);
    }

    public function SetIBlockID($id) {
        $ok=false;
        if (is_numeric($id)) {
            if ($id!=0) $ok=true;
        } else {
            $res = CIBlock::GetList(Array(), Array("CODE" => $id));
            $ar_res = $res->Fetch();
            $ok=true;
            $id=$ar_res['ID'];
        }
        if ($ok==true)
        $this->IBlockID = $id;
        return $this;
    }

    public function GetIBlockID($id) {
        return $this->IBlockID;
        return $this;
    }

    public function __construct($id=0) {
        $this->SetIBlockID($id);
    }
    
    static public function Factory($id) {
        $obj = new ORM($id);

        return $obj;
    }

    protected function _LoadDataFromBase() {
        if ($tmp_data = $this->_res->GetNextElement()) {
            $this->_data_props = $tmp_data->GetProperties();
            $this->_data = $tmp_data->GetFields();
            $this->_loaded = true;
        }
        return $tmp_data;
    }

    protected function _PrepareDatas() {
        $this->arFilter['IBLOCK_ID'] = $this->IBlockID;
        foreach ($this->arFilterBase as $item) {
            $this->arFilter[$item['how'] . $item['what']] = $item['where'];
        };
    }

    protected function _FindGo() {
        $this->_PrepareDatas();
        $this->_res = CIBlockElement::GetList(
                        $this->arOrder, $this->arFilter, $this->arGroupBy, $this->arNavStartParams, $this->arSelectFields
        );
    }

    public function Find() {
        $this->_FindGo();
        $this->_LoadDataFromBase();
        return $this;
    }

    public function FindAll() {
        $tmp_array = array();
        $this->_FindGo();
        while ($this->_LoadDataFromBase()) {
            $tmp_array[] = clone $this;
        };
        return $tmp_array;
    }

    public function AsArray($clear_raw_data=false) {
        $tmp = $this->_data;
        if ($clear_raw_data){
            foreach ($tmp as $codename=>&$clear_item){
                if (strpos($codename,"~")!==FALSE){
                    unset($tmp[$codename]);
                }
            }
        }
        foreach ($this->_data_props as $prop) {
            $tmp['PROPS'][$prop['CODE']] = $prop['VALUE'];
            if (isset($prop["VALUE_ENUM_ID"])) {
                $tmp['PROPS'][$prop['CODE'] . "__ID"] = $prop['VALUE_ENUM_ID'];
            }
        }
        return $tmp;
    }

    public function __set($name, $value) {
        if (in_array($name, $this->nochange_name)) {
            throw new Exception("поле {$name} изменять нельзя!");
        }
        $ok = false;
        if (isset($this->_data[$name])) {
            $this->_changed_fields[$name] = $name;
            $this->_data[$name] = $value;
            $ok = true;
        };
        if (isset($this->_data_props[$name])) {
            if (isset($this->_data_props[$name]["VALUE_ENUM_ID"])) {
                $this->_data_props[$name]["VALUE_ENUM_ID"] = $value;
                $property_enum = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID" => $this->IBlockID, "CODE" => $name, "ID" => $value));
                $property_enum_value = $property_enum->GetNext();
                if ($property_enum_value['VALUE']) {
                    $this->_data_props[$name]['VALUE'] = $property_enum_value['VALUE'];
                } else {
                    die("нет такого значения свойства");
                }
            } else {
                $this->_data_props[$name]['VALUE'] = $value;
            };
            $this->_changed_props[$name] = $name;
            $ok = true;
        }

        if (!$ok)
            die("несуществующе поле {$name}");  //@todo эксепшн
        return $this;
    }

    public function __get($name) {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        } elseif (isset($this->_data_props[$name])) {
            return $this->_data_props[$name]['VALUE'];
        } else {
            $name2 = str_replace("__ID", "", $name);
            if (isset($this->_data_props[$name2])) {
                return $this->_data_props[$name2]['VALUE_ENUM_ID'];
            }
        };

        die("несуществующе поле {$name}");  //@todo эксепшн
    }

    public function Delete() {
        $ELEMENT_ID = $this->_data['ID'];
        $this->_loaded = false;
        $this->_changed_fields = array();
        $this->_changed_props = array();
        return CIBlockElement::Delete($ELEMENT_ID);
    }

    public function _Update() {
        if (count($this->_changed_fields) == 0 && count($this->_changed_props) == 0) {
            $this->_error_text = "Ни однои поле не изменено";
            return true;
        }
        $update = array();
        foreach ($this->_changed_fields as &$field_name) {
            $update[$field_name] = $this->_data[$field_name];
        };
        if (count($this->_changed_props) > 0) {
            foreach ($this->_data_props as $prop_name => &$prop_value) {
                if (isset($prop_value["VALUE_ENUM_ID"])) {
                    $update["PROPERTY_VALUES"][$prop_name] = $prop_value['VALUE_ENUM_ID'];
                } else {
                    $update["PROPERTY_VALUES"][$prop_name] = $prop_value['VALUE'];
                }
            }
        };

        if ($this->_tmp_el == false) {
            $this->_tmp_el = new CIBlockElement;
        }

        if ($res = $this->_tmp_el->Update($this->_data['ID'], $update)) {
            $this->_error_text = "";
            return true;
        } else {
            $this->_error_text = $this->_tmp_el->LAST_ERROR;
            return false;
        }
    }

    public function GetLastError() {
        return $this->_error_text;
    }

    public function Save() {
        if ($this->_loaded) {
            $this->_Update();
        } else {
            $this->_Create();
        }
    }

}

?>