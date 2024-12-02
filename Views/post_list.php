<?php
/*役割: 投稿一覧ページを表示します。
主な処理:
ログイン状態の確認
現在のページ番号を取得（デフォルトは1ページ目）
投稿一覧と総ページ数を取得
*/
// セッションを開始
session_start();
require_once '../Controllers/PostController.php';


$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']); // メッセージを一度だけ表示するため削除

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 現在のページ番号を取得（デフォルトは1ページ目）削除したページの分はない
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 投稿一覧と総ページ数を取得
$posts = getAllPosts($page);
$total_pages = getTotalPages();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>◯ちゃんねるにようこそ</title>
    <link rel="stylesheet" href="../Assets/styles.css">
    <script>
        // メッセージを一定時間にフェードアウトするスクリプト
        document.addEventListener("DOMContentLoaded", function () {
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                // フェードアウト用のスタイル変更
                setTimeout(function () {
                    flashMessage.style.transition = 'opacity 0.5s ease';
                    flashMessage.style.opacity = 0;
                    setTimeout(function () {
                        flashMessage.style.display = 'none';
                    }, 500); // 完全に消えるまで待つ
                }, 3000); // 3秒後にフェードアウト開始
            }
        });
    </script>
    </script>
</head>

<body>
    <div class="container">
        <!-- ヘッダー -->
        <!-- メッセージ表示 -->
        <?php if ($flash_message): ?>
            <div class="flash-message">
                <?php echo htmlspecialchars($flash_message); ?>
            </div>
        <?php endif; ?>

        <!-- 投稿一覧の表示部分 -->
        <div class="post-list">
            <!-- 投稿一覧をここに表示 -->
        </div>

        <div class="header">
            <h1 class="title">◯ちゃんねるにようこそ</h1>
            <p class="welcome-message">ようこそ <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> さん</p>
            <div class="header-buttons">
                <a href="logout.php" class="button delete">ログアウト</a>
                <a href="new_post.php" class="button">新規投稿</a>
            </div>
        </div>

        <!-- 投稿一覧 -->
        <div class="post-list">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>日時</th>
                        <th>題名</th>
                        <th>res</th>
                        <th>名前</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td><?php echo $post['created_at']; ?></td>
                            <td><a href="post_detail.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></td>
                            <td><?php echo $post['res_count']; ?></td>
                            <td><?php echo htmlspecialchars($post['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- ページネーション -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</body>

</html>
<?php
print("セッション確認");
echo "<pre>";
print_r($_SESSION);

echo "</pre>";
?>
