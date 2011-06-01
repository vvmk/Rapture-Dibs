<?php

function short($d) {
	return ($d<0) ? (floor(abs($d))*(-1)) : floor($d);
}

$lat = $_REQUEST['lat'];
$lng = $_REQUEST['lng'];
$slat = short($lat);
$slng = short($lng);
$cname = $_REQUEST['cname'];
$lname = $_REQUEST['lname'];
$address = $_REQUEST['address'];
$notes = $_REQUEST['notes'];

require("db_info.php");
//connect to db
$con = mysql_connect($host,$user,$pass);

if (!$con) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db($db, $con);

$sql = sprintf("INSERT INTO Rapture (sLat, sLon, Lat, Lon, CName, LName, Address, Notes) VALUES ('$slat', '$slng', '$lat', '$lng', '%s', '%s', '%s', '%s')",
mysql_real_escape_string($cname),mysql_real_escape_string($lname),mysql_real_escape_string($address),mysql_real_escape_string($notes));

if (!mysql_query($sql, $con)) {
	die('Error: '.mysql_error());
}
echo 'Thundercats are Go!';

mysql_close($con);
?>