<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width , initial-arcle=1 ,shrink-to-fit=no">
    <link rel="stylesheet" href="css/style.css">
    <title>よくわかるPHPの教科書</title>
</head>


<body>
    <?php
    $email = 'master@h2o-space.com';
    mb_language('japanese');
    mb_internal_encoding('utf-8');

    $from = 'noreplay@example.com';
    $subject = 'よくわかるPHPの教科書';
    $body = 'このメールは、『よくわかるPHPの教科書から送っています』';

    $success = mb_send_mail($email, $subject, $body, 'From:' . $from);
    ?>
    <pre>
        <?php if ($success) {
            print '電子メールを送信しました。メールボックスを確認してみてください。';
        } else {
            print '電子メールの送信に失敗しました。Webサーバーの設定などをご確認ください。';
        }

        ?>
    </pre>




</body>

</html>
