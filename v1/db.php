<?php

# Includes
require("../inc/database.inc.php");
require("../inc/json.inc.php");

$db = pgConnection();
$fromTable = $_REQUEST['table'];
$limit = 0;
if (isset($_REQUEST['limit']))
	$limit = intval($_REQUEST['limit']);
$devel = null;
if (isset($_REQUEST['devel']))
	$devel = $_REQUEST['devel'];

$fromLat = null;
$fromLong = null;
$toLat = null;
$toLong = null;
if (isset($_REQUEST['from_lat']) and isset($_REQUEST['to_lat'])
	and isset($_REQUEST['from_long']) and isset($_REQUEST['to_long'])) {
	$fromLat = $_REQUEST['from_lat'];
	$fromLong = $_REQUEST['from_long'];
	$toLat = $_REQUEST['to_lat'];
	$toLong = $_REQUEST['to_long'];
}

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

if ($fromTable === "stops" and is_numeric($fromLat) and is_numeric($fromLong)
	and is_numeric($toLat) and is_numeric($toLong)) {
	$sql .= " where stop_lat >= ".$fromLat." and stop_lat <= ".$toLat.
		" and stop_lon >= " . $fromLong ." and stop_lon <= ".$toLong;
}

if ($limit != 0) $sql .= " limit ".$limit;

if ($devel === "true") {
	echo $sql; die();
}

$statement=$db->prepare($sql);
$statement->execute();

// send JSON or JSONP results
sendJSON($statement);
