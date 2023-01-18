<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCleaner.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:'';
$queryCountry = array_key_exists('q_country',$_REQUEST)?$_REQUEST['q_country']:'';
$ranking = array_key_exists('ranking',$_REQUEST)?$_REQUEST['ranking']:'';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/cleaning/coordinatevalidator.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

//Sanitation
if($action && !preg_match('/^[a-zA-Z\s]+$/',$action)) $action = '';

$collidStr = '';
if(is_array($collid)) $collidStr = implode(',', $collid);
else $collidStr = $collid;

$cleanManager = new OccurrenceCleaner();
if($collidStr) $cleanManager->setCollId($collidStr);
$collMap = $cleanManager->getCollMap();

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Coordinate Validator</title>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
	<link href="../../js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script>
		function selectAllCollections(cb,classNameStr){
			boxesChecked = true;
			if(!cb.checked){
				boxesChecked = false;
			}
			var dbElements = document.getElementsByName("collid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(classNameStr == '' || dbElement.className.indexOf(classNameStr) > -1){
					dbElement.checked = boxesChecked;
				}
			}
		}

		function checkSelectCollidForm(f){
			var dbElements = document.getElementsByName("collid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(dbElement.checked) return true;
			}
		   	alert("Please select at least one collection!");
	      	return false;
		}
	</script>
	<style type="text/css">
		table.styledtable {  width: 300px }
		table.styledtable td { white-space: nowrap; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../../sitemap.php">Sitemap</a> &gt;&gt;
		<b><a href="coordinatevalidator.php?collid='.$collid.'">Coordinate Validator</a></b>
	</div>
	<!-- inner text -->
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:20px;color:<?php echo (substr($statusStr,0,5)=='ERROR'?'red':'green');?>">
				<?php echo $statusStr; ?>
			</div>
			<hr/>
			<?php
		}
		if($isEditor){
			if($collidStr){
				?>
				<div style="margin:15px">
					Click "Validate Coordinates" button to loop through all unvalidated georeferenced specimens and verify that the coordinates actually fall within the defined political units.
					Click on the list symbol to display specimens of that ranking.
					If there was a mismatch between coordinates and county, this could be due to 1) cordinates fall outside of county limits, 2) wrong county was entered, or 3) county is misspelled.
				</div>
				<div style="margin:15px">
					<?php
					if($action){
						echo '<fieldset style="padding:20px">';
						if($action == 'Validate Coordinates'){
							echo '<legend><b>Validating Coordinates</b></legend>';
							$cleanManager->verifyCoordAgainstPolitical($queryCountry);
						}
						elseif($action == 'displayranklist'){
							echo '<legend><b>Specimen with rank of '.$ranking.'</b></legend>';
							$occurList = array();
							if($action == 'displayranklist'){
								$occurList = $cleanManager->getOccurrenceRankingArr('coordinate', $ranking);
							}
							if($occurList){
								foreach($occurList as $occid => $inArr){
									echo '<div>';
									echo '<a href="../editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">'.$occid.'</a>';
									echo ' - checked by '.$inArr['username'].' on '.$inArr['ts'];
									echo '</div>';
								}
							}
							else{
								echo '<div style="margin:30xp;font-weight:bold;font-size:150%">Nothing to be displayed</div>';
							}
						}
						echo '</fieldset>';
					}
					?>
				</div>
				<div style="margin:10px">
					<div style="font-weight:bold">Ranking Statistics</div>
					<?php
					$coordRankingArr = $cleanManager->getRankingStats('coordinate');
					$rankArr = current($coordRankingArr);
					echo '<table class="styledtable">';
					echo '<tr><th>Ranking</th><th>Protocol</th><th>Count</th></tr>';
					$protocolMap = array('GoogleApiMatch:countryEqual'=>'Questionable State','GoogleApiMatch:stateEqual'=>'Questionable County','GoogleApiMatch:countyEqual'=>'Country, State, and County verified ');
					foreach($rankArr as $rank => $protocolArr){
						foreach($protocolArr as $protocolStr => $cnt){
							if(array_key_exists($protocolStr, $protocolMap)) $protocolStr = $protocolMap[$protocolStr];
							echo '<tr>';
							echo '<td>'.$rank.'</td>';
							echo '<td>'.$protocolStr.'</td>';
							echo '<td>'.number_format($cnt);
							//if(is_numeric($cnt)) echo ' <a href="coordinatevalidator.php?ranking='.$rank.'&action=displayranklist" title="List specimens"><img src="'.$CLIENT_ROOT.'/images/list.png" style="width:12px" /></a>';
							echo '</td>';
							echo '</tr>';
						}
					}
					echo '</table>';
					?>
				</div>
				<div style="margin:10px">
					<div style="font-weight:bold">Non-verified listed by Country</div>
					<?php
					$countryArr = $cleanManager->getUnverifiedByCountry();
					arsort($countryArr);
					echo '<table class="styledtable">';
					echo '<tr><th>Country</th><th>Count</th><th>Action</th></tr>';
					foreach($countryArr as $country => $cnt){
						echo '<tr>';
						echo '<td>';
						echo $country;
						echo ' <a href="../list.php?db=all&country='.$country.'" target="_blank"><img src="../../images/list.png" style="width:12px" /></a>';
						echo '</td>';
						echo '<td>'.number_format($cnt).'</td>';
						echo '<td>';
						?>
						<form action="coordinatevalidator.php" method="post" style="margin:10px">
							<input name="q_country" type="hidden" value="<?php echo $country; ?>" />
							<input name="collid" type="hidden" value="<?php echo $collidStr; ?>" />
							<input name="action" type="submit" value="Validate Coordinates" />
						</form>
						<?php
						echo '</td>';
						echo '</tr>';
					}
					echo '</table>';
					?>
				</div>
	 			<?php
			}
			else{
				?>
				<fieldset style="padding: 15px;margin:20px;">
					<legend><b>Collection Selector</b></legend>
					<form name="selectcollidform" action="coordinatevalidator.php" method="post" onsubmit="return checkSelectCollidForm(this)">
						<div>
							<input type="checkbox" onclick="selectAllCollections(this,'');" /> Select / Unselect All<br/>
							<input type="checkbox" onclick="selectAllCollections(this,'specimen');" /> Select / Unselect All Specimens<br/>
							<input type="checkbox" onclick="selectAllCollections(this,'observation');" /> Select / Unselect All Observations<br/>
							<input type="checkbox" onclick="selectAllCollections(this,'live');" /> Select / Unselect All Live Management<br/>
							<input type="checkbox" onclick="selectAllCollections(this,'snapshot');" /> Select / Unselect All Snapshot Management
						</div>
						<hr/>
						<?php
						foreach($collMap as $id => $collArr){
							echo '<div>';
							$classStr = '';
							if($collArr['colltype'] == 'Preserved Specimens') $classStr = 'specimen';
							else $classStr = 'observation';
							if($collArr['managementtype'] == 'Live Data') $classStr .= ' live';
							elseif($collArr['managementtype'] == 'Snapshot') $classStr .= ' snapshot';
							elseif($collArr['managementtype'] == 'Aggregate') $classStr .= ' aggregate';
							echo '<input name="collid[]" class="'.$classStr.'" type="checkbox" value="'.$id.'" /> ';
							echo $collArr['collectionname'].' ('.$collArr['code'].') - '.$collArr['colltype'].':'.$collArr['managementtype'];
							echo '</div>';
						}
						?>
						<div style="margin: 15px">
							<button name="submitaction" type="submit" value="EvaluateCollections">Evaluate Collections</button>
						</div>
					</form>
				</fieldset>
				<?php
			}
		}
		else{
			echo '<h2>You are not authorized to access this page</h2>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>