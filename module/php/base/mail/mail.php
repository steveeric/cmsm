<?php
//include('../../../../tool/log/logger.php');
include('logger.php');
class Mail{
	private $s = "si@primary-meta-works.com";
	function __construct() {
		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
	}
	
	/**
	 * 
	 * エラー内容のメールを送信する
	 * 
	 * **/
	public function sendError($title,$error){
		if (mb_send_mail($this -> s, $title, $error, "From:".$this -> s)) {
			/*送信成功*/		
		} else {
			$log = new MyLogger("Log","ERROR_MAIL.txt");
			$log ->Error("TITLE:".$title." ERROR:".$error);
		}
	}
	public function sendErrorSitutation($title,$error,$fileName){
		if (mb_send_mail($this -> s, $title, $error, "From:".$this -> s)) {
			/*送信成功*/
		} else {
			$log = new MyLogger("Log","ERROR_".$fileName.".txt");
			$log ->Error("TITLE:".$title." ERROR:".$error);
		}
	}
}

?>
