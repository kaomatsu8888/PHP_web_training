<?php

function buildHuffmanTree($data)
{
    $frequency = array_count_values(str_split($data)); // 各ビットの頻度を計算
    echo nl2br("頻度分布: " . print_r($frequency, true) . "\n");
    asort($frequency); // 頻度でソート
    echo nl2br("頻度分布（ソート後）: " . print_r($frequency, true) . "\n");

    $heap = [];
    foreach ($frequency as $char => $freq) {
        $heap[] = [$freq, $char];
    }
    echo nl2br("初期ヒープ: " . print_r($heap, true) . "\n");

    while (count($heap) > 1) {
        sort($heap); // 最小の2つを取り出す
        $left = array_shift($heap);
        $right = array_shift($heap);
        $freqSum = $left[0] + $right[0];
        $heap[] = [$freqSum, [$left, $right]]; // 頻度の合計を持つ新しいノードを作成
        echo nl2br("ヒープ状態（結合後）: " . print_r($heap, true) . "\n");
    }

    echo nl2br("完成したハフマン木: " . print_r($heap[0], true) . "\n");
    return $heap[0];
}

function generateHuffmanCodes($tree, $prefix = "")
{
    if (!is_array($tree[1])) {
        echo nl2br("葉ノード: {$tree[1]} => コード: $prefix\n");
        return [$tree[1] => $prefix]; // 葉ノードの場合
    }

    $leftCodes = generateHuffmanCodes($tree[1][0], $prefix . "0");
    $rightCodes = generateHuffmanCodes($tree[1][1], $prefix . "1");

    return $leftCodes + $rightCodes;
}

function huffmanEncode($data)
{
    echo nl2br("=== ハフマン符号化処理 ===\n");
    $tree = buildHuffmanTree($data);
    $codes = generateHuffmanCodes($tree);

    echo nl2br("生成されたハフマンコード: " . print_r($codes, true) . "\n");

    $encoded = implode('', array_map(fn($char) => $codes[$char], str_split($data)));
    echo nl2br("符号化されたデータ: $encoded\n");

    return [$encoded, $codes, $tree];
}

function huffmanDecode($encoded, $tree)
{
    echo nl2br("=== ハフマン復元処理 ===\n");
    $decoded = '';
    $node = $tree;

    foreach (str_split($encoded) as $bit) {
        $node = $bit === '0' ? $node[1][0] : $node[1][1];
        if (!is_array($node[1])) {
            echo nl2br("復元中: ノード {$node[1]} を追加\n");
            $decoded .= $node[1];
            $node = $tree;
        }
    }

    echo nl2br("復元されたデータ: $decoded\n");
    return $decoded;
}

// 入力データ
$originalData = '1010101010101111';

echo nl2br("入力データ: $originalData\n");

// ハフマン圧縮処理
list($encodedData, $codes, $tree) = huffmanEncode($originalData);

// ハフマン復元処理
$decodedData = huffmanDecode($encodedData, $tree);

// 元データと復元データの一致確認
echo nl2br("\n=== 結果確認 ===\n");
if ($originalData === $decodedData) {
    echo "成功: 元のデータと復元データが一致しました。\n";
} else {
    echo "失敗: データが一致しません。\n";
}
