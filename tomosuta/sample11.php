<?php
$fruits = [
'apple' => 'りんご', 
'greape' => 'ぶどう',
'lemon' => 'レモン',
'tomato' => 'トマト',
'peach' => 'もも'];// これってピンポイントでぶどう取るってむずかいしよね。だから連想配列
// キーがapple 値がりんご
// echo $fruit['peach'];
// 全部出してーよ！

//foreach使う＝連想配列のものを出していくって認識でOK？それだけじゃないみたい
?>
<dl>
    <?php foreach ($fruits as $english => $japanese): ?>
    <dt><?php echo $english ?></dt>
    <dd><?php echo $japanese ?></dd>
    <?php endforeach; ?>

</dl>
