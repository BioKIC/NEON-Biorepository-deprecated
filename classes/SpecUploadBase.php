<?php
include_once($SERVER_ROOT.'/classes/SpecUpload.php');
include_once($SERVER_ROOT.'/classes/OccurrenceMaintenance.php');
include_once($SERVER_ROOT.'/classes/OccurrenceUtilities.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');
include_once($SERVER_ROOT.'/classes/Encoding.php');

class SpecUploadBase extends SpecUpload{

	protected $transferCount = 0;
	protected $identTransferCount = 0;
	protected $imageTransferCount = 0;
	protected $includeIdentificationHistory = true;
	protected $includeImages = true;
	private $observerUid;
	private $matchCatalogNumber = 1;
	private $matchOtherCatalogNumbers = 0;
	private $verifyImageUrls = false;
	private $processingStatus = '';
	protected $nfnIdentifier;
	protected $uploadTargetPath;

	protected $sourceArr = Array();
	protected $identSourceArr = Array();
	protected $imageSourceArr = Array();
	protected $fieldMap = Array();
	protected $identFieldMap = Array();
	protected $imageFieldMap = Array();
	protected $symbFields = array();
	protected $identSymbFields = array();
	protected $imageSymbFields = array();
	protected $filterArr = array();

	private $sourceCharset;
	private $targetCharset = 'UTF-8';
	private $imgFormatDefault = '';
	private $sourceDatabaseType = '';
	private $dbpkCnt = 0;

	function __construct() {
		parent::__construct();
		set_time_limit(7200);
		ini_set('max_input_time',600);
		ini_set('default_socket_timeout', 6000);
		ini_set('memory_limit','512M');
		if(isset($GLOBALS['CHARSET']) && $GLOBALS['CHARSET']){
			$this->targetCharset = strtoupper($GLOBALS['CHARSET']);
			if($this->targetCharset == 'UTF8') $this->targetCharset == 'UTF-8';
		}
	}

	function __destruct(){
		parent::__destruct();
	}

	public function setFieldMap($fm){
		$this->fieldMap = $fm;
	}

	public function getFieldMap(){
		return $this->fieldMap;
	}

	public function setIdentFieldMap($fm){
		$this->identFieldMap = $fm;
	}

	public function setImageFieldMap($fm){
		$this->imageFieldMap = $fm;
	}

	public function getDbpk(){
		$dbpk = '';
		if(array_key_exists('dbpk',$this->fieldMap)) $dbpk = strtolower($this->fieldMap['dbpk']['field']);
		return $dbpk;
	}

	public function loadFieldMap($autoBuildFieldMap = false){
		//Get Field Map for $fieldMap
		if($this->uspid && !$this->fieldMap){
			switch ($this->uploadType) {
				case $this->FILEUPLOAD:
				case $this->SKELETAL:
				case $this->DWCAUPLOAD:
				case $this->IPTUPLOAD:
				case $this->DIRECTUPLOAD:
				case $this->SCRIPTUPLOAD:
					$sql = 'SELECT sourcefield, symbspecfield FROM uploadspecmap WHERE (uspid = '.$this->uspid.')';
					$rs = $this->conn->query($sql);
					while($row = $rs->fetch_object()){
						$symbFieldPrefix = substr($row->symbspecfield,0,3);
						$symbFieldName = substr($row->symbspecfield,3);
						if($symbFieldPrefix == 'ID-'){
							$this->identFieldMap[$symbFieldName]["field"] = $row->sourcefield;
						}
						elseif($symbFieldPrefix == 'IM-'){
							$this->imageFieldMap[$symbFieldName]["field"] = $row->sourcefield;
						}
						else{
							$this->fieldMap[$row->symbspecfield]["field"] = $row->sourcefield;
						}
					}
					$rs->free();
			}
		}

		//Get uploadspectemp metadata
		$this->setSkipOccurFieldArr();
		if($this->uploadType == $this->RESTOREBACKUP){
			unset($this->skipOccurFieldArr);
			$this->skipOccurFieldArr = array();
		}
		//Other to deal with/skip later: 'ownerinstitutioncode'
		$sql = "SHOW COLUMNS FROM uploadspectemp";
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if(!in_array($field,$this->skipOccurFieldArr)){
				if($autoBuildFieldMap){
					$this->fieldMap[$field]["field"] = $field;
				}
				$type = $row->Type;
				$this->symbFields[] = $field;
				if(array_key_exists($field,$this->fieldMap)){
					if(strpos($type,"double") !== false || strpos($type,"int") !== false){
						$this->fieldMap[$field]["type"] = "numeric";
					}
					elseif(strpos($type,"decimal") !== false){
						$this->fieldMap[$field]["type"] = "decimal";
						if(preg_match('/\((.*)\)$/', $type, $matches)){
							$this->fieldMap[$field]["size"] = $matches[1];
						}
					}
					elseif(strpos($type,"date") !== false){
						$this->fieldMap[$field]["type"] = "date";
					}
					else{
						$this->fieldMap[$field]["type"] = "string";
						if(preg_match('/\((\d+)\)$/', $type, $matches)){
							$this->fieldMap[$field]["size"] = substr($matches[0],1,strlen($matches[0])-2);
						}
					}
				}
			}
		}
		$rs->free();
		//Add additional fields that are used for mapping to other fields just before record is imported into uploadspectemp
		$this->symbFields[] = 'coordinateuncertaintyradius';
		$this->symbFields[] = 'coordinateuncertaintyunits';
		$this->symbFields[] = 'authorspecies';
		$this->symbFields[] = 'authorinfraspecific';
		sort($this->symbFields);
		if($this->paleoSupport) $this->symbFields = array_merge($this->symbFields,$this->getPaleoTerms());
		if($this->materialSampleSupport) $this->symbFields = array_merge($this->symbFields,$this->getMaterialSampleTerms());
		//Specify fields
		$this->symbFields[] = 'specify:subspecies';
		$this->symbFields[] = 'specify:subspecies_author';
		$this->symbFields[] = 'specify:variety';
		$this->symbFields[] = 'specify:variety_author';
		$this->symbFields[] = 'specify:forma';
		$this->symbFields[] = 'specify:forma_author';
		$this->symbFields[] = 'specify:collector_first_name';
		$this->symbFields[] = 'specify:collector_middle_initial';
		$this->symbFields[] = 'specify:collector_last_name';
		$this->symbFields[] = 'specify:determiner_first_name';
		$this->symbFields[] = 'specify:determiner_middle_initial';
		$this->symbFields[] = 'specify:determiner_last_name';
		$this->symbFields[] = 'specify:qualifier_position';
		$this->symbFields[] = 'specify:latitude1';
		$this->symbFields[] = 'specify:latitude2';
		$this->symbFields[] = 'specify:longitude1';
		$this->symbFields[] = 'specify:longitude2';
		$this->symbFields[] = 'specify:land_ownership';
		$this->symbFields[] = 'specify:topo_quad';
		$this->symbFields[] = 'specify:georeferenced_by_first_name';
		$this->symbFields[] = 'specify:georeferenced_by_middle_initial';
		$this->symbFields[] = 'specify:georeferenced_by_last_name';
		$this->symbFields[] = 'specify:locality_continued';
		$this->symbFields[] = 'specify:georeferenced_date';
		$this->symbFields[] = 'specify:elevation_(ft)';
		$this->symbFields[] = 'specify:preparer_first_name';
		$this->symbFields[] = 'specify:preparer_middle_initial';
		$this->symbFields[] = 'specify:preparer_last_name';
		$this->symbFields[] = 'specify:prepared_by_date';
		$this->symbFields[] = 'specify:cataloger_first_name';
		$this->symbFields[] = 'specify:cataloger_middle_initial';
		$this->symbFields[] = 'specify:cataloger_last_name';
		$this->symbFields[] = 'specify:cataloged_date';

		switch ($this->uploadType) {
			case $this->FILEUPLOAD:
			case $this->SKELETAL:
			case $this->DWCAUPLOAD:
			case $this->IPTUPLOAD:
			case $this->RESTOREBACKUP:
			case $this->DIRECTUPLOAD:
				//Get identification metadata
				$skipDetFields = array('detid','occid','tidinterpreted','idbyid','appliedstatus','sortsequence','initialtimestamp');
				if($this->uploadType == $this->RESTOREBACKUP){
					unset($skipDetFields);
					$skipDetFields = array();
				}
				$rs = $this->conn->query('SHOW COLUMNS FROM uploaddetermtemp');
				while($r = $rs->fetch_object()){
					$field = strtolower($r->Field);
					if(!in_array($field,$skipDetFields)){
						if($autoBuildFieldMap){
							$this->identFieldMap[$field]["field"] = $field;
						}
						$type = $r->Type;
						$this->identSymbFields[] = $field;
						if(array_key_exists($field,$this->identFieldMap)){
							if(strpos($type,"double") !== false || strpos($type,"int") !== false || strpos($type,"decimal") !== false){
								$this->identFieldMap[$field]["type"] = "numeric";
							}
							elseif(strpos($type,"date") !== false){
								$this->identFieldMap[$field]["type"] = "date";
							}
							else{
								$this->identFieldMap[$field]["type"] = "string";
								if(preg_match('/\(\d+\)$/', $type, $matches)){
									$this->identFieldMap[$field]["size"] = substr($matches[0],1,strlen($matches[0])-2);
								}
							}
						}
					}
				}
				$rs->free();

				$this->identSymbFields[] = 'genus';
				$this->identSymbFields[] = 'specificepithet';
				$this->identSymbFields[] = 'taxonrank';
				$this->identSymbFields[] = 'infraspecificepithet';
				$this->identSymbFields[] = 'coreid';

				//Get image metadata
				$skipImageFields = array('tid','photographeruid','imagetype','occid','dbpk','specimengui','collid','username','sortsequence','initialtimestamp');
				if($this->uploadType == $this->RESTOREBACKUP){
					unset($skipImageFields);
					$skipImageFields = array();
				}
				$rs = $this->conn->query('SHOW COLUMNS FROM uploadimagetemp');
				while($r = $rs->fetch_object()){
					$field = strtolower($r->Field);
					if(!in_array($field,$skipImageFields)){
						if($autoBuildFieldMap){
							$this->imageFieldMap[$field]["field"] = $field;
						}
						$type = $r->Type;
						$this->imageSymbFields[] = $field;
						if(array_key_exists($field,$this->imageFieldMap)){
							if(strpos($type,"double") !== false || strpos($type,"int") !== false || strpos($type,"decimal") !== false){
								$this->imageFieldMap[$field]["type"] = "numeric";
							}
							elseif(strpos($type,"date") !== false){
								$this->imageFieldMap[$field]["type"] = "date";
							}
							else{
								$this->imageFieldMap[$field]["type"] = "string";
								if(preg_match('/\(\d+\)$/', $type, $matches)){
									$this->imageFieldMap[$field]["size"] = substr($matches[0],1,strlen($matches[0])-2);
								}
							}
						}
					}
				}
				$rs->free();
		}
	}

	public function echoFieldMapTable($autoMap, $mode){
		$prefix = '';
		$fieldMap = $this->fieldMap;
		$symbFieldsRaw = $this->symbFields;
		$sourceArr = $this->sourceArr;
		$translationMap = array('accession'=>'catalognumber','accessionid'=>'catalognumber','accessionnumber'=>'catalognumber','guid'=>'occurrenceid',
			'taxonfamilyname'=>'family','scientificname'=>'sciname','fullname'=>'sciname','speciesauthor'=>'authorspecies','species'=>'specificepithet','commonname'=>'taxonremarks',
			'observer'=>'recordedby','collector'=>'recordedby','primarycollector'=>'recordedby','field:collector'=>'recordedby','collectedby'=>'recordedby',
			'userlogin'=>'recordedby','collectornumber'=>'recordnumber','collectionnumber'=>'recordnumber','field:collectorfieldnumber'=>'recordnumber','collectors'=>'associatedcollectors',
			'datecollected'=>'eventdate','date'=>'eventdate','collectiondate'=>'eventdate','observedon'=>'eventdate','dateobserved'=>'eventdate','collectionstartdate'=>'eventdate','collectionverbatimdate'=>'verbatimeventdate',
			'cf' => 'identificationqualifier','qualifier'=>'identificationqualifier','position'=>'specify:qualifier_position','detby'=>'identifiedby','determinor'=>'identifiedby',
			'determinationdate'=>'dateidentified','determineddate'=>'dateidentified','determinedremarks'=>'identificationremarks','placecountryname'=>'country',
			'placestatename'=>'stateprovince','state'=>'stateprovince','placecountyname'=>'county','municipiocounty'=>'county','location'=>'locality','field:localitydescription'=>'locality',
			'placeguess'=>'locality','localitynotes'=>'locationremarks','latitude'=>'verbatimlatitude','longitude'=>'verbatimlongitude',
			'errorradius'=>'coordinateuncertaintyradius','publicpositionalaccuracy'=>'coordinateuncertaintyinmeters','errorradiusunits'=>'coordinateuncertaintyunits','errorradiusunit'=>'coordinateuncertaintyunits',
			'datum'=>'geodeticdatum','utmzone'=>'utmzoning','township'=>'trstownship','range'=>'trsrange','section'=>'trssection','georeferencingsource'=>'georeferencesources','georefremarks'=>'georeferenceremarks',
			'elevationmeters'=>'minimumelevationinmeters','minelevationm'=>'minimumelevationinmeters','maxelevationm'=>'maximumelevationinmeters','verbatimelev'=>'verbatimelevation',
			'field:associatedspecies'=>'associatedtaxa','associatedspecies'=>'associatedtaxa','assoctaxa'=>'associatedtaxa','specimennotes'=>'occurrenceremarks','notes'=>'occurrenceremarks',
			'generalnotes'=>'occurrenceremarks','plantdescription'=>'verbatimattributes','description'=>'verbatimattributes','specimendescription'=>'verbatimattributes',
			'phenology'=>'reproductivecondition','field:habitat'=>'habitat','habitatdescription'=>'habitat','sitedeschabitat'=>'habitat','captivecultivated'=>'cultivationstatus',
			'ometid'=>'exsiccatiidentifier','exsiccataeidentifier'=>'exsiccatiidentifier','exsnumber'=>'exsiccatinumber','exsiccataenumber'=>'exsiccatinumber',
			'group'=>'paleo-lithogroup','preparationdetails'=>'materialsample-preparationprocess','materialsampletype'=>'materialsample-sampletype',
			'lithostratigraphicterms'=>'paleo-lithology','imageurl'=>'associatedmedia','subject_references'=>'tempfield01','subject_recordid'=>'tempfield02'
		);
		$autoMapExclude = array('institutioncode','collectioncode');

		if($this->paleoSupport){
			$paleoArr = $this->getPaleoTerms();
			foreach($paleoArr as $v){
				$translationMap[substr($v,6)] = $v;
			}
		}
		if($this->materialSampleSupport){
			$msArr = $this->getMaterialSampleTerms();
			foreach($msArr as $term){
				$term = strtolower($term);
				$baseTerm = substr($term,15);
				if($baseTerm != 'individualcount' && $baseTerm != 'disposition' && $baseTerm != 'catalognumber') $translationMap[$baseTerm] = $term;
			}
		}
		if($mode == 'ident'){
			$prefix = 'ID-';
			$fieldMap = $this->identFieldMap;
			$symbFieldsRaw = $this->identSymbFields;
			$sourceArr = $this->identSourceArr;
			$translationMap = array('scientificname'=>'sciname','identificationiscurrent'=>'iscurrent','detby'=>'identifiedby','determinor'=>'identifiedby',
				'determinationdate'=>'dateidentified','notes'=>'identificationremarks','cf' => 'identificationqualifier');
		}
		elseif($mode == 'image'){
			$prefix = 'IM-';
			$fieldMap = $this->imageFieldMap;
			$symbFieldsRaw = $this->imageSymbFields;
			$sourceArr = $this->imageSourceArr;
			$translationMap = array('accessuri'=>'originalurl','thumbnailaccessuri'=>'thumbnailurl','goodqualityaccessuri'=>'url',
				'providermanagedid'=>'sourceidentifier','usageterms'=>'copyright','webstatement'=>'accessrights','creator'=>'photographer',
				'comments'=>'notes','associatedspecimenreference'=>'referenceurl');
		}

		$symbFields = array();
		foreach($symbFieldsRaw as $sValue){
			$symbFields[$sValue] = strtolower($sValue);
		}
		//Build a Source => Symbiota field Map
		$sourceSymbArr = Array();
		foreach($fieldMap as $symbField => $fArr){
			if($symbField != 'dbpk') $sourceSymbArr[$fArr["field"]] = $symbField;
		}

		if($this->uploadType == $this->NFNUPLOAD && !in_array('subject_references', $this->sourceArr) && !in_array('subject_recordid', $this->sourceArr)){
			echo '<div style="color:red">ERROR: input file does not contain proper identifier field (e.g. occid as subject_references or recordID as subject_recordid)</div>';
			return false;
		}
		//Output table rows for source data
		echo '<table class="styledtable" style="width:600px;font-family:Arial;font-size:12px;">';
		echo '<tr><th>Source Field</th><th>Target Field</th></tr>'."\n";
		foreach($sourceArr as $fieldName){
			if($fieldName == 'coreid') continue;
			$diplayFieldName = $fieldName;
			$fieldName = trim(strtolower($fieldName));
			if($this->uploadType == $this->NFNUPLOAD && ($fieldName == 'subject_recordid' || $fieldName == 'subject_references')){
				echo '<input type="hidden" name="sf[]" value="'.$fieldName.'" />';
				echo '<input type="hidden" name="tf[]" value="'.$translationMap[$fieldName].'" />';
			}
			else{
				if($this->uploadType == $this->NFNUPLOAD && substr($fieldName,0,8) == 'subject_') continue;
				$isAutoMapped = false;
				$tranlatedFieldName = str_replace(array('_',' ','.','(',')'),'',$fieldName);
				if($autoMap){
					if(array_key_exists($tranlatedFieldName,$translationMap)) $tranlatedFieldName = strtolower($translationMap[$tranlatedFieldName]);
					if(in_array($tranlatedFieldName,$symbFields) && !in_array($fieldName,$autoMapExclude)){
						$isAutoMapped = true;
					}
					elseif(in_array('specify:'.$fieldName,$symbFields)){
						$tranlatedFieldName = strtolower('specify:'.$fieldName);
						$isAutoMapped = true;
					}
				}
				echo "<tr>\n";
				echo '<td style="padding:2px;">';
				echo $diplayFieldName;
				echo '<input type="hidden" name="'.$prefix.'sf[]" value="'.$fieldName.'" />';
				echo '</td>';
				echo '<td>';
				$className = '';
				if(!$isAutoMapped && !array_key_exists($fieldName,$sourceSymbArr)) $className = 'unmapped';
				echo '<select name="'.$prefix.'tf[]" class="'.$className.'">';
				echo '<option value="">Select Target Field</option>';
				echo '<option value="unmapped"'.(isset($sourceSymbArr[$fieldName]) && substr($sourceSymbArr[$fieldName],0,8)=='unmapped'?'SELECTED':'').'>Leave Field Unmapped</option>';
				echo '<option value="">-------------------------</option>';
				if(array_key_exists($fieldName,$sourceSymbArr)){
					//Source Field is mapped to Symbiota Field
					foreach($symbFields as $sFieldDisplay => $sField){
						echo "<option ".($sourceSymbArr[$fieldName]==$sField?"SELECTED":"").">".$sFieldDisplay."</option>\n";
					}
				}
				elseif($isAutoMapped){
					//Source Field = Symbiota Field
					foreach($symbFields as $sFieldDisplay => $sField){
						$selStr = '';
						if($tranlatedFieldName==$sField && !in_array($sField,$autoMapExclude)) $selStr = 'SELECTED';
						echo '<option '.$selStr.'>'.$sFieldDisplay.'</option>';
					}
				}
				else{
					foreach($symbFields as $sFieldDisplay => $sField){
						echo '<option>'.$sFieldDisplay.'</option>';
					}
				}
				echo "</select></td>\n";
				echo "</tr>\n";
			}
		}
		echo '</table>';
		return true;
	}

	public function saveFieldMap($postArr){
		$statusStr = '';
		if(!$this->uspid && array_key_exists('profiletitle',$postArr)){
			$this->uspid = $this->createUploadProfile(array('uploadtype'=>$this->uploadType,'title'=>$postArr['profiletitle']));
			$this->readUploadParameters();
		}
		if($this->uspid){
			$this->deleteFieldMap();
			$sqlInsert = "INSERT INTO uploadspecmap(uspid,symbspecfield,sourcefield) ";
			$sqlValues = "VALUES (".$this->uspid;
			foreach($this->fieldMap as $k => $v){
				$sourceField = $v["field"];
				$sql = $sqlInsert.$sqlValues.",'".$k."','".$sourceField."')";
				//echo "<div>".$sql."</div>";
				if(!$this->conn->query($sql)){
					$statusStr = 'ERROR saving field map: '.$this->conn->error;
				}
			}
			//Save custom occurrence filter variables
			if($this->filterArr){
				$sql = 'UPDATE uploadspecparameters SET querystr = "'.$this->cleanInStr(json_encode($this->filterArr)).'" WHERE uspid = '.$this->uspid;
				if(!$this->conn->query($sql)){
					$statusStr = 'ERROR saving custom filter variables: '.$this->conn->error;
				}
			}
			//Save identification field map
			foreach($this->identFieldMap as $k => $v){
				$sourceField = $v["field"];
				$sql = $sqlInsert.$sqlValues.",'ID-".$k."','".$sourceField."')";
				//echo "<div>".$sql."</div>";
				if(!$this->conn->query($sql)){
					$statusStr = 'ERROR saving identification field map: '.$this->conn->error;
				}
			}
			//Save image field map
			foreach($this->imageFieldMap as $k => $v){
				$sourceField = $v["field"];
				$sql = $sqlInsert.$sqlValues.",'IM-".$k."','".$sourceField."')";
				//echo "<div>".$sql."</div>";
				if(!$this->conn->query($sql)){
					$statusStr = 'ERROR saving image field map: '.$this->conn->error;
				}
			}

		}
		return $statusStr;
	}

	public function deleteFieldMap(){
		$statusStr = '';
		if($this->uspid){
			$sql = "DELETE FROM uploadspecmap WHERE (uspid = ".$this->uspid.") ";
			//echo "<div>$sql</div>";
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR deleting field map: '.$this->conn->error;
			}
			$sql = "UPDATE uploadspecparameters SET querystr = NULL WHERE (uspid = ".$this->uspid.") ";
			//echo "<div>$sql</div>";
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR deleting field map: '.$this->conn->error;
			}
		}
		return $statusStr;
	}

	public function analyzeUpload(){
		return true;
	}

	protected function prepUploadData(){
		$this->outputMsg('<li>Clearing staging tables</li>');
		$sqlDel1 = 'DELETE FROM uploadspectemp WHERE (collid IN('.$this->collId.'))';
		$this->conn->query($sqlDel1);
		$sqlDel2 = 'DELETE FROM uploaddetermtemp WHERE (collid IN('.$this->collId.'))';
		$this->conn->query($sqlDel2);
		$sqlDel3 = 'DELETE FROM uploadimagetemp WHERE (collid IN('.$this->collId.'))';
		$this->conn->query($sqlDel3);
	}

	public function uploadData($finalTransfer){
		//Stored Procedure upload; other upload types are controlled by their specific class functions
		$this->outputMsg('<li>Initiating data upload</li>');
		$this->prepUploadData();

		if($this->uploadType == $this->STOREDPROCEDURE){
			$this->cleanUpload();
		}
		elseif($this->uploadType == $this->SCRIPTUPLOAD){
			if(system($this->queryStr)){
				$this->outputMsg('<li>Script Upload successful</li>');
				$this->outputMsg('<li>Initializing final transfer steps...</li>');
				$this->cleanUpload();
			}
		}
		if($finalTransfer){
			$this->finalTransfer();
		}
		else{
			$this->outputMsg('<li>Record upload complete, ready for final transfer and activation</li>');
		}
	}

	protected function cleanUpload(){

		if($this->collMetadataArr["managementtype"] == 'Snapshot' || $this->collMetadataArr["managementtype"] == 'Aggregate'){
			//If collection is a snapshot, map upload to existing records. These records will be updated rather than appended
			$this->outputMsg('<li>Linking records (e.g. matching Primary Identifier)... </li>');
			$sql = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.dbpk = o.dbpk) AND (u.collid = o.collid) '.
				'SET u.occid = o.occid '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.dbpk IS NOT NULL) AND (o.dbpk IS NOT NULL)';
			$this->conn->query($sql);
		}

		$this->prepareAssociatedMedia();

		//Run custom cleaning Stored Procedure, if one exists
		if($this->storedProcedure){
			if($this->conn->query('CALL '.$this->storedProcedure)){
				$this->outputMsg('<li>Stored procedure executed: '.$this->storedProcedure.'</li>');
				if($this->conn->more_results()) $this->conn->next_result();
			}
			else{
				$this->outputMsg('<li><span style="color:red;">ERROR: Stored Procedure failed ('.$this->storedProcedure.'): '.$this->conn->error.'</span></li>');
			}
		}

		//Prefrom general cleaning and parsing tasks
		$this->recordCleaningStage1();

		if($this->collMetadataArr["managementtype"] == 'Live Data' || $this->uploadType == $this->SKELETAL){
			if($this->matchCatalogNumber){
				//Match records based on Catalog Number
				$sql = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
					'SET u.occid = o.occid '.
					'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) AND (o.catalogNumber IS NOT NULL) ';
				if($this->collMetadataArr['colltype'] == 'General Observations' && $this->observerUid) $sql .= ' AND o.observeruid = '.$this->observerUid;
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li><span style="color:red;">Warning: unable to match on catalog number: '.$this->conn->error.'</span></li>');
				}
			}
			if($this->matchOtherCatalogNumbers){
				//Match records based on other Catalog Numbers fields
				$sql2 = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.otherCatalogNumbers = o.otherCatalogNumbers) AND (u.collid = o.collid) '.
					'SET u.occid = o.occid '.
					'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.othercatalogNumbers IS NOT NULL) AND (o.othercatalogNumbers IS NOT NULL) ';
				if($this->collMetadataArr['colltype'] == 'General Observations' && $this->observerUid) $sql .= ' AND o.observeruid = '.$this->observerUid;
				if(!$this->conn->query($sql2)){
					$this->outputMsg('<li><span style="color:red;">Warning: unable to match on other catalog numbers: '.$this->conn->error.'</span></li>');
				}
			}
		}
		if($this->collMetadataArr["managementtype"] == 'Live Data'){
			//Make sure that explicitly set occurrenceID GUIDs are not lost during special imports using catalogNumber matching
			$sql = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON u.occid = o.occid SET u.occurrenceID = o.occurrenceID WHERE o.occurrenceID IS NOT NULL AND u.occurrenceID IS NULL ';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li><span style="color:red;">Warning: issue attempting to preserve explicitly defined GUID (e.g. externally generated GUIDs) within a Live Managed collection: '.$this->conn->error.'</span></li>');
			}
		}

		$this->cleanImages();
		//Reset $treansferCnt so that count is accurate since some records may have been deleted due to data integrety issues
		$this->setTransferCount();
		$this->setIdentTransferCount();
		$this->setImageTransferCount();
	}

	private function recordCleaningStage1(){
		$this->outputMsg('<li>Data cleaning:</li>');
		$this->outputMsg('<li style="margin-left:10px;">Cleaning event dates...</li>');

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.year = YEAR(u.eventDate) '.
			'WHERE (u.collid IN('.$this->collId.')) AND (u.eventDate IS NOT NULL) AND (u.year IS NULL)';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.month = MONTH(u.eventDate) '.
			'WHERE (u.collid IN('.$this->collId.')) AND (u.month IS NULL) AND (u.eventDate IS NOT NULL)';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.day = DAY(u.eventDate) '.
			'WHERE u.collid IN('.$this->collId.') AND u.day IS NULL AND u.eventDate IS NOT NULL';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.startDayOfYear = DAYOFYEAR(u.eventDate) '.
			'WHERE u.collid IN('.$this->collId.') AND u.startDayOfYear IS NULL AND u.eventDate IS NOT NULL';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp u '.
			'SET u.endDayOfYear = DAYOFYEAR(u.LatestDateCollected) '.
			'WHERE u.collid IN('.$this->collId.') AND u.endDayOfYear IS NULL AND u.LatestDateCollected IS NOT NULL';
		$this->conn->query($sql);

		$sql = 'UPDATE IGNORE uploadspectemp u '.
			'SET u.eventDate = CONCAT_WS("-",LPAD(u.year,4,"19"),IFNULL(LPAD(u.month,2,"0"),"00"),IFNULL(LPAD(u.day,2,"0"),"00")) '.
			'WHERE (u.eventDate IS NULL) AND (u.year > 1300) AND (u.year <= '.date('Y').') AND (collid = IN('.$this->collId.'))';
		$this->conn->query($sql);

		$this->outputMsg('<li style="margin-left:10px;">Cleaning country and state/province ...</li>');
		//Convert country abbreviations to full spellings
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupcountry c ON u.country = c.iso3 '.
			'SET u.country = c.countryName '.
			'WHERE (u.collid IN('.$this->collId.'))';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupcountry c ON u.country = c.iso '.
			'SET u.country = c.countryName '.
			'WHERE u.collid IN('.$this->collId.')';
		$this->conn->query($sql);

		//Convert state abbreviations to full spellings
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupstateprovince s ON u.stateProvince = s.abbrev '.
			'SET u.stateProvince = s.stateName '.
			'WHERE u.collid IN('.$this->collId.')';
		$this->conn->query($sql);

		//Fill null country with state matches
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupstateprovince s ON u.stateprovince = s.statename '.
			'INNER JOIN lkupcountry c ON s.countryid = c.countryid '.
			'SET u.country = c.countryName '.
			'WHERE u.country IS NULL AND c.countryname = "United States" AND u.collid IN('.$this->collId.')';
		$this->conn->query($sql);
		$sql = 'UPDATE uploadspectemp u INNER JOIN lkupstateprovince s ON u.stateprovince = s.statename '.
			'INNER JOIN lkupcountry c ON s.countryid = c.countryid '.
			'SET u.country = c.countryName '.
			'WHERE u.country IS NULL AND u.collid IN('.$this->collId.')';
		$this->conn->query($sql);

		$this->outputMsg('<li style="margin-left:10px;">Cleaning coordinates...</li>');
		$sql = 'UPDATE uploadspectemp '.
			'SET DecimalLongitude = -1*DecimalLongitude '.
			'WHERE (DecimalLongitude > 0) AND (Country IN("USA","United States","U.S.A.","Canada","Mexico")) AND (stateprovince != "Alaska" OR stateprovince IS NULL) AND (collid IN('.$this->collId.'))';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp '.
			'SET DecimalLatitude = NULL, DecimalLongitude = NULL '.
			'WHERE DecimalLatitude = 0 AND DecimalLongitude = 0 AND collid IN('.$this->collId.')';
		$this->conn->query($sql);

		//Move illegal coordinates to verbatim
		$sql = 'UPDATE uploadspectemp '.
			'SET verbatimcoordinates = CONCAT_WS(" ",DecimalLatitude, DecimalLongitude) '.
			'WHERE verbatimcoordinates IS NULL AND collid IN('.$this->collId.') '.
			'AND (DecimalLatitude < -90 OR DecimalLatitude > 90 OR DecimalLongitude < -180 OR DecimalLongitude > 180)';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp '.
			'SET DecimalLatitude = NULL, DecimalLongitude = NULL '.
			'WHERE collid IN('.$this->collId.') AND (DecimalLatitude < -90 OR DecimalLatitude > 90 OR DecimalLongitude < -180 OR DecimalLongitude > 180)';
		$this->conn->query($sql);

		$this->outputMsg('<li style="margin-left:10px;">Cleaning taxonomy...</li>');
		$sql = 'UPDATE uploadspectemp SET family = sciname WHERE (family IS NULL) AND (sciname LIKE "%aceae" OR sciname LIKE "%idae")';
		$this->conn->query($sql);

		$sql = 'UPDATE uploadspectemp SET sciname = family WHERE (family IS NOT NULL) AND (sciname IS NULL) ';
		$this->conn->query($sql);

		#Updating records with null author
		$sql = 'UPDATE uploadspectemp u INNER JOIN taxa t ON u.sciname = t.sciname '.
			'SET u.scientificNameAuthorship = t.author '.
			'WHERE u.scientificNameAuthorship IS NULL AND t.author IS NOT NULL';
		$this->conn->query($sql);

		//Lock security setting if set so that local system can't override
		$sql = 'UPDATE uploadspectemp '.
			'SET localitySecurityReason = "Locked: set via import file" '.
			'WHERE localitySecurity > 0 AND localitySecurityReason IS NULL AND collid IN('.$this->collId.')';
		$this->conn->query($sql);
	}

	public function getTransferReport(){
		$reportArr = array();
		$reportArr['occur'] = $this->getTransferCount();

		//Number of new specimen records
		$sql = 'SELECT count(*) AS cnt '.
			'FROM uploadspectemp u LEFT JOIN omoccurrences o ON u.occid = o.occid '.
			'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL OR o.occid IS NULL)';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$reportArr['new'] = $r->cnt;
		}
		$rs->free();

		//Number of matching records that will be updated
		$sql = 'SELECT count(*) AS cnt FROM uploadspectemp WHERE (occid IS NOT NULL) AND (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$reportArr['update'] = $r->cnt;
		}
		$rs->free();

		if($this->collMetadataArr['managementtype'] == 'Live Data' && !$this->matchCatalogNumber  && !$this->matchOtherCatalogNumbers && $this->uploadType != $this->RESTOREBACKUP){
			//Records that can be matched on Catalog Number, but will be appended
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM uploadspectemp u INNER JOIN omoccurrences o ON u.collid = o.collid '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber = o.catalogNumber OR u.othercatalogNumbers = o.othercatalogNumbers) ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['matchappend'] = $r->cnt;
			}
			$rs->free();
		}

		if($this->uploadType == $this->RESTOREBACKUP || ($this->collMetadataArr["managementtype"] == 'Snapshot' && $this->uploadType != $this->SKELETAL)){
			//Records already in portal that won't match with an incoming record
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM omoccurrences o LEFT JOIN uploadspectemp u  ON (o.occid = u.occid) '.
				'WHERE (o.collid IN('.$this->collId.')) AND (u.occid IS NULL)';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['exist'] = $r->cnt;
			}
			$rs->free();
		}

		if($this->uploadType != $this->SKELETAL && $this->collMetadataArr["managementtype"] == 'Snapshot' && $this->uploadType != $this->RESTOREBACKUP){
			//Match records that were processed via the portal, walked back to collection's central database, and come back to portal
			$sql = 'SELECT count(o.occid) AS cnt '.
				'FROM uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) '.
				'AND (o.catalogNumber IS NOT NULL) AND (o.dbpk IS NULL)';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['sync'] = $r->cnt;
				$newCnt = $reportArr['new'] - $r->cnt;
				if($newCnt >= -1) $reportArr['new'] = $newCnt;
				$reportArr['update'] += $r->cnt;
				$existCnt = $reportArr['exist'] - $r->cnt;
				if($existCnt >= -1) $reportArr['exist'] = $existCnt;
			}
			$rs->free();
		}

		if($this->uploadType != $this->SKELETAL && $this->uploadType != $this->RESTOREBACKUP && ($this->collMetadataArr["managementtype"] == 'Snapshot' || $this->collMetadataArr["managementtype"] == 'Aggregate')){
			//Look for null dbpk
			$sql = 'SELECT count(*) AS cnt FROM uploadspectemp WHERE (dbpk IS NULL) AND (collid IN('.$this->collId.'))';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$reportArr['nulldbpk'] = $r->cnt;
			}
			$rs->free();

			//Look for duplicate dbpk
			$sql = 'SELECT dbpk FROM uploadspectemp GROUP BY dbpk, collid, basisofrecord HAVING (Count(*)>1) AND (collid IN('.$this->collId.'))';
			$rs = $this->conn->query($sql);
			$reportArr['dupdbpk'] = $rs->num_rows;
			$rs->free();
		}

		if($this->identTransferCount) $reportArr['ident'] = $this->identTransferCount;
		if($this->imageTransferCount) $reportArr['image'] = $this->imageTransferCount;

		return $reportArr;
	}

	public function finalTransfer(){
		global $QUICK_HOST_ENTRY_IS_ACTIVE;
		$this->recordCleaningStage2();
		$this->transferOccurrences();
		$this->transferIdentificationHistory();
		$this->transferImages();
		if($QUICK_HOST_ENTRY_IS_ACTIVE){
			$this->transferHostAssociations();
		}
		$this->finalCleanup();
		$this->outputMsg('<li style="">Upload Procedure Complete ('.date('Y-m-d h:i:s A').')!</li>');
		$this->outputMsg(' ');
	}

	private function recordCleaningStage2(){
		$this->outputMsg('<li>Starting Stage 2 cleaning</li>');
		if($this->uploadType == $this->NFNUPLOAD){
			//Remove specimens without links back to source
			$sql = 'DELETE FROM uploadspectemp WHERE (occid IS NULL) AND (collid IN('.$this->collId.'))';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:10px"><span style="color:red;">ERROR</span> deleting specimens ('.$this->conn->error.')</li>');
			}
		}
		else{
			if($this->collMetadataArr["managementtype"] == 'Snapshot' || $this->uploadType == $this->SKELETAL){
				//Match records that were processed via the portal, walked back to collection's central database, and come back to portal
				$this->outputMsg('<li style="margin-left:10px;">Populating source identifiers (dbpk) to relink specimens processed within portal...</li>');
				$sql = 'UPDATE IGNORE uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
					'SET u.occid = o.occid, o.dbpk = u.dbpk '.
					'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) AND (o.catalogNumber IS NOT NULL) AND (o.dbpk IS NULL) ';
				$this->conn->query($sql);
			}

			if(($this->collMetadataArr["managementtype"] == 'Snapshot' && $this->uploadType != $this->SKELETAL) || $this->collMetadataArr["managementtype"] == 'Aggregate'){
				$this->outputMsg('<li style="margin-left:10px;">Remove NULL dbpk values...</li>');
				$sql = 'DELETE FROM uploadspectemp WHERE (dbpk IS NULL) AND (collid IN('.$this->collId.'))';
				$this->conn->query($sql);

				$this->outputMsg('<li style="margin-left:10px;">Remove duplicate dbpk values...</li>');
				$sql = 'DELETE u.* '.
					'FROM uploadspectemp u INNER JOIN (SELECT dbpk FROM uploadspectemp '.
					'GROUP BY dbpk, collid HAVING Count(*)>1 AND collid IN('.$this->collId.')) t2 ON u.dbpk = t2.dbpk '.
					'WHERE collid IN('.$this->collId.')';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:10px"><span style="color:red;">ERROR</span> ('.$this->conn->error.')</li>');
				}
			}
		}
	}

	protected function transferOccurrences(){
		//Clean and Transfer records from uploadspectemp to specimens
		if($this->uploadType == $this->NFNUPLOAD){
			//Transfer edits to revision history table
			$this->outputMsg('<li>Transferring edits to versioning tables...</li>');
			$this->versionOccurrenceEdits();
		}
		$transactionInterval = 1000;
		$this->outputMsg('<li>Updating existing records in batches of '.$transactionInterval.'... </li>');
		//Grab specimen intervals for updating records in batches
		$intervalArr = array();
		$sql = 'SELECT occid FROM ( SELECT @row := @row +1 AS rownum, occid FROM ( SELECT @row :=0) r, uploadspectemp WHERE occid IS NOT NULL AND collid = '.
			$this->collId.' ORDER BY occid) ranked WHERE rownum % '.$transactionInterval.' = 1';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$intervalArr[] = $r->occid;
		}
		$rs->free();

		$fieldArr = $this->getTransferFieldArr();

		//Update matching records
		$sqlFragArr = array();
		foreach($fieldArr as $v){
			if($v == 'processingstatus' && $this->processingStatus){
				$sqlFragArr[$v] = 'o.processingStatus = u.processingStatus';
			}
			elseif($this->uploadType == $this->SKELETAL || $this->uploadType == $this->NFNUPLOAD){
				$sqlFragArr[$v] = 'o.'.$v.' = IFNULL(o.'.$v.',u.'.$v.')';
			}
			else{
				$sqlFragArr[$v] = 'o.'.$v.' = u.'.$v;
			}
		}
		$obsUidTarget = 'NULL';
		if($this->uploadType == $this->RESTOREBACKUP) $obsUidTarget = 'u.observeruid';
		elseif($this->observerUid) $obsUidTarget = $this->observerUid;
		$sqlBase = 'UPDATE IGNORE uploadspectemp u INNER JOIN omoccurrences o ON u.occid = o.occid SET o.observeruid = '.$obsUidTarget.','.implode(',',$sqlFragArr);
		if($this->collMetadataArr["managementtype"] == 'Snapshot') $sqlBase .= ', o.dateLastModified = CURRENT_TIMESTAMP() ';
		$sqlBase .= ' WHERE (u.collid IN('.$this->collId.')) ';
		$cnt = 1;
		$previousInterval = 0;
		foreach($intervalArr as $intValue){
			if($previousInterval){
				$sql = $sqlBase.'AND (o.occid BETWEEN '.$previousInterval.' AND '.($intValue-1).') ';
				if($this->conn->query($sql)) $this->outputMsg('<li style="margin-left:10px">'.$cnt.': '.$transactionInterval.' updated ('.$this->conn->affected_rows.' changed)</li>');
				else $this->outputMsg('<li style="margin-left:10px">FAILED updating records: '.$this->conn->error.'</li> ');
				$cnt++;
			}
			$previousInterval = $intValue;
		}
		$sql = $sqlBase.'AND (o.occid >= '.$previousInterval.')';
		if($this->conn->query($sql)) $this->outputMsg('<li style="margin-left:10px">'.$cnt.': '.$this->conn->affected_rows.' updated</li>');
		else $this->outputMsg('<li style="margin-left:10px">ERROR updating records: '.$this->conn->error.'</li> ');

		//Insert new records
		if($this->uploadType != $this->NFNUPLOAD){
			$this->outputMsg('<li>Transferring new records in batches of '.$transactionInterval.'...</li>');
			$insertTarget = 0;
			$sql = 'SELECT COUNT(*) AS cnt FROM uploadspectemp WHERE occid IS NULL AND collid IN('.$this->collId.')';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()) $insertTarget = $r->cnt;
			$rs->free();
			$cnt = 1;
			while($insertTarget > 0){
				$sql = 'INSERT IGNORE INTO omoccurrences (collid, dbpk, dateentered, observerUid, '.implode(', ',$fieldArr).' ) '.
					'SELECT u.collid, u.dbpk, "'.date('Y-m-d H:i:s').'", '.$obsUidTarget.', u.'.implode(', u.',$fieldArr).' FROM uploadspectemp u '.
					'WHERE u.occid IS NULL AND u.collid IN('.$this->collId.') LIMIT '.$transactionInterval;
				$insertCnt = 0;
				if($this->conn->query($sql)){
					$insertCnt = $this->conn->affected_rows;
					$warnCnt = $this->conn->warning_count;
					if($warnCnt){
						if(strpos($this->conn->get_warnings()->message,'UNIQUE_occurrenceID'))
							$this->outputMsg('<li style="margin-left:10px"><span style="color:orange">WARNING</span>: '.$warnCnt.' records failed to load due to duplicate occurrenceID values which must be unique across all collections)</li>');
					}
				}
				else{
					$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
					//$this->outputMsg($sql);
				}
				$sql = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON u.dbpk = o.dbpk '.
					'SET u.occid = o.occid '.
					'WHERE o.collid = '.$this->collId.' AND u.collid = '.$this->collId.' AND u.occid IS NULL';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li>ERROR updating occid on recent Insert batch: '.$this->conn->error.'</li> ');
				}
				$this->outputMsg('<li style="margin-left:10px">'.$cnt.': '.$insertCnt.' inserted</li>');
				$insertTarget -= $transactionInterval;
				$cnt++;
			};

			//Link all newly intersted records back to uploadspectemp in prep for loading determiantion history and associatedmedia
			$this->outputMsg('<li>Linking records in prep for loading extended data...</li>');
			//Update occid by matching dbpk
			$sqlOcc1 = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.dbpk = o.dbpk) AND (u.collid = o.collid) '.
				'SET u.occid = o.occid '.
				'WHERE (u.occid IS NULL) AND (u.dbpk IS NOT NULL) AND (u.collid IN('.$this->collId.'))';
			if(!$this->conn->query($sqlOcc1)){
				$this->outputMsg('<li>ERROR updating occid after occurrence insert: '.$this->conn->error.'</li>');
			}
			//Update occid by linking catalognumbers
			$sqlOcc2 = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON (u.catalogNumber = o.catalogNumber) AND (u.collid = o.collid) '.
				'SET u.occid = o.occid '.
				'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL) AND (u.catalogNumber IS NOT NULL) AND (o.catalogNumber IS NOT NULL) ';
			if(!$this->conn->query($sqlOcc2)){
				$this->outputMsg('<li>ERROR updating occid (2nd step) after occurrence insert: '.$this->conn->error.'</li>');
			}

			$this->transferExsiccati();
			$this->transferGeneticLinks();
			$this->transferPaleoData();
			$this->transferMaterialSampleData();

			//Setup and add datasets and link datasets to current user

		}
	}

	private function versionOccurrenceEdits(){
		$nfnFieldArr = array();
		$sql = "SHOW COLUMNS FROM omoccurrences";
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if(in_array($field, $this->symbFields)) $nfnFieldArr[] = $field;
		}
		$rs->free();

		$sqlFrag = '';
		foreach($nfnFieldArr as $field){
			$sqlFrag .= ',u.'.$field.',o.'.$field.' as old_'.$field;
		}
		$sql = 'SELECT o.occid'.$sqlFrag.' FROM omoccurrences o INNER JOIN uploadspectemp u ON o.occid = u.occid WHERE o.collid IN('.$this->collId.') AND u.collid IN('.$this->collId.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$editArr = array();
			foreach($nfnFieldArr as $field){
				if($r[$field] && $r['old_'.$field] != $r[$field]){
					if($r['old_'.$field] && $field != 'processingstatus'){
						$editArr[0]['old'][$field] = $r['old_'.$field];
						$editArr[0]['new'][$field] = $r[$field];
					}
					else{
						$editArr[1]['old'][$field] = $r['old_'.$field];
						$editArr[1]['new'][$field] = $r[$field];
					}
				}
			}
			//Load into revisions table
			foreach($editArr as $appliedStatus => $eArr){
				$sql = 'INSERT INTO omoccurrevisions(occid, oldValues, newValues, externalSource, reviewStatus, appliedStatus) '.
						'VALUES('.$r['occid'].',"'.$this->cleanInStr(json_encode($eArr['old'])).'","'.$this->cleanInStr(json_encode($eArr['new'])).'","Notes from Nature Expedition",1,'.$appliedStatus.')';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:10px;">ERROR adding edit revision ('.$this->conn->error.')</li>');
				}
			}
		}
		$rs->free();
	}

	private function getTransferFieldArr(){
		//Get uploadspectemp supported fields
		$uploadArr = array();
		$sql1 = 'SHOW COLUMNS FROM uploadspectemp';
		$rs1 = $this->conn->query($sql1);
		while($r1 = $rs1->fetch_object()){
			$uploadArr[strtolower($r1->Field)] = 0;
		}
		$rs1->free();
		//Get omoccurrences supported fields
		$specArr = array();
		$sql2 = 'SHOW COLUMNS FROM omoccurrences';
		$rs2 = $this->conn->query($sql2);
		while($r2 = $rs2->fetch_object()){
			$specArr[strtolower($r2->Field)] = 0;
		}
		$rs2->free();
		//Get union of both tables
		$fieldArr = array_intersect_assoc($uploadArr,$specArr);
		unset($fieldArr['occid']);
		unset($fieldArr['collid']);
		if($this->uploadType != $this->RESTOREBACKUP){
			unset($fieldArr['dbpk']);
			unset($fieldArr['observeruid']);
			unset($fieldArr['dateentered']);
			unset($fieldArr['initialtimestamp']);
		}
		return array_keys($fieldArr);
	}

	private function transferExsiccati(){
		$this->outputMsg('<li>Loading Exsiccati numbers...</li>');
		//Add any new exsiccati numbers
		$sqlNum = 'INSERT INTO omexsiccatinumbers(ometid, exsnumber) '.
			'SELECT DISTINCT u.exsiccatiIdentifier, u.exsiccatinumber '.
			'FROM uploadspectemp u LEFT JOIN omexsiccatinumbers e ON u.exsiccatiIdentifier = e.ometid AND u.exsiccatinumber = e.exsnumber '.
			'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NOT NULL) '.
			'AND (u.exsiccatiIdentifier IS NOT NULL) AND (u.exsiccatinumber IS NOT NULL) AND (e.exsnumber IS NULL)';
		if(!$this->conn->query($sqlNum)){
			$this->outputMsg('<li>ERROR adding new exsiccati numbers: '.$this->conn->error.'</li>');
		}
		//Load exsiccati
		$sqlLink = 'INSERT IGNORE INTO omexsiccatiocclink(omenid,occid) '.
			'SELECT e.omenid, u.occid '.
			'FROM uploadspectemp u INNER JOIN omexsiccatinumbers e ON u.exsiccatiIdentifier = e.ometid AND u.exsiccatinumber = e.exsnumber '.
			'WHERE (u.collid IN('.$this->collId.')) AND (e.omenid IS NOT NULL) AND (u.occid IS NOT NULL)';
		if(!$this->conn->query($sqlLink)){
			$this->outputMsg('<li>ERROR adding new exsiccati numbers: '.$this->conn->error.'</li>',1);
		}
	}

	private function transferGeneticLinks(){
		$this->outputMsg('<li>Linking genetic records (aka associatedSequences)...</li>');
		$sql = 'SELECT occid, associatedSequences FROM uploadspectemp WHERE occid IS NOT NULL AND associatedSequences IS NOT NULL ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$seqArr = explode(';', str_replace(array(',','|',''),';',$r->associatedSequences));
			foreach($seqArr as $str){
				//$urlPattern = '/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/';
				if(preg_match('$((http|https)\://[^\s;,]+)$', $str, $match)){
					$url = $match[1];
					$noteStr = trim(str_replace($url, '', $str),',;| ');
					$resNameStr = 'undefined';
					$idenStr = '';
					if(preg_match('$ncbi\.nlm\.nih\.gov.+/([A-Z]+\d+)$', $str, $matchNCBI)){
						//https://www.ncbi.nlm.nih.gov/nuccore/AY138416
						$resNameStr = 'GenBank';
						$idenStr = $matchNCBI[1];
					}
					elseif(preg_match('/boldsystems\.org.*processid=([A-Z\d-]+)/', $str, $matchBOLD)){
						//http://www.boldsystems.org/index.php/Public_RecordView?processid=BSAMQ088-09
						$resNameStr = 'BOLD Systems';
						$idenStr = $matchBOLD[1];
					}
					$seqSQL = 'INSERT INTO omoccurgenetic(occid, resourcename, identifier, resourceurl, notes) '.
						'VALUES('.$r->occid.',"'.$this->cleanInStr($resNameStr).'",'.($idenStr?'"'.$this->cleanInStr($idenStr).'"':'NULL').
						',"'.$url.'",'.($noteStr?'"'.$this->cleanInStr($noteStr).'"':'NULL').')';
					if(!$this->conn->query($seqSQL) && $this->conn->errno != '1062'){
						$this->outputMsg('<li>ERROR adding genetic resource: '.$this->conn->error.'</li>',1);
					}
				}
			}
		}
		$rs->free();
	}

	private function transferPaleoData(){
		if($this->paleoSupport){
			$this->outputMsg('<li>Linking Paleo data...</li>');
			$sql = 'SELECT occid, catalogNumber, paleoJSON FROM uploadspectemp WHERE (occid IS NOT NULL) AND (paleoJSON IS NOT NULL) AND (collid = '.$this->collId.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				try{
					$paleoArr = json_decode($r->paleoJSON,true);
					//Deal with DwC terms
					$eonTerm = '';
					if(isset($paleoArr['earliesteonorlowesteonothem']) && $paleoArr['earliesteonorlowesteonothem']) $eonTerm = $paleoArr['earliesteonorlowesteonothem'];
					if(isset($paleoArr['latesteonorhighesteonothem']) && $paleoArr['latesteonorhighesteonothem'] != $eonTerm) $eonTerm .= ' - '.$paleoArr['latesteonorhighesteonothem'];
					if($eonTerm && !isset($paleoArr['eon'])) $paleoArr['eon'] = $eonTerm;
					unset($paleoArr['earliesteonorlowesteonothem']);
					unset($paleoArr['latesteonorhighesteonothem']);

					$eraTerm = '';
					if(isset($paleoArr['earliesteraorlowesterathem']) && $paleoArr['earliesteraorlowesterathem']) $eraTerm = $paleoArr['earliesteraorlowesterathem'];
					if(isset($paleoArr['latesteraorhighesterathem']) && $paleoArr['latesteraorhighesterathem'] != $eraTerm) $eraTerm .= ' - '.$paleoArr['latesteraorhighesterathem'];
					if($eraTerm && !isset($paleoArr['era'])) $paleoArr['era'] = $eraTerm;
					unset($paleoArr['earliesteraorlowesterathem']);
					unset($paleoArr['latesteraorhighesterathem']);

					$periodTerm = '';
					if(isset($paleoArr['earliestperiodorlowestsystem']) && $paleoArr['earliestperiodorlowestsystem']) $periodTerm = $paleoArr['earliestperiodorlowestsystem'];
					if(isset($paleoArr['latestperiodorhighestsystem']) && $paleoArr['latestperiodorhighestsystem'] != $periodTerm) $periodTerm .= ' - '.$paleoArr['latestperiodorhighestsystem'];
					if($periodTerm && !isset($paleoArr['period'])) $paleoArr['period'] = $periodTerm;
					unset($paleoArr['earliestperiodorlowestsystem']);
					unset($paleoArr['latestperiodorhighestsystem']);

					$epochTerm = '';
					if(isset($paleoArr['earliestepochorlowestseries']) && $paleoArr['earliestepochorlowestseries']) $epochTerm = $paleoArr['earliestepochorlowestseries'];
					if(isset($paleoArr['latestepochorhighestseries']) && $paleoArr['latestepochorhighestseries'] != $epochTerm) $epochTerm .= ' - '.$paleoArr['latestepochorhighestseries'];
					if($epochTerm && !isset($paleoArr['epoch'])) $paleoArr['epoch'] = $epochTerm;
					unset($paleoArr['earliestepochorlowestseries']);
					unset($paleoArr['latestepochorhighestseries']);

					$stageTerm = '';
					if(isset($paleoArr['earliestageorloweststage']) && $paleoArr['earliestageorloweststage']) $stageTerm = $paleoArr['earliestageorloweststage'];
					if(isset($paleoArr['latestageorhigheststage']) && $paleoArr['latestageorhigheststage'] != $stageTerm) $stageTerm .= ' - '.$paleoArr['latestageorhigheststage'];
					if($stageTerm && !isset($paleoArr['stage'])) $paleoArr['stage'] = $stageTerm;
					unset($paleoArr['earliestageorloweststage']);
					unset($paleoArr['latestageorhigheststage']);

					$biostratigraphyTerm = '';
					if(isset($paleoArr['lowestbiostratigraphiczone']) && $paleoArr['lowestbiostratigraphiczone']) $biostratigraphyTerm = $paleoArr['lowestbiostratigraphiczone'];
					if(isset($paleoArr['highestbiostratigraphiczone']) && $paleoArr['highestbiostratigraphiczone'] != $biostratigraphyTerm) $biostratigraphyTerm .= ' - '.$paleoArr['highestbiostratigraphiczone'];
					if($biostratigraphyTerm && !isset($paleoArr['biostratigraphy'])) $paleoArr['biostratigraphy'] = $biostratigraphyTerm;
					unset($paleoArr['lowestbiostratigraphiczone']);
					unset($paleoArr['highestbiostratigraphiczone']);

					$insertSQL = '';
					$valueSQL = '';
					foreach($paleoArr as $k => $v){
						$insertSQL .= ','.$k;
						$valueSQL .= ',"'.$this->cleanInStr($v).'"';
					}
					$sql = 'REPLACE INTO omoccurpaleo(occid'.$insertSQL.') VALUES('.$r->occid.$valueSQL.')';
					if(!$this->conn->query($sql)){
						$this->outputMsg('<li>ERROR adding paleo resources: '.$this->conn->error.'</li>',1);
					}
				}
				catch(Exception $e){
					$this->outputMsg('<li>ERROR adding paleo record (occid: '.$r->occid.', catalogNumber: '.$r->catalogNumber.'): '.$e->getMessage().'</li>',1);
				}
			}
			$rs->free();
		}
	}

	private function transferMaterialSampleData(){
		if($this->materialSampleSupport){
			$this->outputMsg('<li>Linking Material Sample data...</li>');
			$sql = 'SELECT occid, catalogNumber, materialSampleJSON FROM uploadspectemp WHERE (occid IS NOT NULL) AND (materialSampleJSON IS NOT NULL) AND (collid = '.$this->collId.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				try{
					$matSampleArr = json_decode($r->materialSampleJSON,true);
					$insertSQL = '';
					$valueSQL = '';
					foreach($matSampleArr as $k => $v){
						$insertSQL .= ','.$k;
						$valueSQL .= ',"'.$this->cleanInStr($v).'"';
					}
					$sql = 'REPLACE INTO ommaterialsample(occid'.$insertSQL.') VALUES('.$r->occid.$valueSQL.')';
					if(!$this->conn->query($sql)){
						$this->outputMsg('<li>ERROR adding Material Sample resources: '.$this->conn->error.'</li>',1);
					}
				}
				catch(Exception $e){
					$this->outputMsg('<li>ERROR adding Material Sample record (occid: '.$r->occid.', catalogNumber: '.$r->catalogNumber.'): '.$e->getMessage().'</li>',1);
				}
			}
			$rs->free();
		}
	}

	protected function transferIdentificationHistory(){
		$sql = 'SELECT count(*) AS cnt FROM uploaddetermtemp WHERE (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			if($r->cnt){
				$this->outputMsg('<li>Transferring Determination History...</li>');

				//Update occid for determinations of occurrence records already in portal
				$sql = 'UPDATE uploaddetermtemp ud INNER JOIN uploadspectemp u ON ud.collid = u.collid AND ud.dbpk = u.dbpk '.
					'SET ud.occid = u.occid '.
					'WHERE (ud.occid IS NULL) AND (u.occid IS NOT NULL) AND (ud.collid IN('.$this->collId.'))';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:20px;">WARNING updating occids within uploaddetermtemp: '.$this->conn->error.'</li> ');
				}

				//Update determinations where the sourceIdentifiers match
				$sql = 'UPDATE IGNORE omoccurdeterminations d INNER JOIN uploaddetermtemp u ON d.occid = u.occid '.
					'SET d.sciname = u.sciname, d.scientificNameAuthorship = u.scientificNameAuthorship, d.identifiedBy = u.identifiedBy, d.dateIdentified = u.dateIdentified, '.
					'd.identificationQualifier = u.identificationQualifier, d.iscurrent = u.iscurrent, d.identificationReferences = u.identificationReferences, '.
					'd.identificationRemarks = u.identificationRemarks, d.sourceIdentifier = u.sourceIdentifier '.
					'WHERE (u.collid IN('.$this->collId.')) AND (d.sourceIdentifier = u.sourceIdentifier)';
				//echo $sql;
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:20px;">ERROR updating determinations with matching sourceIdentifiers: '.$this->conn->error.'</li> ');
				}
				$sql = 'DELETE u.* FROM omoccurdeterminations d INNER JOIN uploaddetermtemp u ON d.occid = u.occid WHERE (u.collid IN('.$this->collId.')) AND (d.sourceIdentifier = u.sourceIdentifier) ';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:20px;">ERROR removing determinations with matching sourceIdentifiers: '.$this->conn->error.'</li> ');
				}

				//Delete duplicate determinations (likely previously loaded)
				$sqlDel = 'DELETE IGNORE u.* '.
					'FROM uploaddetermtemp u INNER JOIN omoccurdeterminations d ON u.occid = d.occid '.
					'WHERE (u.collid IN('.$this->collId.')) AND (d.sciname = u.sciname) AND (d.identifiedBy = u.identifiedBy) AND (d.dateIdentified = u.dateIdentified)';
				$this->conn->query($sqlDel);

				//Load identification history records
				$sql = 'INSERT IGNORE INTO omoccurdeterminations (occid, sciname, scientificNameAuthorship, identifiedBy, dateIdentified, '.
					'identificationQualifier, iscurrent, identificationReferences, identificationRemarks, sourceIdentifier) '.
					'SELECT u.occid, u.sciname, u.scientificNameAuthorship, u.identifiedBy, u.dateIdentified, '.
					'u.identificationQualifier, u.iscurrent, u.identificationReferences, u.identificationRemarks, sourceIdentifier '.
					'FROM uploaddetermtemp u '.
					'WHERE u.occid IS NOT NULL AND (u.collid IN('.$this->collId.'))';
				if($this->conn->query($sql)){
					//Delete all determinations
					$sqlDel = 'DELETE * FROM uploaddetermtemp WHERE (collid IN('.$this->collId.'))';
					$this->conn->query($sqlDel);
				}
				else{
					$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
				}
			}
		}
		$rs->free();
	}

	private function prepareAssociatedMedia(){
		//parse, check, and transfer all good URLs
		$sql = 'SELECT associatedmedia, tidinterpreted, occid, dbpk FROM uploadspectemp WHERE (associatedmedia IS NOT NULL) AND (collid IN('.$this->collId.'))';
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$this->outputMsg('<li>Loading associatedMedia into image staging table...</li>');
			while($r = $rs->fetch_object()){
				$mediaArr = explode(',',trim(str_replace(array(';','|'),',',$r->associatedmedia),', '));
				foreach($mediaArr as $mediaUrl){
					$mediaUrl = trim($mediaUrl);
					if(strpos($mediaUrl,'"')) continue;
					$this->loadImageRecord(array('occid'=>$r->occid,'collid'=>$this->collId,'dbpk'=>$r->dbpk,'tid'=>($r->tidinterpreted?$r->tidinterpreted:''),'originalurl'=>$mediaUrl));
				}
			}
		}
		$rs->free();
	}

	private function cleanImages(){
		$sql = 'SELECT collid FROM uploadimagetemp WHERE collid = '.$this->collId.' LIMIT 1';
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$this->outputMsg('<li>Removing previously linked images and bad image paths... </li>');
			//Remove images that are not a ULR or local path
			$sql = 'DELETE FROM uploadimagetemp WHERE (originalurl NOT LIKE "http%" AND originalurl NOT LIKE "/%") AND (collid = '.$this->collId.')';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">WARNING removing non-jpgs from uploadimagetemp: '.$this->conn->error.'</li> ');
			}
			//Remove images that are obviously not JPGs
			$sql = 'DELETE FROM uploadimagetemp WHERE (originalurl LIKE "%.dng" OR originalurl LIKE "%.tif") AND (collid = '.$this->collId.')';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">WARNING removing non-jpgs from uploadimagetemp: '.$this->conn->error.'</li> ');
			}
			//Update occid for images of occurrence records already in portal
			$sql = 'UPDATE uploadimagetemp ui INNER JOIN uploadspectemp u ON ui.collid = u.collid AND ui.dbpk = u.dbpk '.
				'SET ui.occid = u.occid '.
				'WHERE (ui.occid IS NULL) AND (u.occid IS NOT NULL) AND (ui.collid = '.$this->collId.')';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">WARNING updating occids within uploadimagetemp: '.$this->conn->error.'</li> ');
			}
			//Remove and skip previously loaded images where urls match exactly
			$sql = 'DELETE u.* FROM uploadimagetemp u INNER JOIN images i ON u.occid = i.occid WHERE (u.collid = '.$this->collId.') AND (u.originalurl = i.originalurl)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">ERROR deleting uploadimagetemp records with matching urls: '.$this->conn->error.'</li> ');
			}
			$sql = 'DELETE u.* FROM uploadimagetemp u INNER JOIN images i ON u.occid = i.occid WHERE (u.collid = '.$this->collId.') AND (u.originalurl IS NULL) AND (i.originalurl IS NULL) AND (u.url = i.url)';
			if(!$this->conn->query($sql)){
				$this->outputMsg('<li style="margin-left:20px;">ERROR deleting image records with matching originalurls: '.$this->conn->error.'</li> ');
			}
		}
		$rs->free();
	}

	protected function transferImages(){
		$sql = 'SELECT count(*) AS cnt FROM uploadimagetemp WHERE (collid = '.$this->collId.')';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			if($r->cnt){
				$this->outputMsg('<li>Transferring images...</li>');
				//Update occid for images of new records
				$sql = 'UPDATE uploadimagetemp ui INNER JOIN uploadspectemp u ON ui.collid = u.collid AND ui.dbpk =u.dbpk '.
					'SET ui.occid = u.occid '.
					'WHERE (ui.occid IS NULL) AND (u.occid IS NOT NULL) AND (ui.collid = '.$this->collId.')';
				//echo $sql.'<br/>';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:20px;">WARNING updating occids within uploadimagetemp: '.$this->conn->error.'</li> ');
				}

				//Set image transfer count
				$this->setImageTransferCount();

				//Get shared field names for transferring between image tables
				$imageFieldArr = array();
				$rs1 = $this->conn->query('SHOW COLUMNS FROM uploadimagetemp');
				while($r1 = $rs1->fetch_object()){
					$imageFieldArr[strtolower($r1->Field)] = 0;
				}
				$rs1->free();
				$rs2 = $this->conn->query('SHOW COLUMNS FROM images');
				while($r2 = $rs2->fetch_object()){
					$fieldName = strtolower($r2->Field);
					if(array_key_exists($fieldName, $imageFieldArr)) $imageFieldArr[$fieldName] = 1;
				}
				$rs2->free();
				foreach($imageFieldArr as $k => $v){
					if(!$v) unset($imageFieldArr[$k]);
				}
				unset($imageFieldArr['sortsequence']);
				unset($imageFieldArr['initialtimestamp']);

				//Remap URLs and remove from import images where source identifiers match, but original URLs differ (e.g. host server is changed)
				$sql = 'UPDATE uploadimagetemp u INNER JOIN images i ON u.occid = i.occid '.
					'SET i.originalurl = u.originalurl, i.url = IFNULL(u.url,if(SUBSTRING(i.url,1,1)="/",i.url,NULL)), i.thumbnailurl = IFNULL(u.thumbnailurl,if(SUBSTRING(i.thumbnailurl,1,1)="/",i.thumbnailurl,NULL)) '.
					'WHERE (u.collid = '.$this->collId.') AND (u.sourceIdentifier = i.sourceIdentifier) ';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:20px;">ERROR remapping URL with matching sourceIdentifier: '.$this->conn->error.'</li> ');
				}
				$sql = 'DELETE u.* FROM uploadimagetemp u INNER JOIN images i ON u.occid = i.occid WHERE (u.collid = '.$this->collId.') AND (u.sourceIdentifier = i.sourceIdentifier)';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:20px;">ERROR deleting incoming image records that have matching sourceIdentifier: '.$this->conn->error.'</li> ');
				}

				//Load images
				$sql = 'INSERT INTO images('.implode(',',array_keys($imageFieldArr)).') '.
					'SELECT '.implode(',',array_keys($imageFieldArr)).' FROM uploadimagetemp WHERE (occid IS NOT NULL) AND (collid = '.$this->collId.')';
				if($this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:10px;">'.$this->imageTransferCount.' images transferred</li> ');
				}
				else{
					$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
				}
			}
		}
		$rs->free();
	}

	protected function transferHostAssociations(){
		$sql = 'SELECT count(*) AS cnt FROM uploadspectemp WHERE collid = '.$this->collId.' AND `host` IS NOT NULL';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			if($r->cnt){
				$this->outputMsg('<li>Transferring host associations...</li>');
				//Update existing host association records
				$sql = 'UPDATE uploadspectemp s LEFT JOIN omoccurassociations a ON s.occid = a.occid '.
					'SET a.verbatimsciname = s.`host` '.
					'WHERE a.occid IS NOT NULL AND s.`host` IS NOT NULL AND a.relationship = "host" ';
				if(!$this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:20px;">WARNING updating host associations within omoccurassociations: '.$this->conn->error.'</li> ');
				}

				//Load images
				$sql = 'INSERT INTO omoccurassociations(occid,relationship,verbatimsciname) '.
					'SELECT s.occid, "host", s.`host` FROM uploadspectemp s LEFT JOIN omoccurassociations a ON s.occid = a.occid '.
					'WHERE (a.occid IS NULL) AND (s.`host` IS NOT NULL) ';
				if($this->conn->query($sql)){
					$this->outputMsg('<li style="margin-left:10px;">Host associations updated</li> ');
				}
				else{
					$this->outputMsg('<li>FAILED! ERROR: '.$this->conn->error.'</li> ');
				}
			}
		}
		$rs->free();
	}

	protected function finalCleanup(){
		$this->outputMsg('<li>Record transfer complete!</li>');
		$this->outputMsg('<li>Cleaning house...</li>');

		//Update uploaddate
		$sql = 'UPDATE omcollectionstats SET uploaddate = CURDATE() WHERE collid IN('.$this->collId.')';
		$this->conn->query($sql);

		//Remove records from occurrence temp table (uploadspectemp)
		$sql = 'DELETE FROM uploadspectemp WHERE (collid IN('.$this->collId.')) OR (initialtimestamp < DATE_SUB(CURDATE(),INTERVAL 3 DAY))';
		$this->conn->query($sql);
		//Optimize table to reset indexes
		$this->conn->query('OPTIMIZE TABLE uploadspectemp');

		//Remove records from determination temp table (uploaddetermtemp)
		$sql = 'DELETE FROM uploaddetermtemp WHERE (collid IN('.$this->collId.')) OR (initialtimestamp < DATE_SUB(CURDATE(),INTERVAL 3 DAY))';
		$this->conn->query($sql);
		//Optimize table to reset indexes
		$this->conn->query('OPTIMIZE TABLE uploaddetermtemp');

		//Remove records from image temp table (uploadimagetemp)
		$sql = 'DELETE FROM uploadimagetemp WHERE (collid IN('.$this->collId.')) OR (initialtimestamp < DATE_SUB(CURDATE(),INTERVAL 3 DAY))';
		$this->conn->query($sql);
		//Optimize table to reset indexes
		$this->conn->query('OPTIMIZE TABLE uploadimagetemp');

		//Remove temporary dbpk values
		if($this->collMetadataArr["managementtype"] == 'Live Data'){
			$sql = 'UPDATE omoccurrences SET dbpk = NULL WHERE (collid IN('.$this->collId.')) AND (dbpk LIKE "SYMBDBPK-%")';
			$this->conn->query($sql);
		}

		//Do some more cleaning of the data after it haas been indexed in the omoccurrences table
		$occurMain = new OccurrenceMaintenance($this->conn);

		if(!$occurMain->generalOccurrenceCleaning($this->collId)){
			$errorArr = $occurMain->getErrorArr();
			foreach($errorArr as $errorStr){
				$this->outputMsg('<li style="margin-left:20px;">'.$errorStr.'</li>',1);
			}
		}

		$this->outputMsg('<li style="margin-left:10px;">Protecting sensitive species...</li>');
		$protectCnt = $occurMain->protectRareSpecies($this->collId);

		$this->outputMsg('<li style="margin-left:10px;">Updating statistics...</li>');
		if(!$occurMain->updateCollectionStats($this->collId)){
			$errorArr = $occurMain->getErrorArr();
			foreach($errorArr as $errorStr){
				$this->outputMsg('<li style="margin-left:20px;">'.$errorStr.'</li>',1);
			}
		}

		$this->outputMsg('<li style="margin-left:10px;">Populating recordID UUIDs for all records... </li>');
		$uuidManager = new UuidFactory();
		$uuidManager->setSilent(1);
		$uuidManager->populateGuids($this->collId);

		if($this->imageTransferCount){
			$this->outputMsg('<li style="margin-left:10px;color:orange">WARNING: Image thumbnails may need to be created using the <a href="../../imagelib/admin/thumbnailbuilder.php?collid='.$this->collId.'">Images Thumbnail Builder</a></li>');
		}
	}

	protected function loadRecord($recMap){
		//Only import record if at least one of the minimal fields have data
		$recMap = OccurrenceUtilities::occurrenceArrayCleaning($recMap);
		$loadRecord = false;
		if($this->uploadType == $this->NFNUPLOAD) $loadRecord = true;
		elseif(isset($recMap['occid']) && $recMap['occid']) $loadRecord = true;
		elseif(isset($recMap['dbpk']) && $recMap['dbpk']) $loadRecord = true;
		elseif(isset($recMap['catalognumber']) && $recMap['catalognumber']) $loadRecord = true;
		elseif(isset($recMap['othercatalognumbers']) && $recMap['othercatalognumbers']) $loadRecord = true;
		elseif(isset($recMap['occurrenceid']) && $recMap['occurrenceid']) $loadRecord = true;
		elseif(isset($recMap['recordedby']) && $recMap['recordedby']) $loadRecord = true;
		elseif(isset($recMap['eventdate']) && $recMap['eventdate']) $loadRecord = true;
		elseif(isset($recMap['sciname']) && $recMap['sciname']) $loadRecord = true;
		if($loadRecord){
			//Remove institution and collection codes when they match what is in omcollections
			if(array_key_exists('institutioncode',$recMap) && $recMap['institutioncode'] == $this->collMetadataArr["institutioncode"]){
				unset($recMap['institutioncode']);
			}
			if(array_key_exists('collectioncode',$recMap) && $recMap['collectioncode'] == $this->collMetadataArr["collectioncode"]){
				unset($recMap['collectioncode']);
			}

			//Do some cleaning on the dbpk; remove leading and trailing whitespaces and convert multiple spaces to a single space
			if(array_key_exists('dbpk',$recMap) && $recMap['dbpk']){
				$recMap['dbpk'] = trim(preg_replace('/\s\s+/',' ',$recMap['dbpk']));
			}
			else{
				if($this->collMetadataArr["managementtype"] == 'Live Data' || $this->uploadType == $this->SKELETAL){
					//If dbpk does not exist, set a temp value
					if(!isset($recMap['dbpk']) || !$recMap['dbpk']) $recMap['dbpk'] = 'SYMBDBPK-'.$this->dbpkCnt;
					$this->dbpkCnt++;
				}
			}

			//Set processingStatus to value defined by loader
			if($this->processingStatus){
				$recMap['processingstatus'] = $this->processingStatus;
			}
			elseif($this->uploadType == $this->SKELETAL){
				$recMap['processingstatus'] = 'unprocessed';
			}

			//Temporarily code until Specify output UUID as occurrenceID
			if($this->sourceDatabaseType == 'specify' && (!isset($recMap['occurrenceid']) || !$recMap['occurrenceid'])){
				if(strlen($recMap['dbpk']) == 36) $recMap['occurrenceid'] = $recMap['dbpk'];
			}

			if(!array_key_exists('basisofrecord',$recMap) || !$recMap['basisofrecord']){
				$recMap['basisofrecord'] = ($this->collMetadataArr["colltype"]=="Preserved Specimens"?'PreservedSpecimen':'HumanObservation');
			}

			$this->buildPaleoJSON($recMap);
			$this->buildMaterialSampleJSON($recMap);
			$sqlFragments = $this->getSqlFragments($recMap,$this->fieldMap);
			if($sqlFragments){
				$sql = 'INSERT INTO uploadspectemp(collid'.$sqlFragments['fieldstr'].') VALUES('.$this->collId.$sqlFragments['valuestr'].')';
				if($this->conn->query($sql)){
					$this->transferCount++;
					if($this->transferCount%1000 == 0) $this->outputMsg('<li style="margin-left:10px;">Count: '.$this->transferCount.'</li>');
					//$this->outputMsg("<li>");
					//$this->outputMsg("Appending/Replacing observation #".$this->transferCount.": SUCCESS");
					//$this->outputMsg("</li>");
				}
				else{
					$sql = Encoding::fixUTF8($sql);
					if(!$this->conn->query($sql)){
						$this->outputMsg('<li>FAILED adding record #'.$this->transferCount.'</li>');
						$this->outputMsg('<li style="margin-left:10px;">Error: '.$this->conn->error.'</li>');
						$this->outputMsg('<li style="margin:0px 0px 10px 10px;">SQL: '.$sql.'</li>');
					}
				}
			}
		}
	}

	private function buildPaleoJSON(&$recMap){
		if($this->paleoSupport){
			$paleoTermArr = $this->getPaleoTerms();
			$paleoArr = array();
			foreach($paleoTermArr as $fieldName){
				$k = strtolower($fieldName);
				if(isset($recMap[$k])){
					if($recMap[$k] !== '') $paleoArr[substr($k,6)] = $recMap[$k];
					unset($recMap[$k]);
				}
			}
			if($paleoArr) $recMap['paleoJSON'] = json_encode($paleoArr);
		}
	}

	private function buildMaterialSampleJSON(&$recMap){
		if($this->materialSampleSupport){
			$msTermArr = $this->getMaterialSampleTerms();
			$msArr = array();
			foreach($msTermArr as $fieldName){
				if(isset($recMap[$fieldName])){
					if($recMap[$fieldName] !== '') $msArr[substr($fieldName,15)] = $recMap[$fieldName];
					unset($recMap[$fieldName]);
				}
			}
			if($msArr) $recMap['materialSampleJSON'] = json_encode($msArr);
		}
	}

	protected function loadIdentificationRecord($recMap){
		if($recMap){
			//coreId should go into dbpk
			if(isset($recMap['coreid']) && !isset($recMap['dbpk'])){
				$recMap['dbpk'] = $recMap['coreid'];
				unset($recMap['coreid']);
			}

			//Import record only if required fields have data (coreId and a scientificName)
			if(isset($recMap['dbpk']) && $recMap['dbpk'] && (isset($recMap['sciname']) || isset($recMap['genus']))){

				//Do some cleaning
				//Populate sciname if null
				if(!array_key_exists('sciname',$recMap) || !$recMap['sciname']){
					if(array_key_exists("genus",$recMap)){
						//Build sciname from individual units supplied by source
						$sciName = $recMap["genus"];
						if(array_key_exists("specificepithet",$recMap) && $recMap["specificepithet"]) $sciName .= " ".$recMap["specificepithet"];
						if(array_key_exists("taxonrank",$recMap) && $recMap["taxonrank"]) $sciName .= " ".$recMap["taxonrank"];
						if(array_key_exists("infraspecificepithet",$recMap) && $recMap["infraspecificepithet"]) $sciName .= " ".$recMap["infraspecificepithet"];
						$recMap['sciname'] = trim($sciName);
					}
				}
				//Remove fields that are not in the omoccurdetermination tables
				unset($recMap['genus']);
				unset($recMap['specificepithet']);
				unset($recMap['taxonrank']);
				unset($recMap['infraspecificepithet']);
				//Try to get author, if it's not there
				if(!array_key_exists('scientificnameauthorship',$recMap) || !$recMap['scientificnameauthorship']){
					//Parse scientific name to see if it has author imbedded
					$parsedArr = OccurrenceUtilities::parseScientificName($recMap['sciname'],$this->conn);
					if(array_key_exists('author',$parsedArr)){
						$recMap['scientificnameauthorship'] = $parsedArr['author'];
						//Load sciname from parsedArr since if appears that author was embedded
						$recMap['sciname'] = trim($parsedArr['unitname1'].' '.$parsedArr['unitname2'].' '.$parsedArr['unitind3'].' '.$parsedArr['unitname3']);
					}
				}

				if((isset($recMap['identifiedby']) && $recMap['identifiedby']) || (isset($recMap['dateidentified']) && $recMap['dateidentified']) || (isset($recMap['sciname']) && $recMap['sciname'])){
					if(!isset($recMap['identifiedby']) || !$recMap['identifiedby']) $recMap['identifiedby'] = 'not specified';
					if(!isset($recMap['dateidentified']) || !$recMap['dateidentified']) $recMap['dateidentified'] = 'not specified';
					$sqlFragments = $this->getSqlFragments($recMap,$this->identFieldMap);
					if($sqlFragments){
						$sql = 'INSERT INTO uploaddetermtemp(collid'.$sqlFragments['fieldstr'].') VALUES('.$this->collId.$sqlFragments['valuestr'].')';
						//echo "<div>SQL: ".$sql."</div>";
						if($this->conn->query($sql)){
							$this->identTransferCount++;
							if($this->identTransferCount%1000 == 0) $this->outputMsg('<li style="margin-left:10px;">Count: '.$this->identTransferCount.'</li>');
						}
						else{
							$outStr = '<li>FAILED adding identification history record #'.$this->identTransferCount.'</li>';
							$outStr .= '<li style="margin-left:10px;">Error: '.$this->conn->error.'</li>';
							$outStr .= '<li style="margin:0px 0px 10px 10px;">SQL: '.$sql.'</li>';
							$this->outputMsg($outStr);
						}
					}
				}
			}
		}
	}

	protected function loadImageRecord($recMap){
		if($recMap){
			//Test images
			$testUrl = '';
			if(isset($recMap['originalurl']) && $recMap['originalurl'] && substr($recMap['originalurl'],0,10) != 'processing'){
				$testUrl = $recMap['originalurl'];
			}
			elseif(isset($recMap['url']) && $recMap['url'] && $recMap['url'] != 'empty'){
				$testUrl = $recMap['url'];
			}
			else{
				//Abort, no images avaialble
				return false;
			}
			//if(strtolower(substr($testUrl,0,4)) != 'http') return false;
			if(stripos($testUrl,'.dng') || stripos($testUrl,'.tif')) return false;
			$skipFormats = array('image/tiff','image/dng','image/bmp','text/html','application/xml','application/pdf','tif','tiff','dng','html','pdf');
			$allowedFormats = array('image/jpeg','image/gif','image/png');
			$imgFormat = $this->imgFormatDefault;
			if(isset($recMap['format']) && $recMap['format']){
				$imgFormat = strtolower($recMap['format']);
				if(in_array($imgFormat, $skipFormats)) return false;
			}
			else{
				$ext = strtolower(substr(strrchr($testUrl, '.'), 1));
				if(strpos($testUrl,'?')) $ext = substr($ext, 0, strpos($ext,'?'));
				if($ext== 'gif') $imgFormat = 'image/gif';
				if($ext== 'png') $imgFormat = 'image/png';
				if($ext== 'jpg') $imgFormat = 'image/jpeg';
				elseif($ext== 'jpeg') $imgFormat = 'image/jpeg';
				if($imgFormat === ''){
					if($this->imgFormatDefault) $imgFormat = $this->imgFormatDefault;
					else {
						$imgFormat = $this->getMimeType($testUrl);
						if($imgFormat) $this->imgFormatDefault = $imgFormat;
						else $this->imgFormatDefault = false;
					}
					//if(!in_array(strtolower($imgFormat), $allowedFormats)) return false;
				}
			}
			if($imgFormat) $recMap['format'] = $imgFormat;

			if($this->verifyImageUrls){
				if(!$this->urlExists($testUrl)){
					$this->outputMsg('<li style="margin-left:20px;">Bad url: '.$testUrl.'</li>');
					return false;
				}
			}

			if(strpos($testUrl,'inaturalist.org') || strpos($testUrl,'inaturalist-open-data')){
				//Special processing for iNaturalist imports
				if(strpos($testUrl,'/original.')){
					$recMap['originalurl'] = $testUrl;
					$recMap['url'] = str_replace('/original.', '/medium.', $testUrl);
					$recMap['thumbnailurl'] = str_replace('/original.', '/small.', $testUrl);
				}
				elseif(strpos($testUrl,'/medium.')){
					$recMap['url'] = $testUrl;
					$recMap['thumbnailurl'] = str_replace('/medium.', '/small.', $testUrl);
					$recMap['originalurl'] = str_replace('/medium.', '/original.', $testUrl);
				}
			}

			if(!isset($recMap['url'])) $recMap['url'] = '';
			if(!array_key_exists('sourceidentifier', $recMap) && in_array('sourceidentifier',$this->imageSymbFields)){
				$url = $recMap['originalurl'];
				if(!$url) $url = $recMap['url'];
				if(preg_match('=/([^/?*;:{}\\\\]+\.[jpegpn]{3,4}$)=', $url, $m)){
					$recMap['sourceidentifier'] = $m[1];
				}
			}
			$sqlFragments = $this->getSqlFragments($recMap,$this->imageFieldMap);
			if($sqlFragments){
				$sql = 'INSERT INTO uploadimagetemp(collid'.$sqlFragments['fieldstr'].') VALUES('.$this->collId.$sqlFragments['valuestr'].')';
				if($this->conn->query($sql)){
					$this->imageTransferCount++;
					$repInt = 1000;
					if($this->verifyImageUrls) $repInt = 100;
					if($this->imageTransferCount%$repInt == 0) $this->outputMsg('<li style="margin-left:10px;">'.$this->imageTransferCount.' images processed</li>');
				}
				else{
					$this->outputMsg("<li>FAILED adding image record #".$this->imageTransferCount."</li>");
					$this->outputMsg("<li style='margin-left:10px;'>Error: ".$this->conn->error."</li>");
					$this->outputMsg("<li style='margin:0px 0px 10px 10px;'>SQL: $sql</li>");
				}
			}
		}
	}

	private function getSqlFragments($recMap,$fieldMap){
		$hasValue = false;
		$sqlFields = '';
		$sqlValues = '';
		foreach($recMap as $symbField => $valueStr){
			if(substr($symbField,0,8) != 'unmapped' && $symbField != 'collid'){
				$sqlFields .= ','.$symbField;
				$valueStr = $this->encodeString($valueStr);
				$valueStr = $this->cleanInStr($valueStr);
				if($valueStr) $hasValue = true;
				//Load data
				$type = '';
				$size = 0;
				if(array_key_exists($symbField,$fieldMap)){
					if(array_key_exists('type',$fieldMap[$symbField])){
						$type = $fieldMap[$symbField]["type"];
					}
					if(array_key_exists('size',$fieldMap[$symbField])){
						$size = $fieldMap[$symbField]["size"];
					}
				}
				switch($type){
					case "numeric":
						if(is_numeric($valueStr)){
							if($symbField == 'coordinateuncertaintyinmeters' && $valueStr < 0) $valueStr = abs($valueStr);
							$sqlValues .= ",".$valueStr;
						}
						elseif(is_numeric(str_replace(',',"",$valueStr))){
							$sqlValues .= ",".str_replace(',',"",$valueStr);
						}
						else{
							$sqlValues .= ",NULL";
						}
						break;
					case "decimal":
						if(strpos($valueStr,',')){
							$sqlValues = str_replace(',','',$valueStr);
						}
						if($valueStr && $size && strpos($size,',') !== false){
							$tok = explode(',',$size);
							$m = $tok[0];
							$d = $tok[1];
							if($m && $d){
								$dec = substr($valueStr,strpos($valueStr,'.'));
								if(strlen($dec) > $d){
									$valueStr = round($valueStr,$d);
								}
								$rawLen = strlen(str_replace(array('-','.'),'',$valueStr));
								if($rawLen > $m){
									if(strpos($valueStr,'.') !== false){
										$decLen = strlen(substr($valueStr,strpos($valueStr,'.')));
										if($decLen < ($rawLen - $m)){
											$valueStr = '';
										}
										else{
											$valueStr = round($valueStr,$decLen - ($rawLen - $m));
										}
									}
									else{
										$valueStr = '';
									}
								}
							}
						}
						if(is_numeric($valueStr)){
							$sqlValues .= ",".$valueStr;
						}
						else{
							$sqlValues .= ",NULL";
						}
						break;
					case "date":
						$dateStr = OccurrenceUtilities::formatDate($valueStr);
						if($dateStr){
							$sqlValues .= ',"'.$dateStr.'"';
						}
						else{
							$sqlValues .= ",NULL";
						}
						break;
					default:	//string
						if($size && strlen($valueStr) > $size){
							$valueStr = substr($valueStr,0,$size);
						}
						if(substr($valueStr,-1) == "\\"){
							$valueStr = rtrim($valueStr,"\\");
						}
						if($valueStr){
							$sqlValues .= ',"'.$valueStr.'"';
						}
						else{
							$sqlValues .= ",NULL";
						}
				}
			}
		}
		if(!$hasValue) return false;
		return array('fieldstr' => $sqlFields,'valuestr' => $sqlValues);
	}

	public function getTransferCount(){
		if(!$this->transferCount) $this->setTransferCount();
		return $this->transferCount;
	}

	protected function setTransferCount(){
		if($this->collId){
			$sql = 'SELECT count(*) AS cnt FROM uploadspectemp WHERE (collid IN('.$this->collId.')) ';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->transferCount = $row->cnt;
			}
			$rs->free();
		}
	}

	public function getIdentTransferCount(){
		if(!$this->identTransferCount) $this->setIdentTransferCount();
		return $this->identTransferCount;
	}

	protected function setIdentTransferCount(){
		if($this->collId){
			$sql = 'SELECT count(*) AS cnt FROM uploaddetermtemp WHERE (collid IN('.$this->collId.'))';
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$this->identTransferCount = $row->cnt;
			}
			$rs->free();
		}
	}

	private function getImageTransferCount(){
		if(!$this->imageTransferCount) $this->setImageTransferCount();
		return $this->imageTransferCount;
	}

	protected function setImageTransferCount(){
		if($this->collId){
			$sql = 'SELECT count(*) AS cnt FROM uploadimagetemp WHERE (collid IN('.$this->collId.'))';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$this->imageTransferCount = $r->cnt;
			}
			else{
				$this->outputMsg('<li style="margin-left:20px;">ERROR setting image upload count: '.$this->conn->error.'</li> ');
			}
			$rs->free();
		}
	}

	protected function setUploadTargetPath(){
		$tPath = $GLOBALS['TEMP_DIR_ROOT'];
		if(!$tPath){
			$tPath = ini_get('upload_tmp_dir');
		}
		if(!$tPath){
			$tPath = $GLOBALS['SERVER_ROOT'].'/temp';
		}
		if(substr($tPath,-1) != '/' && substr($tPath,-1) != '\\'){
			$tPath .= '/';
		}
		if(file_exists($tPath.'downloads')){
			$tPath .= 'downloads/';
		}
		$this->uploadTargetPath = $tPath;
	}

	public function addFilterCondition($columnName, $condition, $value){
		if($columnName && ($value || $condition == 'ISNULL' || $condition == 'NOTNULL')){
			$this->filterArr[strtolower($columnName)][$condition][] = strtolower(trim($value));
		}
	}

	public function setIncludeIdentificationHistory($boolIn){
		$this->includeIdentificationHistory = $boolIn;
	}

	public function setIncludeImages($boolIn){
		$this->includeImages = $boolIn;
	}

	public function setObserverUid($id){
		if(is_numeric($id)) $this->observerUid = $id;
	}

	public function setMatchCatalogNumber($match){
		$this->matchCatalogNumber = $match;
	}

	public function setMatchOtherCatalogNumbers($match){
		$this->matchOtherCatalogNumbers = $match;
	}

	public function setVerifyImageUrls($v){
		$this->verifyImageUrls = $v;
	}

	public function setProcessingStatus($s){
		$this->processingStatus = $s;
	}

	public function setSourceCharset($cs){
		$this->sourceCharset = $cs;
	}

	public function setSourceDatabaseType($type){
		$this->sourceDatabaseType = $type;
	}

	public function getSourceArr(){
		return $this->sourceArr;
	}

	private function getPaleoTerms(){
		$paleoTermArr = array_merge($this->getPaleoDwcTerms(),$this->getPaleoSymbTerms());
		sort($paleoTermArr);
		return $paleoTermArr;
	}

	private function getPaleoDwcTerms(){
		$paleoTermArr = array('paleo-earliesteonorlowesteonothem','paleo-latesteonorhighesteonothem','paleo-earliesteraorlowesterathem',
			'paleo-latesteraorhighesterathem','paleo-earliestperiodorlowestsystem','paleo-latestperiodorhighestsystem','paleo-earliestepochorlowestseries',
			'paleo-latestepochorhighestseries','paleo-earliestageorloweststage','paleo-latestageorhigheststage','paleo-lowestbiostratigraphiczone','paleo-highestbiostratigraphiczone');
		return $paleoTermArr;
	}

	private function getPaleoSymbTerms(){
		$paleoTermArr = array('paleo-geologicalcontextid','paleo-lithogroup','paleo-formation','paleo-member','paleo-bed','paleo-eon','paleo-era','paleo-period','paleo-epoch',
			'paleo-earlyinterval','paleo-lateinterval','paleo-absoluteage','paleo-storageage','paleo-stage','paleo-localstage','paleo-biota','paleo-biostratigraphy',
			'paleo-taxonenvironment','paleo-lithology','paleo-stratremarks','paleo-lithdescription','paleo-element','paleo-slideproperties');
		return $paleoTermArr;
	}

	private function getMaterialSampleTerms(){
		$msTermArr = array('materialSample-materialSampleID','materialSample-sampleType','materialSample-catalogNumber','materialSample-sampleCondition','materialSample-disposition',
			'materialSample-preservationType','materialSample-preparationProcess','materialSample-preparationDate','materialSample-individualCount',
			'materialSample-sampleSize','materialSample-storageLocation','materialSample-remarks');
		//Get dynamic fields
		$sql = 'SELECT t.term FROM ctcontrolvocab v INNER JOIN ctcontrolvocabterm t ON v.cvID = t.cvID WHERE v.tableName = "ommaterialsampleextended" AND v.fieldName = "fieldName"';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$msTermArr[] = 'materialSample-'.$r->term;
		}
		$rs->free();
		return $msTermArr;
	}

	public function getObserverUidArr(){
		$retArr = array();
		if($this->collId){
			$sql = 'SELECT u.uid, CONCAT_WS(", ",u.lastname, u.firstname) as user '.
				'FROM users u INNER JOIN userroles r ON u.uid = r.uid '.
				'WHERE r.tablepk = '.$this->collId.' AND r.role IN("CollEditor","CollAdmin")';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->uid] = $r->user;
			}
			$rs->free();
		}
		asort($retArr);
		return $retArr;
	}

	//Misc functions
	protected function copyChunked($from, $to){
		/*
		 * If transfers fail for large files, you may need to increase following php.ini variables:
		 * 		max_input_time, max_execution_time, default_socket_timeout
		 */

		//2 meg at a time
		$buffer_size = 2097152;		//1048576;
		$byteCount = 0;
		$fin = fopen($from, "rb");
		$fout = fopen($to, "w");
		if($fin && $fout){
			while(!feof($fin)) {
				$byteCount += fwrite($fout, fread($fin, $buffer_size));
			}
		}
		fclose($fin);
		fclose($fout);
		return $byteCount;
	}

	private function getMimeType($url){
		if(!strstr($url, "http")){
			$url = "http://".$url;
		}
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_NOBODY, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt($handle, CURLOPT_TIMEOUT, 3);
		curl_exec($handle);
		return curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
	}

	protected function urlExists($url) {
		$exists = false;
		if(!strstr($url, "http")){
			$url = "http://".$url;
		}
		if(function_exists('curl_init')){
			// Version 4.x supported
			$handle = curl_init($url);
			if (false === $handle){
				$exists = false;
			}
			curl_setopt($handle, CURLOPT_HEADER, false);
			curl_setopt($handle, CURLOPT_FAILONERROR, true);
			curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox
			curl_setopt($handle, CURLOPT_NOBODY, true);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
			$exists = curl_exec($handle);
			curl_close($handle);
		}

		if(!$exists && file_exists($url)){
			$exists = true;
		}

		//One more  check
		if(!$exists){
			$exists = (@fclose(@fopen($url,"r")));
		}
		return $exists;
	}

	protected function encodeString($inStr){
		$retStr = $inStr;

		if($inStr){
			if($this->targetCharset == 'UTF-8'){
				if($this->sourceCharset){
					if($this->sourceCharset == 'ISO-8859-1') $retStr = utf8_encode($inStr);
					elseif($this->sourceCharset == 'MAC'){
						$retStr = iconv('macintosh', 'UTF-8', $inStr);
						//$retStr = mb_convert_encoding($inStr,"UTF-8","auto");
					}
				}
				else{
					if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) == 'ISO-8859-1'){
						$retStr = utf8_encode($inStr);
						//$retStr = iconv("ISO-8859-1//TRANSLIT","UTF-8",$inStr);
					}
				}
			}
			elseif($this->targetCharset == "ISO-8859-1"){
				if($this->sourceCharset){
					if($this->sourceCharset == 'UTF-8') $retStr = utf8_decode($inStr);
					elseif($this->sourceCharset == 'MAC'){
						$retStr = iconv('macintosh', 'ISO-8859-1', $inStr);
						//$retStr = mb_convert_encoding($inStr,"ISO-8859-1","auto");
					}
				}
				else{
					if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') == "UTF-8"){
						$retStr = utf8_decode($inStr);
					}
				}
			}

			//Get rid of UTF-8 curly smart quotes and dashes
			$badwordchars=array("\xe2\x80\x98", // left single quote
					"\xe2\x80\x99", // right single quote
					"\xe2\x80\x9c", // left double quote
					"\xe2\x80\x9d", // right double quote
					"\xe2\x80\x94", // em dash
					"\xe2\x80\xa6" // elipses
			);
			$fixedwordchars=array("'", "'", '"', '"', '-', '...');
			$inStr = str_replace($badwordchars, $fixedwordchars, $inStr);
		}
		return $retStr;
	}
}
?>