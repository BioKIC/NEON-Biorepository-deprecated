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

	public function addMaterialSampleArr($postArr){
		if($this->occid){
			$sql = 'INSERT INTO ommaterialsample(occid, materialSampleType, guid, concentration, concentrationUnit, concentrationMethod, ratioOfAbsorbance260_230, ratioOfAbsorbance260_280,
				volume, volumeUnit, weight, weightUnit, weightMethod, purificationMethod, quality, qualityRemarks, qualityCheckDate, sampleSize, sieving, dnaHybridization, dnaMeltingPoint,
				estimatedSize, poolDnaExtracts, sampleDesignation)
				VALUES('.$this->occid.','.($postArr['materialSampleType']?'"'.$postArr['materialSampleType'].'"':'NULL').','.($postArr['guid']?'"'.$postArr['guid'].'"':'NULL').','.
				(is_numeric($postArr['concentration'])?$postArr['concentration']:'NULL').','.($postArr['concentrationUnit']?'"'.$postArr['concentrationUnit'].'"':'NULL').','.
				($postArr['concentrationMethod']?'"'.$postArr['concentrationMethod'].'"':'NULL').','.(is_numeric($postArr['ratioOfAbsorbance260_230'])?$postArr['ratioOfAbsorbance260_230']:'NULL').','.
				(is_numberic($postArr['ratioOfAbsorbance260_280'])?$postArr['ratioOfAbsorbance260_280']:'NULL').','.(is_numeric($postArr['volume'])?$postArr['volume']:'NULL').','.
				($postArr['volumeUnit']?'"'.$postArr['volumeUnit'].'"':'NULL').','.(is_numeric($postArr['weight'])?$postArr['weight']:'NULL').','.
				($postArr['weightUnit']?'"'.$postArr['weightUnit'].'"':'NULL').','.($postArr['purificationMethod']?'"'.$postArr['purificationMethod'].'"':'NULL').','.
				($postArr['quality']?'"'.$postArr['quality'].'"':'NULL').','.($postArr['qualityRemarks']?'"'.$postArr['qualityRemarks'].'"':'NULL').','.
				($postArr['qualityCheckDate']?'"'.$postArr['qualityCheckDate'].'"':'NULL').','.($postArr['sampleSize']?'"'.$postArr['sampleSize'].'"':'NULL').',';
				($postArr['qualityCheckDate']?'"'.$postArr['qualityCheckDate'].'"':'NULL').','.($postArr['sampleSize']?'"'.$postArr['sampleSize'].'"':'NULL').',';
				($postArr['qualityCheckDate']?'"'.$postArr['qualityCheckDate'].'"':'NULL').','.($postArr['sampleSize']?'"'.$postArr['sampleSize'].'"':'NULL').',';
				($postArr['qualityCheckDate']?'"'.$postArr['qualityCheckDate'].'"':'NULL').','.($postArr['sampleSize']?'"'.$postArr['sampleSize'].'"':'NULL').',';
				($postArr['qualityCheckDate']?'"'.$postArr['qualityCheckDate'].'"':'NULL').','.($postArr['sampleSize']?'"'.$postArr['sampleSize'].'"':'NULL').',';
				($postArr['qualityCheckDate']?'"'.$postArr['qualityCheckDate'].'"':'NULL').','.($postArr['sampleSize']?'"'.$postArr['sampleSize'].'"':'NULL').')';
				if($this->conn->query($sql)){
				return true;
			}
			else{

				return false;
			}
		}
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
	public function setOccid($id){
		if(is_numeric($id)) $this->occid = $id;
	}

	public function getOccid(){
		return $this->occid;
	}

}
?>