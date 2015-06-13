<?php

# Includes
require("../inc/database.inc.php");
require("../inc/json.inc.php");

$db = pgConnection();
$fromTable = $_REQUEST['table'];

// check table
$sqlCheck = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema='public' AND table_name='".$fromTable."')";
$statementCheck = $db->prepare($sqlCheck);
$statementCheck->execute();
$resultCheck = $statementCheck->fetchAll(PDO::FETCH_ASSOC);
$tableExists = $resultCheck[0]["exists"];

if (!$tableExists) {
	header('HTTP/1.0 404 Not Found');
	die("404 Not Found");
}

// Perform the query
$sql = "select * from ".$fromTable;
$statement=$db->prepare($sql);
$statement->execute();

// send JSON or JSONP results
sendJSON($statement);
