<?php
/**
 * SCHEDULE_IDを元に
 * 現在教員が端末を通して行った処理を
 * JSONで返す
 * ***/
include ('../db/db_access.php');
include ('../mail/mail.php');

$scheduleId = $_POST ['s'];
// $scheduleId = $_GET ['s'];

// $stateSQL = "SELECT `CALL_START_TIME` , `CALL_END_TIME` , `CHANGE_SEAT_TIME` FROM `CALL_THE_ROLL` WHERE `SCHEDULE_ID` LIKE '" . $scheduleId . "'";
$stateSQL = "SELECT `CALL_START_TIME` , `CALL_END_TIME`  FROM `CALL_THE_ROLL` WHERE `SCHEDULE_ID` LIKE '" . $scheduleId . "'";

$stateResult = $con->query ( $stateSQL );

if (count ( $stateResult ) == 1) {
	/* 何か端末を通してアクションを起こしている */
	$callStart = $stateResult [0] ['CALL_START_TIME'];
	$callEnd = $stateResult [0] ['CALL_END_TIME'];
	// $cst = $stateResult [0] ['CHANGE_SEAT_TIME'];
	// $gt = $stateResult [0] ['GROUPING_TIME'];
	// $ldot = $stateResult [0] ['LAST_DISCUTION_OPEN_TIME'];
	$call = array (
			'START_TIME' => $callStart,
			'END_TIME' => $callEnd 
	);
	$state = array (
			'CALL' => $call 
	// 'CHANGE' => $cst,
	// 'GROUPING' => $gt,
	// 'DISCUTION' => $ldot
		);
	
	/* 出席者 */
	$attendeeSQL = "SELECT ST.GRADE, M.MAJOR_SUBJECT_NAME, ST.STUDENT_ID, ST.FAMILY_NAME, 
			ST.GIVEN_NAME, ST.FULL_NAME, ST.FAMILY_KANA_NAME,ST.GIVEN_KANA_NAME,SE.SEAT_ID, 
			SB.SEAT_BLOCK_NAME, SE.SEAT_ROW, SE.SEAT_COLUMN, A.ATTEND_TIME
						FROM ATTENDEE A, STUDENT_MST ST, MAJOR_SUBJECT_MST M, SEAT_MST SE, SEAT_BLOCK_MST SB
						WHERE A.STUDENT_ID = ST.STUDENT_ID
						AND ST.MAJOR_SUBJECT = M.MAJOR_SUBJECT
						AND A.SEAT_ID = SE.SEAT_ID
						AND SE.SEAT_BLOCK_ID = SB.SEAT_BLOCK_ID
						AND A.`SCHEDULE_ID` LIKE '" . $scheduleId . "'
						ORDER BY `A`.`ATTEND_TIME` ASC ";
	$attJsont = $con->jsonQuery ( $attendeeSQL );
	/*
	 * while($result = $stmt->fetch(PDO::FETCH_ASSOC)){ $attList[]=$result; }
	 */
	/* 欠席者 */
	/* 取得 */
	
	/*教室情報取得*/
	/*SEAT_BLOCが何行何列か*/
	$sbSQL = "SELECT MAX( SB.SEAT_BLOCK_ROW ) , MAX( SB.SEAT_BLOCK_COLUMN ) 
			FROM `SYLLABUS_MST` S, ROOM_MST R, SEAT_BLOCK_MST SB 
			WHERE S.ROOM_ID = R.ROOM_ID 
			AND R.ROOM_ID = SB.ROOM_ID 
			AND S.SCHEDULE_ID = '" . $scheduleId . "'";
	$sbResult = $con->query ( $sbSQL );
	$seatBlockLayout = array (
			'SEAT_BLOCK_ROW_COUNT' => $sbResult [0] ['MAX( SB.SEAT_BLOCK_ROW )'],
			'SEAT_BLOCK_COLUMN_COUNT' => $sbResult [0] ['MAX( SB.SEAT_BLOCK_COLUMN )'] 
	);
	
	/* 各SEAT_BLOCKが何行何列分の座席で構成されているか */
	$sbbSQL = "SELECT SB.SEAT_BLOCK_ID, SB.SEAT_BLOCK_NAME, SB.SEAT_BLOCK_ROW, SB.SEAT_BLOCK_COLUMN
			FROM `SYLLABUS_MST` S, ROOM_MST R, SEAT_BLOCK_MST SB
			WHERE S.ROOM_ID = R.ROOM_ID
			AND R.ROOM_ID = SB.ROOM_ID
			AND S.SCHEDULE_ID = '" . $scheduleId . "'
			ORDER BY `SB`.`SEAT_BLOCK_NAME` ASC ";
	
	// $sbbResultjson = $con->jsonQuery ( $sbbSQL );
	
	$sbbResult = $con->query ( $sbbSQL );
	for($i = 0; $i < count ( $sbbResult ); $i ++) {
		$sbid = $sbbResult [$i] ['SEAT_BLOCK_ID'];
		/* 各ブロックIDの時に座席の最大行列数を返す */
		$seatCountSQL = "SELECT MAX( SEAT_ROW ) , MAX( SEAT_COLUMN )
				FROM `SEAT_MST` WHERE `SEAT_BLOCK_ID` ='" . $sbid . "'";
		$scResult = $con->query ( $seatCountSQL );
		$seatLayout [] = array (
				'SEAT_BLOCK_ID' => $sbid,
				'SEAT_ROW_COUNT' => $scResult [0] ['MAX( SEAT_ROW )'],
				'SEAT_COLUMN_COUNT' => $scResult [0] ['MAX( SEAT_COLUMN )'] 
		);
		
		$sbn = $sbbResult [$i] ['SEAT_BLOCK_NAME'];
		$seatSQL = "SELECT SEAT_ID, SEAT_ROW, SEAT_COLUMN
			FROM `SEAT_MST`
			WHERE `SEAT_BLOCK_ID` = '" . $sbid . "'
			ORDER BY `SEAT_ROW` , SEAT_COLUMN ASC ";
		// $seatList = $con->jsonQuery ( $seatSQL );
		
		$sResult = $con->query ( $seatSQL );
		for($j = 0; $j < count ( $sResult ); $j ++) {
			$seatId = $sResult [$j] ['SEAT_ID'];
			$sitSQL = "SELECT ST.STUDENT_ID, ST.FULL_NAME
				FROM `ATTENDEE` A, STUDENT_MST ST
				WHERE A.STUDENT_ID = ST.STUDENT_ID
				AND A.`SCHEDULE_ID` LIKE '" . $scheduleId . "'
				AND A.`SEAT_ID` ='" . $seatId . "'";
			// $sitJson = $con->jsonQuery ( $sitSQL );
			$sitResult = $con->query ( $sitSQL );
			if (count ( $sitResult ) == 1) {
				$sitJson = array (
						'STUDENT_ID' => $sitResult [0] ['STUDENT_ID'],
						'FULL_NAME' => $sitResult [0] ['FULL_NAME'] 
				);
			} else {
				$sitJson = NULL;
			}
			$row = $sResult [$j] ['SEAT_ROW'];
			$column = $sResult [$j] ['SEAT_COLUMN'];
			$seatList [] = array (
					'SEAT_ID' => $seatId,
					'SEAT_ROW' => $row,
					'SEAT_COLUMN' => $column,
					'ATTENDEE' => $sitJson 
			);
		}
		
		$seatBlockDitaile = array (
				'SEAT_BLOCK_ID' => $sbid,
				'SEAT_BLOCK_NAME' => $sbn,
				'SEAT_BLOCK_ROW' => $sbbResult [$i] ['SEAT_BLOCK_ROW'],
				'SEAT_BLOCK_COLUMN' => $sbbResult [$i] ['SEAT_BLOCK_COLUMN'],
				'SEAT' => $seatList 
		);
		$seatBlockDitaileList [] = array (
				'BLOCK' => $seatBlockDitaile 
		);
	}
	
	/* シートがいくつあるか */
	$l = array (
			'BLOCK_LAYOUT' => $seatBlockLayout,
			"SEAT_LAYOUT" => $seatLayout 
	);
	$r = array (
			"LAYOUT" => $l,
			"DETAILE_INFO" => $seatBlockDitaileList 
	);
	$j = array (
			'ACTION' => $state,
			'ATTENDEE' => $attJsont,
			'ROOM' => $r 
	);
	json ( $j );
} else {
	/* まだ何も開始されていない */
	$j = array (
			'ACTION' => NULL 
	);
	json ( $j );
}

/* JSON */
function json($user) {
	header ( 'Content-type: application/json' );
	echo json_encode ( $user );
}

?>