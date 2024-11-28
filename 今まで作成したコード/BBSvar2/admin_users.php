<?php
/*
役割: 管理者がユーザーを管理するためのページです。

主な処理:
管理者がログインしているかどうかをチェックして、ログインしていない場合はログインページにリダイレクトします。
データベースからユーザー情報を取得して、一覧表示します。
ユーザーの削除リンクを設置し、削除処理を行います。

*/
session_start();
require 'db_connect.php';

// 管理者がログインしていない場合は、ログインページにリダイレクト
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ユーザーを取得する処理
$sql = "SELECT id,
        name, login_id,created_at 
        FROM users 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー管理</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ユーザー管理</h1>
    <a href="admin_dashboard.php">ダッシュボードに戻る</a>
    <div class="users">
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>名前</th>
                        <th>ログインID</th>
                        <th>登録日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['login_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('本当に削除しますか？');">削除</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>ユーザーがいません。</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
