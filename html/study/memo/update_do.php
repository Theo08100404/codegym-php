<?php require('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width , initial-arcle=1 ,shrink-to-fit=no">
    <link rel="stylesheet" href="css/style.css">
    <title>よくわかるPHPの教科書</title>
</head>
<main>
    <?php
    require('dbconnect.php');

    $statement = $db->prepare('UPDATE memos SET memo=? WHERE id=?');
    $statement->execute(array($_POST['memo'], $_POST['id']));
    ?>
    <p>メモの内容を変更しました</p>
    <p><a href="index.php"></a></p>
</main>
