<?php
session_start(); // セッションを開始
require 'db_connect.php'; // データベース接続ファイルを読み込む

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // POSTリクエストの場合。$_SERVERはサーバーの環境変数を取得するスーパーグローバル変数。
    $login_id = trim($_POST['login_id']); 
    // トリムして変数に代入.トリムは文字列の先頭および末尾にあるホワイトスペースを取り除く.
    // ユーザーが間違ってスペースを入力した場合に、それを取り除いてログインIDを検証するため
    $password = $_POST['password']; // パスワードはトリムしない

    // 入力値のバリデーション
    if (empty($login_id) || empty($password)) {// ログインIDまたはパスワードが空の場合
        echo "ログインIDとパスワードを入力してください。";
    } else {
        // ユーザー情報をデータベースから取得
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE login_id = ?");// ユーザー情報を取得するSQL文
        $stmt->bind_param("s", $login_id);// プレースホルダに値をバインド.プレースホルダはSQL文内に記述された「?」のこと
        $stmt->execute();// SQL文を実行
        $stmt->store_result();// 結果を保存
        $stmt->bind_result($id, $name, $hashed_password); // 結果を変数にバインド.バインドとは、変数をSQL文に埋め込むこと

        if ($stmt->fetch()) {
            // パスワードを検証
            if (password_verify($password, $hashed_password)) {
                // セッションにユーザー情報を保存
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                header("Location: index.php"); // ログイン成功後にメインページにリダイレクト
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

<!-- HTML部分 -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ログイン</h1>
    <form action="login.php" method="post">
        <label>ログインID: <input type="text" name="login_id" required></label><br>
        <label>パスワード: <input type="password" name="password" required></label><br>
        <button type="submit">ログイン</button>
    </form>
    <a h
