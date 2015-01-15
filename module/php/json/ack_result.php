<?php
//DB読み込み
include_once('../base/db/db_access.php');

//$sql="SELECT `SCHEDULE_ID`,PROCESS_START_TIME,`PROCESS_END_TIME` FROM `SEND_PROCESS_HISTORY` WHERE `SEND_PROCESS_KEY` LIKE '".$sendProcessKey."'";
$sql = "SELECT SEND_PROCESS_KEY,`SCHEDULE_ID`,`PROCESS_START_TIME`,`PROCESS_END_TIME` FROM `SEND_PROCESS_HISTORY` ORDER BY `SEND_PROCESS_HISTORY`.`PROCESS_START_TIME` DESC  LIMIT 1";
$spkResult = $con -> query($sql);
$sendProcessKey = $spkResult[0]["SEND_PROCESS_KEY"];
$scheduleId = $spkResult[0]["SCHEDULE_ID"];
$processStartTime = $spkResult[0]["PROCESS_START_TIME"];
$processEndTime = $spkResult[0]["PROCESS_END_TIME"];
if($scheduleId!=null){
//
$sql = "SELECT ROOM_ID,ACTION_ID FROM `SYLLABUS_MST`"
       ." WHERE `SCHEDULE_ID` LIKE '".$scheduleId."'";
$syllabusResult = $con -> query($sql);
$roomId = $syllabusResult[0]["ROOM_ID"];
$actionId = $syllabusResult[0]["ACTION_ID"];

//座席のトータル数を取得
$sql = "SELECT COUNT( S.SEAT_ID ) "
       ."FROM SEAT_MST S, SEAT_BLOCK_MST B,"
       ." ROOM_MST R WHERE S.SEAT_BLOCK_ID = B.SEAT_BLOCK_ID"
       ." AND B.ROOM_ID = R.ROOM_ID AND R.ROOM_ID = ".$roomId;
$seatTotalResult = $con -> query($sql);
$seatTotal = $seatTotalResult[0]["COUNT( S.SEAT_ID )"];
//座席トータル数
//var_dump($seatTotalResult);
$attendee=null;
    $sql = "SELECT A.SEAT_ID, R.DM_BARCODE_ID, ST.STUDENT_ID, ST.FULL_NAME, A.SEAT_REVISION_DISTINATION "
           ." FROM `ATTENDEE` A, STUDENT_MST ST, REGISTER_MST R "
           ."WHERE A.STUDENT_ID = ST.STUDENT_ID "
           ."AND ST.STUDENT_ID = R.STUDENT_ID "
           ."AND A.`SCHEDULE_ID` LIKE '".$scheduleId."' ";
       $attendeeResult = $con -> query($sql);
       
       for($i=0;$i<count($attendeeResult);$i++){
       $seatId = $attendeeResult[$i]["SEAT_ID"];
       $seatRevision = $attendeeResult[$i]["SEAT_REVISION_DISTINATION"];
       $studentId = $attendeeResult[$i]["STUDENT_ID"];
       $fullName = $attendeeResult[$i]["FULL_NAME"];
       $dmBarcodeId = $attendeeResult[$i]["DM_BARCODE_ID"];
       if($seatRevision != 0){
         $seatId = $seatRevision;
       }

       $sql = "SELECT LAST_ACK_TIME FROM `DM_HISTORY` WHERE `PROCESS_DM_KEY` LIKE '".$sendProcessKey.$dmBarcodeId."'";
       $ackResult = $con -> query($sql);
       $lastACKTime = null;
       if($ackResult != null){
       	$lastACKTime = $ackResult[0]["LAST_ACK_TIME"];
       }
       $attendee[] = array('SEAT_ID'=>$seatId,
                         'STUDENT_ID'=>$studentId,
                         'FULL_NAME'=>$fullName,
                         'SEAT_REVISION' =>$seatRevision,
                         'LAST_ACK_TIME' => $lastACKTime);
    }
//座席ブロック情報取得
$sql = "SELECT SEAT_BLOCK_ID,SEAT_BLOCK_NAME,SEAT_BLOCK_ROW,SEAT_BLOCK_COLUMN"
       ."  FROM SEAT_BLOCK_MST WHERE ROOM_ID = ".$roomId;
$seatBlockResult = $con -> query($sql);

//
//var_dump($seatBlockResult);

//座席ブロックの配列初期化
$seatBlock = array();

//各座席ブロックのX,Y方向の座席数を取得する
for($i=0;$i<count($seatBlockResult);$i++){
  $seatBlockId =  $seatBlockResult[$i]["SEAT_BLOCK_ID"];
  $seatBlockName = $seatBlockResult[$i]["SEAT_BLOCK_NAME"];
  $sql = "SELECT MAX( SEAT_ROW ) , MAX( SEAT_COLUMN )"
         ." FROM SEAT_MST WHERE SEAT_BLOCK_ID = ".$seatBlockId;
  $seatBlockDeteileCountResult = $con -> query($sql);
  $seatBlockDeteileInRowSeat = $seatBlockDeteileCountResult[0]["MAX( SEAT_ROW )"];
  $seatBlockDeteileInColumnSeat = $seatBlockDeteileCountResult[0]["MAX( SEAT_COLUMN )"];
  $seatBlockDeteileJson = array('IN_SEAT_ROW_COUNT' => $seatBlockDeteileInRowSeat, 'IN_SEAT_COLUMN_COUNT' => $seatBlockDeteileInColumnSeat);

  //
  //var_dump($seatBlockDeteileCountResult);
  
  $sql = "SELECT S.SEAT_ID, S.SEAT_ROW, S.SEAT_COLUMN "
         ."FROM `SEAT_MST` S, SEAT_BLOCK_MST B "
         ."WHERE S.SEAT_BLOCK_ID = B.SEAT_BLOCK_ID "
         ."AND S.SEAT_BLOCK_ID =".$seatBlockId." " 
         ."ORDER BY `S`.`SEAT_ROW` , S.SEAT_COLUMN ASC ";
  $seatInBlockResult = $con -> query($sql);
  $seat = array();
  for($j=0;$j<count($seatInBlockResult);$j++){
    $seatId = $seatInBlockResult[$j]["SEAT_ID"];
    $row = $seatInBlockResult[$j]["SEAT_ROW"];
    $column = $seatInBlockResult[$j]["SEAT_COLUMN"];
   
    $attend = null;
    if(count($attendee)>0){
    for($k=0;$k<count($attendee);$k++){
      $attSeatId = $attendee[$k]["SEAT_ID"];
      if($seatId == $attSeatId){
        //出席者あり
        //$attend = $attendee[$k];
        $attend = array('STUDENT_ID' => $attendee[$k]["STUDENT_ID"], 
                        'FULL_NAME' => $attendee[$k]["FULL_NAME"],
                        'SEAT_REVISION'=>$attendee[$k]["SEAT_REVISION"],
                        'ACK_TIME'=>$attendee[$k]["LAST_ACK_TIME"]);
      }
    }   
}
    
    $absent = null;
    if($attend == null){
      //なぜいないかを調査する
      $sql = "SELECT ATTEND_ID FROM `ATTENDEE` "
             ."WHERE `SCHEDULE_ID` "
             ."LIKE '".$scheduleId."' "
             ."AND `SEAT_ID` ='".$seatId."'";
      $rebisionResult = $con -> query($sql);
      if(count($rebisionResult)>0){
        //どこかの座席に移動している
        //教室にはいる
      }else{
      //仮に出席していないかをチェックする
      $sql = "SELECT ST.STUDENT_ID, ST.FULL_NAME "
             ."FROM `ATTENDEE_PROVISINAL`AP,STUDENT_MST ST "
             ."WHERE AP.STUDENT_ID = ST.STUDENT_ID "
             ."AND AP.`SCHEDULE_ID` LIKE '".$scheduleId."' "
             ."AND AP.`SEAT_ID` ='".$seatId."'";
      $absentResult = $con -> query($sql);
      if(count($absentResult) == 1){
        //欠席者
        $abStudentId = $absentResult[0]["STUDENT_ID"];
        $abFullName = $absentResult[0]["FULL_NAME"];
        $absent = array('STUDENT_ID' => $abStudentId, 'FULL_NAME' => $abFullName);
      }
     }
    }
 
    $seat[] = array('SEAT_ID' => $seatId, 
                    'SEAT_ROW' => $row, 
                    'SEAT_COLUMN' => $column,
                    'ATTENDEE' => $attend,
                    'ABSENTEE' => $absent);
  }  

  //var_dump($seatInBlockResult);
 
  $seatBlock[] = array('SEAT_BLOCK_ID' => $seatBlockId,'SEAT_BLOCK_NAME' => $seatBlockName,'IN_SEAT_COUNT' => $seatBlockDeteileJson, 'SEAT' =>$seat );

}
}else{
//SCHEDULE_IDがSEND_PROCESSにない
$seatTotal=null;
$seatBlock=null;
}

/**送信結果を上位DBから取得する(ACK回り)****/
$ackCountSQL = "SELECT LAST_ACK_TIME FROM `DM_HISTORY` WHERE `SEND_PROCESS_KEY` LIKE '".$sendProcessKey."'";
$ackCountResult = $con -> query($ackCountSQL);

$sendTotal = count($ackCountResult);
$ackCount = 0;

for($i=0;$i<$sendTotal;$i++){
	if(is_null($ackCountResult[$i]["LAST_ACK_TIME"]) ){
		//null
	}else{
		$ackCount++;
	}
}

$faileACKCount = count($ackCountResult)-$ackCount;

$sendResult = array('TOTAL' => count($ackCountResult), 'ACK_COUNT' => $ackCount, 'FAILE_ACK_COUNT' => $faileACKCount);
$sendProcess = array('PROCESS_START_TIME' => $processStartTime,'PROCESS_END_TIME' => $processEndTime );
header('Content-type: application/json');
$user = array('SEND_PROCESS' => $sendProcess,
              'SEND_RESULT' => $sendResult,
              'SEAT_TOTAL' => $seatTotal,
              'SEAT_BLOCK' => $seatBlock);
echo json_encode($user);

?>
