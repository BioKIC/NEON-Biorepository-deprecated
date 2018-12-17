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

	public function getShipmentArr(){
		$retArr = array();
		$sql = 'SELECT shipmentPK, shipmentID, domainID, dateShipped, senderID, shipmentService, shipmentMethod, trackingNumber, notes, '.
			'CONCAT_WS(", ", u.lastname, u.firstname) AS importUser, CONCAT_WS(", ", u2.lastname, u2.firstname) AS modifiedUser, initialtimestamp '.
			'FROM NeonShipment s INNER JOIN users u ON s.importUid = u.uid '.
			'INNER JOIN users u2 ON s.modifiedByUid = u2.uid ';
		if($this->shipmentPK) $sql .= 'WHERE shipmentPK = '.$this->shipmentPK;
		$rs = $this->conn->query();
		while($r = $rs->fetch_object()){
			$retArr[$r->shipmentPK]['shipmentID'] = $r->shipmentID;
			$retArr[$r->shipmentPK]['domainID'] = $r->domainID;
			$retArr[$r->shipmentPK]['dateShipped'] = $r->dateShipped;
			$retArr[$r->shipmentPK]['senderID'] = $r->senderID;
			$retArr[$r->shipmentPK]['shipmentService'] = $r->shipmentService;
			$retArr[$r->shipmentPK]['shipmentMethod'] = $r->shipmentMethod;
			$retArr[$r->shipmentPK]['trackingNumber'] = $r->trackingNumber;
			$retArr[$r->shipmentPK]['importUser'] = $r->importUser;
			$retArr[$r->shipmentPK]['modifiedUser'] = $r->modifiedUser;
			$retArr[$r->shipmentPK]['ts'] = $r->initialtimestamp;
		}
		$rs->free();
		return $retArr;
	}

	//Specimen check-in functions
	public function checkinSample($barcode){
		if($this->shipmentPK && $barcode){
			$sql = 'UPDATE NeonSample(checkinUid,checkinTimestamp) '.
				'SELECT samplePK, '.$GLOBALS['SYMB_UID'].', now() FROM NeonSample WHERE samplePK = '.$this->shipmentPK.' AND bacode = "'.$this->cleanInStr($barcode).'" ';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR checking-in NEON sample';
				return 0;
			}
		}
		return 1;
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
		$sql = 'INSERT INTO NeonSample(shipmentPK, sampleID, sampleCode, sampleClass, taxonID, individualCount, filterVolume, namedlocation, domainremarks, collectdate, quarantineStatus, checkinUid) '.
			'VALUES('.$shipmentPK.',"'.$this->cleanInStr($recArr['sampleid']).'",'.(isset($recArr['samplecode'])&&$recArr['samplecode']?'"'.$this->cleanInStr($recArr['samplecode']).'"':'NULL').',"'.
			$this->cleanInStr($recArr['sampleclass']).'",'.(isset($recArr['taxonid'])&&$recArr['taxonid']?'"'.$this->cleanInStr($recArr['taxonid']).'"':'NULL').','.
			(isset($recArr['individualcount'])&&$recArr['individualcount']?'"'.$this->cleanInStr($recArr['individualcount']).'"':'NULL').','.
			(isset($recArr['filtervolume'])&&$recArr['filtervolume']?'"'.$this->cleanInStr($recArr['filtervolume']).'"':'NULL').',"'.
			$this->cleanInStr($recArr['namedlocation']).'",'.(isset($recArr['domainremarks'])&&$recArr['domainremarks']?'"'.$this->cleanInStr($recArr['domainremarks']).'"':'NULL').',"'.
			$this->cleanInStr($recArr['collectdate']).'",'.(isset($recArr['quarantinestatus'])&&$recArr['quarantinestatus']?'"'.$this->cleanInStr($recArr['quarantinestatus']).'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
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