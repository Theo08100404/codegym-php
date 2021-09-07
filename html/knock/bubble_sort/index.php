<?php
$nums = [100, 5, 2, -10, 8, 10, -1, 15, 1, -100];

$idxLast = count($nums) - 1;

for ($i = 0; $i < $idxLast; $i++) {
    for ($j = $idxLast; $i < $j; $j--) {
        if ($nums[$j] < $nums[$j - 1]) {
            $temp = $nums[$j - 1];
            $nums[$j - 1] = $nums[$j];
            $nums[$j] = $temp;
        }
    }
}

echo '<pre>';
print_r($nums);
echo '</pre>';
