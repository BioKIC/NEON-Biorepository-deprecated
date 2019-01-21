<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$shipmentPK = $_REQUEST["shipmentpk"];
$barcode = $_REQUEST["barcode"];

$status = '';
if($IS_ADMIN){
	$shipmentManager = new ShipmentManager();
	$shipmentManager->setShipmentPK($shipmentPK);
	$json = $shipmentManager->checkinSample($barcode);
}
echo $json;
?>