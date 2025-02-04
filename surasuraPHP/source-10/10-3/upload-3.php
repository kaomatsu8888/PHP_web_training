<?php
// include('includes/header.php');
  $msg = null;  // アップロード状況を表すメッセージ
  $alert = null;  // メッセージのデザイン用

  // アップロード処理
  if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])){
    // imageはinputタグのname属性.tmp_nameはアップロードされたファイルの一時的な名前.tmpフォルダに保存される
    // 無事アップロードが終わったらtmpフォルダから移動させる.tmpフォルダは一時的なフォルダなので、アップロードが終わったら削除される
    $old_name = $_FILES['image']['tmp_name'];
    $new_name = date("YmdHis"); // ベースとなるファイル名は日付
    
    $new_name .= mt_rand(); // 日付のあとにランダムな数字も追加
    $size = getimagesize($_FILES['image']['tmp_name']); //getimagesize関数で画像の情報を取得
    /*
    $size取得したデータはこんな感じでした
    array(7) {
  [0]=> int(1342)
  [1]=> int(936)
  [2]=> int(2)
  [3]=> string(21) "width="1342" height="936""
  ["bits"]=> int(8)
  ["channels"]=> int(3)
  ["mime"]=> string(10) "image/jpeg"
}

    */
    switch ($size[2]){//
      case IMAGETYPE_JPEG:
        $new_name .= '.jpg';
        break;
      case IMAGETYPE_GIF:
        $new_name .= '.gif';
        break;
      case IMAGETYPE_PNG:
        $new_name .= '.png';
        break;
      default:
        header('Location: upload.php');
        exit();
    }
    if (move_uploaded_file($old_name, 'album/'.$new_name)){
      $msg = 'アップロードしました。';
      $alert = 'success'; // Bootstrapで緑色のボックスにする
    } else {
      $msg = 'アップロードできませんでした。';
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
        <form action="upload-3.php" method="post" enctype="multipart/form-data"><!-- enctype属性にmultipart/form-dataを指定することで、ファイルをアップロードできるようになる -->
          <div class="form-group">
            <label>アップロードファイル</label>
            <!--  このinputタグのtype属性がfileになっているのは、ファイルをアップロードするため -->
            <!-- 送信されるデータは、$_FILES['image']。アップロードされたファイルの情報を取得するため -->
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
