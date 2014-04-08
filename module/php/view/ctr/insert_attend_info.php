<?php
/**
 * 出席申請内容をDBにinsertし，
 * 着席位置をjsonで返すphpです．
 *
 * **/
$dir =  dirname(__FILE__);
$path =  "../../../module/php/base/db/db_access.php";
include($path);

/*POSTされたでーたを取得*/
//$scheduleId = $_GET['s'];
//$randomNo = $_GET['r'];
$scheduleId = $_POST['s'];
$randomNo = $_POST['r'];
$seatId = $_POST['sid'];

/*$scheduleId = $_GET['s'];
$randomNo = $_GET['r'];
$seatId = $_GET['sid'];*/

$state = -1;
$nowTime=null;
$used=0;

//出席で使用した座席が現在誰かに使われていないかをチェックする
$sameSlectSQL = "SELECT STUDENT_ID FROM `ATTENDEE` WHERE `SCHEDULE_ID` = '".$scheduleId."' AND `SEAT_ID` = '".$seatId."' ";
$sameResult = $con -> query($sameSlectSQL);
if(count($sameResult)==1){
	/*誰かがすでに座席を使用している*/
	$sate=0;
	$used = array('USED_STATE' => 1,'USES_STUDET_ID' => $sameResult[0]["STUDENT_ID"]);
}else{
	$used = array('USED_STATE' => 0,'USES_STUDET_ID' => NULL);
	//学籍番号を取得する
$sql = "SELECT STUDENT_ID FROM `REGISTER_MST` WHERE `RANDOM_NO` LIKE '".$randomNo."' ";
$result = $con -> query($sql);

if(count($result)==1){
	$studentId = $result[0]["STUDENT_ID"];
	//attendId用のアイテムを取得する
	$selectSQL = "SELECT YEAR,MONTH,DAY,TIMETABLE_ID FROM `SYLLABUS_MST` WHERE `SCHEDULE_ID` = '".$scheduleId."' ";
	$selectResult = $con -> query($selectSQL);
	if(count($selectResult)==1){
		$year = $selectResult[0]["YEAR"];
		$m = $selectResult[0]["MONTH"];
		$d = $selectResult[0]["DAY"];
		$t = $selectResult[0]["TIMETABLE_ID"];
		$y=substr($year, 2);

		//attendIdを作成
		$attendeeId = $y.$m.$d.$t.$studentId;
		/*日付けを日本に設定*/
		date_default_timezone_set('Asia/Tokyo');
		$nowTime = date("Y-m-d H:i:s");
		$insertSQL = "INSERT INTO `ATTENDEE` (`ATTEND_ID`, `SCHEDULE_ID`, `STUDENT_ID`, `SEAT_ID`, `ATTEND_TIME`)
				VALUES ('".$attendeeId."', '".$scheduleId."', '".$studentId."', '".$seatId."', '".$nowTime."')";
		$insertResult = $con -> execute($insertSQL);
		if($insertResult){
			$state = 0;
		}else{
			/*DBに問題発生*/
			$state = 1;
		}
	}else{
		/*DBに問題発生*/
		$state = 1;
	}
}else{
	/*DBに問題発生*/
	$state = 1;
}
}

$user = array('STATE' => $state,'ATTEND_TIME' => $nowTime,'USED' => $used);

//jsonとして出力
header('Content-type: application/json');
echo json_encode($user);

?>