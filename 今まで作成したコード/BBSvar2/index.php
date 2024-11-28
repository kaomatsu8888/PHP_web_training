<?php
/*
役割: メインの掲示板ページ。投稿の一覧を表示し、ログイン状態によって異なる情報を表示します
（ログイン済みのユーザーには「ログアウト」、ログインしていないユーザーには「ログイン」のリンクを表示）。

主な処理:
セッションを開始して、ユーザーがログインしているかどうかを判定。
データベースから投稿を取得して、最新の投稿を順番に表示。
ヘッダーとフッターの共通部分をインクルード（header.phpとfooter.php）。
*/
session_start();
require 'db_connect.php';
include 'includes/header.php';

// ユーザーがログインしているかを確認
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : null;
$is_admin = isset($_SESSION['admin_id']);

// 投稿の取得処理
$sql = "SELECT id, name, title, content, created_at FROM posts WHERE parent_id IS NULL ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="header">
    <?php if ($is_logged_in): ?>
        <p>ようこそ、<?php echo $user_name; ?>さん</p>
        <a href="logout.php">ログアウト</a>
        <a href="create_post.php">新規投稿</a>
    <?php else: ?>
        <a href="login.php">ログイン</a>
    <?php endif; ?>
</div>

<div class="posts">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <span>投稿者: <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></span><br>
                <span>投稿日時: <?php echo $row['created_at']; ?></span>
                <?php if ($is_logged_in && ($is_admin || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']))): ?>
                    <a href="edit_post.php?id=<?php echo $row['id']; ?>">編集</a> |
                    <a href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                <?php endif; ?>
                <a href="reply_post.php?parent_id=<?php echo $row['id']; ?>">返信</a>

                <!-- レスポンス表示 -->
                <?php
                $reply_sql = "SELECT id, name, content, created_at FROM posts WHERE parent_id = ? ORDER BY created_at ASC";
                $stmt = $conn->prepare($reply_sql);
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($reply_id, $reply_name, $reply_content, $reply_created_at);

                if ($stmt->num_rows > 0):
                    echo "<div class='replies'>";
                    while ($stmt->fetch()):
                        ?>
                        <div class="reply" style="margin-left: 20px;">
                            <p><?php echo htmlspecialchars($reply_content, ENT_QUOTES, 'UTF-8'); ?></p>
                            <span>投稿者: <?php echo htmlspecialchars($reply_name, ENT_QUOTES, 'UTF-8'); ?></span><br>
                            <span>投稿日時: <?php echo $reply_created_at; ?></span>
                        </div>
                        <?php
                    endwhile;
                    echo "</div>";
                endif;
                $stmt->close();
                ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>まだ投稿がありません。</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
