<? 

include_once(dirname(__DIR__).'../base/my_base.php');
$my = new MyBase();
/*セット*/
$my -> setPastScheduleId($_GET['s']);

/*現在の出席状態をチェックする*/
$sql = "SELECT `CALL_START_TIME`, `CALL_END_TIME` FROM `CALL_THE_ROLL` WHERE `SCHEDULE_ID` LIKE '".$my -> getPastScheduleId()."'";
$callResult = $my -> getConnection() -> query($sql);

if(count($callResult) == 1){
	/*出席申請を行っている*/
}else{
	/*一度も出席申請を行ったことが無い*/
	noCallView();
}

?>


<?php 
/**＊
 ***/
//function noCallView(){
	//echo "出席調査が開始されていません．";
	echo <<<EOT
		<div data-role="content" style="text-align: center">
		<p>出席調査が開始されていません．</p>
		</div>
EOT;
	//}
	?>

