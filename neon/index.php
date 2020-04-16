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
	<script type="text/javascript">
	</script>
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