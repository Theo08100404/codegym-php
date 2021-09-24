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
    <?php
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
        $statement = $db->prepare('DELETE FROM memos WHERE id=?');
        $statement->execute(array($id));
    }
    ?>
    <p>メモを削除しました</p>
    <p><a href="index.php">戻る</a></p>
</body>

</html>
