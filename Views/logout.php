<?php
/*役割: ログアウト処理を行う*/

// セッションを開始
session_start();

// セッションを破棄
session_destroy();

// ログインページにリダイレクト
header('Location: login.php');
exit;
?>
