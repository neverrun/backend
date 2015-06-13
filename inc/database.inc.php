<?php
/*
    Database Connections
*/

// Return database connection
function pgConnection() {
	$conn = new PDO ("pgsql:host=localhost;dbname=data;port=5432","db","Hackity.hack", array(PDO::ATTR_PERSISTENT => true));
    return $conn;
}

?>
