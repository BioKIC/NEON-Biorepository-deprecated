<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class ShipmentManager{

	private $conn;
	private $shipmentPK;
	private $uploadFileName;
	private $fieldMap = array();
	private $sourceArr = array();
	private $errorStr;

 	public function __construct(){
 		$this->conn = MySQLiConnectionFactory::getCon("write");
 	}

 	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	public function getShipmentArr($postArr = null){
		$retArr = array();
		$sql = 'SELECT DISTINCT s.shipmentPK, s.shipmentID, s.domainID, s.dateShipped, s.senderID, s.shipmentService, s.shipmentMethod, s.trackingNumber, s.notes AS shipmentNotes, '.
			'CONCAT_WS(", ", u.lastname, u.firstname) AS importUser, CONCAT_WS(", ", u2.lastname, u2.firstname) AS checkinUser, s.checkinTimestamp, '.
			'CONCAT_WS(", ", u3.lastname, u3.firstname) AS modifiedUser, s.initialtimestamp '.
			'FROM NeonShipment s INNER JOIN users u ON s.importUid = u.uid '.
			'LEFT JOIN users u2 ON s.checkinUid = u2.uid '.
			'LEFT JOIN users u3 ON s.modifiedByUid = u3.uid '.
			'LEFT JOIN NeonSample m ON s.shipmentpk = m.shipmentpk ';
		if($this->shipmentPK){
			$sql .= 'WHERE (s.shipmentPK = '.$this->shipmentPK.') ';
		}
		elseif($_POST){
			//Set search criteria
			$sqlWhere = '';
			if(isset($_POST['shipmentID']) && $_POST['shipmentID']){
				$sqlWhere .= 'AND (s.shipmentID = "'.$_POST['shipmentID'].'") ';
			}
			if(isset($_POST['domainID']) && $_POST['domainID']){
				$sqlWhere .= 'AND (s.domainID = "'.$_POST['domainID'].'") ';
			}
			if(isset($_POST['dateShippedStart']) && $_POST['dateShippedStart']){
				$sqlWhere .= 'AND (s.dateShipped > "'.$_POST['dateShippedStart'].'") ';
			}
			if(isset($_POST['dateShippedEnd']) && $_POST['dateShippedEnd']){
				$sqlWhere .= 'AND (s.dateShipped < "'.$_POST['dateShippedEnd'].'") ';
			}
			if(isset($_POST['senderID']) && $_POST['senderID']){
				$sqlWhere .= 'AND (s.senderID = "'.$_POST['senderID'].'") ';
			}
			if(isset($_POST['trackingNumber']) && $_POST['trackingNumber']){
				$sqlWhere .= 'AND (s.trackingNumber = "'.$_POST['trackingNumber'].'") ';
			}
			if(isset($_POST['importedUid']) && $_POST['importedUid']){
				$sqlWhere .= 'AND ((s.importedByUid = "'.$_POST['importedUid'].'") OR (s.modifiedByUid = "'.$_POST['importedUid'].'")) ';
			}
			if(isset($_POST['checkinUid']) && $_POST['checkinUid']){
				$sqlWhere .= 'AND ((s.checkinUid = "'.$_POST['checkinUid'].'") OR (m.checkinUid = "'.$_POST['checkinUid'].'")) ';
			}
			if(isset($_POST['sampleID']) && $_POST['sampleID']){
				$sqlWhere .= 'AND (m.sampleID LIKE "%'.$_POST['sampleID'].'"%) ';
			}
			if(isset($_POST['sampleCode']) && $_POST['sampleCode']){
				$sqlWhere .= 'AND (m.sampleCode = "'.$_POST['sampleCode'].'") ';
			}
			if(isset($_POST['sampleClass']) && $_POST['sampleClass']){
				$sqlWhere .= 'AND (m.sampleClass LIKE "%'.$_POST['sampleClass'].'"%) ';
			}
			if(isset($_POST['taxonID']) && $_POST['taxonID']){
				$sqlWhere .= 'AND (m.taxonID = "'.$_POST['taxonID'].'") ';
			}
			if(isset($_POST['namedLocation']) && $_POST['namedLocation']){
				$sqlWhere .= 'AND (m.namedLocation = "'.$_POST['namedLocation'].'") ';
			}
			if(isset($_POST['collectDateStart']) && $_POST['collectDateStart']){
				$sqlWhere .= 'AND (m.collectDate > "'.$_POST['collectDateStart'].'") ';
			}
			if(isset($_POST['collectDateEnd']) && $_POST['collectDateEnd']){
				$sqlWhere .= 'AND (m.collectDate < "'.$_POST['collectDateEnd'].'") ';
			}
			if($sqlWhere) $sql .= 'WHERE '.subStr($sqlWhere, 3);
		}
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->shipmentPK]['shipmentID'] = $r->shipmentID;
			$retArr[$r->shipmentPK]['domainID'] = $r->domainID;
			$retArr[$r->shipmentPK]['dateShipped'] = $r->dateShipped;
			$retArr[$r->shipmentPK]['senderID'] = $r->senderID;
			$retArr[$r->shipmentPK]['shipmentService'] = $r->shipmentService;
			$retArr[$r->shipmentPK]['shipmentMethod'] = $r->shipmentMethod;
			$retArr[$r->shipmentPK]['trackingNumber'] = $r->trackingNumber;
			$retArr[$r->shipmentPK]['shipmentNotes'] = $r->shipmentNotes;
			$retArr[$r->shipmentPK]['importUser'] = $r->importUser;
			$retArr[$r->shipmentPK]['modifiedUser'] = $r->modifiedUser;
			$retArr[$r->shipmentPK]['ts'] = $r->initialtimestamp;
			$retArr[$r->shipmentPK]['checkinUser'] = $r->checkinUser;
			$retArr[$r->shipmentPK]['checkinTimestamp'] = $r->checkinTimestamp;
		}
		$rs->free();
		return $retArr;
	}

	public function getSampleCount(){
		$retArr = array();
		//Get total sample count
		$sql = 'SELECT COUNT(samplepk) AS cnt FROM neonsample WHERE (shipmentPK = '.$this->shipmentPK.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr['all'] = $r->cnt;
		}
		$rs->free();
		//Get sample count not yet checked-in
		$sql = 'SELECT COUNT(samplepk) AS cnt FROM neonsample WHERE (shipmentPK = '.$this->shipmentPK.') AND (checkinUid IS NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[0] = $r->cnt;
		}
		$rs->free();
		//Get count of samples not yet imported
		$sql = 'SELECT COUNT(s.samplepk) AS cnt '.
			'FROM neonsample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
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
		$headerArr = array('sampleID','sampleCode','sampleClass','taxonID','individualCount','filterVolume','namedLocation','domainRemarks','collectDate','quarantineStatus','sampleNotes','checkinUser','checkinTimestamp');
		$targetArr = array();
		$sql = 'SELECT m.samplePK, m.sampleID, m.sampleCode, m.sampleClass, m.taxonID, m.individualCount, m.filterVolume, m.namedLocation, m.domainRemarks, '.
			'm.collectDate, m.quarantineStatus, m.notes as sampleNotes, CONCAT_WS(", ", u.lastname, u.firstname) as checkinUser, m.checkinTimestamp '.
			'FROM neonsample m LEFT JOIN users u ON m.checkinuid = u.uid '.
			'WHERE m.shipmentPK = '.$this->shipmentPK;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			foreach($headerArr as $fieldName){
				if($r[$fieldName] && !in_array($fieldName, $targetArr)) $targetArr[] = $fieldName;
			}
		}
		$rs->data_seek(0);
		while($r = $rs->fetch_assoc()){
			foreach($targetArr as $fieldName){
				$retArr[$r['samplePK']][$fieldName] = $r[$fieldName];
			}
		}
		$rs->free();
		return $retArr;
	}

	//Check-in functions
	public function checkinShipment(){
		$sql = 'UPDATE neonshipment SET checkinUid = '.$GLOBALS['SYMB_UID'].', checkinTimestamp = now() WHERE checkinUid IS NULL AND shipmentpk = '.$this->shipmentPK;
		if(!$this->conn->query($sql)){
			$this->errorStr = 'ERROR checking-in shipment: '.$this->conn->error;
			return false;
		}
		return true;
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
					'WHERE (shipmentpk = '.$this->shipmentPK.') AND (samplePK IN('.implode(',', $pkArr).')) ';
				if(!$this->conn->query($sql)){
					$this->errorStr = 'ERROR batch checking-in samples: '.$this->conn->error;
					return false;
				}
				return true;
			}
		}
		return false;
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
			$shipmentPK = 0;
			while($recordArr = fgetcsv($fh)){
				$recMap = Array();
				foreach($indexMap as $targetField => $indexValue){
					$recMap[$targetField] = $recordArr[$indexValue];
				}
				if(!$shipmentPK) $shipmentPK = $this->loadShipmentRecord($recMap);
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
		$shipmentPK = '';
		$sql = 'INSERT INTO NeonShipment(shipmentID, domainID, dateShipped, senderID, shipmentService, shipmentMethod, trackingNumber, importUid) '.
			'VALUES("'.$this->cleanInStr($recArr['shipmentid']).'","'.$this->cleanInStr($recArr['domainid']).'","'.$this->cleanInStr($recArr['dateshipped']).'","'.
			$this->cleanInStr($recArr['senderid']).'","'.$this->cleanInStr($recArr['shipmentservice']).'","'.$this->cleanInStr($recArr['shipmentmethod']).'",'.
			(isset($recArr['trackingnumber'])?'"'.$this->cleanInStr($recArr['trackingnumber']).'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
		if($this->conn->query($sql)){
			$shipmentPK = $this->conn->insert_id;
			echo '<li>Shipment record loaded...</li>';
		}
		else{
			if($this->conn->errno == 1062){
				echo '<li>Shipment record with that shipmentID already exists...</li>';
				$sql = 'SELECT shipmentpk FROM NeonShipment WHERE shipmentID = "'.$this->cleanInStr($recArr['shipmentid']).'"';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$shipmentPK = $r->shipmentpk;
				}
				$rs->free();
			}
			else{
				echo '<li style="margin-left:15px">ERROR loading shipment record: '.$this->conn->error.'</li>';
				//echo '<li style="margin-left:15px">SQL: '.$sql.'</li>';
				return false;
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
			$this->cleanInStr($recArr['collectdate']).'",'.(isset($recArr['quarantinestatus'])&&$recArr['quarantinestatus']?'"'.$this->cleanInStr($recArr['quarantinestatus']).'"':'NULL').')';
		if($this->conn->query($sql)){
			echo '<li style="margin-left:15px">Sample record '.$recArr['sampleid'].' loaded...</li>';
		}
		else{
			if($this->conn->errno == 1062){
				echo '<li style="margin-left:15px">Sample record '.$recArr['sampleid'].' was already previously uploaded...</li>';
			}
			else{
				echo '<li style="margin-left:15px">ERROR loading sample record: '.$this->conn->error.'</li>';
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

	private function getContentPath(){
		$contentPath = $GLOBALS['SERVER_ROOT'];
		if(substr($contentPath,-1) != '/') $contentPath .= '/';
		$contentPath .= 'neon/content/';
		return $contentPath;
	}

	//Export functions
	public function exportShipmentList(){
		$fileName = 'shipmentExport_'.date('Y-m-d').'.csv';
		$sql = 'SELECT shipmentPK, shipmentID, domainID, dateShipped, senderID, shipmentService, shipmentMethod, trackingNumber, notes, importUid, modifiedByUid, initialtimestamp FROM NeonShipment';
		$this->exportShipmentData($fileName, $sql);
	}

	public function exportShipmentSampleList($shipmentPK){
		$sql = 'SELECT samplePK, sampleID, sampleClass, namedlocation, domainremarks, collectdate, quarantineStatus, notes, CONCAT_WS(", ",u.lastname, u.firstname) AS checkinUser, checkinTimestamp, initialtimestamp '.
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
			'FROM users u INNER JOIN neonshipment s ON u.uid = s.importUid '.
			'union SELECT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username '.
			'FROM users u INNER JOIN neonshipment s ON u.uid = s.modifiedbyUid) u';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->username;
		}
		asort($retArr);
		return $retArr;
	}

	public function getCheckinUserArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT u.uid, CONCAT_WS(" ", u.lastname, u.firstname) as username FROM users u INNER JOIN neonsample s ON u.uid = s.checkinUid';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->uid] = $r->username;
		}
		asort($retArr);
		return $retArr;
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
		$retArr = array('shipmentid','domainid','dateshipped','senderid','sentto','shipmentservice','shipmentmethod','trackingnumber','sampleid','samplecode','sampleclass',
			'taxonid','individualcount','filtervolume','namedlocation','domainremarks','collectdate','quarantinestatus');
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