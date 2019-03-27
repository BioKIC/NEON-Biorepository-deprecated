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

	public function getShipmentArr(){
		if(!$this->shipmentArr) $this->setShipmentArr();
		return $this->shipmentArr;
	}

	private function setShipmentArr(){
		if($this->shipmentPK){
			$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.domainID, s.dateShipped, s.shippedFrom, s.senderID, s.destinationFacility, s.sentToID, s.shipmentService, s.shipmentMethod, '.
				's.trackingNumber, s.receivedDate, s.receivedBy, s.notes AS shipmentNotes, s.fileName, s.receiptStatus, CONCAT_WS(", ", u.lastname, u.firstname) AS importUser,
				CONCAT_WS(", ", u2.lastname, u2.firstname) AS checkinUser, s.checkinTimestamp, CONCAT_WS(", ", u3.lastname, u3.firstname) AS modifiedUser, s.modifiedTimestamp, s.initialtimestamp AS ts '.
				'FROM NeonShipment s INNER JOIN users u ON s.importUid = u.uid '.
				'LEFT JOIN users u2 ON s.checkinUid = u2.uid '.
				'LEFT JOIN users u3 ON s.modifiedByUid = u3.uid '.
				'LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk '.
				'WHERE (s.shipmentPK = '.$this->shipmentPK.') ';
			//echo '<div>'.$sql.'</div>';
			$rs = $this->conn->query($sql);
			$headerArr = array();
			while($r = $rs->fetch_assoc()){
				if(!$headerArr){
					$headerArr = array_keys($r);
					unset($headerArr[array_search('shipmentPK', $headerArr)]);
				}
				foreach($headerArr as $colName){
					$this->shipmentArr[$colName] = $r[$colName];
				}
			}
			$rs->free();
			if(isset($this->shipmentArr['domainID']) && $this->shipmentArr['domainID']){
				$domainTitle = $this->getDomainTitle($this->shipmentArr['domainID']);
				if($domainTitle) $this->shipmentArr['domainTitle'] = $domainTitle;
			}
		}
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
			'WHERE (s.shipmentPK = '.$this->shipmentPK.') AND (o.occid IS NULL) ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[1] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getSampleArr($samplePK = null, $filter = null){
		$retArr = array();
		$headerArr = array('sampleID','alternativeSampleID','sampleCode','sampleClass','taxonID','individualCount','filterVolume','namedLocation','domainRemarks','collectDate',
			'quarantineStatus','acceptedForAnalysis','sampleCondition','dynamicProperties','sampleNotes','occid','checkinUser','checkinRemarks','checkinTimestamp');
		$targetArr = array();
		$sql = 'SELECT s.samplePK, s.sampleID, s.alternativeSampleID, s.sampleCode, s.sampleClass, s.taxonID, s.individualCount, s.filterVolume, s.namedLocation, '.
			's.domainRemarks, s.collectDate, s.quarantineStatus, s.acceptedForAnalysis, s.sampleCondition, s.dynamicProperties, s.notes as sampleNotes, '.
			'CONCAT_WS(", ", u.lastname, u.firstname) as checkinUser, s.checkinRemarks, s.checkinTimestamp, s.occid '.
			'FROM NeonSample s LEFT JOIN users u ON s.checkinuid = u.uid ';
		if($samplePK){
			$sql .= 'WHERE (s.samplePK = '.$samplePK.') ';
		}
		else{
			$sql .= 'WHERE (s.shipmentPK = '.$this->shipmentPK.') ';
			if($filter){
				if($filter == 'notCheckedIn'){
					$sql .= 'AND (s.checkinTimestamp IS NULL) ';
				}
				elseif($filter == 'altIds'){
					$sql .= 'AND (s.alternativeSampleID IS NOT NULL) ';
				}
			}
			$sql .= 'ORDER BY s.sampleID ';
		}
		$rs = $this->conn->query($sql);
		//Pass through recordset to see which fields have at least one value
		while($r = $rs->fetch_assoc()){
			foreach($headerArr as $fieldName){
				if($r[$fieldName] != '' && !in_array($fieldName, $targetArr)) $targetArr[] = $fieldName;
			}
		}
		if(!array_key_exists('checkinTimestamp', $targetArr)){
			$targetArr[] = 'checkinUser';
			$targetArr[] = 'checkinTimestamp';
		}
		$siteTitleArr = $this->getSiteTitleArr();
		$rs->data_seek(0);
		//Grab data for only columns that have data
		while($r = $rs->fetch_assoc()){
			foreach($targetArr as $fieldName){
				$retArr[$r['samplePK']][$fieldName] = $r[$fieldName];
			}
			if(isset($retArr[$r['samplePK']]['namedLocation'])){
				$namedLocation = $retArr[$r['samplePK']]['namedLocation'];
				if(preg_match('/^([A-Za-z]+)/',$namedLocation,$m)){
					if(array_key_exists($m[1], $siteTitleArr)) $retArr[$r['samplePK']]['siteTitle'] = $siteTitleArr[$m[1]];
				}
			}
		}
		$rs->free();
		if($samplePK) return array_shift($retArr);
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
		$shipmentPK = false;
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
				$indexMap[$targetField][$sourceField] = $index;
			}
			echo '<li>Beginning to load records...</li>';
			while($recordArr = fgetcsv($fh)){
				$recMap = Array('filename' => $this->uploadFileName);
				$dynPropArr = array();
				foreach($indexMap as $targetField => $indexValueArr){
					foreach($indexValueArr as $sField => $indexValue){
						if(strtolower($targetField) == 'dynamicproperties'){
							if($recordArr[$indexValue]) $dynPropArr[$sField] = $recordArr[$indexValue];
						}
						else{
							$recMap[$targetField] = $recordArr[$indexValue];
						}
					}
				}
				if($dynPropArr){
					$recMap['dynamicproperties'] = json_encode($dynPropArr);
				}
				if($shipmentPK === false) $shipmentPK = $this->loadShipmentRecord($recMap);
				if($shipmentPK){
					$this->loadSampleRecord($shipmentPK, $recMap);
					$recCnt++;
				}
				unset($recMap);
			}
			fclose($fh);

			echo '<li>Complete!!!</li>';
		}
		else{
			$this->outputMsg('<li>File Upload FAILED: unable to locate file</li>');
		}
		return $shipmentPK;
	}

	public function loadShipmentRecord($recArr){
		$shipmentPK = 0;
		$trackingId = '';
		$recArr = array_change_key_case($recArr);
		if(isset($recArr['trackingnumber'])){
			$trackingId = trim($recArr['trackingnumber'],' #');
			$trackingId = str_replace(array("\n",','), ';', $trackingId);
			$trackingId = preg_replace('/[^a-zA-Z0-9;]+/', '', $trackingId);
			if($trackingId == 'none') $trackingId = '';
		}
		$sql = 'INSERT INTO NeonShipment(shipmentID, domainID, dateShipped, shippedFrom,senderID, destinationFacility, sentToID, shipmentService, shipmentMethod, trackingNumber, notes, fileName, importUid) '.
			'VALUES("'.$this->cleanInStr($recArr['shipmentid']).'",'.(isset($recArr['domainid'])?'"'.$this->cleanInStr($recArr['domainid']).'"':'NULL').','.
			(isset($recArr['dateshipped'])&&$recArr['dateshipped']?'"'.$this->cleanInStr($this->formatDate($recArr['dateshipped'])).'"':'NULL').','.
			(isset($recArr['shippedfrom'])?'"'.$this->cleanInStr($recArr['shippedfrom']).'"':'NULL').','.(isset($recArr['senderid'])?'"'.$this->cleanInStr($recArr['senderid']).'"':'NULL').','.
			(isset($recArr['destinationfacility'])?'"'.$this->cleanInStr($recArr['destinationfacility']).'"':'NULL').','.
			(isset($recArr['senttoid'])?'"'.$this->cleanInStr($recArr['senttoid']).'"':'NULL').','.
			(isset($recArr['shipmentservice'])?'"'.$this->cleanInStr($recArr['shipmentservice']).'"':'NULL').','.
			(isset($recArr['shipmentmethod'])?'"'.$this->cleanInStr($recArr['shipmentmethod']).'"':'NULL').','.
			($trackingId?'"'.$trackingId.'"':'NULL').','.
			(isset($recArr['shipmentnotes'])?'"'.$this->cleanInStr($recArr['shipmentnotes']).'"':'NULL').','.
			(isset($recArr['filename'])?'"'.$this->cleanInStr($recArr['filename']).'"':'NULL').','.
			$GLOBALS['SYMB_UID'].')';
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
				echo '<li style="margin-left:15px">SQL: '.$sql.'</li>';
				return 0;
			}
		}
		return $shipmentPK;
	}

	private function loadSampleRecord($shipmentPK, $recArr){
		$recArr = array_change_key_case($recArr);
		$sql = 'INSERT INTO NeonSample(shipmentPK, sampleID, sampleCode, sampleClass, taxonID, individualCount, filterVolume, namedlocation, domainremarks, collectdate, dynamicproperties, quarantineStatus) '.
			'VALUES('.$shipmentPK.',"'.$this->cleanInStr($recArr['sampleid']).'",'.(isset($recArr['samplecode'])&&$recArr['samplecode']?'"'.$this->cleanInStr($recArr['samplecode']).'"':'NULL').',"'.
			$this->cleanInStr($recArr['sampleclass']).'",'.(isset($recArr['taxonid'])&&$recArr['taxonid']?'"'.$this->cleanInStr($recArr['taxonid']).'"':'NULL').','.
			(isset($recArr['individualcount'])&&$recArr['individualcount']?'"'.$this->cleanInStr($recArr['individualcount']).'"':'NULL').','.
			(isset($recArr['filtervolume'])&&$recArr['filtervolume']?'"'.$this->cleanInStr($recArr['filtervolume']).'"':'NULL').','.
			(isset($recArr['namedlocation'])?'"'.$this->cleanInStr($recArr['namedlocation']).'"':'NULL').','.
			(isset($recArr['domainremarks'])&&$recArr['domainremarks']?'"'.$this->cleanInStr($recArr['domainremarks']).'"':'NULL').','.
			(isset($recArr['collectdate'])?'"'.$this->cleanInStr($this->formatDate($recArr['collectdate'])).'"':'NULL').','.
			(isset($recArr['dynamicproperties'])?'"'.$this->cleanInStr($recArr['dynamicproperties']).'"':'NULL').','.
			(isset($recArr['quarantinestatus'])&&$recArr['quarantinestatus']?'"'.$this->cleanInStr($recArr['quarantinestatus']).'"':'NULL').')';
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
			//$fieldStr = strtolower(trim($field));
			$fieldStr = trim($field);
			//if($fieldStr) $retArr[] = $fieldStr;
			$retArr[] = $fieldStr;
		}
		return $retArr;
	}

	//Check-in functions
	public function checkinShipment($postArr){
		$sql = 'UPDATE NeonShipment '.
			'SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), '.
			'receivedDate = '.($postArr['receivedDate']?'"'.$this->cleanInStr($postArr['receivedDate']).' '.$this->cleanInStr($postArr['receivedTime']).'"':'NULL').', '.
			'receivedBy = '.($postArr['receivedBy']?'"'.$this->cleanInStr($postArr['receivedBy']).'"':'NULL').', '.
			'notes = '.($postArr['notes']?'CONCAT_WS("; ",notes,"'.$this->cleanInStr($postArr['notes']).'")':'NULL').' '.
			'WHERE (shipmentpk = '.$this->shipmentPK.') AND (checkinUid IS NULL)';
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR checking-in shipment: '.$this->conn->error;
			return false;
		}
		return true;
	}

	public function setReceiptStatus($status, $onlyIfNull = false){
		$statusStr = '';
		if($status == 1) $statusStr = 'Downloaded';
		if($status == 2) $statusStr = 'Submitted';
		if($statusStr) $statusStr .= ':'.$GLOBALS['USERNAME'];
		$sql = 'UPDATE NeonShipment SET receiptstatus = '.($statusStr?'"'.$statusStr.'"':'NULL').' WHERE (shipmentpk = '.$this->shipmentPK.') ';
		if($onlyIfNull) $sql .= 'AND (receiptstatus IS NULL)';
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR tagging receipt as submitted: '.$this->conn->error;
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
		//if(!isset($retArr['receivedBy']) || !$retArr['receivedBy']) $retArr['receivedBy'] = $GLOBALS['USERNAME'];
		if(!isset($retArr['receivedDate']) || !$retArr['receivedDate']){
			//$retArr['receivedDate'] = date('Y-m-d');
			//$retArr['receivedTime'] = date('H:i:s');
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

	public function checkinSample($sampleID, $acceptedForAnalysis, $condition, $alternativeSampleID, $notes){
		$status = 3;
		// status: 0 = check-in failed, 1 = check-in success, 2 = sample already checked-in, 3 = sample not found
		if($this->shipmentPK && $sampleID){
			$samplePK = 0;
			$sql = 'SELECT samplePK, alternativeSampleID, checkinTimestamp FROM NeonSample '.
				'WHERE (shipmentpk = '.$this->shipmentPK.') AND (sampleID = "'.$this->cleanInStr($sampleID).'" OR sampleCode = "'.$this->cleanInStr($sampleID).'") ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$samplePK = $r->samplePK;
				if($alternativeSampleID && $r->alternativeSampleID && $alternativeSampleID != $r->alternativeSampleID) $alternativeSampleID .= '; '.$r->alternativeSampleID;
				if($r->checkinTimestamp) $status = 2;
				else $status = 1;
			}
			$rs->free();
			if($status == 1 && $samplePK){
				$sqlUpdate = 'UPDATE NeonSample SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), acceptedForAnalysis = '.($acceptedForAnalysis?'1':'0').' ';
				if($condition) $sqlUpdate .= ', sampleCondition = CONCAT_WS("; ",sampleCondition,"'.$this->cleanInStr($condition).'") ';
				if($notes) $sqlUpdate .= ', checkinRemarks = "'.$this->cleanInStr($notes).'" ';
				if($alternativeSampleID) $sqlUpdate .= ', alternativeSampleID = "'.$this->cleanInStr($alternativeSampleID).'" ';
				$sqlUpdate .= 'WHERE (samplePK = "'.$samplePK.'") ';
				if(!$this->conn->query($sqlUpdate)){
					$this->errorStr = 'ERROR checking-in NEON sample: '.$this->conn->error;
					$status = 0;
				}
			}
		}
		$retJson = json_encode(array('status'=>$status,'samplePK'=>$samplePK,'errorMsg'=>$this->errorStr));
		return $retJson;
	}

	public function batchCheckinSamples($postArr){
		if($this->shipmentPK){
			$pkArr = $postArr['scbox'];
			if($pkArr){
				$sql = 'UPDATE NeonSample '.
					'SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), acceptedForAnalysis = '.$postArr['acceptedForAnalysis'].' '.
					($postArr['sampleCondition']?', sampleCondition = "'.$this->cleanInStr($postArr['sampleCondition']).'" ':'').
					($postArr['checkinRemarks']?', checkinRemarks = "'.$this->cleanInStr($postArr['checkinRemarks']).'" ':'').
					'WHERE (shipmentpk = '.$this->shipmentPK.') AND (checkinTimestamp IS NULL) AND (samplePK IN('.implode(',', $pkArr).'))';
				//echo $sql;
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
	public function editShipment($postArr){
		if($this->shipmentPK){
			$sql = 'UPDATE NeonShipment '.
				'SET domainID = "'.$this->cleanInStr($postArr['domainID']).'", '.
				'dateShipped = '.($postArr['dateShipped']?'"'.$this->cleanInStr($postArr['dateShipped']).'"':'NULL').', '.
				'shippedFrom = '.($postArr['shippedFrom']?'"'.$this->cleanInStr($postArr['shippedFrom']).'"':'NULL').', '.
				'senderID = '.($postArr['senderID']?'"'.$this->cleanInStr($postArr['senderID']).'"':'NULL').', '.
				'destinationFacility = '.($postArr['destinationFacility']?'"'.$this->cleanInStr($postArr['destinationFacility']).'"':'NULL').', '.
				'sentToID = '.($postArr['sentToID']?'"'.$this->cleanInStr($postArr['sentToID']).'"':'NULL').', '.
				'shipmentService = '.($postArr['shipmentService']?'"'.$this->cleanInStr($postArr['shipmentService']).'"':'NULL').', '.
				'shipmentMethod = '.($postArr['shipmentMethod']?'"'.$this->cleanInStr($postArr['shipmentMethod']).'"':'NULL').', '.
				'trackingNumber = '.($postArr['trackingNumber']?'"'.$this->cleanInStr($postArr['trackingNumber']).'"':'NULL').', '.
				'notes = '.($postArr['shipmentNotes']?'"'.$this->cleanInStr($postArr['shipmentNotes']).'"':'NULL').', '.
				'modifiedByUid = '.$GLOBALS['SYMB_UID'].', '.
				'modifiedTimestamp = now() '.
				'WHERE (shipmentpk = '.$this->shipmentPK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR editing shipment: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	public function resetShipmentCheckin(){
		if($this->shipmentPK){
			$sql = 'UPDATE NeonShipment SET checkinUid = NULL, checkinTimestamp = NULL, receivedDate = NULL, receivedBy = NULL WHERE (shipmentpk = '.$this->shipmentPK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR resetting shipment check-in: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	public function shipmentISDeletable(){
		$status = true;
		if($this->shipmentPK){
			$sql = 'SELECT occid FROM NeonSample WHERE (shipmentPK = '.$this->shipmentPK.') AND (occid IS NOT NULL) LIMIT 1 ';
			$rs = $this->conn->query($sql);
			if($rs->num_rows) $status = false;
			$rs->free();
		}
		return $status;
	}

	public function deleteShipment($shipmentPK){
		if(is_numeric($shipmentPK)){
			$sql = 'DELETE FROM NeonShipment WHERE (shipmentpk = '.$shipmentPK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR deleting shipment: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	public function editSample($postArr){
		$status = false;
		echo 'samplePK: '.$postArr['samplePK'].'<br/>';
		if(is_numeric($postArr['samplePK'])){
			$sql = 'UPDATE NeonSample '.
				'SET alternativeSampleID = '.($postArr['alternativeSampleID']?'"'.$this->cleanInStr($postArr['alternativeSampleID']).'"':'NULL').', '.
				'sampleCode = '.($postArr['sampleCode']?'"'.$this->cleanInStr($postArr['sampleCode']).'"':'NULL').', '.
				'sampleClass = '.($postArr['sampleClass']?'"'.$this->cleanInStr($postArr['sampleClass']).'"':'NULL').', '.
				'quarantineStatus = '.($postArr['quarantineStatus']?'"'.$this->cleanInStr($postArr['quarantineStatus']).'"':'NULL').', '.
				'namedLocation = '.($postArr['namedLocation']?'"'.$this->cleanInStr($postArr['namedLocation']).'"':'NULL').', '.
				'collectDate = '.($postArr['collectDate']?'"'.$this->cleanInStr($postArr['collectDate']).'"':'NULL').', '.
				'taxonID = '.($postArr['taxonID']?'"'.$this->cleanInStr($postArr['taxonID']).'"':'NULL').', '.
				'individualCount = '.(is_numeric($postArr['individualCount'])?'"'.$this->cleanInStr($postArr['individualCount']).'"':'NULL').', '.
				'filterVolume = '.(is_numeric($postArr['filterVolume'])?'"'.$this->cleanInStr($postArr['filterVolume']).'"':'NULL').', '.
				'domainRemarks = '.($postArr['domainRemarks']?'"'.$this->cleanInStr($postArr['domainRemarks']).'"':'NULL').', '.
				'notes = '.($postArr['sampleNotes']?'"'.$this->cleanInStr($postArr['sampleNotes']).'"':'NULL').' '.
				'WHERE (samplepk = '.$postArr['samplePK'].')';
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR editing sample data: '.$this->conn->error;
				return false;
			}
		}
		return $status;
	}

	public function addSample($postArr){
		$status = false;
		if($this->shipmentPK){
			$sql = 'INSERT INTO NeonSample(shipmentPK, sampleID, alternativeSampleID, sampleCode, sampleClass, quarantineStatus, namedLocation, collectDate, taxonID, individualCount, filterVolume, domainRemarks, notes) '.
				'VALUES('.$this->shipmentPK.',"'.$this->cleanInStr($postArr['sampleID']).'",'.($postArr['alternativeSampleID']?'"'.$this->cleanInStr($postArr['alternativeSampleID']).'"':'NULL').','.
				($postArr['sampleCode']?'"'.$this->cleanInStr($postArr['sampleCode']).'"':'NULL').','.
				($postArr['sampleClass']?'"'.$this->cleanInStr($postArr['sampleClass']).'"':'NULL').','.($postArr['quarantineStatus']?'"'.$this->cleanInStr($postArr['quarantineStatus']).'"':'NULL').','.
				($postArr['namedLocation']?'"'.$this->cleanInStr($postArr['namedLocation']).'"':'NULL').','.($postArr['collectDate']?'"'.$this->cleanInStr($postArr['collectDate']).'"':'NULL').','.
				($postArr['taxonID']?'"'.$this->cleanInStr($postArr['taxonID']).'"':'NULL').','.($postArr['individualCount']?'"'.$this->cleanInStr($postArr['individualCount']).'"':'NULL').','.
				($postArr['filterVolume']?'"'.$this->cleanInStr($postArr['filterVolume']).'"':'NULL').','.($postArr['domainRemarks']?'"'.$this->cleanInStr($postArr['domainRemarks']).'"':'NULL').','.
				($postArr['sampleNotes']?'"'.$this->cleanInStr($postArr['sampleNotes']).'"':'NULL').')';
			if($this->conn->query($sql)){
				$status = true;
				if(isset($postArr['checkinSample']) && $postArr['checkinSample']){
					$sqlUpdate = 'UPDATE NeonSample SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now(), acceptedForAnalysis = 1, sampleCondition = "OK" WHERE (samplePK = '.$this->conn->insert_id.') ';
					if(!$this->conn->query($sqlUpdate)){
						$this->errorStr = 'ERROR checking-in NEON sample(2): '.$this->conn->error;
						$status = 0;
					}
				}
			}
			else{
				if($this->conn->errno == 1062){
					$this->errorStr = 'A sample already exists with sampleID: <a href="manifestviewer.php?quicksearch='.$postArr['sampleID'].
						'" target="_blank" onclick="window.close()">'.$postArr['sampleID'].'</a> (click to go to manifest)';
				}
				else{
					$this->errorStr = 'ERROR adding new sample ('.$this->conn->errno.'): '.$this->conn->error;
				}
				return false;
			}
		}
		return $status;
	}

	public function deleteSample($samplePK){
		$status = false;
		if(is_numeric($samplePK)){
			$sql = 'DELETE FROM NeonSample WHERE samplePK = '.$samplePK;
			echo $sql;
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR deleting sample: '.$this->conn->error;
				return false;
			}
		}
		return $status;
	}

	public function editSampleCheckin($postArr){
		$status = false;
		if(is_numeric($postArr['samplePK'])){
			$sql = 'UPDATE NeonSample '.
				'SET acceptedForAnalysis = '.$this->cleanInStr($postArr['acceptedForAnalysis']).', '.
				'sampleCondition = '.($postArr['sampleCondition']?'"'.$this->cleanInStr($postArr['sampleCondition']).'"':'NULL').', '.
				'checkinRemarks = '.($postArr['checkinRemarks']?'"'.$this->cleanInStr($postArr['checkinRemarks']).'"':'NULL').' '.
				'WHERE (samplepk = '.$postArr['samplePK'].')';
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorStr = 'ERROR editing sample check-in info: '.$this->conn->error;
				return false;
			}
		}
		return $status;
	}

	public function resetSampleCheckin($samplePK){
		if(is_numeric($samplePK)){
			$sql = 'UPDATE NeonSample SET checkinUid = NULL, checkinTimestamp = NULL, acceptedForAnalysis = NULL, sampleCondition = NULL, checkinRemarks = NULL WHERE (samplepk = '.$samplePK.')';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR resetting sample check-in: '.$this->conn->error;
				return false;
			}
			return true;
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
			$dwcArr['othercatalogNumbers'] = $sampleArr['sampleID'];
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

	//Shipment and sample search functions
	public function getShipmentList(){
		$retArr = array();
		$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.initialtimestamp '.
			'FROM NeonShipment s LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk ';
		if(isset($_POST['manifestStatus']) && $_POST['manifestStatus'] == 'nonAcceptedSamples'){
			$sql .= 'LEFT JOIN omoccurrences o ON m.occid = o.occid ';
		}
		$sql .= $this->getFilteredWhereSql().'ORDER BY s.shipmentID';
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->shipmentPK]['id'] = $r->shipmentID;
			$retArr[$r->shipmentPK]['ts'] = $r->initialtimestamp;
		}
		$rs->free();
		return $retArr;
	}

	private function getFilteredWhereSql(){
		$sqlWhere = '';
		if(isset($_POST['shipmentID'])){
			if($_POST['shipmentID']){
				$sqlWhere .= 'AND (s.shipmentID = "'.$this->cleanInStr($_POST['shipmentID']).'") ';
			}
			if($_POST['sampleID']){
				$sqlWhere .= 'AND ((m.sampleID LIKE "%'.$this->cleanInStr($_POST['sampleID']).'%") OR (m.alternativeSampleID LIKE "%'.$this->cleanInStr($_POST['sampleID']).'%")) ';
			}
			if($_POST['sampleCode']){
				$sqlWhere .= 'AND (m.sampleCode = "'.$this->cleanInStr($_POST['sampleCode']).'") ';
			}
			if($_POST['domainID']){
				$sqlWhere .= 'AND (s.domainID = "'.$_POST['domainID'].'") ';
			}
			if($_POST['namedLocation']){
				$sqlWhere .= 'AND ((m.namedLocation LIKE "'.$_POST['namedLocation'].'%") OR (m.sampleID LIKE "'.$_POST['namedLocation'].'%")) ';
			}
			if($_POST['sampleClass']){
				$sqlWhere .= 'AND (m.sampleClass LIKE "%'.$this->cleanInStr($_POST['sampleClass']).'%") ';
			}
			if($_POST['taxonID']){
				$sqlWhere .= 'AND (m.taxonID = "'.$_POST['taxonID'].'") ';
			}
			if($_POST['trackingNumber']){
				$trackingId = trim($_POST['trackingNumber'],' #');
				$trackingId = preg_replace('/[^a-zA-Z0-9]+/', '', $trackingId);
				$sqlWhere .= 'AND (s.trackingNumber = "'.$trackingId.'") ';
			}
			if($_POST['dateShippedStart']){
				$sqlWhere .= 'AND (s.dateShipped > "'.$_POST['dateShippedStart'].'") ';
			}
			if($_POST['dateShippedEnd']){
				$sqlWhere .= 'AND (s.dateShipped < "'.$_POST['dateShippedEnd'].'") ';
			}
			/*
			 if(isset($_POST['senderID']) && $_POST['senderID']){
			 $sqlWhere .= 'AND (s.senderID = "'.$_POST['senderID'].'") ';
			 }
			 */
			if($_POST['checkinUid']){
				$sqlWhere .= 'AND ((s.checkinUid = "'.$_POST['checkinUid'].'") OR (m.checkinUid = "'.$_POST['checkinUid'].'")) ';
			}
			if($_POST['importedUid']){
				$sqlWhere .= 'AND ((s.importUid = "'.$_POST['importedUid'].'") OR (s.modifiedByUid = "'.$_POST['importedUid'].'")) ';
			}
			/*
			 if($_POST['collectDateStart']){
			 $sqlWhere .= 'AND (m.collectDate > "'.$_POST['collectDateStart'].'") ';
			 }
			 if($_POST['collectDateEnd']){
			 $sqlWhere .= 'AND (m.collectDate < "'.$_POST['collectDateEnd'].'") ';
			 }
			 */
			if($_POST['sampleCondition']){
				$sqlWhere .= 'AND (m.sampleCondition = "'.$_POST['sampleCondition'].'") ';
			}
			if(isset($_POST['manifestStatus'])){
				if($_POST['manifestStatus'] == 'shipNotCheck'){
					$sqlWhere .= 'AND (s.checkinTimestamp IS NULL) ';
				}
				elseif($_POST['manifestStatus'] == 'receiptNotSubmitted'){
					$sqlWhere .= 'AND (s.receiptstatus IS NULL OR s.receiptstatus NOT LIKE "submitted%") ';
				}
				elseif($_POST['manifestStatus'] == 'sampleNotCheck'){
					$sqlWhere .= 'AND (m.checkinTimestamp IS NULL) ';
				}
				elseif($_POST['manifestStatus'] == 'nonAcceptedSamples'){
					$sqlWhere .= 'AND (m.acceptedForAnalysis = 0) ';
				}
				elseif($_POST['manifestStatus'] == 'nonAcceptedSamples'){
					$sqlWhere .= 'AND (o.occid IS NULL) ';
				}
			}
		}
		if($sqlWhere) $sqlWhere = 'WHERE '.subStr($sqlWhere, 3);
		//echo 'where: '.$sqlWhere; exit;
		return $sqlWhere;
	}

	//Export functions
	public function exportShipmentReceipt(){
		$this->setShipmentArr();
		$fileName = 'receipt_'.$this->shipmentArr['shipmentID'].'_'.date('Y-m-d').'.csv';
		$sql = 'SELECT n.shipmentID, DATE_FORMAT(s.checkinTimestamp,"%Y%m%d") AS shipmentReceivedDate, u.email AS receivedBy, s.sampleID, s.sampleCode, s.sampleClass, IF(s.checkinUid IS NULL, "N", "Y") AS sampleReceived, '.
			'IF(s.acceptedForAnalysis IS NULL,"",IF(s.acceptedForAnalysis = 0,"N","Y")) AS acceptedForAnalysis, s.sampleCondition, s.alternativeSampleID AS unknownSamples, s.notes AS remarks '.
			'FROM NeonShipment n INNER JOIN NeonSample s ON n.shipmentPK = s.shipmentPK '.
			'LEFT JOIN users u ON s.checkinUid = u.uid '.
			'WHERE (s.shipmentPK = '.$this->shipmentPK.')';
		$this->exportData($fileName, $sql);
		$this->setReceiptStatus(1,true);
	}

	public function exportShipmentList(){
		$fileName = 'shipmentExport_'.date('Y-m-d').'.csv';
		$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.domainID, s.dateShipped, s.shippedFrom, s.senderID, s.destinationFacility, s.sentToID, s.shipmentService, s.shipmentMethod, '.
			's.trackingNumber, s.receivedDate, s.receivedBy, s.receiptstatus, s.notes, CONCAT_WS("; ",u1.lastname, u1.firstname) AS importUser, '.
			'CONCAT_WS("; ",u2.lastname, u2.firstname) AS checkinUser, s.checkinTimestamp, CONCAT_WS("; ",u3.lastname, u3.firstname) AS modifiedByUser, s.modifiedTimestamp, s.initialtimestamp '.
			'FROM NeonShipment s LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk '.
			'LEFT JOIN users u1 ON s.importUid = u1.uid '.
			'LEFT JOIN users u2 ON s.checkinUid = u2.uid '.
			'LEFT JOIN users u3 ON s.modifiedByUid = u3.uid ';
		if(isset($_POST['manifestStatus']) && $_POST['manifestStatus'] == 'nonAcceptedSamples'){
			$sql .= 'LEFT JOIN omoccurrences o ON m.occid = o.occid ';
		}
		$sql .= $this->getFilteredWhereSql();
		//echo $sql;
		$this->exportData($fileName, $sql);
	}

	public function exportShipmentSampleList(){
		if($this->shipmentPK){
			$fileName = 'sampleExport_'.date('Y-m-d').'.csv';
			$sql = 'SELECT s.samplePK, s.sampleID, s.alternativeSampleID, s.sampleCode, s.sampleClass, s.taxonID, s.individualCount, s.filterVolume, s.namedlocation, '.
				's.domainremarks, s.collectdate, s.quarantineStatus, s.acceptedForAnalysis, s.sampleCondition, s.dynamicProperties, s.notes, '.
				'CONCAT_WS(", ",u.lastname, u.firstname) AS checkinUser, s.checkinTimestamp, s.initialtimestamp '.
				'FROM NeonSample s LEFT JOIN users u ON s.checkinUid = u.uid WHERE (s.shipmentPK = '.$this->shipmentPK.')';
			$this->exportData($fileName, $sql);
		}
	}

	private function exportData($fileName, $sql){
		//echo "<div>".$sql."</div>"; exit;
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$outstream = fopen("php://output", "w");
		if($rs = $this->conn->query($sql)){
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
		}
		else{
			echo 'ERROR generating recordset';
		}
		fclose($outstream);
		exit;
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

	public function getTrackingStr(){
		$retStr = '';
		$trackingArr = explode(';',$this->shipmentArr['trackingNumber']);
		$trackingArr = array_unique($trackingArr);
		foreach($trackingArr as $trackingStr){
			$trackingId = preg_replace('/[^a-zA-Z0-9;]+/', '', $trackingStr);
			if($this->shipmentArr['shipmentService'] == 'FedEx' && strlen($trackingId) == 12){
				$retStr .= '<a href="https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers='.$trackingId.'&locale=en_US&cntry_code=us" target="_blank">';
			}
			elseif($this->shipmentArr['shipmentService'] == 'UPS' && strlen($trackingId) == 18){
				$retStr .= '<a href="https://www.ups.com/track?loc=en_US&tracknum='.$trackingId.'&requester=WT/trackdetails" target="_blank">';
			}
			$retStr .= $trackingId.'</a>; ';
		}
		return trim($retStr,' ;');
	}

	private function getContentPath(){
		$contentPath = $GLOBALS['SERVER_ROOT'];
		if(substr($contentPath,-1) != '/') $contentPath .= '/';
		$contentPath .= 'neon/content/';
		return $contentPath;
	}

	private function formatDate($dateStr){
		if(preg_match('/^(20\d{2})(\d{2})(\d{2})$/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(20\d{2})-(\d{2})-(\d{2})\D*/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(20\d{2})/', $dateStr, $m)){
			$month = $m[1];
			if(strlen($month) == 1) $month = '0'.$month;
			$day = $m[2];
			if(strlen($day) == 1) $day = '0'.$day;
			$dateStr = $m[3].'-'.$month.'-'.$day;
		}
		return $dateStr;
	}

	public function getConditionArr(){
		$condArr = array('OK'=>'OK - No Known Compromise', 'cold chain broken'=>'Cold Chain Broken', 'damaged'=>'Damaged - Analysis Affected',
			'sample incomplete'=>'Sample Incomplete','handling error'=>'Handling Error', 'other'=>'Other - Described in Remarks');
		return $condArr;
	}

	public function getConditionAppliedArr(){
		$queryArr = array();
		$sql = 'SELECT DISTINCT sampleCondition FROM NeonSample ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$queryArr[$r->sampleCondition] = $r->sampleCondition;
		}
		$rs->free();
		return array_intersect_key($this->getConditionArr(), $queryArr);
	}

	private function getDomainTitle($domainID){
		$domainID = trim($domainID);
		$domainArr = array("D01"=>"Northeast", "D02"=>"Mid-Atlantic", "D03"=>"Southeast", "D04"=>"Atlantic Neotropical", "D05"=>"Great Lakes", "D06"=>"Prairie Peninsula",
			"D07"=>"Appalachians & Cumberland Plateau", "D08"=>"Ozarks Complex", "D08"=>"Ozarks Complex", "D09"=>"Northern Plains", "D10"=>"Central Plains", "D11"=>"Southern Plains",
			"D13"=>"Southern Rockies & Colorado Plateau", "D14"=>"Desert Southwest", "D15"=>"Great Basin", "D16"=>"Pacific Northwest", "D17"=>"Pacific Southwest", "D18"=>"Tundra",
			"D19"=>"Taiga", "D20"=>"Pacific Tropical");
		return (array_key_exists($domainID, $domainArr)?$domainArr[$domainID]:'');
	}

	private function getSiteTitleArr(){
		$siteArr = array("ABBY"=>"Abby Road", "BARR"=>"Barrow Environmental Observatory", "BART"=>"Bartlett Experimental Forest", "BLAN"=>"Blandy Experimental Farm",
			"BONA"=>"Caribou Creek - Poker Flats Watershed", "CLBJ"=>"LBJ National Grassland", "CPER"=>"Central Plains Experimental Range", "DCFS"=>"Dakota Coteau Field School",
			"DEJU"=>"Delta Junction", "DELA"=>"Dead Lake", "DSNY"=>"Disney Wilderness Preserve", "GRSM"=>"Great Smoky Mountains National Park, Twin Creeks", "GUAN"=>"Guanica Forest",
			"HARV"=>"Harvard Forest", "HEAL"=>"Healy", "JERC"=>"Jones Ecological Research Center", "JORN"=>"Jornada LTER", "KONA"=>"Konza Prairie Biological Station",
			"KONZ"=>"Konza Prairie Biological Station", "LAJA"=>"Lajas Experimental Station", "LENO"=>"Lenoir Landing", "MLBS"=>"Mountain Lake Biological Station", "MOAB"=>"Moab",
			"NIWO"=>"Niwot Ridge Mountain Research Station", "NOGP"=>"Northern Great Plains Research Laboratory", "OAES"=>"Klemme Range Research Station", "ONAQ"=>"Onaqui-Ault",
			"ORNL"=>"Oak Ridge", "OSBS"=>"Ordway-Swisher Biological Station", "PUUM"=>"Pu'u Maka'ala Natural Area Reserve", "RMNP"=>"Rocky Mountain National Park, CASTNET",
			"SCBI"=>"Smithsonian Conservation Biology Institute", "SERC"=>"Smithsonian Environmental Research Center", "SJER"=>"San Joaquin", "SOAP"=>"Soaproot Saddle",
			"SRER"=>"Santa Rita Experimental Range", "STEI"=>"Steigerwaldt Land Services", "STER"=>"North Sterling, CO", "TALL"=>"Talladega National Forest", "TEAK"=>"Lower Teakettle",
			"TOOL"=>"Toolik Lake", "TREE"=>"Treehaven", "UKFS"=>"The University of Kansas Field Station", "UNDE"=>"UNDERC", "WOOD"=>"Woodworth", "WREF"=>"Wind River Experimental Forest");
		return $siteArr;
	}

	//Setters and getters
	public function setShipmentPK($id){
		if(is_numeric($id)) $this->shipmentPK = $id;
	}

	public function setQuickSearchTerm($term){
		$cleanTerm = $this->cleanInStr($term);
		$sql = 'SELECT s.shipmentPK '.
			'FROM NeonShipment s LEFT JOIN NeonSample m ON s.shipmentPK = m.shipmentPK '.
			'WHERE (s.shipmentid = "'.$cleanTerm.'" OR m.sampleid = "'.$cleanTerm.'" OR m.alternativeSampleID = "'.$cleanTerm.'" OR m.sampleCode = "'.$cleanTerm.'")';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$this->shipmentPK = $r->shipmentPK;
		}
		$rs->free();
		return $this->shipmentPK;
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
		$retArr = array('shipmentID','domainID','dateShipped','shippedFrom','senderID','destinationFacility','sentToID','shipmentService','shipmentMethod','trackingNumber','shipmentNotes',
			'sampleID','sampleCode','sampleClass','taxonID','individualCount','filterVolume','namedLocation','domainRemarks','collectDate','quarantineStatus','dynamicProperties');
		sort($retArr);
		return $retArr;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	public function in_iarray($needle, $haystack) {
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}

	public function array_key_iexists($key, $array) {
		return array_key_exists(strtolower($key), array_map('strtolower', $array));
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>