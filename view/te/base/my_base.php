<?php
include_once(dirname(__DIR__).'../../../tool/db/DBObject.php');
include_once(dirname(__DIR__).'../../../tool/db/mysql.php');
//include_once(dirname(__DIR__).'../../../module/php/base/db/db_access.php');
include_once(dirname(__DIR__).'../../../module/php/base/teacher/TeacherObject.php');
class MyBase{
	private $scheduleId;
	private $con;
	private $teacherObject;
	function __construct(){
		/**/
		/**/
		$o = new DBObject();
		$h = $o -> getHost();
		$u = $o -> getUser();
		$p = $o -> getPass();
		$db = $o -> getDBName();
		$this -> con = new DB($h,$u,$p,$db);
		/**/
		$this -> teacherObject = new Teacher();;
	}
	public function setPastScheduleId($s){
		$this -> scheduleId = $s;
	}
	public function getPastScheduleId(){
		return $this -> scheduleId;
	}
	public function getConnection(){
		return $this -> con;
	}
	public function getTeacherId(){
		return $this -> teacherId;
	}
	
}



?>