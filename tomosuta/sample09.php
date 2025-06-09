<?php
// リファレンスdate(string $format, ?int $timestamp = null): string　2つ目のパラメータは省略してもおｋ
// unixタイムスタンプつかお
date_default_timezone_set('Asia/Tokyo'); //こいつで日本時間や
$time = time(); //1749477757これは秒数やでいま22時
echo $time . '<br>';
$day = date('n/j(D)', 1949477757);
echo $day . '<br>';

//strtotime これめっちゃみるよね　ストラトぅタイム
// String to Time 
