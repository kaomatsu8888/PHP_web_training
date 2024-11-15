<?php
/*
役割: 管理者用の投稿作成ページ
管理者が投稿を作成するためのフォームを表示し、フォームが送信された場合はデータベースに挿入します。

*/
session_start();
require 'db_connect.php';

// 管理者がログインしていない場合は、adminのログインページにリダイレクト
if (!isset($_SESSION['admin_id'])) { // 管理者がログインしていない場合
    header("Location: admin_login.php"); 
    exit(); // リダイレクトした場合は、以降の処理を行わない
}

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // フォームが送信された場合
    $title = trim($_POST['title']); // タイトルを取得
    $content = trim($_POST['content']); // 内容を取得
    $name = $_SESSION['admin_name']; // 管理者の名前を使用
    
    # タイトルまたは内容が空の場合はエラーメッセージを表示
    if (empty($title) || empty($content)) {
        $error_message = "すべてのフィールドを入力してください。";
    } else {
        // 投稿をデータベースに挿入
        // SQLの内容はpostsテーブルにname, title, content, created_atを挿入する。prepareはSQLを実行するためのメソッド
        $stmt = $conn->prepare("INSERT INTO posts (name, title, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $title, $content); // bind_paramはSQLに変数をバインドするメソッド

        if ($stmt->execute()) {// executeメソッドでSQLを実行
            header("Location: admin_posts.php"); // 投稿成功後に投稿管理ページにリダイレクト
            exit();
        } else {
            $error_message = "投稿に失敗しました: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿作成</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>投稿作成</h1>
    <a href="admin_posts.php">投稿管理に戻る</a>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="admin_create_post.php" method="post"><?php // フォームの送信先はこのページ自身 ?>
        <label>タイトル: <input type="text" name="title" required></label><br>
        <label>内容: <textarea name="content" rows="5" required></textarea></label><br><?php // テキストエリアの内容を入力するフォーム ?>
        <button type="submit">投稿</button>
    </form>
</body>
</html>
