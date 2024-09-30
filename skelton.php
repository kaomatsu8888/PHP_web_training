<?php
///// ↓ここからPHP(フレームワークでいうとコントローラ的処理) ///////////////////////////////////////////////

// html(view)で使用する変数を宣言し対編集する

$hogehoge = hoge("abc");

// 関数も宣言できる
/**
 * 〇〇(関数名)
 *
 * @param string $fuga 〇〇(引数の説明)
 * @return string 〇〇(戻り値の説明)
 */
function hoge($fuga): string
{
    $ret = "piyo";
    if ($fuga == "abc") {
        $ret = "def";        
    }
    return $ret;
}

///// ↓ここからhtml(フレームワークでいうとview) ///////////////////////////////////////////////
?>

<style>
    /* CSSを定義 */
</style>

<h1>見出し</h1>
<?php echo $hogehoge; ?>
<br>
<?=$hogehoge ?> 

<script>
    /* javascript処理を定義 */
</script>
