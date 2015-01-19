<?php

	class ProcessInputValue{
		//正常にマトリックス入力をじゅりしました.
		public $NORMAL_RESULT_INPUT_MATRIX = 1;
		//不正出席の疑いがあります.
		public $ABNORMAL_RESULT_INPUT_MATRIX = 2;
		private $con;
		/**
		* コンストラクタ
 		**/
 		function __construct($con) {
 			$this -> con = $con;
   		}
   		/**
   		* createdate : 2015年1月16日
   		* setItemメソッド
		* @parm attendeeId 出席ID
		* @parm matrixLogId マトリックスログID
		* @parm iputValue 入力値
   		**/
   		public function setItem($attendeeId,$matrixLogId,$inputValue){
            $sql = "SELECT SCHEDULE_ID
                     FROM `ATTENDEE`
                     WHERE `ATTEND_ID` LIKE '".$attendeeId."'";
            $syllabusResult = $this -> con -> query($sql);
            $scheduleId = $syllabusResult[0]["SCHEDULE_ID"];

            $sql = "SELECT `END_MATRIX_DATE_TIME`
                     FROM `SYLLABUS_MST`
                     WHERE `SCHEDULE_ID` LIKE '".$scheduleId."'";
            $endMatrixResult = $this -> con -> query($sql);
            $endMatrixDateTime = $endMatrixResult[0]["END_MATRIX_DATE_TIME"];
            if((!is_null($endMatrixDateTime))){
               //終了しているので受け付けない.
            }else{
      			//正解フラグ 0:不正解 1:正解
      			$correct = 0;
      			//入力値が正しいかをチェックする.
      		   $sql ="SELECT M.MATRIX_VALUE
      			FROM `MATRIX_LOG`ML,`MATRIX`M
      			WHERE ML.`MATRIX_LOG_ID` LIKE '".$matrixLogId."'
      			AND ML.MATRIX_ID = M.MATRIX_ID ";
      			$matrixResult = $this -> con -> query($sql);
      			$matrixOriginalValue = $matrixResult[0]["MATRIX_VALUE"];

               if(strcmp($inputValue,$matrixOriginalValue) == 0){
      				//正解
      				$correct = 1;
      			}else{
      				//不正解
      				$correct = 0;
      			}
      			//入力値をログにセットするSQL
      			$updateMatrixLogSQL = "UPDATE `MATRIX_LOG`
      			SET `INPUT_VALUE` = '".$inputValue."'
      			WHERE `MATRIX_LOG_ID` = '".$matrixLogId."'";
      			$result = $this -> con -> execute($updateMatrixLogSQL);

      			//
      			$sql = "SELECT A.`REQUEST_COUNT_JUDGEMENT_MATRIX`,SY.`MAX_LIMIT_INPUT_MATRIX`
      			FROM `ATTENDEE`A,`SYLLABUS_MST` SY
      			WHERE A.`ATTEND_ID` LIKE '".$attendeeId."'
      			AND A.SCHEDULE_ID = SY.SCHEDULE_ID ";
      			$selResult = $this -> con -> query($sql);

      			//過去のリクエスト数
      			$pastRequestCount = $selResult[0]["REQUEST_COUNT_JUDGEMENT_MATRIX"];
      			//リクエスト可能な最大数
      			$maxRequestCount = $selResult[0]["MAX_LIMIT_INPUT_MATRIX"];

      			$requestCount = ++$pastRequestCount;
      			//もう申請できませんフラグ 0:まだ大丈夫! 1:もう申請できません.
      			$endRequestFlag = 0;
      			if($requestCount >= $maxRequestCount){
      				//もう申請できません.
      				$endRequestFlag = 1;
      			}

      			$resultInputMatrix = 0;

      			$lastSQL = "";
      			if($correct == 1){
      				//正解
      				$resultInputMatrix = $this -> NORMAL_RESULT_INPUT_MATRIX;
      				$lastSQL = "UPDATE `ATTENDEE`
   			   				SET `REQUEST_COUNT_JUDGEMENT_MATRIX` = '".$requestCount."'
   			   				, `RESULT_INPUT_MATRIX` = '".$resultInputMatrix."'
   			   				WHERE `ATTEND_ID` = '".$attendeeId."'";
      			}else{
      				//不正解
      				if($endRequestFlag == 1){
      					//もう上限にたっしました.
                     $resultInputMatrix = $this -> ABNORMAL_RESULT_INPUT_MATRIX;
      					$lastSQL = "UPDATE `ATTENDEE`
   				   				SET `REQUEST_COUNT_JUDGEMENT_MATRIX` = '".$requestCount."'
   				   				, `RESULT_INPUT_MATRIX` = '".$resultInputMatrix."'
   				   				WHERE `ATTEND_ID` = '".$attendeeId."'";
      				}else{
      					//ただ間違えたのでリクエスト回数だけ足します.
      					$lastSQL = "UPDATE `ATTENDEE`
   				   				SET `REQUEST_COUNT_JUDGEMENT_MATRIX` = '".$requestCount."'
   				   				WHERE `ATTEND_ID` = '".$attendeeId."'";
      				}
      			}
      			//出席者にマトリックスの入力状態結果をDBにupdate
      			$result = $this -> con -> execute($lastSQL);
   		 }
         }
	}

?>