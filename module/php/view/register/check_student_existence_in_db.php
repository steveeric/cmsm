<?php
/*DBアクセスセット*/
$dir =  dirname(__FILE__);
$path =  "../../../module/php/base/db/db_access.php";
include($path);

/*POSTされたでーたを取得*/
$studentId = $_POST['studentId'];

/**
 * 学籍番号がSTUDENT_MSTにあるかをチェックする．**/
$sql = "SELECT STUDENT_ID,FULL_NAME FROM `STUDENT_MST` WHERE `STUDENT_ID` LIKE '".$studentId."' ";

$result = $con -> query($sql);

/*DBから同一学籍番号一件が見つかれば*/
if(count($result) == 1){
	$list =  array('STATE' => 0,
			'STUDENT_ID' => $result[0]["STUDENT_ID"],
			'FULL_NAME' => $result[0]["FULL_NAME"]);
}else{
	$list = array('STATE' => 1,
			'STUDENT_ID' => NULL,
			'FULL_NAME' => NULL);
}

//jsonとして出力
header('Content-type: application/json');
echo json_encode($list);
?>