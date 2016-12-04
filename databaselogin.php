<?php
 
$mysqli = new mysqli('localhost', 'wcmod3', 'wustlwcmod3wustl', 'module3data');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>
