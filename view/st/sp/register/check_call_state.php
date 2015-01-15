<?php
/**
 * 出席調査が行われているかをチェックするphpです．
 * DBのCALL_THE_ROLLを参照し出席開始状態を確認します．
 * もし，出席調査中であれば，
 * SCHEDULE_IDを元に部屋の座席位置情報もJSONで返します．
 *
 * **/
$dir =  dirname(__FILE__);
$path =  "../../../module/php/base/db/db_access.php";
include($path);

/*POSTされたでーたを取得*/
//$scheduleId = $_GET['s'];
//$randomNo = $_GET['r'];
$scheduleId = $_POST['s'];
$randomNo = $_POST['r'];

/**DBに登録されているパラメータか登録されているかをチェック**/
$sql = "SELECT S.YEAR,S.MONTH,S.DAY, S.TIMETABLE_ID,C.`CALL_START_TIME`, C.`CALL_END_TIME` FROM `CALL_THE_ROLL` C,SYLLABUS_MST S WHERE C.SCHEDULE_ID = S.SCHEDULE_ID AND C.SCHEDULE_ID = '".$scheduleId."'";
$result = $con -> query($sql);

/**/
if(count($result) == 1){
	/*出席調査が開始されている*/
	$startTime = $result[0]["CALL_START_TIME"];
	$endState=0;
	$endTime=null;

	/**出席申請をしているかを確認する**/
	//学籍番号取得
	$selectStudentIDSQL = "SELECT STUDENT_ID FROM `REGISTER_MST`WHERE RANDOM_NO = '".$randomNo."' ";
	$selectStudentIDResult = $con -> query($selectStudentIDSQL);
	$studentId = $selectStudentIDResult[0]["STUDENT_ID"];

	/*$year=date("Y");
	 $m=date("m");
	$d=date("d");
	$y=substr($year, 2);*/
	$t = $result[0]["TIMETABLE_ID"];
	$year=$result[0]["YEAR"];
	$y=substr($year, 2);
	$m=$result[0]["MONTH"];
	$d=$result[0]["DAY"];

	//出席者インデックス
	$attendeeId = $y.$m.$d.$t.$studentId;
	$selectAttendeeSQL = "SELECT S.SEAT_ID,B.SEAT_BLOCK_NAME,S.SEAT_ROW,S.SEAT_COLUMN,A.ATTEND_TIME
			FROM `ATTENDEE` A,SEAT_MST S,SEAT_BLOCK_MST B
			WHERE A.SEAT_ID = S.SEAT_ID AND S.SEAT_BLOCK_ID = B.SEAT_BLOCK_ID AND A.`ATTEND_ID` LIKE '".$attendeeId."' ";
	$attendeeResult = $con -> query($selectAttendeeSQL);
	$attendeeInfo=null;
	$roomInfo=null;
	if(count($attendeeResult) == 1){
		/*現在のscheduleIdではすでに出席している*/
		$aSeatId = $attendeeResult[0]["SEAT_ID"];
		$aBlockName = $attendeeResult[0]["SEAT_BLOCK_NAME"];
		$aSeatRow = $attendeeResult[0]["SEAT_ROW"];
		$aSeatColumn = $attendeeResult[0]["SEAT_COLUMN"];
		$aAttendTime = $attendeeResult[0]["ATTEND_TIME"];
		$attendeeInfo = array('SEAT_ID' => $aSeatId,
				'SEAT_BLOCK_NAME' => $aBlockName,
				'SEAT_ROW' => $aSeatRow,
				'SEAT_COLUMN' => $aSeatColumn,
				'ATTEND_TIME' => $aAttendTime);
	}else{
		/*まだ出席できてない*/
		/*現在出席可能な状態かをチェックする*/
		$endTime = $result[0]["CALL_END_TIME"];
		if($endTime!=null){
			/*出席調査が終了していたら*/
			$endState=1;
		}else{
			/*現在出席可能な状態なら座席情報を返す*/
			/**座席情報を返す**/
			$selectSeatSQL = "SELECT B.BUILDING_NAME,R.ROOM_NAME,SM.SEAT_ID,BM.SEAT_BLOCK_NAME,SM.SEAT_ROW,SM.SEAT_COLUMN
					FROM SYLLABUS_MST S,SEAT_MST SM,SEAT_BLOCK_MST BM,ROOM_MST R,BUILDING_MST B
					WHERE SM.SEAT_BLOCK_ID = BM.SEAT_BLOCK_ID
					AND BM.ROOM_ID = R.ROOM_ID
					AND R.ROOM_ID = S.ROOM_ID
					AND B.BUILDING_ID = R.BUILDING_ID
					AND S.SCHEDULE_ID = '".$scheduleId."'
							ORDER BY `BM`.`SEAT_BLOCK_NAME`,SM.SEAT_ROW,SM.SEAT_COLUMN ASC ";
			$seatResult = $con -> query($selectSeatSQL);
			$roomName = null;
			$pastSeatBlockName=null;
			$seatBlockList=array();
			$seatRowList=array();;
			$seatColumnList=array();
			$pastSeatRow=0;
			$pastSeatColumn=0;

			$roomName = $seatResult[0]["BUILDING_NAME"].$seatResult[0]["ROOM_NAME"];
			for($i=0;$i<count($seatResult);$i++){
				$saetBlockName = $seatResult[$i]["SEAT_BLOCK_NAME"];
				$saetRow = $seatResult[$i]["SEAT_ROW"];
				$saetColumn = $seatResult[$i]["SEAT_COLUMN"];
				$saetId = $seatResult[$i]["SEAT_ID"];

				//初回以外
				if($i>0){
					//前回と行が違う場合
					if(strcmp($pastSeatRow,$saetRow)!=0){
						$seatRowList[]=array('SEAT_ROW'=> $pastSeatRow, 'SEAT_INFO'=>$seatColumnList);
						//初期化
						$seatColumnList=array();
					}
					//前回とブロック名が違う場合
					if(strcmp($pastSeatBlockName,$saetBlockName)!=0){
						$seatBlockList[]=array('SEAT_BLOCK_NAME'=> $pastSeatBlockName, 'SEAT'=>$seatRowList);
						//初期化
						$seatRowList=array();
					}
				}

				$pastSeatRow=$saetRow;
				$pastSeatBlockName=$saetBlockName;

				$seatColumnList[] = array('SEAT_ID'=> $saetId,'SEAT_COLUMN'=> $saetColumn);

				/*一番最後*/
				if($i == (count($seatResult)-1)){
					$seatRowList[]=array('SEAT_ROW'=> $saetRow, 'SEAT_INFO'=>$seatColumnList);
					$seatBlockList[]=array('SEAT_BLOCK_NAME'=> $saetBlockName, 'SEAT'=>$seatRowList);
				}
			}
			$roomInfo=array('ROOM_NAME' => $roomName, 'SEAT_BLOCK' => $seatBlockList);
		}
	}

	$start = array('STATE' => 1,'TIME' => $startTime);
	$end = array('STATE' => $endState,'TIME' => $endTime);
	$user =  array('START' => $start,'END' => $end,'ATTENDEE' =>$attendeeInfo,'ROOM' => $roomInfo);
}else{
	/*出席調査が開始されていない*/
	$start = array('STATE' => 0,'TIME' => NULL);
	$user =  array('START' => $start, 'END' => NULL);
}


//jsonとして出力
header('Content-type: application/json');
echo json_encode($user);

?>