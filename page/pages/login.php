<?php
include("./../includes/db_con.php");
include("./../includes/sql.php");

$username = $_GET["username"];
$password = $_GET["password"];

$user_id = Login($username, $password);

if (is_null($user_id)) {
    echo "error";
} else {
    session_start();
    $_SESSION["logged_in"] = true;
    $_SESSION["user"] = GetUserById($user_id);
    echo "success";
}

?>