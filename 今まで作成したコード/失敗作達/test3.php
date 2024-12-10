<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>万年カレンダー</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            text-align: center;
            padding: 8px;
        }

        .sunday {
            color: pink;
        }

        .saturday {
            color: lightblue;
        }

        .today {
            background-color: green;
            color: #fff;
        }

        .gray {
            color: #ccc;
        }
    </style>
</head>

<body>
    <?php
    // 現在の年月を取得（GETパラメータを確認し、指定がなければ現在の年月を使用）
    if (isset($_GET['year']) && isset($_GET['month'])) { // GETパラメータがある場合というのは、URLに?year=2024&month=10のように指定されている場合。ない場合は現在の年月を取得する
        $year = (int)$_GET['year']; // GETパラメータの値は文字列なので、intで数値に変換
        $month = (int)$_GET['month'];
    } else { 
        $today = new DateTime(); // 現在の日付を取得
        $year = $today->format('Y'); // 年を取得
        $month = $today->format('n'); // 月を取得
    }

    // 前月・次月を取得
    $prevMonth = $month - 1; // 前月
    $prevYear = $year; // 前月の年
    $nextMonth = $month + 1; // 次月
    $nextYear = $year; // 次月の年

    // 年またぎの処理
    if ($prevMonth < 1) { // 1月より前になった場合
        $prevMonth = 12; // 12月にする
        $prevYear--; // 年を1年前にする
    }
    if ($nextMonth > 12) { // 12月を超えた場合
        $nextMonth = 1; // 1月にする
        $nextYear++; // 年を1年後にする
    }

    // カレンダーを表示するために必要な日数を取得
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 今月の日数
    $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear); // 前月の日数

    // 今月の初日の曜日を取得
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w'); // 0: 日曜〜6: 土曜

    // 今日の日付
    $today = new DateTime();

    echo "<h1>{$year}年 {$month}月</h1>"; // タイトルを表示
    ?>

    <!-- ナビゲーションボタン -->
    <div>
        <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>"> &lt; </a> |<!-- &lt;は左向きの矢印 -->
        <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>"> &gt; </a> <!-- &gt;は右向きの矢印 -->
    </div>

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
            <?php
            // 最初の週を開始
            echo "<tr>";

            // 最初の週の空白セルを表示（前月の日付を表示する代わりに空のセルを埋める）
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo "<td></td>";
            }

            // 今月の日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7; // 曜日を計算
                $dayClass = '';

                // 今日の日付を判定
                if ($day == $today->format("j") && $month == $today->format("n") && $year == $today->format("Y")) { // &&はかつ 今日の日付が今月の日付と一致する場合
                    $dayClass .= " today";
                } elseif ($currentDayOfWeek == 0) { // 日曜日の場合
                    $dayClass .= " sunday";
                } elseif ($currentDayOfWeek == 6) { // 土曜日の場合
                    $dayClass .= " saturday";
                }

                echo "<td class='{$dayClass}'>$day</td>";

                // 週の最後の日（土曜日）なら行を終了し、新しい行を開始
                if ($currentDayOfWeek == 6) {
                    echo "</tr><tr>";
                }
            }

            echo "</tr>"; // 最後の行を終了
            ?>
        </tbody>
    </table>

</body>
</html>
