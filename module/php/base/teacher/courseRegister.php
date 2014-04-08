<?php

/**
 * 
 * 履修者を返すクラス
 * 
 * **/

class CourseRegister{
	public function getCourseRegister($con,$scheduleId){
		/*履修者を取得する*/
		$sql = "SELECT SUBJECT_ID FROM `SYLLABUS_MST` WHERE `SCHEDULE_ID` LIKE '".$scheduleId."' ";
		$subjectIdResult = $con -> query($sql);
		$subjectId = $subjectIdResult[0]['SUBJECT_ID'];
		$courseSQL = "SELECT R.RANDOM_NO FROM `COURSE_REGISTRATION_MST` C,REGISTER_MST R
				WHERE C.STUDENT_ID = R.STUDENT_ID AND C.SUBJECT_ID LIKE '".$subjectId."' ";
		$courseRsult = $con -> query($courseSQL);
		for($i=0;$i<count($courseRsult);$i++){
			$courseList[] = $courseRsult[$i]["RANDOM_NO"];
		}
		return $courseList;
	}
}
?>