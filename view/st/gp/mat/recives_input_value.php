<?php

	$randomNo = htmlspecialchars($_GET['r'], ENT_QUOTES, "utf-8");
	$scheduleId = htmlspecialchars($_GET['s'], ENT_QUOTES, "utf-8");

	$attendeeId = htmlspecialchars($_GET['att'], ENT_QUOTES, "utf-8");
	$matrixLogId = htmlspecialchars($_GET['mat'], ENT_QUOTES, "utf-8");
	$inputValue = htmlspecialchars($_GET['inp'], ENT_QUOTES, "utf-8");
	/*
	$randomNo = $_GET['r'];
	$scheduleId = $_GET['s'];

	$attendeeId = $_GET['att'];
	$matrixLogId = $_GET['mat'];
	$inputValue = $_GET['inp'];
	*/

	if(is_null($randomNo) || is_null($scheduleId)
		|| is_null($attendeeId) || is_null($matrixLogId)
		|| is_null($inputValue)){
		exit("エラー");
	}


	//ドメインクラス読み込み
	include_once(dirname(__FILE__).'../../../../../tool/domain/Domain.php');
	//DBオブジェクトクラス読み込み
	include_once(dirname(__FILE__).'../../../../../tool/db/DBObject.php');
	//mysqlクラス読み込み
	include_once(dirname(__FILE__).'../../../../../tool/db/mysql.php');
	//マトリックスモジュール読み込み
	include_once(dirname(__FILE__).'../../../../../module/php/view/matrix/ProcessInputValue.php');

	//ドメイン
	$domain = new Domain();

   	$o = new DBObject();
	$h = $o -> getHost();
	$u = $o -> getUser();
	$p = $o -> getPass();
	$db = $o -> getDBName();
	$con = new DB($h,$u,$p,$db);

	$processInputValue = new ProcessInputValue($con);
	$processInputValue -> setItem($attendeeId,$matrixLogId,$inputValue);

	$str = "Location: http://".$domain -> getDomain()."/cmsm/view/st/gp/mat/m.php?r=".$randomNo."&s=".$scheduleId;
	header( "HTTP/1.1 301 Moved Permanently" );
	header( $str );
?>