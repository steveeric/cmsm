<?php

//include(dirname(__FILE__).'../module/php/base/db/access.php');
//include(dirname(__FILE__).'../module/php/base/db/db_access.php');

//include_once(dirname(__FILE__).'../../module/php/base/Time/time.php');
include_once('../module/php/base/db/db_access.php');


/*POST*/
$studentId = $_POST['id'];
$y = $_POST['y'];
$m = $_POST['m'];
$d = $_POST['d'];
$process = $_POST['p'];

if(empty($studentId) == TRUE || empty($y) == TRUE || empty($m) == TRUE || empty($d) == TRUE){
	emptyText($studentId,$y,$m,$d);
}else{
	/*入力された月と*/
	$ml = strlen($m);
	if($ml != 2){
		$m = "0".$m;
	}
	/*入力された日のレングスを見る*/
	$dl = strlen($d);
	if($dl != 2){
		$d = "0".$d;
	}

	$result = $con -> checkExistenceBirthDayAndStudentId($studentId,$y,$m,$d);
	if(count($result) == 1){
		$regTime = $result[0]['REGISTER_TIME'];
		$studentId = $result[0]['STUDENT_ID'];
		$fullName = $result[0]['FULL_NAME'];
		$randomNo = $result[0]['RANDOM_NO'];
		if(is_null($regTime)){
			/*初回登録*/
			//errorNotStudentIdInDB($studentId);
			if($process == 1){
				/*登録*/
				$nowTime = $time -> getNowTime();
				$r = $con -> recordRegisterTime($randomNo,$nowTime);
				$rr = $con -> insertMobileScreen($randomNo,$nowTime);
				$urlResult = $con ->getURL();
				$unURL = $urlResult[0]['URL'];
				$url = $unURL."?r=".$randomNo;
				completeView($url);
			}else{
				confirmView($studentId,$fullName,$y,$m,$d);
			}
		}else{
			$urlResult = $con ->getURL();
			$unURL = $urlResult[0]['URL'];
			$url = $unURL."?r=".$randomNo;
			/*登録済み*/
			completeView($url);
		}
	}else{
		/*DBに登録されていないか,登録されている情報がおかしい*/
		errorNotStudentIdInDB($studentId,$y,$m,$d);
	}
}

function confirmView($id,$name,$y,$m,$d){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />

<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<title>確認画面</title>
</head>
<body style="width: 200px;">
	<div>表示されている内容は正しいですか．</div>
	<hr>
		<div>
			学籍番号 : $id
		</div>
		<div>
			氏　名  : $name
		</div>
		<hr>
		<form action='gpc.php' method='POST'>
		<input type='hidden' name='id'  value='$id'>
		<input type='hidden' name='y'  value='$y'>
		<input type='hidden' name='m'  value='$m'>
		<input type='hidden' name='d'  value='$d'>
		<input type='hidden' name='p'  value='1'>
		<input style='padding: 15px 70px;' type='submit' value='はい' />
		</form>
		<form action='gp.php' method='POST'>
		<input style='padding: 15px 70px;' type='submit' value='訂正' />
		</form>
</body>
</html>
EOT;
}

function completeView($url){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />

<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<title>URL発行画面</title>
</head>
<body style="width: 200px;">
	<div>自分専用のURLです．</div>
	<hr>
		<div>
		<a href=$url>$url</a>
		</div>
			<p>URLを選択してリンク先に飛んでください．</p>
		<hr>
</body>
</html>
EOT;
}


/*DBに学籍番号が登録されていなかった*/
function errorNotStudentIdInDB($id,$y,$m,$d){
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
</head>
<body style="width: 200px;">
	<div>照合できませんでした．</div>
	<hr>
		<div>
			入力内容
		</div>
		<div>
			学籍番号 :　$id
		</div>
		<div>
			生年月日 :
		</div>
		<div>
			"$y"年 "$m"月 "$d"日
		</div>
		
		<div>
			もう一度入力し直して下さい.
		</div>
		<hr>
	<form action='gp.php' method='POST'>
		<input style='padding: 15px 70px;' type='submit' value='戻る' />
	</form>
</body>
</html>
EOT;
}
/**入力されていない箇所があった場合**/
function emptyText($id,$y,$m,$d){
	$e = "";
	if(strlen($id) == 0){
		$e = "[学籍番号]".$e;
	}if(strlen($y) == 0){
		$e = $e." [誕生年]";
	}if(strlen($m) == 0){
		$e = $e." [誕生月]";
	}if(strlen($d) == 0){
		$e = $e." [誕生日]";
	}
	$e = $e."が,入力されていません.";
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />

<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
<title>URL発行画面</title>
</head>
<body style="width: 200px;">
	<div>未入力箇所があります．</div>
	<hr>
	<div>$e</div>
	<form action='gp.php' method='POST'>
		<input style='padding: 15px 70px;' type='submit' value='戻る' />
	</form>
</body>
</html>
EOT;
}
?>
