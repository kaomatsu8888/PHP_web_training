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

    // エリアコードを別ファイルからロードする
    $areaCodes = require 'area_codes.php';


    // 定数を定義。メンテ用に年の最小値、最大値を設定
    const MIN_YEAR = 1980;
    const MAX_YEAR = 2024;
    const MIN_MONTH = 1; //触らない。月の最小値
    const MAX_MONTH = 12; //触らない。月の最大値

    // 年月の処理
    if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = (int)$_GET['year'];
        $month = (int)$_GET['month'];
    } else {
        $today = new DateTime();
        $year = $today->format('Y');
        $month = $today->format('n');
    }

    // 年の範囲を定数で制限。範囲外の場合は最大値または最小値を設定
    $year = max(MIN_YEAR, min(MAX_YEAR, $year));

    // 月の範囲を定数で制限
    $month = max(MIN_MONTH, min(MAX_MONTH, $month));

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
    if ($prevYear < MIN_YEAR) {
        $prevYear = MIN_YEAR;
        $prevMonth = 1;
    }
    if ($nextYear > MAX_YEAR) {
        $nextYear = MAX_YEAR;
        $nextMonth = 12;
    }

    // 祝日を取得
    $holidays = get_all_holidays($year);

    // カレンダー表示に必要な情報を取得.うるう年の判定は不要（cal_days_in_month関数が自動で判定してくれる）
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w');

    // 天気の処理 →別ファイルへ移動。天気の処理を別ファイルに移動することで、コードの見通しを良くする

    // エリアコードを取得
    $selectedArea = $_GET['area'] ?? 'tokyo'; // デフォルトは東京130000
    $areaCode = $areaCodes[$selectedArea];

    // 天気データを取得
    $weatherUrl = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$areaCode}.json";
    // JSONデータを取得
    $weatherJson = file_get_contents($weatherUrl); // →データ　"[{"publishingOffice":"大阪管区気象台","reportDatetime":"2024-12-18T11:00:00+09:00","timeSeries":[{"timeDefines":["2024-12-18T11:00:00+09:00","2024-12-19T00:00:00+09:00","2024-12-20T00:00:00+09:00"],"areas":[{"area":{"name":"大阪府","code":"270000"},"weatherCodes":["201","200","101"],"weathers":["くもり　昼過ぎ　から　夕方　晴れ","くもり　所により　明け方　から　朝　雪か雨","晴れ　時々　くもり"],"winds":["西の風　海上　では　西の風　やや強く","北の風　やや強く　海上　では　北の風　強く","北の風　後　南西の風"],"waves":["１メートル","１メートル　後　１．５メートル","０．５メートル"]}]},{"timeDefines":["2024-12-18T12:00:00+09:00","2024-12-18T18:00:00+09:00","2024-12-19T00:00:00+09:00","2024-12-19T06:00:00+09:00","2024-12-19T12:00:00+09:00","2024-12-19T18:00:00+09:00"],"areas":[{"area":{"name":"大阪府","code":"270000"},"pops":["10","10","30","30","20","10"]}]},{"timeDefines":["202"

    // JSONデータを連想配列に変換.格納。第2引数をtrueにすると連想配列に変換される
    $weatherData = json_decode($weatherJson, true);
    /**
    $weatherData =publishingOffice =
"大阪管区気象台"
reportDatetime ="2024-12-18T11:00:00+09:00"
timeSeries =array(2)
tempAverage =array(1)
precipAverage =array(1)
     */


    // 天気データの対応付け
    /**
     * 今日、明日、明後日の天気をそれぞれ日付に対応させる
     * $weatherForecast: 取得した天気データ
     * - 配列の要素が不足している場合、デフォルト値 '-' を設定する.カレンダーには対象外の部分には-が出力される
     */
    // areaWeather: 天気データのうち、地域の天気情報を取得
    $areaWeather = $weatherData[0]['timeSeries'][0]['areas'][0];
    // publishingOffice: 発表者を取得
    $publishingOffice = $weatherData[0]['publishingOffice'];
    // reportDatetime: 報告日時を取得
    $reportDatetime = $weatherData[0]['reportDatetime'];
    // weathers: 天気情報を取得
    $weatherForecast = $areaWeather['weathers'];

    // 今日の日付を取得
    $today = new DateTime();
    // 明日の日付を取得
    $tomorrow = (clone $today)->modify('+1 day');
    // 明後日の日付を取得
    $dayAfterTomorrow = (clone $today)->modify('+2 days');

    // weatherByDate: 日付をキーにして天気を格納.例: ['1' => '晴れ', '2' => '曇り', '3' => '雨']
    // 今日、明日、明後日の天気をそれぞれ日付に対応させる
    $weatherByDate = [
        $today->format('j') => $weatherForecast[0] ?? '-', // 今日の天気。format('j')で日付を取得。$weatherForecast[0]で今日の天気を取得.0,1,2は今日、明日、明後日の天気
        $tomorrow->format('j') => $weatherForecast[1] ?? '-', // 明日の天気
        $dayAfterTomorrow->format('j') => $weatherForecast[2] ?? '-', // 明後日の天気
    ];

    // 新規関数の定義
    // 天気情報を分割して取得.この関数で「はれ のち 雨」だったら、「はれ」と「雨」に分割して取得
    function splitWeather($weatherText)
    {
        // キーワードリスト
        $keywords = ['晴れ', 'くもり', '雨', '雪', '雷', 'ふぶく'];

        // マッチしたキーワードを収集
        // 例: '晴れのちくもり' → ['晴れ', 'くもり']
        // $matches は天気情報に現れるキーワードを文字列の出現順に格納する
        $matches = [];
        foreach ($keywords as $keyword) {
            //strpos関数は、文字列の中に特定の文字列が含まれているかどうかを調べる関数
            if (mb_strpos($weatherText, $keyword) !== false) {
                $matches[] = $keyword;
            }
        }

        // 格納した$matchesから、午前と午後の天気を取得
        $morning = $matches[0] ?? '-'; //$matches[0]で、$matchesの0番目の要素を取得。$matches[0]が存在しない場合は、'-'を返す
        $afternoon = $matches[1] ?? '-'; //午前の天気が見つからない場合は、-を返す

        return [$morning, $afternoon];
    }

    // 新規関数の定義
    // 天気アイコンを選択.天気に応じてアイコンのパスを返す
    function getWeatherIcon($weather)
    {
        $icons = [
            '晴れ' => 'sunny.png',
            'くもり' => 'cloudy.png',
            '雨' => 'rainy.png',
            '雪' => 'snowy.png',
            '雷' => 'thunder.png',
            'ふぶく' => 'blizzard.png',
            '-' => 'unknown.png',
        ];
        return $icons[$weather] ?? 'unknown.png';
    }
    ?>

    <?php //年、月を表示 
    ?>
    <h1><?= $year ?>年 <?= $month ?>月</h1>

    <h2><?= htmlspecialchars(ucfirst($selectedArea)) ?> の天気</h2>
    <p>発表者: <?= htmlspecialchars($publishingOffice) ?></p>
    <p>報告日時: <?= htmlspecialchars($reportDatetime) ?></p>

    <?php //カレンダーの年と月を入力し移動するフォーム 
    ?>

    <form action="weathercallender.php" method="get">
        <label for="area">場所を選択: </label>
        <select name="area" id="area">
            <?php foreach ($areaCodes as $area => $code): ?>
                <option value="<?= htmlspecialchars($area) ?>" <?= $selectedArea === $area ? 'selected' : '' ?>>
                    <?= ucfirst($area) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="year" value="<?= $year ?>" min="<?= MIN_YEAR ?>" max="<?= MAX_YEAR ?>" style="width: 80px; font-size: 20px;">年
        <input type="number" name="month" value="<?= $month ?>" min="<?= MIN_MONTH ?>" max="<?= MAX_MONTH ?>" style="width: 80px; font-size: 20px;">月
        <input type="submit" value="移動" style="font-size: 20px;">
    </form>
    <table>
        <thead><?php //曜日の表示 ?>
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
            echo "<tr>";
            // 前月の残りの日付を表示
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                $prevDay = $daysInPrevMonth - $firstDayOfWeek + $i + 1;
                echo "<td class='gray'>$prevDay</td>";
            }

            // 現在の月の日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7;
                $dayClass = '';
                $holidayName = '';

                // 今日の日付の判定、曜日の判定.今日の日付と一致する場合はクラス "today" を適用
                if ($day == (int)date("j") && $month == (int)date("n") && $year == (int)date("Y")) {
                    $dayClass .= " today";
                } elseif ($currentDayOfWeek == 0) {
                    $dayClass .= " sunday";
                } elseif ($currentDayOfWeek == 6) {
                    $dayClass .= " saturday";
                }

                // 祝日の名前を取得.祝日の場合はクラス "holiday" を適用
                if (isset($holidays[$currentDate])) {
                    $dayClass .= " holiday";
                    $holidayName = $holidays[$currentDate];
                }

                // 天気データの処理
                $weatherText = $weatherByDate[$day] ?? '-'; // 今日の天気を取得。データがない場合は'-'を設定
                list($morningWeather, $afternoonWeather) = splitWeather($weatherText); // 天気を午前と午後に分割
                $morningIcon = getWeatherIcon($morningWeather); // 午前天気アイコンを取得
                $afternoonIcon = getWeatherIcon($afternoonWeather); // 午後天気アイコンを取得

                // セルを出力.クラス名には色分けに使用するクラスを設定
                echo "<td class='{$dayClass}' title='{$holidayName}'>";
                echo "{$day}<br>";

                // 天気データの表示
                if ($morningWeather !== '-' || $afternoonWeather !== '-') { // 天気データがある場合 ||はorの意味
                    if ($morningWeather !== '-') { // 午前の天気がある場合
                        echo "<div class='weather morning'>";
                        echo "<span class='time-label'>午前: </span>"; //spanは、行内の要素をグループ化するための要素
                        if ($morningIcon !== 'unknown.png') { // アイコンがunknownでない場合.アイコンディレクトリの中にある一致するアイコンを表示
                            echo "<img src='icons/{$morningIcon}' alt='{$morningWeather}' class='weather-icon'>";
                        }
                        echo "<span class='weather-text'>{$morningWeather}</span>";
                        echo "</div>";
                    }
                    // 午後の天気(取得タイミングによっては明後日は取れない場合がある)
                    if ($afternoonWeather !== '-') {
                        echo "<div class='weather afternoon'>";
                        echo "<span class='time-label'>午後: </span>";
                        if ($afternoonIcon !== 'unknown.png') {
                            echo "<img src='icons/{$afternoonIcon}' alt='{$afternoonWeather}' class='weather-icon'>";
                        }
                        echo "<span class='weather-text'>{$afternoonWeather}</span>";
                        echo "</div>";
                    }
                } else {
                    echo "<span class='no-weather'>-</span>";
                }

                // 祝日名を表示
                if ($holidayName !== '') {
                    echo "<br><span class='holiday-name'>{$holidayName}</span>";
                }

                echo "</td>";

                // 行を終了し新しい行を開始
                if ($currentDayOfWeek == 6) {
                    echo "</tr><tr>";
                }
            }

            // 翌月の最初の日付を埋める。最後の週の空白セルを翌月の日付で埋める薄文字にする
            // 例: 2024年11月が最後の週が金曜日で終わる場合、12月の1日から7日までを薄文字で表示
            $remainingDays = 7 - ($firstDayOfWeek + $daysInMonth) % 7;
            if ($remainingDays < 7) {
                for ($i = 1; $i <= $remainingDays; $i++) {
                    echo "<td class='gray'>$i</td>";
                }
            }
            echo "</tr>";
            ?>
        </tbody>
    </table>
</body>

</html>
