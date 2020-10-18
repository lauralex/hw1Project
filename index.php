<?php
require_once "init.php";
if (isset($_SESSION["UID"])) {
    $query = "SELECT username from hw1_users where password = " . "'" . mysqli_escape_string($conn, $_SESSION["userid"]) . "'";
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

    mysqli_close($conn);
    header("Location: {$uri}/../home.php");
    exit();
} else {
    mysqli_close($conn);
    header("Location: {$uri}/../login.php");
    exit();
}
