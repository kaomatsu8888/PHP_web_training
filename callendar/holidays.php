<!-- 参考URL https://koyomi8.com/reki_doc/doc_0332.html -->

<?php
// タイムゾーンを東京に設定
date_default_timezone_set('Asia/Tokyo');
/**
 * 指定された年の固定祝日を返す
 *
 * @param int $year 計算対象の年
 * @return array 指定された年の固定祝日を連想配列で返す（例: '2024-01-01' => '元日'）
 * 固定祝日とは、毎年同じ日付で祝日となるもの（例: 元日、憲法記念日など）
 */
function get_fixed_holidays(int $year): array
{
    $holidays = [
        "$year-01-01" => '元日',
        "$year-02-11" => '建国記念の日',
        "$year-05-03" => '憲法記念日',
        "$year-05-05" => 'こどもの日',
        "$year-11-03" => '文化の日',
        "$year-11-23" => '勤労感謝の日',
    ];

    // 4月29日と5月4日の祝日法に関する年次変化
    if ($year >= 2007) {
        $holidays["$year-04-29"] = '昭和の日';
        $holidays["$year-05-04"] = 'みどりの日';
    } elseif ($year >= 1989) {
        $holidays["$year-04-29"] = 'みどりの日';
    } else {
        $holidays["$year-04-29"] = '天皇誕生日';
    }

    // 5月4日は2007年以前は国民の休日として扱う
    if ($year >= 1988 && $year < 2007) {
        $holidays["$year-05-04"] = '国民の休日';
    }

    // 天皇誕生日の年次変化
    if ($year >= 2019) {
        $holidays["$year-02-23"] = '天皇誕生日';
        // 天皇誕生日が日曜日の場合、翌日を振替休日とする

    } elseif ($year >= 1989 && $year <= 2018) {
        $holidays["$year-12-23"] = '天皇誕生日';
    }


    return $holidays;
}



/**
 * 指定された年の移動祝日を計算して返す
 *
 * @param int $year 計算対象の年
 * @return array 移動祝日を連想配列で返す（例: '2024-01-13' => '成人の日'）
 * 移動祝日とは、特定の月の第◯曜日に設定される祝日（例: 成人の日、スポーツの日）
 */
function get_movable_holidays(int $year): array
{
    // 特定の月の第2月曜日を計算する無名関数.このスコープ内でしか使えない
    $second_monday = function ($month, $year) {
        return (new DateTime("second monday of $year-$month"))->format('Y-m-d'); // 指定された月の第2月曜日を取得
    };

    // 特定の月の第3月曜日を取得する無名関数
    $third_monday = function ($month, $year) {
        return (new DateTime("third monday of $year-$month"))->format('Y-m-d');
    };

    $holidays = [];

    // 敬老の日
    if ($year < 2003) {
        $holidays["$year-09-15"] = '敬老の日'; // 2002年以前は9月15日
    } else {
        $holidays[$third_monday(9, $year)] = '敬老の日'; // 2003年以降は9月の第3月曜日
    }

    // 成人の日（2000年以前は1月15日、2000年以降は1月の第2月曜日）
    if ($year < 2000) {
        $holidays["$year-01-15"] = '成人の日';
    } else {
        $holidays[$second_monday(1, $year)] = '成人の日';
    }

    // 海の日
    if ($year >= 1996 && $year < 2003) {
        $holidays["$year-07-20"] = '海の日'; // 1996年から2002年は7月20日
    } elseif ($year == 2020) {
        $holidays["$year-07-23"] = '海の日'; // 2020年はオリンピック特例で7月23日
    } elseif ($year == 2021) {
        $holidays["$year-07-22"] = '海の日'; // 2021年はオリンピック特例で7月22日
    } elseif ($year >= 2003) {
        $holidays[$third_monday(7, $year)] = '海の日'; // 2003年以降は7月の第3月曜日
    }

    // スポーツの日（体育の日）
    if ($year < 2000) {
        $holidays["$year-10-10"] = '体育の日'; // 1999年以前は10月10日
    } elseif ($year >= 2020) {
        // 2020年は特例で7月24日、通常は10月の第2月曜日
        if ($year == 2020) {
            $holidays["$year-07-24"] = 'スポーツの日'; // 2020年のみ7月24日
        } elseif ($year == 2021) {
            $holidays["$year-07-23"] = 'スポーツの日'; // 2021年のみ7月23日
        } else {
            $holidays[$second_monday(10, $year)] = 'スポーツの日'; // 通常のスポーツの日は10月の第2月曜日
        }
    } else {
        $holidays[$second_monday(10, $year)] = '体育の日'; // 2000年から2019年までは体育の日
    }

    // 山の日
    if ($year >= 2016) {
        if ($year == 2020) {
            $holidays["$year-08-10"] = '山の日'; // 2020年のみ8月10日
        } elseif ($year == 2021) {
            $holidays["$year-08-08"] = '山の日'; // 2021年は8月8日が山の日
        } else {
            $holidays["$year-08-11"] = '山の日'; // 通常の山の日は8月11日
        }
    }



    return $holidays;
}


/**
 * 指定された年の春分の日と秋分の日を計算して返す.
 * 春分の日は3月20日を基準に、秋分の日は9月23日を基準にして計算する.
 * うるう年による調整は、1980年を基準にしている.
 *
 * @param int $year 計算対象の年
 * @return array 春分の日と秋分の日を連想配列で返す (例: '2024-03-20' => '春分の日')
 */
function get_solar_term_holidays(int $year): array
{
    // 春分の日を計算 (基準年: 1980年)
    // 1980年の春分の日からのズレを計算し、うるう年を考慮して日付を求める
    $spring_day = floor(20.8431 + 0.242194 * ($year - 1980)) - floor(($year - 1980) / 4); //floorは小数点以下切り捨て→桁数は指定できない
    $spring_equinox = sprintf('%s-%02d-%02d', $year, 3, $spring_day); // 計算結果を YYYY-MM-DD 形式でフォーマット

    // 秋分の日を計算 (基準年: 1980年)
    // 同様に、1980年の秋分の日からのズレを計算して、うるう年を考慮
    $autumn_day = floor(23.2488 + 0.242194 * ($year - 1980)) - floor(($year - 1980) / 4);
    $autumn_equinox = sprintf('%s-%02d-%02d', $year, 9, $autumn_day); // 計算結果を YYYY-MM-DD 形式でフォーマット

    // 計算した春分の日と秋分の日を連想配列で返す
    return [
        $spring_equinox => '春分の日',
        $autumn_equinox => '秋分の日'
    ];
}
// メモ用
// 1980年の春分の日が3月20日だと分かっている。そこから毎年0.242194日ずつズレることが分かっている.
// このズレを積み上げていけば、2024年の春分の日が何日になるか計算できる。

/**
 * 国民の休日を追加する
 * 
 * @param array &$holidays 国民の休日を追加する対象の祝日データ配列（参照渡し）
 * @param int $year 対象の年
 * @return void
 */
function add_national_holidays(&$holidays, $year)// 参照渡しを使用して、祝日データを直接変更する.
{
    // 国民の休日は1988年以降のみ適用
    if ($year < 1988) {
        return;
    }

    ksort($holidays); // 祝日を日付順にソート

    $previousHoliday = null; // 前の祝日の日付を保持する変数
    foreach ($holidays as $date => $name) { // $holidaysから日付と祝日名を取得
        if ($previousHoliday) { // 前の祝日がある場合
            $previousDate = new DateTime($previousHoliday); // 前の祝日の日付をDateTimeオブジェクトに変換
            $currentDate = new DateTime($date); // 現在の祝日の日付をDateTimeオブジェクトに変換

            // 祝日の間に1日だけ空いた平日がある場合、それを「国民の休日」にする
            if ($previousDate->diff($currentDate)->days == 2) { // diffメソッドで2つの日付の差を取得し、daysプロパティで日数を取得// 5月3日と5月5日の場合。5/3 → 5/4 → 5/5 と数えると2日の差になります
                $nationalHolidayDate = clone $previousDate; // 前の祝日の日付を複製.cloneでオブジェクトを複製しないと、元のオブジェクトが変更されてしまう
                $nationalHolidayDate->modify('+1 day'); // 1日後に変更（クローンした日付を1日後にする）
                $nationalHoliday = $nationalHolidayDate->format('Y-m-d'); // 日付をYYYY-MM-DD形式で取得

                // 国民の休日は1988年以降のみ適用
                if ($nationalHolidayDate->format('Y') >= 1988) {
                    $holidays[$nationalHoliday] = '国民の休日'; // 国民の休日を追加
                }
            }
        }
        $previousHoliday = $date; // 現在の祝日を前の祝日として保持
    }
}

// メモ用
// 1988年以降の祝日データを順番に見ていき、前の祝日との間に1日だけ空いた平日がある場合、それを「国民の休日」として追加する
// 参照渡しを使用しており、国民の休日の処理を1988年以前には適用しないようにしていたが、国民の休日が1988年以前も有効になってしまっていた。
// cloneを使用したら、国民の休日が1988年以前にも適用されるようになった。理由は、cloneでオブジェクトを複製した場合、元のオブジェクトとは別のオブジェクトとして扱われるため。らしい

/**
 * 振替休日を追加する
 * 
 * @param array &$holidays 振替休日を追加する対象の祝日データ配列（参照渡し）
 * @return void
 */
function add_substitute_holidays(&$holidays)
{
    ksort($holidays); // 祝日を日付順にソート.振替休日を計算する際に,日付順に祝日を見ていくため
    $new_holidays = []; // 振替休日を格納する配列

    foreach ($holidays as $date => $name) {
        $holidayDate = new DateTime($date);

        // 祝日が日曜日の場合、その翌日が振替休日となる
        if ($holidayDate->format('w') == 0) { // 0が日曜日
            $substituteHoliday = clone $holidayDate; // 祝日の日付を複製
            $substituteHoliday->modify('+1 day'); // 翌日に変更

            // 振替休日が既存の祝日と重ならないように調整
            while (isset($holidays[$substituteHoliday->format('Y-m-d')])) { // 振替休日がすでに祝日として存在する場合、1日ずらして再度チェック
                $substituteHoliday->modify('+1 day'); // 1日ずらす。その次の日が祝日だった場合、再度1日ずらす
            }
            $new_holidays[$substituteHoliday->format('Y-m-d')] = $name . 'の振替休日'; // 振替休日を追加
        }
    }

    $holidays = array_merge($holidays, $new_holidays);
}
// メモ用
// ksortで祝日を日付順にソートしておかないと、振替休日が正しく反映できなかった（ ksortは配列をキーで昇順にソートする）


/**
 * 指定された年の全ての祝日データを返す
 *
 * @param int $year 表示する年
 * @return array その年の全ての祝日データ（固定祝日、移動祝日、春分・秋分の日、振替休日を含む）を連想配列で返す
 */
function get_all_holidays($year)
{
    // それぞれの祝日データを結合して取得
    $holidays = array_merge( // array_mergeで複数の配列を一つにまとめる
        get_fixed_holidays($year), // 固定祝日（元日、建国記念の日など）を取得
        get_movable_holidays($year), // 移動祝日（成人の日、スポーツの日など）を取得
        get_solar_term_holidays($year) // 春分の日と秋分の日を計算して取得
    );


    // 振替休日が必要な場合には、祝日データに追加する
    add_substitute_holidays($holidays); // 日曜日に祝日がある場合、その翌日の振替休日を追加
    add_national_holidays($holidays, $year); // 祝日の間に1日だけ空いた平日がある場合、それを「国民の休日」として追加


    // 最終的に全ての祝日データ（振替休日を含む）を返す
    ksort($holidays); // 日付順にソート
    return $holidays;
}
