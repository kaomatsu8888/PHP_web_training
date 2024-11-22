<?php
/*役割: データベースへの接続を行い、共通の接続設定を管理します。*/

$host = 'localhost';
$db = 'test'; // データベース名を指定
$user = 'root'; // ユーザー名を指定
$pass = ''; // パスワードを指定
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [ // オプションを指定
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // エラーモードを指定。
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // デフォルトのフェッチモードを指定。カラム名をキーとする連想配列で取得
    PDO::ATTR_EMULATE_PREPARES   => false, // プリペアドステートメントを有効。SQLインジェクション対策
];

try { // 例外処理
    $pdo = new PDO($dsn, $user, $pass, $options); // PDOオブジェクトを生成
} catch (\PDOException $e) { // 例外が発生した場合の処理
    throw new \PDOException($e->getMessage(), (int)$e->getCode()); // 例外をスロー
}
