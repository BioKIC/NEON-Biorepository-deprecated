<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceEditorMaterialSample extends Manager{

	private $occid;

	function __construct(){
		parent::__construct(null,'write');
	}

	function __destruct(){
 		parent::__destruct();
	}

	public function getMaterialSampleArr(){
		$retArr = array();
		$sql = 'SELECT msID, materialSampleType, concentration, concentrationUnit, concentrationMethod, ratioOfAbsorbance260_230, ratioOfAbsorbance260_280, volume, volumeUnit, '.
			'weight, weightUnit, weightMethod, purificationMethod, quality, qualityRemarks, qualityCheckDate, sampleSize, sieving, dnaHybridization, dnaMeltingPoint, '.
			'estimatedSize, poolDnaExtracts, sampleDesignation, initialTimestamp '.
			'FROM ommaterialsample '.
			'WHERE occid = '.$this->occid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$retArr[$r['msID']] = $r;
		}
		$rs->free();
		return $retArr;
	}

	public function setOccid($id){
		if(is_numeric($id)) $this->occid = $id;
	}
}
?>