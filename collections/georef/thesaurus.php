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
	if($submitAction == 'submitGeoEdits'){
		$status = $geoManager->editGeoUnit($_POST);
		if(!$status) $statusStr = $geoManager->getErrorMessage();
	}
	elseif($submitAction == 'deleteGeoUnits'){
		$status = $geoManager->deleteGeoUnit($_POST['delGeoThesID']);
		if(!$status) $statusStr = $geoManager->getErrorMessage();
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
	<script type="text/javascript">
		function toggleEditor(){
			$(".editTerm").toggle();
			$(".editFormElem").toggle();
			$("#editButton-div").toggle();
			$("#edit-legend").toggle();
			$("#unitDel-div").toggle();
		}
	</script>
	<style type="text/css">
		fieldset{ margin: 10px; padding: 15px; }
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
		.link-div{ margin:20px 30px }
		#status-div{ margin:15px; padding: 15px; color: red; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($profile_indexMenu)?$profile_indexMenu:'true');
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div id='innertext'>
		<?php
		if($statusStr){
			echo '<div id="status-div">'.$statusStr.'</div>';
		}
		if($geoThesID){
			$geoUnit = $geoManager->getGeograpicUnit($geoThesID);
			//Display details for geographic unit with edit and addNew symbols displayed to upper right
			?>
			<div style="float:right">
				<span class="editIcon"><a href="#" onclick="$('#addGeoUnit-div').toggle();"><img class="editimg" src="../../images/add.png" /></a></span>
				<span class="editIcon"><a href="#" onclick="toggleEditor()"><img class="editimg" src="../../images/edit.png" /></a></span>
			</div>
			<div id="addGeoUnit-div" style="display:none">

				<div style="font-weight: bold; margin:20px">Add a new record form to be placed here</div>
				<!-- Add new blank form for adding new record.  -->
				<!-- But we can also do this via a 2-cycle loop using the form below (first loop is an empy form for adding new record, second loop produces form for editing active record. I can show you this. -->

			</div>
			<div id="updateGeoUnit-div" style="clear:both;margin-bottom:10px;">
				<fieldset id="edit-fieldset">
					<legend>Geographic Unit<span id="edit-legend"> Editor</span></legend>
					<form name="unitEditForm" action="thesaurus.php" method="post">
						<div class="field-div">
							<label>GeoUnit Name</label>:
							<span class="editTerm"><?php echo $geoUnit['geoTerm']; ?></span>
							<span class="editFormElem"><input type="text" name="geoTerm" value="<?php echo $geoUnit['geoTerm'] ?>" maxlength="250" style="width:200px;" required /></span>
						</div>
						<div class="field-div">
							<label>ISO2 Code</label>:
							<span class="editTerm"><?php echo $geoUnit['iso2']; ?></span>
							<span class="editFormElem"><input type="text" name="iso2" value="<?php echo $geoUnit['iso2'] ?>" maxlength="250" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>ISO3 Code</label>:
							<span class="editTerm"><?php echo $geoUnit['iso3']; ?></span>
							<span class="editFormElem"><input type="text" name="iso3" value="<?php echo $geoUnit['iso3'] ?>" maxlength="250" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Abbreviation</label>:
							<span class="editTerm"><?php echo $geoUnit['abbreviation']; ?></span>
							<span class="editFormElem"><input type="text" name="abbreviation" value="<?php echo $geoUnit['abbreviation'] ?>" maxlength="250" style="width:50px;" /></span>
						</div>
						<div class="field-div">
							<label>Numeric Code</label>:
							<span class="editTerm"><?php echo $geoUnit['numCode']; ?></span>
							<span class="editFormElem"><input type="text" name="numCode" value="<?php echo $geoUnit['numCode'] ?>" maxlength="250" style="width:50px;" /></span>
						</div>
						
						<!--
						<div class="field-div">
							<label>Category</label>:
							<span class="editTerm"><?php echo $geoUnit['category']; ?></span>
							<span class="editFormElem"><input type="text" name="category" value="<?php echo $geoUnit['category'] ?>" maxlength="250" style="width:150px;" /></span>
						</div>
						-->
						<div class="field-div">
								<label>Category</label>:
								<span class="editTerm"><?php echo $geoUnit['category']; ?></span>
								<span class="editFormElem">
									<select name="category">
										<option value="">Select Category</option>
										<option value="">----------------------</option>
										<?php
										$categoryList = $geoManager->getCategoryArr();
										foreach($categoryList as $id => $term){
											echo '<option value="'.$catgoryList.'"</option>';
										}
										?>
									</select>
								</span>
							</div>
						
						<div class="field-div">
							<label>Notes</label>:
							<span class="editTerm"><?php echo $geoUnit['notes']; ?></span>
							<span class="editFormElem"><input type="text" name="notes" value="<?php echo $geoUnit['notes'] ?>" maxlength="250" style="width:200px;" /></span>
						</div>
						<?php
						if($geoUnit['geoLevel']){
							$parentStr = '';
							if($geoUnit['parentTerm']) $parentStr = '<a href="thesaurus.php?geoThesID='.$geoUnit['parentID'].'">'.$geoUnit['parentTerm'].'</a>';
							?>
							<div class="field-div">
								<label>Parent term</label>:
								<span class="editTerm"><?php echo $parentStr; ?></span>
								<span class="editFormElem">
									<select name="parentID">
										<option value="">Select Parent Term</option>
										<option value="">----------------------</option>
										<option value="">Is a Root Term (e.g. no parent)</option>
										<?php
										$parentList = $geoManager->getGeoTermArr($geoUnit['geoLevel']);
										foreach($parentList as $id => $term){
											echo '<option value="'.$id.'" '.($id==$geoUnit['parentID']?'selected':'').'>'.$term.'</option>';
										}
										?>
									</select>
								</span>
							</div>
							<?php
						}
						?>
						<!-- Add select elements for accepted terms, similar to how parents are handled. -->

						<div id="editButton-div" class="button-div">
							<input name="geoThesID" type="hidden" value="<?php echo $geoThesID; ?>" />
							<button type="submit" name="submitaction" value="submitGeoEdits">Save Edits</button>
						</div>
					</form>
				</fieldset>
			</div>
			<div id="unitDel-div">
				<form name="unitDeleteForm" action="thesaurus.php" method="post">
					<fieldset>
						<legend>Delete Geographic Unit</legend>
						<div class="button-div">
							<input name="parentID" type="hidden" value="<?php echo $geoUnit['parentID']; ?>" />
							<input name="delGeoThesID" type="hidden"  value="<?php echo $geoThesID; ?>" />

							<!-- We need to decide if we want to allow folks to delete a term and all their children, or only can delete if no children or synonym exists. I'm thinking the later. -->

							<button type="submit" name="submitaction" value="deleteGeoUnits" onclick="return confirm('Are you sure you want to delete this record?')" <?php echo ($geoUnit['childCnt']?'disabled':''); ?>>Delete Geographic Unit</button>
						</div>
						<?php
						if($geoUnit['childCnt']) echo '<div>* Record can not be deleted until all child records are deleted</div>';
						?>
					</fieldset>
				</form>
			</div>
			<?php
			echo '<div class="link-div">';
			if(isset($geoUnit['parentID']) && $geoUnit['parentID']) echo '<div><a href="thesaurus.php?parentID='.$geoUnit['parentID'].'">Return to list</a></div>';
			if(isset($geoUnit['childCnt']) && $geoUnit['childCnt']) echo '<div><a href="thesaurus.php?parentID='.$geoThesID.'">Show children taxa</a></div>';
			echo '</div>';
		}
		else{
			if($geoArr){
				$titleStr = '';
				if($parentID){
					$unitArr = $geoManager->getGeograpicUnit($parentID);
					$titleStr = '<b>'.$geoArr[key($geoArr)]['category'].'</b> geographic terms within <b>'.$unitArr['geoTerm'].'</b>';
				}
				else{
					$titleStr = '<b>Root Terms (terms without parents)</b>';
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
			if($geoThesID || $parentID) echo '<div class="link-div"><a href="thesaurus.php">Show base list</a></div>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>