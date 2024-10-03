<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>万年カレンダー ver.1.0</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .sunday { color: red; }
        .saturday { color: blue; }
        .today { background-color: #90EE90; }
        .other-month { color: #ccc; }
        .holiday { background-color: #FFB6C1; }
        .nav {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .nav a {
            text-decoration: none;
            color: #333;
        }
    </style>
</head>
<body>
<?php
function getHolidays($year) {
    // 仮の祝日データ（実際の2024年の祝日に合わせて調整が必要）
    return [
        '1-1' => '元日',
        '1-8' => '成人の日',
        '2-11' => '建国記念の日',
        '2-23' => '天皇誕生日',
        '3-20' => '春分の日',
        '4-29' => '昭和の日',
        '5-3' => '憲法記念日',
        '5-4' => 'みどりの日',
        '5-5' => 'こどもの日',
        '7-15' => '海の日',
        '8-11' => '山の日',
        '9-16' => '敬老の日',
        '9-22' => '秋分の日',
        '10-14' => 'スポーツの日',
        '11-3' => '文化の日',
        '11-23' => '勤労感謝の日',
    ];
}

$holidays = getHolidays(2024);

function createCalendar($year, $month, $holidays) { // ここで引数を追加
    $firstDay = new DateTime("$year-$month-01"); // 月初め
    $lastDay = new DateTime("$year-$month-" . $firstDay->format('t')); // 月末
    
    $prevMonth = (new DateTime("$year-$month-01"))->modify('-1 month'); // 前月
    $nextMonth = (new DateTime("$year-$month-01"))->modify('+1 month'); // 翌月
    
    $calendar = "<h1>万年カレンダー ver.1.0</h1>"; // タイトル
    $calendar .= "<div class='nav'>"; // 前月・次月リンク
    $calendar .= "<a href='?y={$prevMonth->format('Y')}&m={$prevMonth->format('m')}'>&lt; 前月</a>"; // 前月リンク
    $calendar .= "<h2>{$year}年{$month}月</h2>";
    $calendar .= "<a href='?y={$nextMonth->format('Y')}&m={$nextMonth->format('m')}'>次月 &gt;</a>";
    $calendar .= "</div>";
    
    $calendar .= "<table>";
    $calendar .= "<tr><th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th></tr>";
    
    $currentDay = clone $firstDay;
    $currentDay->modify('-' . ($firstDay->format('w')) . ' days');
    
    while ($currentDay <= $lastDay) {
        $calendar .= "<tr>";
        for ($i = 0; $i < 7; $i++) {
            $class = [];
            if ($currentDay->format('w') == 0) $class[] = 'sunday';
            if ($currentDay->format('w') == 6) $class[] = 'saturday';
            if ($currentDay->format('Y-m') !== $firstDay->format('Y-m')) $class[] = 'other-month';
            if ($currentDay->format('Y-m-d') === date('Y-m-d')) $class[] = 'today';
            
            $holidayKey = $currentDay->format('n-j');
            if (isset($holidays[$holidayKey])) {
                $class[] = 'holiday';
            }
            
            $calendar .= "<td class='" . implode(' ', $class) . "'>";
            $calendar .= $currentDay->format('j');
            if (isset($holidays[$holidayKey])) {
                $calendar .= "<br><small>{$holidays[$holidayKey]}</small>";
            }
            $calendar .= "</td>";
            
            $currentDay->modify('+1 day');
        }
        $calendar .= "</tr>";
        if ($currentDay > $lastDay) break;
    }
    
    $calendar .= "</table>";
    return $calendar;
}

$year = isset($_GET['y']) ? (int)$_GET['y'] : 2024;
$month = isset($_GET['m']) ? (int)$_GET['m'] : date('n');

if ($year < 2024 || $year > 2024) $year = 2024;
if ($month < 1 || $month > 12) $month = date('n');

echo createCalendar($year, $month, $holidays);
?>
</body>
</html>
