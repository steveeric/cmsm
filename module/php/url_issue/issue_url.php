<?php

/**/
//include(dirname(__FILE__) ."../base/db/db_access.php");
include_once(dirname(__FILE__).'../../base/db/db_access.php');
/*ポストで渡されたIDを受け取る*/
$studentId = $_POST['id'];
//$studentId = $_GET['id'];
/*アクセス時間を保持*/
$accessTime = $time -> getNowTime();

//パラメータをチェックする
if(empty($studentId)){
	/*パラメータが無い*/
	json(1,null,-1,null);
}else{
	/**/
	$registerResult = $con -> checkRegisterStudentId($studentId);
	if(count($registerResult) == 1){
		/*DBに登録はされている*/
		$studentId = $registerResult[0]['STUDENT_ID'];
		$fullName = $registerResult[0]['FULL_NAME'];
		$regTime = $registerResult[0]['REGISTER_TIME'];
		$randomNo = $registerResult[0]['RANDOM_NO'];
		if(is_null($regTime)){
			/*まだ登録したことが無い*/
			$updateResult = $con->recordRegisterTime($randomNo, $accessTime);
			if($updateResult){
				/*正常*/
				$con -> insertMobileScreen($randomNo, $accessTime);
				$urlResult = $con -> getURL();
				if(count($urlResult) == 1){
					$unURL = $urlResult[0]['URL'];
					$url = $unURL."?r=".$randomNo;
					$processResult = array('PROCESS_RESULT' => 0,'ERROR' => NULL);
					$item = array('URL' => $url,'STUDENT_ID' => $studentId,'FULL_NAME' => $fullName);
					json(0,$processResult,0,$item);
				}else{
					/*URLがDBにない*/
					$error = array('STUDNET_ID' => NULL,'REGISTER_RESULT' => NULL,'REGISTER_UPDATE' => 0,'URL' => 1);
					$processResult = array('PROCESS_RESULT' => 1,'ERROR' => $error);
					json(0,$processResult,0,NULL);
				}
			}else{
				/*異常*/
				$error = array('STUDNET_ID' => NULL,'REGISTER_RESULT' => NULL,'REGISTER_UPDATE' => 1,'URL' => NULL);
				$processResult = array('PROCESS_RESULT' => 1,'ERROR' => $error);
				json(0,$processResult,-1,NULL);
			}
		}else{
			/*登録したことがある*/
			/*過去の結果を表示*/
			$urlResult = $con -> getURL();
			$unURL = $urlResult[0]['URL'];
			$url = $unURL."?r=".$randomNo;
			$processResult = array('PROCESS_RESULT' => 0,'ERROR' => NULL);
			$item = array('URL' => $url,'STUDENT_ID' => $studentId,'FULL_NAME' => $fullName);
			json(0,$processResult,1,$item);
		}
	}else{
		/*DBに登録がされていない*/
		/*学籍番号がDBに存在するかをチェックする*/
		$exitResult = $con -> checkExistenceStudentId($studentId);
		if(count($exitResult) == 1){
			/*DBに学籍番号は見つかった*/
			/*適当にDB上で乱数と紐付けさせる*/
			for($i = 0; $i < 20; ++$i){
				$num = mt_rand(0,9);
				$randomNo .= $num;
			}
			//$randomNo = $ransu;
			$studentId = $exitResult[0]['STUDENT_ID'];
			$fullName = $exitResult[0]['FULL_NAME'];
			$sql = "INSERT INTO `REGISTER_MST` (`RANDOM_NO`, `STUDENT_ID`, `REGISTER_TIME`) VALUES ('".$randomNo."', '".$studentId."', '".$accessTime."')";
			$con -> insertMobileScreen($randomNo, $accessTime);
			$insertResult = $con -> execute($sql);
			if($insertResult){
				/*正常登録*/
				$urlResult = $con -> getURL();
				if(count($urlResult) == 1){
					$processResult = array('PROCESS_RESULT' => 0,'ERROR' => NULL);
					$unURL = $urlResult[0]['URL'];
					$url = $unURL."?r=".$randomNo;
					$item = array('URL' => $url,'STUDENT_ID' => $studentId,'FULL_NAME' => $fullName);
					json(0,$processResult,0,$item);
				}else{
					$error = array('STUDNET_ID' => NULL,'REGISTER_RESULT' => NULL,'REGISTER_UPDATE' => 0,'URL' => 1);
					$processResult = array('PROCESS_RESULT' => 1,'ERROR' => $error);
					json(0,$processResult,-1,NULL);
				}
			}else{
				/*登録できなかった*/
				$error = array('STUDNET_ID' => 0,'REGISTER_RESULT' => 1,'REGISTER_UPDATE' => NULL,'URL' => NULL);
				$processResult = array('PROCESS_RESULT' => 1,'ERROR' => $error);
				json(0,$processResult,-1,NULL);
			}
		}else{
			/*DBにも学籍番号がみつからなった*/
			/*管理者に問い合わせてください．*/
			//$notId ='{"STUDNET_ID":"1"}';
			$error = array('STUDNET_ID' => 1,'REGISTER_RESULT' => NULL,'REGISTER_UPDATE' => NULL,'URL' => NULL);
			$processResult = array('PROCESS_RESULT' => 1,'ERROR' => $error);
			json(0,$processResult,-1,NULL);
		}
	}
}
/**JSONで結果を表示**/

function json($parameta,$processResul,$pastRegist,$item){
	header('Content-type: application/json');
	$user = array('PARAMETA' => $parameta, 'PROCESS_RESULT' => $processResul, 'PAST_REGIST' => $pastRegist,'ITEM' => $item);
	echo json_encode($user);
}
?>