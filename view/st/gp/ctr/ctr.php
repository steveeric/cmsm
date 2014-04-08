<?php
include ('../../../../module/php/base/db/db_access.php');
include ('../../../../module/php/base/teacher/TeacherObject.php');

/* 乱数 */
$randomNo = $_GET ['r'];

$t = new Time ();
/* TimeTableIdを得るために */
$time = $t->getTimeTableIdTime ();

$teacher = new Teacher ();
$teacherId = $teacher->getTeacherId ();
$nowTimeTableResult = $con->getNowTimeTableId ( $time );
$nowTimeTableId = $nowTimeTableResult [0] ['TIMETABLE_ID'];
$year = $t->getYear ();
$y = substr ( $year, 2, 3 );
$month = $t->getMonth ();
$day = $t->getDay ();

/**
 * 現在の日時からスケジュールIDを取得
 * *
 */
$nowScheduleResult = $con->getNowScheduleId ( $teacherId, $year, $month, $day, $nowTimeTableId );
$scheduleId = $nowScheduleResult [0] ['SCHEDULE_ID'];
if ($scheduleId == null) {
	endClassTime ();
} else {
	/* 乱数をから学籍番号を調べる */
	$checkResult = $con->checkExistenceRegisterInfo ( $randomNo );
	$studentId = $checkResult [0] ['STUDENT_ID'];
	/* 既に出席しているかを調べる */
	$sql = "SELECT CALL_START_TIME,CALL_END_TIME FROM `CALL_THE_ROLL` WHERE `SCHEDULE_ID` LIKE '" . $nowScheduleResult [0] ['SCHEDULE_ID'] . "' ";
	$callResult = $con->query ( $sql );
	if (is_null ( $callResult [0] ['CALL_END_TIME'] )) {
		/* 出席調査中．．． */
		/*出席申請したかを調べる*/
		$attResult = $con->getAttendInfo ( $scheduleId, $studentId );
		if (count ( $attResult ) == 0) {
			/* まだ出席申請していない */
			$roomSQL = "SELECT R.ROOM_ID,B.BUILDING_NAME,R.ROOM_NAME
				FROM `SYLLABUS_MST` SC,ROOM_MST R,BUILDING_MST B
				WHERE SC.ROOM_ID = R.ROOM_ID
				AND R.BUILDING_ID = B.BUILDING_ID
				AND SC.SCHEDULE_ID LIKE '" . $scheduleId . "' ";
			$roomResult = $con->query ( $roomSQL );
			$roomId = $roomResult [0] ["ROOM_ID"];
			$roomName = $roomResult [0] ["BUILDING_NAME"] . $roomResult [0] ["ROOM_NAME"];
			
			$selectSeatBLockSQL = "SELECT B.SEAT_BLOCK_ID,B.SEAT_BLOCK_NAME
				FROM `SYLLABUS_MST` S,SEAT_BLOCK_MST B WHERE S.ROOM_ID = B.ROOM_ID
				AND S.SCHEDULE_ID = '" . $scheduleId . "' ORDER BY `B`.`SEAT_BLOCK_NAME` ASC ";
			$seatResult = $con->query ( $selectSeatBLockSQL );
			
			for($i = 0; $i < count ( $seatResult ); $i ++) {
				$seatBlockId = $seatResult [$i] ["SEAT_BLOCK_ID"];
				$saetBlockName = $seatResult [$i] ["SEAT_BLOCK_NAME"];
				$seatBlockList [] = array (
						$saetBlockName => $saetBlockName 
				);
			}
			
			attendApplicationScreen ( $randomNo, $y, $month, $day, $nowTimeTableId, $scheduleId, $teacherId, $studentId, $roomId, $roomName, $seatBlockList );
		} else {
			/* 既に出席申請した */
			$studentId = $attResult [0] ['STUDENT_ID'];
			$time = $attResult [0] ['ATTEND_TIME'];
			$bname = $attResult [0] ['SEAT_BLOCK_NAME'];
			$row = $attResult [0] ['SEAT_ROW'];
			$column = $attResult [0] ['SEAT_COLUMN'];
			sucessAttend ( $studentId, $time, $bname, $row, $column );
		}
	} else {
		/* 出席調査が終了している */
		/*現在のSCHEDULE_IDの時に出席したかを調べる*/
		$attResult = $con->getAttendInfo ( $scheduleId, $studentId );
		if (is_null ( $attResult [0] ['ATTEND_TIME'] )) {
			/* 出席していない */
			endAttend ( $callResult [0] ['CALL_END_TIME'] );
		} else {
			/* 出席していた */
			$studentId = $attResult [0] ['STUDENT_ID'];
			$time = $attResult [0] ['ATTEND_TIME'];
			$bname = $attResult [0] ['SEAT_BLOCK_NAME'];
			$row = $attResult [0] ['SEAT_ROW'];
			$column = $attResult [0] ['SEAT_COLUMN'];
			sucessAttend ( $studentId, $time, $bname, $row, $column );
		}
	}
}
/**
 * 出席申請画面を出力する*
 */
function attendApplicationScreen($randomNo, $y, $month, $day, $nowTimeTableId, $scheduleId, $teacherId, $studentId, $roomId, $roomName, $seatBlockNameList) {
	echo "<html>";
	echo "<head>";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
	echo "<meta name='viewport'	content='width=200px, initial-scale=1, maximum-scale=2'>";
	echo "<meta http-equiv='pragma' content='no-cache' />";
	echo "<meta http-equiv='cache-control' content='no-cache' />";
	echo "<meta http-equiv='expires' content='0' />";
	echo "<title>出席申請画面</title>";
	echo "</head>";
	echo "<body style='width: 200px;'>";
	echo "<form method='POST' action='ctrr.php'>";
	echo "<div>着席位置を入力して下さい．</div>";
	echo "<hr>";
	echo "<div>";
	echo "教室 : " . $roomName;
	echo "</div>";
	echo "<div>";
	echo disp_list ( $seatBlockNameList );
	echo "</div>";
	echo "<div>　<input type='text' name='seatRow' style='ime-mode: disabled' maxlength='2' size='5'> 行 - <input type='text' name='seatColumn' style='ime-mode: disabled' maxlength='2' size='5'> 列</div>";
	echo "<hr>";
	echo "<input style='padding: 15px 70px;' type='submit' value='申請' />";
	echo "<input type='hidden' name='id' value='" . $studentId . "'>";
	echo "<input type='hidden' name='r' value='" . $randomNo . "'>";
	echo "<input type='hidden' name='t' value='" . $nowTimeTableId . "'>";
	echo "<input type='hidden' name='rid' value='" . $roomId . "'>";
	echo "<input type='hidden' name='tid' value='" . $teacherId . "'>";
	echo "<input type='hidden' name='unId' value='" . $y . $month . $day . "'>";
	echo "<input type='hidden' name='s' value='" . $scheduleId . "'>";
	echo "<input type='hidden' name='p' value='0'>";
	echo "</form>";
	echo "</body>";
	echo "</html>";
	
	// disp_list($seatBlockNameList);
}
function disp_list($seatBlockNameList) {
	echo "ブロック : <select name=seatBlockId>";
	foreach ( $seatBlockNameList as $key => $value ) {
		foreach ( $value as $k => $v ) {
			echo "<option value='$k'>$v</option>";
		}
	}
	echo "</select>";
}

/**
 * 出席内容確認
 * *
 */
function sucessAttend($studentId, $time, $bname, $row, $column) {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>出席申請確認画面</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>以下の内容で出席しました．</div>
	<hr>
		<div>着座位置</div>
	<div>
		学籍番号 : $studentId
	</div>
	<div>
		申請時間 : $time
	</div>
	<div>
		ブロック名 : $bname
	</div>
	<div>
		$row 行 - $column 列
	</div>
	<hr>
</body>
</html>
EOT;
}

/**
 * 出席内容確認
 * *
 */
function endAttend($endTime) {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>出席申請終了画面</title>
</head>
<body style="width: 200px;">
	<div>出席受付終了</div>
	<hr>
		<div>出席申請を終了しました．</div>
		<div>時間 : $endTime</div>
	<hr>
</body>
</html>
EOT;
}

/**
 * 時間外
 * *
 */
function endClassTime() {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>授業時間外</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>現在は授業時間外です.</div>
</body>
</html>
EOT;
}
?>