<?php

	/*
	* 作成日 : 2015年1月19日
	* status_matrix.php
	* マトリックスモード状態を確認するphpです.
	*
	*/

	//DBこねっくしょんモジュールセット読み込み.
	include_once('../base/db/db_access.php');

	$scheduleId = $_GET['s'];

	//授業IDをキーに現在の授業内でのマトリックス状態を取得する.
	/*$sql = "SELECT `MATRIX_BORD_ID`,`START_MATRIX_DATE_TIME`
			,`END_MATRIX_DATE_TIME`,`MAX_LIMIT_INPUT_MATRIX`
			FROM `SYLLABUS_MST`
			WHERE SCHEDULE_ID LIKE '".$scheduleId."'";*/
	$sql = "SELECT SY.`YEAR`,SY.`MONTH`,SY.`DAY`,SY.`WEEK`,SU.`SUBJECT_NAME`
	,SY.`MATRIX_BORD_ID`,SY.`START_MATRIX_DATE_TIME` ,SY.`END_MATRIX_DATE_TIME`,SY.`MAX_LIMIT_INPUT_MATRIX`
	FROM `SYLLABUS_MST`SY,SUBJECT_MST SU
	WHERE SY.SCHEDULE_ID LIKE '".$scheduleId."'
	AND SY.SUBJECT_ID = SU.SUBJECT_ID ";

	//マトリックス状態の結果
	$result = $con -> query($sql);

	$year = $result[0]["YEAR"];
	$month = $result[0]["MONTH"];
	$day = $result[0]["DAY"];
	$week = $result[0]["WEEK"];
	$subjectName = $result[0]["SUBJECT_NAME"];
	$matrixBordId = $result[0]["MATRIX_BORD_ID"];
	$startDateTime = $result[0]["START_MATRIX_DATE_TIME"];
	$endDateTime = $result[0]["END_MATRIX_DATE_TIME"];
	$limitinput = $result[0]["MAX_LIMIT_INPUT_MATRIX"];

	//初期化
	$matrixContentIlligalResult = null;
	if((!is_null($endDateTime))){
		//終了していた場合.
		//本日の出席者数取得
		$todayAttendeeSQL = "SELECT STUDENT_ID
								FROM `ATTENDEE`
								WHERE `SCHEDULE_ID` LIKE '".$scheduleId."'";
		$todayAttendeeResult = $con -> query($todayAttendeeSQL);
		//本日の出席者数
		$todayAttendeeCount = count($todayAttendeeResult);

		//不正者取得
		$illigalAttendeeSQL = "SELECT STUDENT_ID
								FROM `ATTENDEE`
								WHERE `SCHEDULE_ID` LIKE '".$scheduleId."' AND `RESULT_INPUT_MATRIX` != 1 ";
		$illigalResult = $con -> query($illigalAttendeeSQL);
		$illigalArray = array();
		for($i = 0; $i < count($illigalResult); $i++){
			$illigalArray[] = $illigalResult[$i]["STUDENT_ID"];
		}
		$matrixContentIlligalResult = array('TODAY_ATTENDEE_COUNT' => $todayAttendeeCount,'ILLIGAL_STUDENT' => $illigalArray);
	}

	header('Content-type: application/json');
	$user = array('YEAR' => $year
		,'MONTH' => $month
		,'DAY' => $day
		,'WEEK' => $week
		,'SUBJECT_NAME' => $subjectName
		,'MATRIX_BORD_ID' => $matrixBordId
		,'START_MATRIX_DATE_TIME' => $startDateTime
	 	,'END_MATRIX_DATE_TIME' => $endDateTime
	 	,'MAX_LIMIT_INPUT_MATRIX' =>$limitinput
	 	,'ILLIGAL' =>$matrixContentIlligalResult);
	echo json_encode($user);

?>