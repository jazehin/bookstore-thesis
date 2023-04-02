<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$user_id = $_SESSION["user"]["id"];
$isbn = $_GET["isbn"];
$rating = $_GET["rating"];

SetRating($user_id, $isbn, $rating);
?>