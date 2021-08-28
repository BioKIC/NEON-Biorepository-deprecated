<?php
if(!$displayQuery && array_key_exists('displayquery',$_REQUEST)) $displayQuery = $_REQUEST['displayquery'];

$qryArr = $occManager->getQueryVariables();
// Construct a link containing the queryform search parameters
$queryLink = '?displayquery=1&collid='.$_REQUEST['collid'].'&'.http_build_query($qryArr, '', '&amp;');

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
$qCustomOpenParen1 = (array_key_exists('cop1',$qryArr)?$qryArr['cop1']:'');
$qCustomField1 = (array_key_exists('cf1',$qryArr)?$qryArr['cf1']:'');
$qCustomType1 = (array_key_exists('ct1',$qryArr)?$qryArr['ct1']:'');
$qCustomValue1 = (array_key_exists('cv1',$qryArr)?htmlentities($qryArr['cv1'], ENT_COMPAT, $CHARSET):'');
$qCustomCloseParen1 = (array_key_exists('ccp1',$qryArr)?$qryArr['ccp1']:'');
$qCustomAndOr2 = (array_key_exists('cao2',$qryArr)?$qryArr['cao2']:'');
$qCustomOpenParen2 = (array_key_exists('cop2',$qryArr)?$qryArr['cop2']:'');
$qCustomField2 = (array_key_exists('cf2',$qryArr)?$qryArr['cf2']:'');
$qCustomType2 = (array_key_exists('ct2',$qryArr)?$qryArr['ct2']:'');
$qCustomValue2 = (array_key_exists('cv2',$qryArr)?htmlentities($qryArr['cv2'], ENT_COMPAT, $CHARSET):'');
$qCustomCloseParen2 = (array_key_exists('ccp2',$qryArr)?$qryArr['ccp2']:'');
$qCustomAndOr3 = (array_key_exists('cao3',$qryArr)?$qryArr['cao3']:'');
$qCustomOpenParen3 = (array_key_exists('cop3',$qryArr)?$qryArr['cop3']:'');
$qCustomField3 = (array_key_exists('cf3',$qryArr)?$qryArr['cf3']:'');
$qCustomType3 = (array_key_exists('ct3',$qryArr)?$qryArr['ct3']:'');
$qCustomValue3 = (array_key_exists('cv3',$qryArr)?htmlentities($qryArr['cv3'], ENT_COMPAT, $CHARSET):'');
$qCustomCloseParen3 = (array_key_exists('ccp3',$qryArr)?$qryArr['ccp3']:'');
$qCustomAndOr4 = (array_key_exists('cao4',$qryArr)?$qryArr['cao4']:'');
$qCustomOpenParen4 = (array_key_exists('cop4',$qryArr)?$qryArr['cop4']:'');
$qCustomField4 = (array_key_exists('cf4',$qryArr)?$qryArr['cf4']:'');
$qCustomType4 = (array_key_exists('ct4',$qryArr)?$qryArr['ct4']:'');
$qCustomValue4 = (array_key_exists('cv4',$qryArr)?htmlentities($qryArr['cv4']):'');
$qCustomCloseParen4 = (array_key_exists('ccp4',$qryArr)?$qryArr['ccp4']:'');
$qCustomAndOr5 = (array_key_exists('cao5',$qryArr)?$qryArr['cao5']:'');
$qCustomOpenParen5 = (array_key_exists('cop5',$qryArr)?$qryArr['cop5']:'');
$qCustomField5 = (array_key_exists('cf5',$qryArr)?$qryArr['cf5']:'');
$qCustomType5 = (array_key_exists('ct5',$qryArr)?$qryArr['ct5']:'');
$qCustomValue5 = (array_key_exists('cv5',$qryArr)?htmlentities($qryArr['cv5']):'');
$qCustomCloseParen5 = (array_key_exists('ccp5',$qryArr)?$qryArr['ccp5']:'');
$qCustomAndOr6 = (array_key_exists('cao6',$qryArr)?$qryArr['cao6']:'');
$qCustomOpenParen6 = (array_key_exists('cop6',$qryArr)?$qryArr['cop6']:'');
$qCustomField6 = (array_key_exists('cf6',$qryArr)?$qryArr['cf6']:'');
$qCustomType6 = (array_key_exists('ct6',$qryArr)?$qryArr['ct6']:'');
$qCustomValue6 = (array_key_exists('cv6',$qryArr)?htmlentities($qryArr['cv6']):'');
$qCustomCloseParen6 = (array_key_exists('ccp6',$qryArr)?$qryArr['ccp6']:'');
$qCustomAndOr7 = (array_key_exists('cao7',$qryArr)?$qryArr['cao7']:'');
$qCustomOpenParen7 = (array_key_exists('cop7',$qryArr)?$qryArr['cop7']:'');
$qCustomField7 = (array_key_exists('cf7',$qryArr)?$qryArr['cf7']:'');
$qCustomType7 = (array_key_exists('ct7',$qryArr)?$qryArr['ct7']:'');
$qCustomValue7 = (array_key_exists('cv7',$qryArr)?htmlentities($qryArr['cv7']):'');
$qCustomCloseParen7 = (array_key_exists('ccp7',$qryArr)?$qryArr['ccp7']:'');
$qCustomAndOr8 = (array_key_exists('cao8',$qryArr)?$qryArr['cao8']:'');
$qCustomOpenParen8 = (array_key_exists('cop8',$qryArr)?$qryArr['cop8']:'');
$qCustomField8 = (array_key_exists('cf8',$qryArr)?$qryArr['cf8']:'');
$qCustomType8 = (array_key_exists('ct8',$qryArr)?$qryArr['ct8']:'');
$qCustomValue8 = (array_key_exists('cv8',$qryArr)?htmlentities($qryArr['cv8']):'');
$qCustomCloseParen8 = (array_key_exists('ccp8',$qryArr)?$qryArr['ccp8']:'');
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
			<legend>Record Search Form</legend>
			<?php
			if(!$crowdSourceMode){
				?>
				<div class="fieldGroupDiv">
					<div class="fieldDiv" title="Full name of collector as entered in database. To search just on last name, place the wildcard character (%) before name (%Gentry).">
						Collector:
						<input type="text" name="q_recordedby" value="<?php echo $qRecordedBy; ?>" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="Separate multiple terms by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
						Number:
						<input type="text" name="q_recordnumber" value="<?php echo $qRecordNumber; ?>" style="width:120px;" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="Enter ranges separated by ' - ' (space before and after dash required), e.g.: 2002-01-01 - 2003-01-01. Dates can also be specified with < or > signs, e.g.: >2021-01-01">
						Date:
						<input type="text" name="q_eventdate" value="<?php echo $qEventDate; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<?php
			}
			?>
			<div class="fieldGroupDiv">
				<div class="fieldDiv" title="Separate multiples by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
					Catalog Number:
					<input type="text" name="q_catalognumber" value="<?php echo $qCatalogNumber; ?>" onchange="setOrderBy(this)" />
				</div>
				<?php
				if($crowdSourceMode){
					?>
					<div class="fieldDiv" title="Search for term embedded within OCR block of text">
						OCR Fragment:
						<input type="text" name="q_ocrfrag" value="<?php echo $qOcrFrag; ?>" style="width:200px;" />
					</div>
					<?php
				}
				else{
					?>
					<div class="fieldDiv" title="Separate multiples by comma and ranges by ' - ' (space before and after dash required), e.g.: 3542,3602,3700 - 3750">
						Other Catalog Numbers:
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
						Entered by:
						<input type="text" name="q_recordenteredby" value="<?php echo $qRecordEnteredBy; ?>" style="width:70px;" onchange="setOrderBy(this)" />
						<button type="button" onclick="enteredByCurrentUser()" style="font-size:70%" title="Limit to recent records entered by current user">CU</button>
					</div>
					<div class="fieldDiv" title="Enter ranges separated by ' - ' (space before and after dash required), e.g.: 2002-01-01 - 2003-01-01. Dates can also be specified with < or > signs, e.g.: >2021-01-01">
						Date entered:
						<input type="text" name="q_dateentered" value="<?php echo $qDateEntered; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
					<div class="fieldDiv" title="Enter ranges separated by ' - ' (space before and after dash required), e.g.: 2002-01-01 - 2003-01-01. Dates can also be specified with < or > signs, e.g.: >2021-01-01">
						Date modified:
						<input type="text" name="q_datelastmodified" value="<?php echo $qDateLastModified; ?>" style="width:160px" onchange="setOrderBy(this)" />
					</div>
				</div>
				<div class="fieldGroupDiv">
					<div class="fieldDiv">
						Processing Status:
						<select name="q_processingstatus" onchange="setOrderBy(this)">
							<option value=''>All Records</option>
							<option>-------------------</option>
							<?php
							foreach($processingStatusArr as $v){
								//Don't display these options is editor is crowd sourced
								$keyOut = strtolower($v);
								echo '<option value="'.$keyOut.'" '.($qProcessingStatus==$keyOut?'SELECTED':'').'>'.ucwords($v).'</option>';
							}
							echo '<option value="isnull" '.($qProcessingStatus=='isnull'?'SELECTED':'').'>No Set Status</option>';
							if($qProcessingStatus && $qProcessingStatus != 'isnull' && !in_array($qProcessingStatus,$processingStatusArr)){
								echo '<option value="'.$qProcessingStatus.'" SELECTED>'.$qProcessingStatus.'</option>';
							}
							?>
						</select>
					</div>
					<div class="fieldDiv">
						<input name="q_imgonly" type="checkbox" value="1" <?php echo ($qImgOnly==1?'checked':''); ?> onchange="this.form.q_withoutimg.checked = false;" />
						with images
					</div>
					<div class="fieldDiv">
						<input name="q_withoutimg" type="checkbox" value="1" <?php echo ($qWithoutImg==1?'checked':''); ?> onchange="this.form.q_imgonly.checked = false;" />
						without images
					</div>
				</div>
				<?php
				if($ACTIVATE_EXSICCATI){
					if($exsList = $occManager->getExsiccatiList()){
						?>
						<div class="fieldGroupDiv" title="Enter Exsiccati Title">
							<div class="fieldDiv">
								Exsiccati Title:
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
				$advFieldArr = array('family'=>'Family','sciname'=>'Scientific Name','othercatalognumbers'=>'Other Catalog Numbers',
					'country'=>'Country','stateProvince'=>'State/Province','county'=>'County','municipality'=>'Municipality',
					'recordedby'=>'Collector','recordnumber'=>'Collector Number','eventdate'=>'Collection Date');
			}
			else{
				$advFieldArr = array('associatedCollectors'=>'Associated Collectors','associatedOccurrences'=>'Associated Occurrences',
					'associatedTaxa'=>'Associated Taxa','attributes'=>'Attributes','scientificNameAuthorship'=>'Author',
					'basisOfRecord'=>'Basis Of Record','behavior'=>'Behavior','catalogNumber'=>'Catalog Number','collectionCode'=>'Collection Code (override)','recordNumber'=>'Collection Number',
					'recordedBy'=>'Collector/Observer','coordinateUncertaintyInMeters'=>'Coordinate Uncertainty (m)','country'=>'Country',
					'county'=>'County','cultivationStatus'=>'Cultivation Status','dataGeneralizations'=>'Data Generalizations','eventDate'=>'Date',
					'dateEntered'=>'Date Entered','dateLastModified'=>'Date Last Modified','dbpk'=>'dbpk','decimalLatitude'=>'Decimal Latitude',
					'decimalLongitude'=>'Decimal Longitude','maximumDepthInMeters'=>'Depth Maximum (m)','minimumDepthInMeters'=>'Depth Minimum (m)',
					'verbatimAttributes'=>'Description','disposition'=>'Disposition','dynamicProperties'=>'Dynamic Properties',
					'maximumElevationInMeters'=>'Elevation Maximum (m)','minimumElevationInMeters'=>'Elevation Minimum (m)',
					'establishmentMeans'=>'Establishment Means','family'=>'Family','fieldNotes'=>'Field Notes','fieldnumber'=>'Field Number',
					'geodeticDatum'=>'Geodetic Datum','georeferenceProtocol'=>'Georeference Protocol',
					'georeferenceRemarks'=>'Georeference Remarks','georeferenceSources'=>'Georeference Sources',
					'georeferenceVerificationStatus'=>'Georeference Verification Status','georeferencedBy'=>'Georeferenced By','habitat'=>'Habitat',
					'identificationQualifier'=>'Identification Qualifier','identificationReferences'=>'Identification References',
					'identificationRemarks'=>'Identification Remarks','identifiedBy'=>'Identified By','individualCount'=>'Individual Count',
					'informationWithheld'=>'Information Withheld','institutionCode'=>'Institution Code (override)','labelProject'=>'Project',
					'language'=>'Language','lifeStage'=>'Life Stage','locationid'=>'Location ID','locality'=>'Locality',
					'localitySecurity'=>'Locality Security','localitySecurityReason'=>'Locality Security Reason','locationRemarks'=>'Location Remarks',
					'username'=>'Modified By','municipality'=>'Municipality','occurrenceRemarks'=>'Notes (Occurrence Remarks)','ocrFragment'=>'OCR Fragment',
					'otherCatalogNumbers'=>'Other Catalog Numbers','ownerInstitutionCode'=>'Owner Code','preparations'=>'Preparations',
					'reproductiveCondition'=>'Reproductive Condition','samplingEffort'=>'Sampling Effort','samplingProtocol'=>'Sampling Protocol',
					'sciname'=>'Scientific Name','sex'=>'Sex','stateProvince'=>'State/Province',
					'substrate'=>'Substrate','taxonRemarks'=>'Taxon Remarks','typeStatus'=>'Type Status','verbatimCoordinates'=>'Verbatim Coordinates',
					'verbatimEventDate'=>'Verbatim Date','verbatimDepth'=>'Verbatim Depth','verbatimElevation'=>'Verbatim Elevation');
			}
			//sort($advFieldArr);
			?>
			<div class="fieldGroupDiv">
				Custom Field 1:
				<select name="q_customopenparen1" onchange="customSelectChanged(1)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen1=='('?'SELECTED':''); ?> value="(">(</option>
                    <option <?php echo ($qCustomOpenParen1=='(('?'SELECTED':''); ?> value="((">((</option>
                    <option <?php echo ($qCustomOpenParen1=='((('?'SELECTED':''); ?> value="(((">(((</option>
                </select>
				<select name="q_customfield1" onchange="customSelectChanged(1)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField1?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype1">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType1=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType1=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType1=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType1=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType1=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType1=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType1=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType1=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue1" type="text" value="<?php echo $qCustomValue1; ?>" style="width:200px;" />
				<select name="q_customcloseparen1" onchange="customSelectChanged(1)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen1==')'?'SELECTED':''); ?> value=")">)</option>
                </select>
				<a href="#" onclick="toggleCustomDiv2();return false;">
					<img src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv2" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue2||$qCustomType2=='NULL'||$qCustomType2=='NOTNULL'?'block':'none');?>;">
				Custom Field 2:
				 <select name="q_customandor2" onchange="customSelectChanged(2)">
                    <option>AND</option>
                    <option <?php echo ($qCustomAndOr2=='OR'?'SELECTED':''); ?> value="OR">OR</option>
                </select>
                <select name="q_customopenparen2" onchange="customSelectChanged(2)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen2=='('?'SELECTED':''); ?> value="(">(</option>
                  	<option <?php echo ($qCustomOpenParen2=='(('?'SELECTED':''); ?> value="((">((</option>
                    <option <?php echo ($qCustomOpenParen2=='((('?'SELECTED':''); ?> value="(((">(((</option>
                </select>
				<select name="q_customfield2" onchange="customSelectChanged(2)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField2?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype2">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType2=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType2=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType2=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType2=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType2=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType2=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType2=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType2=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue2" type="text" value="<?php echo $qCustomValue2; ?>" style="width:200px;" />
				<select name="q_customcloseparen2" onchange="customSelectChanged(2)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen2==')'?'SELECTED':''); ?> value=")">)</option>
                    <option <?php echo ($qCustomCloseParen2=='))'?'SELECTED':''); ?> value="))">))</option>
                </select>
				<a href="#" onclick="toggleCustomDiv3();return false;">
					<img src="../../images/editplus.png" />
				</a>
			</div>
			<div id="customdiv3" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue3||$qCustomType3=='NULL'||$qCustomType3=='NOTNULL'?'block':'none');?>;">
				Custom Field 3:
				<select name="q_customandor3" onchange="customSelectChanged(3)">
                    <option>AND</option>
                    <option <?php echo ($qCustomAndOr3=='OR'?'SELECTED':''); ?> value="OR">OR</option>
                </select>
                <select name="q_customopenparen3" onchange="customSelectChanged(3)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen3=='('?'SELECTED':''); ?> value="(">(</option>
                    <option <?php echo ($qCustomOpenParen3=='(('?'SELECTED':''); ?> value="((">((</option>
                    <option <?php echo ($qCustomOpenParen3=='((('?'SELECTED':''); ?> value="(((">(((</option>
                </select>
				<select name="q_customfield3" onchange="customSelectChanged(3)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField3?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype3">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType3=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType3=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType3=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType3=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType3=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType3=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType3=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType3=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue3" type="text" value="<?php echo $qCustomValue3; ?>" style="width:200px;" />
				<select name="q_customcloseparen3" onchange="customSelectChanged(3)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen3==')'?'SELECTED':''); ?> value=")">)</option>
                    <option <?php echo ($qCustomCloseParen3=='))'?'SELECTED':''); ?> value="))">))</option>
                    <option <?php echo ($qCustomCloseParen3==')))'?'SELECTED':''); ?> value=")))">)))</option>
                </select>
                <a href="#" onclick="toggleCustomDiv4();return false;">
                    <img src="../../images/editplus.png" />
                </a>
			</div>
			<div id="customdiv4" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue4||$qCustomType4=='NULL'||$qCustomType4=='NOTNULL'?'block':'none');?>;">
				Custom Field 4:
				<select name="q_customandor4" onchange="customSelectChanged(4)">
                    <option>AND</option>
                    <option <?php echo ($qCustomAndOr4=='OR'?'SELECTED':''); ?> value="OR">OR</option>
                </select>
                <select name="q_customopenparen4" onchange="customSelectChanged(4)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen4=='('?'SELECTED':''); ?> value="(">(</option>
                    <option <?php echo ($qCustomOpenParen4=='(('?'SELECTED':''); ?> value="((">((</option>
                    <option <?php echo ($qCustomOpenParen4=='((('?'SELECTED':''); ?> value="(((">(((</option>
                </select>
				<select name="q_customfield4" onchange="customSelectChanged(4)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField4?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype4">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType4=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType4=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType4=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType4=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType4=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType4=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType4=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType4=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue4" type="text" value="<?php echo $qCustomValue4; ?>" style="width:200px;" />
				<select name="q_customcloseparen4" onchange="customSelectChanged(4)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen4==')'?'SELECTED':''); ?> value=")">)</option>
                    <option <?php echo ($qCustomCloseParen4=='))'?'SELECTED':''); ?> value="))">))</option>
                    <option <?php echo ($qCustomCloseParen4==')))'?'SELECTED':''); ?> value=")))">)))</option>
                </select>
                <a href="#" onclick="toggleCustomDiv5();return false;">
                    <img src="../../images/editplus.png" />
                </a>
			</div>
			<div id="customdiv5" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue5||$qCustomType5=='NULL'||$qCustomType5=='NOTNULL'?'block':'none');?>;">
				Custom Field 5:
				<select name="q_customandor5" onchange="customSelectChanged(5)">
                    <option>AND</option>
                    <option <?php echo ($qCustomAndOr5=='OR'?'SELECTED':''); ?> value="OR">OR</option>
                </select>
                <select name="q_customopenparen5" onchange="customSelectChanged(5)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen5=='('?'SELECTED':''); ?> value="(">(</option>
                    <option <?php echo ($qCustomOpenParen5=='(('?'SELECTED':''); ?> value="((">((</option>
                    <option <?php echo ($qCustomOpenParen5=='((('?'SELECTED':''); ?> value="(((">(((</option>
                </select>
				<select name="q_customfield5" onchange="customSelectChanged(5)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField5?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype5">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType5=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType5=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType5=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType5=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType5=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType5=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType5=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType5=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue5" type="text" value="<?php echo $qCustomValue5; ?>" style="width:200px;" />
				<select name="q_customcloseparen5" onchange="customSelectChanged(5)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen5==')'?'SELECTED':''); ?> value=")">)</option>
                    <option <?php echo ($qCustomCloseParen5=='))'?'SELECTED':''); ?> value="))">))</option>
                    <option <?php echo ($qCustomCloseParen5==')))'?'SELECTED':''); ?> value=")))">)))</option>
                </select>
                <a href="#" onclick="toggleCustomDiv6();return false;">
                    <img src="../../images/editplus.png" />
                </a>
			</div>
			<div id="customdiv6" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue6||$qCustomType6=='NULL'||$qCustomType6=='NOTNULL'?'block':'none');?>;">
				Custom Field 6:
				<select name="q_customandor6" onchange="customSelectChanged(6)">
                    <option>AND</option>
                    <option <?php echo ($qCustomAndOr6=='OR'?'SELECTED':''); ?> value="OR">OR</option>
                </select>
                <select name="q_customopenparen6" onchange="customSelectChanged(6)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen6=='('?'SELECTED':''); ?> value="(">(</option>
                    <option <?php echo ($qCustomOpenParen6=='(('?'SELECTED':''); ?> value="((">((</option>
                    <option <?php echo ($qCustomOpenParen6=='((('?'SELECTED':''); ?> value="(((">(((</option>
                </select>
				<select name="q_customfield6" onchange="customSelectChanged(6)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField6?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype6">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType6=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType6=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType6=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType6=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType6=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType6=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType6=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType6=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue6" type="text" value="<?php echo $qCustomValue6; ?>" style="width:200px;" />
				<select name="q_customcloseparen6" onchange="customSelectChanged(6)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen6==')'?'SELECTED':''); ?> value=")">)</option>
                    <option <?php echo ($qCustomCloseParen6=='))'?'SELECTED':''); ?> value="))">))</option>
                    <option <?php echo ($qCustomCloseParen6==')))'?'SELECTED':''); ?> value=")))">)))</option>
                </select>
                <a href="#" onclick="toggleCustomDiv7();return false;">
                    <img src="../../images/editplus.png" />
                </a>
			</div>
			<div id="customdiv7" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue7||$qCustomType7=='NULL'||$qCustomType7=='NOTNULL'?'block':'none');?>;">
				Custom Field 7:
				<select name="q_customandor7" onchange="customSelectChanged(7)">
                    <option>AND</option>
                    <option <?php echo ($qCustomAndOr7=='OR'?'SELECTED':''); ?> value="OR">OR</option>
                </select>
                <select name="q_customopenparen7" onchange="customSelectChanged(7)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen7=='('?'SELECTED':''); ?> value="(">(</option>
                    <option <?php echo ($qCustomOpenParen7=='(('?'SELECTED':''); ?> value="((">((</option>
                </select>
				<select name="q_customfield7" onchange="customSelectChanged(7)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField7?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype7">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType7=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType7=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType7=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType7=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType7=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType7=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType7=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType7=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue7" type="text" value="<?php echo $qCustomValue7; ?>" style="width:200px;" />
				<select name="q_customcloseparen7" onchange="customSelectChanged(7)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen7==')'?'SELECTED':''); ?> value=")">)</option>
                    <option <?php echo ($qCustomCloseParen7=='))'?'SELECTED':''); ?> value="))">))</option>
                    <option <?php echo ($qCustomCloseParen7==')))'?'SELECTED':''); ?> value=")))">)))</option>
                </select>
                <a href="#" onclick="toggleCustomDiv8();return false;">
                    <img src="../../images/editplus.png" />
                </a>
			</div>
			<div id="customdiv8" class="fieldGroupDiv" style="display:<?php echo ($qCustomValue8||$qCustomType8=='NULL'||$qCustomType8=='NOTNULL'?'block':'none');?>;">
				Custom Field 8:
				<select name="q_customandor8" onchange="customSelectChanged(8)">
                    <option>AND</option>
                    <option <?php echo ($qCustomAndOr8=='OR'?'SELECTED':''); ?> value="OR">OR</option>
                </select>
                <select name="q_customopenparen8" onchange="customSelectChanged(8)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomOpenParen8=='('?'SELECTED':''); ?> value="(">(</option>
                </select>
				<select name="q_customfield8" onchange="customSelectChanged(8)">
					<option value="">Select Field Name</option>
					<option value="">---------------------------------</option>
					<?php
					foreach($advFieldArr as $k => $v){
						echo '<option value="'.$k.'" '.($k==$qCustomField8?'SELECTED':'').'>'.$v.'</option>';
					}
					?>
				</select>
				<select name="q_customtype8">
					<option>EQUALS</option>
					<option <?php echo ($qCustomType8=='NOT EQUALS'?'SELECTED':''); ?> value="NOT EQUALS">NOT EQUALS</option>
					<option <?php echo ($qCustomType8=='STARTS'?'SELECTED':''); ?> value="STARTS">STARTS WITH</option>
					<option <?php echo ($qCustomType8=='LIKE'?'SELECTED':''); ?> value="LIKE">CONTAINS</option>
					<option <?php echo ($qCustomType8=='NOT LIKE'?'SELECTED':''); ?> value="NOT LIKE">DOESN'T CONTAIN</option>
					<option <?php echo ($qCustomType8=='GREATER'?'SELECTED':''); ?> value="GREATER">GREATER THAN</option>
					<option <?php echo ($qCustomType8=='LESS'?'SELECTED':''); ?> value="LESS">LESS THAN</option>
					<option <?php echo ($qCustomType8=='NULL'?'SELECTED':''); ?> value="NULL">IS NULL</option>
					<option <?php echo ($qCustomType8=='NOTNULL'?'SELECTED':''); ?> value="NOTNULL">IS NOT NULL</option>
				</select>
				<input name="q_customvalue8" type="text" value="<?php echo $qCustomValue8; ?>" style="width:200px;" />
				<select name="q_customcloseparen8" onchange="customSelectChanged(8)">
                    <option value="">---</option>
                    <option <?php echo ($qCustomCloseParen8==')'?'SELECTED':''); ?> value=")">)</option>
                    <option <?php echo ($qCustomCloseParen8=='))'?'SELECTED':''); ?> value="))">))</option>
                    <option <?php echo ($qCustomCloseParen8==')))'?'SELECTED':''); ?> value=")))">)))</option>
                </select>
			</div>
			<div class="fieldGroupDiv">
				<?php
				if($isGenObs && ($IS_ADMIN || ($collId && array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collId,$USER_RIGHTS["CollAdmin"])))){
					?>
					<div class="fieldDiv">
						<input type="checkbox" name="q_returnall" value="1" <?php echo ($qReturnAll?'CHECKED':''); ?> /> Show records for all users (admin control)
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
						<div style="float:right;margin-top:10px;" title="Go to Label Printing Module">
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
				<input type="button" name="submitaction" value="Display Editor" onclick="submitQueryEditor(this.form)" />
				<input type="button" name="submitaction" value="Display Table" onclick="submitQueryTable(this.form)" />
				<span style="margin-left:10px;">
					<input type="button" name="reset" value="Reset Form" onclick="resetQueryForm(this.form)" />
				</span>
				<span style="margin-left:10px;">
					Sort by:
					<select name="orderby">
						<option value=""></option>
						<option value="recordedby" <?php echo ($qOrderBy=='recordedby'?'SELECTED':''); ?>>Collector</option>
						<option value="recordnumber" <?php echo ($qOrderBy=='recordnumber'?'SELECTED':''); ?>>Number</option>
						<option value="eventdate" <?php echo ($qOrderBy=='eventdate'?'SELECTED':''); ?>>Date</option>
						<option value="catalognumber" <?php echo ($qOrderBy=='catalognumber'?'SELECTED':''); ?>>Catalog Number</option>
						<option value="recordenteredby" <?php echo ($qOrderBy=='recordenteredby'?'SELECTED':''); ?>>Entered By</option>
						<option value="dateentered" <?php echo ($qOrderBy=='dateentered'?'SELECTED':''); ?>>Date Entered</option>
						<option value="datelastmodified" <?php echo ($qOrderBy=='datelastmodified'?'SELECTED':''); ?>>Date Last modified</option>
						<option value="processingstatus" <?php echo ($qOrderBy=='processingstatus'?'SELECTED':''); ?>>Processing Status</option>
						<option value="sciname" <?php echo ($qOrderBy=='sciname'?'SELECTED':''); ?>>Scientific Name</option>
						<option value="family" <?php echo ($qOrderBy=='family'?'SELECTED':''); ?>>Family</option>
						<option value="country" <?php echo ($qOrderBy=='country'?'SELECTED':''); ?>>Country</option>
						<option value="stateprovince" <?php echo ($qOrderBy=='stateprovince'?'SELECTED':''); ?>>State / Province</option>
						<option value="county" <?php echo ($qOrderBy=='county'?'SELECTED':''); ?>>County</option>
						<option value="municipality" <?php echo ($qOrderBy=='municipality'?'SELECTED':''); ?>>Municipality</option>
						<option value="locationid" <?php echo ($qOrderBy=='locationid'?'SELECTED':''); ?>>Location ID</option>
						<option value="locality" <?php echo ($qOrderBy=='locality'?'SELECTED':''); ?>>Locality</option>
						<option value="decimallatitude" <?php echo ($qOrderBy=='decimallatitude'?'SELECTED':''); ?>>Decimal Latitude</option>
						<option value="decimallongitude" <?php echo ($qOrderBy=='decimallongitude'?'SELECTED':''); ?>>Decimal Longitude</option>
						<option value="minimumelevationinmeters" <?php echo ($qOrderBy=='minimumelevationinmeters'?'SELECTED':''); ?>>Elevation Minimum</option>
						<option value="maximumelevationinmeters" <?php echo ($qOrderBy=='maximumelevationinmeters'?'SELECTED':''); ?>>Elevation Maximum</option>
					</select>
				</span>
				<span>
					<select name="orderbydir">
						<option value="ASC">ascending</option>
						<option value="DESC" <?php echo ($qOrderByDir=='DESC'?'SELECTED':''); ?>>descending</option>
					</select>
				</span>
				<input type="button" name="copylink" value="Copy Search As Link" onclick="copyQueryLink(event)">
			</div>
		</fieldset>
	</form>
</div>
<script>

	// Function to copy the query link to the clipboard
	function copyQueryLink(evt){
		
		// Prevent the button from triggering and reloading the page
		evt.preventDefault();

		// Get the queryform parameters, only the ones that are set
		var params = $('form[name="queryform"] :input').filter(function () { return $(this).val() != ""; }).serialize();

		// Check if the catalogNumber field is set, and if not, add it to the query
		var catalogNumber = $('input[name="q_catalognumber"]').val() == "" ? '&q_catalognumber=' : '';

		// Construct the full link to the query form search parameters. Add displayquery to show the query form
		var link = location.protocol + '//' + location.host + location.pathname + '?' + params + catalogNumber + '&displayquery=1';

		// Copy to clipboard
		navigator.clipboard.writeText(link).then(() => {
	      /* clipboard succcessfully set */
	      //console.log("Clipboard copy successful");
	    }, () => {
	      /* clipboard write failed */
	      //console.log("Clipboard copy failed");
	    });
	}

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

		// Reset all of the custom fields
		f.q_customopenparen1.options[0].selected = true;
		f.q_customfield1.options[0].selected = true;
		f.q_customtype1.options[0].selected = true;
		f.q_customvalue1.value = "";
		f.q_customcloseparen1.options[0].selected = true;

		f.q_customandor2.options[0].selected = true;
		f.q_customopenparen2.options[0].selected = true;
		f.q_customfield2.options[0].selected = true;
		f.q_customtype2.options[0].selected = true;
		f.q_customvalue2.value = "";
		f.q_customcloseparen2.options[0].selected = true;

		f.q_customandor3.options[0].selected = true;
		f.q_customopenparen3.options[0].selected = true;
		f.q_customfield3.options[0].selected = true;
		f.q_customtype3.options[0].selected = true;
		f.q_customvalue3.value = "";
		f.q_customcloseparen3.options[0].selected = true;

		f.q_customandor4.options[0].selected = true;
		f.q_customopenparen4.options[0].selected = true;
		f.q_customfield4.options[0].selected = true;
		f.q_customtype4.options[0].selected = true;
		f.q_customvalue4.value = "";
		f.q_customcloseparen4.options[0].selected = true;

		f.q_customandor5.options[0].selected = true;
		f.q_customopenparen5.options[0].selected = true;
		f.q_customfield5.options[0].selected = true;
		f.q_customtype5.options[0].selected = true;
		f.q_customvalue5.value = "";
		f.q_customcloseparen5.options[0].selected = true;

		f.q_customandor6.options[0].selected = true;
		f.q_customopenparen6.options[0].selected = true;
		f.q_customfield6.options[0].selected = true;
		f.q_customtype6.options[0].selected = true;
		f.q_customvalue6.value = "";
		f.q_customcloseparen6.options[0].selected = true;

		f.q_customandor7.options[0].selected = true;
		f.q_customopenparen7.options[0].selected = true;
		f.q_customfield7.options[0].selected = true;
		f.q_customtype7.options[0].selected = true;
		f.q_customvalue7.value = "";
		f.q_customcloseparen7.options[0].selected = true;

		f.q_customandor8.options[0].selected = true;
		f.q_customopenparen8.options[0].selected = true;
		f.q_customfield8.options[0].selected = true;
		f.q_customtype8.options[0].selected = true;
		f.q_customvalue8.value = "";
		f.q_customcloseparen8.options[0].selected = true;

		f.q_imgonly.checked = false;
		f.q_withoutimg.checked = false;
		f.orderby.value = "";
		f.orderbydir.value = "ASC";

		// Hide all the custom field divs except the first on reset
		document.getElementById('customdiv2').style.display = "none";
		document.getElementById('customdiv3').style.display = "none";
		document.getElementById('customdiv4').style.display = "none";
		document.getElementById('customdiv5').style.display = "none";
		document.getElementById('customdiv6').style.display = "none";
		document.getElementById('customdiv7').style.display = "none";
		document.getElementById('customdiv8').style.display = "none";
	}
</script>