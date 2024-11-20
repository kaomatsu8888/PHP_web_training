<?php
// セッションを開始
session_start();

// ログイン状態を確認し、ログインしていなければログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('Location: Views/login.php');
    exit;
}

// ログインしている場合、投稿一覧ページにリダイレクト
header('Location: Views/post_list.php');
exit;
