<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/misc/collprofiles.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = ((array_key_exists("collid",$_REQUEST) && is_numeric($_REQUEST["collid"]))?$_REQUEST["collid"]:0);
$action = array_key_exists("action",$_REQUEST)?htmlspecialchars($_REQUEST["action"]):"";
$eMode = array_key_exists('emode',$_REQUEST)?htmlspecialchars($_REQUEST['emode']):0;

if($eMode && !$SYMB_UID){
	header('Location: ../../profile/index.php?refurl=../collections/misc/collprofiles.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));
}

$collManager = new OccurrenceCollectionProfile();
if(!$collManager->setCollid($collid)) $collid = '';

$collData = $collManager->getCollectionMetadata();

$editCode = 0;		//0 = no permissions; 1 = CollEditor; 2 = CollAdmin; 3 = SuperAdmin
if($SYMB_UID){
	if($IS_ADMIN){
		$editCode = 3;
	}
	else if($collid){
		if(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])){
			$editCode = 2;
		}
		elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
			$editCode = 1;
		}
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE." ".($collid?$collData[$collid]["collectionname"]:"") ; ?> Collection Profiles</title>
	<meta name="keywords" content="Natural history collections,<?php echo ($collid?$collData[$collid]["collectionname"]:""); ?>" />
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
	<script src="../../js/jquery.js?ver=20130917" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js?ver=20130917" type="text/javascript"></script>
	<script>
		function toggleById(target){
			if(target != null){
				var obj = document.getElementById(target);
				if(obj.style.display=="none" || obj.style.display==""){
					obj.style.display="block";
				}
				else {
					obj.style.display="none";
				}
			}
			return false;
		}
	</script>
	<style type="text/css">
		.field-div { margin:10px 0px; clear:both }
		.label { font-weight:bold; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($collections_misc_collprofilesMenu)?$collections_misc_collprofilesMenu:true);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../index.php"><?php echo $LANG['COLLECTION_SEARCH']; ?></a> &gt;&gt;
		<b><?php echo ($collid?$collData[$collid]["collectionname"]:"Collection Profiles"); ?> Details</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($editCode > 1){
			if($action == 'UpdateStatistics'){
				echo '<h2> '.$LANG['UPDATE_STATISTICS'].'</h2>';
				$collManager->updateStatistics(true);
				echo '<hr/>';
			}
		}
		if($editCode && $collid){
			?>
			<div style="float:right;margin:3px;cursor:pointer;" onclick="toggleById('controlpanel');" title="Toggle Manager's Control Panel">
				<img style='border:0px;' src='../../images/edit.png' />
			</div>
			<?php
		}
		if($collid){
			$collData = $collData[$collid];
			$codeStr = ' ('.$collData['institutioncode'];
			if($collData['collectioncode']) $codeStr .= '-'.$collData['collectioncode'];
			$codeStr .= ')';
			echo '<h1>'.$collData['collectionname'].$codeStr.'</h1>';
			if($editCode){
				?>
				<div id="controlpanel" style="clear:both;display:<?php echo ($eMode?'block':'none'); ?>;">
					<fieldset style="padding:10px;padding-left:25px;">
						<legend><b>Data Editor Control Panel</b></legend>
						<fieldset style="float:right;margin:5px" title="Quick Search">
							<legend><b>Quick Search</b></legend>
							<b>Catalog Number</b><br/>
							<form name="quicksearch" action="../editor/occurrenceeditor.php" method="post">
								<input name="q_catalognumber" type="text" />
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="occindex" type="hidden" value="0" />
							</form>
						</fieldset>
						<ul>
							<?php
							if(stripos($collData['colltype'],'observation') !== false){
								?>
								<li>
									<a href="../editor/observationsubmit.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['SUBMIT_IMAGE_V']; ?>
									</a>
								</li>
								<?php
							}
							?>
							<li>
								<a href="../editor/occurrenceeditor.php?gotomode=1&collid=<?php echo $collid; ?>">
									<?php echo $LANG['ADD_NEW_OCCUR']; ?>
								</a>
							</li>
							<?php
							if($collData['colltype'] == 'Preserved Specimens'){
								?>
								<li style="margin-left:10px">
									<a href="../editor/imageoccursubmit.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['CREATE_NEW_REC']; ?>
									</a>
								</li>
								<li style="margin-left:10px">
									<a href="../editor/skeletalsubmit.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['SKELETAL']; ?>
									</a>
								</li>
								<?php
							}
							?>
							<li>
								<a href="../editor/occurrenceeditor.php?collid=<?php echo $collid; ?>">
									<?php echo $LANG['EDIT_EXISTING']; ?>
								</a>
							</li>
							<li>
								<a href="../editor/batchdeterminations.php?collid=<?php echo $collid; ?>">
									<?php echo $LANG['ADD_BATCH_DETER']; ?>
								</a>
							</li>
							<li>
								<a href="../reports/labelmanager.php?collid=<?php echo $collid; ?>">
									<?php echo $LANG['PRINT_LABELS']; ?>
								</a>
							</li>
							<li>
								<a href="../reports/annotationmanager.php?collid=<?php echo $collid; ?>">
									<?php echo $LANG['PRINT_ANNOTATIONS']; ?>
								</a>
							</li>
							<?php
							if($collManager->traitCodingActivated()){
								?>
								<li>
									<a href="#" onclick="$('li.traitItem').show(); return false;" >
										<?php echo (isset($LANG['TRAIT_CODING_TOOLS'])?$LANG['TRAIT_CODING_TOOLS']:'Occurrence Trait Coding Tools'); ?>
									</a>
								</li>
								<li class="traitItem" style="margin-left:10px;display:none;">
									<a href="../traitattr/occurattributes.php?collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['TRAIT_CODING'])?$LANG['TRAIT_CODING']:'Trait Coding from Images'); ?>
									</a>
								</li>
								<li class="traitItem" style="margin-left:10px;display:none;">
									<a href="../traitattr/attributemining.php?collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['TRAIT_MINING'])?$LANG['TRAIT_MINING']:'Trait Mining from Verbatim Text'); ?>
									</a>
								</li>
								<?php
							}
							?>
							<li>
								<a href="../georef/batchgeoreftool.php?collid=<?php echo $collid; ?>">
									<?php echo $LANG['BATCH_GEOREF']; ?>
								</a>
							</li>
							<?php
							if($collData['colltype'] == 'Preserved Specimens'){
								?>
								<li>
									<a href="../loans/index.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['LOAN_MANAGEMENT']; ?>
									</a>
								</li>
								<?php
							}
							?>
						</ul>
					</fieldset>
					<?php
					if($editCode > 1){
						?>
						<fieldset style="padding:10px;padding-left:25px;">
							<legend><b><?php echo $LANG['ADMIN_CONTROL']; ?></b></legend>
							<ul>

								<li>
									<a href="commentlist.php?collid=<?php echo $collid; ?>" >
										<?php echo $LANG['VIEW_COMMENTS']; ?>
									</a>
									<?php if($commCnt = $collManager->unreviewedCommentsExist()) echo '- <span style="color:orange">'.$commCnt.' '.(isset($LANG['UNREVIEWED_COMMENTS'])?$LANG['UNREVIEWED_COMMENTS']:'unreviewed comments').'</span>'; ?>
								</li>
								<li>
									<a href="collmetadata.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['EDIT_META']; ?>
									</a>
								</li>
								<!--
								<li>
									<a href="" onclick="$('li.metadataItem').show(); return false;"  >
										<?php echo $LANG['OPEN_META']; ?>
									</a>
								</li>
								<li class="metadataItem" style="margin-left:10px;display:none;">
									<a href="collmetadata.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['EDIT_META']; ?>
									</a>
								</li>
								<li class="metadataItem" style="margin-left:10px;display:none;">
									<a href="collcontact.php?collid=<?php echo $collid; ?>" >
										<?php echo $LANG['EDIT_CONTACT']; ?>
									</a>
								</li>
								<li class="metadataItem" style="margin-left:10px;display:none;">
									<a href="colladdress.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['EDIT_ADDRESS']; ?>
									</a>
								</li>
								<li class="metadataItem" style="margin-left:10px;display:none;">
									<a href="collproperties.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['EDIT_COLL_PROPS']; ?>
									</a>
								</li>
								 -->
								<li>
									<a href="collpermissions.php?collid=<?php echo $collid; ?>" >
										<?php echo $LANG['MANAGE_PERMISSIONS']; ?>
									</a>
								</li>
								<li>
									<a href="#" onclick="$('li.importItem').show(); return false;" >
										<?php echo (isset($LANG['IMPORT_SPECIMEN'])?$LANG['IMPORT_SPECIMEN']:'Import/Update Specimen Records'); ?>
									</a>
								</li>
								<li class="importItem" style="margin-left:10px;display:none;">
									<a href="../admin/specupload.php?uploadtype=7&collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['SKELETAL_FILE_IMPORT'])?$LANG['SKELETAL_FILE_IMPORT']:'Skeletal File Import'); ?>
									</a>
								</li>
								<li class="importItem" style="margin-left:10px;display:none">
									<a href="../admin/specupload.php?uploadtype=3&collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['TEXT_FILE_IMPORT'])?$LANG['TEXT_FILE_IMPORT']:'Text File Import'); ?>
									</a>
								</li>
								<li class="importItem" style="margin-left:10px;display:none;">
									<a href="../admin/specupload.php?uploadtype=6&collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['DWCA_IMPORT'])?$LANG['DWCA_IMPORT']:'DwC-Archive Import'); ?>
									</a>
								</li>
								<li class="importItem" style="margin-left:10px;display:none;">
									<a href="../admin/specupload.php?uploadtype=8&collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['IPT_IMPORT'])?$LANG['IPT_IMPORT']:'IPT Import'); ?>
									</a>
								</li>
								<li class="importItem" style="margin-left:10px;display:none;">
									<a href="../admin/specupload.php?uploadtype=9&collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['NFN_IMPORT'])?$LANG['NFN_IMPORT']:'Notes from Nature Import'); ?>
									</a>
								</li>
								<li class="importItem" style="margin-left:10px;display:none;">
									<a href="../admin/specuploadmanagement.php?collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['IMPORT_PROFILES'])?$LANG['IMPORT_PROFILES']:'Saved Import Profiles'); ?>
									</a>
								</li>
								<li class="importItem" style="margin-left:10px;display:none;">
									<a href="../admin/specuploadmanagement.php?action=addprofile&collid=<?php echo $collid; ?>">
										<?php echo (isset($LANG['CREATE_PROFILE'])?$LANG['CREATE_PROFILE']:'Create a new Import Profile'); ?>
									</a>
								</li>
								<?php
								if($collData['colltype'] != 'General Observations'){
									if($collData['managementtype'] != 'Aggregate'){
										?>
										<li>
											<a href="../specprocessor/index.php?collid=<?php echo $collid; ?>">
												<?php echo $LANG['PROCESSING_TOOLBOX']; ?>
											</a>
										</li>
										<li>
											<a href="../datasets/datapublisher.php?collid=<?php echo $collid; ?>">
												<?php echo $LANG['DARWIN_CORE_PUB']; ?>
											</a>
										</li>
										<?php
									}
									?>
									<li>
										<a href="../editor/editreviewer.php?collid=<?php echo $collid; ?>">
											<?php echo $LANG['REVIEW_SPEC_EDITS']; ?>
										</a>
									</li>
									<!--
									<li>
										<a href="../reports/accessreport.php?collid=<?php echo $collid; ?>">
											<?php echo (isset($LANG['ACCESS_REPORT'])?$LANG['ACCESS_REPORT']:'View Access Statistics'); ?>
										</a>
									</li>
									 -->
									<?php
								}
								?>
								<li>
									<a href="../datasets/duplicatemanager.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['DUP_CLUSTER']; ?>
									</a>
								</li>
								<li>
									<?php echo $LANG['MAINTENANCE_TASKS']; ?>
								</li>
								<?php
								if($collData['colltype'] != 'General Observations'){
									?>
									<li style="margin-left:10px;">
										<a href="../cleaning/index.php?obsuid=0&collid=<?php echo $collid; ?>">
											<?php echo $LANG['DATA_CLEANING']; ?>
										</a>
									</li>
									<?php
								}
								?>
								<li style="margin-left:10px;">
									<a href="#" onclick="newWindow = window.open('collbackup.php?collid=<?php echo $collid; ?>','bucollid','scrollbars=1,toolbar=0,resizable=1,width=600,height=250,left=20,top=20');">
										<?php echo $LANG['BACKUP_DATA_FILE']; ?>
									</a>
								</li>
								<?php
								if($collData['managementtype'] == 'Live Data'){
									?>
									<li style="margin-left:10px;">
										<a href="../admin/restorebackup.php?collid=<?php echo $collid; ?>">
											<?php echo (isset($LANG['RESTORE_BACKUP'])?$LANG['RESTORE_BACKUP']:'Restore Backup File'); ?>
										</a>
									</li>
									<?php
								}
								?>
								<!--
								<li style="margin-left:10px;">
									<a href="../../imagelib/admin/igsnmapper.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['GUID_MANAGEMENT']; ?>
									</a>
								</li>
								 -->
								<li style="margin-left:10px;">
									<a href="../../imagelib/admin/thumbnailbuilder.php?collid=<?php echo $collid; ?>">
										<?php echo $LANG['THUMBNAIL_MAINTENANCE']; ?>
									</a>
								</li>
								<li style="margin-left:10px;">
									<a href="collprofiles.php?collid=<?php echo $collid; ?>&action=UpdateStatistics" >
										<?php echo $LANG['UPDATE_STATS'];?>
									</a>
								</li>
							</ul>
						</fieldset>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>
			<div style='margin:10px;'>
				<?php
				echo $collManager->getMetadataHtml($collData,$LANG,$LANG_TAG);
				$datasetKey = $collManager->getDatasetKey();
				if($collData['publishtogbif'] && $datasetKey){
					$datasetKey = $collManager->getDatasetKey();
					if($datasetKey){
						$dataUrl = 'http://www.gbif.org/dataset/'.$datasetKey;
						?>
						<div style="margin-top:5px;">
							<div><b><?php echo (isset($LANG['GBIF_DATASET'])?$LANG['GBIF_DATASET']:'GBIF Dataset page'); ?>:</b> <a href="<?php echo $dataUrl; ?>" target="_blank"><?php echo $dataUrl; ?></a></div>
						</div>
						<?php
					}
				}
				if($collData['publishtoidigbio']){
					$idigbioKey = $collManager->getIdigbioKey();
					if(!$idigbioKey) $idigbioKey = $collManager->findIdigbioKey($collData['recordid']);
					if($idigbioKey){
						$dataUrl = 'https://www.idigbio.org/portal/recordsets/'.$idigbioKey;
						?>
						<div style="margin-top:5px;">
							<div><b><?php echo (isset($LANG['IDIGBIO_DATASET'])?$LANG['IDIGBIO_DATASET']:'iDigBio Dataset page'); ?>:</b> <a href="<?php echo $dataUrl; ?>" target="_blank"><?php echo $dataUrl; ?></a></div>
						</div>
						<?php
					}
				}
				if($addrArr = $collManager->getAddress()){
					?>
					<div style="margin-top:5px;">
						<div style="float:left;font-weight:bold;"><?php echo (isset($LANG['ADDRESS'])?$LANG['ADDRESS']:'Address'); ?>:</div>
						<div style="float:left;margin-left:10px;">
							<?php
							echo "<div>".$addrArr["institutionname"];
							if($editCode > 1) echo ' <a href="institutioneditor.php?emode=1&targetcollid='.$collid.'&iid='.$addrArr['iid'].'" title="'.(isset($LANG['EDIT_INST'])?$LANG['EDIT_INST']:'Edit institution information').'"><img src="../../images/edit.png" style="width:13px;" /></a>';
							echo '</div>';
							if($addrArr["institutionname2"]) echo "<div>".$addrArr["institutionname2"]."</div>";
							if($addrArr["address1"]) echo "<div>".$addrArr["address1"]."</div>";
							if($addrArr["address2"]) echo "<div>".$addrArr["address2"]."</div>";
							if($addrArr["city"]) echo "<div>".$addrArr["city"].", ".$addrArr["stateprovince"]."&nbsp;&nbsp;&nbsp;".$addrArr["postalcode"]."</div>";
							if($addrArr["country"]) echo "<div>".$addrArr["country"]."</div>";
							if($addrArr["phone"]) echo "<div>".$addrArr["phone"]."</div>";
							if($addrArr["url"]) echo '<div><a href="'.$addrArr['url'].'">'.$addrArr['url'].'</a></div>';
							if($addrArr["notes"]) echo "<div>".$addrArr["notes"]."</div>";
							?>
						</div>
					</div>
					<?php
				}
				//Collection Statistics
				$statsArr = $collManager->getBasicStats();
				$georefPerc = 0;
				if($statsArr['georefcnt']&&$statsArr['recordcnt']) $georefPerc = (100*($statsArr['georefcnt']/$statsArr['recordcnt']));
				?>
				<div style="clear:both;margin-top:5px;">
					<div style="font-weight:bold;"><?php echo $LANG['COLL_STATISTICS']; ?></div>
					<ul style="margin-top:5px;">
						<li><?php echo number_format($statsArr["recordcnt"]).' '.$LANG['SPECIMEN_RECORDS'];?></li>
						<li><?php echo ($statsArr['georefcnt']?number_format($statsArr['georefcnt']):0).($georefPerc?" (".($georefPerc>1?round($georefPerc):round($georefPerc,2))."%)":'').' '.(isset($LANG['GEOREFERENCED'])?$LANG['GEOREFERENCED']:'georeferenced');?></li>
						<?php
						$extrastatsArr = Array();
						if($statsArr['dynamicProperties']) $extrastatsArr = json_decode($statsArr['dynamicProperties'],true);
						if($extrastatsArr){
							if($extrastatsArr['imgcnt']){
								$imgSpecCnt = $extrastatsArr['imgcnt'];
								$imgCnt = 0;
								if(strpos($imgSpecCnt,':')){
									$imgCntArr = explode(':', $imgSpecCnt);
									$imgCnt = $imgCntArr[0];
									$imgSpecCnt = $imgCntArr[1];
								}
								if($imgSpecCnt){
									$imgPerc = 0;
									if($statsArr['recordcnt']) $imgPerc = (100*($imgSpecCnt/$statsArr['recordcnt']));
									echo '<li>';
									echo number_format($imgSpecCnt).($imgPerc?" (".($imgPerc>1?round($imgPerc):round($imgPerc,2))."%)":'').' '.(isset($LANG['WITH_IMAGES'])?$LANG['WITH_IMAGES']:'with images');
									if($imgCnt) echo ' ('.number_format($imgCnt).' '.(isset($LANG['TOTAL_IMAGES'])?$LANG['TOTAL_IMAGES']:'total images').')';
									echo '</li>';
								}
							}
							if($extrastatsArr['gencnt']) echo '<li>'.number_format($extrastatsArr['gencnt']).' '.(isset($LANG['GENBANK_REF'])?$LANG['GENBANK_REF']:'GenBank references').'</li>';
							if($extrastatsArr['boldcnt']) echo '<li>'.number_format($extrastatsArr['boldcnt']).' '.(isset($LANG['BOLD_REF'])?$LANG['BOLD_REF']:'BOLD references').'</li>';
							if($extrastatsArr['refcnt']) echo '<li>'.number_format($extrastatsArr['refcnt']).' '.(isset($LANG['PUB_REFS'])?$LANG['PUB_REFS']:'publication references').'</li>';
							if($extrastatsArr['SpecimensCountID']){
								$spidPerc = (100*($extrastatsArr['SpecimensCountID']/$statsArr['recordcnt']));
								echo '<li>'.number_format($extrastatsArr['SpecimensCountID']).($spidPerc?" (".($spidPerc>1?round($spidPerc):round($spidPerc,2))."%)":'').' '.(isset($LANG['IDED_TO_SPECIES'])?$LANG['IDED_TO_SPECIES']:'identified to species').'</li>';
							}
						}
						if($statsArr['familycnt']) echo '<li>'.number_format($statsArr['familycnt']).' '.$LANG['FAMILIES'].'</li>';
						if($statsArr['genuscnt']) echo '<li>'.number_format($statsArr['genuscnt']).' '.$LANG['GENERA'].'</li>';
						if($statsArr['speciescnt']) echo '<li>'.number_format($statsArr['speciescnt']).' '.$LANG['SPECIES'].'</li>';
						if($extrastatsArr&&$extrastatsArr['TotalTaxaCount']) echo '<li>'.number_format($extrastatsArr['TotalTaxaCount']).' '.(isset($LANG['TOTAL_TAXA'])?$LANG['TOTAL_TAXA']:'total taxa (including subsp. and var.)').'</li>';
						//if($extrastatsArr&&$extrastatsArr['TypeCount']) echo '<li>'.number_format($extrastatsArr['TypeCount']).' '.(isset($LANG['TYPE_SPECIMENS'])?$LANG['TYPE_SPECIMENS']:'type specimens').'</li>';
						?>
					</ul>
				</div>
			</div>
			<fieldset style='margin:20px;padding:10px;width:300px;background-color:#FFFFCC;'>
				<legend><b><?php echo $LANG['EXTRA_STATS']; ?></b></legend>
				<div style="margin:3px;">
					<a href="collprofiles.php?collid=<?php echo $collid; ?>&stat=geography#geographystats" ><?php echo $LANG['SHOW_GEOG_DIST']; ?></a>
				</div>
				<div style="margin:3px;">
					<a href="collprofiles.php?collid=<?php echo $collid; ?>&stat=taxonomy#taxonomystats" ><?php echo $LANG['SHOW_FAMILY_DIST']; ?></a>
				</div>
			</fieldset>
			<?php
			include('collprofilestats.php');
		}
		else{
			?>
			<h2><?php echo $DEFAULT_TITLE.' '.(isset($LANG['COLLECTION_PROJECTS'])?$LANG['COLLECTION_PROJECTS']:'Natural History Collections and Observation Projects'); ?></h2>
			<div style='margin:10px;clear:both;'>
				<?php
				$serverDomain = "http://";
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $serverDomain = "https://";
				$serverDomain .= $_SERVER["SERVER_NAME"];
				if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER['SERVER_PORT'] != 443) $serverDomain .= ':'.$_SERVER["SERVER_PORT"];
				echo (isset($LANG['RSS_FEED'])?$LANG['RSS_FEED']:'RSS feed').': <a href="../datasets/rsshandler.php" target="_blank">'.$serverDomain.$CLIENT_ROOT.'collections/datasets/rsshandler.php</a>';
				?>
				<hr/>
			</div>
			<table style='margin:10px;'>
				<?php
				foreach($collData as $cid => $collArr){
					?>
					<tr>
						<td style='text-align:center;vertical-align:top;'>
							<?php
							$iconStr = $collArr['icon'];
							if($iconStr){
								if(substr($iconStr,0,6) == 'images') $iconStr = '../../'.$iconStr;
								?>
								<img src='<?php echo $iconStr; ?>' style='border-size:1px;height:30;width:30;' /><br/>
								<?php
								echo $collArr['institutioncode'];
								if($collArr['collectioncode']) echo '-'.$collArr['collectioncode'];
							}
							?>
						</td>
						<td>
							<h3>
								<a href='collprofiles.php?collid=<?php echo $cid;?>'>
									<?php echo $collArr['collectionname']; ?>
								</a>
							</h3>
							<div style='margin:10px;'>
								<?php
								echo $collManager->getMetadataHtml($collArr,$LANG,$LANG_TAG);
								?>
							</div>
							<div style='margin:5px 0px 15px 10px;'>
								<a href='collprofiles.php?collid=<?php echo $cid; ?>'><?php echo $LANG['MORE_INFO']; ?></a>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2'><hr/></td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>