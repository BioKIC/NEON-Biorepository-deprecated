<?php
include_once("TPEditorManager.php");

class TPDescEditorManager extends TPEditorManager{

 	public function __construct(){
 		parent::__construct();
 	}

 	public function __destruct(){
 		parent::__destruct();
 	}

	public function getDescriptions(){
		$descrArr = Array();
		$langArr = false;
		$sql = 'SELECT t.tid, t.sciname, d.tdbid, d.caption, d.source, d.sourceurl, d.displaylevel, d.notes, l.langid, d.language '.
			'FROM taxadescrblock d LEFT JOIN adminlanguages l ON d.langid = l.langid ';
		if($this->acceptance){
			$sql .= 'INNER JOIN taxstatus ts ON d.tid = ts.tid '.
				'INNER JOIN taxa t ON ts.tid = t.tid '.
				'WHERE (ts.TidAccepted = '.$this->tid.') AND (ts.taxauthid = '.$this->taxAuthId.') ';
		}
		else{
			$sql .= 'INNER JOIN taxa t ON d.tid = t.tid WHERE (d.tid = '.$this->tid.') ';
		}
		$sql .= 'ORDER BY d.DisplayLevel ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$langID = $r->langid;
				if(!$langID){
					if($langArr === false) $langArr = $this->getLangMap();
					if(array_key_exists($r->language, $langArr)) $langID = $langArr[$r->language];
					else $langID = 1;
				}
				$descrArr[$langID][$r->tdbid]['caption'] = $r->caption;
				$descrArr[$langID][$r->tdbid]['source'] = $r->source;
				$descrArr[$langID][$r->tdbid]['sourceurl'] = $r->sourceurl;
				$descrArr[$langID][$r->tdbid]['displaylevel'] = $r->displaylevel;
				$descrArr[$langID][$r->tdbid]['notes'] = $r->notes;
				$descrArr[$langID][$r->tdbid]['tid'] = $r->tid;
				$descrArr[$langID][$r->tdbid]['sciname'] = $r->sciname;
			}
			$rs->free();
		}
		else{
			trigger_error('Unable to get descriptions; '.$this->conn->error);
		}
		foreach($descrArr as $langId => $dArr){
			foreach($dArr as $tdbid => $d2Arr){
				$sql2 = 'SELECT tdbid, tdsid, heading, statement, notes, displayheader, sortsequence FROM taxadescrstmts WHERE (tdbid = '.$tdbid.') ORDER BY sortsequence';
				if($rs2 = $this->conn->query($sql2)){
					while($r2 = $rs2->fetch_object()){
						$descrArr[$langId][$tdbid]['stmts'][$r2->tdsid]['heading'] = $r2->heading;
						$descrArr[$langId][$tdbid]['stmts'][$r2->tdsid]['statement'] = $r2->statement;
						$descrArr[$langId][$tdbid]['stmts'][$r2->tdsid]['notes'] = $r2->notes;
						$descrArr[$langId][$tdbid]['stmts'][$r2->tdsid]['displayheader'] = $r2->displayheader;
						$descrArr[$langId][$tdbid]['stmts'][$r2->tdsid]['sortsequence'] = $r2->sortsequence;
					}
					$rs2->free();
				}
				else{
					trigger_error('Unable to get statements; '.$this->conn->error);
				}
			}
		}
		return $descrArr;
	}

	public function addDescriptionBlock($postArr){
		$status = false;
		if(is_numeric($postArr['tid'])){
			$sql = 'INSERT INTO taxadescrblock(tid,uid,langid,displaylevel,notes,caption,source,sourceurl) '.
				'VALUES('.$postArr['tid'].','.$GLOBALS['SYMB_UID'].','.
				($postArr['langid']?$this->cleanInStr($postArr['langid']):'NULL').','.
				(is_numeric($postArr['displaylevel'])?$postArr['displaylevel']:30).','.
				($postArr['notes']?'"'.$this->cleanInStr($postArr['notes']).'"':'NULL').','.
				($postArr['caption']?'"'.$this->cleanInStr($postArr['caption']).'"':'NULL').','.
				($postArr['source']?'"'.$this->cleanInStr($postArr['source']).'"':'NULL').','.
				($postArr['sourceurl']?'"'.$postArr['sourceurl'].'"':'NULL').')';
			//echo $sql;
			if($this->conn->query($sql)) $status = true;
			else $this->errorMessage = 'ERROR adding description block: '.$this->conn->error;
		}
		return $status;
	}

	public function editDescriptionBlock($postArr){
		$status = false;
		if(is_numeric($postArr['tdbid'])){
			$sql = 'UPDATE taxadescrblock '.
				'SET langid = '.($postArr['langid']?$this->cleanInStr($postArr['langid']):'NULL').','.
				(is_numeric($postArr['displaylevel'])?'displaylevel = '.$postArr['displaylevel'].',':'').
				'notes = '.($postArr['notes']?'"'.$this->cleanInStr($postArr['notes']).'"':'NULL').','.
				'caption = '.($postArr['caption']?'"'.$this->cleanInStr($postArr['caption']).'"':'NULL').','.
				'source = '.($postArr['source']?'"'.$this->cleanInStr($postArr['source']).'"':'NULL').','.
				'sourceurl = '.($postArr['sourceurl']?'"'.$this->cleanInStr($postArr['sourceurl']).'"':'NULL').' '.
				'WHERE (tdbid = '.$postArr['tdbid'].')';
			//echo $sql;
			if($this->conn->query($sql)){
				$status = true;
			}
			else{
				$this->errorMessage = 'ERROR editing description block: '.$this->conn->error;
			}
		}
		return $status;
	}

	public function deleteDescriptionBlock($tdbid){
		$status = false;
		if(is_numeric($tdbid)){
			$sql = 'DELETE FROM taxadescrblock WHERE (tdbid = '.$tdbid.')';
			if($this->conn->query($sql)) $status = true;
			else $this->errorMessage = 'ERROR deleting description block: '.$this->conn->error;
		}
		return $status;
	}

	public function remapDescriptionBlock($tdbid){
		$status = false;
		if(is_numeric($tdbid)){
			$displayLevel = 1;
			$sql = 'SELECT max(displaylevel) as maxdl FROM taxadescrblock WHERE tid = '.$this->tid;
			if($rs = $this->conn->query($sql)){
				if($r = $rs->fetch_object()){
					$displayLevel = $r->maxdl + 1;
				}
				$rs->free();
			}
			$sql = 'UPDATE taxadescrblock SET tid = '.$this->tid.',displaylevel = '.$displayLevel.' WHERE tdbid = '.$tdbid;
			if($this->conn->query($sql)) $status = true;
			else $this->errorMessage = 'ERROR remapping description block: '.$this->conn->error;
		}
		return $status;
	}

	public function addStatement($stArr){
		$status = false;
		$stmtStr = $this->cleanInStr($stArr['statement']);
		if(substr($stmtStr,0,3) == '<p>' && substr($stmtStr,-4) == '</p>') $stmtStr = trim(substr($stmtStr,3,strlen($stmtStr)-7));
		if($stmtStr && $stArr['tdbid'] && is_numeric($stArr['tdbid'])){
			$sql = 'INSERT INTO taxadescrstmts(tdbid,heading,statement,displayheader'.($stArr['sortsequence']?',sortsequence':'').') '.
				'VALUES('.$stArr['tdbid'].','.($stArr['heading']?'"'.$this->cleanInStr($stArr['heading']).'"':'""').',"'.$stmtStr.'",'.(array_key_exists('displayheader',$stArr)?'1':'0').
				($stArr['sortsequence']?','.$this->cleanInStr($stArr['sortsequence']):'').')';
			if($this->conn->query($sql)) $status = true;
			else $this->errorMessage = 'ERROR adding description statement: '.$this->conn->error;
		}
		return $status;
	}

	public function editStatement($stArr){
		$status = false;
		$stmtStr = $this->cleanInStr($stArr['statement']);
		if(substr($stmtStr,0,3) == '<p>' && substr($stmtStr,-4) == '</p>') $stmtStr = trim(substr($stmtStr,3,strlen($stmtStr)-7));
		if($stmtStr && $stArr['tdsid'] && is_numeric($stArr["tdsid"])){
			$sql = 'UPDATE taxadescrstmts '.
				'SET heading = '.($stArr['heading']?'"'.$this->cleanInStr($stArr['heading']).'"':'""').','.
				'statement = "'.$stmtStr.'",displayheader = '.(array_key_exists('displayheader',$stArr)?'1':'0').
				(is_numeric($stArr['sortsequence'])?',sortsequence = '.$stArr['sortsequence']:'').
				' WHERE (tdsid = '.$stArr['tdsid'].')';
			//echo $sql;
			if($this->conn->query($sql)) $status = true;
			else $this->errorMessage = "ERROR editing description statement: ".$this->conn->error;
		}
		return $status;
	}

	public function deleteStatement($tdsid){
		$status = true;
		if(is_numeric($tdsid)){
			$sql = 'DELETE FROM taxadescrstmts WHERE (tdsid = '.$tdsid.')';
			if($this->conn->query($sql)) $status = true;
			else $this->errorMessage = "ERROR deleting description statement: ".$this->conn->error;
		}
		return $status;
	}
}
?>