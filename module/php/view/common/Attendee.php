<?php

	/*
	* 作成日 : 2015年1月15日
	* Attendeeクラス
	* 情報を保持するクラスです.
	*
	*
	*
	*/
	class Attendee{
		//出席ID
		private $attendeeId;

		/*マトリックス情報*/
		private $lastMatrixLogId;
      private $resultInputMatrix;
      private $requestcountJudgementMatrix;
		/*マトリックス情報*/

		/**
		* コンストラクタ
 		**/
 		function __construct($attendeeId) {
 			$this -> attendeeId = $attendeeId;
   		}
         /**
         * createdate : 2015年1月15日
         * setMatrixInfoメソッド
         * 出席者のマトリックス情報のセッター.
         * @parm matrixLogId　マトリックスID
         * @parm resultInputMatrix　マトリックスの正解情報　1:正常に正解 2:上限をオーバー
         * @parm requestcountJudgementMatrix　値を入力しOKボタンを押した回数
         *
         **/
   		public function setMatrixInfo($lastMatrixLogId,$resultInputMatrix,$requestcountJudgementMatrix){
   			$this -> lastMatrixLogId = $lastMatrixLogId;
   			$this -> resultInputMatrix = $resultInputMatrix;
   			$this -> requestcountJudgementMatrix = $requestcountJudgementMatrix;
   		}

         public function setMatrixLogId($matrixLogId){
            $this -> lastMatrixLogId =  $matrixLogId;
         }

   		public function getAttendeeId(){
   			return $this -> attendeeId;
   		}

   		public function getLastMatrixLogId(){
   			return $this -> lastMatrixLogId;
   		}
   		public function getResultInputMatrix(){
   			return $this -> resultInputMatrix;
   		}
   		public function getRequestcountJudgementMatrix(){
   			return $this -> requestcountJudgementMatrix;
   		}
	}

?>