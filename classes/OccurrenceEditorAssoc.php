<?php
include_once 'OccurrenceEditorManager.php';
class OccurrenceEditorAssoc extends OccurrenceEditorManager {

	public function __construct($conn = null){
		parent::__construct();
	}

	public function __destruct(){
		parent::__destruct();
	}

	//Occurrence associations
	public function getAssociatedOccurrences(){
		$retArr = array();
		$assocOccidArr = array();
		$uidArr = array();
		$sql = 'SELECT assocOccurID, occid, occidAssociate, relationship, subType, resourceUrl, externalIdentifier, dynamicProperties, createdUid, modifiedUid, modifiedTimestamp, initialTimestamp '.
			'FROM omassociatedoccurrence'.
			'WHERE (occid = '.$this->occid.') OR (occidAssociate = '.$this->occid.')';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$assocOccid = $r->occidAssociate;
				$relationship = $r->relationship;
				if($this->occid == $r->occidAssociate){
					$assocOccid = $r->occid;
					$relationship = $this->getInverseRelationship($relationship);
				}
				$assocOccidArr[$assocOccid] = $r->assocOccurID;
				$retArr[$r->assocOccurID]['occidAssociate'] = $assocOccid;
				$retArr[$r->assocOccurID]['relationship'] = $relationship;
				$retArr[$r->assocOccurID]['subType'] = $assocOccid;
				$retArr[$r->assocOccurID]['resourceUrl'] = $r->resourceUrl;
				$retArr[$r->assocOccurID]['externalIdentifier'] = $r->externalIdentifier;
				$retArr[$r->assocOccurID]['dynamicProperties'] = $r->dynamicProperties;
				$retArr[$r->assocOccurID]['ts'] = $r->modifiedTimestamp;
				$uid = ($r->modifiedUid?$r->modifiedUid:$r->createdUid);
				$uidArr[$uid] = $r->assocOccurID;
			}
			$rs->free();
			//Update modifiedBy data
			$sql = 'SELECT uid, CONCAT_WS("; ",lastname, firstname) as username FROM users WHERE uid IN('.implode(',',array_keys($uidArr)).')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$uidArr[$r->uid]]['definedBy'] = $r->username;
			}
			$rs->free();
			//Grab catalog number of associations
			$sql = 'SELECT o.occid, IFNULL(c.institutioncode, c.collectioncode) as collcode, IFNULL(o.catalogNumber,o.otherCatalogNumbers) as catnum '.
				'FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid '.
				'WHERE o.occid IN('.implode(',',array_keys($assocOccidArr)).')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$assocOccidArr[$r->occid]]['collcode'] = $r->collcode;
				$retArr[$assocOccidArr[$r->occid]]['catnum'] = $r->catnum;

			}
			$rs->free();
		}
		return $retArr;
	}

	public function getAssociateByIdentifier($id,$target,$collidTarget){
		$retArr = array();
		$id = $this->cleanInStr($id);
		$sql = '';
		if($collidTarget && is_numeric($collidTarget)) $sql .= 'AND (o.collid = '.$collidTarget.') ';
		if($target == 'occid'){
			if(is_numeric($id)) $sql .= 'AND (occid = '.$id.') ';
		}
		else $sql .= 'AND (catalogNumber = '.$id.') OR (othercatalognumber = '.$id.') ';
		$sql = 'SELECT o.occid, o.catalogNumber, o.otherCatalogNumbers, o.recordedBy, o.recordNumber, IFNULL(o.eventDate,o.verbatimEventDate) as eventDate, '.
			'IFNULL(c.institutionCode,c.collectionCode) AS collcode, c.collectionName '.
			'FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid WHERE '.substr($sql, 4);
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->occid]['catnum'] = $r->catalogNumber?$r->catalogNumber:$r->otherCatalogNumbers;
			$retArr[$r->occid]['collinfo'] = $r->recordedBy.($r->recordNumber?' ('.$r->recordNumber.')':'').' '.$r->eventDate;
		}
		$rs->free();
		return $retArr;
	}


	//Checklist voucher functions
	public function getVoucherChecklists(){
		$retArr = array();
		$sql = 'SELECT c.clid, c.name FROM fmchecklists c INNER JOIN fmvouchers v ON c.clid = v.clid WHERE v.occid = '.$this->occid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->clid] = $r->name;
		}
		$rs->free();
		asort($retArr);
		return $retArr;
	}

	//Genetic link functions
	public function getGeneticArr(){
		$retArr = array();
		if($this->occid){
			$sql = 'SELECT idoccurgenetic, identifier, resourcename, locus, resourceurl, notes FROM omoccurgenetic WHERE occid = '.$this->occid;
			$result = $this->conn->query($sql);
			if($result){
				while($r = $result->fetch_object()){
					$retArr[$r->idoccurgenetic]['id'] = $r->identifier;
					$retArr[$r->idoccurgenetic]['name'] = $r->resourcename;
					$retArr[$r->idoccurgenetic]['locus'] = $r->locus;
					$retArr[$r->idoccurgenetic]['resourceurl'] = $r->resourceurl;
					$retArr[$r->idoccurgenetic]['notes'] = $r->notes;
				}
				$result->free();
			}
			else{
				trigger_error('Unable to get genetic data; '.$this->conn->error,E_USER_WARNING);
			}
		}
		return $retArr;
	}
}
?>