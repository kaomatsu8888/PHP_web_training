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
$originalData = '1111000011110000';

function compressData($data) {
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

    echo "圧縮完了: $compressed\n";
    return $compressed;
}

function decompressData($compressed) {
    $decompressed = '';
    $length = strlen($compressed);

    echo nl2br("=== 復元処理開始 ===\n");
    echo nl2br("圧縮データ: $compressed\n");

    for ($i = 0; $i < $length; $i += 2) { // 2文字ずつ読み込む
        $bit = $compressed[$i]; // ビットを取得.圧縮データを2文字ずつ読み込む。最初の文字はビット、次の文字は連続回数今回なら４
        $count = intval($compressed[$i + 1]); // 連続回数を取得
        echo nl2br("ビット: $bit, 連続回数: $count\n");

        $decompressed .= str_repeat($bit, $count); // $decompressedに格納.set
        echo "現在の復元結果: $decompressed\n"; // 復元結果を表示
    }

    echo "復元完了: $decompressed\n";
    return $decompressed;
}



// 圧縮処理
echo nl2br("=== 圧縮テスト ===\n");
$compressedData = compressData($originalData);

// 復元処理
echo "\n=== 復元テスト ===\n";
$decompressedData = decompressData($compressedData);

// 元データと復元データの一致確認
echo "\n=== 結果確認 ===\n";
if ($originalData === $decompressedData) {
    echo "成功: 元のデータと復元データが一致しました。\n";
} else {
    echo "失敗: データが一致しません。\n";
}
?>
