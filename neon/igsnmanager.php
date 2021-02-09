<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/IgsnManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/igsnmanager.php?'.$_SERVER['QUERY_STRING']);

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

$igsnManager = new IgsnManager();

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
	<title><?php echo $DEFAULT_TITLE; ?> IGSN Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">

	</script>
	<style type="text/css">
		fieldset{ padding:15px }
		.fieldGroupDiv{ clear:both; margin:10px; }
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
	<a href="igsnmanager.php"><b>IGSN Manager</b></a>
</div>
<div id="innertext">
	<?php
	if($isEditor){
		?>
		<fieldset>
			<legend><b>Collections Needing IGSNs</b></legend>
			<div style="margin-bottom:10px;">IGSN can only be created for NEON samples that have been both Received and Accepted for Analysis</div>
			<div style="">
				<ul>
					<?php
					$taskList = $igsnManager->getIgsnTaskReport();
					if($taskList){
						foreach($taskList as $collid => $collArr){
							echo '<li>'.$collArr['collname'].' ('.$collArr['collcode'].'): ';
							echo '<a href="../collections/admin/igsnmanagement.php?collid='.$collid.'" target="_blank">'.$collArr['cnt'].'</a></li>';
						}
					}
					else{
						echo '<div style="margin:20px"><b>All collections have IGSN assigned</b></div>';
					}
					?>
				</ul>
			</div>
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