<?php
include 'local_config.php';
include 'lib.db.php';
include 'lib.cvm.php';

date_default_timezone_set( 'America/Sao_Paulo' );

function is_xml_file($fname) {
	return is_file($fname) and substr_compare($fname, "xml", -3, 3, true) == 0;
}

$dir = "/Users/wilson/Dropbox/dev/farol-investimentos/xml";
chdir($dir);
$files = array_filter(scandir($dir), is_xml_file);
foreach ($files as $filename) {
	$err = cvm_push_xml_to_db($filename);
	$errc = count($err);
	echo "$filename, $errc\n";
}

?>
