<?php
include (dirname(__DIR__).'../../base/db/db_access.php');
/**現在授業中かを調べる**/
$tt = $time -> getTimeTableIdTime();
/* 授業開講時間 */
$timeResult = $con->getNowTimeTableId ( $tt );
$timeTableId = $timeResult [0] ['TIMETABLE_ID'];
/* 日付取得 */
$y = $time->getYear ();
$m = $time->getMonth ();
$d = $time->getDay ();
/**
 * アクセス時間と乱数を元に現在履修している科目があるかを割り出す*
*/
$sql = "SELECT S.SCHEDULE_ID, S.ACTION_ID
			FROM `COURSE_REGISTRATION_MST` C, REGISTER_MST R, SYLLABUS_MST S
			WHERE C.STUDENT_ID = R.STUDENT_ID
			AND C.SUBJECT_ID = S.SUBJECT_ID
			AND S.TIMETABLE_ID = '" . $timeTableId . "'
			AND S.YEAR = '" . $y . "'
			AND S.MONTH = '" . $m . "'
			AND S.DAY = '" . $d . "'
			AND R.RANDOM_NO = '" . $randomNo . "'";
$checkScheduleResult = $con->query ( $sql );
?>