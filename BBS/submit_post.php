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
$name = $_POST['name']; // 改行を変換せずにそのまま保存
$title = $_POST['title'];
$content = $_POST['content']; 

// データベースに挿入
// SQLの内容は「posts」テーブルに「name」「title」「content」のカラムにそれぞれの値を挿入する
// prepareメソッドの引数にはSQL文を指定し、bind_paramメソッドの引数には「?」に対応する変数の型と値を指定
// データ例：INSERT INTO posts (name, title, content) VALUES ('名前', '題名', '本文')
$stmt = $conn->prepare("INSERT INTO posts (name, title, content) VALUES (?, ?, ?)"); 
// 3つの変数を文字列としてバインド。バインドとは、変数をSQL文に埋め込むこと
$stmt->bind_param("sss", $name, $title, $content); // この文は、3つの変数を文字列としてバインドしている
$stmt->execute(); // SQL文を実行
$stmt->close(); // ステートメントを閉じる.同じ接続で複数のステートメントを実行する場合、ステートメントを閉じないとエラーが発生する可能性があるため
$conn->close(); // 接続を閉じる。データベース接続を閉じることで、データベースへの接続を解除し、リソースを解放する

// メインページにリダイレクト
header("Location: index.php"); // リダイレクトを行うためのヘッダー情報を送信
exit(); // スクリプトの実行を終了
