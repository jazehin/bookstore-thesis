<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$username = $_SESSION["user"]["username"];
$address_id = $_GET["address_id"];

DeleteAddressCon($address_id, $username);
?>