<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDownload.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverCore.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/specprocessor/geolocate.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/specprocessor/geolocate.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/specprocessor/geolocate.en.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

//Sanitation
if(!is_numeric($collid)) $collid = 0;

$customField1 = array_key_exists('customfield1',$_REQUEST)?$_REQUEST['customfield1']:'';
$customType1 = array_key_exists('customtype1',$_REQUEST)?$_REQUEST['customtype1']:'';
$customValue1 = array_key_exists('customvalue1',$_REQUEST)?$_REQUEST['customvalue1']:'';
$customField2 = array_key_exists('customfield2',$_REQUEST)?$_REQUEST['customfield2']:'';
$customType2 = array_key_exists('customtype2',$_REQUEST)?$_REQUEST['customtype2']:'';
$customValue2 = array_key_exists('customvalue2',$_REQUEST)?$_REQUEST['customvalue2']:'';
$customField3 = array_key_exists('customfield3',$_REQUEST)?$_REQUEST['customfield3']:'';
$customType3 = array_key_exists('customtype3',$_REQUEST)?$_REQUEST['customtype3']:'';
$customValue3 = array_key_exists('customvalue3',$_REQUEST)?$_REQUEST['customvalue3']:'';

$dlManager = new OccurrenceDownload();
$collMeta = $dlManager->getCollectionMetadata($collid);

$isEditor = false;
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
 	$isEditor = true;
}

$advFieldArr = array('family'=>'Family','sciname'=>'Scientific Name','identifiedBy'=>'Identified By','typeStatus'=>'Type Status',
	'catalogNumber'=>'Catalog Number','otherCatalogNumbers'=>'Other Catalog Numbers','occurrenceId'=>'Occurrence ID (GUID)',
	'recordedBy'=>'Collector/Observer','recordNumber'=>'Collector Number','associatedCollectors'=>'Associated Collectors',
	'eventDate'=>'Collection Date','verbatimEventDate'=>'Verbatim Date','habitat'=>'Habitat','substrate'=>'Substrate','occurrenceRemarks'=>'Occurrence Remarks',
	'associatedTaxa'=>'Associated Taxa','verbatimAttributes'=>'Description','reproductiveCondition'=>'Reproductive Condition',
	'establishmentMeans'=>'Establishment Means','cultivationStatus'=>'Cultivation Status','lifeStage'=>'Life Stage','sex'=>'Sex',
	'individualCount'=>'Individual Count','samplingProtocol'=>'Sampling Protocol','country'=>'Country',
	'stateProvince'=>'State/Province','county'=>'County','municipality'=>'Municipality','locality'=>'Locality',
	'decimalLatitude'=>'Decimal Latitude','decimalLongitude'=>'Decimal Longitude','geodeticDatum'=>'Geodetic Datum',
	'coordinateUncertaintyInMeters'=>'Uncertainty (m)','verbatimCoordinates'=>'Verbatim Coordinates',
	'georeferencedBy'=>'Georeferenced By','georeferenceProtocol'=>'Georeference Protocol','georeferenceSources'=>'Georeference Sources',
	'georeferenceVerificationStatus'=>'Georeference Verification Status','georeferenceRemarks'=>'Georeference Remarks',
	'minimumElevationInMeters'=>'Elevation Minimum (m)','maximumElevationInMeters'=>'Elevation Maximum (m)',
	'verbatimElevation'=>'Verbatim Elevation','disposition'=>'Disposition');
?>
<html>
	<head>
		<title><?php echo $LANG['OCC_EXP_MAN']; ?></title>
		<?php
		$activateJQuery = false;
		if(file_exists($SERVER_ROOT.'/includes/head.php')){
			include_once($SERVER_ROOT.'/includes/head.php');
		}
		else{
			echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
			echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
		}
		?>
		<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="../../js/symb/shared.js" type="text/javascript"></script>
		<script src="../../js/symb/geolocate.js?ver=1.0" type="text/javascript"></script>
	</head>
	<body>
		<!-- This is inner text! -->
		<div id="innertext" style="background-color:white;">
			<?php
			if($collid && $isEditor){
				if($ACTIVATE_GEOLOCATE_TOOLKIT){
					//GeoLocate tools
					?>
					<form name="expgeolocateform" action="../download/downloadhandler.php" method="post" onsubmit="">
						<fieldset>
							<legend><b><?php echo $LANG['GEO_COM_TOOL']; ?></b></legend>
							<div style="margin:15px;">
								<?php echo $LANG['GEO_COM_TOOL_EXPLAIN']; ?>
							</div>
							<table>
								<tr>
									<td>
										<div style="margin:10px;">
											<b><?php echo $LANG['PROCESSING_STATUS']; ?>:</b>
										</div>
									</td>
									<td>
										<div style="margin:10px 0px;">
											<select name="processingstatus" onchange="cogeUpdateCount(this)">
												<option value=""><?php echo $LANG['ALL_RECORDS']; ?></option>
												<?php
												$statusArr = $dlManager->getProcessingStatusList($collid);
												foreach($statusArr as $v){
													echo '<option value="'.$v.'">'.ucwords($v).'</option>';
												}
												?>
											</select>
										</div>
									</td>
								</tr>
 								<tr>
									<td>
										<div style="margin:10px;">
											<b><?php echo $LANG['ADDITIONAL_FILTERS']; ?>:</b>
										</div>
									</td>
									<td>
										<?php
										$conditionArr = array('EQUALS'=>'EQUALS','NOTEQUALS'=>'NOT EQUALS','STARTS'=>'STARTS WITH','LIKE'=>'CONTAINS','NOTLIKE'=>'DOES NOT CONTAIN','NULL'=>'IS NULL','NOTNULL'=>'IS NOT NULL');
										?>
										<div style="margin:10px 0px;">
											<select name="customfield1" style="width:200px">
												<option value=""><?php echo $LANG['SELECT_FIELD']; ?></option>
												<option value="">---------------------------------</option>
												<?php
												foreach($advFieldArr as $k => $v){
													echo '<option value="'.$k.'" '.($k==$customField1?'SELECTED':'').'>'.$v.'</option>';
												}
												?>
											</select>
											<select name="customtype1" onchange="cogeUpdateCount(this)">
												<?php
												foreach($conditionArr as $condKey => $condValue){
													echo '<option '.($customType1==$condKey?'SELECTED':'').' value="'.$condKey.'">'.$condValue.'</option>';
												}
												?>
											</select>
											<input name="customvalue1" type="text" value="<?php echo $customValue1; ?>" style="width:200px;" onchange="cogeUpdateCount(this)" />
										</div>
										<div style="margin:10px 0px;">
											<select name="customfield2" style="width:200px">
												<option value=""><?php echo $LANG['SELECT_FIELD']; ?></option>
												<option value="">---------------------------------</option>
												<?php
												foreach($advFieldArr as $k => $v){
													echo '<option value="'.$k.'" '.($k==$customField2?'SELECTED':'').'>'.$v.'</option>';
												}
												?>
											</select>
											<select name="customtype2" onchange="cogeUpdateCount(this)">
												<?php
												foreach($conditionArr as $condKey2 => $condValue2){
													echo '<option '.($customType2==$condKey2?'SELECTED':'').' value="'.$condKey2.'">'.$condValue2.'</option>';
												}
												?>
											</select>
											<input name="customvalue2" type="text" value="<?php echo $customValue2; ?>" style="width:200px;" onchange="cogeUpdateCount(this)" />
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<fieldset style="margin:10px;padding:20px;">
											<legend><b><?php echo $LANG['COGE_STATUS']; ?></b></legend>
											<div>
												<b><?php echo $LANG['MATCH_COUNT']; ?>:</b>
												<?php
												$dwcaHandler = new DwcArchiverCore();
												$dwcaHandler->setCollArr($collid);
												$dwcaHandler->setVerboseMode(0);
												$dwcaHandler->addCondition('decimallatitude','NULL');
												$dwcaHandler->addCondition('decimallongitude','NULL');
												$dwcaHandler->addCondition('locality','NOTNULL');
												$dwcaHandler->addCondition('catalognumber','NOTNULL');
												echo '<span id="countdiv">'.$dwcaHandler->getOccurrenceCnt().'</span> records';
												?>
												<span id="recalspan" style="color:orange;display:none;"><?php echo $LANG['RECALCULATING']; ?>... <img src="../../images/workingcircle.gif" style="width:13px;" /></span>
											</div>
											<div>
												<b><?php echo $LANG['COGE_AUTH']; ?>:</b>
												<span id="coge-status" style="width:150px;color:red;"><?php echo $LANG['DISCONNECTED']; ?></span>
												<span style="margin-left:40px"><button name="cogeCheckStatusButton" value="Check Status" onclick="cogeCheckAuthentication()"><?php echo $LANG['CHECK_STATUS']; ?></button></span>
												<span style="margin-left:40px"><a href="http://coge.geo-locate.org" target="_blank" onclick="startAuthMonitoring()"><?php echo $LANG['LOGIN_COGE']; ?></a></span>
											</div>
										</fieldset>
										<fieldset id="coge-communities" style="margin:10px;padding:10px;">
											<legend style="font-weight:bold"><?php echo $LANG['AVAILABLE_COMMS']; ?></legend>
											<div style="margin:10px;">
												<?php echo $LANG['TO_IMPORT_DATA']; ?>
											</div>
											<div style="margin:10px;">
												<div id="coge-commlist" style="margin:15px 0px;padding:15px;border:1px solid orange;">
													<span style="color:orange;"><?php echo $LANG['LOGIN_AND_CHECK']; ?></span>
												</div>
												<div id="coge-fieldDiv" style="display:none">
													<div style="margin:5px;clear:both;">
														<div style="float:left;"><?php echo $LANG['DATA_SOURCE_ID']; ?>:</div>
														<div style="margin-left:250px;"><input name="cogename" type="text" style="width:300px" onchange="verifyDataSourceIdentifier(this.form)" /></div>
													</div>
													<div style="margin:5px;clear:both;">
														<div style="float:left;"><?php echo $LANG['DESCRIPTION']; ?>:</div>
														<div style="margin-left:250px;"><input name="cogedescr" type="text" style="width:300px" /></div>
													</div>
												</div>
											</div>
										</fieldset>
										<div style="margin:20px;clear:both;">
											<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
											<input name="format" type="hidden" value="csv" />
											<input name="schema" type="hidden" value="coge" />
											<div style="margin:5px">
												<button id="builddwcabutton" name="builddwcabutton" value="Push Data to GeoLocate CoGe" onclick="cogePublishDwca(this.form)" disabled ><?php echo $LANG['PUSH_DATA']; ?></button>
												<span id="coge-download" style="display:none;color:orange"><?php echo $LANG['CREATING_PACKAGE']; ?>... <img src="../../images/workingcircle.gif" style="width:13px;" /></span>
												<span id="coge-push2coge" style="display:none;color:orange"><?php echo $LANG['PUSHING_TO_COGE']; ?>... <img src="../../images/workingcircle.gif" style="width:13px;" /></span>
												<span id="coge-importcomplete" style="display:none;color:green">
													<?php echo $LANG['SUCCESS_GEOLOCATE']; ?>
												</span>
											</div>
											<div style="margin-left:15px">
												<div id="coge-dwcalink"></div>
												<div id="coge-guid"></div>
												<div id="coge-importstatus" style="color:orange;display:none;">
													<?php echo $LANG['DATA_IMPORT_COMPLETE']; ?>
												</div>
											</div>
											<div style="margin:5px">
												<button name="submitaction" type="submit" value="Download Records Locally" ><?php echo $LANG['DOWNLOAD_LOCALLY']; ?></button>
											</div>
											<div style="margin:5px">
												<button name="resetbutton" type="button" value="Reset Page" onclick="cogeCheckAuthentication(); return false;" ><?php echo $LANG['RESET_PAGE']; ?></button>
											</div>
										</div>
										<div style="margin:20px;">
											<a href="../editor/editreviewer.php?collid=<?php echo $collid; ?>&display=2"><?php echo $LANG['REVIEW_APPROVE_EDITS']; ?></a>
										</div>
										<div style="margin:20px;">
											<b>* <?php echo $LANG['DEFAULT_QUERY']; ?></b>
										</div>
									</td>
								</tr>
							</table>
						</fieldset>
					</form>
					<?php
				}
			}
			else{
				echo '<div style="font-weight:bold;">'.$LANG['ACCESS_DENIED'].'</div>';
			}
			?>
		</div>
	</body>
</html>