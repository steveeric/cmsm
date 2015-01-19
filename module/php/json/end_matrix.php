<?php

	/*
	* 作成日 : 2015年1月19日
	* end_matrix.php
	* マトリックスモードを終了するphpです.
	*
	*/

	//DBこねっくしょんモジュールセット読み込み.
	//$con : コネクションクラス $time : 時間クラス
	include_once('../base/db/db_access.php');

	$scheduleId = $_GET['s'];

	//
	$sql = "SELECT END_MATRIX_DATE_TIME FROM `SYLLABUS_MST` WHERE `SCHEDULE_ID` LIKE '".$scheduleId."'";
	$result = $con -> query($sql);
	$endTime = $result[0]["END_MATRIX_DATE_TIME"];

	if(is_null($endTime)){
		//まだ終了していなければ
		$sql = "UPDATE `SYLLABUS_MST`
				SET `END_MATRIX_DATE_TIME` = '".$time -> getNowDateTime()."'
				WHERE `SCHEDULE_ID` LIKE '".$scheduleId."' ";
		$updateRes = $con -> execute($sql);
	}

	//正常
	$resultProcess = 0;

	header('Content-type: application/json');
	$user = array('RESULT_PROCESS'=>$resultProcess);
	echo json_encode($user);

?>