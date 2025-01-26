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

    // 年の範囲を1980年から2024年に制限。範囲外の場合は最大値または最小値を設定
    $year = max(1980, min(2025, $year));

    // 月の範囲を1から12に制限
    $month = max(1, min(12, $month));

    // 前月・次月の処理
    $prevMonth = $month - 1;
    $prevYear = $year;
    $nextMonth = $month + 1;
    $nextYear = $year;

    //前月が1月を下回った場合に、12月に戻し年を1つ減らす処理です。
    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    // 年の範囲をチェックして前月・次月の年を調整
    if ($prevYear < 1980) {
        $prevYear = 1980;
        $prevMonth = 1;
    }
    if ($nextYear > 2024) {
        $nextYear = 2024;
        $nextMonth = 12;
    }


    // 祝日を取得
    $holidays = get_all_holidays($year);

    // カレンダー表示に必要な情報を取得.うるう年の判定は不要（cal_days_in_month関数が自動で判定してくれる）
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    // 前月の日数を取得→最初と最後の空白セルを埋めるため.前月の日数を取得するためには、前月の年と月を指定してcal_days_in_month関数を使う
    $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w'); // 1日の曜日を取得（0: 日曜〜6: 土曜）



    ?>
    <?php //年、月を表示 
    ?>
    <h1><?= $year ?>年 <?= $month ?>月</h1>

    <?php //カレンダーの年と月を入力し移動するフォーム 
    ?>
    <form action="callendarvar2.php" method="get">
        <input type="number" name="year" value="<?= $year ?>" min="1980" max="2024"
            style="width: 80px; font-size: 20px;">年
        <input type="number" name="month" value="<?= $month ?>" min="1" max="12"
            style="width: 80px; font-size: 20px;">月
        <input type="submit" value="移動" style="font-size: 20px;">
    </form>

    <?php // 1年前のカレンダーへのリンク。1980年の場合はダミー表示 (リンクが無くなったら位置が動くのを防ぐ)
    ?>
    <?php if ($year > 1980) : ?>
        <a href="callendarvar2.php?year=<?= $year - 1 ?>&month=<?= $month ?>">&lt;1年前のカレンダーを表示</a>
    <?php else : ?>
        <span style="visibility: hidden;">&lt;1年前のカレンダーを表示</span>
    <?php endif; ?>

    &nbsp;&nbsp;

    <?php // 1年後のカレンダーへのリンク。2024年の場合はダミー表示 (リンクが無くなったら位置が動くのを防ぐ)
    ?>
    <?php if ($year < 2024) : ?>
        <a href="callendarvar2.php?year=<?= $year + 1 ?>&month=<?= $month ?>">1年後のカレンダーを表示&gt;</a>
    <?php else : ?>
        <span style="visibility: hidden;">1年後のカレンダーを表示&gt;</span>
    <?php endif; ?>

    &nbsp;&nbsp;

    </form>
    <?php //ナビゲーションボタン 
    ?>
    <div>
        <?php // 前月リンク。1980年1月の場合はダミー表示 
        ?>
        <?php if (!($year == 1980 && $month == 1)) : ?>
            <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="nav-link"> &lt; 前月 </a>
        <?php else : ?>
            <span style="visibility: hidden;"> &lt; 前月 </span>
        <?php endif; ?>
        |
        <?php // 次月リンク。2024年12月の場合はダミー表示 
        ?>
        <?php if (!($year == 2024 && $month == 12)) : ?>
            <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="nav-link"> 次月 &gt; </a>
        <?php else : ?>
            <span style="visibility: hidden;"> 次月 &gt; </span>
        <?php endif; ?>
        <!-- 右の端に今日の日付に飛ぶリンクボタン作成 -->
        |
        <a href="callendarvar2.php" class="nav-link">今月</a>
    </div>



    <?php //カレンダー表示
    ?>
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

            // 最初の週の空白セルを前月の日付で埋める
            for ($i = 0; $i < $firstDayOfWeek; $i++) { // 最初の週の空白セルの数だけ繰り返す
                $prevDay = $daysInPrevMonth - $firstDayOfWeek + $i + 1; // 前月の日付を計算。$daysInPrevMonth-$firstDayOfWeek+1は前月の日付を表示
                echo "<td class='gray'>$prevDay</td>"; // 薄い表示にするためにクラス "gray" を適用
            }

            // 日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) { // 1日から月末日まで繰り返す
                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day); // YYYY-MM-DD 形式で日付をフォーマット.sprintfは文字列をフォーマットする関数
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7; // 曜日を計算（0: 日曜〜6: 土曜）
                $dayClass = ''; // クラス名（色分けに使用）
                $holidayName = ''; // 祝日名

                // 今日の日付の判定
                if ($day == (int)date("j") && $month == (int)date("n") && $year == (int)date("Y")) { // 今日の日付と一致する場合
                    $dayClass .= " today";
                } elseif ($currentDayOfWeek == 0) {
                    $dayClass .= " sunday";
                } elseif ($currentDayOfWeek == 6) {
                    $dayClass .= " saturday";
                }

                // 祝日の判定
                if (isset($holidays[$currentDate])) {
                    $dayClass .= " holiday"; // 祝日の場合はクラス "holiday" を適用
                    $holidayName = $holidays[$currentDate]; // 祝日名を取得
                }

                // カレンダーセルを表示。holidayNameが空の場合は何も表示しない
                echo "<td class='{$dayClass}' title='{$holidayName}'>$day<br>$holidayName</td>";

                // 週の最後の日（土曜日）なら行を終了し、新しい行を開始
                if ($currentDayOfWeek == 6) {
                    echo "</tr><tr>";
                }
            }

            // 最後の週の空白セルを次月の日付で埋める
            $remainingDays = 7 - ($firstDayOfWeek + $daysInMonth) % 7; // 残りの空白日数を計算.7-（最初の曜日+月末日）%7は次月の日付を表示
            if ($remainingDays < 7) { // 残りの空白日数が7未満の場合
                for ($i = 1; $i <= $remainingDays; $i++) { // 残りの空白日数だけ繰り返す
                    echo "<td class='gray'>$i</td>"; // 次月の日付を表示（薄い表示にするためにクラス "gray" を適用）
                }
            }
            echo "</tr>";
            ?>
        </tbody>
    </table>

</body>

</html>
