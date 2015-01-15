<?
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
		//出席情報
		private $attendee;
		/**
		* コンストラクタ
 		**/
 		function __construct($studentId) {
 			$this -> studentId = $studentId;
   		}

   		public function setFullName($fullname){
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