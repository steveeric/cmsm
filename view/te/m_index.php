<?php
/*DBと時間インスタンスを読み込む*/
include_once(dirname(__DIR__).'../../module/php/base/db/db_access.php');
/*教員オブジェクトを読み込む*/
include_once(dirname(__DIR__).'../../module/php/base/teacher/TeacherObject.php');

/*教員オブジェクトインスタンス化
 * 「現状，中村先生しか使えません．」.
* */
$teacher = new Teacher();
/*現在のアクセス時刻を取得*/
$nowTime = $time -> getTimeTableIdTime();
/*教員IDを取得*/
$teacherId = $teacher->getTeacherId();
/*タイムテーブルIDを取得*/
$timeTableResult = $con -> getNowTimeTableId($nowTime);
$timeTableId = $timeTableResult[0]['TIMETABLE_ID'];
/*検索用のアイテムを取得*/
$y = $time -> getYear();
$m = $time -> getMonth();
$d = $time -> getDay();

/*scheduleIdをゲット*/
$scheduleResult = $con -> getNowScheduleId($teacherId,$y,$m,$d,$timeTableId);
if(count($scheduleResult) == 1){
	/*現在授業時間タイ*/
	$s = $scheduleResult[0]['SCHEDULE_ID'];
	doClassRoomTime($s);
}else{
	/*授業時間帯ではない*/
	echo "DEBUG:".$nowTime;
	echo "DEBUG:".$timeTableId;
	echo "現在の時間帯は授業を持っていません．";
}

function doClassRoomTime($scheduleId){
	echo <<<EOT
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
<head>
	<meta charset="utf-8">
	<meta name='viewport' content='width=200px, initial-scale=1, maximum-scale=2'>
	<link rel="stylesheet" type="text/css" href="../../tool/css/teacher/style.css" />
	<title>出席用サーバー 教師用ページ</title>
	<script src="../../module/script/teacher/httploader.js"></script>
</head>
<body onLoad="requestData.readData('disp','ctr/ctr_status.php?s=$scheduleId');　writeString('status_disp','出席の状態');">
	<div id="menu">
		<div class="button" onclick="requestData.readData('disp','ctr/ctr_status.php?s=$scheduleId');　writeString('status_disp','出席の状態');">
			出席の状態
		</div>
		<div class="button" onclick="requestData.readData('disp','att/att_list.php?s=$scheduleId');　writeString('status_disp','出席者リスト');">
			出席者リスト
		</div>
		<div class="button" onclick="requestData.readData('disp','sv/seat_view.php?s=$scheduleId');　writeString('status_disp','座席表');">
			座席表
		</div>
		<div class="button" onclick="requestData.readData('disp','cs/cs.php?s=$scheduleId');　writeString('status_disp','席替え');">
			席替え
		</div>
	</div>
	<div id="status">
		選択項目:<span id="status_disp"></span>
	</div>
	<div id="disp">
	</div>
</body>
</html>
EOT;
}
?>