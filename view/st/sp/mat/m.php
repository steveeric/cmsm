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

<?php
	//マトリックスモジュール読み込み
	include_once(dirname(__FILE__).'../../../../../module/php/view/matrix/matrix.php');

	//マトリックスインスタンス
	$matrix = new Matrix();

	//
	$matrix -> setItem("0001020206KK020140002","");

	$screenState = $matrix -> getScreenState();

	$screenNumber = $screenState -> getScreenNumber();
	$screenContent = $screenState -> getScreenContent();

	if($screenNumber == $matrix -> MODE_CAN_NOT_JOIN){
		//参加できないモード
		echo screenCanNotJoin();
	}else if($screenNumber == $matrix -> MODE_NAME_PLATE){
		//名札モード
		echo screenNamePlate($screenContent);
	}else if($screenNumber == $matrix -> MODE_INPUT_VALUE){
		//入力画面モード

	}

	/**
   	* createdate : 2015年1月15日
   	* screenInputValueメソッド
   	* 入力画面
   	* @parm inputValueClass 入力内容に関する情報クラス
   	**/
	function screenInputValue($inputValueClass){
		return '<div data-role="page" id="SCREEN_CLOSED" data-theme="b">
				'.hedder().'
				<div data-role="content" style="text-align: center">
					<p> 入力画面が入ります. </p>
				</div>
			</div>';
	}

	/**
   	* createdate : 2015年1月15日
   	* screenNamePlateメソッド
   	* 名札表示画面
   	* @parm namePlateClass 名札クラス
   	**/
	function screenNamePlate($namePlateClass){
		$studentId = $namePlateClass -> getStudentId();
		$fullName = $namePlateClass -> getFullName();
		$inputMatrixStatus = $namePlateClass -> getInputMatrixStatus();
		$imageSrc = '';
		if($inputMatrixStatus == $namePlateClass -> OVER_INPUT_LIMET){
			//入力制限をオーバーした.
			//不正出席とみなします.
			$imageSrc = '<img alt="illegal_attendance" src="../../../../tool/image/matrix/illegal_attendee.png">';
		}
			return '<div data-role="page" id="SCREEN_CLOSED" data-theme="b">
				'.hedder().'
				<div data-role="content" style="text-align: center">
					<p>'.$studentId.'</p>
					<p>'.$fullName.'</p>
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
					<p>出席が確認できなかったので参加できません.</p>
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
					<p>入力を締め切りました</p>
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