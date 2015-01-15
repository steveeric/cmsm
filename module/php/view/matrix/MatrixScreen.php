<?php
	/**
	* Matrixコンテンツの画面状態を保持するクラスです
	***/
	class MatrixScreen{
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
		//画面コンテンツクラス
		private $screenContent;


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
   		/*画面コンテンツのセッター*/
		public function setScreenContent($screenContent){
			$this -> screenContent = $screenContent;
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

		/**
		* 画面状態番号を返す.
		**/
		public function getScreenContent(){
			return $this -> screenContent;
		}


	}

?>