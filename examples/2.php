<?php
include ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
include_once(dirname(dirname(__FILE__)) ."/orm.php");

$ormNews = ORM::Factory(4);
$list=$ormNews->Where("WIDTH","=","140")->FindAll();

foreach ($list as $_news){
    $_news->NAME="Новость с номером {$_news->ID}";
    $_news->WIDTH=24;
    echo "<pre>".print_r($_news->AsArray(true),true)."</pre>";
}
?>
