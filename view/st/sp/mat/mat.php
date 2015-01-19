<!DOCTYPE HTML>
	<html>
		<head>
			<meta charset="utf-8">
				<title>Matrix</title>
				<meta name="viewport" content="width=device-width, initial-scale=1">
					<link rel="stylesheet"
						href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />
					<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
					<script
						src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>
					<link rel="stylesheet" type="text/css" href="matrix.css" />
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
		echo screenInputValue($student,$matrix);
	}

	/**
   	* createdate : 2015年1月15日
   	* screenInputValueメソッド
   	* 入力画面
   	* @parm inputValueClass 入力内容に関する情報クラス
   	**/
	function screenInputValue($student,$matrix){

		$MAX_CHKBORD_ROW = $matrix -> matrixBordRowCount;
		$MAX_CHKBORD_COLUMN = $matrix -> matrixBordColumnCount;

		$mustInputRow = $matrix -> matrixRowNumber;
		$mustInputColumn = $matrix -> matrixColumnNumber;
		//入力箇所
		$matrixInputLocate = $mustInputRow."行 - ".$mustInputColumn."列";

		//チェッカーボード作成
		$matrixBordStr = "";
		$matrixBordStr = '<CENTER> <table border="1">';
		for($a = 0; $a < $MAX_CHKBORD_ROW; $a++){
	    	$matrixBordStr = $matrixBordStr.'<tr>';
	    	for($i = 0; $i< $MAX_CHKBORD_COLUMN; $i++){
	    		$color = "";
	    		$r = $a + 1;
	    		$c = $i + 1;
	    		if($r == $mustInputRow && $c == $mustInputColumn){
	    			$color = "#ff0000";
	    		}else{
	    			$color = "#000000";
	    		}
	       		$matrixBordStr = $matrixBordStr.'<td bgcolor="'.$color.'" width="50" height="50"></td>';
	    	}
	    	$matrixBordStr = $matrixBordStr.'</tr>';
		}
		$matrixBordStr = $matrixBordStr.'</table> </CENTER>';
		//
		$assistMessage = "半角数字を入力して下さい.";
		//残り入力回数.
		$rest = $matrix -> inputMaxCount - $student -> getAttendee() -> getRequestcountJudgementMatrix();
		$restMessage = "残り".$rest."回";

		//隠し要素(javascriptで使用するために)
		$hidden = "";
		//出席ID
		$attId = $student -> getAttendee() -> getAttendeeId();
		$hidden = $hidden.'
		<input type="hidden" id="ATT" value="'.$attId.'">';
		//入力しなければならいな桁数
		$digit = $matrix -> matrixDigitCount;
		$hidden = $hidden.'
		<input type="hidden" id="DIG" value="'.$digit.'">';
		//マトリックスログID
		$matrixLogId = $student -> getAttendee() -> getLastMatrixLogId();
		$hidden = $hidden.'
		<input type="hidden" id="MAT" value="'.$matrixLogId.'">';

		return '					<script type="text/javascript" src="matrix.js"></script>

				<div data-role="page" id="SCREEN_CLOSED" data-theme="b">
				'.hedder().'
				<div data-role="content" style="text-align: center">
					<p>'.$matrixInputLocate.'<p>
					'.$matrixBordStr.'
					<p>'.$assistMessage.'<p>
					<font class="text_judge_enter_chkbord_baule" color="#ff0000" vale="">'.$restMessage.'</font>
					<input type="tel" maxlength="'.$matrix -> matrixDigitCount.'" id="FORM_ENTER_MATRIX_VALUE" value="" />
					<input type="button" id="BTN-SEND-MATRIX-VALUE" data-inline="true" value=" 　　　OK　　　"></input>
				</div>
			</div>'.$hidden;
	}

	/**
   	* createdate : 2015年1月15日
   	* screenNamePlateメソッド
   	* 名札表示画面
   	* @parm namePlateClass 名札クラス
   	**/
	function screenNamePlate($screenState){

		$student = $screenState -> getStudent();
		$studentId = $student -> getStudentId();
		$fullName = $student -> getFullName();

		$att = $student -> getAttendee();
		//正解フラグ 1:正常 2:オーバ
		$inputMatrixStatus = $att -> getResultInputMatrix();
		$imageSrc = '';
		if($inputMatrixStatus == $screenState -> OVER_INPUT_LIMET){
			//入力制限をオーバーした.
			//不正出席とみなします.
			$imageSrc = '<img alt="illegal_attendance" src="../../../../tool/image/matrix/illegal_allegations.png">';
		}
			return '<div data-role="page" id="SCREEN_CLOSED" data-theme="b">
				'.hedder().'
				<div data-role="content" style="text-align: center">
					<p class="str" >'.$studentId.'</p>
					<p class="str" >'.$fullName.'</p>
					'.$imageSrc.'
				</div>
			</div>';
	}
	/**
   	* createdate : 2015年1月15日
   	* screenCanNotJoinメソッド
   	* 参加できない画面
   	* マトリックスモード開始までに出席が取れていない学生は参加できない画面.
   	**/
	function screenCanNotJoin(){
		return '<div data-role="page" id="SCREEN_CLOSED" data-theme="b">
				'.hedder().'
				<div data-role="content" style="text-align: center">
					<p class="str" >出席が確認できなかったので参加できません.</p>
				</div>
			</div>';
	}

	/**
   	* createdate : 2015年1月15日
   	* screenClosedメソッド
   	* 終了していた際に表示する画面
   	**/
	function screenClosed(){
		return '<div data-role="page" id="SCREEN_CLOSED" data-theme="b">
				'.hedder().'
				<div data-role="content" style="text-align: center">
					<p class="str" >入力を締め切りました</p>
				</div>
			</div>';
	}

	/**
   		* createdate : 2015年1月15日
   		* screenErrorメソッド
   		* エラーが発生した際に表示する画面.
   		**/
	function screenError(){
		return '<div data-role="page" id="SCREEN_ERROR" data-theme="b">
		'.hedder().'
		<div data-role="content" style="text-align: center">
			<p>エラーが発生しました.</p>
		</div>
	</div>';
	}

	/*ヘッダー*/
	function hedder(){
		return '<div data-role="header">
			<h1>マトリックス</h1>
		</div>';
	}

?>

</body>
ロード中...
<!-- [http://coolbodymaker.com/coolbodymaker_m/:title] -->
</html>