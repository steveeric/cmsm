<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Page Title</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet"
	href="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.css" />
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script
	src="http://code.jquery.com/mobile/1.3.0/jquery.mobile-1.3.0.min.js"></script>

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

<link rel="stylesheet" type="text/css"
	href="../../../../tool/css/style.css">

<script src="../../../../module/script/selectseat/sels.js"></script>

</head>
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>ロード</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>読み込み中です．．．</p>
			<p>しばらくお待ちください．</p>
			
			<p>画面が切り替わらない場合は，</p>
			<p>再読み込みをタップしてください.</p>
			<a href="" id="reloadBtn" data-role="button" data-inline="true"
                                data-theme="b">再読み込み</a>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	<div data-role="page" id="notSeat" data-theme="b">
		<div data-role="header">
			<h1>座席不足</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>着席できる座席を割り当てることができません.</p>
			<p>教員に申し出てください.</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>
	
	<div data-role="page" id="todaySitPostion" data-theme="b">
		<div data-role="header">
			<h1>本日の着席位置</h1>
		</div>
		<div data-role="content" style="text-align: center">
		<p class="idFieald"></p>
		<p class="positionFieald"></p>
		<p>に着席してください.</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>
	<div data-role="page" id="sitPostion" data-theme="b">
		<div data-role="header">
			<h1>着席位置</h1>
		</div>
		<div data-role="content" style="text-align: center">
		<p class="positionPastFieald"></p>
		<p>に着席してください.</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>
	
	<div data-role="page" id="prametaError" data-theme="b">
		<div data-role="header">
			<h1>エラー</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>URLに誤りがあります．</p>
			<p>もう一度初めからやりなして下さい．</p>
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

	
	</div>
</body>
ロード中...
<!-- [http://coolbodymaker.com/coolbodymaker_m/:title] -->
</html>
