<?php
require __DIR__.'/database.php';
require __DIR__.'/_nav.php';

$uid = (int)($_GET['user_id'] ?? 0);
if (!$uid) { header('Location: login.html'); exit; }

$c = db_connect();

$query = $_POST['query'] ?? '';
?>

<!doctype html><html><head><meta charset="utf-8"><title>SQL Console — MiniTube</title>

<link rel="stylesheet" href="css/style.css"></head><body>

<?php render_nav($uid); ?>

<div class="container">
  <h1 class="page-title">SQL Console</h1>
  <div class="card">
    <form method="post">
      <textarea name="query" rows="6" placeholder="SELECT * FROM users LIMIT 5;"><?=h($query)?></textarea>
      <button class="btn btn-primary" style="margin-top:8px">Execute</button>
    </form>
  </div>

<?php if ($query !== ''): ?>
  
  <div class="card">
    <h2 class="section">Executed query</h2>
    <pre style="background:#0f0f0f;color:#0f0;padding:12px;border-radius:8px;overflow:auto"><?=h($query)?></pre>
    <div class="sql-result">

<?php

if (!$query) {
    die("Empty query");
}

$res = $c->query($query);

if ($res === false) {
    echo '<div class="error-msg">SQL error: '.h($c->error).'</div>';
} elseif ($res === true) {
    echo '<div class="success-msg">OK. Affected rows: '.$c->affected_rows.'</div>';
} else {
    $count = 0;
    echo '<table><thead><tr>';
    $fields = $res->fetch_fields();

    foreach ($fields as $f) echo '<th>'.h($f->name).'</th>';
    echo '</tr></thead><tbody>';

    while ($row = $res->fetch_assoc()) {
        if ($count++ >= 10) break;
        echo '<tr>';
        foreach ($row as $val) echo '<td>'.h((string)$val).'</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<p class="small" style="margin-top:8px">Showing up to 10 rows. Total returned: '.$res->num_rows.'</p>';

}

?>

    </div>
  </div>
  
<?php endif; ?>
</div></body></html>
