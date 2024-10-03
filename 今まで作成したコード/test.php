<?php //シンプルなカレンダー
function getCalendar(int $year, int $month): array //この関数の表記方法はgetCalendarは戻り値.($year,$month)は引数
{
    $firstDate = DateTime::createFromFormat('Y-m-d', "{$year}-{$month}-1"); // 月初めの日付を取得
    if ($firstDate === false) { // 月初めの日付が取得できない場合は例外処理
        throw new Exception("Invalid date");
    }
    $firstDayOfWeek = (int)$firstDate->format('w'); // 月初めの曜日を取得（0: 日曜〜6: 土曜）
    $lastDay = (int)$firstDate->format('t'); // 月の日数を取得
    $weeks = (int)ceil(($firstDayOfWeek + $lastDay) / 7); // 週数を計算ceilは小数点以下を切り上げ

    $calendar = []; // カレンダーの日付を格納する配列
    for ($i = 0; $i < $weeks * 7; $i++) { // 週数×7日分のループ
        $day = $i + 1 - $firstDayOfWeek; // 日付を計算
        if ($day <= 0 || $lastDay < $day) { // 日付が月の範囲外の場合は0を設定
            $day = 0; // 月の範囲外の日付は0
        }
        $calendar[intdiv($i, 7)][$i % 7] = $day; // 週ごとに日付を格納
    }
    return $calendar; // カレンダーの日付を返す
}

$year = 2024;  // または $_GET['year'] などで動的に設定
$month = 10;   // または $_GET['month'] などで動的に設定
$calendar = getCalendar($year, $month); // カレンダーを取得

// 月の名前を取得
$monthName = date("F", mktime(0, 0, 0, $month, 1, $year)); // 月の名前を取得
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        td:first-child { color: red; }
        td:last-child { color: blue; }
    </style>
</head>

<body>
    <h1><?php echo $monthName . " " . $year; ?></h1>
    <table>
        <thead>
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($calendar as $week) { ?>
                <tr>
                    <?php foreach ($week as $day) { ?>
                        <td>
                            <?php if (0 < $day) echo $day; ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>
