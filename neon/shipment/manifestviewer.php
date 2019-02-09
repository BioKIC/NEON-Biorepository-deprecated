<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestviewer.php');

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$shipmentPK = array_key_exists("shipmentPK",$_REQUEST)?$_REQUEST["shipmentPK"]:"";
$quickSearchTerm = array_key_exists("quicksearch",$_REQUEST)?$_REQUEST["quicksearch"]:"";

$shipManager = new ShipmentManager();
if($shipmentPK) $shipManager->setShipmentPK($shipmentPK);
elseif($quickSearchTerm) $shipmentPK = $shipManager->setQuickSearchTerm($quickSearchTerm);

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}

$status = "";
if($isEditor){
	if($action == 'downloadcsv'){
		$shipManager->exportShipmentList();
	}
	elseif($action == 'checkinShipment'){
		$shipManager->checkinShipment($_POST);
	}
	elseif($action == 'batchCheckin'){
		$shipManager->batchCheckinSamples($_POST);
	}
	elseif($action == 'batchHarvestOccid'){
		$shipManager->batchHarvestOccid($_POST);
	}
	elseif($action == 'receiptsubmitted'){
		$shipManager->setReceiptStatus($_POST['submitted']);
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
		$(document).ready(function() {
			$("#shipCheckinComment").keydown(function(evt){
				var evt  = (evt) ? evt : ((event) ? event : null);
				if ((evt.keyCode == 13)) { return false; }
			});
		});

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

		function checkinCommentChanged(textObj){
			var f = textObj.form;
			var testStr = textObj.value.trim();
			if(testStr){
				if(!f.receivedDate.value){
					var dateEx1 = /(\d{1,2})\/(\d{1,2})\/(\d{4})/;
					if(extractArr = dateEx1.exec(testStr)){
						var yearStr = extractArr[3];
						var monthStr = extractArr[1];
						var dayStr = extractArr[2];
						if(monthStr.length == 1) monthStr = '0'+monthStr;
						if(dayStr.length == 1) dayStr = '0'+dayStr;
						if(!f.receivedDate.value){
							f.receivedDate.value = yearStr+"-"+monthStr+"-"+dayStr;
							textObj.value = "";
						}
					}
				}
				if(!f.receivedTime.value){
					var timeEx1 = /(\d{1,2}):(\d{1,2})\s{1}([apm.]+)/i;
					if(extractArr = timeEx1.exec(testStr)){
						var hourStr = extractArr[1];
						var minStr = extractArr[2];
						var dayPeriod = extractArr[3].toLowerCase();
						if(dayPeriod.indexOf('p') > -1){
							if(parseInt(hourStr) < 12) hourStr = String(parseInt(hourStr)+12);
						}
						else if(dayPeriod.indexOf('a') > -1){
							if(parseInt(hourStr) == 12) hourStr = "00";
						}
						if(hourStr.length == 1 ) hourStr = "0"+hourStr;
						if(minStr.length == 1 ) minStr = "0"+minStr;
						f.receivedTime.value = hourStr+":"+minStr;
						textObj.value = "";
					}
				}
			}
		}

		function checkinSample(f){
			var sampleIdenfier = f.idenfier.value.trim();
			if(sampleIdenfier != ""){
				$.ajax({
					type: "POST",
					url: "rpc/checkinsample.php",
					dataType: 'json',
					data: { shipmentpk: "<?php echo $shipmentPK; ?>", idenfier: sampleIdenfier, accepted: f.acceptedForAnalysis.value, condition: f.sampleCondition.value, notes: f.sampleNotes.value }
				}).done(function( retJson ) {
					$("#checkinText").show();
					if(retJson.status == 0){
						$("#checkinText").css('color', 'red');
						$("#checkinText").text('check-in failed!');
					}
					else if(retJson.status == 1){
						$("#checkinText").css('color', 'green');
						$("#checkinText").text('success!!!');
						$("#scSpan-"+retJson.samplePK).html("checked-in");
						f.idenfier.value = "";
					}
					else if(retJson.status == 2){
						$("#checkinText").css('color', 'orange');
						$("#checkinText").text('sample already checked-in!');
					}
					else if(retJson.status == 3){
						$("#checkinText").css('color', 'red');
						$("#checkinText").text('sample not found!');
					}
					else{
						$("#checkinText").css('color', 'red');
						$("#checkinText").text('Failed: unknown error!');
					}
					$("#checkinText").animate({fontSize: "110%"}, "slow");
					$("#checkinText").animate({fontSize: "100%"}, "slow");
					//$("#checkinText").hide();
					f.idenfier.focus();
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

		function openShipmentEditor(){
			var url = "shipmenteditor.php?shipmentPK=<?php echo $shipmentPK; ?>";
			openPopup(url,"shipwindow");
			return false;
		}

		function openSampleCheckinEditor(samplePK){
			var url = "samplecheckineditor.php?samplePK="+samplePK;
			openPopup(url,"sample2window");
			return false;
		}

		function openPopup(url,windowName){
			newWindow = window.open(url,windowName,'scrollbars=1,toolbar=0,resizable=1,width=1000,height=400,left=20,top=200');
			if (newWindow.opener == null) newWindow.opener = self;
			return false;
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
	<a href="manifestsearch.php">Manifest Search</a> &gt;&gt;
	<a href="manifestviewer.php?shipmentPK=<?php echo $shipmentPK; ?>"><b>Manifest View</b></a>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		$shipArr = $shipManager->getShipmentArr();
		if($shipArr){
			?>
			<fieldset style="margin-top:30px">
				<legend><b>Shipment #<?php echo $shipmentPK; ?></b></legend>
				<div style="float:left">
					<div class="displayFieldDiv">
						<b>Shipment ID:</b> <?php echo $shipArr['shipmentID']; ?>
						<a href="#" onclick="openShipmentEditor()"><img src="../../images/edit.png" style="width:13px" /></a>
					</div>
					<?php
					$domainStr = $shipArr['domainID'];
					if(isset($shipArr['domainTitle'])) $domainStr = $shipArr['domainTitle'].' ('.$shipArr['domainID'].')';
					?>
					<div class="displayFieldDiv"><b>Domain:</b> <?php echo $domainStr; ?></div>
					<div class="displayFieldDiv"><b>Date Shipped:</b> <?php echo $shipArr['dateShipped']; ?></div>
					<div class="displayFieldDiv"><b>Shipped From:</b> <?php echo $shipArr['shippedFrom']; ?></div>
					<div class="displayFieldDiv"><b>Sender ID:</b> <?php echo $shipArr['senderID']; ?></div>
					<div class="displayFieldDiv"><b>Destination Facility:</b> <?php echo $shipArr['destinationFacility']; ?></div>
					<div class="displayFieldDiv"><b>Sent To ID:</b> <?php echo $shipArr['sentToID']; ?></div>
					<div class="displayFieldDiv"><b>Shipment Service:</b> <?php echo $shipArr['shipmentService']; ?></div>
					<div class="displayFieldDiv"><b>Shipment Method:</b> <?php echo $shipArr['shipmentMethod']; ?></div>
					<?php
					if($shipArr['importUser']) echo '<div class="displayFieldDiv"><b>Manifest Importer:</b> '.$shipArr['importUser'].'</div>';
					if($shipArr['ts']) echo '<div class="displayFieldDiv"><b>Import Date:</b> '.$shipArr['ts'].'</div>';
					if($shipArr['modifiedUser']) echo '<div class="displayFieldDiv"><b>Modified By User:</b> '.$shipArr['modifiedUser'].' ('.$shipArr['modifiedTimestamp'].')</div>';
					if($shipArr['shipmentNotes']) echo '<div class="displayFieldDiv"><b>General Notes:</b> '.$shipArr['shipmentNotes'].'</div>';
					?>
				</div>
				<div style="margin-left:40px;float:left;">
					<?php
					$receivedStr = '<span style="color:orange;font-weight:bold">Not yet arrived</span>';
					if($shipArr['receivedDate']) $receivedStr = $shipArr['receivedBy'].' ('.$shipArr['receivedDate'].')';
					echo '<div class="displayFieldDiv"><b>Received By:</b> '.$receivedStr.'</div>';
					echo '<div class="displayFieldDiv"><b>Tracking Number:</b> <a href="'.$shipManager->getTractingUrl().'" target="_blank">'.$shipArr['trackingNumber'].'</a></div>';
					if($shipArr['checkinTimestamp']){
						echo '<div class="displayFieldDiv"><b>Shipment Check-in:</b> '.$shipArr['checkinUser'].' ('.$shipArr['checkinTimestamp'].')</div>';
					}
					$sampleCntArr = $shipManager->getSampleCount();
					?>
					<div style="margin-top:10px;">
						<div class="displayFieldDiv">
							<b>Total Sample Count:</b> <?php echo ($sampleCntArr['all']); ?>
							<form name="refreshForm" action="manifestviewer.php" method="post" style="display:inline;" title="Refresh Counts and Sample Table">
								<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
								<input type="image" src="../../images/refresh.png" style="width:15px;" />
							</form>
						</div>
						<div style="margin-left:15px">
							<div class="displayFieldDiv"><b>Not checked-in:</b> <?php echo $sampleCntArr[0]; ?></div>
							<div class="displayFieldDiv"><b>Missing Occurrence Link:</b> <?php echo $sampleCntArr[1]; ?></div>
						</div>
						<?php
						if($shipArr['checkinTimestamp'] && $sampleCntArr[0]){
							?>
							<div style="margin-top:15px">
								<fieldset style="padding:10px;width:500px">
									<legend><b>Sample Check-in</b></legend>
									<form name="submitform" method="post" onsubmit="checkinSample(this); return false;">
										<div class="displayFieldDiv">
											<b>Identifier:</b> <input name="idenfier" type="text" style="width:250px" required />
											<span id="checkinText"></span><br/>
										</div>
										<div class="displayFieldDiv">
											<b>Accepted for Analysis:</b>
											<input name="acceptedForAnalysis" type="radio" value="1" checked /> Yes
											<input name="acceptedForAnalysis" type="radio" value="0" /> No
										</div>
										<div class="displayFieldDiv">
											<b>Sample condition:</b>
											<select name="sampleCondition">
												<?php
												$condArr = $shipManager->getConditionArr();
												foreach($condArr as $condKey => $condValue){
													echo '<option value="'.$condKey.'">'.$condValue.'</option>';
												}
												?>
											</select>
										</div>
										<div class="displayFieldDiv">
											<b>Remarks:</b> <input name="sampleNotes" type="text" style="width:300px" />
											<button type="submit">Submit</button>
										</div>
									</form>
								</fieldset>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div style="margin-left:40px;float:left;">
					<?php
					if(!$shipArr['checkinTimestamp']){
						?>
						<fieldset style="padding:10px;">
							<legend><b>Check-in Shipment</b></legend>
							<form action="manifestviewer.php" method="post" style="">
								<?php
								$deliveryArr = $shipManager->getDeliveryArr();
								?>
								<div><b>Received by:</b> <input name="receivedBy" type="text" value="<?php echo (isset($deliveryArr['receivedBy'])?$deliveryArr['receivedBy']:''); ?>" required /></div>
								<div>
									<b>Delivery date:</b>
									<input name="receivedDate" type="date" value="<?php echo (isset($deliveryArr['receivedDate'])?$deliveryArr['receivedDate']:''); ?>" required />
									<input name="receivedTime" type="time" value="<?php echo (isset($deliveryArr['receivedTime'])?$deliveryArr['receivedTime']:''); ?>" />
								</div>
								<div><b>Comments:</b> <input id="shipCheckinComment" name="notes" type="text" value="" style="width:350px" onchange="checkinCommentChanged(this);" /></div>
								<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
								<div style="margin:10px"><button name="action" type="submit" value="checkinShipment"> -- Mark as Arrived -- </button></div>
							</form>
						</fieldset>
						<?php
					}
					?>
				</div>
				<?php
				if($shipArr['checkinTimestamp']){
					$sampleList = $shipManager->getSampleArr();
					if($sampleList){
						?>
						<div style="clear:both;padding-top:30px;">
							<fieldset>
								<legend><b>Sample Listing</b></legend>
								<form name="sampleListingForm" action="manifestviewer.php" method="post" onsubmit="return batchCheckinFormVerify(this)">
									<table class="styledtable">
										<tr>
											<?php
											$headerOutArr = current($sampleList);
											echo '<th><input name="selectall" type="checkbox" onclick="selectAll(this)" /></th>';
											$headerArr = array('sampleID'=>'Sample ID', 'sampleCode'=>'Sample<br/>Code', 'sampleClass'=>'Sample<br/>Class', 'taxonID'=>'Taxon ID',
												'namedLocation'=>'Named<br/>Location', 'collectDate'=>'Collection<br/>Date', 'quarantineStatus'=>'Quarantine<br/>Status',
												'sampleCondition'=>'Sample<br/>Condition','acceptedForAnalysis'=>'Accepted<br/>for<br/>Analysis','checkinUser'=>'Check-in','occid'=>'occid');
												//'individualCount'=>'Individual Count', 'filterVolume'=>'Filter Volume', 'domainRemarks'=>'Domain Remarks', 'sampleNotes'=>'Sample Notes',
											$rowCnt = 1;
											foreach($headerArr as $fieldName => $headerTitle){
												if(array_key_exists($fieldName, $headerOutArr) || $fieldName == 'checkinUser'){
													echo '<th>'.$headerTitle.'</th>';
													$rowCnt++;
												}
											}
											?>
										</tr>
										<?php
										foreach($sampleList as $samplePK => $sampleArr){
											echo '<tr>';
											echo '<td><input id="scbox-'.$samplePK.'" name="scbox[]" type="checkbox" value="'.$samplePK.'" /></td>';
											$sampleID = $sampleArr['sampleID'];
											if($quickSearchTerm == $sampleID) $sampleID = '<b>'.$sampleID.'</b>';
											echo '<td>'.$sampleID.'</td>';
											if(array_key_exists('sampleCode',$sampleArr)) echo '<td>'.$sampleArr['sampleCode'].'</td>';
											echo '<td>'.$sampleArr['sampleClass'].'</td>';
											if(array_key_exists('taxonID',$sampleArr)) echo '<td>'.$sampleArr['taxonID'].'</td>';
											$namedLocation = $sampleArr['namedLocation'];
											if(isset($sampleArr['siteTitle']) && $sampleArr['siteTitle']) $namedLocation = '<span title="'.$sampleArr['siteTitle'].'">'.$namedLocation.'</span>';
											echo '<td>'.$namedLocation.'</td>';
											echo '<td>'.$sampleArr['collectDate'].'</td>';
											echo '<td>'.$sampleArr['quarantineStatus'].'</td>';
											if(array_key_exists('sampleCondition', $sampleArr)) echo '<td>'.$sampleArr['sampleCondition'].'</td>';
											if(array_key_exists('acceptedForAnalysis', $sampleArr)){
												$acceptedForAnalysis = $sampleArr['acceptedForAnalysis'];
												if($sampleArr['acceptedForAnalysis']==1) $acceptedForAnalysis = 'Y';
												if($sampleArr['acceptedForAnalysis']==='0') $acceptedForAnalysis = 'N';
												echo '<td>'.$acceptedForAnalysis.'</td>';
											}
											echo '<td title="'.$sampleArr['checkinUser'].'">';
											echo '<span id="scSpan-'.$samplePK.'">'.$sampleArr['checkinTimestamp'].'</span> ';
											if($sampleArr['checkinTimestamp']) echo '<a href="#" onclick="return openSampleCheckinEditor('.$samplePK.')"><img src="../../images/edit.png" style="width:13px" /></a>';
											echo '</td>';
											if(array_key_exists('occid',$sampleArr)) echo '<td><a href="../../collections/individual/index.php?occid='.$sampleArr['occid'].'" target="_blank">'.$sampleArr['occid'].'</a></td>';
											echo '</tr>';
											$str = '';
											if(isset($sampleArr['individualCount'])) $str .= '<div>Individual Count: '.$sampleArr['individualCount'].'</div>';
											if(isset($sampleArr['filterVolume'])) $str .= '<div>Filter Volume: '.$sampleArr['filterVolume'].'</div>';
											if(isset($sampleArr['domainRemarks'])) $str .= '<div>Domain Remarks: '.$sampleArr['domainRemarks'].'</div>';
											if(isset($sampleArr['sampleNotes'])) $str .= '<div>Sample Notes: '.$sampleArr['sampleNotes'].'</div>';
											if(isset($sampleArr['dynamicProperties']) && $sampleArr['dynamicProperties']){
												$dynPropArr = json_decode($sampleArr['dynamicProperties'],true);
												$propSstr = '';
												foreach($dynPropArr as $category => $propValue){
													$propSstr .= $category.': '.$propValue.'; ';
												}
												$str .= '<div>'.trim($propSstr,'; ').'</div>';
											}
											if($str) echo '<tr><td colspan="'.$rowCnt.'"><div style="margin-left:30px;">'.trim($str,'; ').'</div></td></tr>';
										}
										?>
									</table>
									<div style="margin:15px;float:right">
										<button name="action" type="submit" value="batchHarvestOccid" disabled>Batch Harvest Occurrences</button>
									</div>
									<div style="margin:15px">
										<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
										<fieldset style="width:400px;">
											<legend><b>Batch Check-in Selected Samples</b></legend>
											<div class="displayFieldDiv">
												<b>Accepted for Analysis:</b>
												<input name="acceptedForAnalysis" type="radio" value="1" checked /> Yes
												<input name="acceptedForAnalysis" type="radio" value="0" /> No
											</div>
											<div class="displayFieldDiv">
												<b>Sample condition:</b>
												<select name="sampleCondition">
													<?php
													$condArr = $shipManager->getConditionArr();
													foreach($condArr as $condKey => $condValue){
														echo '<option value="'.$condKey.'">'.$condValue.'</option>';
													}
													?>
												</select>
											</div>
											<div class="displayFieldDiv">
												<b>Remarks:</b> <input name="sampleNotes" type="text" style="width:300px" />
											</div>
											<div style="margin:5px 10px">
												<button name="action" type="submit" value="batchCheckin">Check-in Selected Samples</button>
											</div>
										</fieldset>
									</div>
								</form>
								<fieldset style="width:400px;margin:15px">
									<a id="receiptStatus"></a>
									<legend><b>Receipt Status</b></legend>
									<form name="receiptSubmittedForm" action="manifestviewer.php#receiptStatus" method="post">
										<?php
										$receiptStatus = '';
										if(isset($shipArr['receiptStatus']) && $shipArr['receiptStatus']) $receiptStatus = $shipArr['receiptStatus'];
										$statusArr = explode(':', $receiptStatus);
										if($statusArr) $receiptStatus = $statusArr[0];
										?>
										<input name="submitted" type="radio" value="" <?php echo (!$receiptStatus?'checked':''); ?> onchange="this.form.submit()" />
										<b>Status not set</b><br/>
										<input name="submitted" type="radio" value="1" <?php echo ($receiptStatus=='Downloaded'?'checked':''); ?> onchange="this.form.submit()" />
										<b>Receipt downloaded</b><br/>
										<input name="submitted" type="radio" value="2" <?php echo ($receiptStatus=='Submitted'?'checked':''); ?> onchange="this.form.submit()" />
										<b>Receipt submitted to NEON</b>
										<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
										<input name="action" type="hidden" value="receiptsubmitted" />
									</form>
									<div style="margin:15px">
										<form name="exportReceiptForm" action="exportreceipt.php" method="post">
											<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
											<button name="action" type="submit" value="downloadReceipt">Download Receipt</button>
										</form>
										<div style="margin-top:15px">
											<a href="http://data.neonscience.org/web/external-lab-ingest" target="_blank"><b>Proceed to NEON submission page</b></a>
										</div>
									</div>
								</fieldset>
							</fieldset>
						</div>
						<?php
					}
					else{
						echo '<div><b>No sample records exist</b></div>';
					}
				}
				?>
			</fieldset>
			<?php
		}
		else{
			if($quickSearchTerm){
				echo '<h2>Search term '.$quickSearchTerm.' failed to return results</h2>';
			}
			else{
				echo '<h2>Shipment does not exist or has been deleted</h2>';
			}
			?>
			<div style="margin:10px">
				<b>Quick search:</b>
				<form name="sampleQuickSearchFrom" action="manifestviewer.php" method="post" style="display: inline" >
					<input name="quicksearch" type="text" value="" onchange="this.form.submit()" style="width:250px;" />
				</form>
			</div>
			<div style="margin:10px">
				<h3><a href="manifestsearch.php">List Manifests</a></h3>
			</div>
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