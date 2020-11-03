<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/OccHarvesterReports.php');
header("Content-Type: text/html; charset=".$CHARSET);

$reports = new OccHarvesterReports();

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Occurrence Harvester Reports</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<a href="../index.php">NEON Biorepository Tools</a> &gt;&gt;
			<b>Occurrence Harvester Reports</b>
		</div>
		<div id="innertext">
			<?php
			if($isEditor){
				?>
        
				<?php
			}
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>