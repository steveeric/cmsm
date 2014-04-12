<?php

include('../db/db_access.php');
include('./courseRegister.php');
include('./changeScreenContent.php');
include('../mail/mail.php');

$scheduleId = $_POST['s'];
$situation = $_POST['si'];

/*現在の時刻を取得*/
$nowTime = $time -> getNowDetaileTime();
$courseObject = new CourseRegister();
/*履修者を取得する*/
$courseList = $courseObject -> getCourseRegister($con,$scheduleId);
$user="";
if($situation == 0){
	/*出席調査開始*/
	if(count($courseList)==0){
		/*履修者が一人もいない*/
		$p = array('RESULT' => 1, 'TIME' => NULL);
		$user =  array('TAKE_COURSE' => 1,'PROCESS' => $p);
		json($user);
	}else{
		$mobileScreen = new ChangeScreenContent();
		$result = $mobileScreen -> changeCTR($con, $courseList, $scheduleId);
		if($result){
			/*履修者はいる*/
			$insertCallStartSQL = "INSERT IGNORE INTO `CALL_THE_ROLL` (`SCHEDULE_ID`, `CALL_START_TIME`, `CALL_END_TIME`) VALUES ('".$scheduleId."', '".$nowTime."', NULL)";
			$callStartResult = $con -> execute($insertCallStartSQL);
			if ($callStartResult){
				/*正常に出席調査を開始*/
				$p = array('RESULT' => 0,'TIME' => $nowTime);
				$user =  array('TAKE_COURSE' => 0,'PROCESS' => $p, 'CHANGE_SCREEN' => 0);
				json($user);
			}else{
				/*出席調査が開始出来ない*/
				$p = array('RESULT' => 1,'TIME' => $nowTime);
				errorSentMaile($scheduleId,$situation);
				$user =  array('TAKE_COURSE' => 0,'PROCESS' => $p, 'CHANGE_SCREEN' => 0);
				json($user);
			}
		}else{
			/*履修生の画面が変更出来なかった*/
			/*誰も出席できない*/
			$user =  array('TAKE_COURSE' => 0,'PROCESS' => NULL, 'CHANGE_SCREEN' => 1);
			json($user);
		}
	}
}else{
	/*出席調査終了*/
	$insertCallEndSQL = "UPDATE `CALL_THE_ROLL` SET `CALL_END_TIME` = '".$nowTime."' WHERE `SCHEDULE_ID` = '".$scheduleId."'";
	$callEndResult = $con -> execute($insertCallEndSQL);
	if ($callEndResult){
		$p = array('RESULT' => 0,'TIME' => $nowTime);
		$user =  array('TAKE_COURSE' => 0,'PROCESS' => $p, 'CHANGE_SCREEN' => 0);
		json($user);
	}else{
		$p = array('RESULT' => 1, 'TIME' => NULL);
		$user =  array('TAKE_COURSE' => 0,'PROCESS' => $p, 'CHANGE_SCREEN' => 0);
		errorSentMaile($scheduleId,$situation);
		json($user);
	}
}

function json($user){
	header('Content-type: application/json');
	echo json_encode($user);
}

/**エラー情報を管理者にメールする**/
function errorSentMaile($scheduleId,$si){
	$m = new Mail();
	$title = "";
	if($si == 0){
		/*開始*/
		$title = "出席調査が開始できません.";
	}else{
		/*終了*/
		$title = "出席調査が終了できません.";
	}
	$error = "SCHEDULE_ID : ".$scheduleId."でエラーを検出しました．";
	$m ->sendErrorSitutation($title, $error,"CALL_THE_ROOL");
}
?>