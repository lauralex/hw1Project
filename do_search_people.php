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

if (isset($_GET['searchedUser'])) {
    $escapedSearchedUser = mysqli_escape_string($conn, trim($_GET['searchedUser']));
    $query = "SELECT username, image, ID FROM hw1_users WHERE username LIKE '%{$escapedSearchedUser}%'";
    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        $users = array();
        while ($row = mysqli_fetch_assoc($queryRes)) {

            $foundUser = $row['username'];
            $foundImage = $row['image'];
            $UID = $row['ID'];
            $query = "SELECT followerId FROM hw1_follow WHERE followerId = {$_SESSION['UID']} AND followed = {$UID}";
            $checkFollowRes = mysqli_query($conn, $query);

            $users[] = array('followed'=> $checkFollowRes && mysqli_num_rows($checkFollowRes) > 0, 'UID'=> $UID, 'foundUser'=> $foundUser, 'foundImage'=> $foundImage);
            mysqli_free_result($checkFollowRes);
        }
        echo json_encode($users);

    } else {
        http_response_code(404);

    }
    exit();
}

if (isset($_GET['getAll'])) {
    $query = "SELECT username, image, ID FROM hw1_users";
    $queryRes = mysqli_query($conn, $query);

    if ($queryRes) {
        $users = array();
        while ($row = mysqli_fetch_assoc($queryRes)) {
            $foundUser = $row['username'];
            $foundImage = $row['image'];
            $UID = $row['ID'];
            $query = "SELECT  followerId FROM hw1_follow WHERE followerId = {$_SESSION['UID']} AND followed = {$UID}";
            $checkFollowRes = mysqli_query($conn, $query);

            $users[] = array('followed'=> $checkFollowRes && mysqli_num_rows($checkFollowRes) > 0, 'UID'=> $UID, 'foundUser'=> $foundUser, 'foundImage'=> $foundImage);
            mysqli_free_result($checkFollowRes);
        }
        echo json_encode($users);
    } else {
        http_response_code(404);
    }
    exit();
}