<!DOCTYPE html>
<html>
<head>
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>
<form method="post" action="../Controllers/UserController.php">
        <input type="hidden" name="action" value="login">
        <label for="login_id">ログインID:</label>
        <input type="text" id="login_id" name="login_id" required><br>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">ログイン</button>
    </form>
</body>
<p>
    <a href="register.php">新規登録はこちら</a>
</p>
</html>
