<?php
  $page = $_GET['page'];//スーパーグローバル変数$_GET['page']にリクエストされたページが格納されている
  echo 'リクエストされたページは' . $page . 'です。';
  //デバッグ用
  var_dump($page);

/* memo
・パラメータ名は連想配列のキーになる
例: http://localhost:8000/source-10/10-1/get.php?page=1
$_GET['page']のpageがキーになる
・値は連想配列の値になる
例: http://localhost:8000/source-10/10-1/get.php?page=1
$_GET['page']の1が値になる

出力が
リクエストされたページは1です。となる
何も入力しなかったら
define = 定義　undefined = 未定義
Warning: Undefined array key "page" in C:\xampp\htdocs\surasuraPHP\source-10\10-3\get.php on line 2
リクエストされたページはです。　
*/


?>
