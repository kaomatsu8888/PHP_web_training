<?php 
  $images = array();  // 画像ファイルのリストを格納する配列
  $num = 4;           // 1ページに表示する画像の数.ページング処理をするための変数


  // 画像フォルダから画像のファイル名を読み込む
  if ($handle = opendir('./album')){ // opendir関数でディレクトリを開く
  // whileなんで、ディレクトリ内のファイル名を全部取得するまで繰り返す
    while ($entry = readdir($handle)){ // readdir関数でディレクトリ内のエントリを読み込む
      // 「.」および「..」でないとき、ファイル名を配列に追加.と..を入れるのは..は一つ上のディレクトリのものも表示されるため
      if ($entry != "." && $entry != ".."){
        $images[] = $entry;
      }
    }
    closedir($handle);
  }
?>
<!doctype html>
<html lang="ja">
  <head>
    <title>サークルサイト</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  </head>
  <body>

    <?php include('navbar.php'); ?>

    <main role="main" class="container" style="padding:60px 15px 0">
      <div>
        <!-- ここから「本文」-->

        <h1>アルバム</h1>
<?php
  if (count($images) > 0){
    echo '<div class="row">';
    
    foreach ($images as $img){
      echo '<div class="col-3">';
      echo '  <div class="card">';
      echo '    <a href="./album/'.$img.'" target="_blank"><img src="./album/'.$img.'" class="img-fluid"></a>';
      echo '  </div>';
      echo '</div>';
    }
    echo '</div>';
  } else {
    echo '<div class="alert alert-dark" role="alert">画像はまだありません。</div>';
  }
?>
        <!-- 本文ここまで -->
      </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="/docs/4.5/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
