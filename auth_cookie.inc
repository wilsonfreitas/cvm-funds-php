<?
if (isset ($_COOKIE['GUID']) and isset ($_COOKIE['IDSESSAO'])) {
	$GLOBALS["CVM_TOKEN"] = array(
		'Guid' => $_COOKIE['GUID'],
		'IdSessao' => $_COOKIE['IDSESSAO']
	);
}
?>
