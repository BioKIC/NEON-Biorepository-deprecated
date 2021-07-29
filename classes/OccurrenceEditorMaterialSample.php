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
		$sql = 'SELECT msID, materialSampleType, guid, concentration, concentrationUnit, concentrationMethod, ratioOfAbsorbance260_230, ratioOfAbsorbance260_280, volume, volumeUnit, '.
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

	//Data lookup functions
	public function getMSTypeControlValues(){
		$retArr = array();
		$sql = 'SELECT v.fieldName, t.cvTermID, t.term FROM ctcontrolvocabterm t INNER JOIN ctcontrolvocab v ON t.cvID = v.cvID WHERE v.tableName = "ommaterialsample" ORDER BY t.term';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->fieldName][] = $r->term;
		}
		return $retArr;
	}

	//Setters and getters

}
?>