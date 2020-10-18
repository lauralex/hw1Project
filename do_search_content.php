<?php
require_once "init.php";
header("Content-Type: application/json; charset=UTF-8");
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "bellia_alessandro", "3306");
if (isset($_SESSION["UID"])) {
    $escapedPassword = mysqli_escape_string($conn, $_SESSION['userid']);
    $escapedUser = mysqli_escape_string($conn, $_SESSION['username']);
    $query = "SELECT username from hw1_users where password = '{$escapedPassword}' and username = '{$escapedUser}'";
    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        if (mysqli_num_rows($queryRes) == 0) {
            session_unset();
            session_destroy();
            mysqli_free_result($queryRes);
            mysqli_close($conn);
            header("Location: {$uri}/../login.php");
            exit();
        }
        mysqli_free_result($queryRes);
    } else {
        mysqli_close($conn);
        header("Location: {$uri}/../login.php");
        exit();
    }

} else {
    mysqli_close($conn);
    header("Location: {$uri}/../login.php");
    exit();
}

if (!isset($_POST["service_select"]) || ($_POST["service_select"] != "AniList" && $_POST["service_select"] != "GoogleBooks")) {
    echo "321";
    exit();
}
if (isset($_POST["search_query"]) && empty(trim( $_POST["search_query"]))) {
    echo "321";
    exit();
}
function AniList_proc($search_query)
{
    $query = "query {
        Page(page: 1, perPage: 10) {
            media(search: \"$search_query\") {
                title {
                    romaji
                }
                coverImage {
                    extraLarge
                }
            }
        }
    }";

    $variables = array();

    $post = array(
        'query' => $query,
        'variables' => $variables
    );

    $curl_handle = curl_init("https://graphql.anilist.co");
    curl_setopt($curl_handle, CURLOPT_POST, true);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query(
        $post
    ));

    $res = curl_exec($curl_handle);
    curl_close($curl_handle);

    echo $res;
}

function YoutubeSearch_proc($search_query) {
    $curl_handler = curl_init();
    $encodedSearch = curl_escape($curl_handler, $search_query);
    $yt_url_search = "https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=12&q={$encodedSearch}&type=video&key=KEY";
    curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handler, CURLOPT_URL, $yt_url_search);
    $res = curl_exec($curl_handler);
    curl_close($curl_handler);

    echo $res;
}


$service = $_POST["service_select"];

if ($service == "AniList") {

    $search_query = trim($_POST["search_query"]);
    AniList_proc($search_query);
} else {
    $search_query = trim($_POST["search_query"]);
    YoutubeSearch_proc($search_query);
}