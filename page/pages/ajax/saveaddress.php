<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$username = $_SESSION["user"]["username"];
$company = $_GET["company"] === "null" ? null : $_GET["company"];
$county = $_GET["county"];
$city = $_GET["city"];
$public_space = $_GET["public_space"];
$zip_code = strval($_GET["zip_code"]);
$note = $_GET["note"] === "null" ? null : $_GET["note"];

SaveAddress($username, $company, $county, $city, $public_space, $zip_code, $note);
?>