<?php

	/*
	* 作成日 : 2015年1月19日
	* start_matrix.php
	* マトリックスモードを開始するphpです.
	*
	*/

  //SYLLABUS_MSTのアクションモード変更モジュールをインクルード
  include_once(dirname(__FILE__).'../../run/ChangeAction.php');
  //DBこねっくしょんモジュールセット読み込み.
  include_once('../base/db/db_access.php');

  $scheduleId = $_GET['s'];

  $changeAction = new ChangeAction($con);
  //マトリックスID
  $matrixActionId = $changeAction -> MATRIX;

  $changeAction -> changeActionId($matrixActionId,$scheduleId);
  //正常
  $resultProcess = 0;

	header('Content-type: application/json');
	$user = array('RESULT_PROCESS'=>$resultProcess);
	echo json_encode($user);
?>