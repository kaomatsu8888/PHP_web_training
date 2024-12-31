<?php
// このnullを使っているのは、アップロード状況を表すメッセージを表示する
// 初期値がnullなのは、まだアップロードされていない場合を表すため
  $msg = null;  // アップロード状況を表すメッセージ
  $alert = null;  // メッセージのデザイン用

  // アップロード処理
  /* この処理の解説：$_FILES['image']は、アップロードされたファイルの情報を持つ連想配列
  データ例は以下の通り
  $_FILES['image']['name']：アップロードされたファイルの名前
  $_FILES['image']['type']：アップロードされたファイルのMIMEタイプ
  $_FILES['image']['size']：アップロードされたファイルのサイズ
  $_FILES['image']['tmp_name']：アップロードされたファイルの一時保存先
  $_FILES['image']['error']：アップロード時のエラーコー
  
  */
  if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])){
  // この$_FILES['image']の中には、アップロードされたファイルの情報が入っている
  // 具体的には、アップロードされたファイルの名前、MIMEタイプ、サイズ、一時保存先、エラーコードが入っている
  // この中から、アップロードされたファイルの一時保存先を取得して、$old_nameに代入
  // そして、アップロードされたファイルの名前を取得して、$new_nameに代入
  // この$new_nameは、アップロードされたファイルの名前をそのまま使うのではなく、アップロードされた日時を使っている
  // これは、アップロードされたファイルの名前が重複することを防ぐため
  // そして、move_uploaded_file関数を使って、アップロードされたファイルを指定したディレクトリに移動している
  // この関数は、アップロードされたファイルを指定したディレクトリに移動する関数
    var_dump($_FILES['image']);
    $old_name = $_FILES['image']['tmp_name'];
    $new_name = $_FILES['image']['name'];
    if (move_uploaded_file($old_name, 'album/'.$new_name)){
      $msg = 'アップロードしました。';
      $alert = 'success'; // Bootstrapで緑色のボックスにする
    } else {
      $msg = 'アップロードできませんでした。エラーコード: ' . $_FILES['image']['error'];
      $alert = 'danger';  // Bootstrapで赤いボックスにする
    }
  }
?>
<!doctype html>
<html lang="ja" >
  <head>
    <title>サークルサイト</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  </head>
  <body>

    <?php include('navbar.php'); ?>

    <main role="main" class="container" style="padding:60px 15px 0">
      <div>
        <!-- ここから「本文」-->

        <h1>画像アップロード</h1>
        <?php
          if ($msg){
            echo '<div class="alert alert-'.$alert.'" role="alert">'.$msg.'</div>';
          }
        ?>
        <!-- このformタグのaction属性がupload.phpになっているのは、このページにフォームのデータを送信するため -->
        <form action="upload-2.php" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label>アップロードファイル</label>
            <!-- このinputタグのtype属性がfileになっているのは、ファイルをアップロードするため -->
            <!-- name属性がimageになっているのは、アップロードされたファイルの情報を取得するため -->
            <input type="file" name="image" class="form-control-file">
          </div>
          <input type="submit" value="アップロードする" class="btn btn-primary">
        </form>

        <!-- 本文ここまで -->
      </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
