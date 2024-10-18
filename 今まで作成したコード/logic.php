<?php
// logic.php

/**
 * 祝日データを生成するロジック部分
 * @param int $year 表示する年
 * @return array 祝日データ（配列形式）
 */
function generateHolidays($year) {
    $holidays = [
        "$year-01-01" => '元日',
        "$year-02-11" => '建国記念の日',
        "$year-04-29" => '昭和の日',
        "$year-05-03" => '憲法記念日',
        "$year-05-04" => 'みどりの日',
        "$year-05-05" => 'こどもの日',
        "$year-11-03" => '文化の日',
        "$year-11-23" => '勤労感謝の日'
    ];

    // 1月の第2月曜日を「成人の日」に設定
    $holidays[calculateSecondMonday($year, 1)] = '成人の日';

    // 10月の第2月曜日を「体育の日」に設定（2020年までの例）
    $holidays[calculateSecondMonday($year, 10)] = '体育の日';

    // 春分の日と秋分の日を計算して追加
    $holidays[calculateShunbun($year)] = '春分の日';
    $holidays[calculateShubun($year)] = '秋分の日';

    // 振替休日を設定
    foreach ($holidays as $date => $holidayName) { // $holidaysから日付と祝日名を取得
        $holidayDate = new DateTime($date); // 日付をDateTimeオブジェクトに変換
        if ($holidayDate->format('w') == 0) { // 日曜日なら
            $substituteHoliday = $holidayDate->modify('+1 day')->format('Y-m-d'); // 翌日（月曜日）を取得
            $holidays[$substituteHoliday] = '振替休日'; // 次の月曜日を振替休日として追加
        }
    }

    return $holidays;
}

/**
 * 1月または10月の第2月曜日を求める関数
 * @param int $year 年
 * @param int $month 月
 * @return string "Y-m-d" 形式の日付
 */
function calculateSecondMonday($year, $month) { // 1月または10月の第2月曜日を求める関数
    $date = new DateTime("$year-$month-01"); // 月初めの日付を取得
    $mondayCount = 0; // 月曜日のカウント
    while ($mondayCount < 2) { // 2回目の月曜日まで繰り返す
        if ($date->format('N') == 1) { // 月曜日なら
            $mondayCount++; // 月曜日のカウントを増やす
        }
        if ($mondayCount < 2) { // 2回目の月曜日まで繰り返す
            $date->modify('+1 day'); // 翌日に移動
        }
    }
    return $date->format('Y-m-d'); // Y-m-d形式で日付を返す
}

/**
 * 春分の日を計算する関数
 * @param int $year 年
 * @return string "Y-m-d" 形式の日付
 */
function calculateShunbun($year) {
    // 公式: 春分の日（1980年～2099年）
    $day = floor(20.8431 + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
    return "$year-03-$day";
}

/**
 * 秋分の日を計算する関数
 * @param int $year 年
 * @return string "Y-m-d" 形式の日付
 */
function calculateShubun($year) {
    // 公式: 秋分の日（1980年～2099年）
    $day = floor(23.2488 + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));
    return "$year-09-$day";
}
?>
