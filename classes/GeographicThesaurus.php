<?php
include_once ($SERVER_ROOT . '/classes/Manager.php');

class GeographicThesaurus extends Manager{

	function __construct(){
		parent::__construct(null, 'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getGeograpicList($conditionTerm = null){
		$retArr = array();
		$sql = 'SELECT t.geoThesID, t.geoTerm, t.abbreviation, t.iso2, t.iso3, t.numCode, t.category, t.geoLevel, t.termStatus, t.acceptedID, a.geoterm as acceptedTerm
			FROM geographicthesaurus t LEFT JOIN geographicthesaurus a ON t.acceptedID = a.geoThesID ';
		if($conditionTerm && is_numeric($conditionTerm)) $sql .= 'WHERE (t.parentID = '.$conditionTerm.') ';
		else $sql .= 'WHERE (t.parentID IS NULL) ';
		$sql .= 'ORDER BY t.geoTerm';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->geoThesID]['geoTerm'] = $r->geoTerm;
			$retArr[$r->geoThesID]['abbreviation'] = $r->abbreviation;
			$retArr[$r->geoThesID]['iso2'] = $r->iso2;
			$retArr[$r->geoThesID]['iso3'] = $r->iso3;
			$retArr[$r->geoThesID]['numCode'] = $r->numCode;
			$retArr[$r->geoThesID]['category'] = $r->category;
			$retArr[$r->geoThesID]['geoLevel'] = $r->geoLevel;
			$retArr[$r->geoThesID]['termStatus'] = $r->termStatus;
			$retArr[$r->geoThesID]['acceptedID'] = $r->acceptedID;
			$retArr[$r->geoThesID]['acceptedTerm'] = $r->acceptedTerm;
		}
		$rs->free();

		if($retArr){
			$childCntArr = $this->setChildCnt(implode(',',array_keys($retArr)));
			foreach($childCntArr as $id => $cnt){
				$retArr[$id]['childCnt'] = $cnt;
			}
		}
		return $retArr;
	}

	public function getGeograpicUnit($geoThesID){
		$retArr = array();
		if(is_numeric($geoThesID)){
			$sql = 'SELECT t.geoThesID, t.geoTerm, t.abbreviation, t.iso2, t.iso3, t.numCode, t.category, t.geoLevel, t.parentID, p.geoTerm as parentTerm, t.notes, t.termStatus,
				t.acceptedID, a.geoterm as acceptedTerm
				FROM geographicthesaurus t LEFT JOIN geographicthesaurus a ON t.acceptedID = a.geoThesID
				LEFT JOIN geographicthesaurus p ON t.parentID = p.geoThesID
				WHERE t.geoThesID = '.$geoThesID;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr['geoThesID'] = $r->geoThesID;
				$retArr['geoTerm'] = $r->geoTerm;
				$retArr['abbreviation'] = $r->abbreviation;
				$retArr['iso2'] = $r->iso2;
				$retArr['iso3'] = $r->iso3;
				$retArr['numCode'] = $r->numCode;
				$retArr['category'] = $r->category;
				$retArr['geoLevel'] = $r->geoLevel;
				$retArr['acceptedID'] = $r->acceptedID;
				$retArr['acceptedTerm'] = $r->acceptedTerm;
				$retArr['parentID'] = $r->parentID;
				$retArr['parentTerm'] = $r->parentTerm;
				$retArr['notes'] = $r->notes;
				$retArr['termStatus'] = $r->termStatus;
			}
			$rs->free();
			if($retArr){
				$childArr = $this->setChildCnt($retArr['geoThesID']);
				$cnt = 0;
				if($childArr) $cnt = current($childArr);
				$retArr['childCnt'] = $cnt;
			}
		}
		return $retArr;
	}

	public function editGeoUnit($postArr){
		if(is_numeric($postArr['geoThesID'])){
			if(!$postArr['geoTerm']){
				$this->errorMessage = 'ERROR editing geoUnit: geographic term must have a value';
				return false;
			}
			$sql = 'UPDATE geographicthesaurus '.
				'SET geoterm = "'.$this->cleanInStr($postArr['geoTerm']).'", '.
				'abbreviation = '.($postArr['abbreviation']?'"'.$this->cleanInStr($postArr['abbreviation']).'"':'NULL').', '.
				'iso2 = '.($postArr['iso2']?'"'.$this->cleanInStr($postArr['iso2']).'"':'NULL').', '.
				'iso3 = '.($postArr['iso3']?'"'.$this->cleanInStr($postArr['iso3']).'"':'NULL').', '.
				'numcode = '.(is_numeric($postArr['numCode'])?'"'.$this->cleanInStr($postArr['numCode']).'"':'NULL').', '.
				'geoLevel = '.(is_numeric($postArr['geoLevel'])?$this->cleanInStr($postArr['geoLevel']):'NULL').', '.
				'acceptedID = '.(is_numeric($postArr['acceptedID'])?'"'.$this->cleanInStr($postArr['acceptedID']).'"':'NULL').', '.
				'parentID = '.(is_numeric($postArr['parentID'])?'"'.$this->cleanInStr($postArr['parentID']).'"':'NULL').', '.
				'notes = '.($postArr['notes']?'"'.$this->cleanInStr($postArr['notes']).'"':'NULL').' '.
				'WHERE (geoThesID = '.$postArr['geoThesID'].')';
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR saving edits: '.$this->conn->error;
				return false;
			}
		}
		return true;
	}

	public function addGeoUnit($postArr){
		if(!$postArr['geoTerm']){
			$this->errorMessage = 'ERROR adding geoUnit: geographic term must have a value';
			return false;
		}
		else{
			$sql = 'INSERT INTO geographicthesaurus(geoterm, abbreviation, iso2, iso3, numcode, geoLevel, acceptedID, parentID, notes) '.
				'VALUES("'.$this->cleanInStr($postArr['geoTerm']).'", '.
				($postArr['abbreviation']?'"'.$this->cleanInStr($postArr['abbreviation']).'"':'NULL').', '.
				($postArr['iso2']?'"'.$this->cleanInStr($postArr['iso2']).'"':'NULL').', '.
				($postArr['iso3']?'"'.$this->cleanInStr($postArr['iso3']).'"':'NULL').', '.
				(is_numeric($postArr['numCode'])?'"'.$this->cleanInStr($postArr['numCode']).'"':'NULL').', '.
				(is_numeric($postArr['geoLevel'])?$this->cleanInStr($postArr['geoLevel']):'NULL').', '.
				(is_numeric($postArr['acceptedID'])?'"'.$this->cleanInStr($postArr['acceptedID']).'"':'NULL').', '.
				(is_numeric($postArr['parentID'])?'"'.$this->cleanInStr($postArr['parentID']).'"':'NULL').', '.
				($postArr['notes']?'"'.$this->cleanInStr($postArr['notes']).'"':'NULL').')';
			echo $sql;
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR adding unit: '.$this->conn->error;
				return false;
			}
		}
		return true;
	}

	public function addChildGeoUnit($postArr){
		//Add new child
		//Uses an INSERT INTO sql statement

		/* 	$statusStr = '';
		if(is_numeric($postArr['geoThesID'])){
			$sql = 'UPDATE geographicthesaurus '.
				'SET geoterm = '.($postArr['geoterm']?'"'.$postArr['geoterm'].'"':'NULL').
				', iso2 = '.($postArr['iso2']?'"'.$postArr['iso2'].'"':'NULL').
				', iso3 = '.($postArr['iso3']?'"'.$postArr['iso3'].'"':'NULL').
				' WHERE (geoThesID = '.$postArr['geoThesID'].')';
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: changes saved';
			}
			else{
				$statusStr = 'ERROR: changes not saved'.$this->conn->error;
			}
		}
		return $statusStr; */
	}

	public function deleteGeoUnit($geoThesID){
		if(is_numeric($geoThesID)){
			$sql = 'DELETE FROM geographicthesaurus WHERE (geoThesID = '.$geoThesID.')';
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR deleting geoUnit: '.$this->conn->error;
				return false;
			}
		}
		return true;
	}

	private function setChildCnt($geoIdStr){
		$retArr = array();
		$sql = 'SELECT parentID, count(*) as cnt FROM geographicthesaurus WHERE parentID IN('.$geoIdStr.') GROUP BY parentID ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->parentID] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function getCoordStatistics(){
		$retArr = array();
		$totalCnt = 0;
		$sql = 'SELECT COUNT(*) AS cnt FROM omoccurrences WHERE (collid IN(' . $this->collStr . '))';
		$rs = $this->conn->query($sql);
		while ($r = $rs->fetch_object()) {
			$totalCnt = $r->cnt;
		}
		$rs->free();

		// Full count
		$sql2 = 'SELECT COUNT(occid) AS cnt FROM omoccurrences WHERE (collid IN(' . $this->collStr . ')) AND (decimalLatitude IS NULL) AND (georeferenceVerificationStatus IS NULL) ';
		if ($rs2 = $this->conn->query($sql2)) {
			if ($r2 = $rs2->fetch_object()) {
				$retArr['total'] = $r2->cnt;
				$retArr['percent'] = round($r2->cnt * 100 / $totalCnt, 1);
			}
			$rs2->free();
		}

		return $retArr;
	}

	//Misc data retrieval functions
	public function getParentGeoTermArr($geoLevelMax = 0){
		$retArr = array();
		$sql = 'SELECT t.geoThesID, CONCAT_WS(" ",t.geoTerm,CONCAT(" (",p.geoTerm,")")) AS geoTerm FROM geographicthesaurus t LEFT JOIN geographicthesaurus p ON t.parentID = p.geoThesID ';
		if($geoLevelMax) $sql .= 'WHERE t.geoLevel < '.$geoLevelMax.' ';
		$sql .= 'ORDER BY t.geoLevel, t.geoTerm';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->geoThesID] = $r->geoTerm;
		}
		$rs->free();
		return $retArr;
	}

	public function getAcceptedGeoTermArr($geoLevelMax = 0){
		$retArr = array();
		$sql = 'SELECT geoThesID, geoTerm FROM geographicthesaurus ';
		if($geoLevelMax) $sql .= 'WHERE (geoLevel = '.$geoLevelMax.') ';
		$sql .= 'ORDER BY geoTerm';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->geoThesID] = $r->geoTerm;
		}
		$rs->free();
		return $retArr;
	}

	public function getGeoRankArr(){
		$rankArr = array();
		if(isset($GLOBALS['GEO_THESAURUS_RANKING']) && is_array($GLOBALS['GEO_THESAURUS_RANKING'])){
			$rankArr = $GLOBALS['GEO_THESAURUS_RANKING'];
		}
		else{
			$rankArr = array(10 => 'Oceans', 20 => 'Island Group', 30 => 'Island', 40 => 'Continent', 50 => 'Country', 60 => 'State / Province', 70 => 'County', 80 => 'Municipality',
				100 => 'City / Town', 110 => 'Place Name', 150 => 'Lake / Pond', 160 => 'River / Creek');
		}
		return $rankArr;
	}

	// Setters and getters


	//Misc junction
	public function transferDataFromLkupTables(){
		/*
		INSERT INTO geographicthesaurus(geoterm,iso2,iso3,numcode,category,geoLevel,termstatus)
		SELECT countryName, iso, iso3, numcode, "Country", 1 as geoLevel, 1 as termStatus
		FROM lkupcountry
		WHERE iso IS NOT NULL;

		SELECT * FROM geographicthesaurus ORDER BY geoLevel, geoTerm LIMIT 100000;

		INSERT INTO geographicthesaurus(geoterm,abbreviation,parentID,category,geoLevel,termStatus)
		SELECT DISTINCT s.stateName, s.abbrev, t.geoThesID, 'State', 2 as geoLevel, 1 as termStatus
		FROM lkupcountry c INNER JOIN lkupstateprovince s ON c.countryid = s.countryid
		INNER JOIN geographicthesaurus t ON c.iso = t.iso2
        WHERE t.category = "country" AND t.termstatus = 1
		LIMIT 1000000;

		INSERT INTO geographicthesaurus(geoterm,parentID,category,geoLevel,termStatus)
		SELECT DISTINCT c.countyName, t.geoThesID, 'County', 3 as geoLevel, 1 as termStatus
		FROM lkupstateprovince s INNER JOIN lkupcounty c ON s.stateid = c.stateid
		INNER JOIN geographicthesaurus t ON s.stateName = t.geoterm
		WHERE t.category = "State" AND t.termstatus = 1 AND (c.countyName NOT LIKE "% County" AND c.countyName NOT LIKE "% Parish");
		 */
	}


}
?>