<?php
/**
 * グルーピングの状態を把握するphpです.
 * 
 * ***/
include (dirname(__DIR__).'../../base/db/db_access.php');

/* 自分が出席しているかをチェック */
$sql = "SELECT A.STUDENT_ID,A.SEAT_ID, A.ATTEND_TIME
FROM `ATTENDEE` A, REGISTER_MST R
WHERE A.STUDENT_ID = R.STUDENT_ID
AND R.RANDOM_NO = '" . $randomNo . "'
AND A.SCHEDULE_ID = '" . $scheduleId . "'";
$result = $con->query ( $sql );

/*初期化*/
//$content = "gp";
$seatId=NULL;
$gpName=NULL;
$blockName=NULL;
$row=NULL;
$column=NULL;
$attTime=NULL;
$studentId=NULL;
$notSeat = -1;
$pastAtt = 0;

if (count ( $result ) == 1) {
	$pastAtt = 1;
	/* 1回はアクセスしたことがある */
	$seatId = $result [0] ['SEAT_ID'];
	$studentId = $result [0] ['STUDENT_ID'];
	if ($seatId == 0) {
		/* まだ座席が割り振られてない */
		$data = $con->assignmentSeat ( $roomId, $content, $attendeeId, $scheduleId, $studentId, $attendTime );
		$seatId = $data [0] ['SEAT_ID'];
		$gpName = $data [0] ['GROUP_NAME'];
		$blockName = $data [0] ['SEAT_BLOCK_NAME'];
		$row = $data [0] ['SEAT_ROW'];
		$column = $data [0] ['SEAT_COLUMN'];
		$notSeat = 0;
	} else {
		$notSeat = 0;
		/* 座席とグループ名を算出する！ */
		$sql = "SELECT SC.GROUP_NAME, SB.SEAT_BLOCK_NAME, SE.SEAT_ROW, SE.SEAT_COLUMN
				FROM `SEAT_CHANGE_MST` SC, SEAT_MST SE, SEAT_BLOCK_MST SB
				WHERE SC.SEAT_ID = SE.SEAT_ID
				AND SE.SEAT_BLOCK_ID = SB.SEAT_BLOCK_ID
				AND SE.SEAT_ID = '" . $seatId . "'";
		$selResul = $con->query ( $sql );
		$gpName = $selResul [0] ['GROUP_NAME'];
		$blockName = $selResul [0] ['SEAT_BLOCK_NAME'];
		$row = $selResul [0] ['SEAT_ROW'];
		$column = $selResul [0] ['SEAT_COLUMN'];
		$attTime = $result [0] ['ATTEND_TIME'];
	}
} else {
	/* この授業で自分が初めのアクセス */
	$sql = "SELECT ATTEND_ID FROM `ATTENDEE` WHERE `SCHEDULE_ID` LIKE '" . $scheduleId . "'";
	$otherResult = $con->query ( $sql );
	$stResul = $con->getStudentId ( $randomNo );
	$studentId = $stResul [0] ['STUDENT_ID'];
	
	$selectSQL = "SELECT YEAR,MONTH,DAY,TIMETABLE_ID,ROOM_ID FROM `SYLLABUS_MST` WHERE `SCHEDULE_ID` = '" . $scheduleId . "' ";
	$selectResult = $con->query ( $selectSQL );
	$roomId = $selectResult [0] ["ROOM_ID"];
	$year = $selectResult [0] ["YEAR"];
	$m = $selectResult [0] ["MONTH"];
	$d = $selectResult [0] ["DAY"];
	$t = $selectResult [0] ["TIMETABLE_ID"];
	$y = substr ( $year, 2 );
	$attendeeId = $y . $m . $d . $t . $studentId;
	$attendTime = $time->getNowDetaileTime ();
	if (count ( $otherResult ) > 0) {
		/* 使用できる座席位置を割り出す */
		$data = $con->assignmentSeat ( $roomId, $content, $attendeeId, $scheduleId, $studentId, $attendTime );
		if (is_null ( $data )) {
			/* 座席がない */
			$notSeat = 1;
		} else {
			$notSeat = 0;
			$seatId = $data [0] ['SEAT_ID'];
			$gpName = $data [0] ['GROUP_NAME'];
			$blockName = $data [0] ['SEAT_BLOCK_NAME'];
			$row = $data [0] ['SEAT_ROW'];
			$column = $data [0] ['SEAT_COLUMN'];
		}
	} else {
		/* この授業始めてなので座席 */
		$data = $con->initSeatChangeUsing ( $roomId, $content, $attendeeId, $scheduleId, $studentId, $attendTime );
		if (is_null ( $data )) {
			/* 座席がない */
			$notSeat = 1;
		} else {
			$notSeat = 0;
			$seatId = $data [0] ['SEAT_ID'];
			$gpName = $data [0] ['GROUP_NAME'];
			$blockName = $data [0] ['SEAT_BLOCK_NAME'];
			$row = $data [0] ['SEAT_ROW'];
			$column = $data [0] ['SEAT_COLUMN'];
		}
	}
}
?>
