<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');
header('Content-Type: text/html; charset='.$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/reports/labelprofile.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

//Sanitation
if(!is_numeric($collid)) $collid = 0;

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$isEditor = 0;
if($SYMB_UID) $isEditor = 1;
if($collid && array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])) $isEditor = 2;
if($IS_ADMIN) $isEditor = 3;

$statusStr = '';
if($isEditor && $action){
	$applyEdits = true;
	$group = (isset($_POST['group'])?$_POST['group']:'');
	if($group == 'g' && $isEditor < 3) $applyEdits = false;
	if($group == 'c' && $isEditor < 2) $applyEdits = false;
	if($applyEdits){
		if($action == 'saveProfile'){
			if(!$labelManager->saveLabelJson($_POST)){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
		elseif($action == 'deleteProfile'){
			if(!$labelManager->deleteLabelFormat($_POST['group'],$_POST['index'])){
				$statusStr = implode('; ', $labelManager->getErrorArr());
			}
		}
	}
}
$isGeneralObservation = (($labelManager->getMetaDataTerm('colltype') == 'General Observations')?true:false);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE; ?> Specimen Label Manager</title>
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
			function toggleEditDiv(classTag){
				$('#display-'+classTag).toggle();
				$('#edit-'+classTag).toggle();
			}

			function makeJsonEditable(classTag){
				alert("You should now be able to edit the JSON label definition. Feel free to modify, but note that editing the raw JSON requires knowledge of the JSON format. A simple error may cause label generation to completely fail. Within the next couple weeks, there should be a editor interface made available that will assist. Until then, you may need to ask your portal manager for assistance if you run into problems. Thank you for your patience.");
				$('#json-'+classTag).prop('readonly', false);
			}
		</script>
		<style>
			fieldset{ width:700px; padding:15px; }
			fieldset legend{ font-weight:bold; }
			textarea{ width: 800px; height: 150px }
			input[type=text]{ width:400px }
			hr{ margin:15px 0px; }
			.fieldset-block{ width:550px }
			.field-block{ margin:3px 0px }
			.label{ font-weight: bold; }
			.label-inline{ font-weight: bold; }
			.field-value{  }
			.field-inline{  }
			.edit-icon{ width:13px; }
		</style>
	</head>
	<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<?php
		if($isGeneralObservation) echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
		elseif($collid){
			echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
		}
		?>
		<a href="labelmanager.php?collid=<?php echo $collid; ?>&emode=1">Label Manager</a> &gt;&gt;
		<b>Label Profile Editor</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<div style="width:700px"><span style="color:orange;font-weight:bold;">In development!</span> We are currently working on developing a new system that will allow collection managers and general users to create their own custom label formats that can be saved within the collection and user profiles. We are trying our best to develop these tools with minimum disruptions to normal label printing. More details to provided in the near future.</div>
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:15px;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<?php
		}
		echo '<h2>Specimen Label Profiles</h2>';
		$labelFormatArr = $labelManager->getLabelFormatArr();
		foreach($labelFormatArr as $group => $groupArr){
			if($group == 'g' && $isEditor < 3) continue;
			if($group == 'c' && $isEditor < 2) continue;
			$fieldsetTitle = '';
			if($group == 'g') $fieldsetTitle = 'Portal Profiles ';
			elseif($group == 'c') $fieldsetTitle = $labelManager->getCollName().' Label Profiles ';
			elseif($group == 'u') $fieldsetTitle = 'User Profiles ';
			$fieldsetTitle .= '('.count($groupArr).' formats)';
			?>
			<fieldset>
				<legend><?php echo $fieldsetTitle; ?></legend>
				<div style="float:right;" title="Create a new label profile"><img class="edit-icon" src="../../images/add.png" onclick="$('#edit-<?php echo $group; ?>').toggle()" /></div>
				<?php
				$index = '';
				$formatArr = array();
				do{
					$midText = '';
					$labelType = 2;
					$pageSize = '';
					if($formatArr){
						if($index) echo '<hr/>';
						?>
						<div id="display-<?php echo $group.'-'.$index; ?>">
							<div class="field-block">
								<span class="label">Title:</span>
								<span class="field-value"><?php echo htmlspecialchars($formatArr['title']); ?></span>
								<span title="Edit label profile"> <a href="#" onclick="toggleEditDiv('<?php echo $group.'-'.$index; ?>');return false;"><img class="edit-icon" src="../../images/edit.png" /></a></span>
							</div>
							<?php
							if(isset($formatArr['labelHeader']['midText'])) $midText = $formatArr['labelHeader']['midText'];
							$headerStr = $formatArr['labelHeader']['prefix'].' ';
							if($midText==1) $headerStr .= '&gt;Country&lt;';
							elseif($midText==2) $headerStr .= '&gt;State&lt;';
							elseif($midText==3) $headerStr .= '&gt;County&lt;';
							elseif($midText==4) $headerStr .= '&gt;Family&lt;';
							$headerStr .= ' '.$formatArr['labelHeader']['suffix'];
							if(trim($headerStr)){
								?>
								<div class="field-block">
									<span class="label">Header: </span>
									<span class="field-value"><?php echo htmlspecialchars(trim($headerStr)); ?></span>
								</div>
								<?php
							}
							if($formatArr['labelFooter']['textValue']){
								?>
								<div class="field-block">
									<span class="label">Footer: </span>
									<span class="field-value"><?php echo htmlspecialchars($formatArr['labelFooter']['textValue']); ?></span>
								</div>
								<?php
							}
							if($formatArr['labelType']){
								$labelType = $formatArr['labelType'];
								?>
								<div class="field-block">
									<span class="label">Type: </span>
									<span class="field-value"><?php echo $labelType.(is_numeric($labelType)?' column per page':''); ?></span>
								</div>
								<?php
							}
							if($formatArr['pageSize']){
								$pageSize = $formatArr['pageSize'];
								?>
								<div class="field-block">
									<span class="label">Page size: </span>
									<span class="field-value"><?php echo $pageSize; ?></span>
								</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					echo '<div id="edit-'.$group.(is_numeric($index)?'-'.$index:'').'" style="display:none">';
					?>
					<form name="labelprofileeditor-<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>" action="labelprofile.php" method="post" onsubmit="return validateJsonForm(this)">
						<div class="field-block">
							<span class="label">Title:</span>
							<span class="field-elem"><input name="title" type="text" value="<?php echo ($formatArr?htmlspecialchars($formatArr['title']):''); ?>" required /> </span>
							<?php
							if($formatArr) echo '<span title="Edit label profile"> <img class="edit-icon" src="../../images/edit.png" onclick="toggleEditDiv(\''.$group.'-'.$index.'\')" /></span>';
							?>
						</div>
						<fieldset class="fieldset-block">
							<legend>Label Header</legend>
							<div class="field-block">
								<span class="label">Prefix:</span>
								<span class="field-elem">
									<input name="hPrefix" type="text" value="<?php echo (isset($formatArr['labelHeader']['prefix'])?htmlspecialchars($formatArr['labelHeader']['prefix']):''); ?>" />
								</span>
							</div>
							<div class="field-block">
								<div class="field-elem">
									<span class="field-inline">
										<input name="hMidText" type="radio" value="1" <?php echo ($midText==1?'checked':''); ?> />
										<span class="label-inline">Country</span>
									</span>
									<span class="field-inline">
										<input name="hMidText" type="radio" value="2" <?php echo ($midText==2?'checked':''); ?> />
										<span class="label-inline">State</span>
									</span>
									<span class="field-inline">
										<input name="hMidText" type="radio" value="3" <?php echo ($midText==3?'checked':''); ?> />
										<span class="label-inline">County</span>
									</span>
									<span class="field-inline">
										<input name="hMidText" type="radio" value="4" <?php echo ($midText==4?'checked':''); ?> />
										<span class="label-inline">Family</span>
									</span>
									<span class="field-inline">
										<input name="hMidText" type="radio" value="0" <?php echo (!$midText?'checked':''); ?> />
										<span class="label-inline">Blank</span>
									</span>
								</div>
							</div>
							<div class="field-block">
								<span class="label">Suffix:</span>
								<span class="field-elem"><input name="hSuffix" type="text" value="<?php echo ($formatArr?htmlspecialchars($formatArr['labelHeader']['suffix']):''); ?>" /></span>
							</div>
							<div class="field-block">
								<span class="label">Class names:</span>
								<span class="field-elem"><input name="hClassName" type="text" value="<?php echo ($formatArr?htmlspecialchars($formatArr['labelHeader']['className']):''); ?>" /></span>
							</div>
							<div class="field-block">
								<span class="label">Style:</span>
								<span class="field-elem"><input name="hStyle" type="text" value="<?php echo ($formatArr?htmlspecialchars($formatArr['labelHeader']['style']):''); ?>" /></span>';
							</div>
						</fieldset>
						<fieldset  class="fieldset-block">
							<legend>Label Footer</legend>
							<div class="field-block">
								<span class="label-inline">Footer text:</span>
								<input name="fTextValue" type="text" value="<?php echo (isset($formatArr['labelFooter']['textValue'])?htmlspecialchars($formatArr['labelFooter']['textValue']):''); ?>" />
							</div>
							<div class="field-block">
								<span class="label-inline">Class names:</span>
								<input name="fClassName" type="text" value="<?php echo (isset($formatArr['labelFooter']['className'])?$formatArr['labelFooter']['className']:''); ?>" />
							</div>
							<div class="field-block">
								<span class="label-inline">Style:</span>
								<input name="fStyle" type="text" value="<?php echo (isset($formatArr['labelFooter']['style'])?$formatArr['labelFooter']['style']:''); ?>" />
							</div>
						</fieldset>
						<div class="field-block">
							<div class="label">Default Styles:</div>
							<div class="field-block">
								<input name="defaultStyles" type="text" value="<?php echo (isset($formatArr['defaultStyles'])?$formatArr['defaultStyles']:''); ?>" />
							</div>
						</div>
						<div class="field-block">
							<div class="label">Default CSS:</div>
							<div class="field-block">
								<input name="defaultCss" type="text" value="<?php echo (isset($formatArr['defaultCss'])?$formatArr['defaultCss']:'../../css/symb/labelhelpers.css'); ?>" />
							</div>
						</div>
						<div class="field-block">
							<div class="label">Custom CSS:</div>
							<div class="field-block">
								<input name="customCss" type="text" value="<?php echo (isset($formatArr['customCss'])?$formatArr['customCss']:''); ?>" />
							</div>
						</div>
						<div class="field-block">
							<div class="label">Custom JS:</div>
							<div class="field-block">
								<input name="customJS" type="text" value="<?php echo (isset($formatArr['customJS'])?$formatArr['customJS']:''); ?>" />
							</div>
						</div>
						<fieldset class="fieldset-block">
							<legend>Options</legend>
							<div class="field-block">
								<span class="label-inline">Label type:</span>
								<select name="labelType">
									<option value="1" <?php echo ($labelType==1?'selected':''); ?>>1 columns per page</option>
									<option value="2" <?php echo ($labelType==2?'selected':''); ?>>2 columns per page</option>
									<option value="3" <?php echo ($labelType==3?'selected':''); ?>>3 columns per page</option>
									<option value="4" <?php echo ($labelType==4?'selected':''); ?>>4 columns per page</option>
									<option value="5" <?php echo ($labelType==5?'selected':''); ?>>5 columns per page</option>
									<option value="6" <?php echo ($labelType==6?'selected':''); ?>>6 columns per page</option>
									<option value="7" <?php echo ($labelType==7?'selected':''); ?>>7 columns per page</option>
									<option value="packet" <?php echo ($labelType=='packet'?'selected':''); ?>>Packet labels</option>
								</select>
							</div>
							<div class="field-block">
								<span class="label-inline">Page size:</span>
								<select name="pageSize">
									<option value="letter">Letter</option>
									<option value="a4" <?php echo ($pageSize=='a4'?'SELECTED':''); ?>>A4</option>
									<option value="legal" <?php echo ($pageSize=='legal'?'SELECTED':''); ?>>Legal</option>
									<option value="tabloid" <?php echo ($pageSize=='tabloid'?'SELECTED':''); ?>>Ledger/Tabloid</option>
								</select>
							</div>
							<div class="field-block">
								<input name="displaySpeciesAuthor" type="checkbox" value="1" <?php echo (isset($formatArr['displaySpeciesAuthor'])&&$formatArr['displaySpeciesAuthor']?'checked':''); ?> />
								<span class="label-inline">Display species for infraspecific taxa</span>
							</div>
							<div class="field-block">
								<input name="displayBarcode" type="checkbox" value=1" <?php echo (isset($formatArr['displayBarcode'])&&$formatArr['displayBarcode']?'checked':''); ?> />
								<span class="label-inline">Display barcode</span>
							</div>
						</fieldset>
						<div class="field-block">
							<div class="label">JSON: <span title="Edit JSON label definition"><a href="#" onclick="makeJsonEditable('<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>');return false"><img  class="edit-icon" src="../../images/edit.png" /></a></span>
							</div>
							<div class="field-block">
								<textarea id="json-<?php echo $group.(is_numeric($index)?'-'.$index:''); ?>" name="json" readonly><?php echo (isset($formatArr['labelBlocks'])?json_encode($formatArr['labelBlocks'],JSON_PRETTY_PRINT):''); ?></textarea>
							</div>
						</div>
						<div style="margin-left:20px;">
							<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
							<input type="hidden" name="group" value="<?php echo $group; ?>" />
							<input type="hidden" name="index" value="<?php echo $index; ?>" />
							<span><button name="submitaction" type="submit" value="saveProfile"><?php echo (is_numeric($index)?'Save Label Profile':'Create New Label Profile'); ?></button></span>
							<?php
							if(is_numeric($index)){
								?>
								<span style="margin-left:15px"><button name="submitaction" type="submit" value="deleteProfile">Delete Profile</button></span>
								<?php
							}
							?>
						</div>
					</form>
					<?php
					if(!$formatArr) echo '<hr/>';
					echo '</div>';
					if($groupArr){
						$index = key($groupArr);
						if(is_numeric($index)){
							$formatArr = $groupArr[$index];
							next($groupArr);
						}
					}
				} while(is_numeric($index));
				if(!$formatArr) echo '<div>No label profile yet defined. Click green plus sign to right to create a new profile</div>';
				?>
			</fieldset>
			<?php
		}
		if(!$labelFormatArr) echo '<div>You are not authorized to manage any label profiles. Contact portal administrator for more details.</div>';
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
	</body>
</html>