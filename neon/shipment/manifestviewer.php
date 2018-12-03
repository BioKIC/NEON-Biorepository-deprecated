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
<?php
if($isEditor){
	?>
	<div id="innertext">
		<div style="margin:30px;">
			<?php
			if($action == 'List Shipments'){
				$shipmentArr = $shipManager->getShipmentArr();
				foreach($manifestArr as $id => $manifestArr){

				}
			}
			?>
		</div>
	</div>
	<?php
}
else{
	?>
	<div style='font-weight:bold;margin:30px;'>
		You do not have permissions to view manifests
	</div>
	<?php
}
include($SERVER_ROOT.'/footer.php');
?>
</body>
</html>