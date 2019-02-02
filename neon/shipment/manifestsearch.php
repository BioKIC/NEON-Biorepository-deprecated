<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestsearch.php');

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
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Manifest Viewer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="../../js/jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function fullResetForm(f){
			f.shipmentID.value = "";
			f.sampleID.value = "";
			f.sampleCode.value = "";
			f.domainID.value = "";
			f.namedLocation.value = "";
			f.sampleClass.value = "";
			f.taxonID.value = "";
			f.trackingNumber.value = "";
			f.dateShippedStart.value = "";
			f.dateShippedEnd.value = "";
			f.checkinUid.value = "";
			f.importedUid.value = "";
			f.submit();
		}
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
	<a href="../index.php">NEON Biorepository Tools</a> &gt;&gt;
	<a href="manifestsearch.php"><b>Manifest Search</b></a>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		$postArr = array();
		foreach($_POST as $k => $v){
			$postArr[$k] = trim($v);
		}
		?>
		<fieldset>
			<legend><b>Shipment Filter</b></legend>
			<form action="manifestsearch.php" method="post">
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Shipment ID:</b> <input name="shipmentID" type="text" value="<?php echo (isset($postArr['shipmentID'])?$postArr['shipmentID']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Sample ID:</b> <input name="sampleID" type="text" value="<?php echo (isset($postArr['sampleID'])?$postArr['sampleID']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Sample Code:</b> <input name="sampleCode" type="text" value="<?php echo (isset($postArr['sampleCode'])?$postArr['sampleCode']:''); ?>" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Domain ID:</b> <input name="domainID" type="text" value="<?php echo (isset($postArr['domainID'])?$postArr['domainID']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Site ID:</b> <input name="namedLocation" type="text" value="<?php echo (isset($postArr['namedLocation'])?$postArr['namedLocation']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Sample Class:</b> <input name="sampleClass" type="text" value="<?php echo (isset($postArr['sampleClass'])?$postArr['sampleClass']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Taxon ID:</b> <input name="taxonID" type="text" value="<?php echo (isset($postArr['taxonID'])?$postArr['taxonID']:''); ?>" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Tracking Number:</b> <input name="trackingNumber" type="text" value="<?php echo (isset($postArr['trackingNumber'])?$postArr['trackingNumber']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Date Shipped:</b> <input name="dateShippedStart" type="date" value="<?php echo (isset($postArr['dateShippedStart'])?$postArr['dateShippedStart']:''); ?>" /> -
						<input name="dateShippedEnd" type="date" value="<?php echo (isset($postArr['dateShippedEnd'])?$postArr['dateShippedEnd']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<select name="checkinUid" style="margin:5px 10px">
							<option value="">------------------------</option>
							<option value="">Checked-in By</option>
							<?php
							$usercheckinArr = $shipManager->getCheckinUserArr();
							foreach($usercheckinArr as $uid => $userName){
								echo '<option value="'.$uid.'" '.(isset($postArr['checkinUid'])&&$uid==$postArr['checkinUid']?'SELECTED':'').'>'.$userName.'</option>';
							}
							?>
						</select>
					</div>
					<div class="fieldDiv">
						<select name="importedUid" style="margin:5px 10px">
							<option value="">------------------------</option>
							<option value="">Imported/Modified By</option>
							<?php
							$userImportArr = $shipManager->getImportUserArr();
							foreach($userImportArr as $uid => $userName){
								echo '<option value="'.$uid.'" '.(isset($postArr['importedUid'])&&$uid==$postArr['importedUid']?'SELECTED':'').'>'.$userName.'</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<input name="manifeststatus" type="radio" value="shipNotCheck" /> <b>Shipment not Checked-in</b>
						<select name="sampleCondition" style="margin:5px 10px">
							<option value="">Sample Condition</option>
							<option value="">------------------------</option>
							<?php
							if($condArr = $shipManager->getConditionAppliedArr()){
								foreach($condArr as $condKey => $condValue){
									echo '<option value="'.$condKey.'">'.$condValue.'</option>';
								}
							}
							else{
								echo '<option value="">Sample Conditions have not been set</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<input name="manifeststatus" type="radio" value="shipNotCheck" /> <b>Shipment not Checked-in</b>
						<input name="manifeststatus" type="radio" value="receiptnotsubmitted" style="margin-left:30px;" /> <b>Receipt not submitted to NEON</b>
						<input name="manifeststatus" type="radio" value="sampleNotCheck" style="margin-left:30px;" /> <b>Samples not Checked-in</b>
						<input name="manifeststatus" type="radio" value="occurNotHarvested" style="margin-left:30px;" /> <b>Occurreence not harvested</b>
					</div>
				</div>
				<div style="clear:both; margin:20px">
					<button name="action" type="submit" value="Display Manifests">Display Manifests</button>
					<button type="button" value="Reset" onclick="fullResetForm(this.form)">Reset Form</button>
				</div>
			</form>
		</fieldset>
		<fieldset style="margin-top:30px;padding:15px">
			<legend><b>Shipment Listing</b></legend>
			<ul>
				<?php
				if($shipmentDetails = $shipManager->getShipmentList($postArr)){
					foreach($shipmentDetails as $shipPK => $shipArr){
						echo '<li><a href="manifestviewer.php?shipmentPK='.$shipPK.'">'.$shipArr['id'].'</a> ('.$shipArr['ts'].')</li>';
					}
				}
				else{
					echo '<div>No manifest matching search criteria</div>';
				}
				?>
			</ul>
		</fieldset>
		<?php
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