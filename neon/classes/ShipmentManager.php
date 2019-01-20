<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class ShipmentManager{

	private $conn;
	private $shipmentPK;
	private $shipmentArr = array();
	private $uploadFileName;
	private $fieldMap = array();
	private $sourceArr = array();
	private $stateArr = array();
	private $sampleClassArr = array();
	private $errorStr;

 	public function __construct(){
 		$this->conn = MySQLiConnectionFactory::getCon("write");
 	}

 	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	public function getShipmentArr($postArr = null){
		$retArr = array();
		if(!$this->shipmentArr){
			$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.domainID, s.dateShipped, s.shippedFrom, s.senderID, s.destinationFacility, s.sentToID, s.shipmentService, s.shipmentMethod, '.
				's.trackingNumber, s.receivedDate, s.receivedBy, s.notes AS shipmentNotes, CONCAT_WS(", ", u.lastname, u.firstname) AS importUser,
				CONCAT_WS(", ", u2.lastname, u2.firstname) AS checkinUser, s.checkinTimestamp, CONCAT_WS(", ", u3.lastname, u3.firstname) AS modifiedUser, s.initialtimestamp AS ts '.
				'FROM NeonShipment s INNER JOIN users u ON s.importUid = u.uid '.
				'LEFT JOIN users u2 ON s.checkinUid = u2.uid '.
				'LEFT JOIN users u3 ON s.modifiedByUid = u3.uid '.
				'LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk ';
			if($this->shipmentPK){
				$sql .= 'WHERE (s.shipmentPK = '.$this->shipmentPK.') ';
			}
			elseif($postArr){
				//Set search criteria
				$sqlWhere = '';
				if(isset($postArr['shipmentID']) && $postArr['shipmentID']){
					$sqlWhere .= 'AND (s.shipmentID = "'.$postArr['shipmentID'].'") ';
				}
				if(isset($postArr['domainID']) && $postArr['domainID']){
					$sqlWhere .= 'AND (s.domainID = "'.$postArr['domainID'].'") ';
				}
				if(isset($postArr['dateShippedStart']) && $postArr['dateShippedStart']){
					$sqlWhere .= 'AND (s.dateShipped > "'.$postArr['dateShippedStart'].'") ';
				}
				if(isset($postArr['dateShippedEnd']) && $postArr['dateShippedEnd']){
					$sqlWhere .= 'AND (s.dateShipped < "'.$postArr['dateShippedEnd'].'") ';
				}
				if(isset($postArr['senderID']) && $postArr['senderID']){
					$sqlWhere .= 'AND (s.senderID = "'.$postArr['senderID'].'") ';
				}
				if(isset($postArr['trackingNumber']) && $postArr['trackingNumber']){
					$sqlWhere .= 'AND (s.trackingNumber = "'.$postArr['trackingNumber'].'") ';
				}
				if(isset($postArr['importedUid']) && $postArr['importedUid']){
					$sqlWhere .= 'AND ((s.importedByUid = "'.$postArr['importedUid'].'") OR (s.modifiedByUid = "'.$postArr['importedUid'].'")) ';
				}
				if(isset($postArr['checkinUid']) && $postArr['checkinUid']){
					$sqlWhere .= 'AND ((s.checkinUid = "'.$postArr['checkinUid'].'") OR (m.checkinUid = "'.$postArr['checkinUid'].'")) ';
				}
				if(isset($postArr['sampleID']) && $postArr['sampleID']){
					$sqlWhere .= 'AND (m.sampleID LIKE "%'.$postArr['sampleID'].'"%) ';
				}
				if(isset($postArr['sampleCode']) && $postArr['sampleCode']){
					$sqlWhere .= 'AND (m.sampleCode = "'.$postArr['sampleCode'].'") ';
				}
				if(isset($postArr['sampleClass']) && $postArr['sampleClass']){
					$sqlWhere .= 'AND (m.sampleClass LIKE "%'.$postArr['sampleClass'].'"%) ';
				}
				if(isset($postArr['taxonID']) && $postArr['taxonID']){
					$sqlWhere .= 'AND (m.taxonID = "'.$postArr['taxonID'].'") ';
				}
				if(isset($postArr['namedLocation']) && $postArr['namedLocation']){
					$sqlWhere .= 'AND (m.namedLocation = "'.$postArr['namedLocation'].'") ';
				}
				if(isset($postArr['collectDateStart']) && $postArr['collectDateStart']){
					$sqlWhere .= 'AND (m.collectDate > "'.$postArr['collectDateStart'].'") ';
				}
				if(isset($postArr['collectDateEnd']) && $postArr['collectDateEnd']){
					$sqlWhere .= 'AND (m.collectDate < "'.$postArr['collectDateEnd'].'") ';
				}
				if($sqlWhere) $sql .= 'WHERE '.subStr($sqlWhere, 3);
			}
			//echo '<div>'.$sql.'</div>';
			$rs = $this->conn->query($sql);
			$headerArr = array();
			while($r = $rs->fetch_assoc()){
				if(!$headerArr){
					$headerArr = array_keys($r);
					unset($headerArr[array_search('shipmentPK', $headerArr)]);
				}
				foreach($headerArr as $colName){
					$retArr[$r['shipmentPK']][$colName] = $r[$colName];
				}
			}
			$rs->free();
			if($this->shipmentPK) $this->shipmentArr = $retArr[$this->shipmentPK];
		}
		return $retArr;
	}

	public function getSampleCount(){
		$retArr = array();
		//Get total sample count
		$sql = 'SELECT COUNT(samplepk) AS cnt FROM NeonSample WHERE (shipmentPK = '.$this->shipmentPK.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['all'] = $r->cnt;
		}
		$rs->free();
		//Get sample count not yet checked-in
		$sql = 'SELECT COUNT(samplepk) AS cnt FROM NeonSample WHERE (shipmentPK = '.$this->shipmentPK.') AND (checkinUid IS NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[0] = $r->cnt;
		}
		$rs->free();
		//Get count of samples not yet imported
		$sql = 'SELECT COUNT(s.samplepk) AS cnt '.
			'FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
			'WHERE (s.shipmentPK = '.$this->shipmentPK.') AND (o.occid IS NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[1] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getSampleArr(){
		$retArr = array();
		$headerArr = array('sampleID','sampleCode','sampleClass','taxonID','individualCount','filterVolume','namedLocation','domainRemarks','collectDate',
			'quarantineStatus','sampleNotes','occid','checkinUser','checkinTimestamp');
		$targetArr = array();
		$sql = 'SELECT s.samplePK, s.sampleID, s.sampleCode, s.sampleClass, s.taxonID, s.individualCount, s.filterVolume, s.namedLocation, s.domainRemarks, '.
			's.collectDate, s.quarantineStatus, s.notes as sampleNotes, CONCAT_WS(", ", u.lastname, u.firstname) as checkinUser, s.checkinTimestamp, s.occid '.
			'FROM NeonSample s LEFT JOIN users u ON s.checkinuid = u.uid '.
			'WHERE s.shipmentPK = '.$this->shipmentPK;
		$rs = $this->conn->query($sql);
		//Pass through recordset to see which fields have at least one value
		while($r = $rs->fetch_assoc()){
			foreach($headerArr as $fieldName){
				if($r[$fieldName] && !in_array($fieldName, $targetArr)) $targetArr[] = $fieldName;
			}
		}
		if(!array_key_exists('checkinTimestamp', $targetArr)){
			$targetArr[] = 'checkinUser';
			$targetArr[] = 'checkinTimestamp';
		}
		$rs->data_seek(0);
		//Grab data for only columns that have data
		while($r = $rs->fetch_assoc()){
			foreach($targetArr as $fieldName){
				$retArr[$r['samplePK']][$fieldName] = $r[$fieldName];
			}
		}
		$rs->free();
		return $retArr;
	}

	//Shipment import functions
	public function uploadManifestFile(){
		$status = false;
		ini_set('auto_detect_line_endings', true);
		//Load file onto server
		$uploadPath = $this->getContentPath().'manifests/';
		if(array_key_exists("uploadfile",$_FILES)){
			$this->uploadFileName = $_FILES['uploadfile']['name'];
			$fullPath = $uploadPath.$_FILES['uploadfile']['name'];
			if(!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadPath.$this->uploadFileName)){
				$this->errorStr = 'ERROR uploading file (code '.$_FILES['uploadfile']['error'].'): ';
				if(!is_writable($uploadPath)){
					$this->errorStr .= 'Target path ('.$uploadPath.') is not writable ';
				}
				return false;
			}
			//If a zip file, unpackage and assume that last or only file is the occurrrence file
			if($this->uploadFileName && substr($this->uploadFileName,-4) == ".zip"){
				$zipFilePath = $uploadPath.$this->uploadFileName;
				$fileName = '';
				$zip = new ZipArchive;
				$res = $zip->open($zipFilePath);
				if($res === TRUE) {
					for($i = 0; $i < $zip->numFiles; $i++) {
						$name = $zip->getNameIndex($i);
						if(substr($name,0,2) != '._'){
							$ext = strtolower(substr(strrchr($name, '.'), 1));
							if($ext == 'csv'){
								$fileName = $name;
								break;
							}
						}
					}
					if($fileName){
						$this->uploadFileName = $fileName;
						$zip->extractTo($uploadPath,$this->uploadFileName);
					}
					else{
						$this->uploadFileName = '';
					}
				}
				else{
					echo 'failed, code:' . $res;
					return false;
				}
				$zip->close();
				unlink($zipFilePath);
			}
		}
		return $status;
	}

	public function analyzeUpload(){
		//Just read first line of file to report what fields will be loaded, ignored, and required fulfilled
		if($this->uploadFileName){
			$fullPath = $this->getContentPath().'manifests/'.$this->uploadFileName;
			$fh = fopen($fullPath,'rb') or die("Can't open file");
			$this->sourceArr = $this->getHeaderArr($fh);
			fclose($fh);
		}
	}

	public function uploadData(){
		if($this->uploadFileName){
			echo '<li>Initiating import from: '.$this->uploadFileName.'</li>';
			$fullPath = $this->getContentPath().'manifests/'.$this->uploadFileName;
			$fh = fopen($fullPath,'rb') or die("Can't open file");
			$headerArr = $this->getHeaderArr($fh);
			$recCnt = 0;
			//Setup record index array
			$indexMap = array();
			foreach($this->fieldMap as $sourceField => $targetField){
				$indexArr = array_keys($headerArr,$sourceField);
				$index = array_shift($indexArr);
				$indexMap[$targetField] = $index;
			}
			echo '<li>Beginning to load records...</li>';
			$shipmentPK = false;
			while($recordArr = fgetcsv($fh)){
				$recMap = Array();
				foreach($indexMap as $targetField => $indexValue){
					$recMap[$targetField] = $recordArr[$indexValue];
				}
				if($shipmentPK === false) $shipmentPK = $this->loadShipmentRecord($recMap);
				if($shipmentPK){
					$this->loadSampleRecord($shipmentPK, $recMap);
					$recCnt++;
				}
				unset($recMap);
			}
			fclose($fh);

			//Delete upload file
			if(file_exists($fullPath)) unlink($fullPath);
			echo '<li>Complete!!!</li>';
		}
		else{
			$this->outputMsg('<li>File Upload FAILED: unable to locate file</li>');
		}
	}

	public function loadShipmentRecord($recArr){
		$shipmentPK = 0;
		$sql = 'INSERT INTO NeonShipment(shipmentID, domainID, dateShipped, shippedFrom,senderID, destinationFacility, sentToID, shipmentService, shipmentMethod, trackingNumber, importUid) '.
			'VALUES("'.$this->cleanInStr($recArr['shipmentid']).'","'.$this->cleanInStr($recArr['domainid']).'","'.$this->cleanInStr($this->formatDate($recArr['dateshipped'])).'",'.
			(isset($recArr['shippedfrom'])?'"'.$this->cleanInStr($recArr['shippedfrom']).'"':'NULL').',"'.$this->cleanInStr($recArr['senderid']).'",'.
			(isset($recArr['destinationfacility'])?'"'.$this->cleanInStr($recArr['destinationfacility']).'"':'NULL').','.
			(isset($recArr['senttoid'])?'"'.$this->cleanInStr($recArr['senttoid']).'"':'NULL').',"'.$this->cleanInStr($recArr['shipmentservice']).'","'.$this->cleanInStr($recArr['shipmentmethod']).'",'.
			(isset($recArr['trackingnumber'])?'"'.$this->cleanInStr($recArr['trackingnumber']).'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
		//echo '<div>'.$sql.'</div>';
		if($this->conn->query($sql)){
			$shipmentPK = $this->conn->insert_id;
			echo '<li>Shipment record loaded...</li>';
		}
		else{
			if($this->conn->errno == 1062){
				$sql = 'SELECT shipmentpk FROM NeonShipment WHERE shipmentID = "'.$this->cleanInStr($recArr['shipmentid']).'"';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$shipmentPK = $r->shipmentpk;
				}
				$rs->free();
				echo '<li style="margin-left:15px"><span style="color:orange">NOTICE:</span>Shipment record with that shipmentID already exists (shipmentPK: '.$shipmentPK.')...</li>';
			}
			else{
				echo '<li style="margin-left:15px"><span style="color:red">ERROR</span> loading shipment record (errNo: '.$this->conn->errno.'): '.$this->conn->error.'</li>';
				//echo '<li style="margin-left:15px">SQL: '.$sql.'</li>';
				return 0;
			}
		}
		return $shipmentPK;
	}

	private function loadSampleRecord($shipmentPK, $recArr){
		$sql = 'INSERT INTO NeonSample(shipmentPK, sampleID, sampleCode, sampleClass, taxonID, individualCount, filterVolume, namedlocation, domainremarks, collectdate, quarantineStatus) '.
			'VALUES('.$shipmentPK.',"'.$this->cleanInStr($recArr['sampleid']).'",'.(isset($recArr['samplecode'])&&$recArr['samplecode']?'"'.$this->cleanInStr($recArr['samplecode']).'"':'NULL').',"'.
		$this->cleanInStr($recArr['sampleclass']).'",'.(isset($recArr['taxonid'])&&$recArr['taxonid']?'"'.$this->cleanInStr($recArr['taxonid']).'"':'NULL').','.
		(isset($recArr['individualcount'])&&$recArr['individualcount']?'"'.$this->cleanInStr($recArr['individualcount']).'"':'NULL').','.
		(isset($recArr['filtervolume'])&&$recArr['filtervolume']?'"'.$this->cleanInStr($recArr['filtervolume']).'"':'NULL').',"'.
		$this->cleanInStr($recArr['namedlocation']).'",'.(isset($recArr['domainremarks'])&&$recArr['domainremarks']?'"'.$this->cleanInStr($recArr['domainremarks']).'"':'NULL').',"'.
		$this->cleanInStr($this->formatDate($recArr['collectdate'])).'",'.(isset($recArr['quarantinestatus'])&&$recArr['quarantinestatus']?'"'.$this->cleanInStr($recArr['quarantinestatus']).'"':'NULL').')';
		if($this->conn->query($sql)){
			echo '<li style="margin-left:15px">Sample record '.$recArr['sampleid'].' loaded...</li>';
		}
		else{
			if($this->conn->errno == 1062){
				echo '<li style="margin-left:15px"><span style="color:orange">NOTICE:</span> Sample record '.$recArr['sampleid'].' previously uploaded...</li>';
			}
			else{
				echo '<li style="margin-left:15px"><span style="color:red">ERROR</span> loading sample record: '.$this->conn->error.'</li>';
				echo '<li style="margin-left:15px">SQL: '.$sql.'</li>';
			}
			return $this->conn->affected_rows;
		}
		return true;
	}

	private function getHeaderArr($fHandler){
		$retArr = array();
		$headerArr = fgetcsv($fHandler);
		foreach($headerArr as $field){
			$fieldStr = strtolower(trim($field));
			if($fieldStr){
				$retArr[] = $fieldStr;
			}
		}
		return $retArr;
	}

	//Check-in functions
	public function checkinShipment($postArr){
		$sql = 'UPDATE NeonShipment '.
			'SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), '.
			'receivedDate = '.($postArr['receivedDate']?'"'.$postArr['receivedDate'].' '.$postArr['receivedTime'].'"':'NULL').', '.
			'receivedBy = '.($postArr['receivedBy']?'"'.$postArr['receivedBy'].'"':'NULL').', '.
			'notes = '.($postArr['notes']?'CONCAT_WS("; ",notes,"'.$postArr['notes'].'")':'NULL').' '.
			'WHERE (shipmentpk = '.$this->shipmentPK.') AND (checkinUid IS NULL)';
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR checking-in shipment: '.$this->conn->error;
			return false;
		}
		return true;
	}

	public function getDeliveryArr(){
		$retArr = array();
		if($this->shipmentArr['shipmentService']){
			if($this->shipmentArr['shipmentService'] == 'FedEx') $retArr = $this->getFedExDeliveryStats();
			//elseif($this->shipmentArr['shipmentService'] == '') $retArr = $this->;
		}
		if(!isset($retArr['receivedBy']) || !$retArr['receivedBy']) $retArr['receivedBy'] = $GLOBALS['USERNAME'];
		if(!isset($retArr['receivedDate']) || !$retArr['receivedDate']){
			$retArr['receivedDate'] = date('Y-m-d');
			$retArr['receivedTime'] = date('H:i:s');
		}
		return $retArr;
	}

	private function getFedExDeliveryStats(){
		$retArr = array();
		$trackingNumber = $this->shipmentArr['trackingNumber'];

		$retArr['receivedBy'] = '';
		$retArr['receivedDate'] = '';
		return $retArr;
	}

	public function checkinSample($sampleID){
		if($this->shipmentPK && $sampleID){
			$samplePK = 0;
			$sql = 'SELECT samplePK FROM NeonSample WHERE (shipmentpk = '.$this->shipmentPK.') AND (checkinTimestamp IS NULL) AND (sampleID = "'.$this->cleanInStr($sampleID).'") ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$samplePK = $r->samplePK;
			}
			$rs->free();
			if($samplePK){
				$sqlUpdate = 'UPDATE NeonSample SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now() WHERE (samplePK = "'.$samplePK.'") ';
				if(!$this->conn->query($sqlUpdate)){
					$this->errorStr = 'ERROR checking-in NEON sample: '.$this->conn->error;
					return 0;
				}
			}
		}
		return $samplePK;
	}

	public function batchCheckinSamples($postArr){
		if($this->shipmentPK){
			$pkArr = $postArr['scbox'];
			if($pkArr){
				$sql = 'UPDATE NeonSample SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now() '.
					'WHERE (shipmentpk = '.$this->shipmentPK.') AND (samplePK IN('.implode(',', $pkArr).')) AND (checkinTimestamp IS NULL)';
				if(!$this->conn->query($sql)){
					$this->errorStr = 'ERROR batch checking-in samples: '.$this->conn->error;
					return false;
				}
				return true;
			}
		}
		return false;
	}

	//Shipment and sample edit functions
	public function editComment($inStr){
		if($this->shipmentPK){
			$sql = 'UPDATE NeonShipment SET notes = CONCAT_WS("; ", notes, "'.$this->cleanInStr($inStr).'") WHERE (shipmentpk = '.$this->shipmentPK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR updating comments: '.$this->conn->error;
				return false;
			}
		}
	}


	//Occurrence harvesting code
	public function batchHarvestOccid($postArr){
		$pkArr = $postArr['scbox'];
		if($this->shipmentPK && $pkArr){
			$this->setStateArr();
			$this->setSampleClassArr();
			$sql = 'SELECT samplePK, sampleID, sampleCode, sampleClass, taxonID, individualCount, filterVolume, namedLocation, collectDate '.
				'FROM NeonSample '.
				'WHERE occid IS NULL AND samplePK IN('.implode(',',$pkArr).')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$sampleArr = array();
				$sampleArr['samplePK'] = $r->samplePK;
				$sampleArr['sampleID'] = $r->sampleID;
				$sampleArr['sampleCode'] = $r->sampleCode;
				$sampleArr['sampleClass'] = $r->sampleClass;
				$sampleArr['taxonID'] = $r->taxonID;
				$sampleArr['individualCount'] = $r->individualCount;
				$sampleArr['filterVolume'] = $r->filterVolume;
				$sampleArr['namedLocation'] = $r->namedLocation;
				$sampleArr['collectDate'] = $r->collectDate;
				$this->harvestNeonOccurrence($sampleArr);
			}
			$rs->free();
		}
		return false;
	}

	private function harvestNeonOccurrence($sampleArr){
		$status = false;
		if($sampleArr['samplePK']){
			$dwcArr = array();
			//Get data that was provided within manifest
			$dwcArr['catalogNumber'] = $sampleArr['sampleID'];
			if($sampleArr['collectDate']) $dwcArr['eventDate'] = $sampleArr['collectDate'];
			if($sampleArr['individualCount']) $dwcArr['individualCount'] = $sampleArr['individualCount'];
			if($sampleArr['filterVolume']) $dwcArr['occurrenceRemarks'] = 'filterVolume:'.$sampleArr['filterVolume'];

			//Set occurrence description using sampleClass
			if($sampleArr['sampleClass']){
				if(array_key_exists($sampleArr['sampleClass'], $this->sampleClassArr)) $dwcArr['verbatimAttributes'] = $this->sampleClassArr[$sampleArr['sampleClass']];
				else $dwcArr['verbatimAttributes'] = $sampleArr['sampleClass'];
			}

			//Build proper location code
			if($sampleArr['namedLocation']){
				$locationName = $sampleArr['namedLocation'];
				if(strpos($locationName,'_')){
					if(substr($sampleArr['sampleClass'],0,4) == 'bet_'){
						$locationName .= '.basePlot.bet';
						if(preg_match('/^'.$sampleArr['namedLocation'].'\.([NSEW]{1})\./', $sampleArr['sampleID'], $m)){
							$locationName .= '.'.$m[1];
						}
					}
					elseif(substr($sampleArr['sampleClass'],0,4) == 'bet_'){

					}
				}
				$this->setNeonLocationData($dwcArr, $locationName);
			}

			//Set addtional data
			if($sampleArr['taxonID']) $this->setNeonTaxonomy($dwcArr, $sampleArr['taxonID']);
			$this->setNeonCollector($dwcArr);

			//Load record into omoccurrences table
			if($dwcArr){
				$sql1 = ''; $sql2 = '';
				foreach($dwcArr as $fieldName => $fieldValue){
					$sql1 .= '"'.$fieldName.'",';
					$sql2 .= '"'.$fieldValue.'",';
				}
				$sql = 'INSERT INTO omoccurrences('.trim($sql1,',').') VALUES('.trim($sql2,',').')';
				echo $sql;
				exit;
				if($this->conn->query($sql)){
					//Update NEON Sample table with new occid
					$this->conn->query('UPDATE NeonSample SET occid = '.$this->conn->insert_id.' WHERE (occid IS NULL) AND (samplePK = '.$sampleArr['samplePK'].')');
				}
				else{
					$this->errorStr = 'ERROR creating new occurrence record: '.$this->conn->error.'; '.$sql;
					$status = false;
				}
			}
		}
		return $status;
	}

	private function setNeonLocationData(&$dwcArr, $locationName){
		//http://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam
		$url = 'http://data.neonscience.org/api/v0/locations/'.$locationName;
		$resultArr = $this->getNeonApiArr($url);
		//Extract DwC values
		$locality = $this->getLocationParentStr($resultArr);

		$dwcArr['decimalLatitude'] = $resultArr['locationDecimalLatitude'];
		$dwcArr['decimalLongitude'] = $resultArr['locationDecimalLongitude'];
		$dwcArr['minimumElevationInMeters'] = $resultArr['locationElevation'];
		$habitatArr = array();
		$locPropArr = $resultArr['locationProperties'];
		foreach($locPropArr as $propArr){
			if($propArr['locationPropertyName'] == 'Value for Coordinate source') $dwcArr['georeferenceSources'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Coordinate uncertainty') $dwcArr['coordinateUncertaintyInMeters'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Country'){
				$countryValue = $propArr['locationPropertyValue'];
				if($countryValue == 'unitedStates') $countryValue = 'United States';
				$dwcArr['country'] = $countryValue;
			}
			elseif($propArr['locationPropertyName'] == 'Value for County') $dwcArr['county'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Geodetic datum') $dwcArr['geodeticDatum'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Plot dimensions') $locality .= ' (plot dimensions: '.$propArr['locationPropertyValue'].')';
			elseif(strpos($propArr['locationPropertyName'],'Value for National Land Cover Database') !== false) $habitatArr['landcover'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Slope aspect') $habitatArr['aspect'] = 'slope aspect: '.$propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Slope gradient') $habitatArr['gradient'] = 'slope gradient: '.$propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Soil type order') $habitatArr['soil'] = 'soil type order: '.$propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for State province'){
				$stateStr = $propArr['locationPropertyValue'];
				if(array_key_exists($stateStr, $this->stateArr)) $stateStr = $this->stateArr[$stateStr];
				$dwcArr['stateProvince'] = $stateStr;
			}
		}
		if($locality) $dwcArr['locality'] = trim($locality,', ');
		//Grab some habitat details availalbe with parent location
		if(preg_match('/basePlot\.bet\.[NSEW]{1}/',$locationName)){
			$urlHab = 'http://data.neonscience.org/api/v0/locations/'.substr($locationName,0,-2);
			$habArr = $this->getNeonApiArr($urlHab);
			if(isset($habArr['locationProperties'])){
				foreach($habArr['locationProperties'] as $propArr){
					if($propArr['locationPropertyName'] == 'Value for Slope aspect' && !isset($habitatArr['aspect'])) $habitatArr['aspect'] = 'slope aspect: '.$propArr['locationPropertyValue'];
					elseif($propArr['locationPropertyName'] == 'Value for Slope gradient' && !isset($habitatArr['gradient'])) $habitatArr['gradient'] = 'slope gradient: '.$propArr['locationPropertyValue'];
					elseif($propArr['locationPropertyName'] == 'Value for Soil type order' && !isset($habitatArr['soil'])) $habitatArr['soil'] = 'soil type order: '.$propArr['locationPropertyValue'];
				}
			}
		}
		if($habitatArr) $dwcArr['habitat'] = implode('; ',$habitatArr);
	}

	private function getLocationParentStr($resultArr){
		$parStr = '';
		if(isset($resultArr['locationDescription'])){
			$parStr = str_replace(array('"',', RELOCATABLE'),'',$resultArr['locationDescription']);
			$parStr = preg_replace('/ at site [A-Z]+/', '', $parStr);
			if(isset($resultArr['locationParent'])){
				if($resultArr['locationParent'] == 'REALM') return '';
				$url = 'http://data.neonscience.org/api/v0/locations/'.$resultArr['locationParent'];
				$newLoc = $this->getLocationParentStr($this->getNeonApiArr($url));
				if($newLoc) $parStr = $newLoc.', '.$parStr;
			}
		}
		return $parStr;
	}

	private function getNeonApiArr($url){
		$retArr = array();
		if($url){
			//Request URL example: http://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam
			//$json = file_get_contents($url);

			//curl -X GET --header 'Accept: application/json' 'http://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam'
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_PUT, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Accept: application/json') );
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			$json = curl_exec($curl);
			curl_close($curl);

			$resultArr = json_decode($json,true);
			if(isset($resultArr['data'])){
				$retArr = $resultArr['data'];
			}
			else{
				echo '<li style="margin-left:15px">ERROR retrieving NEON data: '.$url.'</li>';
				return false;
			}
		}
		return $retArr;
	}

	private function setNeonTaxonomy(&$dwcArr, $taxonCode){
		$sql = 'SELECT t.tid, t.sciname, t.author, ts.family '.
			'FROM taxa t INNER JOIN taxaresourcelinks r ON t.tid = r.tid '.
			'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'WHERE (ts.taxauthid = 1) AND (r.sourceidentifier = "'.$taxonCode.'")';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$dwcArr['sciname'] = $r->sciname;
			$dwcArr['scientificNameAuthorship'] = $r->author;
			$dwcArr['tidinterpreted'] = $r->tid;
			$dwcArr['family'] = $r->family;
		}
		$rs->free();
		if(!isset($dwcArr['sciname'])) $dwcArr['sciname'] = $taxonCode;
	}

	private function setNeonCollector(&$dwcArr){
		//Not yet sure how to obtain this data

	}

	private function setStateArr(){
		$sql = 'SELECT DISTINCT s.abbrev, s.statename '.
			'FROM lkupstateprovince s INNER JOIN lkupcountry c ON s.countryId = c.countryId '.
			'WHERE c.iso = "us" ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->stateArr[$r->abbrev] = $r->statename;
		}
		$rs->free();
	}

	private function setSampleClassArr(){
		$result = $this->getNeonApiArr('http://data.neonscience.org/api/v0/samples/supportedClasses');
		if(isset($result['entries'])){
			foreach($result['entries'] as $k => $classArr){
				$this->sampleClassArr[$classArr['key']] = $classArr['value'];
			}
		}
	}

	//Export functions
	public function exportShipmentList(){
		$fileName = 'shipmentExport_'.date('Y-m-d').'.csv';
		$sql = 'SELECT shipmentPK, shipmentID, domainID, dateShipped, shippedFrom, senderID, destinationFacility, sentToID, shipmentService, shipmentMethod, '.
			'trackingNumber, notes, importUid, modifiedByUid, initialtimestamp FROM NeonShipment';
		$this->exportShipmentData($fileName, $sql);
	}

	public function exportShipmentSampleList($shipmentPK){
		$sql = 'SELECT samplePK, sampleID, sampleClass, namedlocation, domainremarks, collectdate, quarantineStatus, notes, '.
			'CONCAT_WS(", ",u.lastname, u.firstname) AS checkinUser, checkinTimestamp, initialtimestamp '.
			'FROM NeonSample s LEFT JOIN users u ON s.checkinUid = u.uid WHERE s.shipmentPK = '.$shipmentPK;
		$fileName = 'shipmentExport_'.date('Y-m-d').'.csv';
		$this->exportShipmentData($fileName, $sql);
	}

	private function exportShipmentData($fileName, $sql){
		//echo "<div>".$sql."</div>"; exit;
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$outstream = fopen("php://output", "w");
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$outHeader = true;
			while($r = $rs->fetch_assoc()){
				if($outHeader){
					fputcsv($outstream,array_keys($r));
					$outHeader = false;
				}
				fputcsv($outstream,$r);
			}
		}
		else{
			echo "Recordset is empty.\n";
		}
		$rs->free();
		fclose($outstream);
	}

	//Various data return functions
	public function getImportUserArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT uid, username '.
			'FROM (SELECT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username '.
			'FROM users u INNER JOIN NeonShipment s ON u.uid = s.importUid '.
			'union SELECT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username '.
			'FROM users u INNER JOIN NeonShipment s ON u.uid = s.modifiedbyUid) u';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->username;
		}
		asort($retArr);
		return $retArr;
	}

	public function getCheckinUserArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username FROM users u INNER JOIN NeonSample s ON u.uid = s.checkinUid';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->username;
		}
		asort($retArr);
		return $retArr;
	}

	private function getContentPath(){
		$contentPath = $GLOBALS['SERVER_ROOT'];
		if(substr($contentPath,-1) != '/') $contentPath .= '/';
		$contentPath .= 'neon/content/';
		return $contentPath;
	}

	private function formatDate($dateStr){
		if(preg_match('/^(20\d{2})(\d{2})(\d{2})$/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		return $dateStr;
	}

	//Setters and getters
	public function setShipmentPK($id){
		if(is_numeric($id)) $this->shipmentPK = $id;
	}

	public function setUploadFileName($name){
		$this->uploadFileName = $name;
	}

	public function getUploadFileName(){
		return $this->uploadFileName;
	}

	public function setFieldMap($fieldMap){
		$this->fieldMap = $fieldMap;
	}

	public function getSourceArr(){
		return $this->sourceArr;
	}

	public function getTargetArr(){
		$retArr = array('shipmentid','domainid','dateshipped','shippedfrom','senderid','destinationfacility','senttoid','shipmentservice','shipmentmethod','trackingnumber',
			'sampleid','samplecode','sampleclass','taxonid','individualcount','filtervolume','namedlocation','domainremarks','collectdate','quarantinestatus');
		return $retArr;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>