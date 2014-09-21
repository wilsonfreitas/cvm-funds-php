#!/usr/bin/php
<?php
// include 'lib.cvm.php';
include 'local_config.php';

date_default_timezone_set( 'America/Sao_Paulo' );

// 
// function check($fname) {
// 	return is_file($fname) and substr_compare($fname, "xml", -3, 3, true) == 0;
// }
// 
// $dir = "/Applications/XAMPP/xamppfiles/temp";
// chdir($dir);
// $files = array_filter(scandir($dir), check);
// 
// foreach ($files as $file) {
// 	// $xmlfile = "/Applications/XAMPP/xamppfiles/temp/20121227.xml";
// 	$xml = simplexml_load_file( $file );
// 	$date = strval( array_pop( $xml->xpath('//CABECALHO/DT_REFER') ) );
// 	$datestr = strftime( "%Y%m%d", strtotime($date) );
// 	$newfile = CVM_PATH . "/" . $cvm['prefix'] . $cvm['informes-diarios-prefix'] . "$datestr.xml";
// 	if ( copy( $file, $newfile ) ) {
// 		print($newfile . " copied!\n");
// 	} else {
// 		print($newfile . " not copied!\n");
// 	}
// }
// $dbconn = db_open_connection();
// $query = query_check_informe_diario_date('2012-12-28');
// $result = mysql_query($query);
// while ( $row = mysql_fetch_array($result) ) {
// 	print_r($row[0]);
// }
// mysql_close($dbconn);
// preg_match('/-(\d{8})\.xml$/', "CVM-ID-20121227.xml", $regs);
// print_r( $regs );
// $date = DateTime::createFromFormat('Ymd', $regs[1]);
// print_r($date);
// print_r($date->format('Y-m-d'));
// print_r( date_parse_from_format('Ymd', '20121227') );
// print_r($date = DateTime::createFromFormat('Y-m-d', "2012-02-01"));
// print_r($date->add(date_interval_create_from_date_string('1 day')));

$filename = '/Users/wilson/Google Drive/dev/farol-investimentos/xml/CVM-ID-20121203.xml';
// $xml = simplexml_load_file($filename);
// print_r($xml
echo date("Y-m-d", filemtime($filename));

?>