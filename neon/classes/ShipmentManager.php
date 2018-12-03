<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class ShipmentManager{

	private $conn;
	private $shipmentPK;

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
	public function importManifest(){

	}

	//Export functions
	public function exportShipmentList(){

	}

	//Setters and getters
	public function setShipmentPK($id){
		if(is_numeric($id)) $this->shipmentPK = $id;
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