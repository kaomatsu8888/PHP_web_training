<?php

/**
 * データをランレングス圧縮する関数
 *
 * @param string $data 圧縮するビット列
 * @return string 圧縮されたデータ
 */
// 入力されたビット列を1文字ずつ読み込み、同じビットが連続している間はカウントを増やします
// 異なるビットに切り替わったら、現在のビットとその連続回数を圧縮データに追加します。
// 例えば、0000 はビット 0 が4回連続しているので、04 と表現します。
// このようにして、データの長さを短縮

// 入力データ
$originalData = '1111111110000000';

function compressData($data)
{
    $compressed = ''; // 圧縮結果を格納する
    $length = strlen($data);
    $count = 1;

    echo nl2br("=== 圧縮処理開始 ===\n"); // nl2br() を使用
    echo nl2br("入力データ: $data\n");

    for ($i = 0; $i < $length; $i++) {
        echo nl2br("現在のビット: {$data[$i]}\n");

        // 次のビットが現在のビットと同じ場合.動きについて説明
        // 例えば、1111000011110000 のようなデータがある場合、
        // 1 が 4 回連続しているので、4 をカウントします。
        if ($i + 1 < $length && $data[$i] == $data[$i + 1]) { // 次のビットが存在し、現在のビットと同じ場合
            $count++; // カウントを増加
            echo nl2br("次のビットも同じ。カウントを増加: $count\n");
        } else {
            // 異なるビットが現れた場合
            $compressed .= $data[$i] . $count;
            echo nl2br("ビット {$data[$i]} の連続カウント $count を圧縮結果に追加: $compressed\n");
            $count = 1; // カウントをリセット
        }
    }

    echo nl2br("圧縮完了: $compressed\n");
    return $compressed;
}


/**
 * ランレングス圧縮されたデータを復元する関数
 *
 * @param string $compressed 圧縮されたデータ
 * @return string 復元されたデータ
 */
function decompressData($compressed)
{
    $decompressed = '';
    $length = strlen($compressed);

    echo nl2br("=== 復元処理開始 ===\n");
    echo nl2br("圧縮データ: $compressed\n");

    for ($i = 0; $i < $length; $i += 2) { // 圧縮データを2文字ずつ読み込む
        if ($i + 1 >= $length) { // 安全性のため、範囲外アクセスを防止
            echo nl2br("エラー: 圧縮データが不正です。適切にフォーマットされていない可能性があります。\n");
            break;
        }

        $bit = $compressed[$i]; // 現在のビットを取得
        $count = intval($compressed[$i + 1]); // 連続回数を取得.intvalは文字列を数値に変換する関数
        echo nl2br("ビット: $bit, 連続回数: $count\n");

        $decompressed .= str_repeat($bit, $count); // ビットを復元。.str_repeat() は文字列を指定回数繰り返す関数
        echo nl2br("現在の復元結果: $decompressed\n");
    }

    echo nl2br("復元完了: $decompressed\n");
    return $decompressed;
}



// 圧縮処理
echo nl2br("=== 圧縮テスト ===\n");
$compressedData = compressData($originalData);

// 復元処理
echo nl2br("\n=== 復元テスト ===\n");
$decompressedData = decompressData($compressedData);

// 元データと復元データの一致確認
echo nl2br("\n=== 結果確認 ===\n");
if ($originalData === $decompressedData) {
    echo "成功: 元のデータと復元データが一致しました。\n";
} else {
    echo "失敗: データが一致しません。\n";
}
