<?php
include_once(dirname(__DIR__).'../base/my_base.php');
$my = new MyBase();
/*セット*/
$my -> setPastScheduleId($_GET['s']);

/*現在の出席状態をチェックする*/
$sql = "SELECT `CALL_START_TIME`, `CALL_END_TIME` FROM `CALL_THE_ROLL` WHERE `SCHEDULE_ID` LIKE '".$my -> getPastScheduleId()."'";
$callResult = $my -> getConnection() -> query($sql);

if(count($callResult) == 1){
	/*出席調査を開始したことがある*/
	/*現在出席調査中かを調べる*/
	if(isset($callResult[0]['CALL_END_TIME'])){
		/*出席終了している*/
		endCallView($my -> getPastScheduleId(),$callResult[0]['CALL_START_TIME'],$callResult[0]['CALL_END_TIME']);
	}else{
		/*まだ出席調査中です．*/
		midleCallView($my -> getPastScheduleId(),$callResult[0]['CALL_START_TIME']);
	}
}else{
	/*まだ出席調査を行ったことがない*/
	notCallView($my -> getPastScheduleId());
}

?>

<div id='call_status'>
	<?php
	function notCallView($scheduleId){
	//echo "出席調査が開始されていません．";
echo <<< EOT
		<p>SCHEDULE_ID:$scheduleId</p>
		<p>出席調査が開始されていません．</p>
		<a href="#" id="bbbtn">button</a>
		<div class="button">Button</div>
		<div class='button' onclick=requestData.testAlert('disp','phpfiles/status.php','phpfiles/changeattendance/endattendance.php','出席を停止します。','出席停止しました');>出席を停止する</div>
	
EOT;
}

function midleCallView($scheduleId,$startTime){
	//echo "現在出席調査中です．";
	echo <<< EOT
		<p>SCHEDULE_ID:$scheduleId</p>
		<p>START_TIME:$startTime</p>
		<a href="#" id="bbbtn">button</a>
		<div class="button">Button</div>
EOT;
}

function endCallView($scheduleId,$startTime,$endTime){
	//echo "現在出席調査中です．";
	echo <<< EOT
		<p>SCHEDULE_ID:$scheduleId</p>
		<p>STRT_TIME:$startTime</p>
			<p>END_TIME:$endTime</p>
EOT;
}

?>
</div>

