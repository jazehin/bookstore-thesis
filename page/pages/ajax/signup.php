<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$username = $_GET["username"];
$password = $_GET["password"];
$email = $_GET["email"];

$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@&#_%$";
$salt = "";
for ($i=0; $i < 10; $i++) { 
    $salt = $salt . $characters[rand(0, strlen($characters) - 1)];
}

$password = hash("sha256", $password . $salt);

SignUp($username, $password, $email, $salt);


?>