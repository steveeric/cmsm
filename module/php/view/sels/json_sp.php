<?php
/**
 * 座席指定の状態と座席を割り振るphpです.
 *
 * ***/
$scheduleId = $_POST ['s'];
//$scheduleId = "0001030110KK020140004";
$randomNo = $_POST ['r'];
//$scheduleId = $_GET ['s'];
//$randomNo = $_GET ['r'];

$content="sels";

include('../common/check_gp_state.php');

header('Content-type: application/json');
if($date==-1 && $subjectName==-1){
	/*SCHEDULE_IDが現在進行中...*/
	$end = array('END_CLASS_ROOM' => 0,'SUBJECT_NAME' => NULL,'DATE' => NULL);
	$pastAtt = array('PAST_ATTEND' => $pastAtt,'ATTEND_TIME' => $attTime);
	$position = array('SEAT_ID' => $seatId,'SEAT_BLOCK_NAME' => $blockName,'SEAT_ROW' => $row,'SEAT_COLUMN' => $column);
	$user = array('END_CLASS_STATE' => $end,'NOT_SEAT' => $notSeat,'POSITION' => $position,'PAST' => $pastAtt,'STUDENT_ID' => $studentId);
}else{
	/*SCHEDULE_IDの授業が終わっている場合*/
	$end = array('END_CLASS_ROOM' => 1,'SUBJECT_NAME' => $subjectName,'DATE' => $date);
	$user = array('END_CLASS_STATE' => $end,'NOT_SEAT' => NULL,'POSITION' => NULL,'PAST' => NULL,'STUDENT_ID' => NULL);
}
echo json_encode($user);
?>
