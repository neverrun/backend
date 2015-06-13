<?php

require("../inc/database.inc.php");
require("../inc/json.inc.php");

function checkTable($table) {
	$db = pgConnection();
	$sqlCheck = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema='public' AND table_name='".$table."')";
	$statementCheck = $db->prepare($sqlCheck);
	$statementCheck->execute();
	$resultCheck = $statementCheck->fetchAll(PDO::FETCH_ASSOC);
	return $resultCheck[0]["exists"];
}

function execSql($sql) {
	$db = pgConnection();
	$statement = $db->prepare($sql);
	$statement->execute();
	return $statement;
}

function processTable($table, $data) {
	if ($table === "routes") {
		processTableRoutes($data);
	} elseif ($table === "stops") {
		processTableStops($data);
	} else {
		processTableSimple($table, $data);
	}
}

function processTableSimple($table, $data) {
	// Perform the query
	$sql = "select * from ".$table;
	if ($data->limit != 0) $sql .= " limit ".$data->limit;
	sendJSON(execSql($sql));
}

function processTableStops($data) {
	$sql = "select * from stops";
	if (is_numeric($data->fromLat) and is_numeric($data->fromLong)
			and is_numeric($data->toLat) and is_numeric($data->toLong)) {
		$sql .= " where stop_lat >= ".$data->fromLat." and stop_lat <= ".$data->toLat.
			" and stop_lon >= " . $data->fromLong ." and stop_lon <= ".$data->toLong;
	}
	if ($data->limit != 0) $sql .= " limit ".$data->limit;
	if ($data->devel === "true") {
		echo $sql; die();
	}
	sendJSON(execSql($sql));
}

function processTableRoutes($data) {
	$table = "pid_shapes";
	//$lat = "shape_pt_lat";
	//$long = "shape_pt_lon";
	$lat = "pointLat";
	$long = "pointLon";
	$sql = "select ".$lat." as lat, ".$long." as lon from ".$table;
	if (is_numeric($data->fromLat) and is_numeric($data->fromLong)
			and is_numeric($data->toLat) and is_numeric($data->toLong)) {
		//$sql .= " where shape_id = 1";
		$sql .= " where ".$lat.">= ".$data->fromLat." and ".$lat." <= ".$data->toLat.
			" and ".$long." >= " . $data->fromLong ." and ".$long." <= ".$data->toLong;
	}
	if ($data->limit != 0) $sql .= " limit ".$data->limit;
	if ($data->devel === "true") {
		echo $sql; die();
	}
	sendJSON(execSql($sql));
}
