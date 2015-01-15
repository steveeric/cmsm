<?php

$GROUPING_ACTION = 9;

//$roomId = $_GET['r'];

$scheduleId = $_GET['s'];
$seatBlockId = $_GET['b'];

//DB読み込み
include_once('../base/db/db_access.php');

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
    $sql = "SELECT A.SEAT_ID, ST.STUDENT_ID, ST.FULL_NAME, A.SEAT_REVISION_DISTINATION "
           ." FROM `ATTENDEE` A, STUDENT_MST ST "
           ."WHERE A.STUDENT_ID = ST.STUDENT_ID "
           ."AND A.`SCHEDULE_ID` LIKE '".$scheduleId."' ";
    $attendeeResult = $con -> query($sql);
       for($i=0;$i<count($attendeeResult);$i++){
       $seatId = $attendeeResult[$i]["SEAT_ID"];
       $seatRevision = $attendeeResult[$i]["SEAT_REVISION_DISTINATION"];
       $studentId = $attendeeResult[$i]["STUDENT_ID"];
       $fullName = $attendeeResult[$i]["FULL_NAME"];

       if($seatRevision != 0){
         $seatId = $seatRevision;
       }

       $groupInfo=null;
       if($actionId==$GROUPING_ACTION){
         $sql = "SELECT GROUP_NAME "
                ."FROM `SEAT_CHANGE_MST` "
                ."WHERE `ROOM_ID` ='".$roomId."' "
                ."AND `SCREEN_CONTENT_ID` LIKE 'gp' "
                ." AND `SEAT_ID` ='".$seatId."'";
         $gpResult = $con -> query($sql);
         $groupInfo = array('GROUP_NAME'=>$gpResult[0]["GROUP_NAME"]);
       }

       $attendee[] = array('SEAT_ID'=>$seatId,
                         'STUDENT_ID'=>$studentId,
                         'FULL_NAME'=>$fullName,
                         'SEAT_REVISION' =>$seatRevision,
                         'GROUP' => $groupInfo);
    }



//座席ブロック情報取得
$sql = "SELECT SEAT_BLOCK_ID,SEAT_BLOCK_NAME,SEAT_BLOCK_ROW,SEAT_BLOCK_COLUMN"
       ."  FROM SEAT_BLOCK_MST WHERE SEAT_BLOCK_ID = '".$seatBlockId."'";
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
    for($k=0;$k<count($attendee);$k++){
      $attSeatId = $attendee[$k]["SEAT_ID"];
      if($seatId == $attSeatId){
        //出席者あり
        //$attend = $attendee[$k];
        $gp = $attendee[$k]["GROUP"];
        $attend = array('STUDENT_ID' => $attendee[$k]["STUDENT_ID"], 
                        'FULL_NAME' => $attendee[$k]["FULL_NAME"],
                        'SEAT_REVISION'=>$attendee[$k]["SEAT_REVISION"],
                        'GROUP' => $gp);
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

/*$testBlock=null;
for($i=0;$i<count($seatBlock);$i++){
 //echo $seatBlock[$i]["SEAT_BLOCK_ID"];
 for($j=0;$j<count($seatBlock[$i]["SEAT"]);$j++){
   $flag = 0;
   for($k=0;$k<count($attendee);$k++){
    if($seatBlock[$i]["SEAT"][$j]["SEAT_ID"] == $attendee[$k]["SEAT_ID"] && $flag == 0){
      //$seatBlock[$i]["SEAT"][$j] = array("ATTENDEE" => $attendee[$k]);
      $testBlock[] = array('SEAT' => $seatBlock[$i]["SEAT"][$j],"ATTENDEE"=>$attendee[$k]);
      $flag = 1;
    }
   }
    if($flag == 0){
      $testBlock[] = array('SEAT' => $seatBlock[$i]["SEAT"][$j],"ATTENDEE" => null);
    }
 }
}*/

header('Content-type: application/json');
$user = array('ACTION'=>$actionId,
              'SEAT_TOTAL' => $seatTotal,
              'SEAT_BLOCK' => $seatBlock);
echo json_encode($user);

?>
