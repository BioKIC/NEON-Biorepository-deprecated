<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$shipmentPK = (isset($_POST['shipmentPK'])?$_POST['shipmentPK']:'');
$exportTask = $_POST['exportTask'];

$status = '';
if($IS_ADMIN){
	$shipmentManager = new ShipmentManager();
	if($exportTask == 'receipt'){
		$shipmentManager->setShipmentPK($shipmentPK);
		$shipmentManager->exportShipmentReceipt();
	}
	elseif($exportTask == 'sampleList'){
		$shipmentManager->setShipmentPK($shipmentPK);
		$shipmentManager->exportSampleList();
	}
}
?>