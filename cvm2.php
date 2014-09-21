<?php
include 'lib.cvm.php';
include 'local_config.php';

if ( isset($_REQUEST['methodname']) ) {
	list($url) = cvm_execute_request(cvm_get_client(), $_REQUEST);
	$zipfile = cvm_download_file($url, CVM_PATH);
	$filesaved = cvm_unzip_downloaded_file($zipfile, CVM_PATH);
	// $xml = simplexml_load_file($xmlfile);
}

if ( isset($_REQUEST['login']) ) {
	$client = cvm_get_client();
	$token = cvm_get_token($client);
	
	if ($token) {
		setcookie("GUID", $token['Guid']);
		setcookie("IDSESSAO", $token['IdSessao']);
	}
	header("Location: cvm2.php");
}

if ( isset($_REQUEST['logout']) ) {
	setcookie("GUID", "", -1);
	setcookie("IDSESSAO", "", -1);
	header("Location: cvm2.php");
}

if ( $cvm['connected'] ) {
	$status = '<span style="color:green;font-weight:bold">conectado</span>';
} else {
	$status = '<span style="color:red;font-weight:bold">não conectado</span>';
}

?>
<!DOCTYPE html>
<html lang="pt">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="dashboard.css" type="text/css" media="screen" charset="utf-8">
		<title>CVM Cockpit</title>
	</head>
<body id="cvm">
	<h1>Download do arquivo de dados cadastrais de fundos</h1>
	<div id="body">
		<div id="cvm-commands">
<? if ( $cvm['connected'] ) { ?>
			<li><a href="cvm2.php?logout=true">Desconectar</a></li>
<? } else { ?>
			<li><a href="cvm2.php?login=true">Conectar</a></li>
<? } ?>
		</div>
		<div id="cvm-token">
<? if ( $cvm['connected'] ) { ?>
			<li><strong>Guid:</strong> <em><?= $cvm['token']['Guid'] ?></em></li>
			<li><strong>IdSessao:</strong> <em><?= $cvm['token']['IdSessao'] ?></em></li>
<? } ?>
			<li><strong>Status:</strong> <?= $status ?></li>
		</div>
<?php
if ( isset($filesaved) ) {
	echo "<p>";
	echo "Arquivo salvo: " . $filesaved;
	echo "</p>";
}
?>
<form action="cvm2.php" method="get" accept-charset="utf-8">
	<p>
		<input type="hidden" name="methodname" value="solicAutorizDownloadCadastro" id="methodname">
		<label for="start_date">Data</label>
		&nbsp;
		<input type="date" name="strDtRefer" autofocus id="start_date" value="">
		&nbsp;
		<input type="submit" value="Download">
	</p>
	<p></p>
</form>

	</div>
</body>
</html>
