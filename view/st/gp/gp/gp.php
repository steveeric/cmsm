<?php
$randomNo = $_GET ['r'];
$scheduleId = $_GET ['s'];

$content = "gp";

include ('../../../../module/php/view/common/check_schedule.php');

if (count ( $checkScheduleResult ) == 0) {
	/* この授業は終わったよ */
	$sysql = "SELECT SY.YEAR, SY.MONTH, SY.DAY, SU.SUBJECT_NAME
			FROM `SYLLABUS_MST` SY, SUBJECT_MST SU
			WHERE SY.SUBJECT_ID = SU.SUBJECT_ID
			AND SY.SCHEDULE_ID = '" . $scheduleId . "'";
	$syResult = $con->query ( $sysql );
	$y = $syResult [0] ['YEAR'];
	$m = $syResult [0] ['MONTH'];
	$d = $syResult [0] ['DAY'];
	$subjectName = $syResult [0] ['SUBJECT_NAME'];
	$date = $y . "-" . $m . "-" . $d;
	encClassView ( $date, $subjectName );
} else {
	include ('../../../../module/php/view/common/check_gp_state.php');
	$nowScheduleId = $checkScheduleResult [0] ['SCHEDULE_ID'];
	if (strcasecmp ( $scheduleId, $nowScheduleId ) == 0) {
		include ('../../../../module/php/view/common/check_gp_state.php');
		/* 情報を提供する */
		if ($notSeat == 1) {
			/* 座席を割り振られなかった. */
			notSeatView ();
		} else {
			/* 正常に座席が割り振られた */
			confirmGroupView ( $studentId, $gpName, $blockName, $row, $column );
		}
	} else {
		/* この授業は終わったよ */
		$sysql = "SELECT SY.YEAR, SY.MONTH, SY.DAY, SU.SUBJECT_NAME
			FROM `SYLLABUS_MST` SY, SUBJECT_MST SU
			WHERE SY.SUBJECT_ID = SU.SUBJECT_ID
			AND SY.SCHEDULE_ID = '" . $scheduleId . "'";
		$syResult = $con->query ( $sysql );
		$y = $syResult [0] ['YEAR'];
		$m = $syResult [0] ['MONTH'];
		$d = $syResult [0] ['DAY'];
		$subjectName = $syResult [0] ['SUBJECT_NAME'];
		$date = $y . "-" . $m . "-" . $d;
		encClassView ( $date, $subjectName );
	}
}

?>

<?php
/**
 * 座席が割り振られなかった
 * *
 */
function notSeatView() {
	hedd ( "座席不足" );
	echo <<<EOT
	<hr>
		<div>着席できる座席を割り当てることが</div>
		<div>できませんでした.</div>
			<div>教員に申し出てください.</div>
	<hr>
EOT;
	footer ();
}

/**
 * 座席が割り振られなかった
 * *
 */
function confirmGroupView($stid, $gpName, $b, $r, $c) {
	hedd ( "本日のグループ" );
	echo <<<EOT
	<hr>
		<div>[$stid]さんは､</div>
		<div>[$gpName]グループです.</div>
			<div> $b 群 $r 行 - $c 列</div>
		<div>に着席してください.</div>
	<hr>
EOT;
	footer ();
}
function hedd($t) {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>$t</title>
</head>
<body style="width: 200px;">
	<div>$t</div>
EOT;
}
function footer() {
	echo <<<EOT
</body>
</html>
EOT;
}
?>