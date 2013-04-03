<?php
include ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
include_once(dirname(dirname(__FILE__)) ."/orm.php");

class NewsBitrixORM extends ORM{
    protected $IBlockID=1;
    protected $auto_getters = array("NAME"=>"GetNameValue");
    protected $auto_setters = array("NAME"=>"SetNameValue");
    
    public function GetNameValue($value){
        return str_replace(" (имя новости!)", "", $this->_data["NAME"]);
    }
    public function SetNameValue($value){
        $this->_data["NAME"]=$value." (имя новости!)";
        $this->_changed_fields["NAME"]="NAME";
        return true;
    }
}

$ormNews = ORM::Factory("news");
$list=$ormNews->Where("ACTIVE","=","Y")->FindAll();

foreach ($list as $_news){
    $_news->NAME="Новость с номером {$_news->ID}";
    
    echo "<pre>".print_r($_news->AsArray(true),true)."</pre>";
    
    $_news->Save();
}
?>
