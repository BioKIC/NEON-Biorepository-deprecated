<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class SpecProcDuplicates extends Manager {

	private $collid;
	private $fieldArr = array();
	private $activeFieldArr = array();
	private $occFieldArr;
	private $geoFieldArr;
	private $evaluationType;
	private $collMetaArr = array();

	function __construct() {
		parent::__construct();
		$this->occurFieldArr = array('family','sciname','taxonRemarks','identifiedBy','dateIdentified','identificationReferences','identificationRemarks','identificationQualifier','typeStatus',
			'recordedBy','recordNumber','associatedCollectors','eventDate','verbatimEventDate','habitat','substrate','fieldNotes','fieldnumber','occurrenceRemarks','informationWithheld',
			'associatedOccurrences','dataGeneralizations','associatedTaxa','dynamicProperties','verbatimAttributes','behavior','reproductiveCondition','cultivationStatus',
			'establishmentMeans','lifeStage','sex','individualCount','samplingProtocol','samplingEffort','preparations',
			'locationID','country','stateProvince','county','municipality','waterBody','locality','localitySecurity','localitySecurityReason','locationRemarks',
			'minimumElevationInMeters','maximumElevationInMeters','verbatimElevation','minimumDepthInMeters','maximumDepthInMeters','verbatimDepth',
			'disposition','storageLocation','language');
		$this->geoFieldArr = array('decimalLatitude','decimalLongitude','geodeticDatum','coordinateUncertaintyInMeters','footprintWKT','georeferencedBy','georeferenceProtocol',
			'georeferenceSources','georeferenceVerificationStatus','georeferenceRemarks','verbatimCoordinates');
		/*
		 $sql = 'SELECT DISTINCT d3.duplicateid, c.institutionCode, c.collectionCode, o3.occid, o3.catalogNumber, o3.otherCatalogNumbers, o3.occurrenceID, '.
		 'o3.family, o3.sciname, o3.recordedBy, o3.recordNumber, o3.country, o3.stateProvince, o3.locality, '.
		 'o3.decimalLatitude, o3.decimalLongitude, o3.geodeticDatum, o3.coordinateUncertaintyInMeters, o3.footprintWKT, o3.verbatimCoordinates, o3.georeferencedBy, '.
		 'o3.georeferenceProtocol, o3.georeferenceSources, o3.georeferenceVerificationStatus, o3.georeferenceRemarks, '.
		 'o3.minimumElevationInMeters, o3.maximumElevationInMeters, o3.verbatimElevation '.
		 'FROM omoccurduplicatelink d INNER JOIN omoccurrences o ON d.occid = o.occid '.
		 'INNER JOIN omoccurduplicatelink d2 ON d.duplicateid = d2.duplicateid '.
		 'INNER JOIN omoccurrences o2 ON d2.occid = o2.occid '.
		 'INNER JOIN omoccurduplicatelink d3 ON d.duplicateid = d3.duplicateid '.
		 'INNER JOIN  omoccurrences o3 ON d3.occid = o3.occid '.
		 'INNER JOIN omcollections c ON o3.collid = c.collid '.
		 'WHERE o.collid = '.$this->collid.' AND o.decimallatitude IS NULL AND o2.collid != '.$this->collid.' AND o2.decimallatitude IS NOT NULL '.
		 'AND ((o3.collid != '.$this->collid.' AND o3.decimallatitude IS NOT NULL) OR o3.collid = '.$this->collid.') '.
		 'ORDER BY d3.duplicateid ';
		 */
	}

	function __destruct(){
 		parent::__destruct();
	}

	//Duplicate matching tools
	public function buildDuplicateArr($evaluationDate, $processingStatus = 'unprocessed',$limit = 100){
		$retArr = array();
		if($this->evaluationType != 'dupe' || $this->evaluationType != 'exsiccate') return false;
		if($this->collid){
			$this->fieldArr = array_merge($this->occurFieldArr,$this->geoFieldArr);
			$sql = 'SELECT o.occid, o.collid, IFNULL(o.catalogNumber,o.othercatalogNumbers) as catalogNumber, '.implode(',',$this->fieldArr).' ';
			$orderBy = '';
			if($this->evaluationType == 'exsiccate'){
				//Match dupes based on Exsiccati
				$sql .= ' ';
				$orderBy = ' ';
			}
			else{
				//Match dupes based on specimen duplicate tables
				$sql .= ',d.duplicateid as dupeid FROM omoccurduplicatelink d INNER JOIN omoccurrences o ON d.occid = o.occid ';
				$orderBy = 'ORDER BY d.duplicateid ';
			}
			$sql .= 'WHERE o.collid = '.$this->collid.' ';
			if($processingStatus) $sql .= 'AND o.processingstatus = "'.$processingStatus.'" ';
			if($evaluationDate){
				$sql .= 'AND o.occid NOT IN(SELECT occid FROM specprocstatus WHERE initialTimestamp > "'.$evaluationDate.'") ';
			}
			if($orderBy) $sql .= $orderBy;
			$rs = $this->conn->query($sql);
			$occid = 0;
			while($r = $rs->fetch_assoc()){
				//Load subject occurrence record into array
				$activeArr = array();
				$occid = $r['occid'];
				$activeArr[0]['occid']['v'] = $r['occid'];
				$activeArr[0]['collid']['v'] = $r['collid'];
				$activeArr[0]['catalogNumber']['v'] = $r['catalogNumber'];
				foreach($this->fieldArr as $fieldName){
					$activeArr[0][$fieldName]['v'] = $r[$fieldName];
				}
				if($this->evaluationType == 'exsiccate'){
					//Exsiccate table

				}
				else{
					//Duplicate table
					$this->appendDuplicates($activeArr,$r['dupeid'],$occid);
					if($evaluatedArr = $this->evaluateDuplicateArr($activeArr)){
						$retArr[$occid] = $evaluatedArr;
					}
				}
				$this->setAsEvaluated($occid);
			}
			$rs->free();
		}
		return $retArr;
	}

	private function appendDuplicates(&$activeArr,$dupeid,$subjectID){
		$status = true;
		$sql = 'SELECT o.occid, o.collid, IFNULL(o.catalogNumber,o.othercatalogNumbers) as catalogNumber, '.implode(',',$this->fieldArr).' '.
			'FROM omoccurrences o INNER JOIN omoccurduplicatelink d ON o.occid = d.occid '.
			'WHERE d.duplicateid = '.$dupeid.' AND d.occid != '.key($subjectID);
		$rs = $this->conn->query($sql);
		$cnt = 1;
		while($r = $rs->fetch_assoc()){
			$activeArr[$cnt]['occid']['v'] = $r['occid'];
			$activeArr[$cnt]['collid']['v'] = $r['collid'];
			$activeArr[$cnt]['catalogNumber']['v'] = $r['catalogNumber'];
			foreach($this->fieldArr as $fieldName){
				$activeArr[$cnt][$fieldName]['v'] = $r[$fieldName];
			}
			$this->collMetaArr[$r->collid] = 0;
			$cnt++;
		}
		$rs->free();
		return $status;
	}

	private function evaluateArr(&$activeArr){
		$status = false;
		$statusCode = 'd';
		$subjectArr = $activeArr[0];
		foreach($this->fieldArr as $fieldName){
			$currentVal = $subjectArr[$fieldName]['v'];
			if(!$currentVal){
				$rateArr = array();
				$topRank = 0;
				$bestStr = '';
				$bestKey = '';
				for($i = 1; $i < count($activeArr); $i++){
					$fieldStr = trim($activeArr[$i][$fieldName]);
					if($fieldStr){
						if(!$bestStr){
							//Seed evaluation values
							$bestStr = $fieldStr;
							$topRank = 1;
						}
						$testStr = $this->normalizeFieldValue($fieldStr);
						$rateArr[$testStr]['i'][] = $i;
						if(array_key_exists($testStr,$rateArr)){
							$rank = $rateArr[$testStr]['r'];
							$rank++;
							if($rank > $topRank){
								$topRank = $rank;
								$bestStr = $fieldStr;
								$bestKey = $testStr;
							}
							$rateArr[$testStr]['r'] = $rank;
						}
						else{
							$rateArr[$testStr]['r'] = 1;
						}
					}
				}
				if($bestStr){
					$activeArr[0][$fieldName]['p'] = $bestStr;
					$this->activeFieldArr[$fieldName] = 1;
				}
				$selectArr = $rateArr[$bestKey]['i'];
				foreach($selectArr as $i){
					$activeArr[$i][$fieldName]['c'] = 's';
				}
			}
		}
		if($statusCode == 'd') $status = false;
		return $status;
	}

	private function normalizeFieldValue($str){
		$str = preg_replace("/[^A-Za-z0-9]/", '', $str);
	}

	private function setAsEvaluated($occid,$processName){
		$sql = 'INSERT INTO specprocstatus(occid,processName) VALUES('.$occid.',"'.$processName.'")';
		if(!$this->conn->query($sql)){
			$this->errorMessage = 'ERROR registering occurrence as evaluated: '.$this->conn->error;
		}
	}

	public function getCollMetaArr(){
		if(!$this->collMetaArr || !current($this->collMetaArr)){
			$collStr = $this->collid;
			if($this->collMetaArr) $collStr .= ','.implode(',',$this->collMetaArr);
			$sql = 'SELECT collid, CONCAT_WS(":",institutionCode, collectionCode) AS collcode, collectionName FROM omcollections WHERE collid IN('.$collStr.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->collMetaArr[$r->collid]['collcode'] = $r->collcode;
				$this->collMetaArr[$r->collid]['name'] = $r->collectionName;
			}
			$rs->free();
		}
		return $this->collMetaArr;
	}

	//NLP Alignment tools
	public function batchBuildFragments(){
		set_time_limit(600);
		$this->logOrEcho('Starting batch process');
		$sql = 'SELECT r.prlid, r.rawstr '.
			'FROM specprocessorrawlabels r LEFT JOIN specprococrfrag f ON r.prlid = f.prlid '.
			'WHERE f.prlid IS NULL LIMIT 1000';
		$rs = $this->conn->query($sql);
		$cnt = 1;
		while($r = $rs->fetch_object()){
			if($this->processFragment($r->rawstr,$r->prlid)){
				if($cnt%1000 == 0) $this->logOrEcho($cnt.' OCR records',1);
			}
			$cnt++;
		}
		$rs->free();
		$this->logOrEcho('Batch process finished');
	}

	private function processFragment($rawOcr,$prlid){
		$status = false;
		//Clean string
		$rawOcr = str_replace('.', ' ',$rawOcr);
		$rawOcr = preg_replace('/\s\s+/',' ',$rawOcr);
		$rawOcr = trim(preg_replace('/[^a-zA-Z0-9\s]/','',$rawOcr));
		if(strlen($rawOcr) > 10){
			//Load into database
			$wordArr = preg_split("/\s/", $rawOcr);
			$previousWord = '';
			$cnt = 0;
			$sqlFrag = '';
			if(count($wordArr) > 1){
				foreach($wordArr as $w){
					if($previousWord){
						$keyTerm = $previousWord.$w;
						$sqlFrag .= ',('.$prlid.',"'.$previousWord.'","'.$w.'","'.$keyTerm.'",'.$cnt.')';
					}
					$previousWord = $w;
				}
				$sql = 'INSERT INTO specprococrfrag(prlid,firstword,secondword,keyterm,wordorder) '.
					'VALUES'.substr($sqlFrag,1);
				//$this->logOrEcho($sql);
				if($this->conn->query($sql)){
					$status = true;
					$cnt++;
				}
				else{
					$this->logOrEcho('ERROR loading terms (#'.$prlid.'): '.$this->conn->error, $indent = 1);
					$this->logOrEcho($sql);
				}
			}
		}
		return $status;
	}

	//Setters and getters
	public function setCollID($id){
		if(is_numeric($id)) $this->collid = $id;
	}

	public function getActiveFieldArr(){
		return $this->activeFieldArr;
	}
}
?>