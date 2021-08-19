<?php
include_once ('../../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
header("Content-Type: text/html; charset=" . $CHARSET);

if(!$SYMB_UID) header('Location: ../profile/index.php?refurl=../collections/georef/thesaurus.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$geoThesID = array_key_exists('geoThesID', $_REQUEST) ? $_REQUEST['geoThesID'] : '';
$parentID = array_key_exists('parentID', $_REQUEST) ? $_REQUEST['parentID'] : '';
$category = array_key_exists('category', $_POST) ? $_POST['category'] : '';
$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

// Sanitation
if(!is_numeric($geoThesID)) $geoThesID = 0;
if(!is_numeric($parentID)) $parentID = 0;
$category = filter_var($category, FILTER_SANITIZE_STRING);
$submitAction = filter_var($submitAction, FILTER_SANITIZE_STRING);

$geoManager = new GeographicThesaurus();

$isEditor = false;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS)) $isEditor = true;

$statusStr = '';
if($isEditor && $submitAction) {
	if($submitAction == 'updateGeoUnit'){
		$statusStr = $geoManager->updateGeoUnit($_POST);
	}
}

$geoArr = $geoManager->getGeograpicList($parentID);

?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> - Geographic Thesaurus Manager</title>
	<?php
	$activateJQuery = true;
	include_once ($SERVER_ROOT . '/includes/head.php');
	?>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div id='innertext'>
		<?php
		if($geoThesID){
			$geoUnit = $geoManager->getGeograpicUnit($geoThesID);
			//Display details for geographic unit with edit and addNew symbols displayed to upper right
			echo '<div style="font-weight:bold;margin-bottom:10px">'.$geoUnit['geoTerm'];
			?>
			<a id="updateGeoUnitToggleDiv" onclick="toggle('updategeounitdiv');">
			<img class="editimg" src="../../images/edit.png" />
			</a>
			</div>
		<?php
			echo '<div style="margin-bottom:10px">Need to display geoUnit details here</div>';
			
			//Provide a form to edit the geo unit that is hidden by default until user clicks edit symbol
		?>
			<!-- How do I make this div toggle??? -->
			<div id="updategeounitdiv">
				<div id="geoUnitNameDiv" style='clear:both;margin-bottom:10px';>
					GeoUnit Name
						<input type="text" id="geounitname" name="geounitname" maxlength="250" style="width:200px;" />
					<br>
					ISO2 Code
						<input type="text" id="iso2code" name="iso2code" maxlength="250" style="width:200px;" />
					<br>
					ISO3 Code
						<input type="text" id="iso3code" name="iso3code" maxlength="250" style="width:200px;" />
					<br>
					Notes
						<input type="text" id="notes" name="notes" maxlength="250" style="width:200px;" />
					<br>
					<button type="submit" name="submitaction" value="Submit Geo Edits">Save Edits</button>
				</div>
				<!-- Add a child term to geo unit-->
				<div id="editchildrendiv">
					Add Child
						<select name="addgeounitchild" onchange="addChildGeoUnit('childgeounit');">
							<option value="">------------</option>
							<?php
								foreach($geoUnit){
								echo '<option value="'.$geoUnit.'">'.$geoTerm.'</option>';
								}
							?>
						</select>
						<button type="submit" name="submitaction" value="Add Child">Submit</button>
					<br>
					Delete Child
						<select name="deletegeounitchild" onchange="deleteChildGeoUnit('childgeounit');">
							<option value="">------------</option>
							<?php
								foreach($geoThesID as $geoThesID => $geoterm){
								echo '<option value="'.$geoThesID.'">'.$geoterm.'</option>';
								}
							?>
						</select>
						<button type="submit" name="submitaction" value="Delete Child">Submit</button>
					<br>
				</div>
				<div id="editparentdiv">
					Change Parent
						<select name="geounitparent" onchange="updateParentGeoUnit('parentgeounit');">
							<option value="">------------</option>
							<?php
								foreach($geoThesID as $geoThesID => $geoterm){
								echo '<option value="'.$geoThesID.'">'.$geoterm.'</option>';
								}
							?>
						</select>
						<button type="submit" name="submitaction" value="Add Parent">Submit</button>
					<br>
				</div>
			</div>
		<?php
			if(isset($geoUnit['parentID']) && $geoUnit['parentID']) echo '<div><a href="thesaurus.php?parentID='.$geoUnit['parentID'].'">Return to list</a></div>';
			if(isset($geoUnit['parentID']) && $geoUnit['parentID']) echo '<div><a href="thesaurus.php?geoThesID='.$geoUnit['parentID'].'">Show parent term</a></div>';
			if(isset($geoUnit['childCnt']) && $geoUnit['childCnt']) echo '<div><a href="thesaurus.php?parentID='.$geoThesID.'">Show children taxa</a></div>';
		}
		else{
			if($geoArr){
				$titleStr = '';
				if($parentID){
					$untiArr = $geoManager->getGeograpicUnit($parentID);
					$titleStr = '<b>'.$geoArr[key($geoArr)]['category'].'</b> geographic terms within <b>'.$untiArr['geoTerm'].'</b>';
				}
				else{
					$titleStr = '<b>Country</b> Terms';
				}
				echo '<div style=";font-size:1.3em;margin: 10px 0px">'.$titleStr.'</div>';
				echo '<ul>';
				foreach($geoArr as $geoID => $unitArr){
					$termDisplay = $unitArr['geoTerm'];
					if(!$unitArr['acceptedTerm']) $termDisplay = '<a href="thesaurus.php?geoThesID='.$geoID.'">'.$termDisplay.'</a>';
					if($unitArr['abbreviation']) $termDisplay .= ' ('.$unitArr['abbreviation'].') ';
					else{
						$codeStr = '';
						if($unitArr['iso2']) $codeStr = $unitArr['iso2'].', ';
						if($unitArr['iso3']) $codeStr .= $unitArr['iso3'].', ';
						if($unitArr['numCode']) $codeStr .= $unitArr['numCode'].', ';
						if($codeStr) $termDisplay .= ' ('.trim($codeStr,', ').') ';
					}
					if($unitArr['acceptedTerm']) $termDisplay .= ' => <a href="thesaurus.php?geoThesID='.$geoID.'">'.$unitArr['acceptedTerm'].'</a>';
					elseif(isset($unitArr['childCnt']) && $unitArr['childCnt']) $termDisplay .= ' - <a href="thesaurus.php?parentID='.$geoID.'">'.$unitArr['childCnt'].' children</a>';
					echo '<li>'.$termDisplay.'</li>';
				}
				echo '</ul>';
			}
			else{
				echo '<div>No records returned</div>';
			}
			if($geoThesID || $parentID) echo '<div><a href="thesaurus.php">Show base list</a></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>