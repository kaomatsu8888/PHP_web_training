<?php
session_start(); // セッションを開始
require 'db_connect.php';
include 'includes/header.php'; // ヘッダーを読み込む

// ログインしているかどうかを確認
$is_logged_in = isset($_SESSION['user_id']);
?>

<div class="header">
    <?php if ($is_logged_in): ?>
        <p>ようこそ、<?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?>さん</p>
        <a href="logout.php">ログアウト</a>
    <?php else: ?>
        <a href="login.php">ログイン</a>
    <?php endif; ?>
</div>

<div class="posts">
    <?php
    // 投稿を取得する処理
    $sql = "SELECT id, title, content, created_at FROM posts WHERE is_deleted = 0 ORDER BY created_at DESC LIMIT " . POSTS_PER_PAGE;
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <span>投稿日時: <?php echo $row['created_at']; ?></span>
            </div>
    <?php
        endwhile;
    else:
    ?>
        <p>まだ投稿がありません。</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; // フッターを読み込む ?>
