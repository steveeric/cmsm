<?php
include_once('../base/db/db_access.php');
/*ポストで渡されたIDを受け取る*/
$studentId = $_POST['id'];
$byear = $_POST['y'];
$bmonth = $_POST['m'];
$bday = $_POST['d'];
//$studentId = $_GET['id'];

$sql = "SELECT STUDENT_ID,FULL_NAME FROM `STUDENT_MST` 
		WHERE `STUDENT_ID` LIKE '".$studentId."' 
				AND `BIRTH_YEAR` LIKE '".$byear."' 
				AND `BIRTH_MONTH` LIKE '".$bmonth."' 
				AND `BIRTH_DAY` LIKE '".$bday."' ";
$result = $con -> query($sql);

if(count($result) == 1){
	$st = array('STUDENT_ID' => $result[0]['STUDENT_ID'], 'FULL_NAME' => $result[0]['FULL_NAME']);
	json(0,$st);
}else{
	json(1,NULL);
}
/**JSONで結果を表示**/
function json($parameta,$st){
	header('Content-type: application/json');
	$user = array('REGISTER' => $parameta, 'STUDENT' => $st);
	echo json_encode($user);
}
?>
