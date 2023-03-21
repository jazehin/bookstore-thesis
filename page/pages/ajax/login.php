<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$username = $_GET["username"];
$password = $_GET["password"];

$salt = GetSalt($username);
$password = hash("sha256", $password .  $salt);

$user_id = Login($username, $password);

if (is_null($user_id)) {
    $_SESSION["logged_in"] = false;
    unset($_SESSION["user"]);
    echo "error";
} else {
    $_SESSION["logged_in"] = true;
    $_SESSION["user"] = GetUserById($user_id);
    echo "success";
}

?>