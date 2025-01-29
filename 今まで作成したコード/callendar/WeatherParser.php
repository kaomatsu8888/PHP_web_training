<?php

/**
 * WeatherParser.php
 * 気象庁APIからの天気予報テキストを解析し、適切な絵文字を返すクラス
 * 
 * 天気予報の文字列を解析し、対応する絵文字を組み合わせて返します。
 * 複数の天気要素、時間変化、地域による違いなどを考慮します。
 * 
 * 使用例:
 * $parser = new WeatherParser();
 * $emoji = $parser->parseWeather("くもり　時々　晴れ");
 * 
 * @author
 * @version 1.1
 */
class WeatherParser
{
    /**
     * 時間帯に関する表現パターン
     * 時間帯と変化を表す表現を分離して定義。共通部分をまとめる
     */
    private const NIGHT_PATTERNS = '(?:夜|夜間|夜遅く)';
    private const DAY_TIME_PATTERNS = '(?:昼|昼過ぎ|夕方|明け方)';
    private const CHANGE_PATTERNS = '(?:から|後|まで)';
    private const TIME_PATTERNS = '(?:' . self::NIGHT_PATTERNS . '|' . self::DAY_TIME_PATTERNS . '|' . self::CHANGE_PATTERNS . ')';

    /**
     * 場所に関する表現パターン
     * 地域による天気の違いを表す表現
     */
    private const LOCATION_PATTERNS = '(?:所により|一部|ところにより)';

    /**
     * 基本的な天気パターン
     * 最も基本的な天気の表現を定義
     * 優先順位の高い順に配置
     */
    private $weatherPatterns = [
        // 特殊な天気現象
        'ふぶき' => [
            'pattern' => '/ふぶく|ふぶき/',
            'icon' => '🌨️'
        ],
        // 雷を含む天気
        '雷雨' => [
            'pattern' => '/雷.*雨|雨.*雷/',
            'icon' => '⛈️'
        ],
        '雷雪' => [
            'pattern' => '/雷.*雪|雪.*雷/',
            'icon' => '⛈️❄️'
        ],
        '雷' => [
            'pattern' => '/雷/',
            'icon' => '⚡'
        ],
        // 基本天気
        '雨' => [
            'pattern' => '/雨/',
            'icon' => '<img src="icons/rainy.jpg" alt="雨" class="weather-icon">'
        ],
        '雪' => [
            'pattern' => '/雪/',
            'icon' => '<img src="icons/snow.jpg" alt="雪" class="weather-icon">'
        ],
        'くもり' => [
            'pattern' => '/くもり/',
            'icon' => '<img src="icons/cloudy.jpg" alt="くもり" class="weather-icon">'
        ],
        '晴れ' => [
            'pattern' => '/晴れ/',
            // iconsフォルダにあるsunny.pngアイコンを指定
            'icon' => '<img src="icons/sunny.jpg" alt="晴れ" class="weather-icon">'

        ]
    ];

    /**
     * 複合的な天気パターン
     * 複数の天気要素や時間変化を含むパターン
     * パターンの複雑さと優先順位に基づいて順序付け
     */
    private $complexPatterns = [
        // 3要素以上の複合パターン（最優先）
        'くもり晴れ雨三要素' => [ // TODO：アイコン出現後午後から雨を追加したい
            'pattern' => '/くもり.*(?:から|後).*晴れ.*(?:所により|一部).*雨/',
            'icon' => '<img src="icons/Cloudy_then_Sunny.jpg" alt="くもり晴れ雨" class="weather-icon">'
        ],
        'くもり夜晴れ雨' => [
            'pattern' => '/くもり.*夜.*晴れ.*(?:その後|から).*雨/',
            'icon' => '☁️➡️🌙➡️🌧️'
        ],
        '晴れくもり雨複合' => [
            'pattern' => '/晴れ.*時々.*くもり.*' . self::LOCATION_PATTERNS . '.*雨/',
            'icon' => '⛅/🌧️'  // 晴れ時々くもりと所により雨の組み合わせ
        ],

        // 特殊な天気パターン
        '雪でふぶく雷' => [
            'pattern' => '/雪\s*で\s*ふぶく.*(?:雷\s*を\s*伴う|雷\s*を\s*ともなう)/',
            'icon' => '🌨️⚡'
        ],
        '雪でふぶく' => [
            'pattern' => '/雪\s*で\s*ふぶく/',
            'icon' => '<img src="icons/blizzard.jpg" alt="雪でふぶく" class="weather-icon">'
        ],

        // 時間指定のある変化パターン
        'くもり昼過ぎ晴れ' => [
            'pattern' => '/くもり.*(?:昼過ぎ|夕方).*晴れ/',
            'icon' => '<img src="icons/Cloudy_then_Sunny.jpg" alt="くもり昼過ぎ晴れ" class="weather-icon">'
        ],
        '晴れくもり雨変化' => [
            'pattern' => '/晴れ.*(?:後|から).*くもり.*時々.*雨/',
            'icon' => '☀️➡️☁️🌧️'
        ],
        'くもり雨晴れ変化' => [
            'pattern' => '/くもり.*時々.*雨.*(?:後|から|のち).*晴れ/',
            'icon' => '☁️🌧️➡️☀️'
        ],

        // 雷を伴うパターン
        '雨雷伴う' => [
            'pattern' => '/雨.*(?:雷\s*を\s*伴う|雷\s*を\s*ともなう)/',
            'icon' => '⛈️'
        ],
        '雪雷伴う' => [
            'pattern' => '/雪.*(?:雷\s*を\s*伴う|雷\s*を\s*ともなう)/',
            'icon' => '❄️⚡'
        ],

        // 雨雪の組み合わせ
        '雨か雪' => [
            'pattern' => '/雨か雪|雪か雨/',
            'icon' => '🌧️❄️'
        ],
        'くもり雨か雪' => [
            'pattern' => '/くもり.*' . self::LOCATION_PATTERNS . '.*(?:雨か雪|雪か雨)/',
            'icon' => '☁️🌧️❄️'
        ],

        // 夜間の天気変化
        '夜間晴れくもり' => [
            'pattern' => '/晴れ.*' . self::NIGHT_PATTERNS . '.*くもり/',
            'icon' => '☀️➡️🌙☁️'
        ],
        '夜間くもり晴れ' => [
            'pattern' => '/くもり.*' . self::NIGHT_PATTERNS . '.*晴れ/',
            'icon' => '☁️➡️🌙🌤️'
        ],

        // 「時々」パターン
        '晴れ時々くもり' => [
            'pattern' => '/晴れ.*時々.*くもり|くもり.*時々.*晴れ/',
            'icon' => '⛅'
        ],
        '晴れ時々雨' => [
            'pattern' => '/晴れ.*時々.*雨|雨.*時々.*晴れ/',
            'icon' => '🌦️'
        ],
        'くもり時々雨' => [
            'pattern' => '/くもり.*時々.*雨|雨.*時々.*くもり/',
            'icon' => '☁️🌧️'
        ],
        'くもり時々雪' => [
            'pattern' => '/くもり.*時々.*雪|雪.*時々.*くもり/',
            'icon' => '☁️❄️'
        ],

        // 一時パターン
        'くもり一時晴れ' => [
            'pattern' => '/くもり.*一時.*晴れ|晴れ.*一時.*くもり/',
            'icon' => '⛅'
        ],
        'くもり一時雨' => [
            'pattern' => '/くもり.*一時.*雨|雨.*一時.*くもり/',
            'icon' => '☁️🌧️'
        ],
        'くもり一時雪' => [
            'pattern' => '/くもり.*一時.*雪|雪.*一時.*くもり/',
            'icon' => '☁️❄️'
        ],

        // 時間変化パターン
        '晴れ後くもり' => [
            'pattern' => '/晴れ.*(?:から|後).*くもり/',
            'icon' => '<img src="icons/Sunny_then_Cloudy.jpg" alt="晴れ後くもり" class="weather-icon">'
        ],
        '晴れ後雨' => [
            'pattern' => '/晴れ.*(?:から|後).*雨/',
            'icon' => '☀️➡️🌧️'
        ],
        'くもり後晴れ' => [
            'pattern' => '/くもり.*(?:から|後).*晴れ/',
            'icon' => '<img src="icons/Cloudy_then_Sunny.jpg" alt="くもり後晴れ" class="weather-icon">'
        ],
        'くもり後雨' => [
            'pattern' => '/くもり.*(?:から|後).*雨/',
            'icon' => '☁️➡️🌧️'
        ],

        // 場所による違いのパターン
        'くもり所により雨' => [
            'pattern' => '/くもり.*' . self::LOCATION_PATTERNS . '.*雨/',
            'icon' => '☁️/🌧️'
        ],
        'くもり所により雪' => [
            'pattern' => '/くもり.*' . self::LOCATION_PATTERNS . '.*雪/',
            'icon' => '☁️/❄️'
        ],
        '晴れ所により雨' => [
            'pattern' => '/晴れ.*' . self::LOCATION_PATTERNS . '.*雨/',
            'icon' => '☀️/🌧️'
        ],
        '晴れ一部雪' => [
            'pattern' => '/晴れ.*' . self::LOCATION_PATTERNS . '.*雪/',
            'icon' => '☀️/❄️'
        ]
    ];

    /**
     * 天気テキストを解析して絵文字を返す
     * 
     * @param string $weatherText 気象庁APIから取得した天気予報テキスト
     * @return string 天気を表す絵文字
     */
    public function parseWeather($weatherText)
    {
        // 天気情報がない場合
        if ($weatherText === '-') {
            return '❓';
        }

        // 複合パターンの判定（時間表現を保持したまま）
        // 復号パターンの表記は、基本パターンよりも優先される
        // 例えば「くもり　夕方　から　晴れ」のような表現を解析
        // ここがないと、基本パターンの「くもり」が優先されてしまう
        foreach ($this->complexPatterns as $pattern) {
            if (preg_match($pattern['pattern'], $weatherText)) {
                return $pattern['icon'];
            }
        }

        // 基本パターンの前に時間と場所の表現を除去 preg_replace(パターン, 置換後の文字列, 対象の文字列)
        $weatherText = preg_replace(
            '/(?:' . self::TIME_PATTERNS . '|' . self::LOCATION_PATTERNS . ')/',
            '',
            $weatherText
        );

        // 基本パターンの判定

        foreach ($this->weatherPatterns as $pattern) {
            if (preg_match($pattern['pattern'], $weatherText)) {
                return $pattern['icon'];
            }
        }

        // マッチするパターンがない場合（全パターンをチェックしてもマッチしない場合）
        return '❓';
    }
}

/**
 * 天気表示のフォーマット関数
 * WeatherParserで取得した天気アイコンをHTML形式で整形して返す
 * - WeatherParserのインスタンスを効率的に管理（static変数で再利用）
 * - 天気アイコンの表示をdivタグで統一（CSSでスタイリング可能）
 * 
 * @param string $weatherText 天気予報テキスト
 * @return string HTML形式でフォーマットされた天気表示
 */
function formatWeatherDisplay($weatherText)
{
    // 天気情報を絵文字に変換。天気解析ツールを入れる箱を用意.staticは「この箱の中身を保存しておく」という意味
    static $parser = null;
    // インスタンスがない場合は新規作成。これがないと、毎回インスタンスを生成してしまう
    if ($parser === null) {
        $parser = new WeatherParser();
    }
    // 天気情報を解析して絵文字に変換
    $weatherEmoji = $parser->parseWeather($weatherText);
    // 絵文字をHTMLで表示。cssでスタイルを設定
    return sprintf('<div class="weather-emoji">%s</div>', $weatherEmoji);
}

/**
 * テストケースを実行して結果を表示
 */
// function displayWeatherTest()
// {
//     $testCases = [
//         // 基本パターン
//         "晴れ",
//         "くもり",
//         "雨",
//         "雪",
//         "雷",

//         // 時間変化パターン
//         "くもり　夕方　から　晴れ",
//         "晴れ　夜　くもり",
//         "くもり　昼過ぎ　晴れ",

//         // 天気の組み合わせ
//         "くもり　時々　晴れ",
//         "晴れ　時々　雨",
//         "くもり　一時　雨",

//         // 地域による違い
//         "くもり　所により　雨",
//         "晴れ　一部　雪",

//         // 特殊な天気
//         "雨　雷を伴う",
//         "雪　で　ふぶく",

//         // 複合パターン
//         "くもり　夕方　から　晴れ　所により　雨",
//         "晴れ　後　くもり　時々　雨",
//         "くもり　時々　雨　のち　晴れ"
//     ];

//     echo '<div class="test-cases">';
//     echo '<h3>天気パターンテスト結果</h3>';
//     echo '<pre class="test-results">';

//     $parser = new WeatherParser();
//     foreach ($testCases as $case) {
//         echo "入力: " . htmlspecialchars($case) . "\n";
//         echo "出力: " . $parser->parseWeather($case) . "\n";
//         echo "-------------------\n";
//     }

//     echo '</pre></div>';
// }

// テストの実行
// displayWeatherTest();
