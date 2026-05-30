<?php
session_start();
require __DIR__ . '/database.php';

$c = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    
    header("Location: login.html");
    exit;
}

$u = $_POST['username'] ?? '';
$p = $_POST['password'] ?? '';

if (!$u || !$p) {
    die("Missing fields");
}

$stmt = $c->prepare("
    SELECT user_id, full_name
    FROM users
    WHERE username = ? AND password = ?
    LIMIT 1
");

$stmt->bind_param("ss", $u, $p);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Checking log ins
if (!$user) {

    echo '
    <link rel="stylesheet" href="css/style.css">
    <div class="center-box">
        <h1>Login failed</h1>
        <p>Invalid username or password.</p>
        <a class="btn btn-secondary" href="login.html">Back</a>
    </div>';

    exit;

}

// Sessions
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['full_name'] = $user['full_name'];

header("Location: feed.php");
exit;

?>