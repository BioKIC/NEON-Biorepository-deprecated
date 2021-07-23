<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistVoucherPensoft.php');

$clid = $_REQUEST['clid'];
$rType = $_REQUEST['rtype'];

if($rType == 'pensoftxlsx'){
	$vManager = null;
	if(version_compare(phpversion(), '5.6', '<')) {
		echo 'ERROR: Excel export feature requires PHP version 5.6 or greater';
		exit;
	}
	$vManager = new ChecklistVoucherPensoft();
	$vManager->setClid($clid);
	$vManager->setCollectionVariables();
	$vManager->downloadPensoftXlsx();
}
else{
	$vManager = new ChecklistVoucherReport();
	$vManager->setClid($clid);
	$vManager->setCollectionVariables();
	if($rType == 'fullcsv'){
		$vManager->downloadChecklistCsv();
	}
	elseif($rType == 'fullvoucherscsv'){
		$vManager->downloadVoucherCsv();
	}
	elseif($rType == 'fullalloccurcsv'){
		$vManager->downloadAllOccurrenceCsv();
	}
	elseif($rType == 'missingoccurcsv'){
		$vManager->exportMissingOccurCsv();
	}
	elseif($rType == 'problemtaxacsv'){
		$vManager->exportProblemTaxaCsv();
	}
}
?>