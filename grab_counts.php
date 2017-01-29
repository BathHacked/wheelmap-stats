<?php

//This should be run on cron.

include_once('config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$timeNow = gmdate("Y-m-d H:i:s");
$saved = 0;
foreach( ['yes','limited','no','unknown'] as $wheelchair_filter ){
    $json = json_decode(file_get_contents("https://wheelmap.org/api/nodes?api_key=$wheelmap_api_key&bbox=-2.413769,51.363902,-2.310473,51.415048&wheelchair=$wheelchair_filter"), true);
    $item_count = $json['meta']['item_count_total'];

    $success = $mysqli->query("INSERT INTO venue_counts (`category`,`wheelchair`,`time`,`count`) VALUES ('0','$wheelchair_filter','$timeNow','$item_count')");
    if( !$success ){
        die( $mysqli->error );
    }
    else{
        $saved++;
    }
}

echo "Saved $saved rows to DB.";