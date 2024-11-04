<?php
// ユーザーが投稿フォームから送信したデータ（名前、題名、本文）を処理し、データベースに保存するファイル。
// フォーム送信後、掲示板のメインページ（index.php）にリダイレクト
// データベース接続
$conn = new mysqli('localhost', 'root', '', 'test'); //$connはデータベース接続のための変数
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 文字コードをUTF-8に設定
$conn->set_charset("utf8mb4");

// 入力データをそのまま保存
$name = $_POST['name'];
$title = $_POST['title'];
$content = nl2br($_POST['content']); // 改行を <br> に変換

// データベースに挿入
$stmt = $conn->prepare("INSERT INTO posts (name, title, content) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $title, $content);
$stmt->execute();
$stmt->close();
$conn->close();

// メインページにリダイレクト
header("Location: index.php");
exit();
