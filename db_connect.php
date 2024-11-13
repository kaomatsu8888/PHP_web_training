<?php
require_once 'config.php';

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 文字コードをUTF-8に設定（データベース用）
$conn->set_charset("utf8mb4");
?>
