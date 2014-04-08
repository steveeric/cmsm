<?php
include('../../../../module/php/base/db/db_access.php');

/*全画面からわたされたPOSTを受け取る*/
$randomNo = $_POST['r'];
$scheduleId = $_POST['s'];
$unAttId = $_POST['unId'];
$timeTableId = $_POST['t'];
$teacherId = $_POST['tid'];
$roomId = $_POST['rid'];
$seatBlockName = $_POST['seatBlockId'];
$seatRow = $_POST['seatRow'];
$seatColumn = $_POST['seatColumn'];
$studentId = $_POST['id'];
$process = $_POST['p'];

/*過去に出席申請したかをチェックする．*/
$sql = "SELECT ATTEND_TIME FROM `ATTENDEE` WHERE `SCHEDULE_ID` LIKE '".$scheduleId."' AND `STUDENT_ID` = '".$studentId."'";
$pastAttResult = $con -> query($sql);
if(count($pastAttResult) == 1){
	/*すでに出席している*/
	$time = $pastAttResult[0]['ATTEND_TIME'];
	sucessAttend($studentId,$time,$seatBlockName,$seatRow,$seatColumn);
}else{
	if($process == 0){
		/*この授業初めて*/
		/*入力された座席が存在するか*/
		$sql = "SELECT S.SEAT_ID FROM `ROOM_MST`R,SEAT_BLOCK_MST SB,SEAT_MST S
				WHERE R.ROOM_ID = SB.ROOM_ID
				AND SB.SEAT_BLOCK_ID = S.SEAT_BLOCK_ID
				AND SB.SEAT_BLOCK_NAME = '".$seatBlockName."' AND S.SEAT_ROW = '".$seatRow."' AND S.SEAT_COLUMN = '".$seatColumn."' AND R.ROOM_ID = '".$roomId."'";
		$chkSeatIdResult = $con -> query($sql);
		if(count($chkSeatIdResult) == 1){
			/*座席はある*/
			$seatId =
			$sql = "SELECT STUDENT_ID,ATTEND_TIME FROM `ATTENDEE` WHERE `SCHEDULE_ID` LIKE '".$scheduleId."' AND `SEAT_ID` = '".$chkSeatIdResult[0]['SEAT_ID']."'";
			$chkAttSameSeat = $con -> query($sql);
			if(count($chkAttSameSeat) == 1){
				/*既に使われている*/
				$studentId = $chkAttSameSeat[0]['STUDENT_ID'];
				$attTime =  $chkAttSameSeat[0]['ATTEND_TIME'];
				errorSameSeatAtt($randomNo,$studentId,$attTime,$seatBlockName,$seatRow,$seatColumn);
			}else{
				confirmEnter($randomNo,$unAttId,$timeTableId,$scheduleId,$teacherId,$studentId,$roomId,$seatBlockName,$seatRow,$seatColumn);
			}
		}else{
			/*座席が無い*/
			errorNotSeatInDB($randomNo,$seatBlockName,$seatRow,$seatColumn);
		}
		/**/
		/**/
	}else if($process == 1){
		$sql = "SELECT S.SEAT_ID FROM `ROOM_MST`R,SEAT_BLOCK_MST SB,SEAT_MST S
				WHERE R.ROOM_ID = SB.ROOM_ID
				AND SB.SEAT_BLOCK_ID = S.SEAT_BLOCK_ID
				AND SB.SEAT_BLOCK_NAME = '".$seatBlockName."' AND S.SEAT_ROW = '".$seatRow."' AND S.SEAT_COLUMN = '".$seatColumn."' AND R.ROOM_ID = '".$roomId."'";
		$chkSeatIdResult = $con -> query($sql);
		$seatId = $chkSeatIdResult[0]['SEAT_ID'];

		$attId = $unAttId.$timeTableId.$studentId;
		$nowTime = $time -> getNowDetaileTime();
		$sql = "INSERT INTO `ATTENDEE` (`ATTEND_ID`, `SCHEDULE_ID`, `STUDENT_ID`, `SEAT_ID`, `ATTEND_TIME`) VALUES ('".$attId."', '".$scheduleId."', '".$studentId."', '".$seatId."', '".$nowTime."')";
		$attInsertResult = $con -> execute($sql);
		if($attInsertResult){
			sucessAttend($studentId,$nowTime,$seatBlockName,$seatRow,$seatColumn);
		}else{
			/*出席できなかった*/
			mb_language("Japanese");
			mb_internal_encoding("UTF-8");
			if (mb_send_mail("si@primary-meta-works.com", "出席不可", "$sql", "From: si@primary-meat-works.com")) {
				//echo "メールが送信されました。";
				/*ログに残す必要がある*/
			} else {
				//echo "メールの送信に失敗しました。";
				/*ログに残す必要がある*/
			}
		}
	}
}
?>

<?php
/**
 * DBからseatIDが取得できなかった時
 * **/
function errorNotSeatInDB($r,$bname,$row,$column){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>座席使用不可画面</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>入力された座席位置は登録されていません．</div>
	<hr>
		<div>入力内容</div>
	<div>
		ブロック名 : $bname
	</div>
	<div>
		$row 行 - $column 列
	</div>
	<hr>
		<form method='GET' action='ctr.php'>
		<input style='padding: 15px 70px;' type='submit' value='再入力' />
		<input type='hidden' name='r'  value='$r'>
		</form>
</body>
</html>
EOT;
}

/**
 * seatIdがすでに使われていた時
 * **/
function errorSameSeatAtt($r,$studentId,$attTime,$bname,$row,$column){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
	
<title>座席使用不可画面</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>入力された座席位置は使用できません．</div>
	<hr>
		<div>[$studentId]さんが，</div>
		<div>$attTime</div>
		<div>に使用しました．</div>
		<div>入力内容</div>
	<div>
		ブロック名 : $bname
	</div>
	<div>
		$row 行 - $column 列
	</div>
	<div>空いている付近の座席で再出席申請して下さい．</div>
	<hr>
		<form method='GET' action='ctr.php'>
		<input style='padding: 15px 70px;' type='submit' value='再入力' />
		<input type='hidden' name='r'  value='$r'>
		</form>
</body>
</html>
EOT;
}

/**
 * 入力内容を確認
 * **/
function confirmEnter($r,$unId,$nowTimeTableId,$scheduleId,$teacherId,$studentId,$roomId,$bname,$row,$column){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
	
<title>座席申請位置確認画面</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>以下の座席位置で間違いありませんか．</div>
	<hr>
		<div>入力内容</div>
	<div>
		ブロック名 : $bname
	</div>
	<div>
		$row 行 - $column 列
	</div>
	<hr>
		<form method='POST' action='ctrr.php'>
		<input style='padding: 15px 70px;' type='submit' value='はい' />
		<input type='hidden' name='r'  value='$r'>
		<input type='hidden' name='id' value='$studentId'>
		<input type='hidden' name='t' value='$nowTimeTableId'>
		<input type='hidden' name='rid' value='$roomId'>
		<input type='hidden' name='seatBlockId'  value='$bname'>
		<input type='hidden' name='seatRow'  value='$row'>
		<input type='hidden' name='seatColumn'  value='$column'>
		<input type='hidden' name='tid' value='$teacherId'>
		<input type='hidden' name='unId' value='$unId'>
		<input type='hidden' name='s' value='$scheduleId'>
		<input type='hidden' name='p' value='1'>
		</form>
		<form method='GET' action='ctr.php'>
		<input style='padding: 15px 70px;' type='submit' value='訂正' />
		<input type='hidden' name='r'  value='$r'>
		</form>
</body>
</html>
EOT;
}

/**
 * 出席内容確認
 * **/
function sucessAttend($studentId,$time,$bname,$row,$column){
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
	<div>
		学籍番号 : $studentId
	</div>
	<div>
		申請時間 : $time
	</div>
		<div>着座位置</div>
	<div>
		ブロック : $bname
	</div>
	<div>
		$row 行 - $column 列
	</div>
	<hr>
</body>
</html>
EOT;
}
?>