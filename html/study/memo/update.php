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
    <h2>Practice</h2>
    <?php
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
        $memos = $db->prepare('SELECT * FROM memos WHERE id=?');
        $memos->execute(array($id));
        $memo = $memos->fetch();
    }
    ?>
    <form action="update_do.php" method="POST">
        <input type="hidden" name="id" value="<?php print($id); ?>">
        <textarea name="memo" cols="50" rows="10"><?php print($memo['memo']); ?></textarea><br>
        <button type="submit">登録する</button>

    </form>
</main>


</html>
