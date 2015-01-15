<?php
/**
 * DBのREGISTER_MST
 * ・存在するパラメータか
 * ・登録されているかどうか
 * をチェックするphpです．
 *
 * **/
$dir =  dirname(__FILE__);
$path =  "../../../module/php/base/db/db_access.php";
include($path);

/*POSTされたでーたを取得*/
//$randomNo = $_GET['r'];
$randomNo = $_POST['r'];

/*日付けを日本に設定*/
date_default_timezone_set('Asia/Tokyo');
$nowTime = date("Y-m-d H:i:s");

$sql = "SELECT `NOW_SCREEN_CONTENT_ID`, `SCHEDULE_ID` FROM `MOBILE_SCREEN` WHERE RANDOM_NO='".$randomNo."' ";
$result = $con -> query($sql);

if(count($result) == 1){
	$content = $result[0]["NOW_SCREEN_CONTENT_ID"];
	$scheduleId = $result[0]["SCHEDULE_ID"];
	
	if(strcasecmp($content,"register")!=0){
		/*登録画面じゃなければ*/
		/**取得してきたscheduleIdは現在授業中かをチェックする.**/
		$timeZoneSQL = "SELECT S.YEAR,S.MONTH,S.DAY,T.CLASS_START_TIME,T.CLASS_END_TIME FROM `SYLLABUS_MST` S,TIMETABLE_MST T WHERE S.TIMETABLE_ID = T.TIMETABLE_ID AND SCHEDULE_ID = '".$scheduleId."' ";
		$timeZoneResult = $con -> query($timeZoneSQL);
		if(count($timeZoneResult) == 1){
			$year = $timeZoneResult[0]["YEAR"];
			$month = $timeZoneResult[0]["MONTH"];
			$day = $timeZoneResult[0]["DAY"];
			$satartTime = $timeZoneResult[0]["CLASS_START_TIME"];
			$endTime = $timeZoneResult[0]["CLASS_END_TIME"];
		
			/**アクセスして正常にコンテンツを表示し始めていいかを調べる**/
			/*scheduleIdに登録されている時間から9分59秒前からコンテンツを展開する*/
			$accessStartTime = date("Y-m-d H:i:s", strtotime("-10 minute", strtotime($satartTime)));
			/**アクセスしていい時間化を調べる**/
			$during;
			if(strtotime($accessStartTime) < strtotime($nowTime) && strtotime($nowTime) < strtotime($endTime)){
				/*授業中なら*/
				$during =  array('DURING_CLSS' => 1);
			}else{
				/*授業中でない*/
				$during =  array('DURING_CLSS' => 0);
			}
			/*情報をセット*/
			$item =  array('NOW_SCREEN_CONTENT_ID' => $content,'SCHEDULE_ID' => $scheduleId,'DURING' => $during);
		}else{
			/*情報をセット*/
			$item =  array('NOW_SCREEN_CONTENT_ID' => $content,'SCHEDULE_ID' => $scheduleId,'DURING' => NULL);
		}
		$user =  array('REGISTER' => 1,'RANDOM_NO'=>$randomNo,'SCREEN' => $item);
	}else{
		/**MOBILE_SCREENが登録画面だったら**/
		$item =  array('NOW_SCREEN_CONTENT_ID' => $content,'SCHEDULE_ID' => NULL,'DURING' => NULL);
		$user =  array('REGISTER' => 1,'RANDOM_NO'=>$randomNo, 'SCREEN' => $item);
	}
}else{
	/**乱数の登録が確認できなければ**/
	$user =  array('REGISTER' => 0,'RANDOM_NO'=>$randomNo, 'SCREEN' => NULL);
}

//jsonとして出力
header('Content-type: application/json');
echo json_encode($user);

?>