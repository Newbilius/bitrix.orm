<?php

include ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
include_once("orm.php");

function print_pr($data) {
    echo "<pre>" . print_r($data, true) . "</pre>";
}

class NewsBitrixORM extends ORM{
    protected $IBlockID=1;
    
    public function __construct($id = 0) {
        parent::__construct($id);
    }
}

$news = ORM::Factory("news");
$news->GetByID(102);
//$news->NAME="новость теста завтрашнего дня";
//$news->DETAIL_TEXT="<span style='colore:red;'>каналья!</span>";
//$news->DETAIL_TEXT_TYPE="html";
//$news->STRING0=array("12");
$news->STRING0=array("12");
$news->AddToArrayValue("STRING0",16);
//$news->AddToArrayValue("STRING0",'тоже строчка');
//$news->AddToArrayValue("STRING0",24);
//$news->CODE="testcode";

/*
 * $news = ORM::Factory(4);
$news->Where("ID","=","101")->Find();
$news->NAME="тест 2 возвращается";
$news->COLOR = "ночной баклажан";
$news->WIDTH=24;
$news->NAME="тестовое предложение";
$news->COLOR = "хитрого пони";
$news->WIDTH = 25;
*/
print_pr($news->AsArray());
//$news->Order("ID", "desc")->Where("ID", "=", "96")->Find();
//$news->NAME="новая новость 1";
//$news->Where("ID", "=", 2)->Find();
//print_pr($news->AsArray(true));
//$news->Order("ID", "desc")->Where("ID", "=", "96")->Find();
//echo "<hr>";
//$news->NAME="Орион Чокопай";
//$news->ID="9999";
//$news->WIDTH = 25;
//$news->COLOR = "серобуромалиновый";
//$news->NAME="";
if (!$news->Save()) 
    {
    print_pr($news->GetLastError());
}
/*
  $news=ORM::Factory(1);
  $news->Order("ID", "desc");
  $news->Limit(2,2);
  $news->ClearLimit();
  $news_list=$news->FindAll();

  foreach ($news_list as $item){
  $tmp=$item->AsArray();
  echo $item->NAME." (".$item->ID.")";
  echo "<br>";
  }
  echo "<hr>Всего:".$news->GetCount();
 */
/*
  $news=ORM::Factory(1);
  $data=$news->Where("ID","=",100)->Find();
  $data->Delete();
  print_pr($data->ASArray());
 */

//$news=ORM::Factory("furniture_offers");
//$news->Where("WIDTH", "", "180");
//$news_list=$news->FindAll();

/*
  foreach ($news_list as $item){
  $item->NAME.=' (Тестинг\'95)';
  $item->WIDTH+=42;
  $tmp=$item->AsArray();
  print_pr($tmp);
  echo $item->COLOR;
  } */
/*
  $news=ORM::Factory(1);
  $news->Where("ID","=",2);
  $tmp=$item->AsArray();
  foreach ($tmp as $code=>$tmp_item){
  if (strpos($code, "~")===FALSE)
  echo '"'.$code.'",';;
  };
 */
//
//$news->Find();
//print_pr($news);
//$news->Where("ID",">","0");
/*
  $news_list=$news->FindAll();
  foreach ($news_list as $item){
  $tmp=$item->AsArray();
  //print_pr($tmp);
  echo $item->NAME;
  echo "<br>";
  //print_pr($tmp['ID']);
  } */
//print_pr($news_list);
?>