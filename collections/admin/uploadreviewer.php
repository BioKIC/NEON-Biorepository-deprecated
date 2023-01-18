<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecUpload.php');
include_once($SERVER_ROOT.'/content/lang/collections/admin/uploadreviewer.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$searchVar = array_key_exists('searchvar',$_REQUEST)?$_REQUEST['searchvar']:'';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$recLimit = array_key_exists('reclimit',$_REQUEST)?$_REQUEST['reclimit']:1000;
$pageIndex = array_key_exists('pageindex',$_REQUEST)?$_REQUEST['pageindex']:0;

$uploadManager = new SpecUpload();
$uploadManager->setCollId($collid);

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
		$isEditor = 1;
	}
	if($isEditor){
		if($action == 'export'){
			$uploadManager->exportPendingImport($searchVar);
			exit;
		}
	}
}

$collMap = $uploadManager->getCollInfo();

/*
$recCnt = $uploadManager->getUploadCount();
$navStr = '<div style="float:right;">';
if($SYMB_UID){
	if(($pageIndex) >= $recLimit){
		$navStr .= '<a href="uploadreviewer.php?collid='.$collid.'&reclimit='.$reclimit.'&pageindex=0" title="First page">|&lt;&lt;</a> | ';
		$navStr .= '<a href="uploadreviewer.php?collid='.$collid.'&reclimit='.$reclimit.'&pageindex='.($pageIndex-1).'" title="Previous '.$recLimit.' record">&lt;&lt;</a>';
	}
	else{
		$navStr .= '|&lt;&lt;</a> | &lt;&lt;';
	}
	$navStr .= ' | ';
	$highRange = ($pageIndex*$recLimit)+$recLimit;
	$navStr .= (($pageIndex*$recLimit)+1).'-'.($recCnt<$highRange?$recCnt:$highRange).' of '.$recCnt.' records';
	$navStr .= ' | ';
	if($recCnt > $highRange){
		$navStr .= '<a href="uploadreviewer.php?collid='.$collid.'&reclimit='.$reclimit.'&pageindex='.($pageIndex+1).'" title="Next '.$recLimit.' records">&gt;&gt;</a> | ';
		$navStr .= '<a href="uploadreviewer.php?collid='.$collid.'&reclimit='.$reclimit.'&pageindex='.($recCnt/$recLimit).'" title="Last page">&gt;&gt;|</a>';
	}
	else{
		$navStr .= '&gt;&gt; | &gt;&gt;|';
	}
	$navStr .= '</div>';
}
*/
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo (isset($LANG['UP_PREVIEW'])?$LANG['UP_PREVIEW']:'Record Upload Preview'); ?></title>
    <style type="text/css">
		table.styledtable td {
		    white-space: nowrap;
		}
	</style>
	<?php
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
</head>
<body style="margin-left: 0px; margin-right: 0px;background-color:white;">
	<!-- inner text -->
	<div id="">
		<?php
		if($isEditor){
			if($collMap){
				echo '<h2>'.$collMap['name'].' ('.$collMap['institutioncode'].($collMap['collectioncode']?':'.$collMap['collectioncode']:'').')</h2>';
			}
			//Setup header map
			$recArr = $uploadManager->getPendingImportData(($recLimit*$pageIndex),$recLimit,$searchVar);
			if($recArr){
				//Check to see which headers have values
				$headerArr = array();
				foreach($recArr as $occurArr){
					foreach($occurArr as $k => $v){
						if(trim($v) && !array_key_exists($k,$headerArr)){
							$headerArr[$k] = $k;
						}
					}
				}
				$translationMap = array('catalognumber' => 'catalogNumber','occurrenceid' => 'occurrenceID','othercatalognumbers' => 'otherCatalogNumbers',
					'identificationqualifier' => 'identificationQualifier','sciname' => 'scientificName','scientificnameauthorship'=>'scientificNameAuthorship',
					'recordedby' => 'recordedBy (collector)','recordnumber' => 'Number','associatedcollectors' => 'associatedCollectors','eventdate' => 'eventDate',
					'verbatimeventdate' => 'verbatimEventDate','identificationremarks' => 'identificationRemarks','taxonremarks' => 'taxonRemarks','identifiedby' => 'identifiedBy',
					'dateidentified' => 'dateIdentified','identificationreferences' => 'identificationReferences','stateprovince' => 'stateProvince',
					'decimallatitude'=>'decimalLatitude','decimallongitude'=>'decimalLongitude','geodeticdatum'=>'geodeticDatum','coordinateuncertaintyinmeters'=>'coordinateUncertaintyInMeters',
					'verbatimcoordinates' => 'verbatimCoordinates','georeferencedby'=>'georeferencedBy','georeferenceprotocol' => 'georeferenceProtocol',
					'georeferencesources' => 'georeferenceSources','georeferenceverificationstatus' => 'georeferenceVerificationStatus','georeferenceremarks' => 'georeferenceRemarks',
					'minimumelevationinmeters' => 'minimumElevationInMeters','maximumelevationinmeters' => 'maximumElevationInMeters','verbatimelevation' => 'verbatimElevation',
					'minimumdepthinmeters' => 'minimumDepthInMeters','maximumdepthinmeters' => 'maximumDepthInMeters','verbatimdepth' => 'verbatimDepth',
					'occurrenceremarks' => 'occurrenceRemarks','associatedsequences' => 'associatedSequences','associatedtaxa' => 'associatedTaxa','verbatimattributes' => 'verbatimAttributes',
					'lifestage' => 'lifeStage', 'individualcount' => 'individualCount','samplingprotocol' => 'samplingProtocol', 'reproductivecondition' => 'reproductiveCondition',
					'typestatus' => 'typeStatus','cultivationstatus' => 'cultivationStatus','establishmentmeans' => 'establishmentMeans','duplicatequantity' => 'duplicatequantity',
					'datelastmodified' => 'dateLastModified','processingstatus' => 'processingStatus','recordenteredby' => 'recordEnteredBy',
					'basisofrecord' => 'basisOfRecord','occid' => 'occid (Primary Key)','dbpk'=>'dbpk (Source Identifier)');
				?>
				<table class="styledtable" style="font-family:Arial;font-size:12px;">
					<tr>
						<?php
						foreach($headerArr as $k => $v){
							$outStr = $v;
							if(isset($translationMap[$v])) $outStr = $translationMap[$v];
							echo '<th>'.$outStr.'</th>';
						}
						?>
					</tr>
					<?php
					$cnt = 0;
					foreach($recArr as $id => $occArr){
						if($occArr['sciname']) $occArr['sciname'] = '<i>'.$occArr['sciname'].'</i> ';
						echo "<tr ".($cnt%2?'class="alt"':'').">\n";
						foreach($headerArr as $k => $v){
							$displayStr = $occArr[$k];
							if(strlen($displayStr) > 60){
								$displayStr = substr($displayStr,0,60).'...';
							}
							if($k == 'occid' && $displayStr && $searchVar != 'new') {
								$displayStr = '<a href="../editor/occurrenceeditor.php?occid='.$displayStr.'" target="_blank">'.$displayStr.'</a>';
							}
							if(!$displayStr) $displayStr = '&nbsp;';
							echo '<td>'.$displayStr.'</td>'."\n";
						}
						echo "</tr>\n";
						$cnt++;
					}
					?>
				</table>
				<div style="width:790px;">
					<?php //echo $navStr; ?>
				</div>
				<?php
			}
			else{
				?>
				<div style="font-weight:bold;font-size:120%;margin:25px;">
					<?php echo (isset($LANG['NO_RECS'])?$LANG['NO_RECS']:'No records have been uploaded'); ?>
				</div>
				<?php
			}
		}
		else{
			echo '<h2>'.(isset($LANG['NOT_AUTH'])?$LANG['NOT_AUTH']:'You are not authorized to access this page').'</h2>';
		}
		?>
	</div>
</body>
</html>