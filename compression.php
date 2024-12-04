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

function compressData($data) {
    $compressed = '';
    // データの長さ. 0または1の連続回数をカウントするための変数
    $length = strlen($data);
    $count = 1;

    for ($i = 0; $i < $length; $i++) { // ビット列を走査.1文字ずつ今回は16文字
        // 次のビットが現在のビットと同じ場合、カウントを増やす
        if ($i + 1 < $length && $data[$i] == $data[$i + 1]) {
            $count++;
        } else {
            // ビットとその連続回数を追加
            $compressed .= $data[$i] . $count;
            $count = 1; // カウントをリセット
        }
    }

    return $compressed;
}

/**
 * 圧縮されたデータを復元する関数
 *
 * @param string $compressed 圧縮されたデータ
 * @return string 復元されたビット列
 */
function decompressData($compressed) {
    $decompressed = '';
    $length = strlen($compressed);

    for ($i = 0; $i < $length; $i += 2) {
        $bit = $compressed[$i]; // ビット（0または1）
        $count = intval($compressed[$i + 1]); // 連続回数.intvalは文字列を数値に変換
        $decompressed .= str_repeat($bit, $count);
    }

    return $decompressed;
}

// 圧縮対象のビット列（例）
$originalData = '1000000101000010001111000111111011011011011111100010010011000011';

// 圧縮処理
$compressedData = compressData($originalData);
// echo "圧縮されたデータを表示" . $compressedData . PHP_EOL; //PHP_EOLは改行

// 復元処理
$decompressedData = decompressData($compressedData);
// echo "圧縮されたデータを復元" . $decompressedData . PHP_EOL;

// 元のデータと復元データの比較
// if ($originalData === $decompressedData) {
    // echo "成功：元のデータと復元データが一致します。" . PHP_EOL;
// } else {
    // echo "エラー：データが一致しません。" . PHP_EOL;
// }


/**
 * 1Dビット列を2D配列に変換
 */
function convertTo2DArray($data, $width) {
    $rows = str_split($data, $width); // 1行の幅で分割
    $array2D = [];
    foreach ($rows as $row) {
        $array2D[] = str_split($row); // 各行を配列に変換
    }
    return $array2D;
}


// 2D配列に変換（ここでは4x4の画像と仮定）
$imageData = convertTo2DArray($decompressedData, 4);

// 画像の幅と高さを設定
$width = 4;
$height = 4;

// 画像を作成
$image = imagecreatetruecolor($width, $height);

// 色を定義
$black = imagecolorallocate($image, 0, 0, 0); // 黒
$white = imagecolorallocate($image, 255, 255, 255); // 白

// ピクセルを描画
for ($y = 0; $y < $height; $y++) { // 画像の高さ分ループ
    for ($x = 0; $x < $width; $x++) { // 画像の幅分ループ
        $color = $imageData[$y][$x] == '1' ? $black : $white; // 1なら黒、0なら白
        imagesetpixel($image, $x, $y, $color); // ピクセルを描画
    }
}

// ヘッダーを設定して画像を出力
header('Content-Type: image/png');
imagepng($image);

// メモリを解放
imagedestroy($image);
?>


?>
