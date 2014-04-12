<?php

class ViewObject{
	private $rootPath = "view";
	private $student = "st";
	private $galapagosPhone = "gp";
	private $smartPhone = "sp";

	function __construct() {
	}
	
	public function getGalapagosPhonePath(){
		$r = $this -> rootPath;
		$st = $this -> student;
		$gp = $this -> galapagosPhone;
		return $r."/".$st."/".$gp;
	}
	public function getSmartPhonePath(){
		$r = $this -> rootPath;
		$st = $this -> student;
		$sp = $this -> smartPhone;
		return $r."/".$st."/".$sp;
	}
	
	private function getPath($r,$s,$p){
		return $r."/".$s."/".$p;
	}
}
?>