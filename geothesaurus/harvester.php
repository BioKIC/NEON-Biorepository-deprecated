<?php
use ZipStream\Option\Method;

include_once ('../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
header("Content-Type: text/html; charset=".$CHARSET);

$geoThesID = array_key_exists('geoThesID', $_REQUEST) ? $_REQUEST['geoThesID'] : '';
$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

// Sanitation
if(!is_numeric($geoThesID)) $geoThesID = 0;
$submitAction = filter_var($submitAction, FILTER_SANITIZE_STRING);

$geoManager = new GeographicThesaurus();

$isEditor = false;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor && $submitAction) {
	if($submitAction == 'transferDataFromLkupTables'){
		if($geoManager->transferDeprecatedThesaurus()) $statusStr = 'Geographic Lookup data integrated into new Geographic Thesaurus';
		else $statusStr = implode('<br/>',$geoManager->getWarningArr());
	}
}

?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Geographic Thesaurus Havester</title>
	<?php
	$activateJQuery = true;
	include_once ($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">

	</script>
	<style type="text/css">
		fieldset{ margin: 10px; padding: 15px; width: 800px }
		legend{ font-weight: bold; }
		label{ text-decoration: underline; }
		#edit-legend{ display: none }
		.field-div{ margin: 3px 0px }
		.editIcon{  }
		.editTerm{ }
		.editFormElem{ display: none }
		#editButton-div{ display: none }
		#unitDel-div{ display: none }
		.button-div{ margin: 15px }
		.link-div{ margin:0px 30px }
		#status-div{ margin:15px; padding: 15px; color: red; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php">Home</a> &gt;&gt;
		<a href="index.php">Geographic Thesaurus Listing</a> &gt;&gt;
		<b><a href="index.php">Geographic Harvester</a></b> &gt;&gt;
	</div>
	<div id='innertext'>
		<?php
		if($statusStr){
			echo '<div id="status-div">'.$statusStr.'</div>';
		}

		if($statusReport = $geoManager->getThesaurusStatus()){
			$geoRankArr = $geoManager->getGeoRankArr();
			echo '<fieldset>';
			echo '<legend>Active Geographic Thesaurus</legend>';
			if(isset($statusReport['active'])){
				foreach($statusReport['active'] as $geoRank => $cnt){
					echo '<div><b>'.$geoRankArr[$geoRank].':</b> '.$cnt.'</div>';
				}
			}
			else echo '<div>Active thesaurus is empty</div>';
			echo '</fieldset>';
			if(isset($statusReport['lkup'])){
				?>
				<fieldset>
					<legend>Geopraphic Lookup Tables - deprecated</legend>
					<p>There appears to be records within the deprecated Geographic lookup tables that are no longer used.<br/>Do you want to transfer this data into the new geographic thesaurus?</p>
					<?php
					foreach($statusReport['lkup'] as $k => $v){
						echo '<div><b>'.$k.':</b> '.$v.'</div>';
					}
					?>
					<hr/>
					<form name="transThesForm" action="harvester.php" method="post" style="margin-top:15px">
						<button name="submitaction" type="submit" value="transferDataFromLkupTables">Transfer Lookup Tables</button>
					</form>
				</fieldset>
				<?php
			}
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>