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
    <div class="container">
        <h1>新規投稿</h1>
        <form method="post" action="../Controllers/PostController.php?action=create">
            <!-- <input type="hidden" name="action" value="create"> -->
            <div class="form-group">
                <label for="title">題名:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">本文:</label>
                <textarea id="content" name="content" rows="10" required></textarea>
            </div>
            <button type="submit" class="button">投稿</button>
            <a href="post_list.php" class="button">戻る</a>
        </form>
        <br>
        
    </div>
</body>
</html>
