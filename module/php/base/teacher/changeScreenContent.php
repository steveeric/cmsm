<?php
include('./mobileScreenObject.php');
class ChangeScreenContent{
	private $object;
	function __construct(){
		$this->object = new MobileScreenObject();
	}
	public function changeCTR($con,$list,$scheduleId){
		$un1 = "INSERT INTO MOBILE_SCREEN (RANDOM_NO, NOW_SCREEN_CONTENT_ID,SCHEDULE_ID) VALUES ";
		$un2 = " ON DUPLICATE KEY UPDATE NOW_SCREEN_CONTENT_ID = VALUES( NOW_SCREEN_CONTENT_ID ),SCHEDULE_ID = VALUES( SCHEDULE_ID )";
		$item="";
		$screen = $this -> object -> getCallTheRoll();
		for ($i = 0; $i< count($list); $i++) {
			//$r = $list[$i]["RANDOM_NO"];
			$r = $list[$i];
			$item = $item."('".$r."','".$screen."','".$scheduleId."')";
			if($i != (count($list) - 1)){
				$item = $item.",";
			}
		}
		$updateSQL = $un1.$item.$un2;
		$result = $con -> execute($updateSQL);
		return $result;
	}

}
?>