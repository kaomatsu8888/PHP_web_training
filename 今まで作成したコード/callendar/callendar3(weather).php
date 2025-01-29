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
    // 必要なファイルの読み込み
    require_once 'WeatherParser.php';
    require_once 'holidays.php';
    // 地域コードの読み込み.
    $areaCodes = require 'area_codes.php';

    // 定数定義
    const MIN_YEAR = 1980;
    const MAX_YEAR = 2025;
    const MIN_MONTH = 1;
    const MAX_MONTH = 12;

    // 現在の年月を取得
    if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = max(MIN_YEAR, min(MAX_YEAR, (int)$_GET['year']));
        $month = max(MIN_MONTH, min(MAX_MONTH, (int)$_GET['month']));
    } else {
        $today = new DateTime();
        $year = (int)$today->format('Y');
        $month = (int)$today->format('n');
    }

    // 前月・次月の計算
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

    // 年の範囲制限
    if ($prevYear < MIN_YEAR) {
        $prevYear = MIN_YEAR;
        $prevMonth = 1;
    }
    if ($nextYear > MAX_YEAR) {
        $nextYear = MAX_YEAR;
        $nextMonth = 12;
    }

    // カレンダー基本情報の取得
    $holidays = get_all_holidays($year);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w');

    // 天気情報の取得
    $selectedArea = $_GET['area'] ?? 'tokyo';
    $areaCode = $areaCodes[$selectedArea];
    
    try {
        $weatherUrl = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$areaCode}.json";
        $weatherJson = file_get_contents($weatherUrl);
        $weatherData = json_decode($weatherJson, true);

        if ($weatherData) {
            $areaWeather = $weatherData[0]['timeSeries'][0]['areas'][0];
            $publishingOffice = $weatherData[0]['publishingOffice'];
            $reportDatetime = $weatherData[0]['reportDatetime'];
            $weatherForecast = $areaWeather['weathers'];

            // 天気予報の日付対応付け
            $today = new DateTime();
            $weatherByDate = [
                $today->format('j') => $weatherForecast[0] ?? '-',
                (clone $today)->modify('+1 day')->format('j') => $weatherForecast[1] ?? '-',
                (clone $today)->modify('+2 days')->format('j') => $weatherForecast[2] ?? '-'
            ];
        }
    } catch (Exception $e) {
        $weatherError = "天気情報の取得に失敗しました。";
    }
    ?>

    <h1><?= $year ?>年 <?= $month ?>月</h1>

    <?php if (isset($weatherError)): ?>
        <p class="error"><?= htmlspecialchars($weatherError) ?></p>
    <?php else: ?>
        <h2><?= htmlspecialchars(ucfirst($selectedArea)) ?> の天気</h2>
        <p>発表者: <?= htmlspecialchars($publishingOffice) ?></p>
        <p>報告日時: <?= htmlspecialchars($reportDatetime) ?></p>
    <?php endif; ?>

    <!-- カレンダー操作フォーム -->
    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="get" class="calendar-form">
        <label for="area">場所を選択:</label>
        <select name="area" id="area" class="area-select">
            <?php foreach ($areaCodes as $area => $code): ?>
                <option value="<?= htmlspecialchars($area) ?>" 
                        <?= $selectedArea === $area ? 'selected' : '' ?>>
                    <?= ucfirst($area) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="year">年:</label>
        <input type="number" name="year" id="year" 
               value="<?= $year ?>" 
               min="<?= MIN_YEAR ?>" 
               max="<?= MAX_YEAR ?>" 
               class="year-input">

        <label for="month">月:</label>
        <input type="number" name="month" id="month" 
               value="<?= $month ?>" 
               min="<?= MIN_MONTH ?>" 
               max="<?= MAX_MONTH ?>" 
               class="month-input">

        <input type="submit" value="移動" class="submit-btn">
    </form>

    <!-- カレンダー -->
    <table class="calendar">
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
            // 天気情報を解析する処理
            $parser = new WeatherParser();
            
            // 前月の日付を表示
            echo "<tr>";
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                $prevDay = $daysInPrevMonth - $firstDayOfWeek + $i + 1;
                echo "<td class='gray'>$prevDay</td>";
            }

            // 当月の日付を表示
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7;
                
                // 日付のクラス設定
                $dayClass = [];
                if ($day == (int)date("j") && $month == (int)date("n") && $year == (int)date("Y")) {
                    $dayClass[] = "today";
                }
                if ($currentDayOfWeek == 0) $dayClass[] = "sunday";
                if ($currentDayOfWeek == 6) $dayClass[] = "saturday";
                if (isset($holidays[$currentDate])) $dayClass[] = "holiday";

                $holidayName = $holidays[$currentDate] ?? '';
                $weatherText = $weatherByDate[$day] ?? "-";
                // 天気情報を絵文字に変換。もし天気情報が取得できなかった場合は？にする
                $weatherIcon = $parser->parseWeather($weatherText);

                echo "<td class='" . implode(' ', $dayClass) . "' title='" . htmlspecialchars($holidayName) . "'>
                    <div class='date-number'>{$day}</div>
                    <div class='weather-container'>{$weatherIcon}</div>
                    <div class='holiday-name'>{$holidayName}</div>
                </td>";

                if ($currentDayOfWeek == 6) {
                    echo "</tr><tr>";
                }
            }

            // 次月の日付を表示
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
