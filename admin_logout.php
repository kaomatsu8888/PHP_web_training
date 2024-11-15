<?php
/*
役割: 管理者ログアウト処理を行います。

主な処理:
セッションを破棄して、管理者ログアウトを実行します。
*/
session_start();
session_unset(); // すべてのセッション変数を削除
session_destroy(); // セッションを破壊

// ログアウト後に管理者ログインページにリダイレクト
header("Location: admin_login.php");
exit();
?>
