<?php
include ('viewObject.php');
include ('module/php/base/db/db_access.php');
/* ガラケーとスマホ振り分け */
include ('module/php/distribut/phone.php');
/* メールモジュール読み込み */
// include('module/php/base/mail/mail.php');

/* ユーザエージェント取得 */
$ua = $_SERVER ['HTTP_USER_AGENT'];
/* 携帯振り分け */
$p = new DistributPhone ( $ua );
$a = new ViewObject ();
$t = new Time ();
/* 乱数パラメータ */
$randomNo = $_GET ['r'];
$l = strlen ( $randomNo );
if ($l != 20) {
	echo "URLに誤りがあります．";
	echo "教員に申し出て下さい．";
} else {
	/* 乱数を元に現在の画面情報を取得する */
	/*$contentResult = $con -> getNowScreenContent($randomNo);*/
	
	/* 現在のアクセス時間を取得する */
	$nowTime = $t->getNowDetaileTime ();
	/*アクセス時間を取得*/
	$tt = $t->getTimeTableIdTime ();
	$timeResult = $con->getNowTimeTableId ( $tt );
	$timeTableId = $timeResult[0]['TIMETABLE_ID'];
	/* 日付取得 */
	$y = $t->getYear ();
	$m = $t->getMonth ();
	$d = $t->getDay();
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
	$result = $con->query ( $sql );
	if (count ( $result ) == 1) {
		/* 履修科目があれば */
		$aId = $result [0] ['ACTION_ID'];
		$scheduleId = $result [0] ['SCHEDULE_ID'];
		if ($aId == 3) {
			/* 座席指定 */
			$upsql = "UPDATE `MOBILE_SCREEN` SET `NOW_SCREEN_CONTENT_ID` = 'sels',SCHEDULE_ID = '" . $scheduleId . "' 
					WHERE `RANDOM_NO` = '" . $randomNo . "' ";
			$upresult = $con->execute ( $upsql );
		} else if ($aId == 9) {
			/* グルーピング */
			$upsql = "UPDATE `MOBILE_SCREEN` SET `NOW_SCREEN_CONTENT_ID` = 'gp',SCHEDULE_ID = '" . $scheduleId . "'
					WHERE `RANDOM_NO` = '" . $randomNo . "' ";
			$upresult = $con->execute ( $upsql );
		} else {
			$upsql = "UPDATE `MOBILE_SCREEN` SET `NOW_SCREEN_CONTENT_ID` = 'ctr',SCHEDULE_ID = '" . $scheduleId . "'
					WHERE `RANDOM_NO` = '" . $randomNo . "' ";
			/* 端末から出席開始 */
			$upresult = $con->execute ( $upsql );
		}
	}
	
	/* 表示する画面コンテンツを取得する */
	$contentResult = $con->getNowScreenContentDetaile ( $nowTime, $randomNo );
	if (count ( $contentResult ) == 1) {
		/* 携帯キーを取得 */
		$key = $p->distributPhoneKey ();
		$contentId = $contentResult [0] ['NOW_SCREEN_CONTENT_ID'];
		$registTime = $contentResult [0] ['REGISTER_TIME'];
		$nowDate = $t->getNowDate ();
		$diff = dayDiff ( $registTime, $nowDate );
		$buttomURL = ".php?r=";
		$extension = ".php";
		/**
		 * ガラケー : 0
		 * スマホ : 1
		 *
		 * 注意！！！！
		 * 必ず0にしておいてください．
		 * **
		 */
		if ($key == 0) {
			/* ガラケー */
			$gpth = $a->getGalapagosPhonePath ();
			if ($diff == 0) {
				/* 登録から一週間以内 */
				$url = $gpth . "/" . $contentId . "/" . $contentId . $extension;
				doPauseGPAcessSite ( $url, $randomNo, $scheduleId );
			} else {
				$url = $gpth . "/" . $contentId . "/" . $contentId . $buttomURL . $randomNo;
				doAccessSite ( $url );
			}
		} else {
			$spth = $a->getSmartPhonePath ();
			if ($diff == 0) {
				/* 登録から一週間以内 */
				$url = $spth . "/" . $contentId . "/" . $contentId . $extension;
				doPauseSPAcessSite ( $url, $randomNo, $scheduleId );
			} else {
				/* スマホ */
				$spth = $a->getSmartPhonePath ();
				$url = $spth . "/" . $contentId . "/" . $contentId . $buttomURL . $randomNo;
				doAccessSite ( $url );
			}
		}
	} else {
		/* コンテンツを取得できませんでした. */
		/*パラメータ正常屋で！！
		 * 何かおかしいぞ！！！*/
		/*パラメータDBにない！！！！*/
		/*不正！？*/
		$title = "画面コンテンツ";
		$error = "[" . $randomNo . "]さんが画面コンテンツを取得できません．";
		// $m = new Mail();
		// $m -> sendError($title, $error);
		echo "コンテンツを取得できませんでした.";
		echo "教員に申し出て下さい．";
	}
}
function dayDiff($registTime, $nowDay) {
	$regDay = substr ( $registTime, 0, 10 );
	$daydiff = (strtotime ( $nowDay ) - strtotime ( $regDay )) / (3600 * 24);
	$difKey = - 1;
	if ($daydiff < 40) {
		/* 一週間以内 */
		$difKey = 0;
	} else {
		$difKey = 1;
	}
	return $difKey;
}
function excute($upsql) {
	$upresult = $con->execute ( $upsql );
	if ($upresult) {
	} else {
		/* DBに問題発生 */
		/*その旨をメールデ送信してください。*/
	}
}

/**
 * URL発行サイトへ*
 */
function doAccessSite($url) {
	// echo $url;
	header ( 'Location: ' . $url );
}

/**
 * ガラパコス携帯用*
 */
function doPauseGPAcessSite($path, $randomNo, $scheduleId) {
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
	<div>URL : http://aitech.ac.jp/scr/esl/cmsm/iv.php?r=$randomNo<div>
	<hr>
	<form action='$path' method='GET'>
		<input type='hidden' name='r'  value='$randomNo'>
		<input type='hidden' name='s'  value='$scheduleId'>
		<input style='padding: 15px 70px;' type='submit' value='次の画面へ' />
	</form>
</body>
</html>
EOT;
}

/**
 * スマホ用携帯サイト*
 */
function doPauseSPAcessSite($path, $randomNo, $scheduleId) {
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
		<a href='$path?r=$randomNo&s=$scheduleId' target="_blank" style="text-align: center">次の画面へ</a>
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
