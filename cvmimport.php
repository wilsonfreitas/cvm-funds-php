<?php
include 'lib.cvm.php';
include 'local_config.php';

$file = $_REQUEST['file'];
chdir(CVM_PATH);
cvm_push_xml_to_db($file);

$month = $_REQUEST['month'];
header("Location: cvm.php?month=$month");
?>
