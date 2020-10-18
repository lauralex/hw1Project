<?php
require_once "init.php";
/*
 * POPULATE YOUTUBE URLS
 *
$query = "SELECT postUrl, postId from hw1_posts where postUrl like '%ytimg%'";

$queryRes = mysqli_query($conn, $query) or die("Query error");

while ($row = mysqli_fetch_assoc($queryRes)) {
    $ytUrl = $row['postUrl'];
    $yt_id_start = strpos($ytUrl, '/vi/') + 4;
    $yt_id_end = strpos($ytUrl, '/', $yt_id_start);
    $yt_id = substr($ytUrl, $yt_id_start, $yt_id_end - $yt_id_start);

    $query = "UPDATE hw1_posts SET url_yt = 'http://youtube.com/watch?v={$yt_id}' WHERE postId = '{$row['postId']}'";
    $populateExec = mysqli_query($conn, $query);
}
*/

/*
 *
 * POPULATE ANIME URLS
 *
$query = "SELECT postUrl, postId from hw1_posts where postUrl like '%anilist%' and postUrl like '%anime%'";

$queryRes = mysqli_query($conn, $query) or die("Query error");

while ($row = mysqli_fetch_assoc($queryRes)) {
    $aniUrl = $row['postUrl'];
    if (!preg_match('/\d{2,}/', $aniUrl, $matches)) continue;
    $query = "UPDATE hw1_posts SET url_an = 'http://anilist.co/anime/{$matches[0]}' WHERE postId = '{$row['postId']}'";
    $populateExec = mysqli_query($conn, $query);

}*/


/*
 *
 * POPULATE MANGA URLS
 *
$query = "SELECT postUrl, postId from hw1_posts where postUrl like '%anilist%' and postUrl like '%manga%'";

$queryRes = mysqli_query($conn, $query) or die("Query error");

while ($row = mysqli_fetch_assoc($queryRes)) {
    $aniUrl = $row['postUrl'];
    if (!preg_match('/\d{2,}/', $aniUrl, $matches)) continue;
    echo "{$matches[0]}<br>";
    $query = "UPDATE hw1_posts SET url_an = 'http://anilist.co/manga/{$matches[0]}' WHERE postId = '{$row['postId']}'";
    $populateExec = mysqli_query($conn, $query);
}*/

function populateAniList($pid)
{
    global $conn;
    $query = "SELECT postUrl, postId from hw1_posts where postUrl like '%anilist%' and postUrl like '%manga%' and postId = '{$pid}'";
    $queryRes = mysqli_query($conn, $query) or die("Query error");
    while ($row = mysqli_fetch_assoc($queryRes)) {
        $aniUrl = $row['postUrl'];
        if (!preg_match('/\d{2,}/', $aniUrl, $matches)) continue;
        echo "{$matches[0]}<br>";
        $query = "UPDATE hw1_posts SET url_an = 'http://anilist.co/manga/{$matches[0]}' WHERE postId = '{$row['postId']}'";
        $populateExec = mysqli_query($conn, $query);
    }
    $query = "SELECT postUrl, postId from hw1_posts where postUrl like '%anilist%' and postUrl like '%anime%' and postId = '{$pid}'";
    $queryRes = mysqli_query($conn, $query) or die("Query error");
    while ($row = mysqli_fetch_assoc($queryRes)) {
        $aniUrl = $row['postUrl'];
        if (!preg_match('/\d{2,}/', $aniUrl, $matches)) continue;
        $query = "UPDATE hw1_posts SET url_an = 'http://anilist.co/anime/{$matches[0]}' WHERE postId = '{$row['postId']}'";
        $populateExec = mysqli_query($conn, $query);
    }
}
