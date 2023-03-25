<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$username = $_SESSION["user"]["username"];
$family_name = $_GET["family_name"] === "null" ? null : $_GET["family_name"];
$given_name = $_GET["given_name"] === "null" ? null : $_GET["given_name"];
$gender = $_GET["gender"] === "null" ? null : $_GET["gender"];
$birthdate = $_GET["birthdate"] === "null" ? null : $_GET["birthdate"];
$phone_number = $_GET["phone_number"] === "null" ? null : $_GET["phone_number"];

SaveUserData($username, $family_name, $given_name, $gender, $birthdate, $phone_number);
?>