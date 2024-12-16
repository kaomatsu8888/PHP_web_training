<?php
// 動的祝日の取得
function get_movable_holidays($year) {
    $second_monday = function($month, $year) {
        $date = new DateTime("second monday of $year-$month");
        return $date->format('Y-m-d');
    };

    return [
        $second_monday(1, $year) => '成人の日',
        $second_monday(10, $year) => '体育の日',
    ];
}

// 春分の日・秋分の日の計算
function get_solar_term_holidays($year) {
    $spring_equinox = date('Y-m-d', strtotime("$year-03-20 +".(int)(($year - 2000) * 0.2422)." days"));
    $autumn_equinox = date('Y-m-d', strtotime("$year-09-23 +".(int)(($year - 2000) * 0.2422)." days"));

    return [
        $spring_equinox => '春分の日',
        $autumn_equinox => '秋分の日',
    ];
}

// 振替休日を追加する関数
function add_substitute_holidays($holidays) {
    $new_holidays = $holidays;
    foreach ($holidays as $date => $name) {
        $holidayDate = new DateTime($date);
        if ($holidayDate->format('w') == 0) { // 日曜日なら
            $substituteHoliday = $holidayDate->modify('+1 day')->format('Y-m-d');
            if (!isset($new_holidays[$substituteHoliday])) {
                $new_holidays[$substituteHoliday] = $name . ' 振替休日';
            }
        }
    }
    return $new_holidays;
}
?>
