<?php
/*
役割: 投稿管理画面を表示します。

主な処理:
管理者がログインしているかどうかをチェックし、ログインしていない場合はログインページにリダイレクトします。
postsテーブルから投稿を取得し、表示します。
*/
session_start();
require 'db_connect.php';

// 管理者がログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// 投稿を取得する処理
// SQLの内容はpostsテーブルからid, name, title, content, created_atを取得し、created_atの降順で並び替える
$sql = "SELECT id, name, title, content, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿管理</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>投稿管理</h1>
    <a href="admin_dashboard.php">ダッシュボードに戻る</a>
    <div class="posts">
    <a href="admin_create_post.php">新規投稿作成</a>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <h3><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <span>投稿者: <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></span><br>
                    <span>投稿日時: <?php echo $row['created_at']; ?></span><br>
                    <a href="edit_post.php?id=<?php echo $row['id']; ?>">編集</a> |
                    <a href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>投稿がありません。</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
