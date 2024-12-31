<?php

function deepFunction1($value) {
    // Xdebugの改良されたvar_dump()のデモ
    var_dump($value);
    return deepFunction2($value);
}

function deepFunction2($value) {
    return deepFunction3($value);
}

function deepFunction3($value) {
    return deepFunction4($value);
}

function deepFunction4($value) {
    // ここで意図的に未定義の関数を呼び出しエラーを発生させます。
    return undefinedFunction($value);
}

// エントリポイント
deepFunction1("Hello, Xdebug!");
