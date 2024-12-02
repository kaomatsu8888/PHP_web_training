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

// セッション開始。既にセッションが開始されている場合は何もしない
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <div class="post-container">
        <!-- 投稿詳細 -->
        <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="post-content">
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <p class="post-meta">投稿者: <?php echo htmlspecialchars($post['name']); ?> | 投稿日: <?php echo $post['created_at']; ?></p>
        </div>

        <!-- レス一覧 -->
        <div class="responses">
            <h2>レス一覧</h2>
            <?php foreach (getResponses($post_id) as $index => $response): ?>
                <div class="response">
                    <p class="response-header"><?php echo ($index + 1) . " 名前：" . htmlspecialchars($response['name']); ?></p>
                    <p class="response-body"><?php echo nl2br(htmlspecialchars($response['content'])); ?></p>
                    <p class="response-meta">投稿日: <?php echo $response['created_at']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- レスポンス投稿フォーム -->
        <form method="post" action="../Controllers/PostController.php?action=response" class="response-form">
            <input type="hidden" name="parent_id" value="<?php echo $post['id']; ?>">
            <textarea name="content" rows="5" placeholder="レスを投稿" required></textarea>
            <button type="submit" class="button">投稿</button>
        </form>

        <!-- ボタン -->
        <div class="post-buttons">
            <? // 管理者または投稿者のみ削除ボタンを表示 ?>
            <? // 条件は、セッションがroleとuser_idを持っているかつ、roleがadminまたはuser_idがpostのuser_idと一致する場合 ?>
            <?php if (isset($_SESSION['role'], $_SESSION['user_id']) && ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $post['user_id'])): ?>
                <!-- 削除ボタンを押すと確認ダイアログが表示される -->
                <form method="post" action="../Controllers/PostController.php" onsubmit="return confirmDelete();">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="button delete">削除</button>
                </form>
                <form method="get" action="post_edit.php">
                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="button small">本文とスレッドタイトルを再編集</button>
                </form>
            <?php endif; ?>
            <a href="post_list.php" class="button small">掲示板に戻る</a>
        </div>
    </div>
</body>
<!-- bodyの終わりにスクリプトを実装 -->
<!-- 動作確認
削除ボタンを押すと確認ダイアログが表示される：
「OK」をクリックすると、削除処理がサーバーに送信される。
「キャンセル」をクリックすると、削除は実行されない。 -->
<script>
    function confirmDelete() {
        return confirm("本当に削除しますか？");
    }
</script>


</html>


<?php
// セッション確認
echo "<pre>";
print("セッション確認");
print_r($_SESSION);
echo "</pre>";

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

?>
