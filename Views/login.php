<?php
// /*役割: ログイン画面を表示する*/
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>

<body>
    <div class="auth-container">
        <h1 class="auth-title">ログイン</h1>
        <form method="post" action="../Controllers/UserController.php" class="auth-form">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="login_id">ログインID:</label>
                <input type="text" id="login_id" name="login_id" placeholder="ユーザーIDを入力" required><?php // ユーザーIDを入力が薄く出力されている ?>
            </div>
            <div class="form-group">
                <label for="password">パスワード:</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力" required><?php // パスワードを入力が薄く入力されている ?>
            </div>
            <button type="submit" class="button">ログイン</button>
        </form>
        <p class="auth-link">
            <a href="register.php">新規登録はこちら</a>
        </p>
    </div>
</body>

</html>
