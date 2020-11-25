<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');
header('Content-Type: text/html; charset='.$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/reports/labeljsoneditor.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}
$statusStr = '';
if($isEditor){
	if($action == 'saveJsonStr'){
		if(!$labelManager->saveLabelJson($_POST)){
			$statusStr = implode('; ', $labelManager->getErrorArr());
		}
	}
	elseif($action == 'deleteJsonStr'){
		if(!$labelManager->deleteLabelFormat($_POST['group'],$_POST['index'])){
			$statusStr = implode('; ', $labelManager->getErrorArr());
		}
	}
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
		<title><?php echo $DEFAULT_TITLE; ?> Specimen Label Manager</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script type="text/javascript">
			function toggle(target){
				var ele = document.getElementById(target);
				if(ele){
					if(ele.style.display=="none") ele.style.display="";
				 	else ele.style.display="none";
				}
			}
		</script>
		<style>
			fieldset{ padding:15px; }
			fieldset legend{ font-weight:bold; }
			textarea{ width: 800px; height: 150px }
			input[type=text]{ width:400px }
			.field-block{ clear:both; margin:3px 0px }
			.fieldset-block{ width:550px }
			.label{ font-weight: bold; display:block }
			.label-inline{ font-weight: bold; }
			.field-inline{  }
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
		if(stripos(strtolower($labelManager->getMetaDataTerm('colltype')), "observation") !== false){
			echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
		}
		else{
			echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
		}
		?>
		<b>Label JSON Editor</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($statusStr){
			?>
			<hr/>
			<div style="margin:15px;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<?php
		}
		echo '<h2>'.$labelManager->getCollName().'</h2>';
		$labelFormatArr = $labelManager->getLabelFormatArr();
		foreach($labelFormatArr as $group => $groupArr){
			echo '<fieldset>';
			echo '<legend>';
			if($group == 'g') echo 'Portal Profiles ';
			elseif($group == 'c') echo 'Collection Profiles ';
			elseif($group == 'u') echo 'User Profiles ';
			echo '('.count($groupArr).' formats)';
			echo '</legend>';
			echo '<div style="float:right;margin-top:-5px"><img src="../../images/add.png" onclick="toggle(\'edit-div-'.$group.'-new\');" /></div>';
			$index = '';
			$formatArr = array();
			do{
				?>
				<div id="edit-div-<?php echo $group.'-'.(is_numeric($index)?$index:'new'); ?>" style="display:<?php echo (is_numeric($index)?'block':'none'); ?>">
					<form name="labeljsoneditor-<?php echo $group.'-'.(is_numeric($index)?$index:'new'); ?>" action="labeljsoneditor.php" method="post" onsubmit="return validateJsonForm(this)">
						<fieldset>
							<legend><?php echo (is_numeric($index)?$formatArr['title']:'New Label Format'); ?></legend>
							<div class="field-block">
								<div class="label">Title:</div>
								<div class="field-block">
									<input name="title" type="text" value="<?php echo (isset($formatArr['title'])?$formatArr['title']:''); ?>" />
								</div>
							</div>
							<fieldset class="fieldset-block">
								<legend>Label Header</legend>
								<div class="field-block">
									<span class="label-inline">Prefix:</span>
									<input name="hPrefix" type="text" value="<?php echo (isset($formatArr['labelHeader']['prefix'])?$formatArr['labelHeader']['prefix']:''); ?>" />
								</div>
								<div class="field-block">
									<?php
									$midText = '';
									if(isset($formatArr['labelHeader']['midText'])) $midText = $formatArr['labelHeader']['midText'];
									?>
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
								<div class="field-block">
									<span class="label-inline">Suffix:</span>
									<input name="hSuffix" type="text" value="<?php echo (isset($formatArr['labelHeader']['suffix'])?$formatArr['labelHeader']['suffix']:''); ?>" />
								</div>
								<div class="field-block">
									<span class="label-inline">Class names:</span>
									<input name="hClassName" type="text" value="<?php echo (isset($formatArr['labelHeader']['className'])?$formatArr['labelHeader']['className']:''); ?>" />
								</div>
								<div class="field-block">
									<span class="label-inline">Style:</span>
									<input name="hStyle" type="text" value="<?php echo (isset($formatArr['labelHeader']['style'])?$formatArr['labelHeader']['style']:''); ?>" />
								</div>
							</fieldset>
							<fieldset  class="fieldset-block">
								<legend>Label Footer</legend>
								<div class="field-block">
									<span class="label-inline">Footer text:</span>
									<input name="fTextValue" type="text" value="<?php echo (isset($formatArr['labelFooter']['textValue'])?$formatArr['labelFooter']['textValue']:''); ?>" />
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
									<input name="defaultCss" type="text" value="<?php echo (isset($formatArr['defaultCss'])?$formatArr['defaultCss']:''); ?>" />
								</div>
							</div>
							<div class="field-block">
								<div class="label">Custom CSS:</div>
								<div class="field-block">
									<input name="customCss" type="text" value="<?php echo (isset($formatArr['customCss'])?$formatArr['customCss']:''); ?>" />
								</div>
							</div>
							<fieldset class="fieldset-block">
								<legend>Options</legend>
								<div class="field-block">
									<?php
									$labelType = 2;
									if(isset($formatArr['labelType']) && $formatArr['labelType']) $formatArr['labelType'];
									?>
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
									<?php
									$pageSize = '';
									if(isset($formatArr['pageSize'])) $pageSize = $formatArr['pageSize'];
									?>
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
								<div class="label">JSON:</div>
								<div class="field-block">
									<textarea name="json"><?php echo (isset($formatArr['labelBlocks'])?json_encode($formatArr['labelBlocks'],JSON_PRETTY_PRINT):''); ?></textarea>
								</div>
							</div>
							<div style="margin-left:20px;">
								<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
								<input type="hidden" name="group" value="<?php echo $group; ?>" />
								<input type="hidden" name="index" value="<?php echo $index; ?>" />
								<span><button name="submitaction" type="submit" value="saveJsonStr"><?php echo (is_numeric($index)?'Save Label Format':'Create New Label Format'); ?></button></span>
								<?php
								if(is_numeric($index)){
									?>
									<span style="margin-left:15px"><button name="submitaction" type="submit" value="deleteJsonStr">Delete Format</button></span>
									<?php
								}
								?>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
				$index = key($groupArr);
				if(is_numeric($index)){
					$formatArr = $groupArr[$index];
					next($groupArr);
				}
			} while(is_numeric($index));
			echo '</fieldset>';
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
	</body>
</html>