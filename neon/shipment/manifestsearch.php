<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestsearch.php?'.$_SERVER['QUERY_STRING']);

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
	if($action == 'exportManifests'){
		$shipManager->exportShipmentList();
	}
	elseif($action == 'exportSamples'){
		$shipManager->exportSampleList();
	}
	elseif($action == 'exportOccurrences'){
		$shipManager->exportOccurrenceList();
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
			f.sampleCondition.value = "";
			var radioList = document.getElementsByName('manifestStatus');
			for(x = 0; x < radioList.length; x++){
				radioList[x].checked = false;
			}
			f.submit();
		}

		function copyUrl($urlFrag){
			var $temp = $("<input>");
			$("body").append($temp);
			var activeLink = "<?php echo $_SERVER['HTTP_HOST'].$CLIENT_ROOT; ?>/neon/shipment/manifestsearch.php?shipmentID="+$urlFrag;
			$temp.val(activeLink).select();
			document.execCommand("copy");
			$temp.remove();
			$("#copiedDiv").show().delay(2000).fadeOut();
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
		$shipmentDetails = $shipManager->getShipmentList();
		$searchArgumentArr = $shipManager->getSearchArr();
		?>
		<fieldset style="position: relative">
			<legend><b>Shipment Filter</b></legend>
			<?php
			$searchArgument = $shipManager->getSearchArgumentStr();
			if($searchArgument){
				?>
				<div style="position:absolute;top:20px;right:10px;">
					<div id="copiedDiv" style="float:left;display:none;margin-right:15px;font-size:80%">URL copied to clipboard</div>
					<a href="#" onclick="copyUrl('<?php echo $searchArgument; ?>')" title="Copy URL to Clipboard">
						<img src="../../images/link2.png" style="width:15px;" />
					</a>
				</div>
				<?php
			}
			?>
			<form action="manifestsearch.php" method="post" style="float:left">
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Shipment ID:</b> <input name="shipmentID" type="text" value="<?php echo (isset($searchArgumentArr['shipmentID'])?$searchArgumentArr['shipmentID']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Sample ID:</b> <input name="sampleID" type="text" value="<?php echo (isset($searchArgumentArr['sampleID'])?$searchArgumentArr['sampleID']:''); ?>" style="width:225px" />
					</div>
					<div class="fieldDiv">
						<b>Sample Code:</b> <input name="sampleCode" type="text" value="<?php echo (isset($searchArgumentArr['sampleCode'])?$searchArgumentArr['sampleCode']:''); ?>" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Domain ID:</b> <input name="domainID" type="text" value="<?php echo (isset($searchArgumentArr['domainID'])?$searchArgumentArr['domainID']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Site ID:</b> <input name="namedLocation" type="text" value="<?php echo (isset($searchArgumentArr['namedLocation'])?$searchArgumentArr['namedLocation']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Sample Class:</b> <input name="sampleClass" type="text" value="<?php echo (isset($searchArgumentArr['sampleClass'])?$searchArgumentArr['sampleClass']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Taxon ID:</b> <input name="taxonID" type="text" value="<?php echo (isset($searchArgumentArr['taxonID'])?$searchArgumentArr['taxonID']:''); ?>" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Tracking Number:</b> <input name="trackingNumber" type="text" value="<?php echo (isset($searchArgumentArr['trackingNumber'])?$searchArgumentArr['trackingNumber']:''); ?>" />
					</div>
					<div class="fieldDiv">
						<b>Date Shipped:</b> <input name="dateShippedStart" type="date" value="<?php echo (isset($searchArgumentArr['dateShippedStart'])?$searchArgumentArr['dateShippedStart']:''); ?>" /> -
						<input name="dateShippedEnd" type="date" value="<?php echo (isset($searchArgumentArr['dateShippedEnd'])?$searchArgumentArr['dateShippedEnd']:''); ?>" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Checked In by: </b>
						<select name="checkinUid" style="margin:5px 10px">
							<option value="">All Records</option>
							<option value="">------------------------</option>
							<?php
							$usercheckinArr = $shipManager->getCheckinUserArr();
							foreach($usercheckinArr as $uid => $userName){
								echo '<option value="'.$uid.'" '.(isset($searchArgumentArr['checkinUid'])&&$uid==$searchArgumentArr['checkinUid']?'SELECTED':'').'>'.$userName.'</option>';
							}
							?>
						</select>
					</div>
					<div class="fieldDiv">
						<b>Imported/Modified by:</b>
						<select name="importedUid" style="margin:5px 10px">
							<option value="">All Records</option>
							<option value="">------------------------</option>
							<?php
							$userImportArr = $shipManager->getImportUserArr();
							foreach($userImportArr as $uid => $userName){
								echo '<option value="'.$uid.'" '.(isset($searchArgumentArr['importedUid'])&&$uid==$searchArgumentArr['importedUid']?'SELECTED':'').'>'.$userName.'</option>';
							}
							?>
						</select>
					</div>
					<div class="fieldDiv">
						<b>Sample Condition:</b>
						<select name="sampleCondition" style="margin:5px 10px">
							<option value="">All Records</option>
							<option value="">------------------------</option>
							<?php
							if($condArr = $shipManager->getConditionAppliedArr()){
								foreach($condArr as $condKey => $condValue){
									echo '<option value="'.$condKey.'" '.(isset($searchArgumentArr['sampleCondition'])&&$condKey==$searchArgumentArr['sampleCondition']?'SELECTED':'').'>'.$condValue.'</option>';
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
						<?php
						$manifestStatus = isset($searchArgumentArr['manifestStatus'])?$searchArgumentArr['manifestStatus']:'';
						?>
						<input name="manifestStatus" type="radio" value="shipNotCheck" <?php echo ($manifestStatus=='shipNotCheck'?'checked':''); ?> /> <b>Shipment Not Checked In</b>
						<input name="manifestStatus" type="radio" value="receiptNotSubmitted" <?php echo ($manifestStatus=='receiptNotSubmitted'?'checked':''); ?> style="margin-left:20px;" /> <b>Receipt Not Submitted</b>
						<input name="manifestStatus" type="radio" value="sampleNotCheck" <?php echo ($manifestStatus=='sampleNotCheck'?'checked':''); ?> style="margin-left:20px;" /> <b>Samples Not Checked In</b>
						<input name="manifestStatus" type="radio" value="nonAcceptedSamples" <?php echo ($manifestStatus=='nonAcceptedSamples'?'checked':''); ?> style="margin-left:20px;" /> <b>Samples Not Accepted for Analysis</b>
						<input name="manifestStatus" type="radio" value="occurNotHarvested" <?php echo ($manifestStatus=='occurNotHarvested'?'checked':''); ?> style="margin-left:20px;" /> <b>Occurrences Not Harvested</b>
					</div>
				</div>
				<div style="clear:both;margin:10px">
					<div style="float:left; margin:10px">
						<button name="action" type="submit" value="listManifests">List Manifests</button>
					</div>
					<div style="float:left; margin:10px">
						<button type="button" value="Reset" onclick="fullResetForm(this.form)">Reset Form</button>
					</div>
				</div>
				<div style="clear:both;margin:10px">
					<div style="float:left; margin:10px">
						<button name="action" type="submit" value="exportSamples">Export Samples</button>
					</div>
					<div style="float:left; margin:10px">
						<button name="action" type="submit" value="exportManifests">Export Manifests</button>
					</div>
					<div style="float:left; margin:10px">
						<button name="action" type="submit" value="exportOccurrences">Export Occurrences</button>
					</div>
				</div>
			</form>
		</fieldset>
		<fieldset style="margin-top:30px;padding:15px">
			<legend><b>Shipment Listing</b></legend>
			<ul>
				<?php
				if($shipmentDetails){
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