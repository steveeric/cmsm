<?php
	/**
	* Matrixコンテンツの画面状態を保持するクラスです
	***/
	class MatrixScreen{

		//入力制限内(正常に値を当てた)
      	public $IN_INPUT_LIMET = 1;
      	//入力制限オーバー
      	public $OVER_INPUT_LIMET = 2;

		//終了しているのか.
		private $endFlag = 0;
		/*
		*スクリーンナンバー
		* 0 : 初期値
		* 1 : 終了
		* 2 : 出席していないので参加できません.
   		* 3 : 名札
   		* 4 : 入力画面
		*/
		private $screenNumber = 0;
		//学生
		private $student;
		//マトリックス
		private $matrix;



		/**
		* コンストラクタ
 		**/
 		function __construct() {

   		}

   		/*コンテンツ終了フラグのセッター*/
		public function setEndFlag($endFlag){
			$this -> endFlag = $endFlag;
		}
   		/*画面状態番号のセッター*/
		public function setScreenNumber($screenNumber){
			$this -> screenNumber = $screenNumber;
		}

		public function setMatrixItem($student,$matrix){
			$this -> student = $student;
			$this -> matrix = $matrix;
		}


		/**
		* コンテンツ終了状態フラグ.
		**/
		public function getEndFlag(){
			return $this -> endFlag;
		}
		/**
		* 画面状態番号を返す.
		**/
		public function getScreenNumber(){
			return $this -> screenNumber;
		}

		public function getStudent(){
			return $this -> student;
		}

		public function getMatrix(){
			return $this -> matrix;
		}



	}

?>