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
    // 祝日データのロジックファイルを読み込む
    include 'holidays.php';

    // 年月の処理
    if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = (int)$_GET['year'];
        $month = (int)$_GET['month'];
    } else {
        $today = new DateTime();
        $year = $today->format('Y');
        $month = $today->format('n');
    }

    // 前月・次月の処理
    $prevMonth = $month - 1;
    $prevYear = $year;
    $nextMonth = $month + 1;
    $nextYear = $year;

    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    // 祝日を取得
    $holidays = get_all_holidays($year);

    // カレンダー表示に必要な情報を取得
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w');

    echo "<h1>{$year}年 {$month}月</h1>";
    ?>

    <!-- ナビゲーションボタン -->
    <div>
        <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="nav-link"> &lt; 前月 </a> |
        <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="nav-link"> 次月 &gt; </a>
    </div>

    <!-- カレンダー表示 -->
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

            // 最初の週の空白セルを表示
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo "<td class='gray'></td>";
            }

            // 日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7;
                $dayClass = '';
                $holidayName = '';

                // 今日の日付の判定
                if ($day == (int)date("j") && $month == (int)date("n") && $year == (int)date("Y")) {
                    $dayClass .= " today";
                } elseif ($currentDayOfWeek == 0) {
                    $dayClass .= " sunday";
                } elseif ($currentDayOfWeek == 6) {
                    $dayClass .= " saturday";
                }

                // 祝日の判定
                if (isset($holidays[$currentDate])) {
                    $dayClass .= " holiday";
                    $holidayName = $holidays[$currentDate];
                }

                // カレンダーセルを表示
                echo "<td class='{$dayClass}' title='{$holidayName}'>$day<br>$holidayName</td>";

                // 週の最後の日（土曜日）なら行を終了し、新しい行を開始
                if ($currentDayOfWeek == 6) {
                    echo "</tr><tr>";
                }
            }

            // 最後の週の空白セルを埋める
            $remainingDays = 7 - ($firstDayOfWeek + $daysInMonth) % 7; //最後の週の残りの日数を計算している。7で割った余りを計算することで、最後の週の残りの日数を求めている
            if ($remainingDays < 7) {
                for ($i = 0; $i < $remainingDays; $i++) {
                    echo "<td class='gray'></td>";
                }
            }
            echo "</tr>";
            ?>
        </tbody>
    </table>

</body>

</html>
