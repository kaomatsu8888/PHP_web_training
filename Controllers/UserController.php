<?php
/*役割：ユーザー情報の取得、登録、ログイン処理を行います。
主な処理：
- loginUser: ログイン処理
- registerUser: ユーザー登録処理
- POSTリクエストの処理
*/

// 必要なファイルの読み込み
require_once '../db.php';
session_start();

// ユーザー情報を取得
function loginUser($login_id, $password)
{
    global $pdo;

    // ユーザー情報を取得
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE login_id = ?");
    $stmt->execute([$login_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // セッションにユーザー情報を保存
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role']; // 管理者か一般ユーザーか

        // 投稿一覧ページにリダイレクト
        header('Location: ../Views/post_list.php');
        exit;
    } else {
        echo "ログインに失敗しました。IDまたはパスワードが正しくありません。";
    }
}



// ユーザー登録処理
function registerUser($name, $login_id, $password)
{
    global $pdo;

    try {
        // パスワードをハッシュ化
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // SQLクエリの実行
        $stmt = $pdo->prepare("INSERT INTO Users (name, login_id, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $login_id, $hashedPassword]);

        // 登録完了後、ログインページにリダイレクト
        header('Location: ../Views/login.php');
        exit;
    } catch (Exception $e) {
        // エラー発生時のメッセージ表示
        echo "ユーザー登録中にエラーが発生しました: " . $e->getMessage();
    }
}


// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'register') {
        registerUser($_POST['name'], $_POST['login_id'], $_POST['password']);
    } elseif ($_POST['action'] === 'login') {
        loginUser($_POST['login_id'], $_POST['password']);
    }
}
