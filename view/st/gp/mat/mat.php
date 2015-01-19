<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
		<meta name='viewport'
			content='width=200px, initial-scale=1, maximum-scale=2'>
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
			<title>マトリックス</title>
	</head>

	<body style="width: 200px;">
		<div>マトリックス</div>

<?php

	$randomNo = htmlspecialchars($_GET['r'], ENT_QUOTES, "utf-8");
	$scheduleId = htmlspecialchars($_GET['s'], ENT_QUOTES, "utf-8");

	if(is_null($randomNo) || is_null($scheduleId)){
		exit ("エラーが発生しました.");
	}

	//マトリックスモジュール読み込み
	include_once(dirname(__FILE__).'../../../../../module/php/view/matrix/matrix.php');

	//マトリックスインスタンス
	$matrixModule = new MatrixModule();

	//X12009
	$matrixModule -> setItem($scheduleId,$randomNo);

	$screenState = $matrixModule -> getScreenState();
	$screenNumber = $screenState -> getScreenNumber();

	//学生情報クラス
	$student = $screenState -> getStudent();

	if($screenNumber == $matrixModule -> MODE_CAN_NOT_JOIN){
		//参加できないモード
		echo screenCanNotJoin();
	}else if($screenNumber == $matrixModule -> MODE_CLOSED){
		//終了しましたモード
		echo screenClosed();
	}else if($screenNumber == $matrixModule -> MODE_NAME_PLATE){
		//名札モード
		echo screenNamePlate($screenState);
	}else if($screenNumber == $matrixModule -> MODE_INPUT_VALUE){
		//入力画面モード
		//マトリックスクラス
		$matrix = $screenState -> getMatrix();
		echo screenInputValue($randomNo,$scheduleId,$student,$matrix);
	}

	/*
	//名札
	echo screenNamePlate();

	//出席を確認できなかったので参加できません.
	echo screenCanNotJoin();

	//入力を締め切りました.
	echo screenClosed();
	*/

?>



<?php


	/**
   	* createdate : 2015年1月17日
   	* screenInputValueメソッド
   	* 入力画面
   	* @parm inputValueClass 入力内容に関する情報クラス
   	**/
	function screenInputValue($randomNo,$scheduleId,$student,$matrix){

		$mustInputRow = $matrix -> matrixRowNumber;
		$mustInputColumn = $matrix -> matrixColumnNumber;
		//入力箇所
		$matrixInputLocate = $mustInputRow."行 - ".$mustInputColumn."列";

		//
		$assistMessage = "を半角数字で入力して下さい.";
		//残り入力回数.
		$rest = $matrix -> inputMaxCount - $student -> getAttendee() -> getRequestcountJudgementMatrix();
		$restMessage = "残り".$rest."回";

		//隠し要素(javascriptで使用するために)
		$hidden = "";
		//出席ID
		$attId = $student -> getAttendee() -> getAttendeeId();
		$hidden = $hidden.'
		<input type="hidden" name="att" value="'.$attId.'">';
		//入力しなければならいな桁数
		$digit = $matrix -> matrixDigitCount;
		$hidden = $hidden.'
		<input type="hidden" name="dig" value="'.$digit.'">';
		//マトリックスログID
		$matrixLogId = $student -> getAttendee() -> getLastMatrixLogId();
		$hidden = $hidden.'
		<input type="hidden" name="mat" value="'.$matrixLogId.'">';
		$hidden = $hidden.'
		<input type="hidden" name="s" value="'.$scheduleId.'">';
		$hidden = $hidden.'
		<input type="hidden" name="r" value="'.$randomNo.'">';



		//$argument = "?r=".$randomNo."&s=".$scheduleId."&att=".$attId."&mat=".$matrixLogId;//.'&inp='.$inputValue;
		//$argument = "";
		return '<hr>
				<div>'.$matrixInputLocate.'<div>
					<div>'.$assistMessage.'<div>
					<font class="text_judge_enter_chkbord_baule" color="#ff0000" vale="">'.$restMessage.'</font>
					<form action="recives_input_value.php" method="GET">
					'.$hidden.'
					<input type="text" name="inp" maxlength="'.$matrix -> matrixDigitCount.'"  value="" />
					<hr>
					<input type="submit" value=" 　　　OK　　　"></input>
					</form>
				';
	}


	/**
   	* createdate : 2015年1月17日
   	* screenNamePlateメソッド
   	* 名札表示画面
   	* @parm namePlateClass 名札クラス
   	**/
	function screenNamePlate($screenState){
$student = $screenState -> getStudent();
		$studentId = $student -> getStudentId();
		$fullName = $student -> getFullName();

		$studentId = "J07011";
		$fullName = "伊藤翔太";

		$att = $student -> getAttendee();
		//正解フラグ 1:正常 2:オーバ
		$inputMatrixStatus = $att -> getResultInputMatrix();
		$imageSrc = '';
		if($inputMatrixStatus == $screenState -> OVER_INPUT_LIMET){
			//入力制限をオーバーした.
			//不正出席とみなします.
			//$imageSrc = '<img alt="illegal_attendance" src="../../../../tool/image/matrix/illegal_attendee.png">';
			$imageSrc = '<div><SPAN style="color:red;">不正疑惑</SPAN></div>';
		}


		return '
				<hr>
					<div>'.$studentId.'</div>
					<div>'.$fullName.'</div>
					'.$imageSrc.'
				<hr>
			';
	}


	/**
   	* createdate : 2015年1月17日
   	* screenCanNotJoinメソッド
   	* 参加できない画面
   	* マトリックスモード開始までに出席が取れていない学生は参加できない画面.
   	**/
	function screenCanNotJoin(){
		return "
			<hr>
				<div>出席が確認できなかったので参加できません</div>
			<hr>"
		;
	}


	/**
   	* createdate : 2015年1月17日
   	* screenClosedメソッド
   	* 終了していた際に表示する画面
   	**/
	function screenClosed(){
		return "
			<hr>
				<div>入力を締め切りました.</div>
			<hr>"
		;
	}
	/**
   		* createdate : 2015年1月17日
   		* screenErrorメソッド
   		* エラーが発生した際に表示する画面.
   		**/
	function screenError(){
		return "
			<hr>
				<div>エラーが発生しました.</div>
			<hr>"
		;
	}
?>

	</body>
</html>