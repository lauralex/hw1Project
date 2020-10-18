<?php
require_once "init.php";
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

if (isset($_GET['postList'])) {
    $query = "SELECT postUrl, postTitle, date, postId, likes, url_yt, url_an, username from hw1_posts join hw1_users on hw1_posts.postUser = hw1_users.ID
        where username = '{$_SESSION['username']}'
           OR postUser in (select followed from hw1_follow join hw1_users on followerId = ID where username = '{$_SESSION['username']}') ORDER BY date DESC";// AGGIUNGERE LA CONDIZIONE OR PER GLI ALTRI UTENTI

    $queryRes = mysqli_query($conn, $query);
    $arrayOfInfo = array();

    if (!$queryRes) {
        mysqli_close($conn);
        http_response_code(404);
        exit();
    }

    while ($row = mysqli_fetch_assoc($queryRes)) {
        $query = "SELECT postId from hw1_postlike join hw1_users on userId = ID where username = '{$_SESSION['username']}' and postId = {$row['postId']}";

        $likeRes = mysqli_query($conn, $query);


        array_push($arrayOfInfo, array('postId' => $row['postId'], 'postTitle' => $row['postTitle'], 'postUrl' => $row['postUrl'], 'date' => $row['date'], 'like' => $likeRes && mysqli_num_rows($likeRes) > 0, 'num_likes' => $row['likes'], 'yt_url' => $row['url_yt'], 'an_url' => $row['url_an'], 'username'=>$row['username']));
        mysqli_free_result($likeRes);
    }
    mysqli_free_result($queryRes);
    mysqli_close($conn);
    echo json_encode($arrayOfInfo);
    exit();
}

if (isset($_GET['likers'])) {
    $postLiked = mysqli_escape_string($conn, $_GET['likers']);

    $query = "SELECT username, image FROM hw1_postlike JOIN hw1_users ON userId = ID WHERE postId = '{$postLiked}'";

    $queryRes = mysqli_query($conn, $query) or die(mysqli_connect_error());
    $likersArr = array();
    while ($row = mysqli_fetch_assoc($queryRes)) {
        $likersArr[] = array('username'=>$row['username'], 'image'=>$row['image']);
    }
    echo json_encode($likersArr);
    exit();
}

if (isset($_GET['resolveLike']) && !empty($_GET['resolveLike'])) {
    $escapedPostId = mysqli_escape_string($conn, $_GET['resolveLike']);


    $query = "SELECT postId FROM hw1_postlike JOIN hw1_users ON userId = ID WHERE username = '{$_SESSION['username']}' AND postId = '{$escapedPostId}'";

    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        if (mysqli_num_rows($queryRes) > 0) {
            mysqli_free_result($queryRes);
            $query = "DELETE FROM hw1_postlike WHERE postId = '{$escapedPostId}' AND userId IN (SELECT ID FROM hw1_users WHERE username = '{$_SESSION['username']}')";
            $queryRes = mysqli_query($conn, $query);

            echo 'Like';

        } else {
            mysqli_free_result($queryRes);
            $query = "INSERT INTO hw1_postlike (userId, postId) VALUES ({$_SESSION['UID']}, '{$escapedPostId}')";
            $queryRes = mysqli_query($conn, $query);

            echo 'Unlike';
        }
    } else {
        echo 'Like';
    }
    mysqli_close($conn);
    exit();
}

function console_log($data)
{
    echo '<script>';
    echo 'console.log(' . json_encode($data) . ')';
    echo '</script>';
}


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta content="Home page" name="description">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <title>Home</title>
    <link rel="icon" href="favicon.png" sizes="64x64" type="image/png">
    <!--    <link href="general.css" rel="stylesheet" type="text/css">-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script defer src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
            crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="home.css">
    <script defer src="home.js"></script>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between mt-3 mb-3 align-items-baseline">
        <h1 class="mb-0">Welcome</h1>
        <h6 class="font-weight-normal mb-0">Alessandro Bellia</h6>
    </div>

    <p><?php console_log($_SESSION['UID']); ?></p>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="./home.php"><img src="favicon_red.png" width="32" height="32" alt="Pc di legno"
                                                       class="d-inline-block mr-1">MyBells</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#myNav" aria-controls="myNav"
                aria-expanded="false" aria-label="Toggle nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="myNav">
            <ul class="mr-auto navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="<?php echo "{$uri}/../home.php" ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo "{$uri}/../create_post.php" ?>">Create Post</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo "{$uri}/../search_people.php" ?>">Search Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo "{$uri}/../logout.php" ?>">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 d-none">
    </div>

    <div class="modal fade" id="post_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="post_modal_label">List of likers</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>

