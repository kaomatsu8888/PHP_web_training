<?php
// 元の64ビット（8x8ピクセル）の画像データ（0:白、1:黒）
$binaryData = "1000000101000010001111000111111011011011011111100010010011000011";
echo "元の画像データ:.$binaryData\n";

// バイナリ(2進数)文字列をバイナリデータに変換する関数
// やっていることは、8ビットずつに分割して、それぞれのビット列を10進数に変換して文字に変換している

function binaryStringToBinaryData($binaryString) {// 8ビットずつに分割して、それぞれのビット列を10進数に変換して文字に変換
    $bytes = [];// 10進数に変換した文字を配列に格納
    for ($i = 0; $i < strlen($binaryString); $i += 8) {// 8ビットずつに分割例えば、10000001 01000010 00111100...
        $byteStr = substr($binaryString, $i, 8);// 8ビットずつに分割.substrは、文字列の一部を取り出す
        $bytes[] = chr(bindec($byteStr));// 10進数に変換して文字に変換.chrは、10進数を文字に変換
    }
    return implode('', $bytes);// 配列を文字列に変換.implodeは、配列を文字列に変換
}

// バイナリデータを0/1文字列に戻す関数
function binaryDataToBinaryString($data) {// 1文字ずつ読み込んで、それぞれの文字を10進数に変換して8ビットのバイナリ文字列に変換
    $result = '';// 8ビットのバイナリ文字列を格納
    for ($i = 0; $i < strlen($data); $i++) {// 1文字ずつ読み込む
        $byteVal = ord($data[$i]);// 文字を10進数に変換
        $result .= str_pad(decbin($byteVal), 8, '0', STR_PAD_LEFT);// 8ビットのバイナリ文字列に変換
    }
    return $result;
}

// ■と□で8x8表示する関数
function printImage($binary) {// 8x8の画像データを1ビットずつ読み込んで、■か□を表示。例えば、10000001なら、■□□□□□□■
    for ($y = 0; $y < 8; $y++) {// 8x8の画像データを1ビットずつ読み込む。縦、横は8ビットまで。xは横、yは縦
        for ($x = 0; $x < 8; $x++) {// 1ビットずつ読み込む。縦、横は8ビットまで。xは横、yは縦
            $bit = $binary[$y * 8 + $x];// 1ビット読み込む。y * 8 + xで、8ビットごとに読み込む
            echo ($bit === '1') ? "■" : "□";// ■か□を表示。1なら■、0なら□。xを8ビット分表示。三項演算子を使用
        }
        echo "<br>";// 改行
    }
}

// バイナリデータへ変換
$rawData = binaryStringToBinaryData($binaryData);// 8ビットずつに分割して、それぞれのビット列を10進数に変換して文字に変換
// このrawDataを圧縮して、base64エンコードして、データベースに保存することで、データの容量を削減できるらしい

// gzcompressで圧縮＆base64エンコード
$compressed = base64_encode(gzcompress($rawData));// データを圧縮して、base64エンコード

// 出力を<pre>タグで囲うと整形されて見やすくなります
echo "<pre>";// 改行されなかったので、<pre>タグで囲む
echo "圧縮後の文字列: " . $compressed . "\n\n";

// 復元テスト
$decoded = base64_decode($compressed);// base64デコード。compressedをbase64デコードして、decodedに格納base64_decodeは組み込み関数
echo "base64デコード(復号)後: " . $decoded . "\n\n";
$uncompressed = gzuncompress($decoded);// データを解凍.gzucompressは、gzcompressで圧縮されたデータを解凍。decodedを解凍して、uncompressedに格納
echo "解凍後のデータ: " . $uncompressed . "\n\n";
$restoredBinary = binaryDataToBinaryString($uncompressed);// 1文字ずつ読み込んで、それぞれの文字を10進数に変換して8ビットのバイナリ文字列に変換
echo "復元後のバイナリデータ: " . $restoredBinary . "\n\n";

// チェック
if ($restoredBinary === $binaryData) { // データが一致しているか確認
    echo "データ復元成功してます！\n\n";
} else {
    echo "データが一致しません。\n\n";
}

echo "--- 復元した画像 ---\n";
printImage($restoredBinary);
echo "</pre>";

/*

Base64の仕組み
データを6ビットずつに分割して、それぞれを対応する文字（A～Z, a～z, 0～9, +, /）に変換します。
例: 100000 → g, 101000 → o

なぜBase64を使うか
安全性と互換性: 一部のシステムやネットワークでは、直接バイナリデータを扱うことができない
Base64に変換することで、文字列として安全に転送や保存ができます。
復元が簡単: Base64エンコードしたデータは、デコード（逆変換）することで元のデータに戻せます。

参考記事：https://qiita.com/PlanetMeron/items/2905e2d0aa7fe46a36d4

*/
