<?php

//出席ID
$attendeeId = htmlspecialchars($_POST['att'], ENT_QUOTES, "utf-8");
//マトリックスログID
$matrixLogId = htmlspecialchars($_POST['mat'], ENT_QUOTES, "utf-8");
//入力された値
$inputValue = htmlspecialchars($_POST['inp'], ENT_QUOTES, "utf-8");

//不具合があった場合処理を中断する.
if(is_null($attendeeId) || is_null($matrixLogId) || is_null($inputValue)){
	//postで渡された値がどれかNULLだった場合処理をやめる.
	break;
}

//DBアクセスのコンフィグをインクルード
include_once(dirname(__FILE__).'../../../../../tool/db/DBObject.php');
//DBアクセス用のmysqlをインクルード
include_once(dirname(__FILE__).'../../../../../tool/db/mysql.php');
//マトリックス入力結果を処理するProcessInputValueをインクルード
include_once(dirname(__FILE__).'../ProcessInputValue.php');

/*コネクション取得*/
$o = new DBObject();
$h = $o -> getHost();
$u = $o -> getUser();
$p = $o -> getPass();
$db = $o -> getDBName();
$con = new DB($h,$u,$p,$db);

$process = new ProcessInputValue($con);
$process -> setItem($attendeeId,$matrixLogId,$inputValue);

?>