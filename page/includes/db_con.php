<?php
function GetConnection()
{
	include("env.php");

	// Create connection
	$con = mysqli_connect($hostname, $username, $password, $database);

	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	} else {
		return $con;
	}
}
?>