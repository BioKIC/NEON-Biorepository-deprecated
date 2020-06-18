<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/exchange.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$exchangeId = array_key_exists('exchangeid',$_REQUEST)?$_REQUEST['exchangeid']:0;
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
		if($formSubmit == 'createExchange'){
			$exchangeId = $loanManager->createNewExchange($_POST);
			if(!$exchangeId) $statusStr = $loanManager->getErrorMessage();
		}
		elseif($formSubmit == 'Save Exchange'){
			$statusStr = $loanManager->editExchange($_POST);
		}
	}
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?>: Exchange Management</title>
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

		function displayNewExchange(){
			if(document.getElementById("exchangeToggle")){
				toggle('newexchangediv');
			}
			var f = document.newexchangegiftform;
			if(f.identifier.value == ""){
				generateNewId(f.collid.value,f.identifier,"ex");
			}
		}

		function verfifyExchangeAddForm(f){
			if(f.iid.options[f.iid.selectedIndex].value == 0){
				alert("Select an institution");
				return false;
			}
			if(f.identifier.value == ""){
				alert("Enter an exchange identifier");
				return false;
			}
			$.ajax({
				method: "POST",
				data: { ident: f.identifier.value, collid: f.collid.value, type: "ex" },
				dataType: "text",
				url: "rpc/identifierCheck.php"
			})
			.done(function(retCode) {
				if(retCode == 1) alert("There is already a transaction with that identifier, please enter a different one.");
				else f.submit();
			});
			return false;
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
		<a href="incoming.php?collid=<?php echo $collid; ?>"><b>Exchange Management</b></a>
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
			?>
			<div id="tabs" style="margin:0px;">
			    <ul>
					<li><a href="#exchangedetaildiv"><span>Exchange Details</span></a></li>
					<li><a href="#exchangedeldiv"><span>Admin</span></a></li>
				</ul>
				<div id="exchangedetaildiv" style="">
					<?php
					//Show loan details
					$exchangeArr = $loanManager->getExchangeDetails($exchangeId);
					$exchangeValue = $loanManager->getExchangeValue($exchangeId);
					$exchangeTotal = $loanManager->getExchangeTotal($exchangeId);
					?>
					<form name="editexchangegiftform" action="exchange.php" method="post">
						<?php
						if($exchangeArr['transactiontype']=='Adjustment'){ ?>
							<fieldset>
								<legend>Edit Adjustment</legend>
								<div style="padding-top:4px;float:left;">
									<div style="padding-top:12px;float:left;">
										<span>
											<b>Transaction Number:</b> <input type="text" autocomplete="off" name="identifier" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $exchangeArr['identifier']; ?>" disabled />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Transaction Type:
										</span><br />
										<span>
											<select name="transactiontype" style="width:100px;">
												<?php if($exchangeArr['transactiontype']=='Shipment'){ ?>
													<option value="Shipment" <?php echo ($exchangeArr['transactiontype']=='Shipment'?'SELECTED':'');?>>Shipment</option>
												<?php }
												if($exchangeArr['transactiontype']=='Adjustment'){ ?>
													<option value="Adjustment" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'SELECTED':'');?>>Adjustment</option>
												<?php } ?>
											</select>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Entered By:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="createdby" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $exchangeArr['createdby']; ?>" onchange=" " disabled />
										</span>
									</div>
								</div>
								<div style="padding-top:8px;float:left;">
									<div style="float:left;">
										<span>
											Institution:
										</span>
										<span>
											<select name="iid" style="width:400px;" >
												<?php
												$instArr = $loanManager->getInstitutionArr();
												foreach($instArr as $k => $v){
													echo '<option value="'.$k.'" '.($k==$exchangeArr['iid']?'SELECTED':'').'>'.$v.'</option>';
												}
												?>
											</select>
										</span>
									</div>
									<div style="float:left;">
										<span style="margin-left:40px;">
											<b>Adjustment Amount:</b>&nbsp;&nbsp;<input type="text" autocomplete="off" name="adjustment" tabindex="100" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['adjustment']; ?>" onchange=" " />
										</span>
									</div>
								</div>
							</fieldset>
							<?php
						}
						else{
							?>
							<fieldset>
								<legend>Edit Gift/Exchange</legend>
								<div style="padding-top:4px;float:left;">
									<div style="padding-top:12px;float:left;">
										<span>
											<b>Transaction Number:</b> <input type="text" autocomplete="off" name="identifier" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $exchangeArr['identifier']; ?>" disabled />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Entered By:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="createdby" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $exchangeArr['createdby']; ?>" onchange=" " disabled />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Date Shipped:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="datesent" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $exchangeArr['datesent']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" />
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Date Received:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="datereceived" tabindex="100" maxlength="32" style="width:100px;" value="<?php echo $exchangeArr['datereceived']; ?>" onchange="verifyDate(this);" title="format: yyyy-mm-dd" />
										</span>
									</div>
								</div>
								<div style="padding-top:8px;padding-bottom:8px;float:left;">
									<div style="float:left;">
										<span>
											Institution:
										</span><br />
										<span>
											<select name="iid" style="width:400px;" >
												<?php
												$instArr = $loanManager->getInstitutionArr();
												foreach($instArr as $k => $v){
													echo '<option value="'.$k.'" '.($k==$exchangeArr['iid']?'SELECTED':'').'>'.$v.'</option>';
												}
												?>
											</select>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Transaction Type:
										</span><br />
										<span>
											<select name="transactiontype" style="width:100px;">
												<?php if($exchangeArr['transactiontype']=='Shipment'){ ?>
													<option value="Shipment" <?php echo ($exchangeArr['transactiontype']=='Shipment'?'SELECTED':'');?>>Shipment</option>
												<?php }
												if($exchangeArr['transactiontype']=='Adjustment'){ ?>
													<option value="Adjustment" <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'SELECTED':'');?>>Adjustment</option>
												<?php } ?>
											</select>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											In/Out:
										</span><br />
										<span>
											<select name="in_out" style="width:100px;">
												<?php if($exchangeArr['transactiontype']=='Adjustment'){ ?>
													<option value="" <?php echo (!$exchangeArr['in_out']?'SELECTED':'');?>>   </option>
												<?php }
												if($exchangeArr['transactiontype']=='Shipment'){ ?>
													<option value="Out" <?php echo ('Out'==$exchangeArr['in_out']?'SELECTED':'');?>>Out</option>
													<option value="In" <?php echo ('In'==$exchangeArr['in_out']?'SELECTED':'');?>>In</option>
												<?php } ?>
											</select>
										</span>
									</div>
								</div>
								<div style="padding-top:8px;padding-bottom:8px;">
									<table class="styledtable" style="font-family:Arial;font-size:12px;">
										<tr>
											<th style="width:220px;text-align:center;">Gift Specimens</th>
											<th style="width:220px;text-align:center;">Exchange Specimens</th>
											<th style="width:220px;text-align:center;">Transaction Totals</th>
										</tr>
										<tr style="text-align:right;">
											<td><b>Total Gifts:</b>&nbsp;&nbsp;<input type="text" autocomplete="off" name="totalgift" tabindex="100" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalgift']; ?>" onchange=" " <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Total Unmounted:</b>&nbsp;&nbsp;<input type="text" autocomplete="off" name="totalexunmounted" tabindex="100" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalexunmounted']; ?>" onchange=" " <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Exchange Value:</b>&nbsp;&nbsp;<input type="text" name="exchangevalue" tabindex="100" maxlength="32" style="width:80px;border:1px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo ($exchangeValue?$exchangeValue:'');?>" onchange=" " disabled="disabled" /></td>
										</tr>
										<tr style="text-align:right;">
											<td><b>Total Gifts For Det:</b>&nbsp;&nbsp;<input type="text" autocomplete="off" name="totalgiftdet" tabindex="100" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalgiftdet']; ?>" onchange=" " <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Total Mounted:</b>&nbsp;&nbsp;<input type="text" autocomplete="off" name="totalexmounted" tabindex="100" maxlength="32" style="width:80px;" value="<?php echo $exchangeArr['totalexmounted']; ?>" onchange=" " <?php echo ($exchangeArr['transactiontype']=='Adjustment'?'disabled':'');?> /></td>
											<td><b>Total Specimens:</b>&nbsp;&nbsp;<input type="text" name="totalspecimens" tabindex="100" maxlength="32" style="width:80px;border:1px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo ($exchangeTotal?$exchangeTotal:'');?>" onchange=" " disabled="disabled" /></td>
										</tr>
									</table>
								</div>
								<div style="padding-top:8px;float:left;">
									<div style="padding-top:15px;float:left;">
										<span style="margin-left:25px;">
											<b>Current Balance:</b> <input type="text" name="invoicebalance" tabindex="100" maxlength="32" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="<?php echo $exchangeArr['invoicebalance']; ?>" onchange=" " disabled />
										</span>
									</div>
									<div style="margin-left:100px;float:left;">
										<span>
											# of Boxes:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="totalboxes" tabindex="100" maxlength="32" style="width:50px;" value="<?php echo $exchangeArr['totalboxes']; ?>" onchange=" " />
										</span>
									</div>
									<div style="margin-left:60px;float:left;">
										<span>
											Shipping Service:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="shippingmethod" tabindex="100" maxlength="32" style="width:180px;" value="<?php echo $exchangeArr['shippingmethod']; ?>" onchange=" " />
										</span>
									</div>
								</div>
								<div style="padding-top:8px;float:left;">
									<div style="float:left;">
										<span>
											Description:
										</span><br />
										<span>
											<textarea name="description" rows="10" style="width:320px;resize:vertical;" onchange=" "><?php echo $exchangeArr['description']; ?></textarea>
										</span>
									</div>
									<div style="margin-left:40px;float:left;">
										<span>
											Notes:
										</span><br />
										<span>
											<textarea name="notes" rows="10" style="width:320px;resize:vertical;" onchange=" "><?php echo $exchangeArr['notes']; ?></textarea>
										</span>
									</div>
								</div>
								<div style="width:100%;padding-top:8px;float:left;">
									<hr />
								</div>
								<div style="padding-top:8px;float:left;">
									<span>
										Additional Message:
									</span><br />
									<span>
										<textarea name="invoicemessage" rows="5" style="width:700px;resize:vertical;" onchange=" "><?php echo $exchangeArr['invoicemessage']; ?></textarea>
									</span>
								</div>
							</fieldset>
							<?php
						}
						?>
						<div style="clear:both;padding-top:8px;float:right;">
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="exchangeid" type="hidden" value="<?php echo $exchangeId; ?>" />
							<button name="formsubmit" type="submit" value="Save Exchange">Save</button>
						</div>
					</form>
					<?php
					if($exchangeArr['transactiontype']=='Shipment'){ ?>
						<form name="reportsform" method="post" onsubmit="return ProcessReport();">
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
								<input name="exchangeid" type="hidden" value="<?php echo $exchangeId; ?>" />
								<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="invoice">Invoice</button>
								<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="label">Mailing Label</button>
								<button name="formsubmit" type="submit" onclick="document.pressed=this.value" value="envelope">Envelope</button>
							</fieldset>
						</form>
					<?php } ?>
				</div>
				<div id="exchangedeldiv">
					<form name="delexchangeform" action="index.php" method="post" onsubmit="return confirm('Are you sure you want to permanently delete this exchange?')">
						<fieldset style="width:350px;margin:20px;padding:20px;">
							<legend>Delete Exchange</legend>
							<input name="formsubmit" type="submit" value="Delete Exchange" />
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="exchangeid" type="hidden" value="<?php echo $exchangeId; ?>" />
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