<?php
/*役割: 投稿の詳細を表示するページです。
主な処理:
1. ログイン確認
2. 投稿IDを取得
3. 投稿データを取得
4. レス一覧を取得
5. レスポンス投稿フォームの表示
6. 削除ボタンの表示
*/

require_once '../Controllers/PostController.php';
// session_start();エラーが出るので下記に書き換えエラー：: session_start(): Ignoring session_start() because a session is already active in　C:\xampp\htdocs\study\Views\post_detail.php
// セッションが
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 投稿IDを取得
if (!isset($_GET['id'])) {
    echo "投稿IDが指定されていません。";
    exit;
}

// 投稿データを取得
$post_id = (int)$_GET['id'];
$post = getPostById($post_id);

if (!$post) {
    echo "指定された投稿が見つかりません。";
    exit;
}

// デバッグ用出力
echo "<pre>";
print("投稿データ確認");
print_r($post);
echo "</pre>";

// デバッグレスポンス確認
echo "<pre>";
print("レスポンス確認");
print_r(getResponses($post_id));
echo "</pre>";

// デバッグ


?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>

<body>
    <div class="container">
        <!-- 投稿詳細 -->
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <p>投稿者: <?php echo htmlspecialchars($post['name']); ?> | 投稿日時: <?php echo $post['created_at']; ?></p>

        <!-- レス一覧 -->
        <h2>レス一覧</h2>
        <div>
            <?php foreach (getResponses($post_id) as $response): ?>
                <div style="margin-left: 20px;">
                    <p><strong><?php echo htmlspecialchars($response['name']); ?></strong>: <?php echo nl2br(htmlspecialchars($response['content'])); ?></p>
                    <p>投稿日: <?php echo $response['created_at']; ?></p>
                    <hr>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- レスポンス投稿フォーム -->
        <h2>レスを投稿</h2>
        <form method="post" action="../Controllers/PostController.php?action=response">
            <input type="hidden" name="parent_id" value="<?php echo $post['id']; ?>">
            <div class="form-group">
                <label for="content">本文:</label>
                <textarea id="content" name="content" rows="5" required></textarea>
            </div>
            <button type="submit" class="button">投稿</button>
        </form>

        <!-- 削除ボタン -->
        <?php if (isset($_SESSION['role'], $_SESSION['user_id']) && ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $post['user_id'])): ?>
            <form method="post" action="../Controllers/PostController.php">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                <button type="submit" class="button small">削除</button>
            </form>
        <?php endif; ?>
        <!-- 編集ボタン -->
        <?php if (isset($_SESSION['role'], $_SESSION['user_id']) && ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $post['user_id'])): ?>
            <form method="get" action="post_edit.php">
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                <button type="submit" class="button small">編集</button>
            </form>
            


        <?php endif; ?>

        <a href="post_list.php" class="button small">掲示板に戻る</a>
        </form>

        <br>

    </div>
</body>

</html>
