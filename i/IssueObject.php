<?php
/**
 * 
 * URLを発行するサイトを管理するオブジェクト
 * ガラパゴスフォン : gp.php
 * スマートフォンは : sp.php
 * にそれぞれふりわけます．
 * 
 * 
 * ***/

class IssueObject{
	private $galapagosPhone = "gp.php";
	private $smartPhone = "sp.php";
	
	public function getGalapagosPhoneSite(){
		return $this -> galapagosPhone;
	}
	public function getSmartPhoneSite(){
		return $this -> smartPhone;
	}
}

?>