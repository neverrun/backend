<?php
/*
    Buffer Point
*/

# Includes
require("../inc/database.inc.php");
require("../inc/json.inc.php");

# Retrive URL arguments
$devel = $_REQUEST['devel'];
$x = $_REQUEST['x'];
$y = $_REQUEST['y'];
$srid = $_REQUEST['srid'];
$distance = $_REQUEST['distance'];
$table = $_REQUEST['table'];
$geometryfield = isset($_REQUEST['geometryfield']) ? $_REQUEST['geometryfield'] : "geom";
$fields = isset($_REQUEST['fields']) ? $_REQUEST['fields'] : "*";
$parameters = isset($_REQUEST['parameters']) ? " and " . $_REQUEST['parameters'] : "";
$limit = isset($_REQUEST['limit']) ? " limit " . $_REQUEST['limit'] : '';
$order = isset($_REQUEST['order']) ? " order by " . $_REQUEST['order'] : ' order by distance ';

# Perform the query
#$sql = "SELECT " . $fields . ",
#    ST_Distance(ST_transform(ST_GeomFromText('POINT(" . $x . " " . $y . ")'," . $srid ."), find_srid('', '" . $table . "', '" . $geometryfield . "')),
#    a." . $geometryfield . ") as distance
#    FROM " . $table . " a
#    WHERE ST_DWithin(a." . $geometryfield . ",
#    ST_Transform(ST_GeomFromText('POINT(" . $x . " " . $y . ")'," . $srid . "), find_srid('', '" . $table . "', '" . $geometryfield . "')), " . $distance . ") "
#    . $parameters
#    . $order
#    . $limit;
$srid2 = 3857;
$sql = "SELECT " . $fields . ",
    ST_Distance(ST_transform(ST_GeomFromText('POINT(" . $x . " " . $y . ")'," . $srid ."), 3857),
    ST_Transform(a.\"" . $geometryfield . "\", 3857)) as distance
    FROM " . $table . " a
    WHERE ST_DWithin(ST_Transform(a.\"" . $geometryfield . "\", 3857),
    ST_Transform(ST_GeomFromText('POINT(" . $x . " " . $y . ")'," . $srid . "), 3857), " . $distance . ") "
    . $parameters
    . $order
    . $limit;
if ($devel === "true") {
	echo $sql; die();
}
$db = pgConnection();
$statement=$db->prepare( $sql );
$statement->execute();

# send JSON or JSONP results
sendJSON($statement);
