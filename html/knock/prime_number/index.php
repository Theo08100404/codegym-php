<?php
for ($i = 1; $i <= 100; $i++) {
    if ($i <= 1) {
        continue;
    }

    if ($i <= 3) {
        // 2と3は素数である
        echo $i . '<br>';
        continue;
    }
}
