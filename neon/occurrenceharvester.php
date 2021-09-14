<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/OccurrenceHarvester.php');
header('Content-Type: text/html; charset='.$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/occurrenceharvester.php?'.$_SERVER['QUERY_STRING']);

$shipmentPK = array_key_exists('shipmentid',$_REQUEST)?$_REQUEST['shipmentpk']:'';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$occurManager = new OccurrenceHarvester();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;

$status = "";
if($isEditor){

}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Occurrence Harvester</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function selectAll(cbObj){
			var boxesChecked = true;
			if(!cbObj.checked) boxesChecked = false;
			var f = cbObj.form;
			for(var i=0;i<f.length;i++){
				if(f.elements[i].name == "scbox[]") f.elements[i].checked = boxesChecked;
			}
		}

		function occurSearchTermChanged(elem){
			if(elem.value != "" || (elem.type == 'checkbox' && elem.checked == true)){
				$("input[name=nullOccurrencesOnly]").prop("checked",false);
			}
		}

		function openPopup(url,windowName){
			newWindow = window.open(url,windowName,'scrollbars=1,toolbar=0,resizable=1,width=1000,height=500,left=20,top=100');
			if (newWindow.opener == null) newWindow.opener = self;
			return false;
		}

		function nullOccurrenceOnlyChanged(cb){
			if(cb.checked == true) $("#extendedVariables").hide();
			else $("#extendedVariables").show();
		}
	</script>
	<style type="text/css">
		fieldset{ padding:15px }
		.fieldGroupDiv{ clear:both; padding:5px 0px; }
		.fieldDiv{ float:left; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../index.php">Home</a> &gt;&gt;
	<a href="index.php">NEON Biorepository Tools</a> &gt;&gt;
	<a href="shipment/manifestsearch.php">Manifest Search</a> &gt;&gt;
	<a href="occurrenceharvester.php"><b>Occurrence Harvester</b></a>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		if($action == 'harvestOccurrences'){
			?>
			<fieldset style="padding:10px">
				<legend><b>Action Panel</b></legend>
				<ul>
				<?php
				$occurManager->batchHarvestOccid($_POST);
				?>
				</ul>
			</fieldset>
			<?php
		}
		?>
		<fieldset>
			<legend><b>Harvesting Report - <?php echo ($shipmentPK?'shipment #'.$shipmentPK:'across all shipments'); ?></b></legend>
			<div style="margin-bottom:25px; margin-left:15px">
				<?php
				$reportArr = $occurManager->getHarvestReport($shipmentPK);
				$occurCnt = (array_key_exists('null',$reportArr)?$reportArr['null']['s-cnt']-$reportArr['null']['o-cnt']:'0');
				echo '<div><b>Occurrences not yet harvested:</b> '.$occurCnt.'</div>';
				unset($reportArr['null']);
				echo '<hr style="margin:10px 0px"/>';
				foreach($reportArr as $msg => $repCntArr){
					$cnt = $repCntArr['s-cnt']-$repCntArr['o-cnt'];
					echo '<div><b>'.$msg.'</b>: ';
					if($cnt) echo $cnt.' failed harvest';
					if($cnt && $repCntArr['o-cnt']) echo '; ';
					if($repCntArr['o-cnt']) echo $repCntArr['o-cnt'].' partial harvest ';
					echo '</div>';
				}
				?>
			</div>
			<div style="margin-bottom:25px; margin-left:15px">
				<a href="shipment/harvesterreports.php?action=shipmentlist">List Errors by Category and Shipments</a>
			</div>
		</fieldset>
		<fieldset>
			<legend><b>Action Panel</b></legend>
			<form action="occurrenceharvester.php" method="post">
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<input name="nullOccurrencesOnly" type="checkbox" value="1" onchange="nullOccurrenceOnlyChanged(this)" checked /> Target New Samples only (NULL occid, no error message)
					</div>
				</div>
				<div id="extendedVariables" style="display:none">
					<div class="fieldGroupDiv">
						<div class="fieldDiv">
							Error Group:
							<select name="errorStr" >
								<option value="nullError">NULL Error Message</option>
								<option value="">---------------------</option>
								<?php
								foreach($reportArr as $msg => $repCntArr){
									echo '<option>'.$msg.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="fieldGroupDiv">
						<div class="fieldDiv">
							WHERE
							<select name="nullfilter" onchange="occurSearchTermChanged(this)">
								<option value="">---------------------</option>
								<option value="sciname">Scientific Name</option>
								<option value="recordedBy">Collector</option>
								<option value="eventDate">Event Date</option>
								<option value="country">Country</option>
								<option value="stateProvince">State/Province</option>
								<option value="county">County</option>
								<option value="decimalLatitude">Lat/Long</option>
							</select>
							IS NULL
						</div>
					</div>
					<div class="fieldGroupDiv">
						<div class="fieldDiv">
							Only update selected fields:
							<select name="targetFields[]" onchange="occurSearchTermChanged(this)" multiple>
								<option value="sciname">Scientific Name</option>
								<option value="recordedBy">Collector data</option>
								<option value="country">Upper geography (e.g. country, state, county)</option>
								<option value="locality">Locality</option>
								<option value="decimalLatitude">Coordinates</option>
								<option value="habitat">Habitat, description, notes, etc</option>
							</select> (multiple selections are allowed)
						</div>
					</div>
					<div class="fieldGroupDiv">
						<div class="fieldDiv" title="Upon reharvesting, replaces existing field values, but only if they haven't been explicitly edited to another value">
							<input name="replaceFieldValues" type="checkbox" value="1"  onchange="occurSearchTermChanged(this)" /> Replace existing field values (excluding fields that have been explicitly modified within portal)
						</div>
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						Limit: <input name="limit" type="text" value="1000" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div style="float:left;margin:20px">
						<button name="action" type="submit" value="harvestOccurrences">Harvest Occurrences</button>
						<!--  <button type="button" value="Reset" onclick="fullResetForm(this.form)">Reset Form</button>  -->
					</div>
					<div style="float:right; margin:20px">
						<button name="action" type="submit" value="exportOccurrences">Export Occurrences</button>
					</div>
				</div>
			</form>
		</fieldset>
		<?php
	}
	else{
		?>
		<div style='font-weight:bold;margin:30px;'>
			You do not have permissions to access occurrence harvester
		</div>
		<?php
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>