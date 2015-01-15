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
		private $matrixLogId;
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
   		public function setMatrixInfo($matrixLogId,$resultInputMatrix,$requestcountJudgementMatrix){
   			$this -> matrixLogId = $matrixLogId;
   			$this -> resultInputMatrix = $resultInputMatrix;
   			$this -> requestcountJudgementMatrix = $requestcountJudgementMatrix;
   		}

   		public function getAttendeeId(){
   			return $this -> attendeeId;
   		}

   		public function getMatrixLogId(){
   			return $this -> matrixLogId;
   		}
   		public function getResultInputMatrix(){
   			return $this -> resultInputMatrix;
   		}
   		public function getRequestcountJudgementMatrix(){
   			return $this -> requestcountJudgementMatrix;
   		}
	}

?>