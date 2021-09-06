<?php
for ($i = 1; $i <= 100; $i++) {
    if ($i <= 1) {
        // 1以下は素数ではない
        continue;
    }
    if ($i <= 3) {
        // 2と3は素数である
        echo $i . '<br>';
        continue;
    }

    //ここにコードを書きましょう。
}
