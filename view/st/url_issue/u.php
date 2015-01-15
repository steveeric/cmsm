<?php

echo "URL発行するぞ！";
echo <<<EOT
<script	src="../module/script/url_issue/s.js"></script>

		<div data-role="page" id="first" data-theme="b">

		<div data-role="header">
			<h1>URL発行</h1>
		</div>
		<div data-role="content" style="text-align: center">
			<p>学籍番号を入力して下さい．</p>
			<p>
				<input type="text" id="studentIdTx" 　maxlength="6"
					style="ime-mode: disabled">
			</p>
			<p class="judge"></p>
			<p>

				<a href="" id="reloadBtn" data-role="button" data-inline="true"
					data-theme="b">OK</a>
		
		</div>
		<div data-role="footer" data-position="fixed">
			<h4>&copy; 2014 Primary Meta Works</h4>
		</div>
	</div>

EOT;

?>