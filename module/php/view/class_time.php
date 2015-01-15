<?php
include('../base/db/db_access.php');

$randomNo = $_POST['r'];
//$randomNo = $_GET['r'];

/*現在の時間と乱数から現在自分が所属するscheduleIdを割です．*/
$timeTableTime = $time -> getTimeTableIdTime();
$timeTableResult = $con -> getNowTimeTableId($timeTableTime);
if(count($timeTableResult) == 0){
	/*本日の授業はもうない*/
	json(0,null);
}else{
	/**/
	$year = $time -> getYear();
	$month = $time -> getMonth();
	$day = $time -> getDay();
	$timeTableId = $timeTableResult[0]['TIMETABLE_ID'];

	/*SCHEDULE_IDを取得する*/
	$sql = "SELECT SCHEDULE_ID FROM `COURSE_REGISTRATION_MST` C,SYLLABUS_MST S,REGISTER_MST R
			WHERE C.SUBJECT_ID = S.SUBJECT_ID AND C.STUDENT_ID = R.STUDENT_ID
			AND R.RANDOM_NO = '".$randomNo."'
					AND S.YEAR = '".$year."'
							AND S.MONTH = '".$month."'
									AND S.DAY = '".$day."'
											AND S.TIMETABLE_ID = '".$timeTableId."'";
	$result = $con -> query($sql);
	if(count($result) == 0){
		/*履修している科目が無い*/
		$take = array('TAKE_COURSE' => 0,'SCHEDULE_ID' => -1);
		json(1,$take);
	}else{
		/*履修している科目がある*/
		$scheduleId = $result[0]['SCHEDULE_ID'];
		$take = array('TAKE_COURSE' => 1,'SCHEDULE_ID' => $scheduleId);
		json(1,$take);
	}
}


/**
 * JSONを出力
 * todayClass : 本日の授業がまだあるか
 * 0 : 無い
 * 1 : ある
 *
 * ***/
function json($todayClass,$take){
	header('Content-type: application/json');
	$user = array('TODAY_CLASS' => $todayClass,'TAKE' => $take);
	echo json_encode($user);
}

?>

