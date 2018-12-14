<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestviewer.php');

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$shipmentId = array_key_exists("shipmentid",$_REQUEST)?$_REQUEST["shipmentid"]:"";

$shipManager = new ShipmentManager();
$shipManager->setShipmentPK($shipmentId);

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}

$status = "";
if($isEditor){
	if($action == 'downloadcsv'){
		$shipManager->exportShipmentList();
		exit;
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Manifest Viewer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="../css/jquery-ui.css" rel="stylesheet" type="text/css" />
	<script src="../js/jquery.js" type="text/javascript"></script>
	<script src="../js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/header.php');
?>
<div class="navpath">
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="index.php"><b>NEON Biorepository Tools</b></a> &gt;&gt;
	<b>Manifest Loader</b>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		if($shipmentId){
			$shipManager->setShipmentPK($shipmentId);
			$shipmentDetails = $shipManager->getShipmentArr();
			foreach($shipmentDetails as $shipID => $shipArr){
				echo '<div><b>shipmentID</b>'.$shipArr['shipmentID'].'</div>';
				echo '<div><b>domainID</b>'.$shipArr['domainID'].'</div>';
				echo '<div><b>dateShipped</b>'.$shipArr['dateShipped'].'</div>';
				echo '<div><b>senderID</b>'.$shipArr['senderID'].'</div>';
				echo '<div><b>shipmentService</b>'.$shipArr['shipmentService'].'</div>';
				echo '<div><b>shipmentMethod</b>'.$shipArr['shipmentMethod'].'</div>';
				echo '<div><b>trackingNumber</b>'.$shipArr['trackingNumber'].'</div>';
				echo '<div><b>importUser</b>'.$shipArr['importUser'].'</div>';
				echo '<div><b>modifiedUser</b>'.$shipArr['modifiedUser'].'</div>';
				echo '<div><b>Upload Date</b>'.$shipArr['ts'].'</div>';
			}
		}
		else{
			//List all manifest matching search criteria
			if($action == 'List Shipments'){
				$shipmentDetails = $shipManager->getShipmentArr();
				foreach($shipmentDetails as $id => $manifestArr){
					echo '<div><a href="manifestviewer.php?shipmentpk='.$shipArr['shipmentID'].'">'.$shipArr['shipmentID'].'</a> - '.$shipArr['ts'].'</div>';

				}
			}
		}
	}
	else{
		?>
		<div style='font-weight:bold;margin:30px;'>
			You do not have permissions to view manifests
		</div>
		<?php
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/footer.php');
?>
</body>
</html>