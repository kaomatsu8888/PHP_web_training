<!DOCTYPE html>
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

            <!-- 投稿フォーム（デフォルトで非表示） -->
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

    <script>
        function toggleForm() {
            const form = document.getElementById("postForm");
            form.style.display = form.style.display === "none" ? "block" : "none";
        }
    </script>
</body>

</html>
