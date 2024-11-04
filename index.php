<!DOCTYPE html> <!-- 掲示板のメインページ。投稿一覧と新規投稿フォームを表示するページです。 -->
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>掲示板 Ver.1.0</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="header">掲示板 Ver.1.0</div>

        <!-- 新規投稿ボタン -->
        <button class="button" onclick="toggleForm()">新規投稿</button>

        <!-- 投稿表示エリア -->
        <div class="content-container">
            <div class="post-container">
                <?php include 'fetch_posts.php'; ?>
            </div>

            <!-- 投稿フォーム（デフォルトで非表示）クリックしたら表示 -->
            <div class="form-container" id="postForm" style="display: none;">
                <form action="submit_post.php" method="post">
                    <label>名前</label>
                    <input type="text" name="name" required>
                    <label>題名</label>
                    <input type="text" name="title" required>
                    <label>本文</label>
                    <textarea name="content" rows="5" required></textarea>
                    <button type="submit" class="submit-btn">投稿</button>
                </form>
            </div>
        </div>
    </div>

    <script><!-- 新規投稿フォームの表示・非表示を切り替える関数 JavaSciptで実装 -->
        function toggleForm() {
            const form = document.getElementById("postForm"); // 投稿フォームの要素を取得
            // 最初はstyle="display: none;"で非表示になっていますが、「新規投稿」ボタンをクリックするとdisplayが"block"に切り替わり、フォームが表示されます。
            // その後、もう一度「新規投稿」ボタンをクリックすると、displayが"none"に切り替わり、フォームが非表示になります。
            form.style.display = form.style.display === "none" ? "block" : "none"; 
        }
    </script>
</body>

</html>
