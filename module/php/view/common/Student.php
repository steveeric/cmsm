<?php
	/*
	* 作成日 : 2015年1月15日
	* Studentクラス
	* 学生情報を保持するクラスです.
	*
	*
	*
	*/
   class Student{
		private $studentId;
		private $fullName;
		private $attendee;

      function __construct($studentId) {
         $this -> studentId = $studentId;
      }
      public function setFullName($fullName){
   		$this -> fullName = $fullName;
   	}
   	public function setAttendee($attendee){
   		$this -> attendee = $attendee;
   	}
   	public function getStudentId(){
   		return $this -> studentId;
   	}
   	public function getFullName(){
   		return $this -> fullName;
   	}
   	public function getAttendee(){
   		return $this -> attendee;
   	}
   }
?>