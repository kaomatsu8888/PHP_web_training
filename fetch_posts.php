<?php
// データベースから投稿を取得し、一覧表示とページャーの生成
// データベース接続
$conn = new mysqli('localhost', 'root', '', 'test');
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 文字コードをUTF-8に設定
$conn->set_charset("utf8mb4");

// 1ページあたりの表示件数
$posts_per_page = 5;

// 現在のページ番号を取得（デフォルトは1）この番号がなかったら1を代入。もしなかったら挙動はどうなるか？
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// 投稿の総数を取得
$total_posts_result = $conn->query("SELECT COUNT(*) AS total FROM posts");
$total_posts_row = $total_posts_result->fetch_assoc();
$total_posts = $total_posts_row['total'];

// ページ数の計算
$total_pages = ceil($total_posts / $posts_per_page);

// 現在のページに表示する投稿を取得
$sql = "SELECT id, name, title, content, DATE_FORMAT(post_date, '%Y/%m/%d') AS formatted_date 
        FROM posts 
        ORDER BY post_date DESC 
        LIMIT $offset, $posts_per_page";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h3>" . htmlspecialchars($row['title'], ENT_NOQUOTES, 'UTF-8') . "</h3>";
        echo "<p>" . htmlspecialchars($row['content'], ENT_NOQUOTES, 'UTF-8') . "</p>";
        // idを利用して動的に表示
        echo "<div class='meta'>No." . $row['id'] . " " . htmlspecialchars($row['name'], ENT_NOQUOTES, 'UTF-8') . " " . $row['formatted_date'] . "</div>";
        echo "</div>";
    }
} else {
    echo "まだ投稿がありません。";
}

// ページャーリンクの生成
echo "<div class='pagination'>";
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        echo "<span class='current-page'>$i</span>";
    } else {
        echo "<a href='index.php?page=$i'>$i</a>";
    }
}
echo "</div>";

$conn->close();
?>
