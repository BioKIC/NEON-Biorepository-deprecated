<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSkeletal.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/skeletalsubmit.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/skeletalsubmit.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/skeletalsubmit.en.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/editor/skeletalsubmit.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid  = $_REQUEST["collid"];
$action = array_key_exists("formaction",$_REQUEST)?$_REQUEST["formaction"]:"";

$skeletalManager = new OccurrenceSkeletal();
if($collid){
	$skeletalManager->setCollid($collid);
	$collMap = $skeletalManager->getCollectionMap();
}

$statusStr = '';
$isEditor = 0;
if($collid){
	if($IS_ADMIN){
		$isEditor = 1;
	}
	elseif(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
		$isEditor = 1;
	}
	elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor'])){
		$isEditor = 1;
	}
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE.' '.$LANG['OCC_SKEL_SUBMIT']; ?></title>
	<?php
	$activateJQuery = true;
	if(file_exists($SERVER_ROOT.'/includes/head.php')){
		include_once($SERVER_ROOT.'/includes/head.php');
	}
	else{
		echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/symb/collections.editor.skeletal.js?ver=1" type="text/javascript"></script>
	<script src="../../js/symb/shared.js?ver=150324" type="text/javascript"></script>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href="../../index.php"><?php echo $LANG['HOME']; ?></a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1"><?php echo $LANG['COL_MNGMT']; ?></a> &gt;&gt;
		<b><?php echo $LANG['OCC_SKEL_SUBMIT']; ?></b>
	</div>
	<!-- inner text -->
	<div id="innertext">
		<div style="float:right;"><a href="#" onclick="toggle('descriptiondiv')"><b><?php echo $LANG['DISPLAY_INSTRUCTIONS']; ?></b></a></div>
		<h1><?php echo $collMap['collectionname']; ?></h1>
		<?php
		if($statusStr){
			echo '<div style="margin:15px;color:red;">'.$statusStr.'</div>';
		}
		if($isEditor){
			?>
			<fieldset style="padding:0px 15px 15px 15px;position:relative;">
				<legend>
					<b><?php echo $LANG['SKELETAL_DATA']; ?></b>
					<a id="optionimgspan" href="#" onclick="showOptions()"><img src="../../images/list.png" style="width:12px;" title="<?php echo $LANG['DISPLAY_OPTIONS']; ?>" /></a>
					<a href="#" onclick="toggle('descriptiondiv')"><img src="../../images/info.png" style="width:12px;" title="<?php echo $LANG['TOOL_DESCRIPTION']; ?>" /></a>
				</legend>
				<div id="descriptiondiv" style="display:none;margin:10px;width:80%">
					<div style="margin-bottom:5px">
						<?php echo $LANG['SKELETAL_DESCIPRTION_1']; //This page is typically used to enter skeletal records into the system during the imaging process...?>
					</div>
					<div style="margin-bottom:5px">
						<?php echo $LANG['SKELETAL_DESCIPRTION_2']; //More complete data can be entered by clicking on the catalog number...?>
					</div>
					<div>
						<?php echo $LANG['SKELETAL_DESCIPRTION_3']; //Click the Display Option symbol located above scientific name to adjust field display...?>
					</div>
 				</div>
				<form id="defaultform" name="defaultform" action="skeletalsubmit.php" method="post" autocomplete="off" onsubmit="return submitDefaultForm(this)">
					<div id="optiondiv" style="display:none;position:absolute;background-color:white;">
						<fieldset>
							<legend><b><?php echo $LANG['OPTIONS']; ?></b></legend>
							<div style="float:right;"><a href="#" onclick="hideOptions()" style="color:red" ><?php echo $LANG['X_CLOSE']; ?></a></div>
							<div style="font-weight:bold"><?php echo $LANG['FIELD_DISPLAY']; ?>:</div>
							<input type="checkbox" onclick="toggleFieldDiv('othercatalognumbersdiv')" /> <?php echo $LANG['OTHER_CAT_NUMS']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('authordiv')" CHECKED /> <?php echo $LANG['AUTHOR']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('familydiv')" CHECKED /> <?php echo $LANG['FAMILY']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('localitysecuritydiv')" CHECKED /> <?php echo $LANG['LOCALITY_SECURITY']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('countrydiv')" /> <?php echo $LANG['COUNTRY']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('statediv')" CHECKED /> <?php echo $LANG['STATE_PROVINCE']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('countydiv')" CHECKED /> <?php echo $LANG['COUNTY_PARISH']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('recordedbydiv')" /> <?php echo $LANG['COLLECTOR']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('recordnumberdiv')" /> <?php echo $LANG['COLLECTOR_NO']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('eventdatediv')" /> <?php echo $LANG['COLLECTION_DATE']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('labelprojectdiv')" /> <?php echo $LANG['LABEL_PROJECT']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('processingstatusdiv')" /> <?php echo $LANG['PROCESSING_STATUS']; ?><br/>
							<input type="checkbox" onclick="toggleFieldDiv('languagediv')" /> <?php echo $LANG['LANGUAGE']; ?><br/>
							<div style="font-weight:bold"><?php echo $LANG['CATNUM_MATCH']; ?>:</div>
							<input name="addaction" type="radio" value="1" checked /> <?php echo $LANG['RESTRICT_IF_EXISTS']; ?> <br/>
							<input name="addaction" type="radio" value="2" /> <?php echo $LANG['APPEND_VALUES']; ?>
						</fieldset>
					</div>
					<div style="position:absolute;background-color:white;top:10px;right:10px;">
						<?php echo $LANG['SESSION']; ?>: <label id="minutes">00</label>:<label id="seconds">00</label><br/>
						<?php echo $LANG['COUNT']; ?>: <label id="count">0</label><br/>
						<?php echo $LANG['RATE']; ?>: <label id="rate">0</label> <?php echo $LANG['PER_HOUR']; ?>
					</div>
					<div>
						<div style="">
							<div id="scinamediv" style="float:left">
								<b><?php echo $LANG['SCINAME']; ?>:</b>
								<input id="fsciname" name="sciname" type="text" value="" style="width:300px"/>
								<input id="ftidinterpreted" name="tidinterpreted" type="hidden" value="" />
							</div>
							<div id="authordiv" style="float:left">
								<input id="fscientificnameauthorship" name="scientificnameauthorship" type="text" value="" />
							</div>
							<?php
							if($IS_ADMIN || isset($USER_RIGHTS['Taxonomy'])){
								?>
								<div style="float:left;padding:2px 3px;">
									<a href="../../taxa/taxonomy/taxonomyloader.php" target="_blank">
										<img src="../../images/add.png" style="width:14px;" title="<?php echo $LANG['ADD_NAME_THESAURUS']; ?>" />
									</a>
								</div>
								<?php
							}
							?>
							<div style="clear:both;">
								<div id="familydiv" style="float:left">
									<b><?php echo $LANG['FAMILY']; ?>:</b> <input id="ffamily" name="family" type="text" tabindex="0" value="" />
								</div>
								<div id="localitysecuritydiv" style="float:left;margin:3px 5px">
									<input id="flocalitysecurity" name="localitysecurity" type="checkbox" tabindex="0" value="1" />
									<?php echo $LANG['PROTECT_LOCALITY']; ?>
								</div>
							</div>
						</div>
						<div style="clear:both;padding-top:5px">
							<div id="countrydiv" style="display:none;float:left;margin:3px 3px 3px 0px;">
								<b><?php echo $LANG['COUNTRY']; ?>:</b><br/>
								<input id="fcountry" name="country" type="text" value="" autocomplete="off" />
							</div>
							<div id="statediv" style="float:left;margin:3px 3px 3px 0px;">
								<b><?php echo $LANG['STATE_PROVINCE']; ?>:</b><br/>
								<input id="fstateprovince" name="stateprovince" type="text" value="" autocomplete="off" onchange="localitySecurityCheck(this.form)" />
							</div>
							<div id="countydiv" style="float:left;margin:3px 3px 3px 0px;">
								<b><?php echo $LANG['COUNTY_PARISH']; ?>:</b><br/>
								<input id="fcounty" name="county" type="text" autocomplete="off" value="" />
							</div>
						</div>
						<div style="clear:both;padding-top:5px">
							<div id="recordedbydiv" style="display:none;float:left;margin:3px 3px 3px 0px;">
								<b><?php echo $LANG['COLLECTOR']; ?>:</b><br/>
								<input id="frecordedby" name="recordedby" type="text" value="" />
							</div>
							<div id="recordnumberdiv" style="display:none;float:left;margin:3px 3px 3px 0px;">
								<b><?php echo $LANG['COLLECTOR_NO']; ?>:</b><br/>
								<input id="frecordnumber" name="recordnumber" type="text" value="" />
							</div>
							<div id="eventdatediv" style="display:none;float:left;margin:3px 3px 3px 0px;">
								<b><?php echo $LANG['DATE']; ?>:</b><br/>
								<input id="feventdate" name="eventdate" type="text" value="" onchange="eventDateChanged(this)" />
							</div>
							<div id="labelprojectdiv" style="display:none;float:left;margin:5px 3px 3px 0px;">
								<b><?php echo $LANG['LABEL_PROJECT']; ?>:</b><br/>
								<input id="flabelproject" name="labelproject" type="text" value="" />
							</div>
							<div id="processingstatusdiv" style="display:none;float:left;margin:7px 3px 3px 0px">
								<b><?php echo $LANG['PROCESSING_STATUS']; ?>:</b><br/>
								<select id="fprocessingstatus" name="processingstatus" style="margin-top:4px">
									<option value=""></option>
									<option>unprocessed</option>
									<option>stage 1</option>
									<option>stage 2</option>
									<option>stage 3</option>
									<option>expert required</option>
									<option>pending review-nfn</option>
									<option>pending review</option>
									<option>reviewed</option>
									<option>closed</option>
								</select>
							</div>
							<div id="languagediv" style="display:none;float:left;margin:7px 3px 3px 0px;">
								<b><?php echo $LANG['LANGUAGE']; ?>:</b><br/>
								<select id="flanguage" name="language" style="margin-top:4px">
									<option value=""></option>
									<?php
									$langArr = $skeletalManager->getLanguageArr();
									foreach($langArr as $code => $langStr){
										echo '<option value="'.$code.'" '.($code == 'en'?'selected':'').'>'.$langStr.'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div style="clear:both;padding:15px;">
							<div style="float:right;margin:16px 30px 0px 0px;">
								<input name="clearform" type="reset" value="Clear Form" style="margin-right:40px" />
							</div>
							<div style="float:left;">
								<b><?php echo $LANG['CATALOGNUMBER']; ?>:</b><br/>
								<input id="fcatalognumber" name="catalognumber" type="text" style="border-color:green;" />
							</div>
							<div id="othercatalognumbersdiv" style="display:none;float:left;margin:3px;">
								<b><?php echo $LANG['OTHER_CAT_NUMS']; ?>:</b><br/>
								<input id="fothercatalognumbers" name="othercatalognumbers" type="text" value="" />
							</div>
							<div style="float:left;margin:16px 3px 3px 3px;">
								<input id="fcollid" name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<button name="recordsubmit" type="submit" value="Add Record" /><?php echo $LANG['ADD_RECORD']; ?></button>
							</div>
						</div>
					</div>
				</form>
			</fieldset>
			<fieldset style="padding:15px;">
				<legend><b><?php echo $LANG['RECORDS']; ?></b></legend>
				<div id="occurlistdiv"></div>
			</fieldset>
			<?php
		}
		else{
			if($collid){
				echo $LANG['NOT_AUTHORIZED'].'<br/>';
				echo $LANG['CONTACT_ADMIN'].'</b> ';
			}
			else{
				echo $LANG['ERROR_NO_ID'];
			}
		}
		?>
	</div>
<?php
	include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>