    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="./index.php">サークルサイト</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
        <?php // aタグのhref属性には、リンク先のURLを記述する.閉じタグの前に?をつけることで、URLの後ろにパラメータを付与できる. ?>
          <li class="nav-item"><a class="nav-link" href="info.php">お知らせ</a></li>
          <li class="nav-item"><a class="nav-link" href="upload-3.php">画像アップロード</a></li>
          <li class="nav-item"><a class="nav-link" href="album-2.php">アルバム</a></li>
          <li class="nav-item"><a class="nav-link" href="bbs.php">掲示板</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">ログアウト</a></li>
        </ul>
      </div>
    </nav>
