<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/OccurrenceHarvester.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestviewer.php?'.$_SERVER['QUERY_STRING']);

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";

$occurManager = new OccurrenceHarvester();

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}

$status = "";
if($isEditor){

}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Occurrence Harvester</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="../../js/jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">

		function selectAll(cbObj){
			var boxesChecked = true;
			if(!cbObj.checked) boxesChecked = false;
			var f = cbObj.form;
			for(var i=0;i<f.length;i++){
				if(f.elements[i].name == "scbox[]") f.elements[i].checked = boxesChecked;
			}
		}

		function openPopup(url,windowName){
			newWindow = window.open(url,windowName,'scrollbars=1,toolbar=0,resizable=1,width=1000,height=500,left=20,top=100');
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
	<a href="occurrenceharvester.php"><b>Occurrence Harvester</b></a>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		if($action == 'harvestAll'){
			?>
			<fieldset style="margin:15px;padding:15px">
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
			<legend><b>Filter Panel</b></legend>
			<form action="occurrenceharvester.php" method="post">
				<div class="fieldGroupDiv">
					<div class="fieldDiv">

					</div>
					<div class="fieldDiv">
						<b>WHERE</b>
						<select name="nullfilter">
							<option value="">Target Field...</option>
							<option value="">---------------------</option>
							<option value="recordedBy">collector</option>
							<option value="eventDate">eventDate</option>
							<option value="country">country</option>
							<option value="stateProvince">stateProvince</option>
							<option value="county">county</option>
							<option value="decimalLatitude">Lat/Long</option>
						</select>
						<b>IS NULL</b>
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<b>Limit:</b> <input name="limit" type="text" value="1000" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div style="float:left;margin:20px">
						<button name="action" type="submit" value="harvestAll">Harvest Occurrence</button>
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
include($SERVER_ROOT.'/footer.php');
?>
</body>
</html>