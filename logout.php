<?php
session_start();
session_unset();
session_destroy();

$uri = $_SERVER['SERVER_NAME'];
if (!empty($_SERVER["HTTPS"]) && "on" == $_SERVER["HTTPS"]) {
    $uri = "https://" . $uri . $_SERVER["SCRIPT_NAME"];
} else {
    $uri = "http://" . $uri . $_SERVER["SCRIPT_NAME"];
}

header("Location: {$uri}/../login.php");