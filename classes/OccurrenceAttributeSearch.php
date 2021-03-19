<?php
include_once($SERVER_ROOT.'/classes/OccurrenceAttributes.php');

class OccurrenceAttributeSearch extends OccurrenceAttributes {

  //use $setAttributes = false in call to ->getTraitArr, if reverting to using OccurrenceAttributes class

  //private $traitSearchArr = array();
  public function __construct(){
		parent::__construct('readonly');
	}

	public function __destruct(){
		parent::__destruct();
	}

  public function getTraitSearchArr($traitID = null){
    if($traitID) {
      $traitIDArr = array_map('trim', explode(',', $traitID));
      if (($key = array_search('0', $traitIDArr)) !== false) {
          unset($traitIDArr[$key]);
      } // this purges potential zeros from the array
      $traitID = implode(',', $traitIDArr);
      foreach($traitIDArr as $tid) {
        if(!is_numeric($tid)) return null; // reject non-numeric IDs
      }
    }
    unset($this->traitArr);
    $this->traitArr = array();
    $this->setTraitSearchArr($traitID);
    $this->setTraitStates();
    return $this->traitArr;
  }

  private function setTraitSearchArr($traitID){
    $sql = 'SELECT traitid, traitname, traittype, units, description, refurl, notes, dynamicproperties FROM tmtraits WHERE traittype IN("UM","OM","TF","NU") ';
    if($traitID) $sql .= 'AND (traitid IN('.$traitID.'))';
    $rs = $this->conn->query($sql);
    while($r = $rs->fetch_object()){
      if(!isset($this->traitArr[$r->traitid])){
        $this->traitArr[$r->traitid]['name'] = $r->traitname;
        $this->traitArr[$r->traitid]['type'] = $r->traittype;
        $this->traitArr[$r->traitid]['props'] = $r->dynamicproperties;
        //Get dependent traits and append to return array
        $this->setDependentTraits($r->traitid);
      }
    }
    $rs->free();
    return $this->traitArr;
  }

  public function echoTraitSearchForm($traitID){
		echo $this->getTraitSearchHTML($traitID,true);
	}

	private function getTraitSearchHTML($traitID,$display,$classStr=''){
		$controlType = '';
		if($this->traitArr[$traitID]['props']){
			$propArr = json_decode($this->traitArr[$traitID]['props'],true);
			if(isset($propArr[0]['controlType'])) $controlType = $propArr[0]['controlType'];
		}
		$innerStr = '<div style="clear:both">';
		if(isset($this->traitArr[$traitID]['states'])){
			if($this->traitArr[$traitID]['type']=='TF'){
				$innerStr .= '<div style="float:left;margin-left: 15px">'.$this->traitArr[$traitID]['name'].':</div>';
				$innerStr .= '<div style="clear:both;margin-left: 25px">';
			}
			else $innerStr .= '<div style="float:left;">';
			$attrStateArr = $this->traitArr[$traitID]['states'];
			foreach($attrStateArr as $sid => $sArr){
				$isCoded = false;
				if(array_key_exists('coded',$sArr)){
					if(is_numeric($sArr['coded'])) $isCoded = $sArr['coded'];
					else $isCoded = true;
					$this->stateCodedArr[$sid] = $sid;
				}
				$depTraitIdArr = array();
				if(isset($sArr['dependTraitID']) && $sArr['dependTraitID']) $depTraitIdArr = $sArr['dependTraitID'];
				if($this->traitArr[$traitID]['type']=='NU'){
					$innerStr .= '<div title="'.$sArr['description'].'" style="clear:both">';
					$innerStr .= $sArr['name'].
					$innerStr .= ': <input name="traitid-'.$traitID.'[]" id="traitstateid-'.$sid.'" class="'.$classStr.'" type="text" value="'.$sid.'-'.($isCoded!==false?$isCoded:'').'" onchange="traitChanged(this)" style="width:50px" /> ';
					if($depTraitIdArr){
						foreach($depTraitIdArr as $depTraitId){
							$innerStr .= $this->getTraitSearchHTML($depTraitId,$isCoded,trim($classStr.' child-'.$sid));
						}
					}
				}
				else{
					if($controlType == 'checkbox' || $controlType == 'radio'){
						$innerStr .= '<div title="'.$sArr['description'].'" style="clear:both">';
						$innerStr .= '<input name="traitid-'.$traitID.'[]" id="traitstateid-'.$sid.'" class="'.$classStr.'" type="'.$controlType.'" value="'.$sid.'" '.($isCoded?'checked':'').' onchange="traitChanged(this)" /> ';
						$innerStr .= $sArr['name'];
					}
					elseif($controlType == 'select'){
						$innerStr .= '<option value="'.$sid.'" '.($isCoded?'selected':'').'>'.$sArr['name'].'</option>';
					}
					if($depTraitIdArr){
						foreach($depTraitIdArr as $depTraitId){
							$innerStr .= $this->getTraitSearchHTML($depTraitId,$isCoded,trim($classStr.' child-'.$sid));
						}
					}
					if($controlType != 'select') $innerStr .= '</div>';
				}
			}
			$innerStr .= '</div>';
		}
		$innerStr .= '</div>';
		//Display if trait has been coded or is the first/base trait (e.g. $indend == 0)
		$divClass = '';
		if($classStr){
			$classArr = explode(' ',$classStr);
			$divClass = array_pop($classArr);
		}
		$outStr = '<div class="'.$divClass.'" style="margin-left:'.($classStr?'10':'').'px; display:'.($display?'block':'none').';">';
		if($controlType == 'select'){
			$outStr .= '<select name="stateid">';
			$outStr .= '<option value="">Select State</option>';
			$outStr .= '<option value="">------------------------------</option>';
			$outStr .= $innerStr;
			$outStr .= '</select>';
		}
		else{
			$outStr .= $innerStr;
		}
		$outStr .= '</div>';
		return $outStr;
	}

}
