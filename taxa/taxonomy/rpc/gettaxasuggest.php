<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyDisplayManager.php');

$term = $_REQUEST['term'];
$taxAuthId = array_key_exists('taid',$_REQUEST)?$_REQUEST['taid']:0;
$rankLimit = array_key_exists('rlimit',$_REQUEST)?$_REQUEST['rlimit']:0;
$rankLow = array_key_exists('rlow',$_REQUEST)?$_REQUEST['rlow']:0;
$rankHigh = array_key_exists('rhigh',$_REQUEST)?$_REQUEST['rhigh']:0;

$taxonomyManager = new TaxonomyDisplayManager();
$taxonomyManager->setTargetStr($term);
$taxonomyManager->setTaxAuthId($taxAuthId);
$retArr = $taxonomyManager->getTaxaSuggest($rankLimit, $rankLow, $rankHigh);

echo json_encode($retArr);
?>