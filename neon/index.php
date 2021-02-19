<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/index.php?'.$_SERVER['QUERY_STRING']);

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> NEON Management Tools</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = false;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	</script>
	<style type="text/css">
		.nps-report { margin-left:15px; display:none; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/includes/header.php');
?>
<div class="navpath">
	<a href="../../index.php">Home</a> &gt;&gt;
	<b>NEON Management Tools</b>
</div>
<?php
if($isEditor){
	?>
	<div id="innertext">
		<fieldset style="padding:10px;">
			<legend><b>Shipment Management Tools</b></legend>
			<ul>
				<li>Quick search:
					<form name="sampleQuickSearchFrom" action="shipment/manifestviewer.php" method="post" style="display: inline" >
						<input name="quicksearch" type="text" value="" onchange="this.form.submit()" style="width:250px;" />
					</form>
				</li>
				<li><a href="shipment/manifestloader.php">Load and Process New Manifests</a></li>
				<li><a href="shipment/samplecheckin.php">Sample Check-in Form</a></li>
				<li><a href="shipment/manifestsearch.php">Manifest Listing and Advanced Search</a></li>
				<li><a href="igsnmanager.php">NEON IGSN Contorl Panel</a></li>
				<li><a href="occurrenceharvester.php">Batch Occurrence Harvester</a></li>
				<!--
				<li><a href="#" onclick="$('.nps-report').show();return false">NPS Year End Reports</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=110&year=2019">BLDE - 2019</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=40&year=2019">GRSM - 2019</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=99&year=2019">LECO - 2019</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=131&year=2019">YELL - 2019</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=110&year=2020">BLDE - 2020</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=40&year=2020">GRSM - 2020</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=99&year=2020">LECO - 2020</a></li>
				<li class="nps-report"><a href="npsReportHandler.php?dsid=131&year=2020">YELL - 2020</a></li>
				 -->
				<li><a href="shipment/harvesterreports.php">Occurrence Harvester Error Report</a></li>
			</ul>
		</fieldset>
	</div>
	<?php
}
else{
	?>
	<div style='font-weight:bold;margin:30px;'>
		You do not have permissions to access shipment managment tools
	</div>
	<?php
}
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>