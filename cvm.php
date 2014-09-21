<?php
include_once 'lib.cvm.php';
include_once 'local_config.php';

if ( $cvm['connected'] ) {
	$status = '<span style="color:green;font-weight:bold">conectado</span>';
} else {
	$status = '<span style="color:red;font-weight:bold">desconectado</span>';
}

if ( isset($_COOKIE['FILESAVED']) ) {
	$filesaved = $_COOKIE['FILESAVED'];
	setcookie('FILESAVED', '', -1);
}

if ( isset( $_REQUEST['month'] ) ) {
	$month = $_REQUEST['month'];
} else {
	$month = date('Y-m');
}

$dates = get_business_days_in_month($month);

$date = new DateTime("$month-01");
$prev_month = $date->modify('-1 month')->format('Y-m');
$next_month = $date->modify('+2 month')->format('Y-m');

list($checks, $errnos) = cvm_check_imported_files_by_month($dates);
$xml_files = cvm_check_downloaded_files_by_month($dates);
?>
<!DOCTYPE html>
<html lang="pt">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="dashboard.css" type="text/css" media="screen" charset="utf-8">
		<title>CVM Dashboard</title>
	</head>
	<body id="cvm">
		<h1>Download de arquivo de informes diários por data</h1>
		<div id="body">
		<div id="cvm-commands">
<?php if ( $cvm['connected'] ): ?>
			<li><a href="cvmlogout.php?month=<?= $month ?>">Desconectar</a></li>
<?php else: ?>
			<li><a href="cvmlogin.php?month=<?= $month ?>">Conectar</a></li>
<?php endif; ?>
		</div>
		<div id="cvm-token">
<?php if ( $cvm['connected'] ): ?>
			<li><strong>Guid:</strong> <em><?= $cvm['token']['Guid'] ?></em></li>
			<li><strong>IdSessao:</strong> <em><?= $cvm['token']['IdSessao'] ?></em></li>
<?php endif; ?>
			<li><strong>Status:</strong> <?= $status ?></li>
		</div>

<?php
if ( isset($filesaved) ):
	echo "<p>";
	echo "Arquivo salvo: " . $filesaved;
	echo "</p>";
endif;
?>

<table style="width:100%;">
	<colgroup>
	<col style="width:10%;"/>
	<col style="width:10%;"/>
	<col style=""/>
	<col style="width:10%;"/>
	<col style="width:10%;"/>
	<col style="width:10%;"/>
	</colgroup>
	
	<tr>
		<th colspan="6">
			<a href="cvm.php?month=<?= $prev_month ?>">&lt;&lt;</a>
			&nbsp;&nbsp;<?= $month ?>&nbsp;&nbsp;
			<a href="cvm.php?month=<?= $next_month ?>">&gt;&gt;</a>
		</th>
	</tr>
	<tr>
		<th colspan="2"> Data </th>
		<th> Arquivo </th>
		<th style="word-wrap: break-word"> Registros na Base </th>
		<th colspan="2"> Ações </th>
	</tr>
<?php
foreach ($xml_files as $datekey => $xml_file):
	$date = DateTime::createFromFormat('Y-m-d', $datekey);
	$weekday = $date->format("l");
?>
	<tr>
		<td> <?= $weekday ?> </td>
		<td> <?= $datekey ?> </td>
		<td> <?= $xml_file ?> </td>
		<td> <?= $checks[$datekey] ?> </td>
		<td> 
			<a href="cvmwebmethods.php?methodname=solicAutorizDownloadArqComptc&iCdTpDoc=209&strDtComptcDoc=<?= $datekey ?>&month=<?= $month ?>"> Download </a>
		</td>
		<td>
<?php if ($xml_file != ""): ?> 
			<a href="cvmimport.php?file=<?= $xml_file ?>&month=<?= $month ?>"> Importar </a>
<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>


<!-- <hr>
<h1>Arquivo anual de informes diários</h1>
<form action="cvmwebmethods.php" method="post" accept-charset="utf-8">
	<p>
		<input type="hidden" name="methodname" value="solicAutorizDownloadArqAnual" id="methodname">
		<label for="filetype">Tipo de arquivo</label><input type="text" name="iCdTpDoc" value="209" id="filetype">
	</p>
	<p><input type="submit" value="Enviar"></p>
</form>

<hr>
<h1>Arquivo de movimentação de informes diários – última data</h1>
<form action="cvmwebmethods.php" method="post" accept-charset="utf-8">
	<p>
		<input type="hidden" name="methodname" value="solicAutorizDownloadArqEntrega" id="methodname">
		<label for="filetype">Tipo de arquivo</label><input type="text" name="iCdTpDoc" value="209" id="filetype">
	</p>
	<p><input type="submit" value="Enviar"></p>
</form>

<hr>
<h1>Arquivo de movimentação de informes diários por data</h1>
<form action="cvmwebmethods.php" method="post" accept-charset="utf-8">
	<p>
		<input type="hidden" name="methodname" value="solicAutorizDownloadArqEntregaPorData" id="methodname">
		<label for="filetype">Tipo de arquivo</label><input type="text" name="iCdTpDoc" value="209" id="filetype">
		<br>
		<label for="infodate">Data:</label><input type="text" name="strDtEntregaDoc" value="" id="infodate">
	</p>
	<p><input type="submit" value="Enviar"></p>
</form> -->
	</div>
	</body>
</html>
