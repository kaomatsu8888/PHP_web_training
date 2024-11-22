<?php
/*
役割: 管理者ダッシュボードページ
管理者がログインしている場合に表示されるページです。
*/
session_start();
require 'db_connect.php';

// 管理者がログインしていない場合は、ログインページにリダイレクト
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: admin_login.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理者ダッシュボード</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>管理者ダッシュボード</h1>
<p>ようこそ、<?php echo htmlspecialchars($_SESSION['admin_name'], ENT_QUOTES, 'UTF-8'); ?>さん</p><?php // 管理者名を表示。htmlspecialchars関数でエスケープ処理を行う。 h関数でもいいのか？?>
<a href="admin_posts.php">投稿管理</a> |
<a href="admin_users.php">ユーザー管理</a> |
<a href="admin_logout.php">ログアウト</a>

</body>
</html>
