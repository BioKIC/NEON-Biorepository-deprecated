<?php
include_once('Manager.php');
include_once('ImInventories.php');
include_once('ProfileManager.php');

class ChecklistAdmin extends Manager{

	private $clid;
	private $clName;

	function __construct() {
		parent::__construct(null, 'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getMetaData($pid = null){
		$inventoryManager = new ImInventories();
		$inventoryManager->setClid($this->clid);
		$retArr = $inventoryManager->getChecklistMetadata($pid);
		$this->clName = $retArr['name'];
		return $retArr;
	}

	public function createChecklist($postArr){
		$newClid = 0;
		if($GLOBALS['SYMB_UID'] && isset($postArr['name'])){
			$postArr['defaultsettings'] = $this->getDefaultJson($postArr);

			$inventoryManager = new ImInventories();
			$newClid = $inventoryManager->insertChecklist($postArr);

			if($newClid){
				//Add permissions to allow creater to be an editor and then reset user permissions stored in browser cache
				$inventoryManager->insertUserRole($GLOBALS['SYMB_UID'], 'ClAdmin', 'fmchecklists', $newClid, $GLOBALS['SYMB_UID']);
				$newPManager = new ProfileManager();
				$newPManager->setUserName($GLOBALS['USERNAME']);
				$newPManager->authenticate();
				if($postArr['type'] == 'excludespp' && $postArr['excludeparent']){
					//If is an exclusion checklists, link to parent checklist
					if(!$inventoryManager->insertChildChecklist($postArr['excludeparent'], $newClid, $GLOBALS['SYMB_UID'])){
						$this->errorMessage = 'ERROR linking exclusion checklist to parent: '.$this->conn->error;
					}
				}
			}
		}
		return $newClid;
	}

	public function editChecklist($postArr){
		$status = false;
		if($GLOBALS['SYMB_UID'] && isset($postArr['name'])){
			$postArr['defaultsettings'] = $this->getDefaultJson($postArr);

			$inventoryManager = new ImInventories();
			$inventoryManager->setClid($this->clid);
			$status = $inventoryManager->updateChecklist($postArr);
			if(!$status) $this->errorMessage = $inventoryManager->getErrorMessage();
		}
		return $status;
	}

	public function deleteChecklist($delClid){
		$inventoryManager = new ImInventories();
		$inventoryManager->setClid($delClid);
		$status = $inventoryManager->deleteChecklist();
		return $status;
	}

	private function getDefaultJson($postArr){
		$defaultArr = Array();
		$defaultArr['ddetails'] = array_key_exists('ddetails',$postArr)?1:0;
		$defaultArr['dsynonyms'] = array_key_exists('dsynonyms',$postArr)?1:0;
		$defaultArr['dcommon'] = array_key_exists('dcommon',$postArr)?1:0;
		$defaultArr['dimages'] = array_key_exists('dimages',$postArr)?1:0;
		$defaultArr['dvoucherimages'] = array_key_exists('dvoucherimages',$postArr)?1:0;
		$defaultArr['dvouchers'] = array_key_exists('dvouchers',$postArr)?1:0;
		$defaultArr['dauthors'] = array_key_exists('dauthors',$postArr)?1:0;
		$defaultArr['dalpha'] = array_key_exists('dalpha',$postArr)?1:0;
		$defaultArr['activatekey'] = array_key_exists('activatekey',$postArr)?1:0;
		return json_encode($defaultArr);
	}

	//Polygon functions
	public function getFootprintWkt(){
		$retStr = '';
		if($this->clid){
			$sql = 'SELECT footprintwkt FROM fmchecklists WHERE (clid = '.$this->clid.')';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retStr = $r->footprintwkt;
			}
			$rs->free();
		}
		return $retStr;
	}

	public function savePolygon($polygonStr){
		$status = true;
		if($this->clid){
			$sql = 'UPDATE fmchecklists SET footprintwkt = '.($polygonStr?'"'.$this->cleanInStr($polygonStr).'"':'NULL').' WHERE (clid = '.$this->clid.')';
			if(!$this->conn->query($sql)){
				echo 'ERROR saving polygon to checklist: '.$this->conn->error;
				$status = false;
			}
		}
		return $status;
	}

	//Child checklist functions
	public function getChildrenChecklist(){
		$retArr = Array();
		$targetStr = $this->clid;
		do{
			$sql = 'SELECT c.clid, c.name, child.clid as pclid '.
				'FROM fmchklstchildren child INNER JOIN fmchecklists c ON child.clidchild = c.clid '.
				'WHERE child.clid IN('.trim($targetStr,',').') '.
				'ORDER BY c.name ';
			$rs = $this->conn->query($sql);
			$targetStr = '';
			while($r = $rs->fetch_object()){
				$retArr[$r->clid]['name'] = $r->name;
				$retArr[$r->clid]['pclid'] = $r->pclid;
				$targetStr .= ','.$r->clid;
			}
			$rs->free();
		}while($targetStr);
		asort($retArr);
		return $retArr;
	}

	public function getParentChecklists(){
		$retArr = Array();
		$targetStr = $this->clid;
		do{
			$sql = 'SELECT c.clid, c.name, child.clid as pclid '.
				'FROM fmchklstchildren child INNER JOIN fmchecklists c ON child.clid = c.clid '.
				'WHERE child.clidchild IN('.trim($targetStr,',').') ';
			$rs = $this->conn->query($sql);
			$targetStr = '';
			while($r = $rs->fetch_object()){
				$retArr[$r->clid] = $r->name;
				$targetStr .= ','.$r->clid;
			}
			if($targetStr) $targetStr = substr($targetStr,1);
			$rs->free();
		}while($targetStr);
		asort($retArr);
		return $retArr;
	}

	public function getUserChecklistArr(){
		$retArr = array();
		if(isset($GLOBALS['USER_RIGHTS']['ClAdmin'])){
			$clidStr = implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']);
			if($clidStr){
				$sql = 'SELECT clid, name FROM fmchecklists WHERE (clid IN('.$clidStr.')) AND (type != "excludespp") ';
				if($this->clid) $sql .= 'AND (clid <> '.$this->clid.') ';
				$sql .= 'ORDER BY name';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[$r->clid] = $r->name;
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function getUserProjectArr(){
		$retArr = array();
		if(isset($GLOBALS['USER_RIGHTS']['ProjAdmin'])){
			$pidStr = implode(',',$GLOBALS['USER_RIGHTS']['ProjAdmin']);
			if($pidStr){
				$sql = 'SELECT pid, projname FROM fmprojects WHERE (pid IN('.$pidStr.')) ORDER BY projname';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[$r->pid] = $r->projname;
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function addChildChecklist($clidAdd){
		$inventoryManager = new ImInventories();
		$status = $inventoryManager->insertChildChecklist($this->clid, $clidAdd, $GLOBALS['SYMB_UID']);
		return $status;
	}

	public function deleteChildChecklist($clidDel){
		$statusStr = '';
		$sql = 'DELETE FROM fmchklstchildren WHERE clid = '.$this->clid.' AND clidchild = '.$clidDel;
		if(!$this->conn->query($sql)){
			$statusStr = 'ERROR deleting child checklist link';
		}
		return $statusStr;
	}

	//Point functions
	public function addPoint($tid,$lat,$lng,$notes){
		$statusStr = '';
		if(is_numeric($tid) && is_numeric($lat) && is_numeric($lng)){
			$sql = 'INSERT INTO fmchklstcoordinates(clid,tid,decimallatitude,decimallongitude,notes) VALUES('.$this->clid.','.$tid.','.$lat.','.$lng.',"'.$this->cleanInStr($notes).'")';
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR: unable to add point. '.$this->conn->error;
			}
		}
		return $statusStr;
	}

	public function removePoint($pointPK){
		$statusStr = '';
		if($pointPK && is_numeric($pointPK)){
			if(!$this->conn->query('DELETE FROM fmchklstcoordinates WHERE (chklstcoordid = '.$pointPK.')')){
				$statusStr = 'ERROR: unable to remove point. '.$this->conn->error;
			}
		}
		return $statusStr;
	}

	//Editor management
	public function getEditors(){
		$editorArr = array();
		$sql = 'SELECT u.uid, CONCAT_WS(", ",u.lastname,u.firstname) AS uname, l.username, CONCAT_WS(", ",u2.lastname,u2.firstname) AS assignedby '.
			'FROM userroles ur INNER JOIN users u ON ur.uid = u.uid '.
			'LEFT JOIN userlogin l ON u.uid = l.uid '.
			'LEFT JOIN users u2 ON ur.uidassignedby = u2.uid '.
			'WHERE (ur.role = "ClAdmin") AND (ur.tablepk = '.$this->clid.') '.
			'ORDER BY u.lastname,u.firstname';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$uName = $r->uname;
				if(strlen($uName) > 60) $uName = substr($uName,0,60);
				if($r->username) $uName .= ' ('.$r->username.')';
				$editorArr[$r->uid]['name'] = $uName;
				$editorArr[$r->uid]['assignedby'] = $r->assignedby;
			}
			$rs->free();
		}
		return $editorArr;
	}

	public function addEditor($u){
		$statusStr = '';
		if(is_numeric($u) && $this->clid){
			$sql = 'INSERT INTO userroles(uid,role,tablename,tablepk,uidassignedby) VALUES('.$u.',"ClAdmin","fmchecklists",'.$this->clid.','.$GLOBALS["SYMB_UID"].')';
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR: unable to add editor; SQL: '.$this->conn->error;
			}
		}
		return $statusStr;
	}

	public function deleteEditor($u){
		$statusStr = '';
		$sql = 'DELETE FROM userroles WHERE (uid = '.$u.') AND (role = "ClAdmin") AND (tablepk = '.$this->clid.') ';
		if(!$this->conn->query($sql)){
			$statusStr = 'ERROR: unable to remove editor; SQL: '.$this->conn->error;
		}
		return $statusStr;
	}

	//Get list data
	public function getReferenceChecklists(){
		$retArr = array();
		$sql = 'SELECT clid, name FROM fmchecklists WHERE (access = "public") AND (type != "excludespp") ';
		if(isset($GLOBALS['USER_RIGHTS']['ClAdmin'])){
			$clidStr = implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']);
			if($clidStr) $sql .= 'OR clid IN('.$clidStr.') ';
		}
		$sql .= 'ORDER BY name';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->clid] = $row->name;
		}
		$rs->free();
		return $retArr;
	}

	public function getPoints($tid){
		$retArr = array();
		$sql = 'SELECT c.chklstcoordid, c.decimallatitude, c.decimallongitude, c.notes FROM fmchklstcoordinates c WHERE c.clid = '.$this->clid.' AND c.tid = '.$tid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->chklstcoordid]['lat'] = $r->decimallatitude;
			$retArr[$r->chklstcoordid]['lng'] = $r->decimallongitude;
			$retArr[$r->chklstcoordid]['notes'] = $r->notes;
		}
		$rs->free();
		return $retArr;
	}

	public function getTaxa(){
		$retArr = array();
		$sql = 'SELECT t.tid, t.sciname FROM fmchklsttaxalink l INNER JOIN taxa t ON l.tid = t.tid WHERE l.clid = '.$this->clid.' ORDER BY t.sciname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tid] = $r->sciname;
		}
		$rs->free();
		return $retArr;
	}

	public function getUserList(){
		$returnArr = Array();
		$sql = 'SELECT u.uid, CONCAT(CONCAT_WS(", ",u.lastname,u.firstname)," (",l.username,")") AS uname '.
			'FROM users u INNER JOIN userlogin l ON u.uid = l.uid '.
			'ORDER BY u.lastname,u.firstname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$returnArr[$r->uid] = $r->uname;
		}
		$rs->free();
		return $returnArr;
	}

	public function getInventoryProjects(){
		$retArr = Array();
		if($this->clid){
			$sql = 'SELECT p.pid, p.projname FROM fmprojects p INNER JOIN fmchklstprojlink pl ON p.pid = pl.pid WHERE pl.clid = '.$this->clid.' ORDER BY p.projname';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->pid] = $r->projname;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getPotentialProjects($pidArr){
		$retArr = Array();
		if($pidArr){
			$sql = 'SELECT pid, projname FROM fmprojects WHERE pid IN('.implode(',',$pidArr).') ORDER BY projname';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->pid] = $r->projname;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function addProject($pid){
		$statusStr = '';
		if(is_numeric($pid)){
			$sql = 'INSERT INTO fmchklstprojlink(pid, clid) VALUES('.$pid.','.$this->clid.')';
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR adding project: '.$this->conn->error;
			}
		}
		return $statusStr;
	}

	public function deleteProject($pid){
		$statusStr = '';
		if(is_numeric($pid)){
			$sql = 'DELETE FROM fmchklstprojlink WHERE (pid = '.$pid.') AND (clid = '.$this->clid.')';
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR deleting project: '.$this->conn->error;
			}
		}
		return $statusStr;
	}

	public function hasVoucherProjects(){
		global $USER_RIGHTS;
		$retBool = false;
		$runQuery = true;
		$sql = 'SELECT collid, collectionname FROM omcollections WHERE (colltype = "Observations" OR colltype = "General Observations") ';
		if(!array_key_exists('SuperAdmin',$USER_RIGHTS)){
			$collInStr = '';
			foreach($USER_RIGHTS as $k => $v){
				if($k == 'CollAdmin' || $k == 'CollEditor'){
					$collInStr .= ','.implode(',',$v);
				}
			}
			if($collInStr){
				$sql .= 'AND collid IN ('.substr($collInStr,1).') ';
			}
			else{
				$runQuery = false;
			}
		}
		$sql .= ' LIMIT 1';
		//echo $sql;
		if($runQuery){
			if($rs = $this->conn->query($sql)){
				if($r = $rs->fetch_object()){
					$retBool = true;
				}
				$rs->free();
			}
		}
		return $retBool;
	}

	public function getManagementLists($uid){
		$returnArr = Array();
		if(is_numeric($uid)){
			//Get project and checklist IDs from userpermissions
			$clStr = '';
			$projStr = '';
			$sql = 'SELECT role,tablepk FROM userroles WHERE (uid = '.$uid.') AND (role = "ClAdmin" OR role = "ProjAdmin") ';
			//$sql = 'SELECT pname FROM userpermissions '.
			//	'WHERE (uid = '.$uid.') AND (pname LIKE "ClAdmin-%" OR pname LIKE "ProjAdmin-%") ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($r->role == 'ClAdmin') $clStr .= ','.$r->tablepk;
				if($r->role == 'ProjAdmin') $projStr .= ','.$r->tablepk;
			}
			$rs->free();
			if($clStr){
				//Get checklists
				$sql = 'SELECT clid, name FROM fmchecklists WHERE (clid IN('.substr($clStr,1).')) ORDER BY name';
				$rs = $this->conn->query($sql);
				while($row = $rs->fetch_object()){
					$returnArr['cl'][$row->clid] = $row->name;
				}
				$rs->free();
			}
			if($projStr){
				//Get projects
				$sql = 'SELECT pid, projname FROM fmprojects WHERE (pid IN('.substr($projStr,1).')) ORDER BY projname';
				$rs = $this->conn->query($sql);
				while($row = $rs->fetch_object()){
					$returnArr['proj'][$row->pid] = $row->projname;
				}
				$rs->free();
			}
		}
		return $returnArr;
	}

	public function parseChecklist($tid, $taxa, $targetClid, $parentClid, $targetPid, $copyAttributes){
		$inventoryManager = new ImInventories();
		$fieldArr = array();
		if(!$targetClid){
			$clMeta = $this->getMetaData();
			$fieldArr['name'] = $clMeta['name'].' child checklist - '.$taxa;
			if($copyAttributes){
				$extraArr = array('authors','type','locality','publication','abstract','notes','latcentroid','longcentroid','pointradiusmeters','private','defaultsettings','dynamicsql','uid','type','sortsequence');
				foreach($extraArr as $fieldName){
					$fieldArr[$fieldName] = $clMeta[$fieldName];
				}
			}
			$targetClid = $inventoryManager->insertChecklist($fieldArr);
		}
		if($parentClid === 0){
			$fieldArr['name'] = $clMeta['name'].' parent checklist - '.$taxa;
			$parentClid = $inventoryManager->insertChecklist($fieldArr);
		}
	}

	//Misc set/get functions
	public function setClid($clid){
		if(is_numeric($clid)) $this->clid = $clid;
	}

	public function getClName(){
		return $this->clName;
	}
}
?>