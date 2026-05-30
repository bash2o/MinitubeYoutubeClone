<?php
require __DIR__ . '/database.php';

$c = db_connect_server();
if (!$c) die("DB connection failed");

$DB_NAME = 'basak_sakalli';

//Creating Database
if (!$c->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
    die("DB create error: " . $c->error);
}

//Database selection
if (!$c->select_db($DB_NAME)) {
    die("DB select error: " . $c->error);
}

// Creating tables

$schema = "
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255),
    user_image TEXT,
    full_name VARCHAR(100),
    email VARCHAR(100),
    country VARCHAR(50),
    joined_on DATE,
    bio TEXT
);

CREATE TABLE IF NOT EXISTS channels (
    channel_id INT PRIMARY KEY,
    owner_id INT,
    channel_image TEXT,
    name VARCHAR(100),
    description TEXT,
    created_on DATE,
    category VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS videos (
    video_id INT PRIMARY KEY,
    channel_id INT,
    title VARCHAR(255),
    description TEXT,
    url TEXT,
    duration_seconds INT,
    uploaded_at DATETIME,
    view_count INT,
    like_count INT
);

CREATE TABLE IF NOT EXISTS subscriptions (
    subscriber_id INT,
    channel_id INT,
    subscribed_at DATETIME
);

CREATE TABLE IF NOT EXISTS comments (
    comment_id INT PRIMARY KEY,
    video_id INT,
    user_id INT,
    parent_comment_id INT NULL,
    body TEXT,
    posted_at DATETIME
);
";

if (!$c->multi_query($schema)) {
    die("Schema error: " . $c->error);
}

do {
    if ($res = $c->store_result()) {
        $res->free();
    }
} while ($c->next_result());

// Loading the seed data
$sql = file_get_contents(__DIR__ . '/seed.sql');
if (!$sql) die("seed.sql missing or empty");

// Executing the seed part
if ($c->multi_query($sql)) {

    do {
        if ($res = $c->store_result()) {
            $res->free();
        }
    } while ($c->next_result());

} else {

    die("Seed error: " . $c->error);

}

$c->close();

header('Location: login.php');
exit;
?>