<?php
// データベースから投稿一覧を取得し、投稿を表示するためのファイルです。
// データベース接続
$conn = new mysqli('localhost', 'root', '', 'test');
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 文字コードをUTF-8に設定
$conn->set_charset("utf8mb4");

// 1ページあたりの表示件数
$posts_per_page = 5;

// 現在のページ番号を取得（デフォルトは1）
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // 1より小さいページ番号は1に設定
$offset = ($page - 1) * $posts_per_page;

// 投稿の総数を取得
$total_posts_result = $conn->query("SELECT COUNT(*) AS total FROM posts");
if (!$total_posts_result) {
    die("クエリの実行に失敗しました: " . $conn->error); // クエリの実行に失敗した場合、エラーメッセージを表示して終了
}
$total_posts_row = $total_posts_result->fetch_assoc(); // 結果を1行取得
$total_posts = $total_posts_row['total']; // 投稿の総数

// ページ数の計算
$total_pages = ceil($total_posts / $posts_per_page); // ceil関数で小数点以下を切り上げ

// 現在のページに表示する投稿を取得
$sql = "SELECT id, name, title, content, 
        DATE_FORMAT(post_date, '%Y/%m/%d') 
        AS formatted_date 
        FROM posts 
        ORDER BY post_date DESC 
        LIMIT $offset, $posts_per_page"; // LIMITで取得する範囲を指定
$result = $conn->query($sql); // クエリの実行
if (!$result) {
    die("クエリの実行に失敗しました: " . $conn->error);
}

if ($result->num_rows > 0) { // 投稿が1件以上ある場合
    while ($row = $result->fetch_assoc()) { // 投稿を1行ずつ取得
        echo "<div class='post'>"; // 投稿を表示するためのHTML
        echo "<h3>" . htmlspecialchars($row['title'], ENT_NOQUOTES, 'UTF-8') . "</h3>";
        echo "<p>" . htmlspecialchars($row['content'], ENT_NOQUOTES, 'UTF-8') . "</p>";
        // htmlspecialcharsは、特殊文字をHTMLエンティティに変換する関数。XSS（クロスサイトスクリプティング）攻撃を防ぐために使用
        echo "<div class='meta'>No." . $row['id'] . " " . htmlspecialchars($row['name'], ENT_NOQUOTES, 'UTF-8') . " " . $row['formatted_date'] . "</div>";
        echo "</div>";
    }
} else { // 投稿が1件もない場合
    echo "まだ投稿がありません。";
}

// ページャーリンクの生成
echo "<div class='pagination'>";
for ($i = 1; $i <= $total_pages; $i++) { // ページ数分のリンクを生成
    if ($i == $page) { // 現在のページ番号の場合はリンクを表示しない
        echo "<span class='current-page'>$i</span>"; // 現在のページ番号を強調表示
    } else {
        echo "<a href='index.php?page=$i'>$i</a>";
    }
}
echo "</div>";

// データベース接続を閉じる
$conn->close();
