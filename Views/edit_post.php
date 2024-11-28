<?php
/*役割: 投稿の編集画面を表示します。
主な処理:
1. ログイン確認
2. 投稿IDの取得
3. 投稿を取得
4. 編集フォームの表示
5. フォームが送信された場合の処理
*/

require_once '../Controllers/PostController.php';
session_start();

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 投稿IDの取得
if (!isset($_GET['id'])) {
    echo "投稿IDが指定されていません。";
    exit;
}

$post_id = (int)$_GET['id'];
$post = getPostById($post_id);

// 編集フォームの表示
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    updatePost($post_id, $_POST['title'], $_POST['content']);
    header('Location: post_detail.php?id=' . $post_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿編集</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>投稿編集</h1>
        <form method="post">
            <label for="title">題名:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>
            <label for="content">内容:</label>
            <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea><br>
            <button type="submit" class="button">更新</button>
        </form>
        <br>
        <!-- 戻るボタン -->
        <a href="post_detail.php?id=<?php echo $post_id; ?>" class="button small">レス一覧に戻る</a>
    </div>
</body>
</html>
