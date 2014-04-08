<?php

class Time{
	/**
	 *
	 * 現在の時刻を返す
	 *
	 * **/

	function __construct() {
		/*日付けを日本に設定*/
		date_default_timezone_set('Asia/Tokyo');
	}
	public function getYear(){
		return date('Y');
	}
	public function getMonth(){
		return date('m');
	}
	public function getDay(){
		return date('d');
	}

	public function getNowTime(){
		return date('Y-m-d H:i:s');
	}
	public function getTimeTableIdTime(){
		return date('H:i:s');
	}
	//何時何分何秒を返す
	/*function getClassNowTime(){
		return date("H:i:s");
	}*/

	function getNowDate(){
          return date('Y-m-d');
        }	
	
	//詳しい現在の時間を返す
	function getNowDetaileTime(){
		return date("Y-m-d H:i:s");
	}
}

?>
