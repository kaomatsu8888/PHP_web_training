<!DOCTYPE html>
<html>
<head>
    <title>新規登録</title>
</head>
<body>
    <h1>新規登録</h1>
    <form method="post" action="../Controllers/UserController.php">
        <input type="hidden" name="action" value="register">
        <label for="name">名前:</label>
        <input type="text" id="name" name="name" required><br>
        <label for="login_id">ログインID (メールアドレス):</label>
        <input type="email" id="login_id" name="login_id" required><br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">登録</button>
    </form>
</body>
</html>
