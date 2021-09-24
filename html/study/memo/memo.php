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
    <main>
        <?php
        $id = $_REQUEST['id'];
        $memos = $db->prepare('SELECT * FROM memos WHERE id=?');
        $memos->execute(array($_REQUEST['id']));
        $memo = $memos->fetch();
        ?>
        <article>
            <pre><?php print($memo['memo']); ?></pre>
            <a href="update.php?id=<?php print($memo['id']); ?>">編集する</a>
            |
            <a href="delete.php?id=<?php print($memo['id']); ?>">削除する</a>
            |
            <a href="index.php">戻る</a>
        </article>

    </main>
</body>

</html>
