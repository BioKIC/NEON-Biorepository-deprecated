<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$shipmentPK = $_REQUEST['shipmentpk'];
$barcode = $_REQUEST['barcode'];
$condition = (array_key_exists('condition',$_REQUEST)?$_REQUEST['condition']:'');
$notes = (array_key_exists('notes',$_REQUEST)?$_REQUEST['notes']:'');

$status = '';
if($IS_ADMIN){
	$shipmentManager = new ShipmentManager();
	$shipmentManager->setShipmentPK($shipmentPK);
	$json = $shipmentManager->checkinSample($barcode,$condition,$notes);
}
echo $json;
?>