<?php
require __DIR__.'/database.php';
require __DIR__.'/_nav.php';

$uid = (int)($_GET['user_id'] ?? 0);
$cid = (int)($_GET['channel_id'] ?? 0);

if (!$uid || !$cid) die("Missing params");

$c = db_connect();

if (isset($_GET['action'])) {
    if ($_GET['action']==='sub') {

        $c->query("INSERT IGNORE INTO subscriptions VALUES($uid,$cid,NOW())");

    }
    if ($_GET['action']==='unsub') {

        $c->query("DELETE FROM subscriptions WHERE subscriber_id=$uid AND channel_id=$cid");

    }
    
    header("Location: channel.php?user_id=$uid&channel_id=$cid");
    exit;
}

$ch = $c->query("
SELECT ch.*,
u.full_name,
u.country,
(SELECT COUNT(*) FROM subscriptions s WHERE s.channel_id=ch.channel_id) subs
FROM channels ch
JOIN users u ON u.user_id=ch.owner_id
WHERE ch.channel_id=$cid
")->fetch_assoc();

if (!$ch) die("Channel not found");

$isSub = $c->query("
SELECT 1 FROM subscriptions
WHERE subscriber_id=$uid AND channel_id=$cid
")->num_rows > 0;

$vids = $c->query("
SELECT * FROM videos
WHERE channel_id=$cid
ORDER BY uploaded_at DESC
");
?>

<!doctype html>
<html>
<head>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php render_nav($uid); ?>

<div class="container">

<div class="card">

<img class="avatar avatar-lg" src="<?= h($ch['channel_image']) ?>">

<h1><?= h($ch['name']) ?></h1>

<div class="small">
<?= h($ch['full_name']) ?> · <?= h($ch['country']) ?>
· <?= $ch['subs'] ?> subs
</div>

<p><?= trim($ch['description']) ?: '(no description)' ?></p>

<a class="subscribe-btn <?= $isSub ? 'unsubscribe' : 'subscribe' ?>"
   href="channel.php?user_id=<?= $uid ?>&channel_id=<?= $cid ?>&action=<?= $isSub ? 'unsub' : 'sub' ?>">

   <?= $isSub ? '✓ Subscribed' : '+ Subscribe' ?>

</a>

</div>

<div class="card">
<h2>Videos</h2>

<?php while($v=$vids->fetch_assoc()): ?>
<a href="watch.php?user_id=<?= $uid ?>&video_id=<?= $v['video_id'] ?>">
<?= h($v['title']) ?>
</a><br>
<?php endwhile; ?>

</div>

</div>

</body>
</html>