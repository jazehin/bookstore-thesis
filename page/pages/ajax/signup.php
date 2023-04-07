<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$username = $_GET["username"];
$original_password = $_GET["password"];
$email = $_GET["email"];

$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@&#_%$";
$salt = "";
for ($i=0; $i < 10; $i++) { 
    $salt = $salt . $characters[rand(0, strlen($characters) - 1)];
}

$password = hash("sha256", $original_password . $salt);

SignUp($username, $password, $email, $salt);

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