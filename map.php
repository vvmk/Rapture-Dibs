<?php

function short($d) {
	return ($d<0) ? (floor(abs($d))*(-1)) : floor($d);
}

$cat = $_REQUEST['cat'];

$c_lat = $_REQUEST['lat'];
$c_lng = $_REQUEST['lng'];

$s_lat = short($c_lat);
$s_lng = short($c_lng);


//broaden the scope of SELECTED markers
$latMax = $s_lat + 10;
$latMin = $s_lat - 10;
$lngMax = $s_lng + 10;
$lngMin = $s_lng - 10;

require("db_info.php");
//connect to db
$con = mysql_connect($host,$user,$pass);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db($db, $con);

if ($cat=='t') {
	$result = mysql_query("SELECT * FROM Rapture");
} else {
	$result = mysql_query("SELECT * FROM Rapture WHERE sLat >= '$latMin' AND sLat <= '$latMax' AND sLon >= '$lngMin' AND sLon <= '$lngMax'");
}

?>

	<html>
	<head>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js" ></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	</head>
	<body>
		<div id="map-canvas" style="width:100%; height:100%;"></div>
	<script>
		var markers = [];
<?php
$i = 0;
while($row = mysql_fetch_array($result)) {	
?>
	var marker<?= $i ?> = new Object();
	marker<?= $i ?>.lat = <?= $row['Lat']; ?>;
	marker<?= $i ?>.lng = <?= $row['Lon']; ?>;
	marker<?= $i ?>.cname = '<?= $row['CName']; ?>';
	marker<?= $i ?>.lname = '<?= $row['LName']; ?>';
	marker<?= $i ?>.address = '<?= $row['Address']; ?>';
	marker<?= $i ?>.notes = '<?= $row['Notes']; ?>';
	markers.push(marker<?= $i ?>);
<?php 
$i++;
}

mysql_close($con);

?>
	var c = new google.maps.LatLng(<?= $c_lat ?>, <?= $c_lng ?>);
	
	var myOptions = {
		zoom: 10,
		center: c,
		mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    	
	var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
	
	//placeholder marker for new points
	var newMark = new google.maps.Marker();
	
	google.maps.event.addListener(map, 'click', function(event) {
		var coords = event.latLng;
		
		newMark.setPosition(coords);
		newMark.setMap(map);
		
		var addNewHTML = '<div id="new-container"><form name="newLoc" method="GET"> \
				*<input placeholder="Your name" id="cname" type="textbox" /> \
				*<input placeholder="Item description" id="lname" type="textbox" /> \
				<input placeholder="Address" id="address" type="textbox" /> \
				<input placeholder="Notes" id="notes" type="textarea" /> \
				<input type="button" value="Call Dibs" onClick="sendNew(this.form, '+
				coords.lat()+', '+ coords.lng()+')" id="submit" /></form> \
				<span class="tiny">* required fields</span> \
				</div>';
		$('#newForm-container', parent.document.body).html(addNewHTML);
		
		
	});
	
	$(markers).each(function(i, e) {
		//var icon = markers[i].icon
		var point = new google.maps.LatLng(markers[i].lat, markers[i].lng);
		var mark = new google.maps.Marker({
			position: point,
			title: markers[i].lname
		});
		mark.setMap(map);
		
		var infoString = '<div>'+markers[i].lname+'<br />'+markers[i].address+'</br />claimed by '+markers[i].cname+'<br />Notes: '+markers[i].notes+'</div>';

		var infoWindow = new google.maps.InfoWindow({
		    content: infoString
		});
		
		google.maps.event.addListener(mark, 'click', function() {
		  infoWindow.open(map,mark);
		});
	});

	//google.maps.event.addListener(markerC, 'click', toggleBounce);
	</script>
	</body>
	</html>