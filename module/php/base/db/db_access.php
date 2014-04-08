<?php

/*時間を出す読み込み*/
// include('../Time/time.php');
// /*DBアクセスオブジェクト読み込み*/
// include('../../../../tool/db/DBObject.php');
// /*myslqツール読み込み*/
// include('../../../../tool/db/mysql.php');

include_once(dirname(__FILE__).'../../Time/time.php');
include_once(dirname(__FILE__).'../../../../../tool/db/DBObject.php');
include_once(dirname(__FILE__).'../../../../../tool/db/mysql.php');

$o = new DBObject();
$h = $o -> getHost();
$u = $o -> getUser();
$p = $o -> getPass();
$db = $o -> getDBName();

/*時間クラスをインスタンス化*/
$time = new Time();

/*コネクションを張る*/
$con = new DB($h,$u,$p,$db);
?>
