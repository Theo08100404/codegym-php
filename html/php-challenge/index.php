<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    // ログインしている
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    // ログインしていない
    header('Location: login.php');
    exit();
}

// 投稿を記録する
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ));

        header('Location: index.php');
        exit();
    }
}
// 投稿を取得する
$page = $_REQUEST['page'];
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

// 最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// 返信の場合
if (isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));

    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];
}

// htmlspecialcharsのショートカット
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// 本文内のURLにリンクを設定します
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}

?>
<?php
//いいね済みかどうか確認
if (isset($_REQUEST['like'])) {
    $pressd = $db->prepare('SELECT COUNT(*) AS cnt FROM favorites WHERE post_id=? AND member_id=?');
    $pressd->execute(array(
        $_REQUEST['like'],
        $_SESSION['id']
    ));
    $like_cnt = $pressd->fetch();
    //いいねがなければDBに追加
    if ($like_cnt['cnt'] < 1) {
        $press = $db->prepare('INSERT INTO favorites SET post_id=? , member_id=? , created=NOW()');
        $press->execute(array(
            $_REQUEST['like'],
            $_SESSION['id'],

        ));
        header('Location: index.php');
        exit();
    }
    //いいねがあればDBから削除
    else {
        $change = $db->prepare('DELETE FROM favorites WHERE post_id=? AND member_id=? ');
        $change->execute(array(
            $_REQUEST['like'],
            $_SESSION['id']
        ));
        header('Location: index.php');
        exit();
    }
}
?>
<?php
//リツイートされてるかどうか確認
if (isset($_REQUEST['retweet'])) {
    $pushed = $db->prepare('SELECT COUNT(*) AS cnt FROM posts WHERE member_id=? AND retweet_post_id=? ');
    $pushed->execute(array(
        $_SESSION['id'],
        $_REQUEST['retweet']
    ));
    $retweet_cnt = $pushed->fetch();


    //リツイートされてなければ追加
    if ($retweet_cnt['cnt'] < 1) {
        $push = $db->prepare('INSERT INTO posts SET member_id=? , retweet_post_id=? ,created=NOW()');
        $push->execute(array(
            $_SESSION['id'],
            $_REQUEST['retweet']

        ));
        header('Location: index.php');
        exit();
    }
    //リツイートされていれば削除
    else {
        $cansel_ret = $db->prepare('DELETE FROM posts WHERE member_id=? AND retweet_post_id=? ');
        $cansel_ret->execute(array(
            $_SESSION['id'],
            $_REQUEST['retweet']

        ));
        header('Location: index.php');
        exit();
    }
}
//元ポストの情報を取得

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
            <form action="" method="post">
                <dl>
                    <dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="message" cols="50" rows="5"><?php echo h($message); ?></textarea>
                        <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>" />
                    </dd>
                </dl>
                <div>
                    <p>
                        <input type="submit" value="投稿する" />
                    </p>
                </div>
            </form>

            <?php
            foreach ($posts as $post) :
                //いいねの数取得
                $like_number = $db->prepare('SELECT COUNT(*) as cnt from favorites WHERE post_id=? ');
                $like_number->execute(array(
                    $post['id']
                ));
                $total_like = $like_number->fetch();

                //それぞれの投稿に対していいねしてるかしてないか確認
                $pressd = $db->prepare('SELECT COUNT(*) AS cnt FROM favorites WHERE post_id=? AND member_id=?');
                $pressd->execute(array(
                    $post['id'],
                    $_SESSION['id']
                ));
                $like_cnt = $pressd->fetch();


                //リツイートの数確認
                $retweet_number = $db->prepare('SELECT COUNT(*) as cnt FROM posts WHERE retweet_post_id=? ');
                $retweet_number->execute(array(
                    $post['id']
                ));
                $total_retweet = $retweet_number->fetch();

                //それぞれの投稿に対してリツイートしてるかしてないか確認
                $pushed = $db->prepare('SELECT COUNT(*) AS cnt FROM posts WHERE retweet_post_id=? AND member_id=?');
                $pushed->execute(array(
                    $post['id'],
                    $_SESSION['id']

                ));
                $retweet_cnt = $pushed->fetch();

                //リツイートと投稿のリツイート数取得
                $retweeted_number = $db->prepare('SELECT COUNT(*) AS cnt FROM posts WHERE retweet_post_id=? ');
                $retweeted_number->execute(array(
                    $post['retweet_post_id']
                ));
                $retweeted_cnt = $retweeted_number->fetch();

                //リツイート投稿に対して自分がリツイートしているか確認
                $MY_pushed = $db->prepare('SELECT COUNT(*) AS cnt FROM posts WHERE retweet_post_id=? AND member_id=?');
                $MY_pushed->execute(array(
                    $post['id'],
                    $_SESSION['id']

                ));
                $retweeted = $MY_pushed->fetch();

            ?>
                <?php
                //リツイート元の情報取得
                $retweet_message = $db->prepare('SELECT message as msg FROM posts WHERE id=?');
                $retweet_message->execute(array(
                    $post['retweet_post_id']
                ));
                $retweet_msg = $retweet_message->fetch();

                $tweeted_id = $db->prepare('SELECT member_id as id FROM posts WHERE id=?');
                $tweeted_id->execute(array(
                    $post['retweet_post_id']
                ));
                $Tweeted_id = $tweeted_id->fetch();

                $tweeted_name = $db->prepare('SELECT name as namae FROM members WHERE id=?');
                $tweeted_name->execute(array(
                    $Tweeted_id['id']
                ));
                $Tweeted_name = $tweeted_name->fetch();

                $tweeted_pic = $db->prepare('SELECT picture as pic FROM members WHERE id=?');
                $tweeted_pic->execute(array(
                    $Tweeted_id['id']
                ));
                $Tweeted_pic = $tweeted_pic->fetch();

                ?>

                <div class="msg">
                    <span><?php if ((int)($post['retweet_post_id']) > 0) : ?></span>
                    <span><?php echo h($post['name']) . 'さんがリツイートしました。'; ?></span>
                    <img src="member_picture/<?php echo h($Tweeted_pic['pic']); ?>" width="48" height="48" alt="<?php echo h($Tweeted_name['namae']); ?>" />
                    <p><?php echo makeLink(h($retweet_msg['msg'])); ?><span class="name">（<?php echo h($Tweeted_name['namae']); ?>）</span>[<a href="index.php?res=<?php echo h($Tweeted_id['id']); ?>">Re</a>]</p>
                <?php else : ?>
                    <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>" />
                    <p><?php echo makeLink(h($post['message'])); ?><span class="name">（<?php echo h($post['name']); ?>）</span>[<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]</p>
                <?php endif; ?>

                <p class="day">
                    <!-- 課題：リツイートといいね機能の実装 -->

                    <span class="retweet">
                        <?php if ((((int)($post['retweet_post_id']) > 0)) && $retweeted_cnt['cnt'] >= 1) : ?>
                            <a href="index.php?retweet=<?php echo h($post['id']); ?>">
                                <img class="retweet-image" src="images/retweet-solid-blue.svg">
                            <?php elseif ($retweet_cnt['cnt'] < 1) : ?>
                                <a href="index.php?retweet=<?php echo h($post['id']); ?>">
                                    <img class="retweet-image" src="images/retweet-solid-gray.svg"> </a>
                            <?php else : ?>
                                <a href="index.php?retweet=<?php echo h($post['id']); ?>">
                                    <img class="retweet-image" src="images/retweet-solid-blue.svg">

                                <?php endif; ?>
                                </a>
                                <?php if (((int)($post['retweet_post_id']) > 0)) : ?>
                                    <span><?php echo h($retweeted_cnt['cnt']); ?></span>
                                <?php else : ?>
                                    <span><?php echo h($total_retweet['cnt']); ?></span>
                                <?php endif; ?>


                    </span>

                    <span class="favorite">

                        <?php if ($like_cnt['cnt'] < 1) : ?>
                            <a href="index.php?like=<?php echo h($post['id']); ?>">
                                <img class="favorite-image" src="images/heart-solid-gray.svg"></a>
                        <?php else : ?>
                            <a href="index.php?like=<?php echo h($post['id']); ?>">
                                <img class="favorite-image" src="images/heart-solid-red.svg">
                            <?php endif; ?>
                            </a>

                            <span><?php echo h($total_like['cnt']); ?></span>
                    </span>

                    <a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
                    <?php
                    if ($post['reply_post_id'] > 0) :
                    ?><a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">
                            返信元のメッセージ</a>
                    <?php
                    endif;
                    ?>
                    <?php
                    if ($_SESSION['id'] == $post['member_id']) :
                    ?>
                        [<a href="delete.php?id=<?php echo h($post['id']); ?>" style="color: #F33;">削除</a>]
                    <?php
                    endif;
                    ?>
                </p>
                </div>
            <?php
            endforeach;
            ?>

            <ul class="paging">
                <?php
                if ($page > 1) {
                ?>
                    <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
                <?php
                } else {
                ?>
                    <li>前のページへ</li>
                <?php
                }
                ?>
                <?php
                if ($page < $maxPage) {
                ?>
                    <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
                <?php
                } else {
                ?>
                    <li>次のページへ</li>
                <?php
                }
                ?>
            </ul>
        </div>

    </div>
</body>

</html>
