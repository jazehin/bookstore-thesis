<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");

$email = $_GET["email"];
$username = $_GET["username"];

$out = "";

if (EmailAlreadyExists($email)) {
    $out = "email-exists";
} else {
    $out = "email-does-not-exist";
}

if (UsernameAlreadyExists($username)) {
    $out = $out . " username-exists";
} else {
    $out = $out . " username-does-not-exist";
}

echo $out;

?>