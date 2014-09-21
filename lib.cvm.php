<?php
// include 'auth_cookie.inc';
include_once 'lib.db.php';
include_once 'local_config.php';

$cvm['wsdl'] = 'http://www.cvm.gov.br/webservices/Sistemas/SCW/CDocs/WsDownloadInfs.asmx?WSDL';
$cvm['ns'] = 'http://www.cvm.gov.br/webservices/';
$cvm['download-text'] = 'Autalização de banco de dados de fundos de investimento';
$cvm['nr-sist'] = 0000;
$cvm['senha'] = "xxxxx";
$cvm['prefix'] = 'CVM-';
$cvm['informes-diarios-prefix'] = 'ID-';
$cvm['cadastro-prefix'] = 'CD-';
$cvm['methods'] = array(
	'solicAutorizDownloadArqComptc' => array(
		array("iCdTpDoc", function($req) {return intval($req["iCdTpDoc"]);} ),
		array("strDtComptcDoc", function($req) {return $req["strDtComptcDoc"];} ),
	),
	// solicAutorizDownloadArqAnual => array(
	// 	array("iCdTpDoc", function($req) {return intval($req["iCdTpDoc"]);} ),
	// ),
	// solicAutorizDownloadArqEntrega => array(
	// 	array("iCdTpDoc", function($req) {return intval($req["iCdTpDoc"]);} ),
	// ),
	'solicAutorizDownloadArqEntregaPorData' => array(
		array("iCdTpDoc", function($req) {return intval($req["iCdTpDoc"]);} ),
		array("strDtEntregaDoc", function($req) {return $req["strDtEntregaDoc"];} ),
	),
	'solicAutorizDownloadCadastro' => array(
		array("strDtRefer", function($req) {return $req["strDtRefer"];})
	),
);

if (isset ($_COOKIE['GUID']) and isset ($_COOKIE['IDSESSAO'])) {
	$cvm['token'] = array(
		'Guid' => $_COOKIE['GUID'],
		'IdSessao' => $_COOKIE['IDSESSAO']
	);
	$cvm['connected'] = true;
} else {
	$cvm['connected'] = false;
}

function cvm_login() {
	global $cvm;
	
	$client = cvm_get_client();
	$token = cvm_get_token($client);

	if ($token):
		// setcookie("GUID", $token[Guid]);
		// setcookie("IDSESSAO", $token[IdSessao]);
		$cvm[token] = $token;
		$cvm[connected] = true;
	else:
		$cvm[connected] = false;
	endif;
}

function cvm_logout() {
	global $cvm;
	setcookie("GUID", "", -1);
	setcookie("IDSESSAO", "", -1);
	unset($cvm[token]);
	$cvm[connected] = false;
}

function cvm_get_token($client) {
	global $cvm;
	
	try {
		$result = $client->Login(array("iNrSist" => $cvm['nr-sist'], "strSenha" => $cvm['senha']));
	} catch (SoapFault $fault) {
		trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
	}
	
	// Parse the response XML
	$xml = simplexml_load_string($client->__getLastResponse());
	$xml->registerXPathNamespace('cvm', $cvm['ns']);
	$item = $xml->xpath('//cvm:sessaoIdHeader');
	if ($item) {
		$token = $item[0];
		return array('Guid' => $token->Guid, 'IdSessao' => $token->IdSessao);
	} else {
		$xmlcontent = htmlentities($client->__getLastResponse());
		trigger_error("SOAP xml parse error: cvm:sessaoIdHeader not found.<br><br>$xmlcontent", E_USER_ERROR);
	}
}

function cvm_get_client() {
	global $cvm;
	
	$client = new SoapClient($cvm['wsdl'],
		array("trace" => true, "exceptions" => true, "cache_wsdl" => false)
	);
	
	if ( isset ($cvm['token']) ) {
		$header = new SoapHeader($cvm['ns'], 'sessaoIdHeader', $cvm['token']);
		$client->__setSoapHeaders($header);
	}
	
	return $client;
}

function cvm_execute_request($client, $req) {
	global $cvm;
	$method = $req['methodname'];
	$resultmethod = $method . "Result";
	$methodparms["strMotivoAutorizDownload"] = $cvm['download-text'];
	foreach ($cvm["methods"][$method] as $parms):
		$methodparms[$parms[0]] = $parms[1]($req);
	endforeach;
	
	// $methodparms = array_map(function ($parms) {
	// 	global $req;
	// 	return $parms[1]($req);
	// }, $methHandler[$method])
	
	$res = $client->$method($methodparms);
	return array($res->$resultmethod, $res, $method);
}

function cvm_download_file($url) {
	global $cvm;
	
	$to = sys_get_temp_dir();
	
	$handle = fopen($url, 'rb');
	$handle or trigger_error("Download cvm file error: unable to download file for the given URL\n" . $url, E_USER_ERROR);

	$contents = stream_get_contents($handle);
	fclose($handle);
	
	$tmp = tempnam($to, $cvm['prefix']);
	$handle = fopen($tmp, 'w');
	fwrite($handle, $contents);
	fclose($handle);
	
	return $tmp;
}

function cvm_unzip_downloaded_file($zipfile) {
	global $cvm;
	
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
	$newfilename = $cvm['prefix'] . $cvm['informes-diarios-prefix'] . "$datestr.xml";
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

function cvm_push_xml_to_db($xmlfile) {
	$contents = file_get_contents($xmlfile);
	$xml = simplexml_load_string($contents);
	$informes = $xml->xpath('//INFORMES/INFORME_DIARIO');
	
	$dbconn = db_open_connection();
	
	foreach ($informes as $informe) {
		$inf[VL_TOTAL] = str_replace(',', '.', $informe->VL_TOTAL);
		$inf[VL_QUOTA] = str_replace(',', '.', $informe->VL_QUOTA);
		$inf[PATRIM_LIQ] = str_replace(',', '.', $informe->PATRIM_LIQ);
		$inf[CAPTC_DIA] = str_replace(',', '.', $informe->CAPTC_DIA);
		$inf[RESG_DIA] = str_replace(',', '.', $informe->RESG_DIA);
		$inf[CNPJ_FDO] = $informe->CNPJ_FDO;
		$inf[DT_COMPTC] = $informe->DT_COMPTC;
		$query = query_insert_informe_diario($inf);
    // print($query);
    // print("\n");
		$result = mysql_query($query);
		$errno = mysql_errno($dbconn);
		if ( $errno != 0 ) {
			$errors[] = array($errno, $informe->CNPJ_FDO);
		}
	}
	
	mysql_close($dbconn);
	
	return $errors;
}

function cvm_request_insert_a_bunch($reqs) {
	$dbconn = db_open_connection();
	
	foreach ($reqs as $req) {
		$query = query_insert_cvm_request($req);
		$result = mysql_query($query);
		$errno = mysql_errno($dbconn);
		if ( $errno != 0 ) {
			$errors[] = $errno;
		}
	}
	
	mysql_close($dbconn);
	
	return $errors;
}

function cvm_request_by_month($month, $method, $file_type) {
// 	$query = <<<SQL
// SELECT `file_type`, `method`, `request_date`, `reference_date`, `status`, `message`
// FROM `cvm_request`
// WHERE
// 	`method` = '$method' AND
// 	`file_type` = '$file_type' AND
// 	DATE_FORMAT(`reference_date`, '%Y-%m') = '$month'
// GROUP BY
// 	`reference_date`
// ORDER BY
// 	`reference_date`
// SQL;
	$query = <<<SQL
SELECT 
	cvm_request.file_type, cvm_request.method, cvm_request.request_date, 
	cvm_request.reference_date, cvm_request.status, cvm_request.message,
	cvm_download_register.status as download_status,
	cvm_download_register.download_time,
	cvm_file_register.gen_date, cvm_file_register.nr_entries
FROM cvm_request
LEFT JOIN cvm_download_register
ON 
	cvm_download_register.fk_request = cvm_request.id_request
LEFT JOIN cvm_file_register
ON 
	cvm_download_register.id_download_register = cvm_file_register.fk_download_register	
WHERE
	cvm_request.method = '$method'
	AND cvm_request.file_type = '$file_type'
	AND DATE_FORMAT(cvm_request.reference_date, '%Y-%m') = '$month'
GROUP BY
	reference_date
SQL;

	$dbconn = db_open_connection();
	$result = mysql_query($query);
	while ( $row = mysql_fetch_array($result) ) {
		$res[] = array(
			file_type => $row[file_type],
			method => $row[method],
			reference_date => $row[reference_date],
			request_date => $row[request_date],
			status => $row[status],
			message => $row[message],
			download_status => $row[download_status],
			download_time => $row[download_time],
			gen_date => $row[gen_date],
			nr_entries => $row[nr_entries],
		);
	}
	// $errno = mysql_errno($dbconn);
	// if ( $errno != 0 )
	// 	$errors[] = $errno;
	mysql_close($dbconn);
	return $res;
}

function cvm_check_imported_files($xml_paths) {
	$dbconn = db_open_connection();
	
	foreach ($xml_paths as $xml_file) {
		preg_match('/-(\d{8})\.xml$/', $xml_file, $regs);
		$date = DateTime::createFromFormat('Ymd', $regs[1]);
		$result = mysql_query( query_check_informe_diario_date($date->format('Y-m-d')) );
		while ( $row = mysql_fetch_array($result) ) {
			$res[$xml_file] = $row[0];
		}
		$errno = mysql_errno($dbconn);
		if ( $errno != 0 ) {
			$errors[] = array($errno, $date);
		}
	}
	
	mysql_close($dbconn);
	
	return array($res, $errors);
}

function cvm_check_imported_files_by_month($dates) {
	global $month;
	$dbconn = db_open_connection();
	$date = DateTime::createFromFormat('Y-m-d', "$month-01");
	$errors = array();
	
	foreach ($dates as $datestr) {
		$result = mysql_query( query_check_informe_diario_date($datestr) );
		if ( $row = mysql_fetch_array($result) ) {
			$res[$datestr] = $row[0];
		}
		($errno = mysql_errno($dbconn)) == 0 or ($errors[] = array($errno, $datestr));
	}
	
	mysql_close($dbconn);
	
	return array($res, $errors);
}

function cvm_check_downloaded_files_by_month($dates) {
	global $cvm;
	
	$pwd = getcwd();
	chdir(CVM_PATH);
	foreach ($dates as $datekey) {
		$date = DateTime::createFromFormat('Y-m-d', $datekey);
		$datestr = $date->format('Ymd');
		$fname = $cvm['prefix'].$cvm['informes-diarios-prefix']."$datestr.xml";
		if ( is_file( $fname ) ) {
			$res[$datekey] = $fname;
		} else {
			$res[$datekey] = "";
		}
	}
	chdir($pwd);
	return $res;
}

// function cvm_insert_download_register($xml_file) {
// 	
// }

// function query_insert_download_register($register) {
// 	$query = <<<SQL	
// insert into cvm_download_register
// 	(reference_date, download_date, gen_date, nr_registers)
// values
// 	('$register->reference_date', '$register->download_date', 
// 		'$register->gen_date', '$register->nr_registers')
// SQL;
// 	return $query;
// }

function query_insert_cvm_request($req) {
	$query = <<<SQL
insert into cvm_request
	(method, file_type, request_date, reference_date, message, status)
values
	('$req[method]', '$req[file_type]', '$req[request_date]', 
		'$req[reference_date]', '$req[message]', '$req[status]')
SQL;
	return $query;
}

function query_insert_informe_diario($informe) {
	$query = <<<SQL
insert into informes_diarios
	(CNPJ_FDO, DT_COMPTC, VL_TOTAL, VL_QUOTA, PATRIM_LIQ, CAPTC_DIA, RESG_DIA, NR_COTST)
values
	('$informe[CNPJ_FDO]', '$informe[DT_COMPTC]', '$informe[VL_TOTAL]',
		'$informe[VL_QUOTA]', '$informe[PATRIM_LIQ]', '$informe[CAPTC_DIA]',
		'$informe[RESG_DIA]', '$informe[NR_COTST]')
SQL;
	return $query;
}

function query_check_informe_diario_date($date) {
	$query = <<<SQL
select count(CNPJ_FDO) from informes_diarios where DT_COMPTC = '$date'
SQL;
	return $query;
}

function get_business_days_in_month($month) {
	// print_r(date_parse($month));
	// echo '<br>';
	// $date = DateTime::createFromFormat('Y-m', $month);
	$date = new DateTime("$month-01");
	// print_r($date);
	// echo '<br>';
	// $date->modify('first day of');
	// print_r($date);
	// echo '<br>';
	$dates = array();
	
	function get_dates_o($month, $date) {
		global $dates;
		if ($date->format('Y-m') != $month) {
			return $dates;
		} else {
			$weekday = $date->format('D');
			if ( ! ($weekday == "Sun" or $weekday == "Sat") ) $dates[] = $date->format('Y-m-d');
			return get_dates_o($month, $date->modify('next day'));
		}
	}
	
	return get_dates_o($month, $date);
}

?>
