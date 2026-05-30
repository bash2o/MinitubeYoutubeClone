<?php

function render_nav($uid){
    $u = (int)$uid;
    echo '<div class="navbar"><div class="logo"><span>▶</span> MiniTube</div>';
    echo '<div class="nav-links">';
    echo '<a href="feed.php?user_id='.$u.'">Feed</a>';
    echo '<a href="sql.php?user_id='.$u.'">SQL Console</a>';
    echo '<a href="login.html">Logout</a>';
    echo '</div></div>';
}
