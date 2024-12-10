<?php
// 東京(130000)の予報を取得
$url = "https://www.jma.go.jp/bosai/forecast/data/forecast/130000.json";
$json = file_get_contents($url);
$weather = json_decode($json, true);

// 特定の地域（今回は東京）だけ選択して変数に詰め直す
// データ形式例：$weather[0]['timeSeries'][0]['areas'][0]['area']['name'] = '東京地方'
$area = $weather[0]['timeSeries'][0]['areas'][0];

// 発表者と報告日時の情報を画面に書き出す
$publishingOffice = $weather[0]['publishingOffice'];
$reportDatetime = $weather[0]['reportDatetime'];
$targetArea = $area['area']['name'];
$todayWeather = $area['weathers'][0];
$tomorrowWeather = $area['weathers'][1];
$dayAfterTomorrowWeather = $area['weathers'][2];
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>東京の天気予報</title>
</head>

<body>
    <h1>東京の天気予報</h1>
    <div id="publishingOffice">発表者: <span><?php echo htmlspecialchars($publishingOffice); ?></span></div>
    <div id="reportDatetime">報告日時: <span><?php echo htmlspecialchars($reportDatetime); ?></span></div>
    <div id="targetArea">地域: <span><?php echo htmlspecialchars($targetArea); ?></span></div>
    <div id="today">今日の天気: <span><?php echo htmlspecialchars($todayWeather); ?></span></div>
    <div id="tomorrow">明日の天気: <span><?php echo htmlspecialchars($tomorrowWeather); ?></span></div>
    <div id="dayAfterTomorrow">明後日の天気: <span><?php echo htmlspecialchars($dayAfterTomorrowWeather); ?></span></div>
</body>

</html>

<!-- https://www.jma.go.jp/bosai/forecast/data/forecast/130000.json 
"[{"publishingOffice":"気象庁",
"reportDatetime":"2024-10-29T12:00:00+09:00",
"timeSeries":[{"timeDefines":["2024-10-29T11:00:00+09:00","2024-10-30T00:00:00+09:00","2024-10-31T00:00:00+09:00"],
"areas":[{"area":{"name":"東京地方","code":"130010"},
"weatherCodes":["300","313","101"],
"weathers":["雨","雨　昼前　から　くもり","晴れ　時々　くもり"],
"winds":["北の風　２３区西部　では　北の風　やや強く","北の風　２３区西部　では　北の風　やや強く","北の風　後　南の風"],
"waves":["１メートル","１メートル","０．５メートル"]},{"area":{"name":"伊豆諸島北部","code":"130020"},
"weatherCodes":["300","313","201"],
"weathers":["雨　所により　夕方　から　雷を伴い　激しく　降る","雨　昼過ぎ　から　くもり　所により　昼前　まで　雷を伴い　激しく　降る","くもり　時々　晴れ"],
"winds":["北東の風　強く","北の風　強く　後　北東の風　やや強く","北�"
-->
