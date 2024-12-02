<?php
/*
役割: 投稿の削除処理を行います。

主な処理:
1. 管理者がログインしていない場合は、ログインページにリダイレクト
2. 投稿IDを取得
3. 投稿を削除
4. 削除成功後に投稿管理ページにリダイレクト

*/
session_start();
require 'db_connect.php';

// 管理者がログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$post_id = $_GET['id'];

// 投稿を削除する処理
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);

// 削除の確認を行う
if ($stmt->execute()) {
    header("Location: admin_posts.php"); // 削除成功後に投稿管理ページにリダイレクト
    exit();
} else {
    echo "削除に失敗しました: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
