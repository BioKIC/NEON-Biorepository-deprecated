<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$classNew = $_REQUEST['classnew'];
$samplePK = $_REQUEST['samplepk'];

$json = '{}';
if($IS_ADMIN && $classNew){
	$shipmentManager = new ShipmentManager();
	$json = $shipmentManager->confirmCollectionTransfer($samplePK,$classNew);
}
echo $json;
?>