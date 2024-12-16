<?php
// 修正バージョン: 正しいバイナリデータを使用

// 圧縮アルゴリズム (RLE)
function compressData($data) {
    $compressed = [];
    $count = 1;
    for ($i = 1; $i < strlen($data); $i++) {
        if ($data[$i] === $data[$i - 1]) {
            $count++;
        } else {
            $compressed[] = [$data[$i - 1], $count];
            $count = 1;
        }
    }
    $compressed[] = [$data[strlen($data) - 1], $count];
    return $compressed;
}

// 解凍アルゴリズム (RLE)
function decompressData($compressed) {
    $decompressed = "";
    foreach ($compressed as $item) {
        [$bit, $count] = $item;
        $decompressed .= str_repeat($bit, $count);
    }
    return $decompressed;
}

// 画像生成
function createImage($binaryData, $width) {
    $length = strlen($binaryData);
    $height = ceil($length / $width);

    $image = imagecreate($width, $height);
    $black = imagecolorallocate($image, 0, 0, 0); // 黒
    $white = imagecolorallocate($image, 255, 255, 255); // 白

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $index = $y * $width + $x;
            if ($index < $length) {
                $color = ($binaryData[$index] === '1') ? $white : $black;
                imagesetpixel($image, $x, $y, $color);
            }
        }
    }

    imagepng($image, "output_corrected_with_data.png");
    imagedestroy($image);

    echo "画像を作成しました: output_corrected_with_data.png\n";
}

// 正しいバイナリデータを使用
$binaryData = "00000010101010000000000001010000010100000001001000100010001001011001010101010101010100100100000000000000000000000000000000000001100000000000000000001101000000000000000000011010000000000000000001010100000000000000000011111000000000000000000000000000000011000011100011000011000100000000000001100100001101000110001100001101011111011111011111011111000000000000000000000000001000000000000000001000000000000000000000000000010000000000000000011111100000000000001111100000000000000000000000110000110000111000110001000000010000000001000011010000110...";
$compressed = compressData($binaryData);
$decompressed = decompressData($compressed);

echo "元データの長さ: " . strlen($binaryData) . "\n";
echo "圧縮後のデータ:\n";
print_r($compressed);
echo "解凍後のデータの長さ: " . strlen($decompressed) . "\n";

// 画像の幅を設定
$width = 37; // 縦横比をできるだけ整える
createImage($decompressed, $width);
?>
