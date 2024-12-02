<?php
/*役割: 投稿の編集ページです。
主な処理:
1. ログイン確認
2. 投稿IDの取得
3. 投稿データの取得
4. 編集権限の確認
*/



require_once '../Controllers/PostController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 投稿IDの取得
if (!isset($_GET['id'])) {
    echo "編集対象の投稿が指定されていません。";
    exit;
}

$post_id = (int)$_GET['id'];
$post = getPostById($post_id);

if (!$post) {
    echo "指定された投稿が見つかりません。";
    exit;
}

// 編集権限の確認
if (!isset($_SESSION['role'], $_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $post['user_id'])) {
    echo "編集権限がありません。";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>投稿編集</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>投稿編集</h1>
        <form method="post" action="../Controllers/PostController.php?action=update">
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
            <div class="form-group">
                <label for="title">タイトル:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">本文:</label>
                <textarea id="content" name="content" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>
            <button type="submit" class="button">更新</button>
        </form>
        <a href="post_detail.php?id=<?php echo $post['id']; ?>" class="button small">戻る</a>
    </div>
</body>
</html>
