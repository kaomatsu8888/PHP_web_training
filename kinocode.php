<?php

// for ($i=0; $i<=2 ; $i++) {
//     for ($j=0; $j <=2 ; $j++) { 
//         echo $i. "-".$j."\n";
//     }
// }

// 0-0
// 0-1
// 0-2
// 1-0
// 1-1
// 1-2
// 2-0
// 2-1
// 2-2
// $arr = [2,4,5,8,10];
// $sum = 0;

// for ($i=0; $i < count($arr); $i++) { 
//     $sum += $arr[$i];
// }
// echo $sum;

// 方法1: foreach文を使う
// foreach ($arr as $value) {
//     echo $value."\n";
// }

// // 方法2: for文を使う
// for ($i = 0; $i < count($arr); $i++) {
//     echo $arr[$i]."\n";
// }

// // 方法3: while文を使う
// $i = 0;
// while ($i < count($arr)) {
//     echo $arr[$i]."\n";
//     $i++;
// }

// for ($i=0; $i <= 10; $i++) { 
//     if ($i === 3) {
//         continue;
//     }else if ($i == 7){
//         break;
//     }
//     echo $i."\n";
// }


// function say_hello() {
//     echo "Hello World"."\n";
// }
// say_hello();
// say_hello();
// say_hello();

// 無名関数変数　=function() {}

// $say_hello = function() {
//     echo "Good Morning"."\n";
// };//なんでここにコロンがあるんだよ！！！！　必要らしいです

// $say_hello();

// function say_hello($greeting) {
//     echo $greeting."\n"; //これは引数を入力（$greeting）を入力した文字が出力されるプログラムっすね
// };

// say_hello("ごっちゃんです");

// 引数１個バージョンの関数です
// function cal($x) {
//     echo ($x - 3)."\n";
// };

// cal(6);

// 変数2こばーじょんです
// function cal($x,$y) {
//     echo ($x *$y)."\n";
// };

// cal(4,5);

// 変数3個バージョンです
// function cal($x,$y,$z) {
//     echo ($x *$y * $z)."\n";
// };
// cal(4,5,6);

//今回の注意点として、関数内の計算しているものをただechoで表示しているだけなので値を出したりなどは
//していない
// function cal($x,$y,$z) {
//     return $x * $y * $z;
// ;
//戻って来たものを変数に入れて再利用できる。確定した値を使用する場合はreturnを使用したほうがいいかも
// $result = cal(4,5,6);
// echo $result."\n";

function cal($x,$y,$z) {
    return ($x + $y + $z)/3;
};

$result = cal(9,4,2);
    echo $result;
