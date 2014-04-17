<?php
include('viewObject.php');
/**/
include('module/php/base/db/db_access.php');
/*ガラケーとスマホ振り分け*/
include('module/php/distribut/phone.php');
/*メールモジュール読み込み*/
//include('module/php/base/mail/mail.php');

/*ユーザエージェント取得*/
$ua = $_SERVER['HTTP_USER_AGENT'];
/*携帯振り分け*/
$p = new DistributPhone($ua);
$a = new ViewObject();
$t = new Time();
/*乱数パラメータ*/
$randomNo = $_GET['r'];
$l=strlen($randomNo);
if($l != 20){
	echo "URLに誤りがあります．";
	echo "教員に申し出て下さい．";
}else{
	/*乱数を元に現在の画面情報を取得する*/
	/*$contentResult = $con -> getNowScreenContent($randomNo);*/
        $nowTime = $t-> getNowDetaileTime();
	$contentResult = $con -> getNowScreenContentDetaile($nowTime,$randomNo);
	if(count($contentResult) == 1){
		/*携帯キーを取得*/
		$key = $p -> distributPhoneKey();
		$contentId = $contentResult[0]['NOW_SCREEN_CONTENT_ID'];
		$registTime = $contentResult[0]['REGISTER_TIME'];
		$nowDate = $t -> getNowDate();
		$diff = dayDiff($registTime,$nowDate);
		$buttomURL = ".php?r=";
		$extension = ".php";
		/**
		 *
		 * ガラケー : 1
		 * スマホ    : 0
		 *
		 * 注意！！！！
		 * 必ず0にしておいてください．
		 * ***/
		if($key == 0){
			/*ガラケー*/
			$gpth = $a -> getGalapagosPhonePath();
			if($diff == 0){
				/*登録から一週間以内*/
				$url = $gpth."/".$contentId."/".$contentId.$extension;
				doPauseGPAcessSite($url,$randomNo);
			}else{
				$url = $gpth."/".$contentId."/".$contentId.$buttomURL.$randomNo;
				doAccessSite($url);
			}
		}else{
			$spth = $a -> getSmartPhonePath();
			if($diff == 0){
				/*登録から一週間以内*/
			       $url = $spth."/".$contentId."/".$contentId.$extension;
		     		doPauseSPAcessSite($url,$randomNo);
			}else{
				/*スマホ*/
				$spth = $a ->getSmartPhonePath();
				$url = $spth."/".$contentId."/".$contentId.$buttomURL.$randomNo;
				doAccessSite($url);
			}
		}
	}else{
		/*コンテンツを取得できませんでした.*/
		/*パラメータ正常屋で！！
		 * 何かおかしいぞ！！！*/
		/*パラメータDBにない！！！！*/
		/*不正！？*/
		$title = "画面コンテンツ";
		$error = "[".$randomNo."]さんが画面コンテンツを取得できません．";
		//$m = new Mail();
		//$m -> sendError($title, $error);
		echo "コンテンツを取得できませんでした.";
		echo "教員に申し出て下さい．";
	}
}

function dayDiff($registTime,$nowDay){
	$regDay = substr($registTime, 0, 10);
	$daydiff = (strtotime($nowDay)-strtotime($regDay))/(3600*24);
	$difKey=-1;
	if($daydiff < 40){
		/*一週間以内*/
		$difKey=0;
	}else{
		$difKey=1;
	}
	return $difKey;
}


/**URL発行サイトへ**/
function doAccessSite($url){
	//echo $url;
	header('Location: '.$url);
}

/**ガラパコス携帯用**/
function doPauseGPAcessSite($path,$randomNo){
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>遷移画面</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>画面遷移</div>
	<hr>
	<p>このサイトはあなたのマイページです．</p>
	<div>ブックマークしておいてください．</div>
	<div>中村先生の授業では，このサイトを通して</div>
	<div>様々な授業コンテンツを提供致します．</div>
	<div></div>
	<div>URL : http://aitech.ac.jp/scr/esl/cmsm/iv.phpr=$randomNo<div>
	<hr>
	<form action='$path' method='GET'>
		<input type='hidden' name='r'  value='$randomNo'>
		<input style='padding: 15px 70px;' type='submit' value='次の画面へ' />
	</form>
</body>
</html>
EOT;
}

/**スマホ用携帯サイト**/
function doPauseSPAcessSite($path,$randomNo){
echo <<<EOT
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Page Title</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet"
	href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script
	src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>
</head>
<script>
$(document).ready(function() {
});
</script>
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>画面遷移</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>このサイトはあなたのマイページです．</p>
			<p>ブックマークしておいてください．</p>
			<p>中村先生の授業では，このサイトを通して</p>
			<p>様々な授業コンテンツを提供致します．</p>
		</div>
		<hr>
		<div data-role="content" style="text-align: center">
		<a href='$path?r=$randomNo' target="_blank" style="text-align: center">次の画面へ</a>
		</div>
	</div>
	<!--content end-->
	<div data-role="footer" style="text-align: center">
		<h4>&copy; 2014 Primary Meta Works</h4>
	</div>
	<!-- footer end-->
	</div>
</body>
ロード中...
</html>
EOT;
}
?>
