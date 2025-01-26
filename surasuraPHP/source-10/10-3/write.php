<?php
include('includes/header.php');
  // データの受け取り
  $name = $_POST['name'];
  $title = $_POST['title'];
  $body = $_POST['body'];
  $pass = $_POST['pass'];
  $token = $_POST['token']; // CSRF対策

  // CSRF対策：トークンが正しいか？
  if ($token != hash("sha256", session_id())){
    header('Location: bbs.php');
    exit();
  }

  // 必須項目チェック（名前か本文が空ではないか？）
  // いずれかが空の場合はbbs.phpに戻る. || は論理演算子で、どちらかが空の場合 仮に&&だったとしたら、どちらも空の場合
  if ($name == '' || $body == ''){
    // このheader関数は、指定したURLに移動する関数
    header("Location: bbs.php");  // 空のときbbs.phpへ移動
    exit();
  }

  // 必須項目チェック（削除パスワードは４桁の数字か？
  // preg_match関数は、指定した正規表現に一致するかどうかを調べる関数.例:1234は一致するが、12345は一致しない.aaaaも一致しない.passは数字のみ。passwordの変数名
  if (!preg_match("/^[0-9]{4}$/", $pass)){ // 正規表現開始マークは「/」、終了マークは「/」、^は行の先頭、$は行の末尾、[0-9]は0から9までの数字、{4}は直前の文字が4回繰り返されることを表す
    // このheader関数は、指定したURLに移動する関数
    header("Location: bbs.php");  // 書式が違うときbbs.phpへ移動
    exit();
  }

  // DBに接続.dsnとはData Source Nameの略で、データベースに接続するための情報をまとめたもの
  $dsn = 'mysql:host=localhost;dbname=tennis;charset=utf8';
  $user = 'tennisuser';
  $password = 'password'; // tennisuserに設定したパスワード

  // tryを使用するときは、例外処理を行うcatchが必要。tryの中でエラーが発生すると、catchの中の処理が実行される。
  // キャッチに向かってスロー(throw)するとも言う.exceptionは例外のこと
  try {
    // PDOインスタンスの作成
    // dbはデータベースへの接続を表すPDOインスタンス.PDOはPHP Data Objectsの略。
    // ここは、データベースに接続するための情報をまとめたもの.PHPの拡張機能
    // このPDO（抽象化レイヤ）があるおかげで、MySQLだけでなく、PostgreSQLやSQLite,Oracleなどのデータベースにも対応できる
    $db = new PDO($dsn, $user, $password);
    // ↑続き：new演算子を使ってPDOクラスのインスタンスを作成。（オブジェクト指向）
    // エラー（例外）が発生した時の処理方法を指定
    // アロー演算子は、オブジェクトのプロパティやメソッドにアクセスするための演算子
    // PDO::ATTR_ERRMODEは、エラーレポートのモードを指定するための属性
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // プリペアドステートメントを作成
    // 実行したいクエリのテンプレートみたいなもの
    // プリペアドステートメントを使うと、SQLインジェクション対策になる
    $stmt = $db->prepare("
      INSERT INTO bbs (name, title, body, date, pass)
      VALUES (:name, :title, :body, now(), :pass)"
    );
    // プリペアドステートメントにパラメータを割り当てる
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);//この行がやっていることは、:nameというプレースホルダに$nameの値を割り当てている
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':body', $body, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    // クエリの実行
    $stmt->execute();

    // bbs.phpに戻る
    header('Location: bbs.php');
    exit();
    // エラーが発生した時の処理
  } catch (PDOException $e){
    // エラーメッセージの出力
    exit('エラー：' . $e->getMessage());
  }
?>
