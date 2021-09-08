<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonomyEditorManager.php');
header('Content-Type: text/html; charset='.$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../taxa/taxonomy/taxonomyloader.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$tid = array_key_exists('tid',$_REQUEST)?$_REQUEST['tid']:'';
$status = "";

$loaderObj = new TaxonomyEditorManager();

$isEditor = false;
if($IS_ADMIN || array_key_exists('Taxonomy',$USER_RIGHTS)){
	$isEditor = true;
}

if($isEditor){
	if(array_key_exists('sciname',$_POST)){
		$status = $loaderObj->loadNewName($_POST);
		if(is_int($status)){
		 	header('Location: taxoneditor.php?tid='.$status);
		}
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Taxon Loader: </title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script src="../../js/symb/taxa.taxonomyloader.js?ver=19"></script>
</head>
<body>
<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="taxonomydisplay.php">Taxonomy Tree Viewer</a> &gt;&gt;
		<b>Taxonomy Loader</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($status){
			echo '<div style="color:red;font-size:120%;">'.$status.'</div>';
		}
		if($isEditor){
			?>
			<form id="loaderform" name="loaderform" action="taxonomyloader.php" method="post" onsubmit="return verifyLoadForm(this)">
				<fieldset>
					<legend><b>Add New Taxon</b></legend>
					<div>
						<div style="float:left;width:170px;">Taxon Name:</div>
						<input type="text" id="sciname" name="sciname" style="width:300px;border:inset;" value="" onchange="parseName(this.form)"/>
					</div>
					<div>
						<div style="float:left;width:170px;">Author:</div>
						<input type='text' id='author' name='author' style='width:300px;border:inset;' />
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Taxon Rank:</div>
						<select id="rankid" name="rankid" title="Rank ID" style="border:inset;">
							<option value="">Select Taxon Rank</option>
							<option value="0">Non-Ranked Node</option>
							<option value="">--------------------------------</option>
							<?php
							$tRankArr = $loaderObj->getRankArr();
							foreach($tRankArr as $rankId => $nameArr){
								foreach($nameArr as $rName){
									echo '<option value="'.$rankId.'" '.($rankId==220?' SELECTED':'').'>'.$rName.'</option>';
								}
							}
							?>
						</select>
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Unit Name 1:</div>
						<select name="unitind1" onchange="updateFullname(this.form)">
							<option value=""></option>
							<option value="&#215;">&#215;</option>
							<option value="&#8224;">&#8224;</option>
						</select>
						<input type='text' id='unitname1' name='unitname1' onchange="updateFullname(this.form)" style='width:200px;border:inset;' title='Genus or Base Name'/>
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Unit Name 2:</div>
						<select name="unitind2" onchange="updateFullname(this.form)">
							<option value=""></option>
							<option value="&#215;">&#215;</option>
						</select>
						<input type='text' id='unitname2' name='unitname2' onchange="updateFullname(this.form)" style='width:200px;border:inset;' title='epithet'/>
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Unit Name 3:</div>
						<input type='text' id='unitind3' name='unitind3' onchange="updateFullname(this.form)" style='width:50px;border:inset;' title='Rank: e.g. subsp., var., f.'/>
						<input type='text' id='unitname3' name='unitname3' onchange="updateFullname(this.form)" style='width:200px;border:inset;' title='infrasp. epithet'/>
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Parent Taxon:</div>
						<input type="text" id="parentname" name="parentname" style="width:300px;border:inset;" />
						<span id="addparentspan" style="display:none;">
							<a id="addparentanchor" href="taxonomyloader.php?target=" target="_blank">Add Parent</a>
						</span>
						<input id="parenttid" name="parenttid" type="hidden" value="" />
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Notes:</div>
						<input type='text' id='notes' name='notes' style='width:400px;border:inset;' title=''/>
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Source:</div>
						<input type='text' id='source' name='source' style='width:400px;border:inset;' title=''/>
					</div>
					<div style="clear:both;">
						<div style="float:left;width:170px;">Locality Security Status:</div>
						<select id="securitystatus" name="securitystatus" style='border:inset;'>
							<option value="0">No Security</option>
							<option value="1">Hide Locality Details</option>
						</select>
					</div>
					<div style="clear:both;">
						<fieldset>
							<legend><b>Acceptance Status</b></legend>
							<div>
								<input type="radio" id="isaccepted" name="acceptstatus" value="1" onchange="acceptanceChanged(this.form)" checked> Accepted
								<input type="radio" id="isnotaccepted" name="acceptstatus" value="0" onchange="acceptanceChanged(this.form)"> Not Accepted
							</div>
							<div id="accdiv" style="display:none;margin-top:3px;">
								Accepted Taxon:
								<input id="acceptedstr" name="acceptedstr" type="text" style="width:400px;border:inset;" />
								<input id="tidaccepted" name="tidaccepted" type="hidden" />
								<div style="margin-top:3px;">
									Unacceptability Reason:
									<input type='text' id='unacceptabilityreason' name='unacceptabilityreason' style='width:350px;border:inset;' />
								</div>
							</div>
						</fieldset>
					</div>
					<div style="clear:both;">
						<input type="submit" name="submitaction" value="Submit New Name" />
					</div>
				</fieldset>
			</form>
			<?php
		}
		else{
			?>
			<div style="margin:30px;font-weight:bold;font-size:120%;">
				You do not have permission to access this page. Please contact the portal manager.
			</div>
			<?php
		}
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</div>
</body>
</html>