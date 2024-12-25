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
    include 'holidays.php';
    $areaCodes = require 'area_codes.php';

    const MIN_YEAR = 1980;
    const MAX_YEAR = 2024;
    const MIN_MONTH = 1;
    const MAX_MONTH = 12;

    if (isset($_GET['year']) && isset($_GET['month'])) {
        $year = (int)$_GET['year'];
        $month = (int)$_GET['month'];
    } else {
        $today = new DateTime();
        $year = $today->format('Y');
        $month = $today->format('n');
    }

    $year = max(MIN_YEAR, min(MAX_YEAR, $year));
    $month = max(MIN_MONTH, min(MAX_MONTH, $month));

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

    if ($prevYear < MIN_YEAR) {
        $prevYear = MIN_YEAR;
        $prevMonth = 1;
    }
    if ($nextYear > MAX_YEAR) {
        $nextYear = MAX_YEAR;
        $nextMonth = 12;
    }

    $holidays = get_all_holidays($year);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);
    $firstDayOfWeek = (new DateTime("$year-$month-1"))->format('w');

    $selectedArea = $_GET['area'] ?? 'tokyo';
    $areaCode = $areaCodes[$selectedArea];
    $weatherUrl = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$areaCode}.json";
    $weatherJson = file_get_contents($weatherUrl);
    $weatherData = json_decode($weatherJson, true);

    $areaWeather = $weatherData[0]['timeSeries'][0]['areas'][0];
    $publishingOffice = $weatherData[0]['publishingOffice'];
    $reportDatetime = $weatherData[0]['reportDatetime'];
    $weatherForecast = $areaWeather['weathers'];

    $today = new DateTime();
    $tomorrow = (clone $today)->modify('+1 day');
    $dayAfterTomorrow = (clone $today)->modify('+2 days');

    $weatherByDate = [
        $today->format('j') => $weatherForecast[0] ?? '-',
        $tomorrow->format('j') => $weatherForecast[1] ?? '-',
        $dayAfterTomorrow->format('j') => $weatherForecast[2] ?? '-',
    ];

    function splitWeather($weatherText)
    {
        $keywords = ['晴れ', 'くもり', '雨', '雪', '雷', 'ふぶく'];
        $matches = [];
        foreach ($keywords as $keyword) {
            if (mb_strpos($weatherText, $keyword) !== false) {
                $matches[] = $keyword;
            }
        }
        return [$matches[0] ?? '-', $matches[1] ?? '-'];
    }

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

    <h1><?= $year ?>年 <?= $month ?>月</h1>
    <h2><?= htmlspecialchars(ucfirst($selectedArea)) ?> の天気</h2>
    <p>発表者: <?= htmlspecialchars($publishingOffice) ?></p>
    <p>報告日時: <?= htmlspecialchars($reportDatetime) ?></p>

    <form action="lastvartion.php" method="get">
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
            echo "<tr>";
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                $prevDay = $daysInPrevMonth - $firstDayOfWeek + $i + 1;
                echo "<td class='gray'>$prevDay</td>";
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $currentDayOfWeek = ($firstDayOfWeek + $day - 1) % 7;
                $dayClass = '';
                $holidayName = '';

                if ($day == (int)date("j") && $month == (int)date("n") && $year == (int)date("Y")) {
                    $dayClass .= " today";
                } elseif ($currentDayOfWeek == 0) {
                    $dayClass .= " sunday";
                } elseif ($currentDayOfWeek == 6) {
                    $dayClass .= " saturday";
                }

                if (isset($holidays[$currentDate])) {
                    $dayClass .= " holiday";
                    $holidayName = $holidays[$currentDate];
                }

                $weatherText = $weatherByDate[$day] ?? '-';
                list($morningWeather, $afternoonWeather) = splitWeather($weatherText);
                $morningIcon = getWeatherIcon($morningWeather);
                $afternoonIcon = getWeatherIcon($afternoonWeather);

                echo "<td class='{$dayClass}' title='{$holidayName}'>";
                echo "{$day}<br>";

                if ($morningWeather !== '-' || $afternoonWeather !== '-') {
                    if ($morningWeather !== '-') {
                        echo "<div class='weather morning'>";
                        echo "<span class='time-label'>午前: </span>";
                        if ($morningIcon !== 'unknown.png') {
                            echo "<img src='icons/{$morningIcon}' alt='{$morningWeather}' class='weather-icon'>";
                        }
                        echo "<span class='weather-text'>{$morningWeather}</span>";
                        echo "</div>";
                    }

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

                if ($holidayName !== '') {
                    echo "<br><span class='holiday-name'>{$holidayName}</span>";
                }

                echo "</td>";

                if ($currentDayOfWeek == 6) {
                    echo "</tr><tr>";
                }
            }

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
