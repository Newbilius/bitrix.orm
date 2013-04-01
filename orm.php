<?php

if (CModule::IncludeModule("iblock")){};

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

    public function Where($what,$how,$where){
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
    