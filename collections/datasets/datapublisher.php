<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverPublisher.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/datasets/datapublisher.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/datasets/datapublisher.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/datasets/datapublisher.en.php');
header('Content-Type: text/html; charset=' .$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$emode = array_key_exists('emode',$_REQUEST)?$_REQUEST['emode']:0;
$action = array_key_exists('formsubmit',$_REQUEST)?$_REQUEST['formsubmit']:'';

if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($emode)) $emode = 0;

$dwcaManager = new DwcArchiverPublisher();
$collManager = new OccurrenceCollectionProfile();
$collManager->setVerboseMode(2);

$publishGBIF = false;
$collArr = array();
if($collid){
	$collManager->setCollid($collid);
	$dwcaManager->setCollArr($collid);
	$collArr = current($collManager->getCollectionMetadata());
	if($collArr['publishtogbif']) $publishGBIF = true;
}

$includeDets = 1;
$includeImgs = 1;
$includeMatSample = 1;
$redactLocalities = 1;
if($action == 'savekey' || (isset($_REQUEST['datasetKey']) && $_REQUEST['datasetKey'])){
	$collManager->setAggKeys($_POST);
	$collManager->updateAggKeys();
}
elseif($action){
	if(!array_key_exists('dets', $_POST)) $includeDets = 0;
	$dwcaManager->setIncludeDets($includeDets);
	if(!array_key_exists('imgs', $_POST)) $includeImgs = 0;
	$dwcaManager->setIncludeImgs($includeImgs);
	if(!array_key_exists('matsample', $_POST)) $includeMatSample = 0;
	$dwcaManager->setIncludeMaterialSample($includeMatSample);
	if (!array_key_exists('redact', $_POST)) $redactLocalities = 0;
	$dwcaManager->setRedactLocalities($redactLocalities);
	$dwcaManager->setTargetPath($SERVER_ROOT.(substr($SERVER_ROOT, -1) == '/' ? '' : '/').'content/dwca/');
}

$idigbioKey = $collManager->getIdigbioKey();

$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))){
	$isEditor = 1;
}

if($isEditor){
	if(array_key_exists('colliddel',$_POST)){
		$dwcaManager->deleteArchive($_POST['colliddel']);
	}
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<meta http-equiv="Cache-control" content="no-cache, no-store, must-revalidate">
	<meta http-equiv="Pragma" content="no-cache">
	<title><?php echo $LANG['DWCA_PUBLISHER']; ?></title>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<style type="text/css">
		.nowrap { white-space: nowrap; }
	</style>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript" src="../../js/symb/collections.gbifpublisher.js?ver=4"></script>
	<script type="text/javascript">
		function toggle(target){
			var objDiv = document.getElementById(target);
			if(objDiv){
				if(objDiv.style.display=="none"){
					objDiv.style.display = "block";
				}
				else{
					objDiv.style.display = "none";
				}
			}
			else{
			  	var divs = document.getElementsByTagName("div");
			  	for (var h = 0; h < divs.length; h++) {
			  	var divObj = divs[h];
					if(divObj.className == target){
						if(divObj.style.display=="none"){
							divObj.style.display="block";
						}
					 	else {
					 		divObj.style.display="none";
					 	}
					}
				}
			}
			return false;
		}

		function verifyDwcaForm(f){

			return true;
		}

		function verifyDwcaAdminForm(f){
			var dbElements = document.getElementsByName("coll[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(dbElement.checked) return true;
			}
		   	alert("<?php echo $LANG['PLS_CHOOSE_COL']; ?>");
			return false;
		}

		function validateGbifForm(f){
			var keyValue = f.organizationKey.value.trim();
			if(keyValue == ""){
				return true;
			}
			else{
				if(keyValue.length != 36){
					alert("<?php echo $LANG['KEY_WRONG']; ?>");
					return false;
				}
				if((keyValue.substring(8,9) != "-") || keyValue.substring(13,14) != "-" || keyValue.substring(18,19) != "-" || keyValue.substring(23,24) != "-"){
					alert("<?php echo $LANG['KEY_NOT_VALID'].' 7a989612-d0ff-407a-8aba-0a6d06f58dca)'; ?>");
					return false;
				}
				$.ajax({
					method: "GET",
					dataType: "json",
					url: "https://api.gbif.org/v1/organization/" + keyValue
				})
				.done(function( retJson ) {
					f.submit();
				})
				.fail(function() {
					alert("<?php echo $LANG['KEY_INVALID_CONTACT']; ?>");
				});
				return false;
			}
			return false;
		}

		function keyChanged(formElem){
			var keyValue = formElem.value;
			if(keyValue.indexOf("/")){
				keyValue = keyValue.substring(keyValue.lastIndexOf("/")+1);
				formElem.value = keyValue;
			}
		}

		function checkAllColl(cb){
			var boxesChecked = true;
			if(!cb.checked){
				boxesChecked = false;
			}
			var cName = cb.className;
			var dbElements = document.getElementsByName("coll[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(dbElement.className == cName){
					if(dbElement.disabled == false) dbElement.checked = boxesChecked;
				}
				else{
					dbElement.checked = false;
				}
			}
		}
	</script>
</head>
<body>
<?php
$displayLeftMenu = (isset($collections_datasets_datapublisherMenu)?$collections_datasets_datapublisherMenu: 'true');
include($SERVER_ROOT.'/includes/header.php');
?>
<div class='navpath'>
	<a href="../../index.php"><?php echo $LANG['HOME']; ?></a> &gt;&gt;
	<?php
	if($collid){
		?>
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1"><?php echo $LANG['COL_MANAGEMENT']; ?></a> &gt;&gt;
		<?php
	}
	else{
		?>
		<a href="../../sitemap.php"><?php echo $LANG['SITEMAP']; ?></a> &gt;&gt;
		<?php
	}
	?>
	<b><?php echo $LANG['DWCA_PUBLISHER']; ?></b>
</div>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if(!$collid && $IS_ADMIN){
		?>
		<div style="float:right;">
			<a href="#" title="<?php echo $LANG['DISPLAY_CONTROL_PANEL']; ?>" onclick="toggle('dwcaadmindiv')">
				<img style="border:0;width:12px;" src="../../images/edit.png" />
			</a>
		</div>
		<?php
	}
	echo '<h1>'.$LANG['DWCA_PUBLISHING'].'</h1>';
	if($collid){
		echo '<div style="font-weight:bold;font-size:120%;">'.$collArr['collectionname'].'</div>';
		?>
		<div style="margin:10px;">
			<?php
			echo $LANG['DWCA_EXPLAIN_1'].' <a href="https://en.wikipedia.org/wiki/Darwin_Core_Archive" target="_blank">'.$LANG['DWCA'].'</a> '.$LANG['DWCA_EXPLAIN_2'].
			' <a href="http://rs.tdwg.org/dwc/terms/" target="_blank">'.$LANG['DWC'].'</a> '.$LANG['DWCA_EXPLAIN_3'].
			' <a href="https://biokic.github.io/symbiota-docs/coll_manager/data_publishing/idigbio/" target="_blank"> '.$LANG['PUBLISH_IDIGBIO'].'</a> &amp;'.
			' <a href=https://biokic.github.io/symbiota-docs/coll_manager/data_publishing/gbif/" target="_blank"> '.$LANG['PUBLISH_GBIF'].'</a>.';
			?>
		</div>
		<?php
	}
	else{
		?>
		<div style="margin:10px;">
			<?php
			echo $LANG['DWCA_DOWNLOAD_EXPLAIN_1'].' <a href="https://en.wikipedia.org/wiki/Darwin_Core_Archive" target="_blank">'.$LANG['DWCA'].'</a> ';
			echo $LANG['DWCA_DOWNLOAD_EXPLAIN_2'].' <a href="http://rs.tdwg.org/dwc/terms/" target="_blank">'.$LANG['DWC'].'</a> '.$LANG['DWCA_DOWNLOAD_EXPLAIN_3'];
			?>
		</div>
		<div style="margin:10px;">
			<?php
			echo '<h3>'.$LANG['DATA_USE_POLICY'].':</h3>';
			echo $LANG['DATA_POLICY_1'].' <a href="../../includes/usagepolicy.php">'.$LANG['DATA_USE_POLICY'].'</a>. '.$LANG['DATA_POLICY_2'];
			?>
		</div>
		<?php
	}
	?>
	<div style="margin:20px;">
		<?php
		echo '<b>'.$LANG['RSS_FEED'].':</b> ';
		$urlPrefix = $dwcaManager->getServerDomain().$CLIENT_ROOT.(substr($CLIENT_ROOT,-1)=='/'?'':'/');
		if(file_exists('../../webservices/dwc/rss.xml')){
			$feedLink = $urlPrefix.'webservices/dwc/rss.xml';
			echo '<a href="'.$feedLink.'" target="_blank">'.$feedLink.'</a>';
		}
		else{
			echo '--'.$LANG['FEED_NOT_PUBLISHED'].'--';
		}
		?>
	</div>
	<?php
	if($collid){
		if($action == 'buildDwca'){
			echo '<ul>';
			$dwcaManager->setVerboseMode(3);
			$dwcaManager->setLimitToGuids(true);
			$dwcaManager->createDwcArchive();
			$dwcaManager->writeRssFile();
			echo '</ul>';
			if($publishGBIF){
				echo '<ul>';
				$collManager->triggerGBIFCrawl($collArr['dwcaurl'], $collid, $collArr['collectionname']);
				echo '</ul>';
			}
		}
		$dwcUri = '';
		$dwcaArr = $dwcaManager->getDwcaItems($collid);
		if($dwcaArr){
			$dArr = current($dwcaArr);
			$dwcUri = ($dArr['collid'] == $collid?$dArr['link']:'');
			if(!$idigbioKey) $idigbioKey = $collManager->findIdigbioKey($collArr['recordid']);
			?>
			<div style="margin:10px;">
				<div>
					<b><?php echo $LANG['TITLE']; ?>:</b> <?php echo $dArr['title']; ?>
					<form action="datapublisher.php" method="post" style="display:inline;" onsubmit="return window.confirm('<?php echo $LANG['SURE_DELETE']; ?>');">
						<input type="hidden" name="colliddel" value="<?php echo $dArr['collid']; ?>">
						<input type="hidden" name="collid" value="<?php echo $dArr['collid']; ?>">
						<input type="image" src="../../images/del.png" name="action" value="DeleteCollid" title="<?php echo $LANG['DELETE_ARCHIVE']; ?>" style="width:15px;">
					</form>
				</div>
				<div><b><?php echo $LANG['DESCRIPTION']; ?>:</b> <?php echo $dArr['description']; ?></div>
				<?php
				$emlLink = $urlPrefix.'collections/datasets/emlhandler.php?collid='.$collid;
				?>
				<div><b>EML:</b> <a href="<?php echo $emlLink; ?>"><?php echo $emlLink; ?></a></div>
				<div><b><?php echo $LANG['DWCA_FILE']; ?>:</b> <a href="<?php echo $dArr['link']; ?>"><?php echo $dArr['link']; ?></a></div>
				<div><b><?php echo $LANG['PUB_DATE']; ?>:</b> <?php echo $dArr['pubDate']; ?></div>
			</div>
			<?php
		}
		else echo '<div style="margin:20px;font-weight:bold;color:orange;">'.$LANG['DWCA_NOT_PUBLISHED'].'</div>';
		?>
		<fieldset style="margin:15px;padding:15px;">
			<legend><b><?php echo $LANG['PUB_INFO']; ?></b></legend>
			<?php
			//Data integrity checks
			$blockSubmitMsg = '';
			$recFlagArr = $dwcaManager->verifyCollRecords($collid);
			if($collArr['guidtarget']){
				echo '<div style="margin:10px;"><b>GUID source:</b> '.$collArr['guidtarget'].'</div>';
				if(isset($recFlagArr['nullGUIDs']) && $recFlagArr['nullGUIDs']){
					echo '<div style="margin:10px;">';
					if($collArr['guidtarget'] == 'occurrenceId'){
						echo '<b>'.$LANG['RECORDS_MISSING'].' <a href="" target="_blank">'.$LANG['OCCID_GUIDS'].'</a>:</b> '.$recFlagArr['nullGUIDs'];
						echo ' <span style="color:red;margin-left:15px;">'.$LANG['RECS_TO_NOT_PUBLISH'].'</span> ';
					}
					elseif($collArr['guidtarget'] == 'catalogNumber'){
						echo '<b>'.$LANG['RECS_WO_CATNUMS'].':</b> '.$recFlagArr['nullGUIDs'];
						echo ' <span style="color:red;margin-left:15px;">'.$LANG['RECS_WILL_NOT_PUBLISH'].'</span> ';
					}
					else{
						echo $LANG['RECS_MISSING_GUIDS'].': '.$recFlagArr['nullGUIDs'].'<br/>';
						echo $LANG['PLEASE_GO_TO'].' <a href="../admin/guidmapper.php?collid='.$collid.'">'.$LANG['COLL_GUID_MAP'].'</a> '.$LANG['TO_ASSIGN_GUIDS'];
					}
					echo '</div>';
				}
				if($collArr['dwcaurl']){
					$serverName = $_SERVER["SERVER_NAME"];
					if(substr($serverName, 0, 4) == 'www.') $serverName = substr($serverName, 4);
					if(!strpos($collArr['dwcaurl'],$serverName)){
						$baseUrl = substr($collArr['dwcaurl'],0,strpos($collArr['dwcaurl'],'/content')).'/collections/datasets/datapublisher.php';
						$blockSubmitMsg = $LANG['ALREADY_PUBLISHED'].' (<a href="'.$baseUrl.'" target="_blank">'.substr($baseUrl,0,strpos($baseUrl,'/',10)).'</a>) ';
					}
				}
			}
			else{
				echo '<div style="margin:10px;font-weight:bold;color:red;">'.$LANG['GUID_NOT_SET'].' <a href="../misc/collmetadata.php?collid='.$collid.'">'.$LANG['EDIT_METADATA'].'</a> '.$LANG['TO_SET_GUID'].'.</div>';
				$blockSubmitMsg = $LANG['CANNOT_PUBLISH'].'<br/>';
			}
			if($recFlagArr['nullBasisRec']){
				echo '<div style="margin:10px;font-weight:bold;color:red;">'.$LANG['THERE_ARE'].' '.$recFlagArr['nullBasisRec'].$LANG['MISSING_BASISOFRECORD'].' '.' <a href="../editor/occurrencetabledisplay.php?q_recordedby=&q_recordnumber=&q_catalognumber&collid='.$collid.'&csmode=0&occid=&occindex=0">'.$LANG['EDIT_EXISTING'].'</a> '.$LANG['TO_CORRECT'].'</div>';
			}
			if($publishGBIF && $dwcUri && isset($GBIF_USERNAME) && isset($GBIF_PASSWORD) && isset($GBIF_ORG_KEY) && $GBIF_ORG_KEY){
				if($collManager->getDatasetKey()){
					$dataUrl = 'http://www.gbif.org/dataset/'.$collManager->getDatasetKey();
					?>
					<div style="margin:10px;">
						<div><b><?php echo $LANG['GBIF_DATASET']; ?>:</b> <a href="<?php echo $dataUrl; ?>" target="_blank"><?php echo $dataUrl; ?></a></div>
					</div>
					<?php
				}
				else{
					?>
					<div style="margin:10px;">
						<?php echo $LANG['YOU_SELECTED_GBIF_1'].
						' <a href="https://www.gbif.org/become-a-publisher" target="_blank">'.$LANG['GBIF_ENDORSE'].
						'</a> '.$LANG['TO'].' '.$LANG['YOU_SELECTED_GBIF_2'];
						?>
						<form style="margin-top:10px;" name="gbifpubform" action="datapublisher.php" method="post" onsubmit="return validateGbifForm(this)" >
							<b><?php echo $LANG['GBIF_KEY']; ?>:</b> <input type="text" id="organizationKey" name=organizationKey value="<?php echo $collManager->getOrganizationKey(); ?>" oninput="$('#validatebtn').removeAttr('disabled')" onchange="keyChanged(this)" style="width:275px;" />
							<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
							<input type="hidden" id="portalname" name="portalname" value="<?php echo $DEFAULT_TITLE; ?>" />
							<input type="hidden" id="collname" name="collname" value="<?php echo $collArr['collectionname']; ?>" />
							<input type="hidden" id="gbifInstOrgKey" name="gbifInstOrgKey" value="<?php echo $GBIF_ORG_KEY; ?>" />
							<input type="hidden" id="installationKey" name="installationKey" value="<?php echo $collManager->getInstallationKey(); ?>" />
							<input type="hidden" id="datasetKey" name="datasetKey" value="" />
							<input type="hidden" id="endpointKey" name="endpointKey" value="" />
							<input type="hidden" id="dwcUri" name="dwcUri" value="<?php echo $dwcUri; ?>" />
							<input type="hidden" name="formsubmit" value="savekey" />
							<button type="submit" id="validatebtn" name="validate" disabled><?php echo $LANG['VALIDATE_KEY']; ?></button>
							<?php
							if($collManager->getOrganizationKey()){
								?>
								<div style="margin:10px 0px;clear:both;">
									<?php
									$collPath = "http://";
									if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $collPath = "https://";
									$collPath .= $_SERVER["SERVER_NAME"];
									if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER['SERVER_PORT'] != 443) $collPath .= ':'.$_SERVER["SERVER_PORT"];
									$collPath .= $CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.$collid;
									$bodyStr = 'Please provide the following GBIF user permission to create and update datasets for the following GBIF publisher.<br/>'.
										'Once these permissions are assigned, we will be pushing a DwC-Archive from the following Symbiota collection to GBIF.<br/><br/>'.
										'GBIF user: '.$GBIF_USERNAME.'<br/>'.
										'GBIF publisher identifier: '.$collManager->getOrganizationKey().'<br/>'.
										'GBIF publisher: https://www.gbif.org/publisher/'.$collManager->getOrganizationKey().'<br/>'.
										'Symbiota collection: '.$collPath.'<br/><br/>'.
										'Sincerely, <br/><br/><br/><br/><br/><br/>';
									echo $LANG['BEFORE_SUBMITTING'];
									echo ' (<a href="mailto:helpdesk@gbif.org?subject=Publishing%20data%20from%20Symbiota%20portal%20to%20GBIF...&body='.rawurlencode(str_replace('<br/>', "\n", $bodyStr)).'">helpdesk@gbif.org</a>) ';
									echo $LANG['WITH_REQUEST_1'].' &quot;<b>'.$GBIF_USERNAME.'</b>&quot; '.$LANG['WITH_REQUEST_2'];
									echo ' <a href="#" onclick="toggle(\'emailMsg\');return false;" style="color:blue">'.$LANG['HERE'].'</a> '.$LANG['WITH_REQUEST_3'];
									?>
									<fieldset id="emailMsg" style="display:none;padding:15px;margin:15px"><legend><?php echo $LANG['EMAIL_DRAFT']; ?></legend><?php echo trim($bodyStr,' <br/>'); ?></fieldset>
									<br/><br/>
									<button type="button" onclick="processGbifOrgKey(this.form);"><?php echo $LANG['SUBMIT_DATA']; ?></button>
									<img id="workingcircle" src="../../images/ajax-loader_sm.gif" style="margin-bottom:-4px;width:20px;display:none;" />
								</div>
								<?php
							}
							?>
						</form>
					</div>
					<?php
				}
			}
			if($idigbioKey && $dwcUri){
				$dataUrl = 'https://www.idigbio.org/portal/recordsets/'.$idigbioKey;
				?>
				<div style="margin:10px;">
					<div><b><?php echo $LANG['IDIGBIO_DATASET']; ?>:</b> <a href="<?php echo $dataUrl; ?>" target="_blank"><?php echo $dataUrl; ?></a></div>
				</div>
				<?php
			}
			?>
		</fieldset>
		<fieldset style="padding:15px;margin:15px;">
			<legend><b><?php echo $LANG['PUBLISH_REFRESH']; ?></b></legend>
			<form name="dwcaform" action="datapublisher.php" method="post" onsubmit="return verifyDwcaForm(this)">
				<div>
					<input type="checkbox" name="dets" value="1" <?php echo ($includeDets?'CHECKED':''); ?> /> <?php echo $LANG['INCLUDE_DETS']; ?><br/>
					<input type="checkbox" name="imgs" value="1" <?php echo ($includeImgs?'CHECKED':''); ?> /> <?php echo $LANG['INCLUDE_IMGS']; ?><br/>
					<?php
					if($collManager->materialSampleIsActive()) echo '<input type="checkbox" name="matsample" value="1" '.($includeMatSample?'CHECKED':'').' /> '.$LANG['INCLUDE_MATSAMPLE'].'<br/>';
					?>
					<input type="checkbox" name="redact" value="1" <?php echo ($redactLocalities?'CHECKED':''); ?> /> <?php echo $LANG['REDACT_LOC']; ?><br/>
				</div>
				<div style="clear:both;margin:10px;">
					<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
					<?php
					echo '<button type="submit" name="formsubmit" value="buildDwca" '.($blockSubmitMsg?'disabled':'').'>'.$LANG['CREATE_REFRESH'].'</button>';
					if($blockSubmitMsg) echo '<span style="color:red;margin-left:10px;">'.$blockSubmitMsg.'</span>';
					?>
				</div>
				<?php
				if($collArr['managementtype'] != 'Live Data' || $collArr['guidtarget'] != 'symbiotaUUID'){
					?>
					<div style="margin:10px;font-weight:bold">
						<?php echo $LANG['NOTE_LACKING_EXCLUDED']; ?>
					</div>
					<?php
				}
				?>
			</form>
		</fieldset>
		<?php
	}
	else{
		$catID = (isset($DEFAULTCATID)?$DEFAULTCATID:0);
		if($IS_ADMIN){
			if($action == 'buildDwca'){
				echo '<ul>';
				$dwcaManager->setVerboseMode(2);
				$dwcaManager->setLimitToGuids(true);
				$dwcaManager->batchCreateDwca($_POST['coll']);
				echo '</ul>';
				echo '<ul>';
				$collManager->batchTriggerGBIFCrawl($_POST['coll']);
				echo '</ul>';
			}
			?>
			<div id="dwcaadmindiv" style="margin:10px;display:<?php echo ($emode?'block':'none'); ?>;" >
				<form name="dwcaadminform" action="datapublisher.php" method="post" onsubmit="return verifyDwcaAdminForm(this)">
					<fieldset style="padding:15px;">
						<legend><b><?php echo $LANG['PUBLISH_REFRESH']; ?></b></legend>
						<div style="margin:10px;">
							<input name="collcheckall" type="checkbox" value="" onclick="checkAllColl(this)" /> <?php echo $LANG['SEL_DESEL_ALL']; ?><br/><br/>
							<?php
							$collList = $dwcaManager->getCollectionList($catID);
							foreach($collList as $k => $v){
								$errMsg = '';
								if(isset($v['err']) && $v['err']){
									$errMsg = $LANG[$v['err']];
									if($v['err'] == 'ALREADY_PUB_DOMAIN') $errMsg .= ' (<a href="'.$v['url'].'" target="_blank">'.substr($v['url'],0,strpos($v['url'],'/',10)).'</a>)';
								}
								$inputAttr = '';
								if($errMsg) $inputAttr = 'DISABLED';
								elseif($v['url']) $inputAttr = 'CHECKED';
								echo '<input name="coll[]" type="checkbox" value="'.$k.'" '.$inputAttr.' />';
								echo '<a href="../misc/collprofiles.php?collid='.$k.'" target="_blank">'.$v['name'].'</a>';
								if($errMsg) echo '<span style="color:red;margin-left:15px;">'.$errMsg.'</span>';
								elseif($v['url']) echo '<span> - published</span>';
								echo '<br/>';
							}
							?>
						</div>
						<fieldset style="margin:10px;padding:15px;">
							<legend><b><?php echo $LANG['OPTIONS']; ?></b></legend>
							<input type="checkbox" name="dets" value="1" <?php echo ($includeDets?'CHECKED':''); ?> /> <?php echo $LANG['INCLUDE_DETS']; ?><br/>
							<input type="checkbox" name="imgs" value="1" <?php echo ($includeImgs?'CHECKED':''); ?> /> <?php echo $LANG['INCLUDE_IMGS']; ?><br/>
							<?php
							if($dwcaManager->materialSampleIsActive()) echo '<input type="checkbox" name="matsample" value="1" '.($includeMatSample?'CHECKED':'').' /> '.$LANG['INCLUDE_MATSAMPLE'].'<br/>';
							?>
							<input type="checkbox" name="redact" value="1" <?php echo ($redactLocalities?'CHECKED':''); ?> /> <?php echo $LANG['REDACT_LOC']; ?><br/>
						</fieldset>
						<div style="clear:both;margin:20px;">
							<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
							<button type="submit" name="formsubmit" value="buildDwca" ><?php echo $LANG['CREATE_REFRESH']; ?></button>
						</div>
					</fieldset>
				</form>
			</div>
			<?php
		}
		if($dwcaArr = $dwcaManager->getDwcaItems()){
			?>
			<table class="styledtable" style="font-family:Arial;font-size:12px;margin:10px;">
				<tr><th><?php echo $LANG['CODE']; ?></th><th><?php echo $LANG['COL_NAME']; ?></th><th><?php echo $LANG['DWCA']; ?></th><th><?php echo $LANG['METADATA']; ?></th><th><?php echo $LANG['PUB_DATE']; ?></th></tr>
				<?php
				foreach($dwcaArr as $k => $v){
					?>
					<tr>
						<td><?php echo '<a href="../misc/collprofiles.php?collid='.$v['collid'].'">'.str_replace(' DwC-Archive','',$v['title']).'</a>'; ?></td>
						<td><?php echo substr($v['description'],24); ?></td>
						<td class="nowrap">
							<?php
							echo '<a href="'.$v['link'].'">DwC-A ('.$dwcaManager->humanFileSize($v['link']).')</a>';
							if($IS_ADMIN){
								?>
								<form action="datapublisher.php" method="post" style="display:inline;" onsubmit="return window.confirm('<?php echo $LANG['SURE_DELETE']; ?>');">
									<input type="hidden" name="colliddel" value="<?php echo $v['collid']; ?>">
									<input type="image" src="../../images/del.png" name="action" value="DeleteCollid" title="<?php echo $LANG['DELETE_ARCHIVE']; ?>" style="width:15px;" />
								</form>
								<?php
							}
							?>
						</td>
						<td>
							<?php
							echo '<a href="'.$urlPrefix.'collections/datasets/emlhandler.php?collid='.$v['collid'].'">EML</a>';
							?>
						</td>
						<td class="nowrap"><?php echo date('Y-m-d', strtotime($v['pubDate'])); ?></td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		else{
			echo '<div style="margin:10px;font-weight:bold;">'.$LANG['NO_PUBLISHABLE'].'</div>';
		}
		if($catID){
			if($addDwca = $dwcaManager->getAdditionalDWCA($catID)){
				echo '<div style="font-weight:bold;font-size:140%;margin:50px 0px 15px 0px;">'.$LANG['ADDIT_SOURCES'].'</div>';
				echo '<ul>';
				foreach($addDwca as $domanName => $domainArr){
					echo '<li><a href="'.$domainArr['url'].'" target="_blank">'.$domanName.'</a> - '.$domainArr['cnt'].' Archives</li>';
				}
				echo '</ul>';
			}
		}
	}
	?>
</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>
