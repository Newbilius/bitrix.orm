<?php
include ($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/prolog_before.php");
include_once("orm.php");

function print_pr($data){
    echo "<pre>".print_r($data,true)."</pre>";
}

$news=ORM::Factory(1);
//$news->Where("ID","=",2);
//
//$news->Find();
//print_pr($news);

$news->Where("ID",">","0");

$news_list=$news->FindAll();
foreach ($news_list as $item){
    $tmp=$item->AsArray();
    //print_pr($tmp);
    echo $item->NAME;
    echo "<br>";
    //print_pr($tmp['ID']);
}
//print_pr($news_list);
?>