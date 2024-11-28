<?php
/*
役割: データベースへの接続を行い、共通の接続設定を管理します。

主な処理:
config.phpからデータベース設定を読み込み、mysqliオブジェクトを使ってデータベース接続を確立。
UTF-8エンコーディングを設定して、文字化けが発生しないように
*/
require_once 'config.php';//config.phpファイルを読み込む

// データベース接続
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE); //mysqliオブジェクトを使ってデータベース接続を確立

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 文字コードをUTF-8に設定（データベース用）
$conn->set_charset("utf8mb4");
?>
