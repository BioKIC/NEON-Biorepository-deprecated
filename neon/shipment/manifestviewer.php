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
	elseif($action == 'checkinShipment'){
		$shipManager->checkinShipment();
	}
	elseif($action == 'batchCheckin'){
		$shipManager->batchCheckinSamples($_POST);
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
		<?php
		if($shipmentPK){
			?>
			function batchCheckinFormVerify(f){
				var formVerified = false;
				for(var h=0;h<f.length;h++){
					if(f.elements[h].name == "scbox[]" && f.elements[h].checked){
						formVerified = true;
						break;
					}
				}
				if(!formVerified){
					alert("Select samples to check-in");
					return false;
				}
			}

			function checkinSample(f){
				var checkingStr = f.checkinField.value.trim();
				if(checkingStr != ""){
					$.ajax({
						type: "POST",
						url: "rpc/checkinsample.php",
						data: { shipmentpk: "<?php echo $shipmentPK; ?>", barcode: f.checkinField.value }
					}).done(function( submitStatus ) {
						if(submitStatus == 0){
							$("#failText").show();
							$("#failText").animate({fontSize: "150%"}, "slow");
							$("#failText").animate({fontSize: "100"}, "slow");
							$("#failText").hide();
						}
						else{
							$("#successText").show();
							$("#successText").animate({fontSize: "150%"}, "slow");
							$("#successText").animate({fontSize: "100"}, "slow");
							$("#successText").hide();
							$("#scbox--"+submitStatus).hide();
							$("#scSpan-"+submitStatus).html("checked-in");
						}
					});
				}
			}

			function selectAll(cbObj){
				var boxesChecked = true;
				if(!cbObj.checked) boxesChecked = false;
				var f = cbObj.form;
				for(var i=0;i<f.length;i++){
					if(f.elements[i].name == "scbox[]") f.elements[i].checked = boxesChecked;
				}
			}
			<?php
		}
		?>
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
	<a href="manifestviewer.php"><b>Manifest Searh</b></a>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		$shipManager->setShipmentPK($shipmentPK);
		$shipmentDetails = $shipManager->getShipmentArr($_POST);
		if($shipmentPK){
			$shipArr = array_pop($shipmentDetails);
			?>
			<fieldset style="margin-top:30px">
				<legend><b>Shipment #<?php echo $shipmentPK; ?></b></legend>
				<div>
					<div style="float:left">
						<div class="displayFieldDiv"><b>Shipment ID:</b> <?php echo $shipArr['shipmentID']; ?></div>
						<div class="displayFieldDiv"><b>Domain ID:</b> <?php echo $shipArr['domainID']; ?></div>
						<div class="displayFieldDiv"><b>Date Shipped:</b> <?php echo $shipArr['dateShipped']; ?></div>
						<div class="displayFieldDiv"><b>Sender ID:</b> <?php echo $shipArr['senderID']; ?></div>
						<div class="displayFieldDiv"><b>Shipment Service:</b> <?php echo $shipArr['shipmentService']; ?></div>
						<div class="displayFieldDiv"><b>Shipment Method:</b> <?php echo $shipArr['shipmentMethod']; ?></div>
						<?php
						if($shipArr['shipmentNotes']) echo '<div class="displayFieldDiv"><b>Shipment Notes:</b> '.$shipArr['shipmentNotes'].'</div>';
						if($shipArr['importUser']) echo '<div class="displayFieldDiv"><b>Import User:</b> '.$shipArr['importUser'].'</div>';
						if($shipArr['ts']) echo '<div class="displayFieldDiv"><b>Import Date:</b> '.$shipArr['ts'].'</div>';
						if($shipArr['modifiedUser']) echo '<div class="displayFieldDiv"><b>Modified By User:</b> '.$shipArr['modifiedUser'].'</div>';
						?>
					</div>
					<div style="margin-left:40px;float:left;">
						<?php
						if($shipArr['checkinTimestamp']){
							if($shipArr['checkinTimestamp']) echo '<div class="displayFieldDiv"><b>Check-in Timestamp:</b> '.$shipArr['checkinTimestamp'].'</div>';
							if($shipArr['checkinUser']) echo '<div class="displayFieldDiv"><b>Check-in User:</b> '.$shipArr['checkinUser'].'</div>';
						}
						else{
							?>
							<div class="displayFieldDiv" style="color:orange;font-size:120%">
								<b>Not yet arrived</b>
								<form action="manifestviewer.php" method="post" style="display:inline;margin-left:20px">
									<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
									<button name="action" type="submit" value="checkinShipment"> -- Mark as Arrived -- </button>
								</form>
							</div>
							<div class="displayFieldDiv"><b>Tracking Number:</b> <a href="" target="_blank"><?php echo $shipArr['trackingNumber']; ?></a></div>
							<?php
						}
						echo '<div style="margin-top:15px">';
						$sampleCntArr = $shipManager->getSampleCount();
						echo '<div class="displayFieldDiv"><b>Total Sample Count:</b> '.$sampleCntArr['cnt'].'</div>';
						unset($sampleCntArr['cnt']);
						$notCheckedIn = 0;
						if(isset($sampleCntArr[0])){
							$notCheckedIn = $sampleCntArr[0];
							unset($sampleCntArr[0]);
						}
						echo '<div class="displayFieldDiv"><b>Samples checked-in:</b> '.$checkinCnt.'</div>';
						if($notCheckedIn) echo '<div class="displayFieldDiv"><b>Samples not checked-in:</b> '.$notCheckedIn.'</div>';
						echo '</div>';
						if($shipArr['checkinTimestamp']){
							?>
							<div>
								<form name="submitform" method="post" onsubmit="checkinSample(this); return false;">
									<b>Sample check-in: </b><input name="checkinField" type="text" style="width:300px" />
									<span id="successText" style="color:green;display:none">success!!!</span>
									<span id="failText" style="color:red;display:none">Check-in failed</span>
								</form>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				if($shipArr['checkinTimestamp']){
					?>
					<div style="clear:both;margin-top:30px;">
						<fieldset>
							<legend><b>Sample Listing</b></legend>
							<form action="manifestviewer.php" method="post" onsubmit="return batchCheckinFormVerify(this)">
								<table class="styledtable">
									<tr><th><input name="selectall" type="checkbox" onclick="selectAll(this)" /></th><th>Sample ID</th><th>Sample Code</th><th>Sample Class</th><th>Taxon ID</th><th>Named Location</th><th>Collect Date</th><th>Quarantine Status</th><th>Check-in ts</th></tr>
									<?php
									$sampleList = $shipManager->getSampleArr();
									foreach($sampleList as $samplePK => $sampleArr){
										echo '<tr>';
										echo '<td>'.($sampleArr['checkinTS']?'':'<input id="scbox-'.$samplePK.'" name="scbox[]" type="checkbox" value="'.$samplePK.'" />').'</td>';
										echo '<td>'.$sampleArr['sampleID'].'</td>';
										echo '<td>'.$sampleArr['sampleCode'].'</td>';
										echo '<td>'.$sampleArr['sampleClass'].'</td>';
										echo '<td>'.$sampleArr['taxonID'].'</td>';
										echo '<td>'.$sampleArr['namedLocation'].'</td>';
										echo '<td>'.$sampleArr['collectDate'].'</td>';
										echo '<td>'.$sampleArr['quarantineStatus'].'</td>';
										echo '<td title="'.$sampleArr['checkinUser'].'"><span id="scSpan-'.$samplePK.'">'.$sampleArr['checkinTS'].'</span></td>';
										echo '</tr>';
										$str = '';
										if($sampleArr['individualCount']) $str .= '<div>Individual Count: '.$sampleArr['individualCount'].'</div>';
										if($sampleArr['filterVolume']) $str .= '<div>Filter Volume: '.$sampleArr['filterVolume'].'</div>';
										if($sampleArr['domainRemarks']) $str .= '<div>Domain Remarks: '.$sampleArr['domainRemarks'].'</div>';
										if($sampleArr['sampleNotes']) $str .= '<div>Sample Notes: '.$sampleArr['sampleNotes'].'</div>';
										if($str) echo '<tr><td colspan="8"><div style="margin-left:30px;">'.trim($str,'; ').'</div></td></tr>';
									}
									?>
								</table>
								<div style="margin:15px">
									<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
									<button name="action" type="submit" value="batchCheckin">Batch Check-in Samples</button>
								</div>
							</form>
						</fieldset>
					</div>
					<?php
				}
				?>
			</fieldset>
			<?php
		}
		else{
			//List all manifest matching search criteria
			?>
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