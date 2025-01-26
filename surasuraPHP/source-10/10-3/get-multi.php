<?php
  foreach ($_GET as $key => $value){
    echo 'キー：' . $key . '<br>';
    echo '値：' . $value . '<br><br>';
  }
  /* memo
  ・$_GETは連想配列
  ・foreach文で連想配列を展開
  ・キーと値をそれぞれ出力
  htmlの入力欄で
  「http://localhost/surasuraphp/source-10/10-1/get-multi.php?param1=1&param2=2&param3=3」
  とした場合
  キー：param1
  値：1
  &は区切り文字。それぞれのパラメータを区切る
  キー：param2
  値：2
  キー：param3
  値：3
  と出力される

  */
?>
