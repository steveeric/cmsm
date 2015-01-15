<?php
echo <<<EOT
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta name='viewport'
	content='width=200px, initial-scale=1, maximum-scale=2'>
		
<title>登録用画面</title>
</head>
<body style="width: 200px;">
	<div>学籍番号と生年月日を半角英数字で入力してください</div>
	
	<hr>
	<form action='gpc.php' method='POST'>
		<div>
			学籍番号:<input type='text' name='id' />
		</div>
		<div>入力例 : J07011</div>
		<hr>
		<div>
			生年月日:
		</div>
		<div>
			<input type='text' name='y' size='4' maxlength='4' />年　<input type='text' name='m' size='2' maxlength='2'/>月 <input type='text' name='d' size='2' maxlength='2'/>日
		</div>
		<div>入力例 : 1996年02月02日</div>
		<input type='hidden' name='p'  value='0'>
		<hr>
		<input style='padding: 15px 70px;' type='submit' value='OK' />
	</form>
</body>
</html>
EOT;

?>
