<?php
include('../../../../module/php/base/db/db_access.php');
/*乱数*/
$randomNo = $_GET['r'];

/*2重登録チェック*/
$result = $con -> checkRnadomNoInDB($randomNo);
$studentId = $result[0]['STUDENT_ID'];
if(is_null($result[0]['REGISTER_TIME'])){
	/*日付けを日本に設定*/
	date_default_timezone_set('Asia/Tokyo');
	$nowTime = date("Y-m-d H:i:s");
	$updateSQL = "UPDATE `REGISTER_MST` SET `STUDENT_ID` = '".$studentId."',REGISTER_TIME = '".$nowTime."' WHERE `RANDOM_NO` = '".$randomNo."';";
	$updateResult = $con -> execute($updateSQL);
	if($updateResult){
		/*登録正常に終了*/
		$insertSQL = "INSERT INTO `MOBILE_SCREEN` (`RANDOM_NO`, `NOW_SCREEN_CONTENT_ID`, `SCHEDULE_ID`, `LAST_ACCESS_TIME`) VALUES ('".$randomNo."', 'register', '0', '".$nowTime."');";
		$insertResult = $con -> execute($insertSQL);
		if($insertResult){
			sucessRegister($studentId,$nowTime);
		}else{
			/*DBに問題発生*/
			errorDoNotUpdate($studentId);
		}
	}else{
		/*DBに問題発生*/
		errorDoNotUpdate($studentId);
	}
}else{
	/*2重登録*/
	$id = $result[0]['STUDENT_ID'];
	$result = $con -> getRegistrationTime($randomNo);
	pastRegister($id,$result);
}
?>
<?php 
/**
 * 入力した内容が上手くDBに反映されなかった場合
 * **/
function errorDoNotUpdate($studentId){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
	<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>登録不可画面</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>登録できませんでした</div>
	<hr>
	<div>入力された「$studentId」は，登録できませんでした．</div>
			<div>教員に申し出て下さい．</div>
</body>
</html>
EOT;
}

/**もう一度入力画面に戻す**/
function errorNotStudentIdInDB($studentId,$randomNo){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
	<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>登録不可画面</title>
</head>
<body style="width: 200px;">
	<div>登録できませんでした</div>
	<hr>
	<div>入力された"$studentId"は，使用できません．</div>
			<div>前の画面に戻り，もう一度学籍番号の入力をお願い致します．</div>
<form action='../iv.php' method='GET'>
			<input type='hidden' name='r'  value='$randomNo'>
		<hr>
		<input style='padding: 15px 70px;' type='submit' value='戻る' />
	</form>
</body>
</html>
EOT;
}

/**正常に登録が行えた**/
function sucessRegister($studentId,$time){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<title>登録内容確認画面</title>
</head>
<body style="width: 200px;">
	<div>登録内容確認</div>
	<hr>
	<div>登録は正常に行えました．</div>
	<div>以下の内容で登録しました．</div>
	<BR>
	<div>登録日 :	$time</div>
			<div>学籍番号 :	$studentId</div>
			<div>これからは，登録で使用したQRコードを使用しますので紛失しないようお願い致します．</div>
					<hr>
</body>
</html>
EOT;
}


/*すでに紐づけて使用した．*/
function pastRegister($studentId,$result){
	//$result = $con -> getRegistrationTime($randomNo);
	if(count($result) == 1){
		/*登録されている内容を画面に出力する*/
		$registTime = $result[0]['REGISTER_TIME'];
		echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<title>登録用画面</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>登録内容確認</div>
	<hr>
	<div>以下の学生が登録しています．</div>
	<BR>
		<div>学籍番号 : $studentId</div>
		<div>登録日 :	$registTime</div>
		<hr>
</body>
</html>
EOT;
	}else{
/*エラー発生*/
echo "404";
echo "過去に登録していますが，DBに不具合が生じました．";
}
}
?>
