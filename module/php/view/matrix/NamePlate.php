<?php

	class NamePlate{
		//入力制限内(正常に値を当てた)
		public $IN_INPUT_LIMET = 1;
		//入力制限オーバー
		public $OVER_INPUT_LIMET = 2;

		//学籍番号
		private $studentId;
		//氏名
		private $fullName;
		/*
		* 1 : 正常に正解した
		* 2 : 入力回数オーバー
		*/
		//入力正解ステータス
		private $inputMatrixStatus;
		/**
		* コンストラクタ
 		**/
 		function __construct($studentId,$fullName,$status) {
 			$this -> studentId = $studentId;
 			$this -> fullName = $fullName;
 			$this -> inputMatrixStatus = $status;
   		}

   		public function getStudentId(){
   			return $this -> studentId;
   		}
   		public function getFullName(){
   			return $this -> fullName;
   		}
   		public function getInputMatrixStatus(){
   			return $this -> inputMatrixStatus;
   		}

	}


?>