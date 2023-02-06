<?php
function GetConnection()
{
	$servername = "localhost";
	$username = "jazehin";
	$password = "VízPronto6395";

	// Create connection
	$con = mysqli_connect($servername, $username, $password);

	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	} else {
		return $con;
	}
}
?>