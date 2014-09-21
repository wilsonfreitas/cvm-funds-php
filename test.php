<?php
include 'local_config.php';
include 'lib.db.php';
include 'lib.cvm.php';

date_default_timezone_set( 'America/Sao_Paulo' );

$filename = "/Users/wilson/Dropbox/dev/farol-investimentos/xml/CVM-ID-20131025.xml";
$err = cvm_push_xml_to_db( $filename );
echo "errors " . count($err) . "\n";
?>
