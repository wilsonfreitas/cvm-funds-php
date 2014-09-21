<?php
include 'lib.cvm.php';

$string = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<soap:Header>
		<sessaoIdHeader xmlns="http://www.cvm.gov.br/webservices/">
			<Guid>8200ac01-bfb5-46d6-a625-38108141fb33</Guid>
			<IdSessao>135128883</IdSessao>
		</sessaoIdHeader>
	</soap:Header>
	<soap:Body>
		<solicAutorizDownloadArqComptcResponse xmlns="http://www.cvm.gov.br/webservices/">
			<solicAutorizDownloadArqComptcResult>http://cvmweb.cvm.gov.br/swb/sistemas/scw/DownloadArqs/LeDownloadArqs.aspx?VL_GUID=8200ac01-bfb5-46d6-a625-38108141fb33&amp;PK_SESSAO=135128883&amp;PK_ARQ_INFORM_DLOAD=131515</solicAutorizDownloadArqComptcResult>
		</solicAutorizDownloadArqComptcResponse>
	</soap:Body>
</soap:Envelope>
XML;

$xml = simplexml_load_string($string);
$xml->registerXPathNamespace('cvm', $cvm['ns']);
$item = $xml->xpath('//cvm:solicAutorizDownloadArqComptcResponse');
$url = $item[0]->solicAutorizDownloadArqComptcResult;

?>
<pre><? print_r($item); ?></pre>
<pre><? print($token->solicAutorizDownloadArqComptcResult); ?></pre>
<pre><? print_r($cvm); ?></pre>

