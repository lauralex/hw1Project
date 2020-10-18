<?php
require_once "init.php";
if (isset($_SESSION["UID"])) {
    $escapedPassword = mysqli_escape_string($conn, $_SESSION['userid']);
    $escapedUser = mysqli_escape_string($conn, $_SESSION['username']);
    $query = "SELECT username from hw1_users where password = '{$escapedPassword}' and username = '{$escapedUser}'";
    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        if (mysqli_num_rows($queryRes) == 0) {
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

if (isset($_GET['follow'])) {
    $follow = mysqli_escape_string($conn, $_GET['follow']);
    $query = "SELECT followerId FROM hw1_follow WHERE followerId = {$_SESSION['UID']} AND followed = '{$follow}'";

    $queryRes = mysqli_query($conn, $query);
    if ($queryRes) {
        if (mysqli_num_rows($queryRes) > 0) {
            $query = "DELETE FROM hw1_follow WHERE followerId = {$_SESSION['UID']} AND followed = '{$follow}'";
            $deleteRes = mysqli_query($conn, $query);
            if (!$deleteRes) {
                http_response_code(404);
                mysqli_close($conn);
                exit();
            }

            echo 'User unfollowed';
        } else {
            $query = "INSERT INTO hw1_follow (followerId, followed) VALUES ('{$_SESSION['UID']}', '{$follow}')";
            $addRes = mysqli_query($conn, $query);
            if (!$addRes) {
                http_response_code(404);
                mysqli_close($conn);
                exit();
            }

            echo 'User followed';
        }
        mysqli_free_result($queryRes);
    } else {
        http_response_code(404);
    }

    exit();

}

?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta content="Cerca Utenti" name="description">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <title>Cerca Utenti</title>
    <link rel="icon" href="favicon.png" sizes="64x64" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script defer src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script defer src="search_people.js?ver=11"></script>
</head>
<body>
<div class="container">
    <h1>Welcome</h1>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
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
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo "{$uri}/../create_post.php" ?>">Create Post</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?php echo "{$uri}/../search_people.php" ?>">Search Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo "{$uri}/../logout.php" ?>">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <form id="search_user" class="mb-4">
        <div class="form-group">
            <label for="search_user_box">Insert a username you want to search</label>
            <input type="search" class="form-control" id="search_user_box" name="searchedUser">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <button type="button" id="search_all" class="btn btn-primary">Search all</button>
    </form>

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 d-none">

    </div>

    <div class="modal fade" id="follow_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modal_form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="follow_modal_label"></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
