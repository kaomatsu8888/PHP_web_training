<?php
/*役割: 新規投稿画面を表示する
*/

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規投稿</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>

<body>
    <div class="post-container">
        <h1 class="post-title">新規投稿</h1>
        <form method="post" action="../Controllers/PostController.php?action=create" class="post-form">
            <div class="form-group">
                <label for="title">タイトル:</label>
                <input type="text" id="title" name="title" placeholder="タイトルを入力" required>
            </div>
            <div class="form-group">
                <label for="content">本文:</label>
                <textarea id="content" name="content" rows="10" placeholder="本文を入力" required></textarea>
            </div>
            <div class="form-buttons">
                <button type="submit" class="button">投稿</button>
                <a href="post_list.php" class="button small">戻る</a>
            </div>
        </form>
    </div>
</body>

</html>


<?php
// デバッグ用出力
echo "<pre>";
print("セッション確認");
print_r($_SESSION);
echo "</pre>";

?>
