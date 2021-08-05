<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/IgsnManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/igsnmanager.php?'.$_SERVER['QUERY_STRING']);

$recTarget = array_key_exists('recTarget',$_POST)?$_POST['recTarget']:'';
$startIndex = array_key_exists('startIndex',$_POST)?$_POST['startIndex']:'';
$limit = array_key_exists('limit',$_POST)?$_POST['limit']:'';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

//Sanitation
if(!is_numeric($recTarget)) $recTarget = 0;
$startIndex = filter_var($startIndex, FILTER_SANITIZE_STRING);
if(!is_numeric($limit)) $limit = 1000;

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
		legend{ font-weight:bold; }
		.fieldGroupDiv{ clear:both; margin:10px; }
		.fieldDiv{ float:left; }
		label{ font-weight: bold; }
		button{ width: 250px; }
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
		if($action != 'syncIGSNs'){
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
		if($action == 'syncIGSNs'){
			echo '<fieldset>';
			echo '<legend>Action Panel: IGSN synchronization</legend>';
			echo '<ul>';
			$startIndex = $igsnManager->synchronizeIgsn($recTarget, $startIndex ,$limit);
			echo '<li><a href="igsnmanager.php">Return to IGSN report listing</a></li>';
			echo '</ul>';
			echo '</fieldset>';
		}

		?>
		<fieldset>
			<legend>IGSN Synchronization</legend>
			<div style="margin-bottom:10px;">
				Displays record counts synchronized with the central NEON System.
				After uploading IGSNs into NEON system, run the synchronization tools to adjust the report.
				Will soon add the ability to download a CSV report of unsynchronized ISGNs along with sampleCode, sampleID, and sampleClass that can be used to upload into central NEON system.
			</div>
			<div style="">
				<ul>
					<?php
					$reportArr = $igsnManager->getIgsnSynchronizationReport();
					if($reportArr){
						if(isset($reportArr['x'])) echo '<div><label>Unchecked: </label>'.$reportArr['x'].'</div>';
						if(isset($reportArr[0])) echo '<div><label>Unsynchronized: </label>'.$reportArr[0].'</div>';
						if(isset($reportArr[1])) echo '<div><label>Synchronized: </label>'.$reportArr[1].'</div>';
					}
					?>
				</ul>
				<div style="">
					<form name="igsnsyncform" method="post" action="igsnmanager.php">
						<div style="clear:both;">
							<div style="float:left; margin-left:35px; margin-right:5px"><label>Target:</label> </div>
							<div style="float:left;">
								<input name="recTarget" type="radio" value="0" <?php echo ($recTarget?'':'checked'); ?> /> Unchecked only<br/>
								<input name="recTarget" type="radio" value="1" <?php echo ($recTarget?'checked':''); ?> /> All unlinked records
							</div>
						</div>
						<div style="clear:both;padding-top:10px;margin-left:35px;">
							<label>Start at IGSN:</label> <input name="startIndex" type="text" value="<?php echo $startIndex; ?>" />
						</div>
						<div style="clear:both;padding-top:10px;margin-left:35px;">
							<label>Transaction limit:</label> <input name="limit" type="text" value="<?php echo $limit; ?>" />
						</div>
						<div style="clear:both;padding:20px 35px;">
							<button name="action" type="submit" value="syncIGSNs">Synchronize Records</button>
						</div>
					</form>
				</div>
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