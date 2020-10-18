<?php
session_start();
require_once "db_parameters.php";
$uri = $_SERVER['SERVER_NAME'];
if (!empty($_SERVER["HTTPS"]) && "on" == $_SERVER["HTTPS"]) {
    $uri = "https://" . $uri . $_SERVER["SCRIPT_NAME"];
} else {
    $uri = "http://" . $uri . $_SERVER["SCRIPT_NAME"];
}

function mys_con() {
    return mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "bellia_alessandro", "3306");
}

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, "bellia_alessandro", "3306");