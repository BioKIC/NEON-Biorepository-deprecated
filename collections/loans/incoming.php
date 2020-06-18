<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/incoming.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

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
		if($formSubmit == 'createLoanIn'){
			$loanId = $loanManager->createNewLoanIn($_POST);
			if(!$loanId) $statusStr = $loanManager->getErrorMessage();
		}
		elseif($formSubmit == 'Save Incoming'){
			$statusStr = $loanManager->editLoanIn($_POST);
		}
	}
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?>: Incoming Loan Management</title>
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

		function verifyLoanInEditForm(f){
			if(f.iidowner.options[f.iidowner.selectedIndex].value == 0){
				alert("Select an institution");
				return false;
			}
			if(f.loanidentifierown.value == ""){
				alert("Enter the sender's loan number");
				return false;
			}
			return true;
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
	<div class="navpath">
		<a href='../../index.php'>Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management Menu</a> &gt;&gt;
		<a href="index.php?collid=<?php echo $collid; ?>">Loan Index</a> &gt;&gt;
		<a href="incoming.php?collid=<?php echo $collid; ?>"><b>Incoming Loan Management</b></a>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor && $collid){
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
					<li><a href="#loandiv"><span>Loan Details</span></a></li>
					<?php
					if($specList){
						?>
						<li><a href="#specdiv"><span>Specimens</span></a></li>
						<?php
					}
					?>
					<li><a href="#inloandeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="loandiv">
					<?php
					$loanArr = $loanManager->getLoanInDetails($loanId);
					?>
					<form name="editloanform" action="incoming.php" method="post" onsubmit="return verifyLoanInEditForm(this)">
						<fieldset>
							<legend>Loan In Details</legend>
							<div style="padding-top:18px;float:left;">
								<span>
									<b>Loan Number:</b> <input type="text" autocomplete="off" name="loanidentifierborr" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo ($loanArr['loanidentifierborr']?$loanArr['loanidentifierborr']:$loanArr['loanidentifierown']); ?>" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Entered By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="createdbyborr" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo ($loanArr['createdbyborr']?$loanArr['createdbyborr']:$PARAMS_ARR['un']); ?>" onchange=" " disabled />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Processed By:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="processedbyborr" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyborr']; ?>" onchange=" " />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Received:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="datereceivedborr" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['datereceivedborr']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" />
								</span>
							</div>
							<div style="margin-left:20px;padding-top:4px;float:left;">
								<span>
									Date Due:
								</span><br />
								<span>
									<input type="text" autocomplete="off" name="datedue" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['datedue']; ?>" onchange="verifyDueDate(this);" title="format: yyyy-mm-dd" <?php echo ($loanArr['collidown']?'disabled':''); ?> />
								</span>
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Sent From:
									</span><br />
									<span>
										<select name="iidowner" style="width:400px;" >
											<?php
											$instArr = $loanManager->getInstitutionArr();
											foreach($instArr as $k => $v){
												echo '<option value="'.$k.'" '.($loanArr['iidowner']==$k?'SELECTED':'').'>'.$v.'</option>';
											}
											?>
										</select>
									</span>
								</div>
								<div style="margin-left:100px;float:left;">
									<span>
										Sender's Loan Number:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="loanidentifierown" maxlength="255" style="width:160px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $loanArr['loanidentifierown']; ?>" <?php echo ($loanArr['collidown']?'disabled':''); ?> />
									</span>
								</div>
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
								<div style="padding-top:15px;margin-left:40px;float:left;">
									<span>
										<b>Specimen Total:</b> <input type="text" autocomplete="off" name="numspecimens" tabindex="100" maxlength="32" style="width:80px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo ($loanArr['collidown']?count($specList):$loanArr['numspecimens']); ?>" onchange=" " <?php echo ($loanArr['collidown']?'disabled':''); ?> />
									</span>
								</div>
							</div>
							<div style="padding-top:8px;clear:both;">
								<div style="float:left;">
									<span>
										Loan Description:
									</span><br />
									<span>
										<textarea name="description" rows="10" style="width:320px;resize:vertical;" onchange=" " <?php echo ($loanArr['collidown']?'disabled="disabled"':''); ?> ><?php echo $loanArr['description']; ?></textarea>
									</span>
								</div>
								<div style="margin-left:20px;float:left;">
									<span>
										Notes:
									</span><br />
									<span>
										<textarea name="notes" rows="10" style="width:320px;resize:vertical;" onchange=" " <?php echo ($loanArr['collidown']?'disabled="disabled"':''); ?> ><?php echo $loanArr['notes']; ?></textarea>
									</span>
								</div>
							</div>
							<div style="width:100%;padding-top:8px;float:left;">
								<hr />
							</div>
							<div style="padding-top:8px;float:left;">
								<div style="float:left;">
									<span>
										Date Returned:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="datesentreturn" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['datesentreturn']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Ret. Processed By:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="processedbyreturnborr" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $loanArr['processedbyreturnborr']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										# of Boxes:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="totalboxesreturned" tabindex="100" maxlength="32" style="width:50px;" value="<?php echo $loanArr['totalboxesreturned']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Shipping Service:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="shippingmethodreturn" tabindex="100" maxlength="32" style="width:180px;" value="<?php echo $loanArr['shippingmethodreturn']; ?>" onchange=" " />
									</span>
								</div>
								<div style="margin-left:40px;float:left;">
									<span>
										Date Closed:
									</span><br />
									<span>
										<input type="text" autocomplete="off" name="dateclosed" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $loanArr['dateclosed']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" <?php echo ($loanArr['collidown']?'disabled':''); ?> />
									</span>
								</div>
							</div>
							<div style="padding-top:8px;float:left;">
								<div>
									Additional Invoice Message:
								</div>
								<div>
									<textarea name="invoicemessageborr" rows="5" style="width:700px;resize:vertical;" onchange=" "><?php echo $loanArr['invoicemessageborr']; ?></textarea>
								</div>
							</div>
							<div style="clear:both;padding-top:8px;">
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="collidborr" type="hidden" value="<?php echo $collid; ?>" />
								<input name="loanid" type="hidden" value="<?php echo $loanId; ?>" />
								<button name="formsubmit" type="submit" value="Save Incoming">Save</button>
							</div>
						</fieldset>
					</form>
					<form name="reportsform" onsubmit="return ProcessReport();" method="post">
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
							<?php
							if($specList){ ?>
								<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="spec">Specimen List</button>
							<?php } ?>
							<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="label">Mailing Label</button>
							<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="envelope">Envelope</button>
						</fieldset>
					</form>
				</div>
				<?php
				if($specList){
					?>
					<div id="specdiv">
						<table class="styledtable" style="font-family:Arial;font-size:12px;">
							<tr>
								<th style="width:100px;text-align:center;">Catalog Number</th>
								<th style="width:375px;text-align:center;">Details</th>
								<th style="width:75px;text-align:center;">Date Returned</th>
							</tr>
							<?php
							foreach($specList as $k => $specArr){
								?>
								<tr>
									<td>
										<a href="#" onclick="openIndPopup(<?php echo $k; ?>);">
											<?php echo $specArr['catalognumber']; ?>
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
					</div>
					<?php
				}
				?>
				<div id="inloandeldiv">
					<form name="delinloanform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this loan?')">
						<fieldset style="width:550px;margin:20px;padding:20px;">
							<legend>Delete Incoming Loan</legend>
							<?php
							if($specList){
								?>
								<div style=";margin-bottom:15px;">
									Loan cannot be deleted until all linked specimens are removed (can only be done by lending institution)
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