<?php
/*
役割: 新しいユーザーを登録するためのページ。ユーザー情報を受け取り、データベースに保存します。
主な処理:
名前、ログインID、パスワードを入力するフォームを表示。
パスワードをBcryptでハッシュ化し、安全にデータベースに保存。
登録が成功した場合は、ログインページへのリンクを表示します。

*/

require 'db_connect.php'; // データベースへの接続

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // POSTリクエスト。フォームが送信された場合
    $name = trim($_POST['name']);// トリムして変数に代入
    $login_id = trim($_POST['login_id']);// トリムして変数に代入
    $password = $_POST['password'];// パスワードはトリムしない

    // 入力値のバリデーション
    if (empty($name) || empty($login_id) || empty($password)){ // $name, $login_id, $passwordのいずれかが空の場合
        echo "すべてのフィールドを入力してください。";
    } else {
        // パスワードをBcryptでハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // データベースに挿入
        $stmt = $conn->prepare("INSERT INTO users (name, login_id, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $login_id, $hashed_password);

        if ($stmt->execute()) {
            echo "ユーザー登録が成功しました。<a href='login.php'>ログインはこちら</a>";
        } else {
            echo "登録に失敗しました: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー新規登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ユーザー新規登録</h1>
    <form action="register.php" method="post">
        <label>名前: <input type="text" name="name" required></label><br>
        <label>ログインID: <input type="text" name="login_id" required></label><br>
        <label>パスワード: <input type="password" name="password" required></label><br>
        <button type="submit">登録</button>
    </form>
    <a href="login.php">既にアカウントをお持ちの方はこちらでログイン</a>
</body>
</html>
