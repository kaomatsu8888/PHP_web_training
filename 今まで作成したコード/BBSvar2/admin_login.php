<?php
/*
役割: 管理者ログイン処理を行います。

主な処理:
POSTリクエストの場合、ログインIDとパスワードを取得して、データベースに問い合わせる。
ユーザーが存在し、パスワードが一致する場合、セッションにユーザー情報を保存して、メインページにリダイレクト。
ユーザーが存在しない、またはパスワードが一致しない場合、エラーメッセージを表示。

*/
session_start();
require 'db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];

    // 入力値のバリデーション
    if (empty($login_id) || empty($password)) {
        echo "ログインIDとパスワードを入力してください。";
    } else {
        // ユーザー情報をデータベースから取得
        // $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE login_id = ?");
        $stmt = $conn->prepare(
        "SELECT id, 
        name, 
        password FROM admin_users 
        WHERE login_id = ?"
        );
        $stmt->bind_param("s", $login_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $name, $hashed_password);

        if ($stmt->fetch()) {
            // パスワードを検証
            if (password_verify($password, $hashed_password)) {
                // セッションにユーザー情報を保存
                $_SESSION['user_id'] = $id; // ここで user_id をセッションに設定
                $_SESSION['user_name'] = $name;
                // デバッグ用にセッション情報を表示
                echo "セッション情報が設定されました。";
                var_dump($_SESSION);

                header("Location: admin_dashboard.php"); // ここでダッシュボードにリダイレクト
                exit();
            } else {
                echo "パスワードが間違っています。";
            }
        } else {
            echo "ユーザーが見つかりません。";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>管理者ログイン画面</h1>
    <form action="admin_login.php" method="post">
        <label>ログインID: <input type="text" name="login_id" required></label><br>
        <label>パスワード: <input type="password" name="password" required></label><br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
