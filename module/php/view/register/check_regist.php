<?php
/**
 * DBのREGISTER_Mか/
 $path =  "../../../module/php/base/db/db_access.php";*/
include('../../../../module/php/base/db/db_access.php');

/*POSTされたでーたを取得*/
//$randomNo = $_GET['r'];
$randomNo = $_POST['r'];

/**DBに登録されているパラメータか登録されているかをチェック**/
$sql = "SELECT RANDOM_NO,STUDENT_ID,REGISTER_TIME FROM `REGISTER_MST` WHERE RANDOM_NO='".$randomNo."'";
$result = $con -> query($sql);

/**DBの状態**/
if(count($result) == 1){
	$id = $result[0]["STUDENT_ID"];
	$time = $result[0]["REGISTER_TIME"];
	$item = array('STUDENT_ID' => $id,
			'REGISTER_TIME' => $time);
	if(is_null($id)){
		/*登録で使っていなければ*/
		$registInfo = array('REGISTER_STATE' => 0,'REGISTER_INFO' => $item);
	}else{
		/*登録で使っている*/
		$registInfo = array('REGISTER_STATE' => 1,'REGISTER_INFO' => $item);
	}
	$list =  array('PARAMETA' => 0,'REGISTER' => $registInfo);
}else{
	/*DBにパラメータが無い
	 * つまり，不正か！？管理者のミス*/
	$list =  array('PARAMETA' => 1,'REGISTER' => $registInfo);
}

//jsonとして出力
header('Content-type: application/json');
echo json_encode($list);

?>
