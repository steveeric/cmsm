<?php
	/*
	* 作成日 : 2015年1月15日
	* Matrixクラス
	* マトリックスに関する情報を保持するクラス
	*
	*
	*
	*/
	class Matrix{
		//マトリックスID
		public $matrixId;
		//マトリックス枠の最大行数
		public $matrixBordRowCount;
		//マトリックス枠の最大列数
		public $matrixBordColumnCount;
		//入力可能な最大回数
		public $inputMaxCount;
		//入力すべき行番号
		public $matrixRowNumber;
		//入力すべき列番号
		public $matrixColumnNumber;
		//入力すべき値
		public $matrixMustInputItem;
		//入力桁数
		public $matrixDigitCount;
		/**
		* コンストラクタ
 		**/
 		function __construct($matrixId) {
 			$this -> matrixId = $matrixId;
   		}
   		/**
   		* createdate : 2015年1月15日
   		* setMatrixGridメソッド
   		* マトリックスの概要をセットする.
   		* @parm inputMaxCount 入力可能な最大回数
   		* @parm totalRowCount マトリックスの格子の行数
   		* @parm totalColumnCount マトリックスの格子の列数
   		* @parm digitCount 入力桁数
   		**/
   		public function setMatrixBord($inputMaxCount,$totalRowCount,$totalColumnCount,$digitCount){
   			$this -> inputMaxCount = $inputMaxCount;
   			$this -> matrixBordRowCount = $totalRowCount;
   			$this -> matrixBordColumnCount = $totalColumnCount;
   			$this -> matrixDigitCount = $digitCount;
   		}
		/**
   		* createdate : 2015年1月15日
   		* setMatrixメソッド
   		* マトリックス情報をセットする.
   		* @parm inputItem 入力すべきアイテム
   		* @parm rowNumber 入力すべき行番号
   		* @parm columnNumber 入力すべき列番号
   		**/
   		public function setMatrix($inputItem,$rowNumber,$columnNumber){
   			$this -> matrixRowNumber = $rowNumber;
   			$this -> matrixColumnNumber = $columnNumber;
   			$this -> matrixMustInputItem = $inputItem;
   		}
         /*
   		public function getMatrixId(){
   			return $this -> matrixId;
   		}
   		public function getMatrixMustInputItem(){
   			return $this -> matrixMustInputItem;
   		}
         */
	}

?>