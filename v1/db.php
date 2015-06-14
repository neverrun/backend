<?php

require("db_fcs.php");
ini_set('memory_limit', '-1');

$db = pgConnection();
$table = $_REQUEST['table'];
$data = new StdClass();
$data->limit = 0;
if (isset($_REQUEST['limit']))
	$data->limit = intval($_REQUEST['limit']);
$data->devel = null;
if (isset($_REQUEST['devel']))
	$data->devel = $_REQUEST['devel'];

$data->fromLat = null;
$data->fromLong = null;
$data->toLat = null;
$data->toLong = null;
if (isset($_REQUEST['from_lat']) and isset($_REQUEST['to_lat'])
	and isset($_REQUEST['from_long']) and isset($_REQUEST['to_long'])) {
	$data->fromLat = $_REQUEST['from_lat'];
	$data->fromLong = $_REQUEST['from_long'];
	$data->toLat = $_REQUEST['to_lat'];
	$data->toLong = $_REQUEST['to_long'];
}

if (!checkTable($table) and $table !== "pid_routes2") {
	header('HTTP/1.0 404 Not Found');
	die("404 Not Found");
}

processTable($table, $data);
