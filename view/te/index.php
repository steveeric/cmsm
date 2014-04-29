<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Teacher Controller</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--
<script type="text/javascript" data-main="../tool/js/myJS.js" src="../tool/js/require-2.1.11.js"></script>-->
<link rel="stylesheet"
	href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script
	src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
	<script
	src="../../tool/js/libs/jCanvas.js"></script>
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
	href="../../tool/css/teacher/tab.css">

<script src="../../module/script/teacher/jquery.tab.js"></script>

</head>
<body>
	<ul id="tab">
		<li class="selected"><a href="#tab1">出席状態</a></li>
		<li><a href="#tab2">出席者</a></li>
		<li><a href="#tab3">座席表</a></li>
		<li><a href="#tab4">席替え</a></li>
	</ul>

	<div id="detail">
		<div id="tab1" class="tabbox">
			<div data-role="content" style="text-align: center">
				<p>出席調査</p>
				<p class="attendCount"></p>
				<p class="callstarttime">出席調査開始時間 : 開始されていません.</p>
				<p class="callendtime">出席調査終了時間 : 開始されていません.</p>
				<a href="" id="callStartBtn" data-role="button" data-inline="true"
					data-theme="b"> 出席調査開始 </a> <a href="" id="callEndBtn"
					data-role="button" data-inline="true" data-theme="b"> 出席調査終了 </a>
			</div>
		</div>
		<div id="tab2" class="tabbox">
			<p>出席者一覧</p>
			<p class="tab2TodayAttendeeCount"></p>
			<label for="search-basic">学生検索:</label>
			<input type="email" name="search" id="search-basic" value="" placeholder="学籍番号を入力してください." />
			<!-- #tab2 -->
			<div class="tableview-wrapper">
				<h2 id="toc-a" class="dummy-header"></h2>
				<div class="tableview" id="tableviewid"></div>
			</div>
		</div>
		<div id="tab3" class="tabbox">
		<div class="seatView" id="seatViewid"></div>
				<!-- #tab3 -->
			</div>
			<div id="tab4" class="tabbox">
				<p>席替え</p>
				<!-- #tab4 -->
				<a href="" id="changeSeatBnt" data-role="button" data-inline="true"
					data-theme="b"> 席替え開始 </a>
			</div>
			<div id="tab5" class="tabbox">
				<p>タブ5のコンテンツ。</p>
				<!-- #tab5 -->
			</div>
			<!-- #detail -->
		</div>

</body>
</html>
