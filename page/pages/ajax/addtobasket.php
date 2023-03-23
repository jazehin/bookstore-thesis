<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$isbn = $_GET["isbn"];
$title = $_GET["title"];

if (isset($_SESSION["basket"][$isbn])) {
    $_SESSION["basket"][$isbn] = $_SESSION["basket"][$isbn] + 1;
} else {
    $_SESSION["basket"][$isbn] = 1;
}

echo "A(z) \"" . $title . "\" című könyv hozzá lett adva a kosárhoz.";