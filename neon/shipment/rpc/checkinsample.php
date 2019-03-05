<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$shipmentPK = $_REQUEST['shipmentpk'];
$sampleIdentifier = $_REQUEST['idenfier'];
$acceptedForAnalysis = (array_key_exists('accepted',$_REQUEST)?$_REQUEST['accepted']:'');
$condition = (array_key_exists('condition',$_REQUEST)?$_REQUEST['condition']:'');
$alternativeSampleID = (array_key_exists('altSampleID',$_REQUEST)?$_REQUEST['altSampleID']:'');
$notes = (array_key_exists('notes',$_REQUEST)?$_REQUEST['notes']:'');

$status = '';
if($IS_ADMIN){
	$shipmentManager = new ShipmentManager();
	$shipmentManager->setShipmentPK($shipmentPK);
	$json = $shipmentManager->checkinSample($sampleIdentifier,$acceptedForAnalysis,$condition,$alternativeSampleID,$notes);
}
echo $json;
?>