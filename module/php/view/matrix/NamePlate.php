<?php

	class NamePlate{


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