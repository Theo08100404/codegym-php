<?php require('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width , initial-arcle=1 ,shrink-to-fit=no">
    <link rel="stylesheet" href="css/style.css">
    <title>よくわかるPHPの教科書</title>
</head>

<body>
    <a href="input.html">別の回答を送る</a>
    <pre>
<?php
$statement = $db->prepare('INSERT INTO memos SET memo=? , created_at=NOW()');
$statement->execute(array($_POST['memo']));
echo 'メッセージが登録されました';


?>
    </pre>
</body>
