<?php
include_once('Manager.php');

class TPEditorManager extends Manager {

	protected $tid;
	protected $rankid;
	private $parentTid;
	protected $taxAuthId = 1;
	private $sciname;
	private $author;
	protected $family;
	protected $acceptance = true;
	private $forwarded = false;

	private $acceptedArr = array();
	private $synonymArr = array();
	private $submittedArr = array();
	private $langArr = false;

	public function __construct(){
		parent::__construct(null,'write');
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function setTid($tid){
		$status = false;
		if(is_numeric($tid)){
			$this->tid = $tid;
			$sql = 'SELECT tid, sciname, author, rankid FROM taxa WHERE (tid = '.$this->tid.') ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->submittedArr['tid'] = $r->tid;
				$this->submittedArr['sciname'] = $r->sciname;
				$this->submittedArr['author'] = $r->author;
				$this->submittedArr['rankid'] = $r->rankid;
				$this->tid = $r->tid;
				$this->sciname = $r->sciname;
				$this->author = $r->author;
				$this->rankid = $r->rankid;
			}
			$rs->free();

			$sql2 = 'SELECT ts.family, ts.parenttid, t.tid, t.sciname, t.author, t.rankid, t.securitystatus '.
				'FROM taxstatus ts INNER JOIN taxa t ON ts.tidaccepted = t.tid '.
				'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (ts.tid = '.$this->tid.') ';
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$this->acceptedArr[$r2->tid]['sciname'] = $r2->sciname;
				$this->acceptedArr[$r2->tid]['author'] = $r2->author;
				$this->acceptedArr[$r2->tid]['rankid'] = $r2->rankid;
				$this->acceptedArr[$r2->tid]['family'] = $r2->family;
				$this->acceptedArr[$r2->tid]['parenttid'] = $r2->parenttid;
				$this->family = $r2->family;
				$this->parentTid = $r2->parenttid;
				if($r2->securitystatus > 0) $this->displayLocality = 0;
				$status = true;
			}
			$rs2->free();

			if($this->tid != key($this->acceptedArr)){
				if(count($this->acceptedArr) == 1){
					$this->forwarded = true;
					$this->tid = key($this->acceptedArr);
					$this->sciname = $this->acceptedArr[$this->tid]['sciname'];
					$this->author = $this->acceptedArr[$this->tid]['author'];
					$this->rankid = $this->acceptedArr[$this->tid]['rankid'];
					$this->family = $this->acceptedArr[$this->tid]['family'];
					$this->parentTid = $this->acceptedArr[$this->tid]['parenttid'];
				}
				else{
					$this->acceptance = false;
				}
			}
		}
		return $status;
	}

	public function getTid(){
		return $this->tid;
	}

	public function getSciName(){
		return $this->sciname;
	}

	public function getSubmittedValue($k=0){
		return $this->submittedArr[$k];
	}

	public function getChildrenTaxa(){
		$childrenArr = Array();
		$sql = "SELECT t.Tid, t.SciName, t.Author ".
			"FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid ".
			"WHERE ts.taxauthid = '.$this->taxAuthId.' AND (ts.ParentTid = ".$this->tid.") ORDER BY t.SciName";
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$childrenArr[$r->Tid]["sciname"] = $r->SciName;
			$childrenArr[$r->Tid]["author"] = $r->Author;
		}
		$rs->free();
		return $childrenArr;
	}

	public function getSynonym(){
		$synArr = Array();
		$sql = "SELECT t2.tid, t2.SciName, ts.SortSequence ".
			"FROM (taxa t1 INNER JOIN taxstatus ts ON t1.tid = ts.tidaccepted) ".
			"INNER JOIN taxa t2 ON ts.tid = t2.tid ".
			"WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (ts.tid <> ts.TidAccepted) AND (t1.tid = ".$this->tid.") ".
			"ORDER BY ts.SortSequence, t2.SciName";
		//echo $sql."<br>";
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$synArr[$r->tid]["sciname"] = $r->SciName;
			$synArr[$r->tid]["sortsequence"] = $r->SortSequence;
		}
		$rs->free();
		return $synArr;
	}

	public function editSynonymSort($synSort){
		$status = "";
		foreach($synSort as $editKey => $editValue){
			if(is_numeric($editKey) && is_numeric($editValue)){
				$sql = "UPDATE taxstatus SET SortSequence = ".$editValue." WHERE (tid = ".$editKey.") AND (TidAccepted = ".$this->tid.')';
				//echo $sql."<br>";
				if(!$this->conn->query($sql)){
					$status .= $this->conn->error."\nSQL: ".$sql.";<br/> ";
				}
			}
		}
		if($status) $status = "Errors with editVernacularSort method:<br/> ".$status;
		return $status;
	}

	public function getVernaculars(){
		$vernArr = Array();
		$sql = 'SELECT v.vid, v.vernacularname, v.langid, l.langname, v.source, v.username, v.notes, v.sortsequence '.
			'FROM taxavernaculars v INNER JOIN adminlanguages l ON v.langid = l.langid '.
			'WHERE (tid = '.$this->tid.') '.
			'ORDER BY sortsequence';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$vernArr[$r->langname][$r->vid]['vernname'] = $this->cleanOutStr($r->vernacularname);
			$vernArr[$r->langname][$r->vid]['source'] = $this->cleanOutStr($r->source);
			$vernArr[$r->langname][$r->vid]['username'] = $r->username;
			$vernArr[$r->langname][$r->vid]['notes'] = $this->cleanOutStr($r->notes);
			$langID = $r->langid;
			if(!$langID){
				if($this->langArr === false) $this->setLangArr();
				if(array_key_exists($langID, $this->langArr)) $langID = $this->langArr[$langID];
				else $langID = 1;
			}
			$vernArr[$r->langname][$r->vid]['langid'] = $langID;
			$vernArr[$r->langname][$r->vid]['sort'] = $r->sortsequence;
		}
		$rs->free();
		return $vernArr;
	}

	public function editVernacular($postArr){
		$status = false;
		$vid = $postArr["vid"];
		if(is_numeric($vid)){
			$vernName = $this->cleanInStr($postArr['vernname']);
			$langID = $postArr['langid'];
			if(!is_numeric($langID)) $langID = 1;
			$sortSequence = $postArr['sortsequence'];
			$source = $this->cleanInStr($postArr['source']);
			$notes = $this->cleanInStr($postArr['notes']);
			if(!is_numeric($sortSequence)) $sortSequence = 1;
			$username = $this->cleanInStr($GLOBALS['PARAMS_ARR']['un']);

			$sql = 'UPDATE taxavernaculars SET VernacularName = "'.$vernName.'", langid = '.$langID.', SortSequence = '.$sortSequence;
			if($source) $sql .= ', source = "'.$source.'"';
			if($notes) $sql .= ', notes = "'.$notes.'"';
			if($username) $sql .= ', username = "'.$username.'"';
			$sql .= ' WHERE (vid = '.$vid.')';
			//echo $sql;
			if($this->conn->query($sql)) $status = true;
			else{
				$status = false;
				$this->errorMessage = "Error:editingVernacular: ".$this->conn->error;
			}
		}
		return $status;
	}

	public function addVernacular($postArr){
		$status = false;
		$vernName = $this->cleanInStr($postArr['vernname']);
		$langID = $postArr['langid'];
		if(!is_numeric($langID)) $langID = 1;
		$source = $this->cleanInStr($postArr['source']);
		$notes = $this->cleanInStr($postArr['notes']);
		$username = $this->cleanInStr($GLOBALS['PARAMS_ARR']['un']);
		$sortSeq = $postArr['sortsequence'];
		if(!is_numeric($sortSeq)) $sortSeq = 1;
		$sql = 'INSERT INTO taxavernaculars (tid,vernacularname,langid,source,notes,username,sortsequence) '.
			'VALUES ('.$this->getTid().',"'.$vernName.'",'.$langID.','.($source?'"'.$source.'"':'NULL').','.($notes?'"'.$notes.'"':'NULL').','.($username?'"'.$username.'"':'NULL').','.$sortSeq.')';
		if($this->conn->query($sql)) $status = true;
		else{
			$this->errorMessage = "Error:addingNewVernacular: ".$this->conn->error."\nSQL: ".$sql;
			$status = false;
		}
		return $status;
	}

	public function deleteVernacular($delVid){
		$status = false;
		if(is_numeric($delVid)){
			$sql = 'DELETE FROM taxavernaculars WHERE (VID = '.$delVid.')';
			if($this->conn->query($sql)) $status = true;
			else{
				$this->errorMessage = 'Error:deleteVernacular: '.$this->conn->error;
				$status = false;
			}
		}
		return $status;
	}

	public function getChildrenArr(){
		$returnArr = Array();
		$sql = 'SELECT t.tid, t.sciname FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'WHERE ts.taxauthid = '.$this->taxAuthId.' AND (ts.parenttid = '.$this->tid.')';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$returnArr[$r->tid] = $r->sciname;
		}
		$rs->free();
		return $returnArr;
	}

	public function getMaps(){
		$mapArr = Array();
		$sql = "SELECT tm.mid, tm.url, tm.title, tm.initialtimestamp ".
			"FROM taxamaps tm INNER JOIN taxa t ON tm.tid = t.TID ".
			"WHERE (tm.tid = ".$this->tid.") ";
		$rs = $this->conn->query($sql);
		$mapCnt = 0;
		while($r = $rs->fetch_object()){
			$mapArr[$mapCnt]["url"] = $r->url;
			$mapArr[$mapCnt]["title"] = $r->title;
			$mapCnt++;
		}
		$rs->free();
		return $mapArr;
	}

	public function getLinks(){
		$linkArr = Array();
		$sql = 'SELECT tl.url, tl.title '.
			'FROM taxalinks tl INNER JOIN taxa ON tl.tid = taxa.TID '.
			'WHERE (taxa.TID = '.$this->tid.') ';
		$rs = $this->conn->query($sql);
		$linkCnt = 0;
		while($r = $rs->fetch_object()){
			$linkArr[$linkCnt]["url"] = $r->url;
			$linkArr[$linkCnt]["title"] = $r->title;
			$linkCnt++;
		}
		$rs->free();
		return $linkArr;
	}

	//Misc data functions
	private function setLangArr(){
		if($this->langArr === false){
			$this->langArr = array();
			$sql = 'SELECT langid, langname, iso639_1 FROM adminlanguages';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->langArr[$r->langname] = $r->langid;
				$this->langArr[$r->iso639_1] = $r->langid;
			}
			$rs->free();
		}
	}

	public function getLangArr(){
		$retArr = array();
		$sql = 'SELECT langid, langname, iso639_1 FROM adminlanguages ORDER BY langname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->langid] = $r->langname.' ('.$r->iso639_1.')';
		}
		$rs->free();
		return $retArr;
	}

	//Setters and getters
	public function getAuthor(){
		return $this->author;
	}

	public function getFamily(){
		return $this->family;
	}

	public function getRankId(){
		return $this->rankid;
	}

	public function getParentTid(){
		return $this->parentTid;
	}

	public function isAccepted(){
		return $this->acceptance;
	}

	public function isForwarded(){
		return $this->forwarded;
	}
}
?>