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

<script src="../../../module/script/teacher/contents.js"></script>

</head>
<body>
	<div data-role="page" id="first" data-theme="b">
		<div data-role="header">
			<h1>ロード中</h1>
		</div>
		<div data-role="content" style="text-align: center"></div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>
	<div data-role="page" id="ctr" data-theme="b">
		<div data-role="content" style="text-align: center">
			<?php include('../ctr/ctr_status.php');?>
		</div>
	</div>
	<div data-role="page" id="att" data-theme="b">
		<div data-role="content" style="text-align: center">
			<?php include('../att/att_list.php');?>
		</div>
	</div>
</body>
</html>
