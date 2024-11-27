<?php
/*役割: 投稿一覧ページを表示します。
主な処理:
ログイン状態の確認
現在のページ番号を取得（デフォルトは1ページ目）
投稿一覧と総ページ数を取得
*/
require_once '../Controllers/PostController.php';

// ログイン状態の確認
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
echo "<pre>";
print_r($_SESSION);

echo "</pre>";

// 現在のページ番号を取得（デフォルトは1ページ目）
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
    <title>投稿一覧</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <div class="header">
            <h1>投稿一覧</h1>
            <p>ようこそ <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> さん</p>
            <a href="logout.php" class="button">ログアウト</a>
        </div>

        <!-- 新規投稿ボタン -->
        <div class="new-post">
            <a href="new_post.php" class="button">新規投稿</a>
        </div>

        <!-- 投稿一覧 -->
        <div class="left-panel">
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
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</body>
</html>
