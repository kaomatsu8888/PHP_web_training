<!-- 実装できている -->
<!-- 固定祝日、移動祝日、春分・秋分の日、振替休日の取得。
基本的な移動祝日（成人の日、体育の日、スポーツの日など）の取得。
振替休日の追加と処理。 -->

<!-- 実装できていない、または未対応のこと -->
<!-- 2007年以前のみどりの日や2020年の特例祝日、国民の休日の特例処理など、特定の年や条件に対応する祝日。
「海の日」「山の日」「敬老の日」「天皇誕生日」など、特定の祝日の変動や新しい祝日への対応。 -->
<!-- 参考URL https://koyomi8.com/reki_doc/doc_0332.html -->

<?php

/**
 * 指定された年の固定祝日を返す
 *
 * @param int $year 計算対象の年
 * @return array 指定された年の固定祝日を連想配列で返す（例: '2024-01-01' => '元日'）
 * 固定祝日とは、毎年同じ日付で祝日となるもの（例: 元日、憲法記念日など）
 */
function get_fixed_holidays($year)
{
    return [
        "$year-01-01" => '元日',
        "$year-05-03" => '憲法記念日',
        "$year-05-04" => 'みどりの日', // (2007年から祝日,それ以前は4/29,要修正)
        "$year-05-05" => 'こどもの日',
        "$year-11-03" => '文化の日',
        "$year-11-23" => '勤労感謝の日'
    ];
}

/**
 * 指定された年の移動祝日を計算して返す
 *
 * @param int $year 計算対象の年
 * @return array 移動祝日を連想配列で返す（例: '2024-01-13' => '成人の日'）
 * 移動祝日とは、特定の月の第◯曜日に設定される祝日（例: 成人の日、スポーツの日）
 */
function get_movable_holidays($year)
{
    // 特定の月の第2月曜日を計算する無名関数.このスコープ内でしか使えない
    $second_monday = function ($month, $year) {
        return (new DateTime("second monday of $year-$month"))->format('Y-m-d'); // 指定された月の第2月曜日を取得
    };

    $holidays = [];

    // 成人の日（2000年以前は1月15日、2000年以降は1月の第2月曜日）
    if ($year < 2000) {
        $holidays["$year-01-15"] = '成人の日'; // 2000年以前は1月15日
    } else {
        $holidays[$second_monday(1, $year)] = '成人の日'; // 2000年以降は1月の第2月曜日
    }

    // 体育の日（2000年以前は10月10日、2000年から2019年までは10月の第2月曜日）
    // 2020年からは「スポーツの日」に名称変更
    if ($year < 2000) {
        $holidays["$year-10-10"] = '体育の日'; // 2000年以前は10月10日
    } elseif ($year >= 2020) {
        $holidays[$second_monday(10, $year)] = 'スポーツの日'; // 2020年以降はスポーツの日
    } else {
        $holidays[$second_monday(10, $year)] = '体育の日'; // 2000年から2019年までは体育の日
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
function get_solar_term_holidays($year)
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
 * 振替休日を追加する
 * 
 * @param array &$holidays 振替休日を追加する対象の祝日データ配列（参照渡し）
 * @return void 祝日データ配列に必要な振替休日を追加する
 */
function add_substitute_holidays(&$holidays) // 引数を参照渡しすることで、元の配列に変更を加える
{
    foreach ($holidays as $date => $name) { // 祝日データ配列から日付と祝日名を順に取得
        $holidayDate = new DateTime($date); // 日付を DateTime オブジェクトに変換 (例: "2024-01-01")

        if ($holidayDate->format('w') == 0) { // 日付が日曜日かどうか確認 (format('w')は曜日を数値で取得, 0が日曜日)
            $substituteHoliday = $holidayDate->modify('+1 day')->format('Y-m-d'); // 翌日を取得 (modify('+1 day')で翌日の月曜日に移動)1の月曜日に移動

            if (!array_key_exists($substituteHoliday, $holidays)) {
                // もし振替休日を設定しようとしている日付が祝日として存在していなければ、新たに振替休日として追加する
                // 例えば、「元日」が日曜日だった場合、その翌日（月曜日）を「元日の振替休日」として $holidays 配列に追加
                $holidays[$substituteHoliday] = $name . ' の振替休日'; // 振替休日としての名称を設定して追加する
            }
        }
    }
}

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
        get_fixed_holidays($year), // 固定祝日（元日、憲法記念日など）を取得
        get_movable_holidays($year), // 移動祝日（成人の日、スポーツの日など）を取得
        get_solar_term_holidays($year) // 春分の日と秋分の日を計算して取得
    );

    // 振替休日が必要な場合には、祝日データに追加する
    add_substitute_holidays($holidays); // 日曜日に祝日がある場合、その翌日の振替休日を追加

    // 最終的に全ての祝日データ（振替休日を含む）を返す
    return $holidays;
}
