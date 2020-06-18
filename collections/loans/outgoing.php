<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/outgoing.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$loanId = array_key_exists('loanid',$_REQUEST)?$_REQUEST['loanid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;

$isEditor = 0;
if($SYMB_UID && $collid){
	if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))
		|| (array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"]))){
		$isEditor = 1;
	}
}

$loanManager = new OccurrenceLoans();
if($collid) $loanManager->setCollId($collid);

$statusStr = '';
if($isEditor){
	if($formSubmit){
		if($formSubmit == 'createLoanOut'){
			$loanId = $loanManager->createNewLoanOut($_POST);
			if(!$loanId) $statusStr = $loanManager->getErrorMessage();
		}
		elseif($formSubmit == 'Save Outgoing'){
			$statusStr = $loanManager->editLoanOut($_POST);
		}
		elseif($formSubmit == 'Perform Action'){
			$statusStr = $loanManager->editSpecimen($_REQUEST);
		}
		elseif($formSubmit == 'Add New Determinations'){
			include_once($SERVER_ROOT.'/classes/OccurrenceEditorDeterminations.php');
			$occManager = new OccurrenceEditorDeterminations();
			$occidArr = $_REQUEST['occid'];
			foreach($occidArr as $k){
				$occManager->setOccId($k);
				$occManager->addDetermination($_REQUEST,$isEditor);
			}
		}
	}
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?>: Outgoing Loan Management</title>
	<?php
	$activateJQuery = true;
	if(file_exists($SERVER_ROOT.'/includes/head.php')){
		include_once($SERVER_ROOT.'/includes/head.php');
	}
	else{
		echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		var tabIndex = <?php echo $tabIndex; ?>;

		function addSpecimen(f,splist){
			if(!f.catalognumber.value){
				alert("Please enter a catalog number!");
				return false;
			}
			else{
				//alert("rpc/insertLoanSpecimens.php?loanid="+f.loanid.value+"&catalognumber="+f.catalognumber.value+"&collid="+f.collid.value);
				$.ajax({
					method: "POST",
					data: { loanid: f.loanid.value, catalognumber: f.catalognumber.value, collid: f.collid.value },
					dataType: "text",
					url: "rpc/insertLoanSpecimens.php"
				})
				.done(function(retStr) {
					if(retStr == "0"){
						document.getElementById("addspecsuccess").style.display = "none";
						document.getElementById("addspecerr1").style.display = "block";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecerr1").style.display = "none";
						}, 4000);
						//alert("ERROR: Specimen record not found in database.");
					}
					else if(retStr == "1"){
						f.catalognumber.value = '';
						document.getElementById("addspecsuccess").style.display = "block";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecsuccess").style.display = "none";
						}, 4000);
						//alert("SUCCESS: Specimen record added to loan.");
						if(splist == 0){
							document.getElementById("speclistdiv").style.display = "block";
							document.getElementById("nospecdiv").style.display = "none";
						}
					}
					else if(retStr == "2"){
						document.getElementById("addspecsuccess").style.display = "none";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "block";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecerr2").style.display = "none";
						}, 4000);
						//alert("ERROR: More than one specimen with that catalog number.");
					}
					else if(retStr == "3"){
						document.getElementById("addspecsuccess").style.display = "none";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "block";
						setTimeout(function () {
							document.getElementById("addspecerr3").style.display = "none";
						}, 4000);
						//alert("ERROR: More than one specimen with that catalog number.");
					}
					else{
						f.catalognumber.value = "";
						document.refreshspeclist.submit();
						/*
						document.getElementById("addspecsuccess").style.display = "block";
						document.getElementById("addspecerr1").style.display = "none";
						document.getElementById("addspecerr2").style.display = "none";
						document.getElementById("addspecerr3").style.display = "none";
						setTimeout(function () {
							document.getElementById("addspecsuccess").style.display = "none";
							}, 5000);
						alert("SUCCESS: Specimen added to loan.");
						*/
					}
				})
				.fail(function() {
					alert("Generation of new ID failed");
				});
			}
			return false;
		}

		function ProcessReport(){
			if(document.pressed == 'invoice'){
				document.reportsform.action ="reports/defaultinvoice.php";
			}
			else if(document.pressed == 'spec'){
				document.reportsform.action ="reports/defaultspecimenlist.php";
			}
			else if(document.pressed == 'label'){
				document.reportsform.action ="reports/defaultmailinglabel.php";
			}
			else if(document.pressed == 'envelope'){
				document.reportsform.action ="reports/defaultenvelope.php";
			}
			if(document.getElementById("printbrowser").checked){
				document.reportsform.target = "_blank";
			}
			if(document.getElementById("printdoc").checked){
				document.reportsform.target = "_self";
			}
			return true;
		}

		function verifySpecEditForm(f){
			//Make sure at least on specimen checkbox is checked
			var cbChecked = false;
			var dbElements = document.getElementsByName("occid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(dbElement.checked){
					cbChecked = true;
					break;
				}
			}
			if(!cbChecked){
				alert("Please select specimens to which you wish to apply the action");
				return false;
			}

			//If task equals delete, confirm action
			var applyTaskObj = f.applytask;
			var l = applyTaskObj.length;
			var applyTaskValue = "";
			for(var i = 0; i < l; i++) {
				if(applyTaskObj[i].checked) {
					applyTaskValue = applyTaskObj[i].value;
				}
			}
			if(applyTaskValue == "delete"){
				return confirm("Are you sure you want to remove selected specimens from this loan?");
			}
			return true;
		}

		function verifyLoanDet(){
			if(document.getElementById('dafsciname').value == ""){
				alert("Scientific Name field must have a value");
				return false;
			}
			if(document.getElementById('identifiedby').value == ""){
				alert("Determiner field must have a value (enter 'unknown' if not defined)");
				return false;
			}
			if(document.getElementById('dateidentified').value == ""){
				alert("Determination Date field must have a value (enter 's.d.' if not defined)");
				return false;
			}
			//If sciname was changed and submit was clicked immediately afterward, wait 5 seconds so that name can be verified
			if(pauseSubmit){
				var date = new Date();
				var curDate = null;
				do{
					curDate = new Date();
				}while(curDate - date < 5000 && pauseSubmit);
			}
			return true;
		}

		function initLoanDetAutocomplete(f){
			$( f.sciname ).autocomplete({
				source: "../editor/rpc/getspeciessuggest.php",
				minLength: 3,
				change: function(event, ui) {
					if(f.sciname.value){
						pauseSubmit = true;
						verifyLoanDetSciName(f);
					}
					else{
						f.scientificnameauthorship.value = "";
						f.family.value = "";
						f.tidtoadd.value = "";
					}
				}
			});
		}

		function verifyLoanDetSciName(f){
			$.ajax({
				type: "POST",
				url: "../editor/rpc/verifysciname.php",
				dataType: "json",
				data: { term: f.sciname.value }
			}).done(function( data ) {
				if(data){
					f.scientificnameauthorship.value = data.author;
					f.family.value = data.family;
					f.tidtoadd.value = data.tid;
				}
				else{
		            alert("WARNING: Taxon not found. It may be misspelled or needs to be added to taxonomic thesaurus by a taxonomic editor.");
					f.scientificnameauthorship.value = "";
					f.family.value = "";
					f.tidtoadd.value = "";
				}
			});
		}

		function selectAll(cb){
			boxesChecked = true;
			if(!cb.checked){
				boxesChecked = false;
			}
			var dbElements = document.getElementsByName("occid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				dbElement.checked = boxesChecked;
			}
		}

		function toggle(target){
			var objDiv = document.getElementById(target);
			if(objDiv){
				if(objDiv.style.display=="none"){
					objDiv.style.display = "block";
				}
				else{
					objDiv.style.display = "none";
				}
			}
			else{
			  	var divs = document.getElementsByTagName("div");
			  	for (var h = 0; h < divs.length; h++) {
			  	var divObj = divs[h];
					if(divObj.className == target){
						if(divObj.style.display=="none"){
							divObj.style.display="block";
						}
					 	else {
					 		divObj.style.display="none";
					 	}
					}
				}
			}
		}

	</script>
	<script type="text/javascript" src="../../js/symb/collections.loans.js"></script>
	<style>
		fieldset{ padding:10px; }
		fieldset legend{ font-weight:bold }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management Menu</a> &gt;&gt;
		<a href="index.php?collid=<?php echo $collid; ?>">Loan Index</a> &gt;&gt;
		<a href="outgoing.php?collid=<?php echo $collid; ?>"><b>Outgoing Loan Management</b></a>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor && $collid){
			//Collection is defined and User is logged-in and have permissions
			if($statusStr){
				$colorStr = 'red';
				if(stripos($statusStr,'SUCCESS') !== false) $colorStr = 'green';
				?>
				<hr/>
				<div style="margin:15px;color:<?php echo $colorStr; ?>;">
					<?php echo $statusStr; ?>
				</div>
				<hr/>
				<?php
			}
			$specList = $loanManager->getSpecList($loanId);
			?>
			<div id="tabs" style="margin:0px;">
			    <ul>
					<li><a href="#outloandetaildiv"><span>Loan Details</span></a></li>
					<li><a href="#outloanspecdiv"><span>Specimens</span></a></li>
					<li><a href="#outloandeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="outloandetaildiv">
					<?php
					$loanArr = $loanManager->getLoanOutDetails($loanId);
					?>
					<form name="editloanform" action="outgoing.php" method="post">
						<fieldset>
							<legend>Loan Out Details</legend>
							<div style="padding-top:18px;float:left;">
								<span>
									<b>Loan Number:</b> <input type="text" autocomplete="off" name="loanidentifierown" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $loanArr['loanidentifierown']; ?>" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Entered By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="createdbyown" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $loanArr['createdbyown']; ?>" onchange=" " disabled />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Processed By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="processedbyown" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyown']; ?>" onchange=" " />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Sent:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="datesent" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['datesent']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Due:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="datedue" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['datedue']; ?>" onchange="verifyDueDate(this);" title="format: yyyy-mm-dd" />
								</span>
							</div>
							<div style="padding-top:8px;float:left;">
								<span>
									Sent To:
								</span><br />
								<span>
									<select name="iidborrower" style="width:400px;">
										<?php
										$instArr = $loanManager->getInstitutionArr();
										foreach($instArr as $k => $v){
											echo '<option value="'.$k.'" '.($loanArr['iidborrower']==$k?'SELECTED':'').'>'.$v.'</option>';
										}
										?>
									</select>
								</span>
								<?php
								if($IS_ADMIN){
									?>
									<span>
										<a href="../admin/institutioneditor.php?iid=<?php echo $loanArr['iidborrower']; ?>" target="_blank" title="Edit institution details (option available only to Super Admin)">
											<img src="../../images/edit.png" style="width:15px;" />
										</a>
									</span>
									<?php
								}
								?>
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Requested for:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="forwhom" tabindex="100" maxlength="32" style="width:180px;" value="<?php echo $loanArr['forwhom']; ?>" onchange=" " />
									</span>
								</div>
								<div style="padding-top:15px;margin-left:20px;float:left;">
									<span>
										<b>Specimen Total:</b> <input type="text" name="totalspecimens" tabindex="100" maxlength="32" style="width:80px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo count($specList); ?>" onchange=" " disabled />
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										# of Boxes:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="totalboxes" tabindex="100" maxlength="32" style="width:50px;" value="<?php echo $loanArr['totalboxes']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										Shipping Service:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="shippingmethod" tabindex="100" maxlength="32" style="width:180px;" value="<?php echo $loanArr['shippingmethod']; ?>" onchange=" " />
									</span>
								</div>
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Loan Description:
									</span><br />
									<span>
										<textarea name="description" rows="10" style="width:320px;resize:vertical;" onchange=" "><?php echo $loanArr['description']; ?></textarea>
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										Notes:
									</span><br />
									<span>
										<textarea name="notes" rows="10" style="width:320px;resize:vertical;" onchange=" "><?php echo $loanArr['notes']; ?></textarea>
									</span>
								</div>
							</div>
							<div style="width:100%;padding-top:8px;float:left;">
								<hr />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Date Received:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="datereceivedown" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['datereceivedown']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Ret. Processed By:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="processedbyreturnown" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyreturnown']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Date Closed:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="dateclosed" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['dateclosed']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" />
									</span>
								</div>
							</div>
							<div style="clear:left;padding-top:8px;float:left;">
								<span>
									Additional Invoice Message:
								</span><br />
								<span>
									<textarea name="invoicemessageown" rows="5" style="width:700px;resize:vertical;" onchange=" "><?php echo $loanArr['invoicemessageown']; ?></textarea>
								</span>
							</div>
							<div style="clear:both;padding-top:8px;float:right;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
								<button name="formsubmit" type="submit" value="Save Outgoing">Save</button>
							</div>
						</fieldset>
					</form>
					<form name="reportsform" onsubmit="return ProcessReport();" method="post" onsubmit="">
						<fieldset>
							<legend>Generate Loan Paperwork</legend>
							<div style="float:right;">
								<b>International Shipment:</b> <input type="checkbox" name="international" value="1" /><br /><br />
								<b>Mailing Account #:</b> <input type="text" autocomplete="off" name="mailaccnum" tabindex="100" maxlength="32" style="width:100px;" value="" />
							</div>
							<div style="padding-bottom:2px;">
								<b>Print Method:</b> <input type="radio" name="print" id="printbrowser" value="browser" checked /> Print in Browser
								<input type="radio" name="print" id="printdoc" value="doc" /> Export to DOCX
							</div>
							<div style="padding-bottom:8px;">
								<b>Invoice Language:</b> <input type="radio" name="languagedef" value="0" checked /> English
								<input type="radio" name="languagedef" value="1" /> English/Spanish
								<input type="radio" name="languagedef" value="2" /> Spanish
							</div>
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
							<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="invoice">Invoice</button>
							<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="spec">Specimen List</button>
							<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="label">Mailing Label</button>
							<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="envelope">Envelope</button>
						</fieldset>
					</form>
				</div>
				<div id="outloanspecdiv">
					<div style="float:right;margin:10px;">
						<a href="#" onclick="toggle('newspecdiv');">
							<img src="../../images/add.png" title="Add New Specimen" />
						</a>
					</div>
					<div id="newspecdiv" style="display:none;">
						<fieldset>
							<legend>Add Specimen</legend>
							<form name="addspecform" style="margin-bottom:0px;padding-bottom:0px;" action="outgoing.php" method="post" onsubmit="addSpecimen(this,<?php echo (!$specList?'0':'1'); ?>);return false;">
								<div style="float:left;padding-bottom:2px;">
									<b>Catalog Number: </b><input type="text" autocomplete="off" name="catalognumber" maxlength="255" style="width:200px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="" />
								</div>
								<div id="addspecsuccess" style="float:left;margin-left:30px;padding-bottom:2px;color:green;display:none;">
									SUCCESS: Specimen record added to loan.
								</div>
								<div id="addspecerr1" style="float:left;margin-left:30px;padding-bottom:2px;color:red;display:none;">
									ERROR: No specimens found with that catalog number.
								</div>
								<div id="addspecerr2" style="float:left;margin-left:30px;padding-bottom:2px;color:red;display:none;">
									ERROR: More than one specimen located with same catalog number.
								</div>
								<div id="addspecerr3" style="float:left;margin-left:30px;padding-bottom:2px;color:orange;display:none;">
									Warning: Specimen already linked to loan.
								</div>
								<div style="padding-top:8px;clear:left;float:left;">
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
									<input name="formsubmit" type="submit" value="Add Specimen" />
								</div>
							</form>
							<div id="refreshbut" style="float:left;padding-top:10px;margin-left:10px;">
								<form style="margin-bottom:0px;" name="refreshspeclist" action="outgoing.php" method="post">
									<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input name="tabindex" type="hidden" value="1" />
									<input name="formsubmit" type="submit" value="Refresh List" />
								</form>
							</div>
						</fieldset>
					</div>
					<div id="speclistdiv" style="<?php echo (!$specList?'display:none;':''); ?>">
						<div style="height:25px;margin-top:15px;">
							<div style="float:left;margin-left:15px;">
								<input name="" value="" type="checkbox" onclick="selectAll(this);" />
								Select/Deselect All
							</div>
							<div id="refreshbut" style="display:none;float:right;margin-right:15px;">
								<form name="refreshspeclist" action="outgoing.php" method="post">
									<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
									<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
									<input name="tabindex" type="hidden" value="1" />
									<input name="formsubmit" type="submit" value="Refresh List" />
								</form>
							</div>
						</div>
						<form name="speceditform" action="outgoing.php" method="post" onsubmit="return verifySpecEditForm(this)" >
							<table class="styledtable" style="font-family:Arial;font-size:12px;">
								<tr>
									<th style="width:25px;text-align:center;">&nbsp;</th>
									<th style="width:100px;text-align:center;">Catalog Number</th>
									<th style="width:375px;text-align:center;">Details</th>
									<th style="width:75px;text-align:center;">Date Returned</th>
								</tr>
								<?php
								foreach($specList as $k => $specArr){
									?>
									<tr>
										<td>
											<input name="occid[]" type="checkbox" value="<?php echo $specArr['occid']; ?>" />
										</td>
										<td>
											<a href="#" onclick="openIndPopup(<?php echo $specArr['occid']; ?>); return false;">
												<?php echo $specArr['catalognumber']; ?>
											</a>
											<a href="#" onclick="openEditorPopup(<?php echo $specArr['occid']; ?>); return false;">
												<img src="../../images/edit.png" />
											</a>
										</td>
										<td>
											<?php
											$loc = $specArr['locality'];
											if(strlen($loc) > 500) $loc = substr($loc,400);
											echo '<i>'.$specArr['sciname'].'</i>; ';
											echo  $specArr['collector'].'; '.$loc;
											?>

										</td>
										<td><?php echo $specArr['returndate']; ?></td>
									</tr>
									<?php
								}
							?>
							</table>
							<table style="width:100%;">
								<tr>
									<td colspan="10" valign="bottom">
										<div id="newdetdiv" style="display:none;">
											<fieldset style="margin: 15px 15px 0px 15px;">
												<legend><b>Add a New Determinations</b></legend>
												<div style='margin:3px;'>
													<b>Identification Qualifier:</b>
													<input type="text" name="identificationqualifier" title="e.g. cf, aff, etc" />
												</div>
												<div style='margin:3px;'>
													<b>Scientific Name:</b>
													<input type="text" id="dafsciname" name="sciname" style="background-color:lightyellow;width:350px;" onfocus="initLoanDetAutocomplete(this.form)" />
													<input type="hidden" id="daftidtoadd" name="tidtoadd" value="" />
													<input type="hidden" name="family" value="" />
												</div>
												<div style='margin:3px;'>
													<b>Author:</b>
													<input type="text" name="scientificnameauthorship" style="width:200px;" />
												</div>
												<div style='margin:3px;'>
													<b>Confidence of Determination:</b>
													<select name="confidenceranking">
														<option value="8">High</option>
														<option value="5" selected>Medium</option>
														<option value="2">Low</option>
													</select>
												</div>
												<div style='margin:3px;'>
													<b>Determiner:</b>
													<input type="text" name="identifiedby" id="identifiedby" style="background-color:lightyellow;width:200px;" />
												</div>
												<div style='margin:3px;'>
													<b>Date:</b>
													<input type="text" name="dateidentified" id="dateidentified" style="background-color:lightyellow;" onchange="detDateChanged(this.form);" />
												</div>
												<div style='margin:3px;'>
													<b>Reference:</b>
													<input type="text" name="identificationreferences" style="width:350px;" />
												</div>
												<div style='margin:3px;'>
													<b>Notes:</b>
													<input type="text" name="identificationremarks" style="width:350px;" />
												</div>
												<div style='margin:3px;'>
													<input type="checkbox" name="makecurrent" value="1" /> Make this the current determination
												</div>
												<div style='margin:3px;'>
													<input type="checkbox" name="printqueue" value="1" /> Add to Annotation Print Queue
												</div>
												<div style='margin:15px;'>
													<div style="float:left;">
														<input type="submit" name="formsubmit" onclick="verifyLoanDet();" value="Add New Determinations" />
													</div>
												</div>
											</fieldset>
										</div>
										<div style="margin:10px;float:left;">
											<div style="float:left;">
												<input name="applytask" type="radio" value="check" title="Check-in Specimens" CHECKED />Check-in Specimens<br/>
												<input name="applytask" type="radio" value="delete" title="Delete Specimens" />Delete Specimens from Loan
											</div>
											<span style="margin-left:25px;float:left;">
												<input name="formsubmit" type="submit" value="Perform Action" />
												<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
												<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
												<input name="tabindex" type="hidden" value="1" />
											</span>
										</div>
										<div style="margin:10px;float:right;">
											<div id="detAddToggleDiv" onclick="toggle('newdetdiv');">
												<a href="#" onclick="return false;">Add New Determinations</a>
											</div>
										</div>
									</td>
								</tr>
							</table>
						</form>
					</div>
					<div id="nospecdiv" style="font-weight:bold;font-size:120%;<?php echo ($specList?'display:none;':''); ?>">There are no specimens registered for this loan.</div>
				</div>
				<div id="outloandeldiv">
					<form name="deloutloanform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this loan?')">
						<fieldset style="width:550px;margin:20px;padding:20px;">
							<legend>Delete Outgoing Loan</legend>
							<?php
							if($specList){
								?>
								<div style=";margin-bottom:15px;">
									Loan cannot be deleted until all linked specimens are removed
								</div>
								<?php
							}
							?>
							<input name="formsubmit" type="submit" value="Delete Loan" <?php if($specList) echo 'DISABLED'; ?> />
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
						</fieldset>
					</form>
				</div>
			</div>
			<?php
		}
		else{
			if(!$isEditor) echo '<h2>You are not authorized to add occurrence records</h2>';
			else echo '<h2>ERROR: unknown error, please contact system administrator</h2>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>