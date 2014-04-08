<?php
/**
 * 乱数と学籍番号を紐づけるphp
 * 
 * **/
/*DBアクセスセット*/
$dir =  dirname(__FILE__);
$path =  "../../../module/php/base/db/db_access.php";
include($path);

/*POSTされたでーたを取得*/
$studentId = $_POST['studentId'];
$randomNo = $_POST['r'];

//$studentId = $_GET['studentId'];
//$randomNo = $_GET['r'];

/*日付けを日本に設定*/
date_default_timezone_set('Asia/Tokyo');
$nowTime = date("Y-m-d H:i:s");
/**
 * REGISTER_MSTに学籍番号を登録する．
 * 
 * ***/
$updateSQL = "UPDATE `REGISTER_MST` SET `STUDENT_ID` = '".$studentId."',REGISTER_TIME = '".$nowTime."' WHERE `RANDOM_NO` = '".$randomNo."'";
$result = $con -> execute($updateSQL);

/**
 * MOBILE_SCREENにも登録
 * **/
$insertSQL = "INSERT INTO `MOBILE_SCREEN` (`RANDOM_NO`, `NOW_SCREEN_CONTENT_ID`, `SCHEDULE_ID`, `LAST_ACCESS_TIME`) VALUES ('".$randomNo."', 'register', '0', '".$nowTime."');";
$result = $con -> execute($insertSQL);

if ($result){
	$user =  array('STATE' => 0);
}else{
	$user =  array('STATE' => 1);
}

//jsonとして出力
header('Content-type: application/json');
echo json_encode($user);
?>