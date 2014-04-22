<?php

echo "haro-";
session_start();  
session_name(); 
print $sessionId = session_id();
var_dump($sessionId);
$_SESSION = array() ; //すべてのセッション変数を初期化
session_destroy() ; //セッションを破棄
?>
