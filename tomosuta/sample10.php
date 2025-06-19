<?php
$week_name = ['日','月','火','水','木','金','土'];// 配列やで
echo $week_name[6] . '<br>';
date_default_timezone_set('Asia/Tokyo'); //こいつで日本時間や
$week = date('w');
echo "今日は、$week_name[$week]曜日です";
//3だったら水曜という判定で作りたい。
