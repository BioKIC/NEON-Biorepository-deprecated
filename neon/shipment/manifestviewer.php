<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
include_once($SERVER_ROOT.'/neon/classes/OccurrenceHarvester.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestviewer.php?'.$_SERVER['QUERY_STRING']);

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$shipmentPK = array_key_exists("shipmentPK",$_REQUEST)?$_REQUEST["shipmentPK"]:"";
$sampleFilter = isset($_REQUEST['sampleFilter'])?$_REQUEST['sampleFilter']:'';
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
	if($action == 'checkinShipment'){
		$shipManager->checkinShipment($_POST);
	}
	elseif($action == 'batchCheckin'){
		$shipManager->batchCheckinSamples($_POST);
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
			if(f.sampleReceived.value == "0"){
				if(f.acceptedForAnalysis.value != "" || f.sampleCondition.value != ""){
					alert("If sample is not received, Accepted for Analysis and Sample Condition must be NULL");
					return false;
				}
			}
			else if(f.sampleReceived.value == "1"){
				if(f.acceptedForAnalysis.value == ""){
					alert("Please select if accepted for analysis");
					return false;
				}
			}
			if(f.acceptedForAnalysis.value === 0){
				if(f.sampleCondition.value == "ok"){
					alert("Sample Condition cannot be OK if sample is Not Accepted for Analysis");
					return false;
				}
				else if(f.sampleCondition.value == ""){
					alert("Enter a Sample Condition");
					return false;
				}
			}
			return true;
		}

		function checkinCommentChanged(textObj){
			//USPS: WEDNESDAY 15  MAY 2019	 by 8:00pm
			//USPS ver2: May 15, 2019 at 2:43 pm
			//FedEx: Tuesday 9/04/2018 at 1:03 pm
			//UPS: Thursday 02/07/2019 Delivery Time at 12:33 P.M.

			var f = textObj.form;
			var testStr = textObj.value.trim();
			if(testStr){
				if(!f.receivedDate.value){
					var yearStr = "";
					var monthStr = "";
					var dayStr = "";
					var dateEx1 = /(\d{1,2})\/(\d{1,2})\/(\d{4})/;
					var dateEx2 = /(\d{1,2})\s{1,3}([A-Z]+)\s{1,3}(\d{4})/;
					var dateEx3 = /([A-Za-z]+)\s{1,3}(\d{1,2})[,\s,]{1,3}(\d{4})/;
					if(extractArr = dateEx1.exec(testStr)){
						yearStr = extractArr[3];
						monthStr = extractArr[1];
						dayStr = extractArr[2];
					}
					else if(extractArr = dateEx2.exec(testStr)){
						yearStr = extractArr[3];
						monthStr = getMonthFromString(extractArr[2]);
						dayStr = extractArr[1];
					}
					else if(extractArr = dateEx3.exec(testStr)){
						yearStr = extractArr[3];
						monthStr = getMonthFromString(extractArr[1]);
						dayStr = extractArr[2];
					}
					if(yearStr){
						if(monthStr.length == 1) monthStr = '0'+monthStr;
						if(dayStr.length == 1) dayStr = '0'+dayStr;
						if(!f.receivedDate.value){
							f.receivedDate.value = yearStr+"-"+monthStr+"-"+dayStr;
							textObj.value = "";
						}
					}
				}
				if(!f.receivedTime.value){
					var timeEx1 = /(\d{1,2}):(\d{1,2})\s{0,1}([apm.]+)/i;
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

		function getMonthFromString(mon){
			var d = Date.parse(mon + "1, 2012");
			if(!isNaN(d)){
				var month = new Date(d).getMonth() + 1;
				return month.toString();
			}
			return "";
		}

		function checkinSample(f){
			if(f.sampleReceived.value == "0"){
				if(f.acceptedForAnalysis.value != "" || f.sampleCondition.value != ""){
					alert("If sample is not received, Accepted for Analysis and Sample Condition must be NULL");
					return false;
				}
			}
			else if(f.sampleReceived.value == "1"){
				if(f.acceptedForAnalysis.value == ""){
					alert("Please select if accepted for analysis");
					return false;
				}
			}
			if(f.acceptedForAnalysis.value === 0){
				if(f.sampleCondition.value == "ok"){
					alert("Sample Condition cannot be OK when sample is tagged as Not Accepted for Analysis");
					return false;
				}
				else if(f.sampleCondition.value == ""){
					alert("Sample Condition required when sample is tagged as Not Accepted for Analysis");
					return false;
				}
			}
			var sampleIdentifier = f.identifier.value.trim();
			if(sampleIdentifier != ""){
				//alert("rpc/checkinsample.php?shipmentpk=<?php echo $shipmentPK; ?>&identifier="+sampleIdentifier+"&received="+f.sampleReceived.value+"&accepted="+f.acceptedForAnalysis.value+"&condition="+f.sampleCondition.value+"&altSampleID="+f.alternativeSampleID.value+"&notes="+f.checkinRemarks.value);
				$.ajax({
					type: "POST",
					url: "rpc/checkinsample.php",
					dataType: 'json',
					data: { shipmentpk: "<?php echo $shipmentPK; ?>", identifier: sampleIdentifier, received: f.sampleReceived.value, accepted: f.acceptedForAnalysis.value, condition: f.sampleCondition.value, altSampleID: f.alternativeSampleID.value, notes: f.checkinRemarks.value }
				}).done(function( retJson ) {
					$("#checkinText").show();
					if(retJson.status == 0){
						$("#checkinText").css('color', 'red');
						$("#checkinText").text('check-in failed!');
					}
					else if(retJson.status == 1){
						$("#checkinText").css('color', 'green');
						$("#checkinText").text('success!!!');
						$("#scSpan-"+retJson.samplePK).html("checked in");
						f.identifier.value = "";
						f.sampleReceived.value = 1;
						f.acceptedForAnalysis.value = 1;
						f.sampleCondition.value = "ok";
						f.alternativeSampleID.value = "";
						f.checkinRemarks.value = "";
					}
					else if(retJson.status == 2){
						$("#checkinText").css('color', 'orange');
						$("#checkinText").text('already checked!');
					}
					else if(retJson.status == 3){
						$("#checkinText").css('color', 'red');
						$("#checkinText").text('not found!');
					}
					else{
						$("#checkinText").css('color', 'red');
						$("#checkinText").text('Failed: unknown error!');
					}
					$("#checkinText").animate({fontSize: "125%"}, "slow");
					$("#checkinText").animate({fontSize: "100%"}, "slow");
					$("#checkinText").animate({fontSize: "125%"}, "slow");
					$("#checkinText").animate({fontSize: "100%"}, "slow").delay(5000).fadeOut();
					f.identifier.focus();
				});
			}
		}

		function sampleReceivedChanged(f){
			$(f.acceptedForAnalysis).prop("checked", false );
			$('[name=sampleCondition]').val( '' );
		}

		function popoutCheckinBox(){
			$("#sampleCheckinDiv").css('position', 'fixed');
			$("#popoutDiv").hide();
			$("#bindDiv").show();
		}

		function bindCheckinBox(){
			$("#sampleCheckinDiv").css('position', 'static');
			$("#popoutDiv").show();
			$("#bindDiv").hide();
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

		function openSampleEditor(samplePK){
			var url = "sampleeditor.php?samplePK="+samplePK;
			openPopup(url,"sample1window");
			return false;
		}

		function addSample(shipmentPK){
			var url = "sampleeditor.php?shipmentPK="+shipmentPK;
			openPopup(url,"sample1window");
			return false;
		}

		function openSampleCheckinEditor(samplePK){
			var url = "samplecheckineditor.php?samplePK="+samplePK;
			openPopup(url,"sample2window");
			return false;
		}

		function openPopup(url,windowName){
			newWindow = window.open(url,windowName,'scrollbars=1,toolbar=0,resizable=1,width=1000,height=500,left=20,top=100');
			if (newWindow.opener == null) newWindow.opener = self;
			return false;
		}
	</script>
	<style type="text/css">
		.fieldGroupDiv{ clear:both; margin-top:2px; height: 25px; }
		.fieldDiv{ float:left; margin-left: 10px}
		.displayFieldDiv{ margin-bottom: 3px }
		fieldset legend{ font-weight:bold; }
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
		if($action == 'batchHarvestOccid'){
			?>
			<fieldset style="padding:15px">
				<legend>Action Panel</legend>
				<ul>
				<?php
				$occurManager = new OccurrenceHarvester();
				$occurManager->batchHarvestOccid($_POST);
				?>
				</ul>
			</fieldset>
			<?php
		}
		$shipArr = $shipManager->getShipmentArr();
		if($shipArr){
			?>
			<fieldset style="margin-top:30px">
				<legend>Shipment #<?php echo $shipmentPK; ?></legend>
				<div style="float:left;margin-right:40px;width:400px;">
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
					if($shipArr['fileName']) echo '<div class="displayFieldDiv"><b>Import file:</b> <a href="'.$CLIENT_ROOT.'/neon/content/manifests/'.$shipArr['fileName'].'">'.$shipArr['fileName'].'</a></div>';
					?>
				</div>
				<div style="float:left;">
					<?php
					$receivedStr = '<span style="color:orange;font-weight:bold">Not yet arrived</span>';
					if($shipArr['receivedDate']) $receivedStr = $shipArr['receivedBy'].' ('.$shipArr['receivedDate'].')';
					echo '<div class="displayFieldDiv"><b>Received By:</b> '.$receivedStr.'</div>';
					echo '<div class="displayFieldDiv"><b>Tracking Number:</b> '.$shipManager->getTrackingStr().'</div>';
					if($shipArr['checkinTimestamp']){
						echo '<div class="displayFieldDiv"><b>Shipment Check-in:</b> '.$shipArr['checkinUser'].' ('.$shipArr['checkinTimestamp'].')</div>';
					}
					$sampleCntArr = $shipManager->getSampleCount();
					?>
					<div style="margin-top:10px;">
						<div class="displayFieldDiv">
							<b>Total Sample Count:</b> <?php echo ($sampleCntArr['all']); ?>
							<form name="refreshForm" action="manifestviewer.php" method="get" style="display:inline;" title="Refresh Counts and Sample Table">
								<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
								<input type="image" src="../../images/refresh.png" style="width:15px;" />
							</form>
						</div>
						<div style="margin-left:15px">
							<div class="displayFieldDiv"><b>Not Checked In:</b> <?php echo $sampleCntArr[0]; ?></div>
							<div class="displayFieldDiv"><b>Missing Occurrence Link:</b> <?php echo $sampleCntArr[1]; ?></div>
						</div>
						<?php
						if($shipArr['checkinTimestamp'] && $sampleCntArr[0]){
							?>
							<div id="sampleCheckinDiv" style="margin-top:15px;background-color:white;top:50px;right:200px">
								<fieldset style="padding:10px;width:500px">
									<legend>Sample Check-in</legend>
									<form name="submitform" method="post" onsubmit="checkinSample(this); return false;">
										<div id="popoutDiv" style="float:right"><a href="#" onclick="popoutCheckinBox();return false" title="Popout Sample Check-in Box">&gt;&gt;</a></div>
										<div id="bindDiv" style="float:right;display:none"><a href="#" onclick="bindCheckinBox();return false" title="Bind Sample Check-in Box to top of form">&lt;&lt;</a></div>
										<div class="displayFieldDiv">
											<b>Identifier:</b> <input name="identifier" type="text" style="width:250px" required />
											<div id="checkinText" style="display:inline"></div>
										</div>
										<div class="displayFieldDiv">
											<b>Sample Received:</b>
											<input name="sampleReceived" type="radio" value="1" checked /> Yes
											<input name="sampleReceived" type="radio" value="0" onchange="sampleReceivedChanged(this.form)" /> No
										</div>
										<div class="displayFieldDiv">
											<b>Accepted for Analysis:</b>
											<input name="acceptedForAnalysis" type="radio" value="1" checked /> Yes
											<input name="acceptedForAnalysis" type="radio" value="0" onchange="this.form.sampleCondition.value = ''" /> No
										</div>
										<div class="displayFieldDiv">
											<b>Sample Condition:</b>
											<select name="sampleCondition">
												<option value="">Not Set</option>
												<option value="">--------------------------------</option>
												<?php
												$condArr = $shipManager->getConditionArr();
												foreach($condArr as $condKey => $condValue){
													echo '<option value="'.$condKey.'" '.($condKey=='ok'?'SELECTED':'').'>'.$condValue.'</option>';
												}
												?>
											</select>
										</div>
										<div class="displayFieldDiv">
											<b>Alternative ID:</b> <input name="alternativeSampleID" type="text" style="width:225px" />
										</div>
										<div class="displayFieldDiv">
											<b>Remarks:</b> <input name="checkinRemarks" type="text" style="width:300px" />
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
							<legend>Check-in Shipment</legend>
							<form action="manifestviewer.php" method="post">
								<?php
								$deliveryArr = $shipManager->getDeliveryArr();
								?>
								<div><b>Received By:</b> <input name="receivedBy" type="text" value="<?php echo (isset($deliveryArr['receivedBy'])?$deliveryArr['receivedBy']:''); ?>" required /></div>
								<div>
									<b>Delivery Date:</b>
									<input name="receivedDate" type="date" value="<?php echo (isset($deliveryArr['receivedDate'])?$deliveryArr['receivedDate']:''); ?>" required />
									<input name="receivedTime" type="time" value="<?php echo (isset($deliveryArr['receivedTime'])?$deliveryArr['receivedTime']:''); ?>" />
								</div>
								<div><b>Comments:</b> <input id="shipCheckinComment" name="notes" type="text" value="" style="width:350px" onchange="checkinCommentChanged(this);" /></div>
								<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
								<div style="float:right;margin:40px 15px 0px 0px"><a href="manifestviewer.php?sampleFilter=displaySamples&shipmentPK=<?php echo $shipmentPK; ?>#samplePanel">Display Samples</a></div>
								<div style="margin:10px"><button name="action" type="submit" value="checkinShipment"> -- Mark as Arrived -- </button></div>
							</form>
						</fieldset>
						<?php
					}
					?>
				</div>
				<?php
				if($shipArr['checkinTimestamp'] || $sampleFilter == 'displaySamples'){
					$sampleList = $shipManager->getSampleArr(null, $sampleFilter);
					?>
					<div style="clear:both;padding-top:30px;">
						<fieldset id="samplePanel">
							<legend>Sample Listing</legend>
							<div>
								<div style="float:left">Records displayed: <?php echo count($sampleList); ?></div>
								<div style="float:right;">
									<form name="filterSampleForm" action="manifestviewer.php#samplePanel" method="post" style="">
										Filter by:
										<select name="sampleFilter" onchange="this.form.submit()">
											<option value="">All Records</option>
											<option value="notCheckedIn" <?php echo ($sampleFilter=='notCheckedIn'?'SELECTED':''); ?>>Not Checked In</option>
											<option value="missingOccid" <?php echo ($sampleFilter=='missingOccid'?'SELECTED':''); ?>>Missing Occurrences</option>
											<option value="notAccepted" <?php echo ($sampleFilter=='notAccepted'?'SELECTED':''); ?>>Not Accepted for Analysis</option>
											<option value="altIds" <?php echo ($sampleFilter=='altIds'?'SELECTED':''); ?>>Has Alternative IDs</option>
											<option value="harvestingError" <?php echo ($sampleFilter=='harvestingError'?'SELECTED':''); ?>>Harvesting Errors</option>
										</select>
										<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
									</form>
								</div>
							</div>
							<div style="clear:both">
								<?php
								if($sampleList){
									?>
									<form name="sampleListingForm" action="manifestviewer.php" method="post" onsubmit="return batchCheckinFormVerify(this)">
										<table class="styledtable">
											<tr>
												<?php
												$headerOutArr = current($sampleList);
												echo '<th><input name="selectall" type="checkbox" onclick="selectAll(this)" /></th>';
												$headerArr = array('sampleID'=>'Sample ID', 'sampleCode'=>'Sample<br/>Code', 'sampleClass'=>'Sample<br/>Class', 'taxonID'=>'Taxon ID',
													'namedLocation'=>'Named<br/>Location', 'collectDate'=>'Collection<br/>Date', 'quarantineStatus'=>'Quarantine<br/>Status','sampleReceived'=>'Sample<br/>Received',
													'acceptedForAnalysis'=>'Accepted<br/>for<br/>Analysis','sampleCondition'=>'Sample<br/>Condition','checkinUser'=>'Check-in','occid'=>'occid');
													//'individualCount'=>'Individual Count', 'filterVolume'=>'Filter Volume', 'domainRemarks'=>'Domain Remarks', 'sampleNotes'=>'Sample Notes',
												$rowCnt = 1;
												foreach($headerArr as $fieldName => $headerTitle){
													if(array_key_exists($fieldName, $headerOutArr) || $fieldName == 'checkinUser' || $fieldName == 'occid'){
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
												echo '<td>';
												echo $sampleID;
												echo ' <a href="#" onclick="return openSampleEditor('.$samplePK.')"><img src="../../images/edit.png" style="width:12px" /></a>';
												echo '</td>';
												if(array_key_exists('sampleCode',$sampleArr)) echo '<td>'.$sampleArr['sampleCode'].'</td>';
												echo '<td>'.$sampleArr['sampleClass'].'</td>';
												if(array_key_exists('taxonID',$sampleArr)) echo '<td>'.$sampleArr['taxonID'].'</td>';
												if(array_key_exists('namedLocation', $sampleArr)){
													$namedLocation = $sampleArr['namedLocation'];
													if(isset($sampleArr['siteTitle']) && $sampleArr['siteTitle']) $namedLocation = '<span title="'.$sampleArr['siteTitle'].'">'.$namedLocation.'</span>';
													echo '<td>'.$namedLocation.'</td>';
												}
												if(array_key_exists('collectDate', $sampleArr)) echo '<td>'.$sampleArr['collectDate'].'</td>';
												echo '<td>'.$sampleArr['quarantineStatus'].'</td>';
												if(array_key_exists('sampleReceived', $sampleArr)){
													$sampleReceived = $sampleArr['sampleReceived'];
													if($sampleArr['sampleReceived']==1) $sampleReceived = 'Y';
													if($sampleArr['sampleReceived']==='0') $sampleReceived = 'N';
													echo '<td>'.$sampleReceived.'</td>';
												}
												if(array_key_exists('acceptedForAnalysis', $sampleArr)){
													$acceptedForAnalysis = $sampleArr['acceptedForAnalysis'];
													if($sampleArr['acceptedForAnalysis']==1) $acceptedForAnalysis = 'Y';
													if($sampleArr['acceptedForAnalysis']==='0') $acceptedForAnalysis = 'N';
													echo '<td>'.$acceptedForAnalysis.'</td>';
												}
												if(array_key_exists('sampleCondition', $sampleArr)) echo '<td>'.$sampleArr['sampleCondition'].'</td>';
												echo '<td title="'.$sampleArr['checkinUser'].'">';
												echo '<span id="scSpan-'.$samplePK.'">'.$sampleArr['checkinTimestamp'].'</span> ';
												if($sampleArr['checkinTimestamp']) echo '<a href="#" onclick="return openSampleCheckinEditor('.$samplePK.')"><img src="../../images/edit.png" style="width:13px" /></a>';
												echo '</td>';
												echo '<td style="text-align:center">';
												if(array_key_exists('occid',$sampleArr) && $sampleArr['occid']){
													echo '<a href="../../collections/individual/index.php?occid='.$sampleArr['occid'].'" target="_blank"><img src="../../images/list.png" style="width:13px" /></a>&nbsp;&nbsp;&nbsp;';
													echo '<a href="../../collections/editor/occurrenceeditor.php?occid='.$sampleArr['occid'].'" target="_blank"><img src="../../images/edit.png" style="width:13px" /></a>';
												}
												echo '</td>';
												echo '</tr>';
												$str = '';
												if(isset($sampleArr['alternativeSampleID'])) $str .= '<div>Alternative Sample ID: '.$sampleArr['alternativeSampleID'].'</div>';
												if(isset($sampleArr['individualCount'])) $str .= '<div>Individual Count: '.$sampleArr['individualCount'].'</div>';
												if(isset($sampleArr['filterVolume'])) $str .= '<div>Filter Volume: '.$sampleArr['filterVolume'].'</div>';
												if(isset($sampleArr['domainRemarks'])) $str .= '<div>Domain Remarks: '.$sampleArr['domainRemarks'].'</div>';
												if(isset($sampleArr['sampleNotes'])) $str .= '<div>Sample Notes: '.$sampleArr['sampleNotes'].'</div>';
												if(isset($sampleArr['checkinRemarks'])) $str .= '<div>Check-in Remarks: '.$sampleArr['checkinRemarks'].'</div>';
												if(isset($sampleArr['dynamicProperties']) && $sampleArr['dynamicProperties']){
													$dynPropArr = json_decode($sampleArr['dynamicProperties'],true);
													$propStr = '';
													foreach($dynPropArr as $category => $propValue){
														$propStr .= $category.': '.$propValue.'; ';
													}
													$str .= '<div>'.trim($propStr,'; ').'</div>';
												}
												if(isset($sampleArr['symbiotaTarget']) && $sampleArr['symbiotaTarget']){
													$symbTargetArr = json_decode($sampleArr['symbiotaTarget'],true);
													$symbStr = '';
													foreach($symbTargetArr as $symbLabel => $symbValue){
														$symbStr .= $symbLabel.': '.$symbValue.'; ';
													}
													$str .= '<div>Symbiota targeted data ['.trim($symbStr,'; ').']</div>';
												}
												if(isset($sampleArr['occurErr'])) $str .= '<div>Occurrence Harvesting Error: '.$sampleArr['occurErr'].'</div>';
												if($str) echo '<tr><td colspan="'.$rowCnt.'"><div style="margin-left:30px;">'.trim($str,'; ').'</div></td></tr>';
											}
											?>
										</table>
										<div style="margin:15px;float:left">
											<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
											<fieldset style="width:450px;">
												<legend>Batch Check-in Selected Samples</legend>
												<div class="displayFieldDiv">
													<b>Sample Received:</b>
													<input name="sampleReceived" type="radio" value="1" checked /> Yes
													<input name="sampleReceived" type="radio" value="0" onchange="sampleReceivedChanged(this.form)" /> No
												</div>
												<div class="displayFieldDiv">
													<b>Accepted for Analysis:</b>
													<input name="acceptedForAnalysis" type="radio" value="1" checked /> Yes
													<input name="acceptedForAnalysis" type="radio" value="0" onchange="this.form.sampleCondition.value = ''" /> No
												</div>
												<div class="displayFieldDiv">
													<b>Sample Condition:</b>
													<select name="sampleCondition">
														<option value="">Not Set</option>
														<option value="">--------------------------------</option>
														<?php
														$condArr = $shipManager->getConditionArr();
														foreach($condArr as $condKey => $condValue){
															echo '<option value="'.$condKey.'" '.($condKey=='ok'?'SELECTED':'').'>'.$condValue.'</option>';
														}
														?>
													</select>
												</div>
												<div class="displayFieldDiv">
													<b>Check-in Remarks:</b> <input name="checkinRemarks" type="text" style="width:300px" />
												</div>
												<div style="margin:5px 10px">
													<button name="action" type="submit" value="batchCheckin">Check-in Selected Samples</button>
												</div>
											</fieldset>
										</div>
										<div style="margin:15px;float:left">
											<div style="margin:5px;">
												<a href="#" onclick="addSample(<?php echo $shipmentPK; ?>);return false;"><button name="addSampleButton" type="button">Add New Sample</button></a>
											</div>
											<fieldset style="margin:5px">
												<legend>Occurrence Harvesting</legend>
												<button name="action" type="submit" value="batchHarvestOccid">Batch Harvest</button>
												<div style="margin:10px" title="Upon reharvesting, replaces existing field values, but only if they haven't been explicitly edited to another value">
													<input name="replaceFieldValues" type="checkbox" value="1" /> Replace Existing Field Values
												</div>
											</fieldset>
										</div>
									</form>
									<div style="clear:both">
										<div style="float:left;margin-left:15px;">
											<fieldset style="width:450px;">
												<a id="receiptStatus"></a>
												<legend>Receipt Status</legend>
												<form name="receiptSubmittedForm" action="manifestviewer.php#receiptStatus" method="post">
													<?php
													$receiptStatus = '';
													if(isset($shipArr['receiptStatus']) && $shipArr['receiptStatus']) $receiptStatus = $shipArr['receiptStatus'];
													$statusArr = explode(':', $receiptStatus);
													if($statusArr) $receiptStatus = $statusArr[0];
													?>
													<input name="submitted" type="radio" value="" <?php echo (!$receiptStatus?'checked':''); ?> onchange="this.form.submit()" />
													<b>Status Not Set</b><br/>
													<input name="submitted" type="radio" value="1" <?php echo ($receiptStatus=='Downloaded'?'checked':''); ?> onchange="this.form.submit()" />
													<b>Receipt Downloaded</b><br/>
													<input name="submitted" type="radio" value="2" <?php echo ($receiptStatus=='Submitted'?'checked':''); ?> onchange="this.form.submit()" />
													<b>Receipt Submitted to NEON</b>
													<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
													<input name="action" type="hidden" value="receiptsubmitted" />
												</form>
												<div style="margin:15px">
													<form name="exportReceiptForm" action="exporthandler.php" method="post">
														<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
														<input name="exportTask" type="hidden" value="receipt" />
														<button name="action" type="submit" value="downloadReceipt">Download Receipt</button>
													</form>
													<div style="margin-top:15px">
														<a href="http://data.neonscience.org/web/external-lab-ingest" target="_blank"><b>Proceed to NEON submission page</b></a>
													</div>
												</div>
											</fieldset>
										</div>
										<div style="float:left;margin-left:30px;">
											<form name="exportSampleListForm" action="exporthandler.php" method="post" style="">
												<input name="shipmentPK" type="hidden" value="<?php echo $shipmentPK; ?>" />
												<input name="exportTask" type="hidden" value="sampleList" />
												<div style="margin:10px 0px"><button name="action" type="submit" value="exportSampleListing">Export Sample Listing</button></div>
											</form>
											<div ><a href="manifestloader.php"><button name="loadManifestButton" type="button">Load Another Manifest</button></a></div>
										</div>
										<?php
										$collectionArr = $shipManager->getCollectionArr();
										if($collectionArr){
											?>
											<div style="float:left;margin:15px 30px;">
												<fieldset style="width:400px;padding:15px;">
													<legend>Append Data to Occurrence Records via File Upload</legend>
													<?php
													foreach($collectionArr as $collid => $collName){
														echo '<div><a href="../../collections/admin/specupload.php?uploadtype=7&matchothercatnum=1&collid='.$collid.'" target="_blank">'.$collName.'</a></div>';
													}
													?>
													* Link records by matching sampleID to otherCatalogNumbers
												</fieldset>
											</div>
											<?php
										}
										?>
									</div>
									<?php
								}
								else{
									echo '<div style="margin: 20px">No samples exist matching filter criteria</div>';
								}
								?>
							</div>
						</fieldset>
					</div>
					<?php
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