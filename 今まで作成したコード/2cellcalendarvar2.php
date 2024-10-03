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
        }
    </style>
    <h1>万年カレンダー</h1>
    <h3>
        <a href="?ym=<?php echo $prevMonth; ?>">&lt;</a>
        <a href="?ym=<?php echo $nextMonth; ?>">&gt;</a>
    </h3>
</head>

<body>

    <?php
    //  日付を格納するための配列 日付を格納するための配列
    $day = [];

    // 今月の日数を設定
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, 9, 2024); // $daysInMonth = 30;



    // 今日の日付を取得
    $today = new DateTime(); // new DateTime()は、DateTimeクラスのインスタンス作成
    // 今月の年と月を取得、表示
    $yearMonth = $today->format("Y年n月");
    echo $yearMonth . "<br><br>";
    // 曜日の配列
    $loopsweek = ["日", "月", "火", "水", "木", "金", "土"];

    ?>
    <h1></h1>

    <table>
        <thead> <!-- 表の見出し、グループ化 -->
            <tr>
                <th>日付</th>
                <th>曜日</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $day = [];
            // 日付を配列に格納しつつ、曜日ごとの判定
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $nowday = DateTime::createFromFormat('Y-m-d', ($today->format("Y-m-") . $i)); //この処理は、日付をY-m-dの形式で取得している
                // 現在の曜日を計算（日曜を0、土曜を6とする）
                // $currentDayOfWeek = ($firstDayOfWeek + $i - 1) % 7;
                $currentDayOfWeek = $nowday->format('w'); // wは曜日を0~6の数字で取得する
                $day[$i] = [
                    "currentDayOfWeek" => $currentDayOfWeek, // 今日の曜日を取得
                    "dayClass" => "", // クラス名（色分けに使用）
                    "youbi" => $loopsweek[$currentDayOfWeek], // 曜日(日曜日=0, 月曜日=1, 土曜日=6,9月1日は日曜日なので0)
                    "day" => $nowday, // 日付
                ];
                // 今日の日付の判定
                if ($i == $today->format("j")) { // ->format("j")はtodayにアクセス
                    $day[$i]['dayClass'] .= " today"; // .=は文字列を連結
                    echo "<tr class='{$day[$i]['dayClass']}'>"; // 今日の日付の背景色を変える ※ダメコメント具体性なし　具体性ありにする　第三者目線見てわかる
                    echo "<td>$i</td>"; // $iは日付
                    echo "<td>{$loopsweek[$currentDayOfWeek]}</td>"; // $currentDayOfWeekは0~6の数字
                    echo "</tr>"; // 閉じタグ
                }
                // 土曜日の判定
                elseif ($day[$i]['currentDayOfWeek']  == 6) {
                    $day[$i]['dayClass'] .= " saturday";
                    echo "<tr class='{$day[$i]['dayClass']}'>";
                    echo "<td>$i</td>";
                    echo "<td>{$loopsweek[$currentDayOfWeek]}</td>";
                    echo "</tr>";
                }
                // 日曜日の判定
                elseif ($day[$i]['currentDayOfWeek'] == 0) {
                    $day[$i]['dayClass'] .= " sunday";
                    echo "<tr class='{$day[$i]['dayClass']}'>";
                    echo "<tr class='sunday'>";
                    echo "<td>$i</td>";
                    echo "<td>{$loopsweek[$currentDayOfWeek]}</td>";
                    echo "</tr>";
                }
                // 平日の場合
                else {
                    echo "<tr>";
                    echo "<td>$i</td>";
                    echo "<td>{$loopsweek[$currentDayOfWeek]}</td>";
                    echo "</tr>";
                }
            }
            var_dump($day);
            ?>
        </tbody>
    </table>
</body>

</html>
