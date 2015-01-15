<?php
$miniTimeZone = 3600;

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
	
	/*テスト時にはKEYを変更してください.*/
	$key = $p->distributPhoneKey ();
        //$key = 0;
	
	/* 現在のアクセス時間を取得する */
	$nowTime = $t->getNowDetaileTime ();
	
	/* アクセス時間を記録 */
	$recordSQL = "UPDATE `MOBILE_SCREEN` SET LAST_ACCESS_TIME = '" . $nowTime . "',`SMART_PHONE_FLAG` = '" . $key . "',USER_AGENT='" . $ua . "'  WHERE `RANDOM_NO` = '" . $randomNo . "';";
	$con->execute ( $recordSQL );
	
	/* アクセス時間を取得 */
	$tt = $t->getTimeTableIdTime ();
	
	/* 現在が */
	$timeZoneSQL = "SELECT MIN( `CLASS_START_TIME` ) , MAX( `CLASS_END_TIME` ) FROM `TIMETABLE_MST` ";
	$timeZoneResult = $con->query ( $timeZoneSQL );
	$pretimeZoneMini = $timeZoneResult [0] ["MIN( `CLASS_START_TIME` )"];
	$timeZoneMax = $timeZoneResult [0] ["MAX( `CLASS_END_TIME` )"];
	$timeZoneMini = date ( 'H:i:s', (strtotime ( $pretimeZoneMini ) - $miniTimeZone) );
	if ($tt < $timeZoneMini) {
		/* 1限開始前 */
		doBeforeTheStartOfClasses ( $key );
	} else if ($timeZoneMax < $tt) {
		/* 今日の授業はすべて終了している */
		doAfterTheStartOfClasses ( $key );
	} else {
		/* 授業開講時間 */
		$timeResult = $con->getNowTimeTableId ( $tt );
		$timeTableId = $timeResult [0] ['TIMETABLE_ID'];
		/* 日付取得 */
		$y = $t->getYear ();
		$m = $t->getMonth ();
		$d = $t->getDay ();
		/**
		 * アクセス時間と乱数を元に現在履修している科目があるかを割り出す*
		 */
		$sql = "SELECT S.SCHEDULE_ID, S.ACTION_ID, S.USE_ESL,R.DM_BARCODE_ID, SU.SUBJECT_NAME ,C.COURSE_REGISTRATION_PERMIT 
			FROM `COURSE_REGISTRATION_MST` C, REGISTER_MST R, SYLLABUS_MST S,SUBJECT_MST SU 
			WHERE C.STUDENT_ID = R.STUDENT_ID
			AND C.SUBJECT_ID = S.SUBJECT_ID 
			AND S.SUBJECT_ID =SU.SUBJECT_ID 
			AND S.TIMETABLE_ID = '" . $timeTableId . "'
			AND S.YEAR = '" . $y . "'
			AND S.MONTH = '" . $m . "'
			AND S.DAY = '" . $d . "'
			AND R.RANDOM_NO = '" . $randomNo . "'";
		$result = $con->query ( $sql );
		//var_dump($result);
                if (count ( $result ) == 1) {
			/* 履修科目があれば */
			$aId = $result [0] ['ACTION_ID'];
			$scheduleId = $result [0] ['SCHEDULE_ID'];
			$useESL = $result [0] ['USE_ESL'];
			$dmBarcodeId = null;
			$dmBarcodeId = $result [0] ['DM_BARCODE_ID'];
			$subjectName = $result [0] ['SUBJECT_NAME'];
			$permit = $result[0] ['COURSE_REGISTRATION_PERMIT'];
			if($permit == 0){
                                   /*授業ヘの参加を許可していない場合*/
                                   /*宿題4下位以上忘れている*/
				   doNotCourseRegistrationPermit($key,$subjectName);
			}else{
				/*授業ヘの参加を許可している場合*/
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
					
				/* 表示する画面コンテンツを取得する */
				$contentResult = $con->getNowScreenContentDetaile ( $nowTime, $randomNo );
				if (count ( $contentResult ) == 1) {
					/* 携帯キーを取得 */
					//$key = $p->distributPhoneKey ();
					//$key=0;
					$contentId = $contentResult [0] ['NOW_SCREEN_CONTENT_ID'];
					$registTime = $contentResult [0] ['REGISTER_TIME'];
					$nowDate = $t->getNowDate ();
					$diff = dayDiff ( $registTime, $nowDate );
					$diff=1;
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
					// $nowDateESL =　$t -> getNowDate();
					$waitTime = "12時30分";
					if ($key == 0) {
						/* ガラケー */
						$gpth = $a->getGalapagosPhonePath ();
						/* ESLを使うかをチェックする */
						/*ESLが登録されているかをチェックする*/
						if ($useESL == 1 && $dmBarcodeId != null) {
							/* ESLを使う */
							doUseESLGP ( $nowDate, $subjectName, $waitTime );
						} else {
							if ($diff == 0) {
								/* 登録から一週間以内 */
								$url = $gpth . "/" . $contentId . "/" . $contentId . $extension;
								doPauseGPAcessSite ( $url, $randomNo, $scheduleId );
							} else {
								$url = $gpth . "/" . $contentId . "/" . $contentId . $buttomURL . $randomNo."&s=".$scheduleId;
								doAccessSite ( $url );
							}
						}
					} else {
						$spth = $a->getSmartPhonePath ();
						/* ESLを使うかをチェックする */
						/*ESLが登録されているかをチェックする*/
						if ($useESL == 1 && $dmBarcodeId != null) {
							/* ESLを使う */
							doUseESLSP ( $nowDate, $subjectName, $waitTime );
						} else {
							if ($diff == 0) {
								/* 登録から一週間以内 */
								$url = $spth . "/" . $contentId . "/" . $contentId . $extension;
								doPauseSPAcessSite ( $url, $randomNo, $scheduleId );
							} else {
								/* スマホ */
								$spth = $a->getSmartPhonePath ();
								$url = $spth . "/" . $contentId . "/" . $contentId . $buttomURL . $randomNo."&s=".$scheduleId;
								doAccessSite ( $url );
							}
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
		} else {
                        $sql = "SELECT M.RANDOM_NO, R.REGISTER_TIME, M.NOW_SCREEN_CONTENT_ID, S.STUDENT_ID, S.FULL_NAME
                                FROM `MOBILE_SCREEN` M, REGISTER_MST R, STUDENT_MST S
                                WHERE M.RANDOM_NO = R.RANDOM_NO
                                AND R.STUDENT_ID = S.STUDENT_ID
                                AND M.`RANDOM_NO` LIKE '".$randomNo."'";
                         $result = $con -> query($sql);
                         //var_dupm($result);
                         $nowScreenContentId = $result[0]["NOW_SCREEN_CONTENT_ID"];
                         $studentId = $result[0]["STUDENT_ID"];
                         $fullName = $result[0]["FULL_NAME"];
                         $registerTime = $result[0]["REGISTER_TIME"];

                         if(strcmp($nowScreenContentId,"register") == 0){
                           /*登録サイトを表示する*/
                           registerInfo( $key, $registerTime, $studentId, $fullName);
                         }else{
			   /* 現在の時間帯に履修科目がない */
			   doNotClasses ( $key );
                         }
		}
	}
}
function dayDiff($registTime, $nowDay) {
	$regDay = substr ( $registTime, 0, 10 );
	$daydiff = (strtotime ( $nowDay ) - strtotime ( $regDay )) / (3600 * 24);
	$difKey = - 1;
	if ($daydiff < 100) {
		/* 一週間以内 */
		$difKey = 0;
	} else {
		$difKey = 1;
	}
        $difKey=0;
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
	 //echo $url;
	header ( 'Location: ' . $url );
}
/**
 * *
 *
 * 全授業開始前
 *
 * *
 */
function doBeforeTheStartOfClasses($k) {
	if ($k == 1) {
		/* スマートフォン */
		doSPBeforeTheStartOfClasses ();
	} else {
		/* ガラパゴス */
		doGPBeforeTheStartOfClasses ();
	}
}
// ガラパゴスフォン用
function doGPBeforeTheStartOfClasses() {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>授業開始前</title>
</head>
<body style="width: 200px;">
	<div>授業開始前</div>
	<hr>
	<div>おはようございます.</div>
	<div>現在授業開始前です.</div>
	<div>しばらくお待ちください.</div>
	<hr>
</body>
</html>
EOT;
}
// スマートフォン用
function doSPBeforeTheStartOfClasses() {
	spHeadder ( "beforeTheStartOfClasses" );
	echo <<<EOT
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>授業開始前</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>おはようございます.</p>
			<p>現在授業開始前です.</p>
			<p>しばらくお待ちください.</p>
		</div>
		<hr>
	</div>
	<!--content end-->
EOT;
	spFooter ();
}

/**
**
***/

function registerInfo($k,$registerTime,$id,$name) {
        $str1 = "<p>[STEP6]～[STEP8]の操作が異なります.</p> <p>現在見ているWEBサイトをブックマークして下さい.</p> <div></div><p>以下の学生が登録しています.</p>";
        $str2 = "<p>このWEBサイトはあなたのマイページです.</p><p>ブックマークしておいてください.</p> <p>中村先生の授業では、このサイトを通して</p><p>様々な授業コンテンツを提供致します.</p>";
        if ($k == 1) {
                /* スマートフォン */
                registerSpInfo($str1,$str2,$registerTime,$id,$name);
        } else {
                /* ガラパゴス */
                registerGpInfo($str1,$str2,$registerTime,$id,$name);
        }
}

function registerSpInfo($str1,$str2,$time,$id,$name){

 spHeadder ( "registerSpInfo" );
        echo <<<EOT
<body>
        <div data-role="page" id="first" data-theme="b">
                <div data-role="header">
                        <h1>登録内容確認</h1>
                </div>
                <div data-role="content" style="text-align: center">
                $str1
　　　　　　　　<div>学籍番号 : $id </div>
                <div>登録日 : $time </div>
                <p></p>
                $str2
                </div>
        <!--content end-->
EOT;
        spFooter ();

}

function registerGpInfo($str1,$str2,$time,$id,$name){
        echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>AIT.MyPage</title>
</head>
<body style="width: 200px;">
        <div>登録内容確認</div>
        <hr>
         $str1
         <div>学籍番号 : $id </div>
         <div>登録日 : $time </div>
         <p></p>
         $str2
        <hr>
</body>
</html>
EOT;

}

/**
 * *
 *
 * 履修科目がない
 *
 * *
 */
function doNotClasses($k) {
	if ($k == 1) {
		/* スマートフォン */
		doNotSPClasses ();
	} else {
		/* ガラパゴス */
		doNotGPClasses ();
	}
}

// スマートフォン
function doNotSPClasses() {
	spHeadder ( "doNotCourseClasses" );
	echo <<<EOT
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>履修科目がありません</h1>
		</div>
		<div data-role="content" style="text-align: center">
		<div>現在の時間帯に提供できるコンテンツがありません.</div>
                </div>
	<!--content end-->
EOT;
	spFooter ();
}

function spHeadder($title) {
	echo <<<EOT
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>$title</title>
<link rel="stylesheet" type="text/css"	href="tool/css/style.css">
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
EOT;
}
function spFooter() {
	echo <<<EOT
<div data-role="footer" style="text-align: center" data-position="fixed">
		<h4>&copy; 2014 Primary Meta Works</h4>
	</div>
	<!-- footer end-->
	</div>
</body>
ロード中...
</html>
EOT;
}

// ガラパゴズ用
function doNotGPClasses() {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>履修科目がありません</title>
</head>
<body style="width: 200px;">
	<div>履修科目がありません</div>
	<hr>
	<div>現在の時間帯に提供できるコンテンツがありません.</div>
        <hr>
</body>
</html>
EOT;
}

/**
 * *
 *
 * 全授業終了後
 *
 * *
 */
function doAfterTheStartOfClasses($k) {
	if ($k == 1) {
		/* スマートフォン */
		doSPAfterTheStartOfClasses ();
	} else {
		/* ガラパゴス */
		doGPAfterTheStartOfClasses ();
	}
}
function doSPAfterTheStartOfClasses() {
	spHeadder ( "afterTheStartOfClasses" );
	echo <<<EOT
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>全授業終了</h1>
		</div>
		<div data-role="content" style="text-align: center">
		<div>本日の授業は,すべて終了しました.</div>
		</div>
		<hr>
	</div>
	<!--content end-->
EOT;
	spFooter ();
}

// ガラパゴスフォン用
function doGPAfterTheStartOfClasses() {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>全授業終了</title>
</head>
<body style="width: 200px;">
	<div>全授業終了</div>
	<hr>
	<div>本日の授業は,すべて終了しました.</div>
	<hr>
</body>
</html>
EOT;
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
 * ガラパコス携帯用*
 */
function doUseESLGP($nowDate, $subName, $waitTime) {
	echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>連絡事項</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>連絡事項</div>
	<hr>
	<div>$nowDate の、</div>
	<div>$subName では、</div>
	<div>ESLを使用します.</div>
	<div>ESLを首から下げて</div>
	<div>講義室へ来てください.</div>
	<div>$waitTime 以降に本日の着席位置がESLに表示されます.</div>
	<div>　</div>
	<div>本日からESLを忘れた学生は講義を受講できません.</div>
        <div>ただし、宿題の提出は受付ますので</div>
        <div>講義室の入り口で提出し,次回の宿題を受け取ってください.</div>
        <div>もし、ESLをまだもらっていない学生は個別に対応しますので,</div>
        <div>TAに申し出てください.</div>
</body>
</html>
EOT;
}

/**
 * スマホ用携帯サイト*
 */
function doUseESLSP($nowDate, $subName, $waitTime) {
	spHeadder ( "連絡事項" );
	echo <<<EOT
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>連絡事項</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>$nowDate の、</p>
			<p>$subName では、</p>
			<p>ESLを使用します.</p>
			<p>ESLを首から下げて</p>
			<p>講義室へ来てください.</p>
			<p>$waitTime 以降に本日の着席位置がESLに表示されます.</p>
			<p>　</p>
			<p>本日からESLを忘れた学生は講義を受講できません.</p>
                        <p>ただし、宿題の提出は受付ますので</p>
                        <p>講義室の入り口で提出し,次回の宿題を受け取ってください.</p>
                        <p>もし、ESLをまだもらっていない学生は個別に対応しますので,</p>
                        <p>TAに申し出てください.</p>
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
/**
 * スマホ用携帯サイト*
 */
function doPauseSPAcessSite($path, $randomNo, $scheduleId) {
	spHeadder ( "MyPage" );
	echo <<<EOT
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


/**授業ヘの参加を認めない場合**/
function doNotCourseRegistrationPermit($key,$subjectName) {
	if($key == 0){
		//ガラケー用
		echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<title>連絡事項</title>
<script type="text/javascript" src="js/main.js"></script>
</head>
<body style="width: 200px;">
	<div>連絡事項</div>
	<hr>
　　　　<div>コンテンツをご提供することができません.</div>
	<div>宿題を4回以上提出しなかったので、</div>
        <div>$subjectName への、</div>
	<div>参加をすることができません.</div>
	<div>もし、違う場合はお手数ですが教員に申し出てください.</div>
</body>
</html>
EOT;
	}else{
		//スマートフォン用
		spHeadder ( "afterTheStartOfClasses" );
		echo <<<EOT
	<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>連絡事項</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>コンテンツをご提供することができません.</p>
　　　　　　　　　　　　<p>宿題を4回以上提出しなかったので、</p>
                        <p>$subjectName への、</p>
			<p>参加をすることができません.</p>
			<p>もし、違う場合はお手数ですが教員に申し出てください.</p>
		</div>
		<hr>
	</div>
EOT;
		spFooter ();
	}
}

/**授業ヘの参加を認めない場合**/

?>
