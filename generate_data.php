<?php

function load_lines($f){
    if (!file_exists($f)) {
        die("Missing seed file: $f");
    }

    $lines = file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        die("Cannot read file: $f");
    }

    return array_values(array_filter(array_map('trim', $lines)));
}

// Loading datas
$base = __DIR__.'/seed';
$firsts = load_lines("$base/first_names.txt");
$lasts  = load_lines("$base/last_names.txt");
$countries = load_lines("$base/countries.txt");
$categories = load_lines("$base/categories.txt");
$titles = load_lines("$base/video_titles.txt");
$ytids  = load_lines("$base/youtube_ids.txt");

mt_srand(42);

function esc($s){ return str_replace("'", "''", $s); }

function rand_date($start, $end){
    $s=strtotime($start); $e=strtotime($end);
    return date('Y-m-d', mt_rand($s,$e));
}

function rand_dt($start,$end){
    $s=strtotime($start); $e=strtotime($end);
    return date('Y-m-d H:i:s', mt_rand($s,$e));
}

$out = "-- Auto-generated seed data for MiniTube\n";
$out .= "SET FOREIGN_KEY_CHECKS=0;\n";

// Users
$N_USERS = 120;
$usernames = [];
$out .= "INSERT INTO users(user_id,username,password,user_image,full_name,email,country,joined_on,bio) VALUES\n";
$rows = [];

for ($i=1;$i<=$N_USERS;$i++){

    $fn = $firsts[array_rand($firsts)];
    $ln = $lasts[array_rand($lasts)];
    $base_u = strtolower($fn.'.'.$ln);
    $u = $base_u; $k=1;

    while (in_array($u,$usernames)) { $u = $base_u.$k; $k++; }

    $usernames[] = $u;
    $full = "$fn $ln";
    $email = $u.'@minitube.test';
    $img = "https://i.pravatar.cc/150?img=".(($i%70)+1);
    $country = $countries[array_rand($countries)];
    $joined = rand_date('2020-01-01','2024-12-31');
    $bio = "Hi, I'm $fn. Welcome to my MiniTube profile!";
    $rows[] = sprintf("(%d,'%s','pass123','%s','%s','%s','%s','%s','%s')",
        $i, esc($u), esc($img), esc($full), esc($email), esc($country), $joined, esc($bio));

}
$out .= implode(",\n",$rows).";\n\n";


// 60 channels for each of the different users
$N_CH = 60;
$owners = range(1,$N_USERS);
shuffle($owners);
$owners = array_slice($owners,0,$N_CH);
$out .= "INSERT INTO channels(channel_id,owner_id,channel_image,name,description,created_on,category) VALUES\n";
$rows=[];

for ($i=1;$i<=$N_CH;$i++){

    $oid = $owners[$i-1];
    $name = ucfirst(strtok($usernames[$oid-1],'.'))." TV";
    $img = "https://picsum.photos/seed/ch$i/200";
    $desc = (mt_rand(1,5)===1)
        ? "No description provided"
        : "Welcome to $name. Subscribe for weekly content!";
    $cat = $categories[array_rand($categories)];
    $created = rand_date('2020-06-01','2025-01-01');
    $rows[] = sprintf("(%d,%d,'%s','%s','%s','%s','%s')",
        $i,$oid,esc($img),esc($name),esc($desc),$created,esc($cat));

}
$out .= implode(",\n",$rows).";\n\n";

// Videos
$N_V = 220;
$out .= "INSERT INTO videos(video_id,channel_id,title,description,url,duration_seconds,uploaded_at,view_count,like_count) VALUES\n";
$rows=[];

for ($i=1;$i<=$N_V;$i++){

    $ch = mt_rand(1,$N_CH);
    $t = $titles[array_rand($titles)] . " #".$i;
    $desc = "Video description for: $t";
    $yt = $ytids[array_rand($ytids)];
    $url = "https://www.youtube.com/watch?v=$yt";
    $dur = mt_rand(30, 1800);
    $up = rand_dt('2023-01-01','2025-06-01');
    $views = mt_rand(0,5000);
    $likes = mt_rand(0,(int)($views/2)+1);
    $rows[] = sprintf("(%d,%d,'%s','%s','%s',%d,'%s',%d,%d)",
        $i,$ch,esc($t),esc($desc),esc($url),$dur,$up,$views,$likes);

}
$out .= implode(",\n",$rows).";\n\n";

// Subscriptions
$N_SUB = 150;
$pairs = [];

while (count($pairs) < $N_SUB) {
    $s = mt_rand(1,$N_USERS); $c = mt_rand(1,$N_CH);
    $key = "$s-$c";
    if (isset($pairs[$key])) continue;
    $pairs[$key] = [$s,$c, rand_dt('2023-01-01','2025-06-01')];
}
$out .= "INSERT INTO subscriptions(subscriber_id,channel_id,subscribed_at) VALUES\n";

$rows=[];

foreach ($pairs as $p) {

    $rows[] = sprintf("(%d,%d,'%s')",$p[0],$p[1],$p[2]);

}
$out .= implode(",\n",$rows).";\n\n";

// Comments
$N_TOP = 160; $N_REP = 30;
$out .= "INSERT INTO comments(comment_id,video_id,user_id,parent_comment_id,body,posted_at) VALUES\n";
$rows=[];
$cid = 1;
$sample = ["Great video!","Loved this.","First!","Where did you film this?","Subscribed.","Hilarious","Really informative.","This made my day.","Can you do a part 2?","Underrated channel."];

for ($i=0;$i<$N_TOP;$i++){

    $vid = mt_rand(1,$N_V);
    $uid = mt_rand(1,$N_USERS);
    $body = $sample[array_rand($sample)];
    $when = rand_dt('2024-01-01','2025-06-01');
    $rows[] = sprintf("(%d,%d,%d,NULL,'%s','%s')",$cid,$vid,$uid,esc($body),$when);
    $cid++;

}
for ($i=0;$i<$N_REP;$i++){

    $parent = mt_rand(1,$N_TOP);

    if (!isset($rows[$parent-1])) {
        continue;
    }

    preg_match('/^\((\d+),(\d+),/', $rows[$parent-1], $m);

    if (!isset($m[2])) {
        continue;
    }

    $vid = (int)$m[2];

    $uid = mt_rand(1,$N_USERS);
    $body = "Reply: ".$sample[array_rand($sample)];
    $when = rand_dt('2024-02-01','2025-06-15');
    $rows[] = sprintf("(%d,%d,%d,%d,'%s','%s')",$cid,$vid,$uid,$parent,esc($body),$when);
    $cid++;
    
}
$out .= implode(",\n",$rows).";\n\n";
$out .= "SET FOREIGN_KEY_CHECKS=1;\n";

$result = file_put_contents(__DIR__.'/seed.sql', $out);

if ($result === false) {
    die("Cannot write seed.sql file");
}