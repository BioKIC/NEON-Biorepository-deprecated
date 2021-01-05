<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ProfileManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

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
	if(!$collArr) echo '<div style="margin:40px 15px;font-weight:bold">You do not yet have management permissions for any occurrence projects</div>';
	foreach($genArr as $collId => $secArr){
		$cName = $secArr['collectionname'].' ('.$secArr['institutioncode'].($secArr['collectioncode']?'-'.$secArr['collectioncode']:'').')';
		?>
		<fieldset>
			<legend><?php echo $cName; ?></legend>
			<div style="margin-left:10px">
				Total Record Count: <?php echo $specHandler->getPersonalOccurrenceCount($collId); ?>
			</div>
			<ul>
				<li>
					<a href="../collections/editor/occurrencetabledisplay.php?collid=<?php echo $collId; ?>">
						Display All Records
					</a>
				</li>
				<li>
					<a href="../collections/editor/occurrencetabledisplay.php?collid=<?php echo $collId; ?>&displayquery=1">
						Search Records
					</a>
				</li>
				<li>
					<a href="../collections/editor/occurrenceeditor.php?gotomode=1&collid=<?php echo $collId; ?>">
						Add a New Record
					</a>
				</li>
				<li>
					<a href="../collections/reports/labelmanager.php?collid=<?php echo $collId; ?>">
						Print Labels
					</a>
				</li>
				<li>
					<a href="../collections/reports/annotationmanager.php?collid=<?php echo $collId; ?>">
						Print Annotation Labels
					</a>
				</li>
				<li>
					<a href="../collections/editor/observationsubmit.php?collid=<?php echo $collId; ?>">
						Submit image vouchered observation
					</a>
				</li>
				<li>
					<a href="../collections/editor/editreviewer.php?display=1&collid=<?php echo $collId; ?>">
						Review/Verify Occurrence Edits
					</a>
				</li>
				<!--
				<li>Import csv file</li>
				 -->
				<li>
					<a href="#" onclick="newWindow = window.open('personalspecbackup.php?collid=<?php echo $collId; ?>','bucollid','scrollbars=1,toolbar=0,resizable=1,width=400,height=200,left=20,top=20');">
						Backup file download (CSV extract)
					</a>
				</li>
				<li>
					<a href="../collections/misc/commentlist.php?collid=<?php echo $collId; ?>">
						View User Comments
					</a>
					<?php if($commCnt = $specHandler->unreviewedCommentsExist($collId)) echo '- <span style="color:orange">'.$commCnt.' unreviewed comments</span>'; ?>
				</li>
				<!--
				<li>
					<a href="../collections/cleaning/index.php?collid=<?php echo $collId; ?>">
						Data Cleaning Module
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
			<legend>Collection Management</legend>
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
			<legend>Observation Project Management</legend>
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
				<legend>General Observation Administration</legend>
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
		<legend>Miscellaneous Tools</legend>
		<ul>
			<li><a href="../collections/datasets/index.php">Dataset Management</a></li>
			<?php
			if((count($cArr)+count($oArr)) > 1){
				?>
				<li><a href="../collections/georef/batchgeoreftool.php">Cross Collection Georeferencing Tool</a></li>
				<?php
				if(isset($USER_RIGHTS['CollAdmin']) && count(array_diff($USER_RIGHTS['CollAdmin'],array_keys($genAdminArr))) > 1){
					?>
					<li><a href="../collections/cleaning/taxonomycleaner.php">Cross Collection Taxonomy Cleaning Tool</a></li>
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