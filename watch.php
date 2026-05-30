<?php
session_start();

require __DIR__.'/database.php';
require __DIR__.'/_nav.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$uid = (int)$_SESSION['user_id'];
$vid = (int)($_GET['video_id'] ?? 0);

if (!$vid) die('Missing video id');

$c = db_connect();

// Inserting comments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['body'])) {

    $body = $_POST['body'];

    $stmt = $c->prepare("
        INSERT INTO comments(video_id,user_id,parent_comment_id,body,posted_at)
        VALUES (?,?,NULL,?,NOW())
    ");
    $stmt->bind_param("iis", $vid, $uid, $body);
    $stmt->execute();

    header("Location: watch.php?user_id=$uid&video_id=$vid");
    exit;

}

// Viewcount
$c->query("UPDATE videos SET view_count=view_count+1 WHERE video_id=$vid");

// Video Query
$v = $c->query("
SELECT v.*, 
       ch.name AS channel_name, 
       ch.channel_id,
       u.country AS uploader_country,

CASE
 WHEN v.view_count >= 1000 THEN 'Popular'
 WHEN v.view_count >= 100 THEN 'Trending'
 ELSE 'New'
END AS popularity

FROM videos v
JOIN channels ch ON ch.channel_id = v.channel_id
JOIN users u ON u.user_id = ch.owner_id
WHERE v.video_id = $vid
")->fetch_assoc();

if (!$v) die("Video not found");


$yt = getYouTubeID($v['url']);

$comments = $c->query("
SELECT
 c.comment_id,
 c.parent_comment_id,
 c.body,
 c.posted_at,
 u.full_name,
 u.username
FROM comments c
JOIN users u ON u.user_id=c.user_id
WHERE c.video_id=$vid
ORDER BY c.parent_comment_id IS NULL DESC, c.posted_at DESC
");
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= h($v['title']) ?> — MiniTube</title>
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php render_nav($uid); ?>

<div class="container">

<div class="card">

<?php if ($yt): ?>
<div class="embed-wrap">
    <iframe
        src="https://www.youtube.com/embed/<?= h($yt) ?>"
        allowfullscreen>
    </iframe>
</div>
<?php else: ?>
<p><b>Video embed failed (invalid URL)</b></p>
<?php endif; ?>

<h1 style="margin-top:10px">
    <?= h($v['title']) ?>
    <span class="badge"><?= h($v['popularity']) ?></span>
</h1>

<div class="small">
    <a href="channel.php?user_id=<?= $uid ?>&channel_id=<?= $v['channel_id'] ?>">
        <b><?= h($v['channel_name']) ?></b>
    </a>
    · <?= h($v['uploader_country']) ?>
    · <?= $v['view_count'] ?> views
</div>

</div>

<div class="card">
<h2>Add Comment</h2>

<form method="post">
    <textarea name="body" required></textarea>
    <button class="btn btn-primary">Comment</button>
</form>
</div>


<div class="card">
<h2>Comments</h2>

<?php while($cmt = $comments->fetch_assoc()): ?>
<div class="comment">
    <b><?= h($cmt['full_name']) ?></b>
    <div><?= h($cmt['body']) ?></div>
</div>
<?php endwhile; ?>

</div>

</div>

</body>
</html>