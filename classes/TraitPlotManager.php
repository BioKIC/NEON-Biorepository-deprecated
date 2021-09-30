<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TaxonProfile.php');
include_once($SERVER_ROOT.'/classes/TraitPolarPlot.php');


class TraitPlotManager extends TaxonProfile {
	// consider extending TPEditorManager to have plots written to description block statements instead

	// PROPERTIES
	private $traitId;
	private $stateId;
	private $traitName;
	private $stateNameArr = array();
	// private $taxonArr = array(); // get this from parent
	private $plotInstance;
	private $plotCaption;


	// METHODS

	// ### Public Methods ###
  public function __construct($type = "polar"){
		parent::__construct();
		switch(strtolower($type)) {
			case "polar":
				$this->plotInstance = new PolarPlot();
				break;
			// case "bar":
			//	$this->plotInstance = new BarPlot();
			//	break;
			// case "box":
			// 	$this->plotInstance = new BoxPlot();
			// 	break;
			// case "line":
			// 	$this->plotInstance = new LinePlot();
			// 	break;
			// case "point":
			// 	$this->plotInstance = new PointPlot();
			// 	break;
			default:
				$this->plotInstance = new PolarPlot();
		}
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function setTraitStateId($tsid){
		if(is_numeric($tsid) && $tsid > 0){
			$this->stateId = $tsid;
			$this->traitId = null;
			$this->setTraitStateNames();
			$this->setTraitName();
		}
	}

	public function setTraitId($traitid){
		if(is_numeric($traitid) && $traitid > 0){
			$this->traitId = $traitid;
			$this->stateId = null;
			$this->setTraitName();
			$this->setTraitStateNames();
		}
	}


	public function getTraitName(){
		if(isset($this->traitName)){
			$retStr = $this->traitName;
		} else {
			$retStr = "Trait information unavailable";
		}
		return $retStr;
	}

	public function getStateName(){
		if(isset($this->stateNameArr)){
			$retStr = implode(", ", $this->stateNameArr);
		} else {
			$retStr = "Trait state unavailable";
		}
		return $retStr;
	}

	public function getPlotCaption() {
		return $this->plotCaption;
	}

	public function getViewboxWidth() {
		return $this->plotInstance->getPlotWidth();
	}

	public function getViewboxHeight() {
		return $this->plotInstance->getPlotHeight();
	}

	public function monthlyPolarPlot() {
		if($this->rankId > 179) {  // limit to genus and below
			$this->plotInstance->setAxisNumber(12);
			$this->plotInstance->setAxisRotation(15);
			$this->plotInstance->setTickNumber(3);
			$this->plotInstance->setAxisLabels(array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'));
			$this->plotInstance->setDataValues($this->summarizeTraitByMonth());
			return $this->plotInstance->display();
		}
	}

	public function display() {
		return $this->plotInstance->display();
	} // this function looks weird because I want this manager
	  // to run different plot types, but that isn't finished yet.


	### Private methods ###
	private function setTraitName(){
		$this->traitName = null;
		if($this->traitId){
			$sql = 'SELECT DISTINCT traitname FROM tmtraits WHERE traitid = ' . $this->traitId;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->traitName = $r->traitname;
			}
			$rs->free();
		} elseif($this->stateId) {
			$sql = 'SELECT DISTINCT traitname FROM tmtraits WHERE traitid = (SELECT DISTINCT traitid FROM tmstates WHERE stateid = ' . $this->stateId .' LIMIT 1);';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->traitName = $r->traitname;
			}
			$rs->free();
		} else {
			$this->traitName = 'Trait information unavailable';
		}
	}

	private function setTraitStateNames(){
		$this->stateNameArr = array();
		if($this->traitId){
			$sql = 'SELECT stateid, statename FROM tmstates WHERE traitid = ' . $this->traitId . ' ORDER BY sortseq;';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->stateNameArr[$r->stateid] = $r->statename;
			}
			$rs->free();
		} elseif($this->stateId) {
			$sql = 'SELECT DISTINCT statename FROM tmstates WHERE stateid = ' . $this->stateId . ';';
			$rs = $this->conn->query($sql);
			$r = $rs->fetch_object();
			$this->stateNameArr[$this->stateId] = $r->statename;
			$rs->free();
		} else {
			$this->stateNameArr[1] = "Trait state unavailable";
		}
	}

	private function setPlotCaption($by = 'month', $num = 0, $timeStr = '') {
	// 	//{month, year, geographic region}
		// if($timeStr = '') { $timeStr = date("Y-m-d H:i:s"); }
	 	// $numStr = 'specimens';
	 	// if($num = 1) { $numStr = 'specimen'; }
	 	// $this->plotCaption = "Frequency of " . $this->traitName . ', ' . $this->getStateName() . " by " . $by . " for " . $num . " herbarium " . $numStr . " of <i>" . $this->acceptedArr[$this->tid]['sciname'] . "</i> " . $this->acceptedArr[$this->tid]['author'] . " from PORTAL NAME sampled on " . $timeStr . ".";
		// // . " (including the lower taxa or synonyms: " . implode($this->taxonArr['synonymNames']) . ") ;
	}

	// private function summarizeTraitByAdministrativeDivision($level=2){
	// 	/*
	// 	  $level takes a numeric value in the range 0 through 2. Numbers indicate:
	// 	   0 = Country or equivalent sovereignty (country)
	// 		 1 = State/Province/Department or equivalent first-level division (stateProvince)
	// 		 2 = County/District/Canton or equivalent second-level division (county)
	// 		Levels 1 and 2 are nested in their higher geography if more than one unique value exists
	// 		 for that higher level.
	// 	*/
	// 	if($this->tid && $this->traitid){
	// 		$searchtids = array_merge(array($this->tid), $this->taxonArr['synonymtids']);
	// 		$LevelArr = explode(',', $level);
	// 		$LevelArr = array_unique($LevelArr);
	// 		sort($LevelArr, SORT_NUMERIC);
	// 		$LevelStr = '';
	// 		for($el in $LevelArr) {
	// 			if(strlen($LevelStr) > 0) $LevelStr .= ', ';
	// 			switch(trim($el)) {
	// 				case "0":
	// 					$LevelArr .= 'o.country';
	// 					break;
	// 				case "1":
	// 					$LevelArr .= 'o.stateProvince';
	// 					break;
	// 				case "2":
	// 					$LevelArr .= 'o.county';
	// 					break;
	// 				default:
	// 					if(strlen($LevelStr) > 1) {
	// 						// drop the added comma and space
	// 						$LevelArr = substr($LevelArr, 0, strlen($LevelArr) - 2);
	// 					}
	// 			}
	// 		}
	// 		 $sql = 'SELECT CONCAT_WS("|", ' . $LevelStr . ') AS admin_div, COUNT(*) AS tally FROM tmattributes AS a LEFT JOIN omoccurrences AS o ON o.occid = a.occid WHERE o.tidinterpreted IN(' . implode(",", $searchtids) . ') AND a.stateid = ' . $this->sid . ' GROUP BY ' . $LevelStr . ' ORDER BY ' . $LevelStr;
	// 		 $rs = $this->conn->query($sql);
	// 		 $countArr = array();
	// 		 while($r = $rs->fetch_object()){
	// 			 $countArr[$r->admin_div] = $r->tally;
	// 		 }
	// 		 $rs->free();
	// 	}
	// 	return $countArr;
	// }


// NOT WORKING RIGHT NOW
 	// private function summarizeTraitByYear($missing = 0){
	// 	// summarizeTraitByYear returns an array. Gaps between consecutive years are filled
	// 	//  with the value in $missing; default is 0.
	// 	if($this->tid && $this->sid){
	// 		$searchtids = array_merge(array($this->tid), $this->taxonArr['synonymtids']);
	// 		 $sql = 'SELECT o.year, COUNT(*) AS count FROM tmattributes AS a LEFT JOIN omoccurrences AS o ON o.occid = a.occid WHERE o.tidinterpreted IN(' . implode(",", $searchtids) . ') AND a.stateid = ' . $this->sid . ' GROUP BY o.year ORDER BY o.year';
	// 		 $rs = $this->conn->query($sql);
	// 		 mysqli_data_seek($rs , (mysqli_num_rows($rs) - 1));
	// 		 $lastYear = mysqli_fetch_array($rs)["year"];
	// 		 mysqli_data_seek($rs , 0);
	// 		 $firstYear = mysqli_fetch_array($rs)["year"];
	// 		 $countArr = array_fill(0, $lastYear - $firstYear, $missing);  // missing value array
	// 		 mysqli_data_seek($rs , 0);
	// 		 while($r = $rs->fetch_object()){
	// 			 if($r->year > 0) {
	// 				 $countArr[] = $r->year => $r->count;
	// 			 }
	// 		 }
	// 		 $rs->free();
	// 	}
	// 	return $countArr;
	// }

 	private function summarizeTraitByMonth($missing = 0){
		/*
		  summarizeTraitByMonth returns a 12-element array. Months with no data are
			 filled with the value passed to $missing; default is 0. Data with invalid
			 months (>12, <1, blank, etc.) are ignored.
		*/
		$countArr = array_fill(0, 12, $missing);  // missing value array
		if($this->tid && ($this->traitId || $this->stateId)){
			$searchtids = array($this->tid);
			if(isset($this->synonymArr)){
				$searchtids = array_merge($searchtids, array_keys($this->synonymArr));
			}
			 $sql = 'SELECT o.month, COUNT(o.occid) AS count FROM tmattributes AS a LEFT JOIN omoccurrences AS o ON o.occid = a.occid WHERE o.tidinterpreted IN(' . implode(",", $searchtids) . ') AND ';
			 if($this->stateId) {
				 $sql .= 'a.stateid = '. $this->stateId;
			//} elseif ($this->traitId) {
			//	 $sql .= 'a.traitid = '. $this->traitId;
			 }
			 $sql .= ' GROUP BY o.month;';
			 $rs = $this->conn->query($sql);
			 while($r = $rs->fetch_object()){
				 if($r->month > 0 && $r->month < 13) {
					 $countArr[$r->month-1] = (int)$r->count;
				 }
			 }
			 $rs->free();
			 //$this->setPlotCaption("month", array_sum($countArr), date("Y-m-d H:i:s"));
		}
    return $countArr;
  }

}

?>
