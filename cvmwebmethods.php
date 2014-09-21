<?php
include_once 'lib.cvm.php';
include_once 'local_config.php';

// cvmwebmethods.php?methodname=solicAutorizDownloadArqComptc&iCdTpDoc=209&strDtComptcDoc=2014-07-09
header('Content-Type: application/json');

cvm_login();
list($url) = cvm_execute_request(cvm_get_client(), $_REQUEST);
$zipfile = cvm_download_file($url);
$xmlfile = cvm_unzip_downloaded_file($zipfile);
$data = cvm_parse_xml($xmlfile);

echo json_encode($data);

// cvm_logout();
unlink($zipfile);
unlink($xmlfile);
?>
