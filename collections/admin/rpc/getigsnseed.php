<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');

$collid = $_REQUEST['collid'];
$ns = $_REQUEST['ns'];

if(is_numeric($collid)){
	$sesarManager = new OccurrenceSesar();
	$sesarManager->setCollid($collid);
	$sesarManager->setNamespace($ns);
	$seed = $sesarManager->generateIgsnSeed();
	echo $seed;
}
?>