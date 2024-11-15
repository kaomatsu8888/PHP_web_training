<?php
/* 役割: 返信処理を行います。
 * 
 * 主な処理:
 * ユーザーがログインしていない場合は、ログインページにリダイレクト
 * 返信内容を入力し、送信ボタンを押すと、データベースに返信内容を保存
 * 返信成功後にメインページにリダイレクト
 */
 
session_start();
require 'db_connect.php';

// ユーザーがログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$parent_id = $_GET['parent_id'];

// 返信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    $user_name = $_SESSION['user_name'];

    if (empty($content)) {
        $error_message = "内容を入力してください。";
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (name, content, parent_id, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ssi", $user_name, $content, $parent_id);

        if ($stmt->execute()) {
            header("Location: index.php"); // 返信成功後にメインページにリダイレクト
            exit();
        } else {
            $error_message = "返信に失敗しました: " . $conn->error;
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
    <title>返信</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>返信</h1>
    <a href="index.php">メインページに戻る</a>
    <?php if (isset($error_message)): ?><?php // エラーメッセージがある場合 ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="reply_post.php?parent_id=<?php echo $parent_id; ?>" method="post">
        <label>内容: <textarea name="content" rows="5" required></textarea></label><br>
        <button type="submit">返信</button>
    </form>
</body>
</html>
