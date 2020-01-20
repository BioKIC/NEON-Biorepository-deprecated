<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');

$target = $_REQUEST['target'];
$occid = $_REQUEST['occid'];

$retCode = 0;
if($IS_ADMIN && is_numeric($target) && is_numeric($occid)){
	$shipmentManager = new ShipmentManager();
	$retCode = $shipmentManager->transferOccurrence($occid, $target);
}
echo $retCode;
?>