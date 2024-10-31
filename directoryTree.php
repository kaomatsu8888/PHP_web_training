<?php

// フォルダ階層を表示する再帰関数
function displayDirectoryTree($dir, $level = 0) {// $dirは「どのフォルダを見るか」を指定する部分.$levelは「何階層目か」を指定する部分
    // ディレクトリが存在しない場合、処理を中断
    if (!is_dir($dir)) { // is_dir関数は、指定したファイルがディレクトリかどうかを調べる関数
        echo "ディレクトリが存在しません: $dir";
        return;
    }

    // ディレクトリ内のファイルとフォルダを取得
    $files = scandir($dir); // scandir関数は、指定したディレクトリ内のファイルとディレクトリの一覧を取得する関数

    foreach ($files as $file) { // ファイルとディレクトリの一覧を1つずつ取り出す
        // "." と ".." はスキップ
        if (substr($file,0 ,1)=='.') {
            continue;
        }
        // if ($file === '.' || $file === '..') { // 「.」と「..」はカレントディレクトリと親ディレクトリを表す特殊なディレクトリなので、スキップ
        //     continue;
        // }

        // インデントのためのスペースを生成
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level); // str_repeat関数は、指定した文字列を指定した回数繰り返す関数
        // これで、表示する際に少し右にずれて表示され、階層が分かりやすくなる
        // フルパスを作成
        $filePath = $dir . DIRECTORY_SEPARATOR . $file; 
        /*
        DIRECTORY_SEPARATOR は、「フォルダとファイルの間に入れる区切り文字」
        Windowsでは「\」、MacやLinuxでは「/」です。
        これを使って、フォルダの中のファイルやサブフォルダのフルパスを作成
        */

        // ファイルまたはディレクトリかどうかをチェック
        if (is_dir($filePath)) {
            // ディレクトリの場合
            echo "{$indent}📁 {$file}<br>";
            // 再帰的にサブディレクトリを表示
            displayDirectoryTree($filePath, $level + 1);
        } else {
            // ファイルの場合。indentは「&nbsp;&nbsp;&nbsp;&nbsp;」が入る.&nbsp;は半角スペース
            echo "{$indent}📄 {$file}<br>";
        }
    }
}

// 表示するディレクトリのパス
$rootDirectory = 'C:\\xampp\\htdocs\\study';  // ディレクトリのパスを指定

// フォルダ階層の表示を開始
echo "<h2>ディレクトリ構造</h2>";
echo "<h3>ルートディレクトリ: $rootDirectory</h3>";
displayDirectoryTree($rootDirectory);

?>
