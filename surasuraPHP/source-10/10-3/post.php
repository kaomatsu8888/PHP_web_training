<?php
// お名前
// $_POST['key名']で値を取得。GETと同じく連想配列
// ただし、GETはURLにパラメータが表示されるが、POSTは表示されない
// また、GETはURLの長さに制限があるが、POSTはない
$name = $_POST['name'];

// 性別
// ラジオボタンの場合、選択されていない場合は何も送信されない
// 今回は$gender変数に値を代入するので、何も送信されない場合の処理は不要
$gender = $_POST['gender'];

// 性別の日本語化
// 三項演算子を使って、男性か女性かを判定

// 三項演算子
// 条件式 ? trueの場合の値 : falseの場合の値
// 今回は、$genderがmanの場合は「男性」、womanの
// 場合は「女性」を$genderに代入
if ($gender == "man") {
  $gender = "男性";
} else if ($gender == "woman") {
  $gender = "女性";
} else { // ここがなかったら、manをokamaとかにしてもエラーにならない
  $gender = "不正な値です";
}

// 評価
// 送信された評価の数だけ★を追加
// 5-送信された数字の分だけ☆を追加
// 送信された評価の数は$_POST['star']で取得
// 送信された評価の数は文字列型なので、数値型に変換する
// その後、for文を使って評価の数だけ★を追加
// 5-送信された数字の分だけ☆を追加
// 例えば、送信された数字が3の場合、★★★☆☆となる
// intval関数は、文字列を数値に変換する関数. 例えば、intval('123')は123を返す
// intvalを入れた理由は、送信された数字が文字列型だから
$tmp_star = intval($_POST['star']); // 送信された評価の数
$star = ''; // 画面へ出力する用の文字列
// 送信された評価の数が1未満または5より大きい場合は「不正な値です」と表示
if ($tmp_star < 1 || $tmp_star > 5) {
  $star = '不正な値です';
} else {
  for ($i = 0; $i < $tmp_star; $i++) {
    $star .= '★'; // 送信された評価の数だけ★を追加
  }
  for (; $i < 5; $i++) {
    $star .= '☆'; // 「5-送信された数字」の分だけ☆を追加
  }
}

// ご意見
$other = $_POST['other'];
?>
<html>

<head>
  <meta charset="UTF-8">
  <title>アンケート結果</title>
</head>

<body>
  <h1>アンケート結果</h1>
  <p>お名前：<?php echo $name; ?></p>
  <p>性別：<?php echo $gender; ?></p>
  <p>評価：<?php echo $star; ?></p>
  <p>ご意見：<?php echo nl2br($other); ?></p>
</body>

</html>
