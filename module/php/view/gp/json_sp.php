<?php
/**
 * 
 * JSONで返す.
 * 
 * ***/
$scheduleId = $_POST ['s'];
$randomNo = $_POST ['r'];

$content = "gp";


include('../common/check_gp_state.php');

header('Content-type: application/json');
if($date==-1 && $subjectName==-1){
	/*SCHEDULE_IDが現在進行中...*/
	$end = array('END_CLASS_ROOM' => 0,'SUBJECT_NAME' => NULL,'DATE' => NULL);
	$pastAtt = array('PAST_ATTEND' => $pastAtt,'ATTEND_TIME' => $attTime);
	$position = array('SEAT_ID' => $seatId,'GROUP_NAME' => $gpName,'SEAT_BLOCK_NAME' => $blockName,'SEAT_ROW' => $row,'SEAT_COLUMN' => $column);
	$user = array('END_CLASS_STATE' => $end,'NOT_SEAT' => $notSeat,'POSITION' => $position,'PAST' => $pastAtt,'STUDENT_ID' => $studentId);
}else{
	/*SCHEDULE_IDの授業が終わっている場合*/
	$end = array('END_CLASS_ROOM' => 1,'SUBJECT_NAME' => $subjectName,'DATE' => $date);
	$user = array('END_CLASS_STATE' => $end,'NOT_SEAT' => NULL,'POSITION' => NULL,'PAST' => NULL,'STUDENT_ID' => NULL);
}
echo json_encode($user);

?>
