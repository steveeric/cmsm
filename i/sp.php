<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>ISSUE URL</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--
<script type="text/javascript" data-main="../tool/js/myJS.js" src="../tool/js/require-2.1.11.js"></script>-->
<link rel="stylesheet"
	href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />

<!-- <script src="../tool/js/libs/jQuery/1.8.2/jquery-1.8.2.min.js"></script>  -->
<script src="http://code.jquery.com/jquery-1.8.2.min.js	"></script>
<script
	src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
<script>
	$(document).bind("mobileinit", function() {
		$.mobile.defaultTransition = "flip";
	});
</script>
<script type="text/javascript" charset="utf-8">
		$(document).bind("mobileinit", function(){
			$.mobile.page.prototype.options.addBackBtn = false;
			$.mobile.ajaxEnabled = false;
		});
</script>

<link rel="stylesheet" type="text/css" href="../tool/css/style.css">

<script src="../module/script/url_issue/s.js"></script>

</head>
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>URL発行</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>あなたは，これから様々な授業コンテンツを</p>
			<p>携帯画面を通して受けていくことになります．</p>
			<p>そのWEBサイトへの個別URLを発行します．</p>
			<p></p>
			<hr>
			<p>・学籍番号を入力して下さい．</p>
			<p>入力例 : J07011</p>
			<p>
				<input type="text" id="studentIdTx" 　maxlength="6"
					style="ime-mode: disabled">
			</p>
			<hr>
			<p>・生年月日を選択して下さい．</p>
			<div id="date" class="calender">
				<fieldset data-role="controlgroup" data-type="horizontal">
					<select name="data[year]" id="year">
						<option value="1980" selected="selected">1980年</option>
						<option value="1986">1986年</option>
						<option value="1987">1987年</option>
						<option value="1988">1988年</option>
						<option value="1989">1989年</option>
						<option value="1990">1990年</option>
						<option value="1991">1991年</option>
						<option value="1992">1992年</option>
						<option value="1993">1993年</option>
						<option value="1994">1994年</option>
						<option value="1995">1995年</option>
						<option value="1996">1996年</option>
					</select> <select name="data[month]" id="month">
						<option value="01" selected="selected">1月</option>
						<option value="02">2月</option>
						<option value="03">3月</option>
						<option value="04">4月</option>
						<option value="05">5月</option>
						<option value="06">6月</option>
						<option value="07">7月</option>
						<option value="08">8月</option>
						<option value="09">9月</option>
						<option value="10">10月</option>
						<option value="11">11月</option>
						<option value="12">12月</option>
					</select> <select name="data[day]" id="day">
						<option value="01" selected="selected">1日</option>
						<option value="02">2日</option>
						<option value="03">3日</option>
						<option value="04">4日</option>
						<option value="05">5日</option>
						<option value="06">6日</option>
						<option value="07">7日</option>
						<option value="08">8日</option>
						<option value="09">9日</option>
						<option value="10">10日</option>
						<option value="11">11日</option>
						<option value="12">12日</option>
						<option value="13">13日</option>
						<option value="14">14日</option>
						<option value="15">15日</option>
						<option value="16">16日</option>
						<option value="17">17日</option>
						<option value="18">18日</option>
						<option value="19">19日</option>
						<option value="20">20日</option>
						<option value="21">21日</option>
						<option value="22">22日</option>
						<option value="23">23日</option>
						<option value="24">24日</option>
						<option value="25">25日</option>
						<option value="26">26日</option>
						<option value="27">27日</option>
						<option value="28">28日</option>
						<option value="29">29日</option>
						<option value="30">30日</option>
						<option value="31">31日</option>
					</select>
				</fieldset>
			</div>
			<hr>
			<p class="judge" color="#ff0000"></p>
			<p>
				<a href="" id="okBtn" data-role="button" data-inline="true"
					data-theme="b"> OK </a>
			</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="confirm" data-theme="b">
		<div data-role="header">
			<h1>URL発行</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>表示されている内容は正しいですか．</p>
			<p></p>
			<p class="confirmStudentIdFiled"></p>
			<p class="confirmStudentNameFiled"></p>
			<p></p>
			<a href="" id="returnEnterBtn" data-role="button" data-inline="true">訂正</a>
			<a href="" id="completeBtn" data-role="button" data-inline="true"
				data-theme="b">はい</a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="complete" data-theme="b">
		<div data-role="header">
			<h1>URL発行</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>自分専用のURLです．</p>
			<p></p>
			<a href="" target="_blank" id = url><p class="completeURLFiled"></p></a>
			<p></p>
			<p>URLをタップしてください．</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>
</body>
読み込み中...
</html>
