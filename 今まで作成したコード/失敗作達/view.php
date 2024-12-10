<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>万年カレンダー</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    // ロジック部分を読み込み
    include('logic.php');

    // 現在の年と月を取得（GET パラメータから取得）
    if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = (int)$_GET['year'];
        $month = (int)$_GET['month'];
    } else {
        $today = new DateTime();
        $year = $today->format('Y');
        $month = $today->format('n');
    }

    // 祝日データを取得
    $holidays = generateHolidays($year);

    // 前月・次月の設定
    $prevMonth = $month - 1;
    $prevYear = $year;
    $nextMonth = $month + 1;
    $nextYear = $year;

    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--; // --で前年にする
    }
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    // カレンダー表示に必要な日数
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w');

    echo "<h1>{$year}年 {$month}月</h1>";
    ?>
    
    <!-- ナビゲーションボタン -->
    <div>
        <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>"> &lt; 前の月 </a> |
        <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>"> 次の月 &gt; </a>
    </div>

    <table>
        <thead>
            <tr>
                <th class="sunday-header">日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th class="saturday-header">土</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 最初の週を開始
            echo "<tr>";

            // 最初の週の空白セルを表示(前月の日付)
            $prevMonthDaysToShow = $firstDayOfWeek; // 今月の最初の曜日数だけ前月の日付を表示する
            for ($i = $prevMonthDaysToShow; $i > 0; $i--) { // 2024.10月1日が火曜日でi=2、2>0なので2回ループ
                $day = $daysInPrevMonth - $i + 1; // 前月の日付を逆算して表示
                echo "<td class='gray'>$day</td>";
            }

            // 日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDay = sprintf('%04d-%02d-%02d', $year, $month, $day); // ゼロパディング
                $dayClass = '';
                $holidayName = '';

                if (isset($holidays[$currentDay])) {
                    $dayClass = $holidays[$currentDay] === '振替休日' ? 'substitute' : 'holiday';
                    $holidayName = $holidays[$currentDay];
                }

                echo "<td class='{$dayClass}' title='{$holidayName}'>$day</td>";

                // 土曜日で行を終了し、新しい行を開始
                if (($firstDayOfWeek + $day) % 7 == 0) {
                    echo "</tr><tr>";
                }
            }

            // 最後の週で次月の日付を表示
            $nextMonthDaysToShow = 7 - (($firstDayOfWeek + $daysInMonth) % 7); // 今月の最後の曜日数だけ次月の日付を表示する 7-((2+31)%7)=3
            if ($nextMonthDaysToShow < 7) { // 7未満の場合は次月の日付を表示。7以上の場合は何もしない
                for ($i = 1; $i <= $nextMonthDaysToShow; $i++) { // 1から次月の日数までループ
                    echo "<td class='gray'>$i</td>";
                }
            }

            echo "</tr>";
            ?>
        </tbody>
    </table>
</body>
</html>
