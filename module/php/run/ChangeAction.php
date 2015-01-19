<?php
	/*
	* 作成日 : 2015年1月19日
	* ChangeActionクラス
	* DBのSYLLABUS_MSTの「ACTION_ID」を、
	* 変更するためのモジュールクラスになります.
	* 3 : 座席指定
	* 4 : マトリックス
	* 9 : グルーピング
	*/

   //時間クラス用のtimeをインクルード
   include_once(dirname(__FILE__).'../../base/Time/time.php');

	class ChangeAction{
		//座席指定
		public $SEAT_SELECT = 3;
		//マトリックス
		public $MATRIX = 4;
		//グルーピング
		public $GROUPING = 9;
		//コネクション
		private $con;
 		/**
		* コンストラクタ
 		**/
 		function __construct($con) {
 			$this -> con = $con;
   		}

   		/**
	   	* createdate : 2015年1月19日
	   	* changeActionIdメソッド
		* DB上のSYLLABUS_MST内「ACTION_ID」を変更します.
		* @parm $actionId 授業内の行動ID
		* @parm $scheduleId 授業ID
	   	**/
   		public function changeActionId($actionId,$scheduleId){

   			$firstSQL = "UPDATE `SYLLABUS_MST` SET `ACTION_ID` = '".$actionId."' ";

   			if($actionId == $this -> MATRIX){
   				//マトリックスモードだった場合.
   				$time = new Time();
   				//部分的なSQL文生成
   				$str = $this -> recordDoingStartMatrixDateTime($time);
   				$firstSQL = $firstSQL . $str;
   			}

   			$secondSQL = "WHERE `SCHEDULE_ID` = '".$scheduleId."' ";

   			$sql = $firstSQL.$secondSQL;

   			$this -> con -> execute($sql);
   		}

   		/**
	   	* createdate : 2015年1月19日
	   	* recordDoingStartMatrixDateTimeメソッド
		* マトリックス開始時刻を記録するための、部分的なSQL分を生成.
		* @return マトリックスの開始時刻を記録するための部分的なSQL分を生成
	   	**/
   		private function recordDoingStartMatrixDateTime($time){
   			$nowDateTime = $time -> getNowDateTime();
   			return " , `START_MATRIX_DATE_TIME` = '".$nowDateTime."' ";
   		}
	}


?>