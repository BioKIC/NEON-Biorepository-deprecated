<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class IgsnManager{

	private $conn;

	private $errorStr;

 	public function __construct(){
 		$this->conn = MySQLiConnectionFactory::getCon("write");
 	}

 	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	public function getIgsnTaskReport(){
		$this->setNullNeonIdentifiers();
		$retArr = array();
		$sql = 'SELECT c.collid, CONCAT_WS("-",c.institutioncode,c.collectioncode) as collcode, c.collectionname, count(o.occid) as cnt '.
			'FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid '.
			'INNER JOIN NeonSample s ON o.occid = s.occid '.
			'WHERE c.institutionCode = "NEON" AND o.occurrenceId IS NULL AND s.errorMessage IS NULL AND s.sampleReceived = 1 AND s.acceptedForAnalysis = 1 '.
			'GROUP BY c.collid';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['collcode'] = $r->collcode;
			$retArr[$r->collid]['collname'] = $r->collectionname;
			$retArr[$r->collid]['cnt'] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	private function setNullNeonIdentifiers(){
		$sql = 'UPDATE omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid '.
			'SET o.catalognumber = o.occurrenceID '.
			'WHERE o.occurrenceID LIKE "NEON%" AND o.catalognumber IS NULL';
		$this->conn->query($sql);
	}


	//Setters and getters
	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>