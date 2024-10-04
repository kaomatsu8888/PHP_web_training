<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>万年カレンダー</title>
    <!-- styleはstyle.cssへ移動 -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    // 祝日リスト（2024）
    $holidays = [
        '2024-01-01' => '元日',
        '2024-01-08' => '成人の日',
        '2024-02-11' => '建国記念の日',
        '2024-02-23' => '天皇誕生日',
        '2024-03-20' => '春分の日',
        '2024-04-29' => '昭和の日',
        '2024-05-03' => '憲法記念日',
        '2024-05-04' => 'みどりの日',
        '2024-05-05' => 'こどもの日',
        '2024-07-15' => '海の日',
        '2024-08-11' => '山の日',
        '2024-09-16' => '敬老の日',
        '2024-09-22' => '秋分の日',
        '2024-10-14' => 'スポーツの日',
        '2024-11-03' => '文化の日',
        '2024-11-23' => '勤労感謝の日'
    ];

// 振替休日を計算し、祝日リストに追加するロジック
foreach ($holidays as $date => $holidayName) { // $holidaysから日付と祝日名を取得
    $holidayDate = new DateTime($date); // 日付をDateTimeオブジェクトに変換
    if ($holidayDate->format('w') == 0) { // 日曜日なら,format('w')で曜日を取得（0: 日曜〜6: 土曜）
        $substituteHoliday = $holidayDate->modify('+1 day')->format('Y-m-d'); // 翌日（月曜日）を取得
        // 振替休日がすでに祝日として存在しないことを確認して追加
        if (!isset($holidays[$substituteHoliday])) { // issetは変数がセットされていたらtrueを返す.今回は振替休日がすでに祝日として存在しないことを確認して追加
            $holidays[$substituteHoliday] = $holidayName . ' 振替休日';
        }
    }
}

    // 現在の年月を取得（GETパラメータを確認し、指定がなければ現在の年月を使用）
    if (isset($_GET['year']) && isset($_GET['month'])) { // GETパラメータがある場合というのは、URLに?year=2024&month=10のように指定されている場合。ない場合は現在の年月を取得する.issetは変数がセットされているかどうかを調べる関数
        $year = (int)$_GET['year']; // GETパラメータの値は文字列なので、intで数値に変換
        $month = (int)$_GET['month'];
    } else { //初期ページ用
        $today = new DateTime(); // 現在の日付を取得
        $year = $today->format('Y'); // 年を取得
        $month = $today->format('n'); // 月を取得
    }

    // 前月・次月を取得　ボタン用処理
    $prevMonth = $month - 1; // 前月
    $prevYear = $year; // 前月の年
    $nextMonth = $month + 1; // 次月
    $nextYear = $year; // 次月の年

    // 年またぎの処理　ボタン用処理
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
    $today = new DateTime(); // 現在の日付を取得

    echo "<h1>{$year}年 {$month}月</h1>"; // タイトルを表示
    ?>

    <!-- 次月、前月ナビゲーションボタン -->
    <div>
        <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>"> &lt; </a> |<!-- &lt;は左向きの矢印1ヶ月戻る -->
        <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>"> &gt; </a> <!-- &gt;は右向きの矢印進む -->
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


            // 今月の日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) { // 1日から月末日までループ
                $currentDay = sprintf('%04d-%02d-%02d', $year, $month, $day); // 今月の日付（Y-m-d 形式）.sprintfは文字列をフォーマットする関数. %04dは4桁の数字で0埋めするという意味
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7; // 曜日を計算. 0: 日曜〜6: 土曜
                $dayClass = ''; // クラス名（色分けに使用）
                $holidayName = ''; // 祝日名


                // 今日の日付を判定
                if ($day == $today->format("j") && $month == $today->format("n") && $year == $today->format("Y")) { // &&はかつ 今日の日付が今月の日付と一致する場合
                    $dayClass .= " today";
                } elseif ($currentDayOfWeek == 0) { // 日曜日の場合
                    $dayClass .= " sunday";
                } elseif ($currentDayOfWeek == 6) { // 土曜日の場合
                    $dayClass .= " saturday";
                }

                // 祝日判定
                if (isset($holidays[$currentDay])) { // issetは変数がセットされていたらtrueを返す関数
                    if (strpos($holidays[$currentDay], '振替休日') !== false) { // strposは文字列内の部分文字列が最初に現れる位置を見つける関数. 振替休日が含まれている場合
                        $dayClass .= " substitute";
                    } else {
                        $dayClass .= " holiday";
                    }
                    $holidayName = $holidays[$currentDay]; // 祝日名を取得
                }
                // 日付と祝日名を表示
                echo "<td class='{$dayClass}' title='{$holidayName}'>$day<br><span style='font-size: 0.75em;'>$holidayName</span></td>";
                // echo "<td class='{$dayClass}'>$day</td>"; // 日付を表示

                // 週の最後の日（土曜日）なら行を終了し、新しい行を開始
                if ($currentDayOfWeek == 6) {
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


            echo "</tr>"; // 最後の行を終了
            ?>
        </tbody>
    </table>

</body>

</html>
