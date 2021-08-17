<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceCollectionProperty extends Manager {

	private $collid;
	private $collMetaArr = array();
	private $dynPropArr = array();

	public function __construct(){
		parent::__construct(null,'write');
	}

	public function __destruct(){
		parent::__destruct();
	}

	private function setDynamicPropertyArr(){
		if($this->collid){
			$sql = 'SELECT p.collPropID, p.propCategory, p.propTitle, p.propJson, p.notes, l.username, IFNULL(p.modifiedTimestamp, p.initialTimestamp) AS ts
				FROM omcollproperties p LEFT JOIN userlogin l ON p.modifiedUid WHERE (p.collid = '.$this->collid.') ';
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_assoc()){
					$this->dynPropArr[$r->propCategory][$r->collPropID]['title'] = $r->propTitle;
					$this->dynPropArr[$r->propCategory][$r->collPropID]['json'] = $r->propJson;
					$this->dynPropArr[$r->propCategory][$r->collPropID]['modified'] = $r->ts;
					$this->dynPropArr[$r->propCategory][$r->collPropID]['username'] = $r->username;
					$this->dynPropArr[$r->propCategory][$r->collPropID]['notes'] = $r->notes;
				}
				$rs->free();
			}
		}
	}

	public function addProperty($category, $title, $json, $notes=null){
		if($this->collid){
			$sql = 'INSERT INTO omcollproperties(propCategory, propTitle, propJson, notes, modifiedUid)
				VALUES("'.$this->cleanInStr($category).'","'.$this->cleanInStr($title).'","'.$this->cleanInStr($json).'",'.($notes?'"'.$this->cleanInStr($notes).'"':'NULL').','.$GLOBALS['SYMB_UID'].')';
			if(!$this->conn->query($sql)){
				$this->errorMessage = 'ERROR adding Collection Property: '.$this->conn->error;
				return false;
			}
			return true;
		}
	}

	//System transfer functions
	public function getSystemConvertionCode(){
		$statusCode = 0;
		if(!$this->collMetaArr['dynamicProperties'] || $this->dynPropArr) $statusCode = 1;
		else $statusCode = 2;
		return $statusCode;
	}

	public function transferDynamicProperties(){
		$status = false;
		$dynPropArr = array();
		$sql = 'SELECT dynamicProperties FROM omcollections WHERE (collid = '.$this->collid.') AND (dynamicProperties IS NOT NULL)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			if($r->dynamicProperties){
				$dynPropArr = json_decode($r->dynamicProperties,true);
				$status = true;
			}
		}
		$rs->free();

		if(isset($dynPropArr['editorProps']['modules-panel'])){
			$this->addProperty('editorProperties', 'Module Activation', json_decode($dynPropArr['editorProps']['modules-panel']));
		}
		if(isset($dynPropArr['sesar'])){
			$this->addProperty('sesarTools', 'IGSN Profile', json_decode($dynPropArr['sesar']));
		}
		if(isset($dynPropArr['publicationProps'])){
			$this->addProperty('publicationProperties', 'Publication Properties', json_decode($dynPropArr['publicationProps']));
		}
		if(isset($dynPropArr['labelFormats'])){
			foreach($dynPropArr['labelFormats'] as $v){
				$this->addProperty('labelFormat', $v['title'], json_decode($v));
			}
		}
		return $status;
	}

	//Misc data collection function
	private function setCollMetaArr(){
		if($this->collid){
			$sql = 'SELECT institutionCode, collectionCode, collectionName, dynamicProperties FROM omcollections WHERE collid = '.$this->collid;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$this->collMetaArr['collName'] = $r->collectionName.' ('.$r->institutionCode.($r->collectionCode?'-'.$r->collectionCode:'').')';
				$this->collMetaArr['dynamicProperties'] = $r->dynamicProperties;
			}
			$rs->free();
		}
	}

	//Setters and getters
	public function setCollid($collid){
		if(is_numeric($collid)){
			$this->collid = $collid;
			$this->setCollMetaArr();
			$this->setDynamicPropertyArr();
		}
	}

	public function getCollMetaArr(){
		return $this->collMetaArr;
	}

	public function getDynPropArr(){
		return $this->dynPropArr;
	}
}
?>