<?php
$randomNo = $_GET ['r'];
$scheduleId = $_GET ['s'];

$content="gp";

include ('../../../../module/php/view/common/check_gp_state.php');

if ($notSeat == 1) {
	/* 座席を割り振られなかった. */
	notSeatView ();
} else {
	/* 正常に座席が割り振られた */
	confirmGroupView ( $studentId, $gpName, $blockName, $row, $column );
}

?>

<?php
/**
 * 座席が割り振られなかった
 * *
 */
function notSeatView() {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>座席不足</title>
</head>
<body style="width: 200px;">
	<div>座席不足</div>
	<hr>
		<div>着席できる座席を割り当てることが</div>
		<div>できませんでした.</div>
			<div>教員に申し出てください.</div>
	<hr>
</body>
</html>
EOT;
}

/**
 * 座席が割り振られなかった
 * *
 */
function confirmGroupView($stid, $gpName, $b, $r, $c) {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>本日のグループ</title>
</head>
<body style="width: 200px;">
	<div>本日のグループ</div>
	<hr>
		<div>[$stid]さんは､</div>
		<div>[$gpName]グループです.</div>
			<div> $b 群 $r 行 - $c 列</div>
		<div>に着席してください.</div>
	<hr>
</body>
</html>
EOT;
}
?>
