<?php
// include_once 'lib.cvm.php';
// include_once 'local_config.php';
// cvmwebmethods.php?methodname=solicAutorizDownloadArqComptc&iCdTpDoc=209&strDtComptcDoc=2014-07-09
// header('Content-Type: application/json');

function cvm_download_file($url) {
	$to = sys_get_temp_dir();
	
	$handle = fopen($url, 'rb');
	$handle or trigger_error("Download cvm file error: unable to download file for the given URL\n" . $url, E_USER_ERROR);

	$contents = stream_get_contents($handle);
	fclose($handle);
	
	$tmp = tempnam($to, 'CVM');
	$handle = fopen($tmp, 'w');
	fwrite($handle, $contents);
	fclose($handle);
	
	return $tmp;
}

function cvm_unzip_downloaded_file($zipfile) {
	$to = sys_get_temp_dir();
	
	$zip = new ZipArchive();
	$zip->open($zipfile) or trigger_error("Unzip cvm file error: problems to unzip downloaded file.", E_USER_ERROR);
	$filename = $zip->getNameIndex(0);
	$zip->extractTo($to);
	$zip->close();
	
	chdir($to);
	$content = file_get_contents($filename);
	$xml = simplexml_load_string($content);
	$date = strval( array_pop( $xml->xpath('//CABECALHO/DT_REFER') ));
	$datestr = strftime( "%Y%m%d", strtotime( $date ) );
	$newfilename = "CVM-ID-$datestr.xml";
	rename( $filename , $newfilename );
	
	return "$to/$newfilename";
}

function cvm_parse_xml($xmlfile) {
	$contents = file_get_contents($xmlfile);
	$xml = simplexml_load_string($contents);
	$informes = $xml->xpath('//INFORMES/INFORME_DIARIO');
	
	foreach ($informes as $informe) {
		$inf[VL_TOTAL] = str_replace(',', '.', $informe->VL_TOTAL);
		$inf[VL_QUOTA] = str_replace(',', '.', $informe->VL_QUOTA);
		$inf[PATRIM_LIQ] = str_replace(',', '.', $informe->PATRIM_LIQ);
		$inf[CAPTC_DIA] = str_replace(',', '.', $informe->CAPTC_DIA);
		$inf[RESG_DIA] = str_replace(',', '.', $informe->RESG_DIA);
		$inf[CNPJ_FDO] = "$informe->CNPJ_FDO";
		$inf[DT_COMPTC] = "$informe->DT_COMPTC";
		$infs[] = $inf;
	}
	
	return $infs;
}

echo "wilson";

$json = file_get_contents('http://cvm-funds.appspot.com');
$url = json_decode($json);

echo $url;

$zipfile = cvm_download_file($url);
$xmlfile = cvm_unzip_downloaded_file($zipfile);
$data = cvm_parse_xml($xmlfile);

echo json_encode($data);

// cvm_logout();
unlink($zipfile);
unlink($xmlfile);
?>
