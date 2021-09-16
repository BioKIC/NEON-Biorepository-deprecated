<?php

class PortalStats{

	function __construct(){
		parent::__construct(null, 'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getTotalOccs($conditionTerm = null){
		$retArr = array();
		$sql = 'SELECT COUNT(occid) FROM omoccurrences';
		$rs = $this->conn->query($sql);
		$retArr = $rs
		$rs->free();
		return $retArr
	}

	public function getNumberColls($conditionTerm = null){
		$retArr = array();
		$sql = 'SELECT COUNT(DISTINCT collid) FROM omoccurrences';
		$rs = $this->conn->query($sql);
		$retArr = $rs
		$rs->free();
		return $retArr
	}

}
?>