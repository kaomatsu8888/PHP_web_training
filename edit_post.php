<?php
/*
役割: 投稿の削除処理を行います。

主な処理:
1. 管理者がログインしていない場合は、ログインページにリダイレクト
2. 投稿IDを取得
3. 投稿を削除
4. 削除成功後に投稿管理ページにリダイレクト
*/
session_start();
require 'db_connect.php';

// 管理者がログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$post_id = $_GET['id'];
$error_message = "";

// 投稿を取得する処理
$stmt = $conn->prepare("SELECT title, content FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $content);
$stmt->fetch();
$stmt->close();

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = trim($_POST['title']);
    $new_content = trim($_POST['content']);

    if (empty($new_title) || empty($new_content)) {
        $error_message = "すべてのフィールドを入力してください。";
    } else {
        // 投稿を更新する処理
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $new_title, $new_content, $post_id);

        if ($stmt->execute()) {
            header("Location: admin_posts.php"); // 更新成功後に投稿管理ページにリダイレクト
            exit();
        } else {
            $error_message = "更新に失敗しました: " . $conn->error;
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
    <title>投稿編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>投稿編集</h1>
    <a href="admin_posts.php">投稿管理に戻る</a>
    <?php if ($error_message): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post">
        <label>タイトル: <input type="text" name="title" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" required></label><br>
        <label>内容: <textarea name="content" rows="5" required><?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?></textarea></label><br>
        <button type="submit">更新</button>
    </form>
</body>
</html>
