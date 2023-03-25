<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$username = $_SESSION["user"]["username"];
$genre = $_GET["genre"];
$mode = $_GET["mode"];

if ($mode == "add") {
    SetPreference($username, $genre, true);
} else if ($mode == "remove") {
    SetPreference($username, $genre, false);
}
?>