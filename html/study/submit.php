<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width , initial-arcle=1 ,shrink-to-fit=no">
    <link rel="stylesheet" href="css/style.css">
    <title>よくわかるPHPの教科書</title>
</head>

<body>
    <header>
        <h1 class="font-weight-normal">フォームの受信</h1>
    </header>
    <main>
        <h2>Practice</h2>
        <pre>
            お名前：<?php print(htmlspecialchars($_REQUEST['comment'], ENT_QUOTES));




                ?>
            性別：<?php print(htmlspecialchars($_POST['gender'], ENT_QUOTES));
                ?>
            
            ご予約日:
            <?php
            foreach ($_POST['reserve'] as $reserve) {
                print(htmlspecialchars($reserve, ENT_QUOTES) . ' ');
            }
            ?>
            <?php
            $zip = '987-6543';
            $zip = mb_convert_kana($zip, 'a', 'utf-8');
            if (preg_match("/\A\d{3}[-]\d{4}\z/", $zip)) {
                print('郵便番号: 〒' . $zip);
            } else {
                print('※郵便番号を　　123-4567の形式でご記入下さい');
            }
            ?>
            変数の値：<?php print($value); ?>
            Cookieの値: <?php print($_COOKIE['sanve_message']); ?>



        </pre>
    </main>
</body>

</html>
