<?php
/*
役割: 新規投稿ページ
主な処理:
ログインしているユーザーのみがアクセスできるように、ログインチェックを行います。
新規投稿フォームから送信されたデータを受け取り、データベースに挿入します。
新規投稿が成功した場合は、メインページにリダイレクトします。
新規投稿が失敗した場合は、エラーメッセージを表示します。
*/
session_start();
require 'db_connect.php';

// ユーザーがログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// セッションからユーザーIDを取得
$user_id = $_SESSION['user_id'];

// 投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_name = $_SESSION['user_name'];

    if (empty($title) || empty($content)) {
        $error_message = "すべてのフィールドを入力してください。";
    } else {
        // 投稿をデータベースに挿入
        $stmt = $conn->prepare("INSERT INTO posts (user_id, name, title, content, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $user_id, $user_name, $title, $content);

        if ($stmt->execute()) {
            header("Location: index.php"); // 投稿成功後にメインページにリダイレクト
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
    <title>新規投稿</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>新規投稿</h1>
    <a href="index.php">メインページに戻る</a>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="create_post.php" method="post">
        <label>タイトル: <input type="text" name="title" required></label><br>
        <label>内容: <textarea name="content" rows="5" required></textarea></label><br>
        <button type="submit">投稿</button>
    </form>
</body>
</html>
