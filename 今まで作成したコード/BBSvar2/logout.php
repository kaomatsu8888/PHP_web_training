<?php
session_start();
// session_unset(); // すべてのセッション変数を削除
session_destroy(); // セッションを破壊

// ログアウト後にメインページにリダイレクト
header("Location: index.php");
exit();
?>
