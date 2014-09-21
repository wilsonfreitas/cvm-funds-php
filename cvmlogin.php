<?php
include 'lib.cvm.php';

$client = cvm_get_client();
$token = cvm_get_token($client);

if ($token) {
	setcookie("GUID", $token['Guid']);
	setcookie("IDSESSAO", $token['IdSessao']);
}
$month = $_REQUEST['month'];
header("Location: cvm.php?month=$month");
?>