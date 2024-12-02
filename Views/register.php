<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">新規登録</h1>
        <form method="post" action="../Controllers/UserController.php" class="auth-form">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label for="name">名前:</label>
                <input type="text" id="name" name="name" placeholder="名前を入力" required><?php // 名前を入力が薄く出力されている ?>
            </div>
            <div class="form-group">
                <label for="login_id">ログインID (メールアドレス):</label>
                <input type="email" id="login_id" name="login_id" placeholder="メールアドレスを入力" required><?php // メールアドレスを入力が薄く出力されている ?>
            </div>
            <div class="form-group">
                <label for="password">パスワード:</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力" required><?php // パスワードを入力が薄く出力されている ?>
            </div>
            <button type="submit" class="button">登録</button>
        </form>
        <p class="auth-link">
            <a href="login.php">既にアカウントをお持ちの方はこちら</a>
        </p>
    </div>
</body>
</html>
