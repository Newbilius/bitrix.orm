<?php
include ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
include_once(dirname(dirname(__FILE__)) ."/orm.php");

$ormNews = ORM::Factory("news");
$list=$ormNews->Where("ACTIVE","=","Y")->FindAll();

foreach ($list as $_news){   
    echo "<pre>".print_r($_news->AsArray(true),true)."</pre>";
}
?>
