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

	//Import functions
	public function uploadManifestFile(){
		$status = false;
		ini_set('auto_detect_line_endings', true);
		//Load file onto server
		$uploadPath = $this->getContentPath().'manifests/';
		$fileName = '';
		if(array_key_exists("uploadfile",$_FILES)){
			$fileName = $_FILES['uploadfile']['name'];
			$fullPath = $uploadPath.$_FILES['uploadfile']['name'];
			if(!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadPath.$fileName)){
				$this->errorStr = 'ERROR uploading file (code '.$_FILES['uploadfile']['error'].'): ';
				if(!is_writable($uploadPath)){
					$this->errorStr .= 'Target path ('.$uploadPath.') is not writable ';
				}
				return false;
			}
			//If a zip file, unpackage and assume that last or only file is the occurrrence file
			if($fileName && substr($fileName,-4) == ".zip"){
				$fileName = '';
				$zipFilePath = $uploadPath.$fileName;
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
						$zip->extractTo($uploadPath,$fileName);
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
			$fullPath = $this->getContentPath().$this->uploadFileName;
			$fh = fopen($fullPath,'rb') or die("Can't open file");
			$this->sourceArr = $this->getHeaderArr($fh);
			fclose($fh);
		}
	}

	public function uploadData(){
		if($this->uploadFileName){
			echo '<li>Initiating import from: '.$this->uploadFileName.'</li>';
			$fullPath = $this->getUploadPath.$this->uploadFileName;
			$fh = fopen($fullPath,'rb') or die("Can't open file");
			$headerArr = $this->getHeaderArr($fh);
			$recCnt = 0;
			echo '<li>Beginning to load records...</li>';
			$shipmentPK = 0;
			while($recordArr = fgetcsv($fh)){
				$recMap = Array();
				foreach($this->fieldMap as $sourceField => $targetField){
					$indexArr = array_keys($headerArr,$sourceField);
					$index = array_shift($indexArr);
					if(array_key_exists($index,$recordArr)){
						$valueStr = $recordArr[$index];
						$recMap[$targetField] = $valueStr;
					}
				}
				if(!$shipmentPK) $shipmentPK = $this->loadShipmentRecord($recMap);
				if($shipmentPK) $this->loadSampleRecord($shipmentPK, $recMap);
				unset($recMap);
				$recCnt++;
			}
			fclose($fh);

			//Delete upload file
			if(file_exists($fullPath)) unlink($fullPath);
		}
		else{
			$this->outputMsg('<li>File Upload FAILED: unable to locate file</li>');
		}
	}

	public function loadShipmentRecord($recArr){
		$sql = 'INSERT INTO NeonShipment(shipmentID, domainID, dateShipped, senderID, shipmentService, shipmentMethod, trackingNumber, importUid) '.
			'VALUES("'.$this->cleanInStr($recArr['shipmentID']).'","'.$this->cleanInStr($recArr['domainID']).'","'.$this->cleanInStr($recArr['dateShipped']).'","'.
			$this->cleanInStr($recArr['senderID']).'","'.$this->cleanInStr($recArr['shipmentService']).'","'.$this->cleanInStr($recArr['shipmentMethod']).'",'.
			(isset($recArr['trackingNumber'])?'"'.$this->cleanInStr($recArr['trackingNumber']).'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
			if(!$this->conn->query($sql)){
				echo 'ERROR loading shipment record: '.$this->conn->error;
				return false;
			}
			return $this->conn->insert_id;
	}

	private function loadSampleRecord($shipmentPK, $recArr){
		$sql = 'INSERT INTO NeonSample(shipmentPK, sampleID, sampleCode, sampleClass, taxonID, individualCount, filterVolume, namedlocation, collectdate, quarantineStatus) '.
			'VALUES('.$shipmentPK.',"'.$this->cleanInStr($recArr['sampleID']).'","'.$this->cleanInStr($recArr['sampleCode']).'","'.$this->cleanInStr($recArr['sampleClass']).'",'.
			(isset($recArr['taxonID'])?'"'.$this->cleanInStr($recArr['taxonID']).'"':'NULL').','.(isset($recArr['individualCount'])?'"'.$this->cleanInStr($recArr['individualCount']).'"':'NULL').','.
			(isset($recArr['filterVolume'])?'"'.$this->cleanInStr($recArr['filterVolume']).'"':'NULL').',"'.$this->cleanInStr($recArr['namedlocation']).'","'.
			$this->cleanInStr($recArr['collectdate']).'",'.(isset($recArr['quarantineStatus'])?'"'.$this->cleanInStr($recArr['quarantineStatus']).'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
		if(!$this->conn->query($sql)){
			echo 'ERROR loading sample record: '.$this->conn->error;
		}
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
		if(substr($path,-1) != '/') $contentPath .= '/';
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
		$sql = 'SELECT samplePK, sampleID, sampleClass, namedlocation, collectdate, quarantineStatus, notes, CONCAT_WS(", ",u.lastname, u.firstname) AS checkinUser, checkinTimestamp, initialtimestamp '.
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

	public function setFieldMap($fieldMap){
		$this->fieldMap = $fieldMap;
	}

	public function getSourceArr(){
		return $this->sourceArr;
	}

	public function getTargetArr(){
		$retArr = array('shipmentid','domainid','dateshipped','senderid','shipmentservice','shipmentmethod','trackingnumber','sampleid','samplecode','sampleclass','taxonid',
			'individualcount','filtervolume','namedlocation','collectdate','quarantinestatus');
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