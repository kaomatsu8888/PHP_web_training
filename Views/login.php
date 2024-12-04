<?php
// /*役割: ログイン画面を表示する*/
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="../Assets/styles.css">
</head>

<body>
    <div class="auth-container">
        <h1 class="auth-title">ログイン</h1>
        <form method="post" action="../Controllers/UserController.php" class="auth-form">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="login_id">ログインID:</label>
                <input type="text" id="login_id" name="login_id" placeholder="ユーザーIDを入力" required><?php // 「ユーザーIDを入力」が薄く出力されている ?>
            </div>
            <div class="form-group">
            <?php // ここの入力フォームはパスワードをいれると黒い◯が表示される.htmlの仕組みでpasswordという属性を持つinputタグは入力された文字が隠される ?>
                <label for="password">パスワード:</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力" required><?php // 「パスワードを入力」が薄く入力されている ?>
                <!-- チェックボックスを押したらパスワードを見えるように -->
                <button id="show-password" type="button">パスワードを表示</button>
            </div>
            <button type="submit" class="button">ログイン</button>
        </form>
        <p class="auth-link">
            <a href="register.php">新規登録はこちら</a>
        </p>
    </div>
</body>
<!-- javascriptの動きについて分解して言語化すると以下のようになる
1. パスワード表示ボタンをクリックすると、パスワードの入力欄のtype属性がpasswordからtextに変わる
2. パスワードの入力欄のtype属性がpasswordの場合、パスワードが隠れる
3. パスワードの入力欄のtype属性がtextの場合、パスワードが表示される
4. パスワード表示ボタンを再度クリックすると、パスワードの入力欄のtype属性がtextからpasswordに変わる
5. パスワードの入力欄のtype属性がtextの場合、パスワードが表示される

-->
<script>
    // パスワード表示ボタン.constは定数を宣言するためのキーワード.document.getElementByIdは指定したid属性を持つ要素を取得するメソッド
    const showPasswordButton = document.getElementById('show-password'); // ボタンの要素を取得.show-passwordはボタンのid
    // パスワードの入力欄の要素を取得.passwordはパスワードのid.パスワードを入力するinputタグのtype属性がpasswordの場合、パスワードが隠れる
    const passwordInput = document.getElementById('password'); 
    showPasswordButton.addEventListener('click', () => { // ボタンがクリックされたら
        if (passwordInput.type === 'password') { // パスワードが隠れている場合.パスワードが表示されている場合は、type属性がtextになる
            passwordInput.type = 'text'; // パスワードを表示する
        } else {
            passwordInput.type = 'password'; // パスワードを隠す
        }
    });
</script>

</html>
