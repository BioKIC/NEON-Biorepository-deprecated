<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
@include_once($SERVER_ROOT.'/content/lang/profile/occurrencemenu.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
unset($_SESSION['editorquery']);

$specHandler = new ProfileManager();
$specHandler->setUid($SYMB_UID);

$genArr = array();
$cArr = array();
$oArr = array();
$collArr = $specHandler->getCollectionArr();
foreach($collArr as $id => $collectionArr){
	if($collectionArr['colltype'] == 'General Observations') $genArr[$id] = $collectionArr;
	elseif($collectionArr['colltype'] == 'Preserved Specimens') $cArr[$id] = $collectionArr;
	elseif($collectionArr['colltype'] == 'Observations') $oArr[$id] = $collectionArr;
}
?>
<div style="margin:10px;">
<?php
if($SYMB_UID){
	if(!$collArr) echo '<div style="margin:40px 15px;font-weight:bold">'.(isset($LANG['NO_PROJECTS'])?$LANG['NO_PROJECTS']:'You do not yet have management permissions for any occurrence projects').'</div>';
	foreach($genArr as $collId => $secArr){
		$cName = $secArr['collectionname'].' ('.$secArr['institutioncode'].($secArr['collectioncode']?'-'.$secArr['collectioncode']:'').')';
		?>
		<fieldset>
			<legend><?php echo $cName; ?></legend>
			<div style="margin-left:10px">
				<?php
				echo (isset($LANG['TOTAL_RECORDS'])?$LANG['TOTAL_RECORDS']:'Total Record Count').': '.$specHandler->getPersonalOccurrenceCount($collId);
				?>
			</div>
			<ul>
				<li>
					<a href="../collections/editor/occurrencetabledisplay.php?collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['DISPLAY_ALL'])?$LANG['DISPLAY_ALL']:'Display All Records'); ?>
					</a>
				</li>
				<li>
					<a href="../collections/editor/occurrencetabledisplay.php?collid=<?php echo $collId; ?>&displayquery=1">
						<?php echo (isset($LANG['SEARCH_RECORDS'])?$LANG['SEARCH_RECORDS']:'Search Records'); ?>
					</a>
				</li>
				<li>
					<a href="../collections/editor/occurrenceeditor.php?gotomode=1&collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['ADD_RECORD'])?$LANG['ADD_RECORD']:'Add a New Record'); ?>
					</a>
				</li>
				<li>
					<a href="../collections/reports/labelmanager.php?collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['PRINT_LABELS'])?$LANG['PRINT_LABELS']:'Print Labels'); ?>
					</a>
				</li>
				<li>
					<a href="../collections/reports/annotationmanager.php?collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['PRINT_ANNOTATIONS'])?$LANG['PRINT_ANNOTATIONS']:'Print Annotation Labels'); ?>
					</a>
				</li>
				<li>
					<a href="../collections/editor/observationsubmit.php?collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['SUBMIT_OBSERVATION'])?$LANG['SUBMIT_OBSERVATION']:'Submit image-vouchered observation'); ?>
					</a>
				</li>
				<li>
					<a href="../collections/editor/editreviewer.php?display=1&collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['REVIEW_EDITS'])?$LANG['REVIEW_EDITS']:'Review/Verify Occurrence Edits'); ?>
					</a>
				</li>
				<!--
				<li>Import csv file</li>
				 -->
				<li>
					<a href="#" onclick="newWindow = window.open('personalspecbackup.php?collid=<?php echo $collId; ?>','bucollid','scrollbars=1,toolbar=0,resizable=1,width=400,height=200,left=20,top=20');">
						<?php echo (isset($LANG['DOWNLOAD_BACKUP'])?$LANG['DOWNLOAD_BACKUP']:'Download backup file (CSV extract)'); ?>
					</a>
				</li>
				<li>
					<a href="../collections/misc/commentlist.php?collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['VIEW_COMMENTS'])?$LANG['VIEW_COMMENTS']:'View User Comments'); ?>
					</a>
					<?php if($commCnt = $specHandler->unreviewedCommentsExist($collId)) echo '- <span style="color:orange">'.$commCnt.' '.(isset($LANG['UNREVIEWED'])?$LANG['UNREVIEWED']:'unreviewed comments').'</span>'; ?>
				</li>
				<!--
				<li>
					<a href="../collections/cleaning/index.php?collid=<?php echo $collId; ?>">
						<?php echo (isset($LANG['DATA_CLEANING'])?$LANG['DATA_CLEANING']:'Data Cleaning Module'); ?>
					</a>
				</li>
				 -->
			</ul>
		</fieldset>
		<?php
	}
	if($cArr){
		?>
		<fieldset>
			<legend><?php echo (isset($LANG['COL_MANAGE'])?$LANG['COL_MANAGE']:'Collection Management'); ?></legend>
			<ul>
				<?php
				foreach($cArr as $collId => $secArr){
					$cName = $secArr['collectionname'].' ('.$secArr['institutioncode'].($secArr['collectioncode']?'-'.$secArr['collectioncode']:'').')';
					echo '<li><a href="../collections/misc/collprofiles.php?collid='.$collId.'&emode=1">'.$cName.'</a></li>';
				}
				?>
			</ul>
		</fieldset>
		<?php
	}
	if($oArr){
		?>
		<fieldset>
			<legend><?php echo (isset($LANG['OBS_MANAGEMENT'])?$LANG['OBS_MANAGEMENT']:'Observation Project Management'); ?></legend>
			<ul>
				<?php
				foreach($oArr as $collId => $secArr){
					$cName = $secArr['collectionname'].' ('.$secArr['institutioncode'].($secArr['collectioncode']?'-'.$secArr['collectioncode']:'').')';
					echo '<li><a href="../collections/misc/collprofiles.php?collid='.$collId.'&emode=1">'.$cName.'</a></li>';
				}
				?>
			</ul>
		</fieldset>
		<?php
	}
	$genAdminArr = array();
	if($genArr && isset($USER_RIGHTS['CollAdmin'])){
		$genAdminArr = array_intersect_key($genArr,array_flip($USER_RIGHTS['CollAdmin']));
		if($genAdminArr){
			?>
			<fieldset>
				<legend><?php echo (isset($LANG['GEN_OBS_ADMIN'])?$LANG['GEN_OBS_ADMIN']:'General Observation Administration'); ?></legend>
				<ul>
					<?php
					foreach($genAdminArr as $id => $secArr){
						$cName = $secArr['collectionname'].' ('.$secArr['institutioncode'].($secArr['collectioncode']?'-'.$secArr['collectioncode']:'').')';
						echo '<li><a href="../collections/misc/collprofiles.php?collid='.$id.'&emode=1">'.$cName.'</a></li>';
					}
					?>
				</ul>
			</fieldset>
			<?php
		}
	}
	?>
	<fieldset>
		<legend><?php echo (isset($LANG['MISC_TOOLS'])?$LANG['MISC_TOOLS']:'Miscellaneous Tools'); ?></legend>
		<ul>
			<li><a href="../collections/datasets/index.php"><?php echo (isset($LANG['DATASET_MANAGEMENT'])?$LANG['DATASET_MANAGEMENT']:'Dataset Management'); ?></a></li>
			<?php
			if((count($cArr)+count($oArr)) > 1){
				?>
				<li><a href="../collections/georef/batchgeoreftool.php"><?php echo (isset($LANG['CROSS_COL_GEOREF'])?$LANG['CROSS_COL_GEOREF']:'Cross-Collection Georeferencing Tool'); ?></a></li>
				<?php
				if(isset($USER_RIGHTS['CollAdmin']) && count(array_diff($USER_RIGHTS['CollAdmin'],array_keys($genAdminArr))) > 1){
					?>
					<li><a href="../collections/cleaning/taxonomycleaner.php"><?php echo (isset($LANG['CROSS_COL_TAXON'])?$LANG['CROSS_COL_TAXON']:'Cross Collection Taxonomy Cleaning Tool'); ?></a></li>
					<?php
				}
			}
			?>
		</ul>
	</fieldset>
	<?php
}
?>
</div>