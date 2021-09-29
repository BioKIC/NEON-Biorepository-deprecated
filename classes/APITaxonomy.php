<?php
require_once($SERVER_ROOT.'/classes/APIBase.php');

class APITaxonomy extends Manager{

	public function __construct(){
		parent::__construct();
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function getTaxon($sciname){
		$retArr = array();
		$sciname = preg_replace('/[^a-zA-Z\-\.× ]+/', '', $sciname);
		$sql = 'SELECT tid, sciname, author FROM taxa WHERE (sciname = "'.$sciname.'")';
		if(preg_match('/\s{1}\D{1}\s{1}/i',$sciname)){
			$sciname = preg_replace('/\s{1}x{1}\s{1}|\s{1}×{1}\s{1}/i', ' _ ', $sciname);
			$sql = 'SELECT tid, sciname, author FROM taxa WHERE (sciname LIKE "'.$sciname.'")';
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tid]['sciname'] = $r->sciname;
			$retArr[$r->tid]['author'] = $r->author;
		}
		$rs->free();
		if(count($retArr) > 1){
			//Is a Homonym, thus get kingdom
			$sql = 'SELECT e.tid, t.sciname FROM taxa t INNER JOIN taxaenumtree e ON t.tid = e.parenttid '.
				'WHERE (e.taxauthid = 1) AND (t.rankid = 10) AND (e.tid IN("'.implode(',',array_keys($retArr)).'"))';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->tid]['kingdom'] = $r->sciname;
			}
			$rs->free();
		}
		return $retArr;
	}

  /**
   * Returns the metadata and taxonomy of a given taxon in the portal
   *
   * @param [string] $sciname scientific name to search
   * @return [array] $retArr with taxon information
   */
	public function getTaxonomy($sciname){
		$retArr = array();
    // metadata
    // $portalUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $portalUrl = "http://$_SERVER[HTTP_HOST]";
    $retArr['meta']['source'] = $portalUrl;
    $date=date('Y-m-d h:i:s');
    $retArr['meta']['datefetched'] = $date;
    
    // taxon
		$sciname = preg_replace('/[^a-zA-Z\-\.× ]+/', '', $sciname);
		$sql = 'SELECT tid, sciname, author, kingdomname, rankid, phylosortsequence, notes, nomenclaturalstatus, securitystatus FROM taxa WHERE (sciname = "'.$sciname.'")';
		if(preg_match('/\s{1}\D{1}\s{1}/i',$sciname)){
			$sciname = preg_replace('/\s{1}x{1}\s{1}|\s{1}×{1}\s{1}/i', ' _ ', $sciname);
			$sql = 'SELECT tid, sciname, author, kingdomname, phylosortsequence, notes, nomenclaturalstatus, securitystatus FROM taxa WHERE (sciname LIKE "'.$sciname.'")';
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
      $retArr['taxon']['tid'] = $r->tid;
			$retArr['taxon']['sciname'] = $r->sciname;
			$retArr['taxon']['author'] = $r->author;
      $retArr['taxon']['kingdomname'] = $r->kingdomname;
      $retArr['taxon']['rankid'] = $r->rankid;
      $retArr['taxon']['phylosortsequence'] = $r->phylosortsequence;
      $retArr['taxon']['notes'] = $r->notes;
      $retArr['taxon']['nomenclaturalstatus'] = $r->nomenclaturalstatus;
      if ($r->securitystatus === '0') {
        $retArr['taxon']['securitystatus'] = "public";
      } else {
        $retArr['taxon']['securitystatus'] = "protected";
      }
		}
		$rs->free();
    // parents
    $tid = $retArr['taxon']['tid'];
    $sqlP = 'SELECT tr.*, t.sciname, t.author, t.rankid, u.rankname, t.kingdomname FROM taxaenumtree tr JOIN taxa t ON tr.parenttid = t.tid JOIN taxonunits u ON t.rankid = u.rankid AND t.kingdomName = u.kingdomName WHERE tr.tid = '.$tid.' ORDER BY t.rankid DESC;';
    $rsP = $this->conn->query($sqlP);
    while($rP = $rsP->fetch_object()){
      $retArr['taxonomy'][strtolower($rP->rankname)] = $rP->sciname;
    }
    $retArr['taxonomy']['kingdom'] = $retArr['taxon']['kingdomname'];
		return $retArr;
	}

	//Setters and getters

}
?>