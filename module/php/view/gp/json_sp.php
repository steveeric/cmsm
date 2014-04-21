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
$pastAtt = array('PAST_ATTEND' => $pastAtt,'ATTEND_TIME' => $attTime);
$position = array('SEAT_ID' => $seatId,'GROUP_NAME' => $gpName,'SEAT_BLOCK_NAME' => $blockName,'SEAT_ROW' => $row,'SEAT_COLUMN' => $column);
$user = array('NOT_SEAT' => $notSeat,'POSITION' => $position,'PAST' => $pastAtt,'STUDENT_ID' => $studentId);
echo json_encode($user);

?>
