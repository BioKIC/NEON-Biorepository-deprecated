<?php
if(!$displayQuery && array_key_exists('displayquery',$_REQUEST)) $displayQuery = $_REQUEST['displayquery'];
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/collections/editor/includes/queryform.en.php');

$qryArr = $occManager->getQueryVariables();
$qCatalogNumber = (array_key_exists('cn',$qryArr)?$qryArr['cn']:'');
$qOtherCatalogNumbers = (array_key_exists('ocn',$qryArr)?$qryArr['ocn']:'');
$qRecordedBy = (array_key_exists('rb',$qryArr)?$qryArr['rb']:'');
$qRecordNumber = (array_key_exists('rn',$qryArr)?$qryArr['rn']:'');
$qEventDate = (array_key_exists('ed',$qryArr)?$qryArr['ed']:'');
$qRecordEnteredBy = (array_key_exists('eb',$qryArr)?$qryArr['eb']:'');
$qReturnAll = (array_key_exists('returnall',$qryArr)?$qryArr['returnall']:0);
$qProcessingStatus = (array_key_exists('ps',$qryArr)?$qryArr['ps']:'');
$qDateEntered = (array_key_exists('de',$qryArr)?$qryArr['de']:'');
$qDateLastModified = (array_key_exists('dm',$qryArr)?$qryArr['dm']:'');
$qExsiccatiId = (array_key_exists('exsid',$qryArr)?$qryArr['exsid']:'');
$qImgOnly = (array_key_exists('io',$qryArr)?$qryArr['io']:0);
$qWithoutImg = (array_key_exists('woi',$qryArr)?$qryArr['woi']:0);
$qCustomField1 = (array_key_exists('cf1',$qryArr)?$qryArr['cf1']:'');
$qCustomType1 = (array_key_exists('ct1',$qryArr)?$qryArr['ct1']:'');
$qCustomValue1 = (array_key_exists('cv1',$qryArr)?htmlentities($qryArr['cv1'], ENT_COMPAT, $CHARSET):'');
$qCustomField2 = (array_key_exists('cf2',$qryArr)?$qryArr['cf2']:'');
$qCustomType2 = (array_key_exists('ct2',$qryArr)?$qryArr['ct2']:'');
$qCustomValue2 = (array_key_exists('cv2',$qryArr)?htmlentities($qryArr['cv2'], ENT_COMPAT, $CHARSET):'');
$qCustomField3 = (array_key_exists('cf3',$qryArr)?$qryArr['cf3']:'');
$qCustomType3 = (array_key_exists('ct3',$qryArr)?$qryArr['ct3']:'');
$qCustomValue3 = (array_key_exists('cv3',$qryArr)?htmlentities($qryArr['cv3'], ENT_COMPAT, $CHARSET):'');
$qOcrFrag = (array_key_exists('ocr',$qryArr)?htmlentities($qryArr['ocr'], ENT_COMPAT, $CHARSET):'');
$qOrderBy = (array_key_exists('orderby',$qryArr)?$qryArr['orderby']:'');
$qOrderByDir = (array_key_exists('orderbydir',$qryArr)?$qryArr['orderbydir']:'');

//Set processing status
$processingStatusArr = array();
if(isset($PROCESSINGSTATUS) && $PROCESSINGSTATUS){
	$processingStatusArr = $PROCESSINGSTATUS;
}
else{
	$processingStatusArr = array('unprocessed','unprocessed/NLP','stage 1','stage 2','stage 3','pending review-nfn','pending review','expert required','reviewed','closed');
}
//if(!isset($_REQUEST['q_catalognumber'])) $displayQuery = true;
?>
<div id="querydiv" style="clear:both;width:850px;display:<?php echo ($displayQuery?'block':'none'); ?>;">
	<form name="queryform" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return verifyQueryForm(this)">
		<fieldset style="padding:5px;">
			<legend><?php echo $LANG['RECORD_SEARCH_FORM']; ?></legend>
			<?php
			if(!$crowdSourceMode){
				?>
				<div class="fieldGroupDiv">
					<div class="fieldDiv" title="<?php echo $LANG['WILD_EXPLAIN']; ?>">
						<?php echo $LANG['COLLECTOR']; ?>:
						<input type="text" name="q_recordedby" value="<?php echo $qRecordedBy; ?>" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['SEPARATE_RANGES']; ?>">
						<?php echo $LANG['NUMBER']; ?>:
						<input type="text" name="q_recordnumber" value="<?php echo $qRecordNumber; ?>" style="width:120px;" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['ENTER_RANGES']; ?>">
						<?php echo $LANG['DATE']; ?>:
						<input type="text" name="q_eventdate" value="<?php echo $qEventDate; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<?php
			}
			?>
			<div class="fieldGroupDiv">
				<div class="fieldDiv" title="<?php echo $LANG['SEPARATE_RANGES']; ?>">
					<?php echo $LANG['CAT_NUM']; ?>:
					<input type="text" name="q_catalognumber" value="<?php echo $qCatalogNumber; ?>" onchange="setOrderBy(this)" />
				</div>
				<?php
				if($crowdSourceMode){
					?>
					<div class="fieldDiv" title="Search for term embedded within OCR block of text">
						<?php echo $LANG['OCR_FRAGMENT']; ?>:
						<input type="text" name="q_ocrfrag" value="<?php echo $qOcrFrag; ?>" style="width:200px;" />
					</div>
					<?php
				}
				else{
					?>
					<div class="fieldDiv" title="<?php echo $LANG['SEPARATE_RANGES']; ?>">
						<?php echo $LANG['OTHER_CAT_NUMS']; ?>:
						<input type="text" name="q_othercatalognumbers" value="<?php echo $qOtherCatalogNumbers; ?>" />
					</div>
					<?php
				}
				?>
			</div>
			<?php
			if(!$crowdSourceMode){
				?>
				<div class="fieldGroupDiv">
					<div class="fieldDiv" style="<?php echo ($isGenObs?'display:none':''); ?>">
						<?php echo $LANG['ENTERED_BY']; ?>:
						<input type="text" name="q_recordenteredby" value="<?php echo $qRecordEnteredBy; ?>" style="width:70px;" onchange="setOrderBy(this)" />
						<button type="button" onclick="enteredByCurrentUser()" style="font-size:70%" title="<?php echo $LANG['LIMIT_TO_CURRENT']; ?>"><?php echo $LANG['CU']; ?></button>
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['ENTER_RANGES']; ?>">
						<?php echo $LANG['DATE_ENTERED']; ?>:
						<input type="text" name="q_dateentered" value="<?php echo $qDateEntered; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="<?php echo $LANG['ENTER_RANGES']; ?>">
						<?php echo $LANG['DATE_MODIFIED']; ?>:
						<input type="text" name="q_datelastmodified" value="<?php echo $qDateLastModified; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						<?php echo $LANG['PROC_STATUS']; ?>:
						<select name="q_processingstatus" onchange="setOrderBy(this)">
							<option value=''><?php echo $LANG['ALL_RECORDS']; ?></option>
							<option>-------------------</option>
							<?php
							foreach($processingStatusArr as $v){
								//Don't display these options is editor is crowd sourced
								$keyOut = strtolower($v);
								echo '<option value="'.$keyOut.'" '.($qProcessingStatus==$keyOut?'SELECTED':'').'>'.ucwords($v).'</option>';
							}
							echo '<option value="isnull" '.($qProcessingStatus=='isnull'?'SELECTED':'').'>'.$LANG['NO_SET_STATUS'].'</option>';
							if($qProcessingStatus && $qProcessingStatus != 'isnull' && !in_array($qProcessingStatus,$processingStatusArr)){
								echo '<option value="'.$qProcessingStatus.'" SELECTED>'.$qProcessingStatus.'</option>';
							}
							?>
						</select>
					</div>
					<div class="fieldDiv">
						<input name="q_imgonly" type="checkbox" value="1" <?php echo ($qImgOnly==1?'checked':''); ?> onchange="this.form.q_withoutimg.checked = false;" />
						<?php echo $LANG['WITH_IMAGES']; ?>
					</div>
					<div class="fieldDiv">
						<input name="q_withoutimg" type="checkbox" value="1" <?php echo ($qWithoutImg==1?'checked':''); ?> onchange="this.form.q_imgonly.checked = false;" />
						<?php echo $LANG['WITHOUT_IMAGES']; ?>
					</div>
				</div>
				<?php
				if($ACTIVATE_EXSICCATI){
					if($exsList = $occManager->getExsiccatiList()){
						?>
						<div class="fieldGroupDiv" title="<?php echo $LANG['ENTER_EXS_TITLE']; ?>">
							<div class="fieldDiv">
								<?php echo $LANG['EXS_TITLE']; ?>:
								<select name="q_exsiccatiid" style="max-width:650px">
									<option value=""></option>
									<?php
									foreach($exsList as $exsID => $exsTitle){
										echo '<option value="'.$exsID.'" '.($qExsiccatiId==$exsID?'SELECTED':'').'>'.$exsTitle.'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<?php
					}
				}
			}
			$advFieldArr = array();
			if($crowdSourceMode){
				$advFieldArr = array('family'=>$LANG['FAMILY'],'sciname'=>$LANG['SCI_NAME'],'othercatalognumbers'=>$LANG['OTHER_CAT_NUMS'],
					'country'=>$LANG['COUNTRY'],'stateProvince'=>$LANG['STATE_PROVINCE'],'county'=>$LANG['COUNTY'],'municipality'=>$LANG['MUNICIPALITY'],
					'recordedby'=>$LANG['COLLECTOR'],'recordnumber'=>$LANG['COL_NUMBER'],'eventdate'=>$LANG['COL_DATE']);
			}
			else{
				$advFieldArr = array('associatedCollectors'=>$LANG['ASSOC_COLLECTORS'],'associatedOccurrences'=>$LANG['ASSOC_OCCS'],
					'associatedTaxa'=>$LANG['ASSOC_TAXA'],'attributes'=>$LANG['ATTRIBUTES'],'scientificNameAuthorship'=>$LANG['AUTHOR'],
					'basisOfRecord'=>$LANG['BASIS_OF_RECORD'],'behavior'=>$LANG['BEHAVIOR'],'catalogNumber'=>$LANG['CAT_NUM'],'collectionCode'=>$LANG['COL_CODE'],'recordNumber'=>$LANG['COL_NUMBER'],
					'recordedBy'=>$LANG['COL_OBS'],'coordinateUncertaintyInMeters'=>$LANG['COORD_UNCERT_M'],'country'=>$LANG['COUNTRY'],
					'county'=>$LANG['COUNTY'],'cultivationStatus'=>$LANG['CULT_STATUS'],'dataGeneralizations'=>$LANG['DATA_GEN'],'eventDate'=>$LANG['DATE'],
					'dateEntered'=>$LANG['DATE_ENTERED'],'dateLastModified'=>$LANG['DATE_LAST_MODIFIED'],'dbpk'=>$LANG['DBPK'],'decimalLatitude'=>$LANG['DEC_LAT'],
					'decimalLongitude'=>$LANG['DEC_LONG'],'maximumDepthInMeters'=>$LANG['DEPTH_MAX'],'minimumDepthInMeters'=>$LANG['DEPTH_MIN'],
					'verbatimAttributes'=>$LANG['DESCRIPTION'],'disposition'=>$LANG['DISPOSITION'],'dynamicProperties'=>$LANG['DYNAMIC_PROPS'],
					'maximumElevationInMeters'=>$LANG['ELEV_MAX_M'],'minimumElevationInMeters'=>$LANG['ELEV_MIN_M'],
					'establishmentMeans'=>$LANG['ESTAB_MEANS'],'family'=>$LANG['FAMILY'],'fieldNotes'=>$LANG['FIELD_NOTES'],'fieldnumber'=>$LANG['FIELD_NUMBER'],
					'geodeticDatum'=>$LANG['GEO_DATUM'],'georeferenceProtocol'=>$LANG['GEO_PROTOCOL'],
					'georeferenceRemarks'=>$LANG['GEO_REMARKS'],'georeferenceSources'=>$LANG['GEO_SOURCES'],
					'georeferenceVerificationStatus'=>$LANG['GEO_VERIF_STATUS'],'georeferencedBy'=>$LANG['GEO_BY'],'habitat'=>$LANG['HABITAT'],
					'identificationQualifier'=>$LANG['ID_QUALIFIER'],'identificationReferences'=>$LANG['ID_REFERENCES'],
					'identificationRemarks'=>$LANG['ID_REMARKS'],'identifiedBy'=>$LANG['IDED_BY'],'individualCount'=>$LANG['IND_COUNT'],
					'informationWithheld'=>$LANG['INFO_WITHHELD'],'institutionCode'=>$LANG['INST_CODE'],'labelProject'=>$LANG['LAB_PROJECT'],
					'language'=>$LANG['LANGUAGE'],'lifeStage'=>$LANG['LIFE_STAGE'],'locationid'=>$LANG['LOCATION_ID'],'locality'=>$LANG['LOCALITY'],
					'localitySecurity'=>$LANG['LOC_SEC'],'localitySecurityReason'=>$LANG['LOC_SEC_REASON'],'locationRemarks'=>$LANG['LOC_REMARKS'],
					'username'=>$LANG['MODIFIED_BY'],'municipality'=>$LANG['MUNICIPALITY'],'occurrenceRemarks'=>$LANG['NOTES_REMARKS'],'ocrFragment'=>$LANG['OCR_FRAGMENT'],
					'otherCatalogNumbers'=>$LANG['OTHER_CAT_NUMS'],'ownerInstitutionCode'=>$LANG['OWNER_CODE'],'preparations'=>$LANG['PREPARATIONS'],
					'reproductiveCondition'=>$LANG['REP_COND'],'samplingEffort'=>$LANG['SAMP_EFFORT'],'samplingProtocol'=>$LANG['SAMP_PROTOCOL'],
					'sciname'=>$LANG['SCI_NAME'],'sex'=>$LANG['SEX'],'stateProvince'=>$LANG['STATE_PROVINCE'],
					'substrate'=>$LANG['SUBSTRATE'],'taxonRemarks'=>$LANG['TAXON_REMARKS'],'typeStatus'=>$LANG['TYPE_STATUS'],'verbatimCoordinates'=>$LANG['VERBAT_COORDS'],
					'verbatimEventDate'=>$LANG['VERBATIM_DATE'],'verbatimDepth'=>$LANG['VERBATIM_DEPTH'],'verbatimElevation'=>$LANG['VERBATIM_ELE']);
			}
			// sort($advFieldArr);
			?>
			<div class="fieldGroupDiv">
				<?php echo $LANG['CUSTOM_FIELD_1']; ?>:
				<select name="q_customfield1" onchange="customSelectChanged(1)">
					<option value=""><?php echo $LANG['SELECT_FIELD_NAME']; ?></option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField1?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype1">
					<option><?php echo $LANG['EQUALS']; ?></option>
					<option <?php echo ($qCustomType1=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS"><?php echo $LANG['NOT_EQUALS']; ?></option>
					<option <?php echo ($qCustomType1=='STARTS'?'SELECTED':''); ?> value="STARTS"><?php echo $LANG['STARTS_WITH']; ?></option>
					<option <?php echo ($qCustomType1=='LIKE'?'SELECTED':''); ?> value="LIKE"><?php echo $LANG['CONTAINS']; ?></option>
					<option <?php echo ($qCustomType1=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE"><?php echo $LANG['DOESNT_CONTAIN']; ?></option>
					<option <?php echo ($qCustomType1=='GREATER'?'SELECTED':''); ?> value="GREATER"><?php echo $LANG['GREATER_THAN']; ?></option>
					<option <?php echo ($qCustomType1=='LESS'?'SELECTED':''); ?> value="LESS"><?php echo $LANG['LESS_THAN']; ?></option>
					<option <?php echo ($qCustomType1=='NULL'?'SELECTED':''); ?> value="NULL"><?php echo $LANG['IS_NULL']; ?></option>
					<option <?php echo ($qCustomType1=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL"><?php echo $LANG['IS_NOT_NULL']; ?></option>
				</select>
				<input name="q_customvalue1" type="text" value="<?php echo $qCustomValue1; ?>" style="width:200px;" />
				<a href="#" onclick="toggleCustomDiv2();return false;">
					<img src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv2" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue2||$qCustomType2=='NULL'||$qCustomType2=='NOTNULL'?'block':'none');?>;">
				<?php echo $LANG['CUSTOM_FIELD_2']; ?>:
				<select name="q_customfield2" onchange="customSelectChanged(2)">
					<option value=""><?php echo $LANG['SELECT_FIELD_NAME']; ?></option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField2?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype2">
					<option><?php echo $LANG['EQUALS']; ?></option>
					<option <?php echo ($qCustomType2=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS"><?php echo $LANG['NOT_EQUALS']; ?></option>
					<option <?php echo ($qCustomType2=='STARTS'?'SELECTED':''); ?> value="STARTS"><?php echo $LANG['STARTS_WITH']; ?></option>
					<option <?php echo ($qCustomType2=='LIKE'?'SELECTED':''); ?> value="LIKE"><?php echo $LANG['CONTAINS']; ?></option>
					<option <?php echo ($qCustomType2=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE"><?php echo $LANG['DOESNT_CONTAIN']; ?></option>
					<option <?php echo ($qCustomType2=='GREATER'?'SELECTED':''); ?> value="GREATER"><?php echo $LANG['GREATER_THAN']; ?></option>
					<option <?php echo ($qCustomType2=='LESS'?'SELECTED':''); ?> value="LESS"><?php echo $LANG['LESS_THAN']; ?></option>
					<option <?php echo ($qCustomType2=='NULL'?'SELECTED':''); ?> value="NULL"><?php echo $LANG['IS_NULL']; ?></option>
					<option <?php echo ($qCustomType2=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL"><?php echo $LANG['IS_NOT_NULL']; ?></option>
				</select>
				<input name="q_customvalue2" type="text" value="<?php echo $qCustomValue2; ?>" style="width:200px;" />
				<a href="#" onclick="toggleCustomDiv3();return false;">
					<img src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv3" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue3||$qCustomType3=='NULL'||$qCustomType3=='NOTNULL'?'block':'none');?>;">
				<?php echo $LANG['CUSTOM_FIELD_3']; ?>:
				<select name="q_customfield3" onchange="customSelectChanged(3)">
					<option value=""><?php echo $LANG['SELECT_FIELD_NAME']; ?></option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField3?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype3">
					<option><?php echo $LANG['EQUALS']; ?></option>
					<option <?php echo ($qCustomType3=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS"><?php echo $LANG['NOT_EQUALS']; ?></option>
					<option <?php echo ($qCustomType3=='STARTS'?'SELECTED':''); ?> value="STARTS"><?php echo $LANG['STARTS_WITH']; ?></option>
					<option <?php echo ($qCustomType3=='LIKE'?'SELECTED':''); ?> value="LIKE"><?php echo $LANG['CONTAINS']; ?></option>
					<option <?php echo ($qCustomType3=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE"><?php echo $LANG['DOESNT_CONTAIN']; ?></option>
					<option <?php echo ($qCustomType3=='GREATER'?'SELECTED':''); ?> value="GREATER"><?php echo $LANG['GREATER_THAN']; ?></option>
					<option <?php echo ($qCustomType3=='LESS'?'SELECTED':''); ?> value="LESS"><?php echo $LANG['LESS_THAN']; ?></option>
					<option <?php echo ($qCustomType3=='NULL'?'SELECTED':''); ?> value="NULL"><?php echo $LANG['IS_NULL']; ?></option>
					<option <?php echo ($qCustomType3=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL"><?php echo $LANG['IS_NOT_NULL']; ?></option>
				</select>
				<input name="q_customvalue3" type="text" value="<?php echo $qCustomValue3; ?>" style="width:200px;" />
			</div>
			<div class="fieldGroupDiv">
				<?php
				if($isGenObs && ($IS_ADMIN || ($collId && array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS["CollAdmin"])))){
					?>
					<div class="fieldDiv">
						<input type="checkbox" name="q_returnall" value="1" <?php echo ($qReturnAll?'CHECKED':''); ?> /> <?php echo $LANG['SHOW_RECS_ALL']; ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="fieldGroupDiv">
				<?php
				if(!$crowdSourceMode){
					$qryStr = '';
					if($qRecordedBy) $qryStr .= '&recordedby='.$qRecordedBy;
					if($qRecordNumber) $qryStr .= '&recordnumber='.$qRecordNumber;
					if($qEventDate) $qryStr .= '&eventdate='.$qEventDate;
					if($qCatalogNumber) $qryStr .= '&catalognumber='.$qCatalogNumber;
					if($qOtherCatalogNumbers) $qryStr .= '&othercatalognumbers='.$qOtherCatalogNumbers;
					if($qRecordEnteredBy) $qryStr .= '&recordenteredby='.$qRecordEnteredBy;
					if($qDateEntered) $qryStr .= '&dateentered='.$qDateEntered;
					if($qDateLastModified) $qryStr .= '&datelastmodified='.$qDateLastModified;
					if($qryStr){
						?>
						<div style="float:right;margin-top:10px;" title="<?php echo $LANG['GO_LABEL_PRINT']; ?>">
							<a href="../reports/labelmanager.php?collid=<?php echo $collId.$qryStr; ?>">
								<img src="../../images/list.png" style="width:15px;" />
							</a>
						</div>
						<?php
					}
				}
				?>
				<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
				<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
				<input type="hidden" name="occid" value="<?php echo $occManager->getOccId(); ?>" />
				<input type="hidden" name="occindex" value="<?php echo $occManager->getOccIndex(); ?>" />
				<input type="hidden" name="occidlist" value="<?php echo $occManager->getOccidIndexStr(); ?>" />
				<input type="hidden" name="direction" value="" />
				<button name="submitaction" value="Display Editor" onclick="submitQueryEditor(this.form)" ><?php echo $LANG['DISPLAY_EDITOR']; ?></button>
				<button name="submitaction" value="Display Table" onclick="submitQueryTable(this.form)" ><?php echo $LANG['DISPLAY_TABLE']; ?></button>
				<span style="margin-left:10px;">
					<input type="button" name="reset" value="Reset Form" onclick="resetQueryForm(this.form)" />
				</span>
				<span style="margin-left:10px;">
					<?php echo $LANG['SORT_BY']; ?>:
					<select name="orderby">
						<option value=""></option>
						<option value="recordedby" <?php echo ($qOrderBy=='recordedby'?'SELECTED':''); ?>><?php echo $LANG['COLLECTOR']; ?></option>
						<option value="recordnumber" <?php echo ($qOrderBy=='recordnumber'?'SELECTED':''); ?>><?php echo $LANG['NUMBER']; ?></option>
						<option value="eventdate" <?php echo ($qOrderBy=='eventdate'?'SELECTED':''); ?>><?php echo $LANG['DATE']; ?></option>
						<option value="catalognumber" <?php echo ($qOrderBy=='catalognumber'?'SELECTED':''); ?>><?php echo $LANG['CAT_NUM']; ?></option>
						<option value="recordenteredby" <?php echo ($qOrderBy=='recordenteredby'?'SELECTED':''); ?>><?php echo $LANG['ENTERED_BY']; ?></option>
						<option value="dateentered" <?php echo ($qOrderBy=='dateentered'?'SELECTED':''); ?>><?php echo $LANG['DATE_ENTERED']; ?></option>
						<option value="datelastmodified" <?php echo ($qOrderBy=='datelastmodified'?'SELECTED':''); ?>><?php echo $LANG['DATE_LAST_MODIFIED']; ?></option>
						<option value="processingstatus" <?php echo ($qOrderBy=='processingstatus'?'SELECTED':''); ?>><?php echo $LANG['PROC_STATUS']; ?></option>
						<option value="sciname" <?php echo ($qOrderBy=='sciname'?'SELECTED':''); ?>><?php echo $LANG['SCI_NAME']; ?></option>
						<option value="family" <?php echo ($qOrderBy=='family'?'SELECTED':''); ?>><?php echo $LANG['FAMILY']; ?></option>
						<option value="country" <?php echo ($qOrderBy=='country'?'SELECTED':''); ?>><?php echo $LANG['COUNTRY']; ?></option>
						<option value="stateprovince" <?php echo ($qOrderBy=='stateprovince'?'SELECTED':''); ?>><?php echo $LANG['STATE_PROVINCE']; ?></option>
						<option value="county" <?php echo ($qOrderBy=='county'?'SELECTED':''); ?>><?php echo $LANG['COUNTY']; ?></option>
						<option value="municipality" <?php echo ($qOrderBy=='municipality'?'SELECTED':''); ?>><?php echo $LANG['MUNICIPALITY']; ?></option>
						<option value="locationid" <?php echo ($qOrderBy=='locationid'?'SELECTED':''); ?>><?php echo $LANG['LOCATION_ID']; ?></option>
						<option value="locality" <?php echo ($qOrderBy=='locality'?'SELECTED':''); ?>><?php echo $LANG['LOCALITY']; ?></option>
						<option value="decimallatitude" <?php echo ($qOrderBy=='decimallatitude'?'SELECTED':''); ?>><?php echo $LANG['DEC_LAT']; ?></option>
						<option value="decimallongitude" <?php echo ($qOrderBy=='decimallongitude'?'SELECTED':''); ?>><?php echo $LANG['DEC_LONG']; ?></option>
						<option value="minimumelevationinmeters" <?php echo ($qOrderBy=='minimumelevationinmeters'?'SELECTED':''); ?>><?php echo $LANG['ELEV_MIN']; ?></option>
						<option value="maximumelevationinmeters" <?php echo ($qOrderBy=='maximumelevationinmeters'?'SELECTED':''); ?>><?php echo $LANG['ELEV_MAX']; ?></option>
					</select>
				</span>
				<span>
					<select name="orderbydir">
						<option value="ASC"><?php echo $LANG['ASCENDING']; ?></option>
						<option value="DESC" <?php echo ($qOrderByDir=='DESC'?'SELECTED':''); ?>><?php echo $LANG['DESCENDING']; ?></option>
					</select>
				</span>
			</div>
		</fieldset>
	</form>
</div>
<script>
	function enteredByCurrentUser(){
		var f = document.queryform;
		resetQueryForm(f);
		f.q_recordenteredby.value = "<?php echo $GLOBALS['USERNAME']?>";
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0');
		f.q_dateentered.value = today.getFullYear()+'-'+mm+'-'+dd;
	}

	function resetQueryForm(f){
		f.occid.value = "";
		f.occidlist.value = "";
		f.direction.value = "";
		f.occindex.value = "0";
		f.q_catalognumber.value = "";
		f.q_othercatalognumbers.value = "";
		f.q_recordedby.value = "";
		f.q_recordnumber.value = "";
		f.q_eventdate.value = "";
		f.q_recordenteredby.value = "";
		f.q_dateentered.value = "";
		f.q_datelastmodified.value = "";
		f.q_processingstatus.value = "";
		if(f.q_exsiccatiid) f.q_exsiccatiid.value = "";
		f.q_customfield1.options[0].selected = true;
		f.q_customtype1.options[0].selected = true;
		f.q_customvalue1.value = "";
		f.q_customfield2.options[0].selected = true;
		f.q_customtype2.options[0].selected = true;
		f.q_customvalue2.value = "";
		f.q_customfield3.options[0].selected = true;
		f.q_customtype3.options[0].selected = true;
		f.q_customvalue3.value = "";
		f.q_imgonly.checked = false;
		f.q_withoutimg.checked = false;
		f.orderby.value = "";
		f.orderbydir.value = "ASC";
	}
</script>