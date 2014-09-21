<?php
include 'lib.cvm.php';
include 'local_config.php';

if ( isset($_REQUEST['methodname']) ) {
	$client = cvm_get_client();
	$methodname = $_REQUEST['methodname'];
	$resultmethod = $methodname . "Result";
	$methodparms = array(
		"iCdTpDoc" => $_REQUEST["iCdTpDoc"],
		"strDtIniEntregDoc" => $_REQUEST["strDtIniEntregDoc"],
	);
	$response = $client->$methodname($methodparms);
	$resultset = $response->$resultmethod;
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

<header id="cvm">
<h1>Execução de métodos <em>session-free</em></h1>
</header>

<section id="cvm">

<form action="cvm3.php" method="get" accept-charset="utf-8">
	<input type="hidden" name="methodname" value="retornaListaComptcDocs">
	<input type="hidden" name="iCdTpDoc" value="209">
	<fieldset id="sessionfree">
		<!-- <legend>Execução de métodos <em>session-free</em></legend> -->
		<ol>
			<li>
			<label for="start_date">Data</label>
			<input type="date" name="strDtIniEntregDoc" autofocus id="start_date" value="">
			</li>
			<li>
			<input type="submit" value="Enviar">
			</li>
		</ol>
	</fieldset>
</form>
<?php if ( isset( $response ) ) { ?>
	<p>Data: <?= $_REQUEST["strDtIniEntregDoc"] ?> </p>
<table>
	<tr>
		<th>
			Data
		</th>
	</tr>
<?php
foreach ( $resultset->string as $date ) {
// foreach ( array_pop( $resultset ) as $date ) {
?>
	<tr>
		<td><?= $date ?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>
</section>
</body>
</html>
