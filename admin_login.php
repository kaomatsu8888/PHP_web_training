<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
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
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE login_id = ?");
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
                header("Location: index.php");
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
