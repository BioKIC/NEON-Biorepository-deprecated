<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/samplecheckineditor.php');

$action = array_key_exists("action",$_POST)?$_POST["action"]:"";
$samplePK = array_key_exists("samplePK",$_REQUEST)?$_REQUEST["samplePK"]:"";

$shipManager = new ShipmentManager();

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}

$status = "";
if($isEditor){
	if($action == 'save'){
		if($shipManager->editSampleCheckin($_POST)) $status = 'close';
	}
	elseif($action == 'nullCheckin'){
		if($shipManager->resetSampleCheckin($samplePK)) $status = 'close';
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Sample Check-in Editor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="../../js/jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
			if($status == 'close') echo 'closeWindow();';
			?>

			$("input").each(function() {
				$(this).change(function(){ $("#submitButton").prop("disabled",false); });
			});
			$("select").each(function() {
				$(this).change(function(){ $("#submitButton").prop("disabled",false); });
			});
		});

		function verifySampleEditForm(f){

			return true;
		}

		function closeWindow(){
			window.opener.refreshForm.submit();
			window.close();
		}
	</script>
	<style type="text/css">
		fieldset{ padding:15px }
		.fieldGroupDiv{ clear:both; margin-top:2px; height: 25px; }
		.fieldDiv{ float:left; margin-left: 10px}
	</style>
</head>
<body>
<div id="popup-innertext">
	<?php
	if($isEditor && $samplePK){
		$sampleArr = $shipManager->getSampleArr($samplePK);
		?>
		<fieldset style="width:800px;">
			<legend><b><?php echo $sampleArr['sampleID'].' (#'.$samplePK.')'; ?></b></legend>
			<form method="post" action="samplecheckineditor.php">
				<div class="fieldGroupDiv">
					<b>Accepted for Analysis:</b>
					<?php
					$acceptedForAnalysis = (isset($sampleArr['acceptedForAnalysis'])?$sampleArr['acceptedForAnalysis']:'');
					?>
					<input name="acceptedForAnalysis" type="radio" value="1" <?php echo ($acceptedForAnalysis==1?'checked':''); ?> /> Yes
					<input name="acceptedForAnalysis" type="radio" value="0" <?php echo ($acceptedForAnalysis==='0'?'checked':''); ?> /> No
				</div>
				<div class="fieldGroupDiv">
					<b>Sample condition:</b>
					<select name="sampleCondition">
						<option value="">------------------------------------------</option>
						<?php
						$sampleCondition = (isset($sampleArr['sampleCondition'])?$sampleArr['sampleCondition']:'');
						$condArr = $shipManager->getConditionArr();
						foreach($condArr as $condKey => $condValue){
							echo '<option value="'.$condKey.'" '.($condKey==$sampleCondition?'selected':'').'>'.$condValue.'</option>';
						}
						?>
					</select>
				</div>
				<div class="fieldGroupDiv">
					<b>Notes:</b> <input name="sampleNotes" type="text" value="<?php echo isset($sampleArr['sampleNotes'])?$sampleArr['sampleNotes']:''; ?>" style="width:500px" />
				</div>
				<div style="clear:both;margin:15px">
					<input name="samplePK" type="hidden" value="<?php echo $samplePK; ?>" />
					<div><button id="submitButton" type="submit" name="action" value="save" disabled>Save Changes</button></div>
					<?php
					if(isset($sampleArr['checkinTimestamp']) && $sampleArr['checkinTimestamp']){
						?>
						<div style="margin-top:15px">
							<button type="submit" name="action" value="nullCheckin" onclick="return confirm('Are you sure you want to totally reset check-in status?')">Clear Check-in Details</button>
						</div>
						<?php
					}
					?>
				</div>
			</form>
		</fieldset>
		<?php
	}
	else{
		?>
		<div style='font-weight:bold;margin:30px;'>
			Sample identifier not set or you do not have permissions to view manifests
		</div>
		<?php
	}
	?>
</div>
</body>
</html>