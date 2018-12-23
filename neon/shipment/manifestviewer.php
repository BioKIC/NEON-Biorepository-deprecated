<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestviewer.php');

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$shipmentID = array_key_exists("shipmentid",$_REQUEST)?$_REQUEST["shipmentid"]:"";

$shipManager = new ShipmentManager();
$shipManager->setShipmentPK($shipmentID);

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
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
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
	<a href="../index.php"><b>NEON Biorepository Tools</b></a> &gt;&gt;
	<b>Manifest Loader</b>
</div>
<div id="innertext">
	<fieldset>
		<legend><b>Shipment Filter</b></legend>
		<div><b>Shipment ID:</b> <input name="shipmentid" type="text" value="<?php echo $shipmentID; ?>" /></div>
		<div><b>Domain ID:</b> <input name="domainid" type="text" value="<?php echo $domainID; ?>" /></div>
		<div>
			<b>Date Shipped:</b> <input name="dateshippedstart" type="date" value="<?php echo $dateShippedStart; ?>" /> -
			<input name="dateshippedend" type="date" value="<?php echo $dateShippedEnd; ?>" />
		</div>
		<div><b>Sender ID:</b> <input name="senderid" type="text" value="<?php echo $senderID; ?>" /></div>
		<div><b>Tracking Number:</b> <input name="trackingnumber" type="text" value="<?php echo $trackingNumber; ?>" /></div>
		<div><b>Sample ID:</b> <input name="sampleid" type="text" value="<?php echo $sampleID; ?>" /></div>
		<div><b>Sample Class:</b> <input name="sampleClass" type="text" value="<?php echo $sampleClass; ?>" /></div>
		<div><b>Named Location:</b> <input name="namedlocation" type="text" value="<?php echo $namedLocation; ?>" /></div>
		<div>
			<b>Date Collected:</b> <input name="collectdatestart" type="text" value="<?php echo $collectDateStart; ?>" /> -
			<input name="collectdateend" type="text" value="<?php echo $collectDateEnd; ?>" />
		</div>
		<div>
			<select name="importedby">
				<option>Imported/Modified By</option>
				<option>------------------------</option>
				<?php
				$userArr = $shipManager->getUserArr();
				foreach($userArr as $uid => $userName){
					echo '<option value="'.$uid.'">'.$userName.'</option>';
				}
				?>
			</select>
		</div>
		<div><b>Sample Code:</b> <input name="samplecode" type="text" value="<?php echo $sampleCode; ?>" /></div>
		<div><b>Taxon ID:</b> <input name="taxonid" type="text" value="<?php echo $taxonID; ?>" /></div>
		<div>
			<select name="importedby">
				<option>Imported/Modified By</option>
				<option>------------------------</option>
				<?php
				reset($userArr);
				foreach($userArr as $uid => $userName){
					echo '<option value="'.$uid.'">'.$userName.'</option>';
				}
				?>
			</select>
		</div>



shipmentid		equals
domainid		equals
dateshipped		date, range
senderid		equals
trackingnumber	equals
sampleid		like
sampleClass		like
namedlocation	equals
collectdate		date, range

imported or modified by		select, uid/user list
sampleCode 					equals
taxonID						equals
checkinUid					select, uid/user list

	</fieldset>
	<?php
	if($isEditor){
		if($shipmentID){
			$shipManager->setShipmentPK($shipmentID);
			$shipmentDetails = $shipManager->getShipmentArr();
			$shipArr = array_pop($shipmentDetails)
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
		else{
			//List all manifest matching search criteria
			$filterCriteria = array();
			$shipmentDetails = $shipManager->getShipmentArr($filterCriteria);
			foreach($shipmentDetails as $shipID => $shipArr){
				echo '<div><a href="manifestviewer.php?shipmentpk='.$shipArr['shipmentID'].'">'.$shipArr['shipmentID'].'</a> - '.$shipArr['ts'].'</div>';

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