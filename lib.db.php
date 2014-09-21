<?php
include_once "local_config.php";

function db_open_connection() {
	$dbconn = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
	if ( !$dbconn ) {
		die('Not connected : ' . mysql_error());
	}
	$db_selected = mysql_select_db(DB_DATABASE, $dbconn);
	if ( !$db_selected ) {
		die('Fail selecting database : ' . mysql_error());
	}
	return $dbconn;
}

/*
 * MySQL error codes:
 * 	1062: unique violation
 **/
?>
