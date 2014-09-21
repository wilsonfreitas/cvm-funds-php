<?php
$zip = new ZipArchive;

if ( $zip->open('/tmp/date.zip') ) {
	$filename = $zip->getNameIndex(0);
	$zip->extractTo('/tmp/');
	$zip->close();
} else {
	echo 'doh!';
}

$xml = simplexml_load_file("/tmp/$filename");

?>
<pre>
<?
// foreach ($xml->INFORMES->INFORME_DIARIO as $value) {
// 	print("$value->CNPJ_FDO: $value->VL_QUOTA\n");
// }

print_r($xml);

?>
</pre>



