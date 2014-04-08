<?php
/*URL発行サイトのオブジェクト*/
include('IssueObject.php');
/*ガラケーとスマホ振り分け*/
include('../module/php/distribut/phone.php');

/*ユーザエージェント取得*/
$ua = $_SERVER['HTTP_USER_AGENT'];

/*アクセス先のサイト*/
$a = new IssueObject();
/*携帯振り分け*/
$p = new DistributPhone($ua);

/*携帯キーを取得*/
$key = $p -> distributPhoneKey();

if($key == 0){
	/*ガラケー*/
	$url = $a ->getGalapagosPhoneSite();
	doAccessSite($url);
}else{
	/*スマホ*/
	$url = $a ->getSmartPhoneSite();
	doAccessSite($url);
}

/**URL発行サイトへ**/
function doAccessSite($url){
	header('Location: '.$url);
}

?>
