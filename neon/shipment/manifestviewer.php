<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestviewer.php');

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$shipmentPK = array_key_exists("shipmentPK",$_REQUEST)?$_REQUEST["shipmentPK"]:"";

$shipManager = new ShipmentManager();
$shipManager->setShipmentPK($shipmentPK);

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
	<style type="text/css">
		fieldset{ padding:15px }
		.fieldGroupDiv{ clear:both; margin-top:2px; height: 25px; }
		.fieldDiv{ float:left; margin-left: 10px}
		.displayFieldDiv{ margin-bottom: 3px }
	</style>
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
		<form action="manifestviewer.php" method="post">
			<div class="fieldGroupDiv">
				<div class="fieldDiv">
					<b>Shipment ID:</b> <input name="shipmentID" type="text" value="<?php echo (isset($_POST['shipmentID'])?$_POST['shipmentID']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Domain ID:</b> <input name="domainID" type="text" value="<?php echo (isset($_POST['domainID'])?$_POST['domainID']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Date Shipped:</b> <input name="dateShippedStart" type="date" value="<?php echo (isset($_POST['dateShippedStart'])?$_POST['dateShippedStart']:''); ?>" /> -
					<input name="dateShippedEnd" type="date" value="<?php echo (isset($_POST['dateShippedEnd'])?$_POST['dateShippedEnd']:''); ?>" />
				</div>
			</div>
			<div class="fieldGroupDiv">
				<div class="fieldDiv">
					<b>Sender ID:</b> <input name="senderID" type="text" value="<?php echo (isset($_POST['senderID'])?$_POST['senderID']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Tracking Number:</b> <input name="trackingNumber" type="text" value="<?php echo (isset($_POST['trackingNumber'])?$_POST['trackingNumber']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Sample ID:</b> <input name="sampleID" type="text" value="<?php echo (isset($_POST['sampleID'])?$_POST['sampleID']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Sample Class:</b> <input name="sampleClass" type="text" value="<?php echo (isset($_POST['sampleClass'])?$_POST['sampleClass']:''); ?>" />
				</div>
			</div>
			<div class="fieldGroupDiv">
				<div class="fieldDiv">
					<b>Named Location:</b> <input name="namedLocation" type="text" value="<?php echo (isset($_POST['namedLocation'])?$_POST['namedLocation']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Date Collected:</b> <input name="collectDateStart" type="date" value="<?php echo (isset($_POST['collectDateStart'])?$_POST['collectDateStart']:''); ?>" /> -
					<input name="collectDateEnd" type="date" value="<?php echo (isset($_POST['collectDateEnd'])?$_POST['collectDateEnd']:''); ?>" />
				</div>
				<div class="fieldDiv"><b>Imported/Modified By:</b>
					<select name="importedUid">
						<option value="">Select User</option>
						<option value="">------------------------</option>
						<?php
						$userImportArr = $shipManager->getImportUserArr();
						foreach($userImportArr as $uid => $userName){
							echo '<option value="'.$uid.'" '.(isset($_POST['importedUid'])&&$uid==$_POST['importedUid']?'SELECTED':'').'>'.$userName.'</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="fieldGroupDiv">
				<div class="fieldDiv">
					<b>Sample Code:</b> <input name="sampleCode" type="text" value="<?php echo (isset($_POST['sampleCode'])?$_POST['sampleCode']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Taxon ID:</b> <input name="taxonID" type="text" value="<?php echo (isset($_POST['taxonID'])?$_POST['taxonID']:''); ?>" />
				</div>
				<div class="fieldDiv">
					<b>Checked-in By</b>
					<select name="checkinUid">
						<option value="">Select User</option>
						<option value="">------------------------</option>
						<?php
						$usercheckinArr = $shipManager->getCheckinUserArr();
						foreach($usercheckinArr as $uid => $userName){
							echo '<option value="'.$uid.'" '.(isset($_POST['checkinUid'])&&$uid==$_POST['checkinUid']?'SELECTED':'').'>'.$userName.'</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div style="clear:both; margin:20px">
				<input name="action" type="submit" value="Display Manifests" />
			</div>
		</form>
	</fieldset>
	<?php
	if($isEditor){
		$shipManager->setShipmentPK($shipmentPK);
		$shipmentDetails = $shipManager->getShipmentArr($_POST);
		if($shipmentPK){
			$shipArr = array_pop($shipmentDetails);
			?>
			<fieldset style="margin-top:30px">
				<legend><b>Shipment #<?php echo $shipmentPK; ?></b></legend>
				<div style="">
					<div class="displayFieldDiv"><b>Shipment ID:</b> <?php echo $shipArr['shipmentID']; ?></div>
					<div class="displayFieldDiv"><b>Domain ID:</b> <?php echo $shipArr['domainID']; ?></div>
					<div class="displayFieldDiv"><b>Date Shipped:</b> <?php echo $shipArr['dateShipped']; ?></div>
					<div class="displayFieldDiv"><b>Sender ID:</b> <?php echo $shipArr['senderID']; ?></div>
					<div class="displayFieldDiv"><b>Shipment Service:</b> <?php echo $shipArr['shipmentService']; ?></div>
					<div class="displayFieldDiv"><b>Shipment Method:</b> <?php echo $shipArr['shipmentMethod']; ?></div>
					<div class="displayFieldDiv"><b>Tracking Number:</b> <a href=""><?php echo $shipArr['trackingNumber']; ?></a></div>
					<?php
					if($shipArr['shipmentNotes']) echo '<div class="displayFieldDiv"><b>Shipment Notes:</b> '.$shipArr['shipmentNotes'].'</div>';
					?>
					<div class="displayFieldDiv"><b>Sample ID:</b> <?php echo $shipArr['sampleID']; ?></div>
					<?php
					if($shipArr['sampleCode']) '<div class="displayFieldDiv"><b>Sample Code:</b> '.$shipArr['sampleCode'].'</div>';
					?>
					<div class="displayFieldDiv"><b>Sample Class:</b> <?php echo $shipArr['sampleClass']; ?></div>
					<?php
					if($shipArr['taxonID']) '<div class="displayFieldDiv"><b>Taxon ID:</b> '.$shipArr['taxonID'].'</div>';
					if($shipArr['individualCount']) '<div class="displayFieldDiv"><b>Individual Count:</b> '.$shipArr['individualCount'].'</div>';
					if($shipArr['filterVolume']) '<div class="displayFieldDiv"><b>Filter Volume:</b> '.$shipArr['filterVolume'].'</div>';
					?>
					<div class="displayFieldDiv"><b>Named Location:</b> <?php echo $shipArr['namedLocation']; ?></div>
					<?php
					if($shipArr['domainRemarks']) '<div class="displayFieldDiv"><b>Domain Remarks:</b> '.$shipArr['domainRemarks'].'</div>';
					if($shipArr['collectDate']) '<div class="displayFieldDiv"><b>Collect Date:</b> '.$shipArr['collectDate'].'</div>';
					?>
					<div class="displayFieldDiv"><b>Quarantine Status:</b> <?php echo $shipArr['quarantineStatus']; ?></div>
					<?php
					if($shipArr['sampleNotes']) '<div class="displayFieldDiv"><b>Sample Notes:</b> '.$shipArr['sampleNotes'].'</div>';
					?>
				</div>
				<div style="margin-top:15px;">
					<?php
					if($shipArr['checkinTS']) '<div class="displayFieldDiv"><b>Check-in Timestamp:</b> '.$shipArr['checkinTS'].'</div>';
					if($shipArr['checkinUser']) '<div class="displayFieldDiv"><b>Check-in User:</b> '.$shipArr['checkinUser'].'</div>';
					if($shipArr['importUser']) '<div class="displayFieldDiv"><b>Import User:</b> '.$shipArr['importUser'].'</div>';
					if($shipArr['ts']) '<div class="displayFieldDiv"><b>Import Date:</b> '.$shipArr['ts'].'</div>';
					if($shipArr['modifiedUser']) echo '<div class="displayFieldDiv"><b>Modified By User:</b> '.$shipArr['modifiedUser'].'</div>';
					$sampleCntArr = $shipManager->getSampleCount();
					echo '<div class="displayFieldDiv"><b>Total Sample Count:</b> '.$sampleCntArr['cnt'].'</div>';
					unset($sampleCntArr['cnt']);
					$notCheckedIn = 0;
					if(isset($sampleCntArr[0])){
						$notCheckedIn = $sampleCntArr[0];
						unset($sampleCntArr[0]);
					}
					foreach($sampleCntArr as $checkinUser => $checkinArr){
						foreach($checkinArr as $checkinTS => $checkinCnt){
							echo '<div class="displayFieldDiv"><b>Checked-in:</b> '.$checkinCnt.' ('.$checkinTS.' by '.$checkinUser.')</div>';
						}
					}
					if($notCheckedIn) echo '<div class="displayFieldDiv"><b>Not checked-in:</b> '.$notCheckedIn.' (<a href="samplecheckin.php?shipmentpk='.$shipmentPK.'">check-in</a>)</div>';
					?>
					<fieldset>
						<legend><b>Sample Listing</b></legend>
						<table class="styledtable">
							<tr><th>Sample ID</th><th>Sample Code</th><th>Sample Class</th><th>Taxon ID</th><th>Named Location</th><th>Collect Date</th><th>Quarantine Status</th><th>Check-in ts</th></tr>
							<?php
							$sampleList = $shipManager->getSampleArr();
							foreach($sampleList as $samplePK => $sampleArr){
								echo '<tr>';
								echo '<td>'.$sampleArr['sampleID'].'</td>';
								echo '<td>'.$sampleArr['sampleCode'].'</td>';
								echo '<td>'.$sampleArr['sampleClass'].'</td>';
								echo '<td>'.$sampleArr['taxonID'].'</td>';
								echo '<td>'.$sampleArr['namedLocation'].'</td>';
								echo '<td>'.$sampleArr['collectDate'].'</td>';
								echo '<td>'.$sampleArr['quarantineStatus'].'</td>';
								echo '<td title="'.$sampleArr['checkinUser'].'">'.$sampleArr['checkinTS'].'</td>';
								echo '</tr>'
								echo '<tr style="display:hidden">';
								echo '';
								echo '</tr>';



								$retArr[$r->samplePK]['checkinUser'] = $r->checkinUser;
								$retArr[$r->samplePK]['individualCount'] = $r->individualCount;
								$retArr[$r->samplePK]['filterVolume'] = $r->filterVolume;
								$retArr[$r->samplePK]['domainRemarks'] = $r->domainRemarks;
								$retArr[$r->samplePK]['sampleNotes'] = $r->sampleNotes;
							}
							?>
						</table>
					</fieldset>
				</div>
			</fieldset>
			<?php
		}
		else{
			//List all manifest matching search criteria
			?>
			<fieldset style="margin-top:30px;padding:15px">
				<legend><b>Shipment Listing</b></legend>
				<ul>
					<?php
					foreach($shipmentDetails as $shipPK => $shipArr){
						echo '<li><a href="manifestviewer.php?shipmentPK='.$shipPK.'">#'.$shipPK.': '.$shipArr['shipmentID'].'</a> ('.$shipArr['ts'].')</li>';
					}
					?>
				</ul>
			</fieldset>
			<?php
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