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

if (isset($_GET["uri"]) && isset($_GET["title"]) && isset($_SESSION['username'])) {
    if (empty($_GET['title']) || empty($_GET['uri'])) {
        echo "321"; // error invalid parameters OR invalid session
        exit();
    }

    $conn = mys_con();
    $content_title = mysqli_escape_string($conn, trim($_GET["title"]));
    $content_uri = mysqli_escape_string($conn, $_GET["uri"]);

    $user = mysqli_escape_string($conn, $_SESSION['username']);
    $checkUser = "SELECT ID FROM hw1_users where username = '{$user}'";
    $checkUserRes = mysqli_query($conn, $checkUser);
    if ($checkUserRes) {
        if (mysqli_num_rows($checkUserRes) == 0) {
            echo "545"; // error user not found
            exit();
        }
    } else {
        echo "999"; // error invalid query
        exit();
    }
    $userIdRes = mysqli_fetch_assoc($checkUserRes)['ID'];
    $yt_url = null;

    if (!empty($_GET['yt_url'])) {
        $yt_url = mysqli_escape_string($conn, $_GET['yt_url']);
    }


    if (isset($yt_url)) {
        $queryPost = "INSERT INTO hw1_posts (postTitle, postUrl, postUser, date, url_yt) VALUES ('{$content_title}', '{$content_uri}', {$userIdRes}, now(), '{$yt_url}')";
    } else {
        $queryPost = "INSERT INTO hw1_posts (postTitle, postUrl, postUser, date) VALUES ('{$content_title}', '{$content_uri}', {$userIdRes}, now())";
    }
    $queryPostRes = mysqli_query($conn, $queryPost);
    $pid = mysqli_insert_id($conn);
    if (!$queryPostRes) die('878');
    require_once "populate_yt_urls.php";
    populateAniList($pid);
    echo "success";
    exit();
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta content="Crea Post" name="description">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <title>Crea Post</title>
    <link rel="icon" href="favicon.png" sizes="64x64" type="image/png">
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
    <script type="text/javascript">
        const uri = "<?php echo $uri ?>";
    </script>
    <script defer src="create_post.js?ver=16"></script>
</head>
<body>
<div class="container">
    <p><?php echo $_SESSION['username']; ?> This site is not vulnerable only to SQL Injection :)</p>
    <p><?php if (isset($_GET["uri"])) echo $_GET["uri"]; ?></p>
    <h1>Welcome</h1>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-2">
        <a class="navbar-brand" href="./home.php"><img src="favicon_red.png" width="32" height="32" alt="Pc di legno" class="d-inline-block mr-1">MyBells</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#myNav" aria-controls="myNav"
                aria-expanded="false" aria-label="Toggle nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="myNav">
            <ul class="mr-auto navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo "{$uri}/../home.php" ?>">Home</a>
                </li>
                <li class="nav-item active">
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

    <form id="search_form">
        <div class="form-group">
            <label for="service_select">Service</label>
            <select name="service_select" class="form-control" id="service_select" aria-describedby="service_desc">
                <option selected disabled value="">Choose...</option>
                <option value="AniList">AniList</option>
                <option value="GoogleBooks">Youtube Search</option>
            </select>
            <small id="service_desc" class="form-text text-muted">Select a service to use for your post</small>
        </div>
        <div class="form-group">
            <label for="search_query">Search</label>
            <input class="form-control" id="search_query" type="search" name="search_query"
                   aria-describedby="search_desc">
            <small id="search_desc" class="form-text text-muted">Search for something you like</small>
        </div>
        <button type="submit" class="btn btn-primary mb-4">Search</button>
    </form>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 d-none">

    </div>

    <div class="modal fade" id="post_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modal_form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="post_modal_label">Insert Title</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>