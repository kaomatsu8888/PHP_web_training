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
    // 祝日データを読み込む関数
    function readHolidays($filePath)
    {
        $holidays = [];

        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            // 1行ずつファイルを読み込む
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) { // fgetcsv関数は、ファイルポインタから1行読み込み、CSVフィールドを処理する.1000は最大の長さ
                list($year, $month, $day, $name, $type) = $data; // データを取得する。list関数は、配列から値を取り出して変数に代入する
                if ($year === "年") continue; // ヘッダー行をスキップ

                // 配列に格納（年、月、日ごとに祝日をまとめる）
                $holidays[$year][$month][$day] = $name; // 連想配列に格納.格納データ形式例は、$holidays[2024][1][1] = '元日';
            }
            fclose($handle); // ファイルを閉じる
        }
        return $holidays; // 祝日データを返す
    }

    // 振替休日を追加する関数
    function addSubstituteHolidays(&$holidays)
    {
        foreach ($holidays as $year => $months) { // $holidaysから年と月を取得
            foreach ($months as $month => $days) { // $monthsから月と日を取得
                foreach ($days as $day => $holidayName) { // $daysから日と祝日名を取得
                    $holidayDate = new DateTime("$year-$month-$day"); // 日付をDateTimeオブジェクトに変換

                    // 祝日が日曜日の場合、翌日を振替休日として追加
                    if ($holidayDate->format('w') == 0) { // 日曜日の場合
                        $substituteDate = $holidayDate->modify('+1 day'); // 翌日を振替休日に設定
                        $substituteYear = $substituteDate->format('Y');
                        $substituteMonth = $substituteDate->format('n');
                        $substituteDay = $substituteDate->format('j');

                        // 振替休日が既に他の祝日として存在しないことを確認
                        if (!isset($holidays[$substituteYear][$substituteMonth][$substituteDay])) { // 振替休日が存在しない場合例：$holidays[2024][1][2] = '元日 振替休日';
                            $holidays[$substituteYear][$substituteMonth][$substituteDay] = $holidayName . ' の振替休日';
                        }
                    }
                }
            }
        }
    }

    // CSVファイルのパスを指定して読み込む（パスを必要に応じて修正）
    $holidaysFilePath = 'holidays.csv'; // 同ディレクトリ内にあると仮定
    $holidaysData = readHolidays($holidaysFilePath);

    // 読み込んだ祝日データに振替休日を追加
    addSubstituteHolidays($holidaysData);

    // 現在の年月を取得（GET パラメータで指定されていればそれを使用）
    if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = (int)$_GET['year'];
        $month = (int)$_GET['month'];
    } else {
        $today = new DateTime();
        $year = $today->format('Y');
        $month = $today->format('n');
    }

    // 前月・次月の計算
    $prevMonth = $month - 1;
    $prevYear = $year;
    $nextMonth = $month + 1;
    $nextYear = $year;

    // 年をまたぐ場合の処理
    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    // カレンダーを表示するために必要な日数を取得
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

    // 今月の初日の曜日を取得
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w');

    // 今日の日付
    $today = new DateTime();

    echo "<h1>{$year}年 {$month}月</h1>";
    ?>

    <!-- 次月、前月ナビゲーションボタン -->
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

            // 最初の週の空白セルを表示（前月の日付を表示しないように空白セルのみ表示）
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo "<td class='gray'></td>";
            }

            // 今月の日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDay = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7;
                $dayClass = '';
                $holidayName = '';

                // 今日の日付を判定し、適切なクラスを付与
                if ($day == $today->format("j") && $month == $today->format("n") && $year == $today->format("Y")) {
                    $dayClass .= " today";
                } elseif ($currentDayOfWeek == 0) {
                    $dayClass .= " sunday";
                } elseif ($currentDayOfWeek == 6) {
                    $dayClass .= " saturday";
                }

                // 祝日判定（外部ファイルのデータを使用）
                if (isset($holidaysData[$year][$month][$day])) {
                    $dayClass .= " holiday";
                    $holidayName = $holidaysData[$year][$month][$day]; // 祝日名を取得
                }

                // 日付と祝日名を表示
                echo "<td class='{$dayClass}' title='{$holidayName}'>$day<br><span style='font-size: 0.75em;'>$holidayName</span></td>";

                // 週の最後の日（土曜日）なら行を終了し、新しい行を開始
                if ($currentDayOfWeek == 6) {
                    echo "</tr><tr>";
                }
            }

            // 最後の週の残り部分を空白セルで埋める
            $remainingDays = 7 - ($firstDayOfWeek + $daysInMonth) % 7;
            if ($remainingDays < 7) {
                for ($i = 0; $i < $remainingDays; $i++) {
                    echo "<td class='gray'></td>";
                }
            }
            echo "</tr>"; // 最後の行を終了
            ?>
        </tbody>
    </table>

</body>

</html>
