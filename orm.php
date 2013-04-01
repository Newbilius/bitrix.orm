<?php

if (CModule::IncludeModule("iblock")){};

class ORMOptions{
    public static $standart_fields=Array("ID","TIMESTAMP_X","TIMESTAMP_X_UNIX","MODIFIED_BY",
        "DATE_CREATE","DATE_CREATE_UNIX","CREATED_BY","IBLOCK_ID","IBLOCK_SECTION_ID",
        "ACTIVE","ACTIVE_FROM","ACTIVE_TO","DATE_ACTIVE_FROM","DATE_ACTIVE_TO","SORT",
        "NAME","PREVIEW_PICTURE","PREVIEW_TEXT","PREVIEW_TEXT_TYPE","DETAIL_PICTURE",
        "DETAIL_TEXT","DETAIL_TEXT_TYPE","SEARCHABLE_CONTENT","WF_STATUS_ID",
        "WF_PARENT_ELEMENT_ID","WF_LAST_HISTORY_ID","WF_NEW","LOCK_STATUS","WF_LOCKED_BY",
        "WF_DATE_LOCK","WF_COMMENTS","IN_SECTIONS","SHOW_COUNTER","SHOW_COUNTER_START",
        "CODE","TAGS","XML_ID","EXTERNAL_ID","TMP_ID","USER_NAME","LOCKED_USER_NAME",
        "CREATED_USER_NAME","LANG_DIR","LID","IBLOCK_TYPE_ID","IBLOCK_CODE","IBLOCK_NAME",
        "IBLOCK_EXTERNAL_ID","DETAIL_PAGE_URL","LIST_PAGE_URL","CREATED_DATE","BP_PUBLISHED");
}

class ORM {
    protected $IBlockID;
    protected $arFilter = array();
    protected $arFilterBase = array();
    protected $arOrder = Array("ID" => "ASC");
    protected $arGroupBy = false;
    protected $arNavStartParams = false;
    protected $arSelectFields = Array();
    
    protected $_res = false;
    protected $_data;
    protected $_data_props;
    
    protected $_changed_fields=array();
    protected $_changed_props=array();
    
    protected function _PrepareWhereHow($how){
        $true_array=Array("","!","><","!><","=", "%", "?",">", "<", ">=", "<=");
        if (!in_array($how, $true_array)){
            return "";
        }
        if ($how=="=") return "";
        return $how;
    }
    
    public function Where($what,$how,$where){
        $how=$this->_PrepareWhereHow($how);
        if (!in_array($what, ORMOptions::$standart_fields)){
            if (strpos("PROPERTY_", $what)===FALSE){
                $what="PROPERTY_".$what."_VALUE";
            };
        };
        
        $this->arFilterBase[]=Array(
            "what"=>$what,
            "how"=>$how,
            "where"=>$where
        );
    }
    
    public function SetIBlockID($id) {
        $this->IBlockID = $id;
    }

    public function GetIBlockID($id) {
        return $this->IBlockID;
    }

    static public function Factory($id) {
        $obj = new ORM();
        if (is_numeric($id)) {
            $obj->SetIBlockID($id);
        }
        return $obj;
    }

    protected function _LoadDataFromBase() {
        if ($tmp_data = $this->_res->GetNextElement()) {
            $this->_data_props=$tmp_data->GetProperties();
            $this->_data = $tmp_data->GetFields();
        }
        return $tmp_data;
    }

    protected function _PrepareDatas() {
        $this->arFilter['IBLOCK_ID'] = $this->IBlockID;
        foreach ($this->arFilterBase as $item){
            $this->arFilter[$item['how'].$item['what']]=$item['where'];
        };
    }

    protected function _FindGo() {
        $this->_PrepareDatas();
        print_pr($this->arFilter);
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

    public function AsArray() {
        $tmp=$this->_data;
        foreach ($this->_data_props as $prop){
            $tmp['PROPS'][$prop['CODE']]=$prop['VALUE'];
        }
        return $tmp;
    }

    public function __set($name, $value) {
        $ok=false;
        if (isset($this->_data[$name])) {
            $this->_changed_fields[$name] = $name;
            $this->_data[$name] = $value;
            $ok=true;
        } 
        
        if (!$ok) die("несуществующе поле {$name}");  //@todo эксепшн
    }

    public function __get($name) {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        };
        if (isset($this->_data_props[$name])){
            return $this->_data_props[$name]['VALUE'];
        }
        
        die("несуществующе поле {$name}");  //@todo эксепшн
    }

}

?>
    