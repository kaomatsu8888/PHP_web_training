<?php
/*
役割: システムの共通設定（データベース情報や定数など）を管理します。

主な処理:
データベースサーバー名、ユーザー名、パスワード、データベース名を定義。
1ページあたりの投稿数などのシステム共通の設定を定数として定義します。
*/

// データベース設定
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'test');
define('POSTS_PER_PAGE', 5); // 1ページあたりの投稿数
?>
