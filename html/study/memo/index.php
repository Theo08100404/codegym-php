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
    if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
        $page = $_REQUEST['page'];
    } else {
        $page = 1;
    }
    $start = 5 * ($page - 1);
    $memos = $db->prepare('SELECT * FROM memos ORDER BY id LIMIT ? , 5');
    $memos->bindParam(1, $start, PDO::PARAM_INT);
    $memos->execute();
    ?>

    <article>
        <?php while ($memo = $memos->fetch()) : ?>
            <p>
                <a href="memo.php?id=<?php print($memo['id']); ?>">
                    <?php print(mb_substr($memo['memo'], 0, 50)); ?>
                    <?php print((mb_strlen($memo['memo']) > 50 ? '...' : '')); ?>
                </a>
            </p>
            <time><?php print($memo['created_at']); ?></time>
            <hr>
        <?php endwhile; ?>
        <?php if ($page >= 2) : ?>
            <a href="index.php?page=<?php print($page - 1); ?>"><?php print($page - 1); ?>ページへ</a>
        <?php endif; ?>
        |
        <?php
        $counts = $db->query('SELECT COUNT(*) as cnt FROM memos');
        $count = $counts->fetch();
        $max_page = floor($count['cnt'] / 5) + 1;
        if ($page < $max_page) : ?>
            <a href="index.php?page=<?php print($page + 1); ?>"><?php print($page + 1); ?>ページへ</a>
        <?php endif; ?>
    </article>



</body>

</html>
