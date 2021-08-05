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

	//Functions for coordinating IGSN with central NEON system
	public function getIgsnSynchronizationReport(){
		$retArr = array();
		$sql = 'SELECT IFNULL(s.igsnPushedToNEON,"x") as igsnPushedToNEON, COUNT(s.samplePK) as cnt
			FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid
			WHERE o.occurrenceID LIKE "NEON%" GROUP BY s.igsnPushedToNEON';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->igsnPushedToNEON] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function synchronizeIgsn($uncheckedOnly, $startIndex, $limit){
		set_time_limit(3600);
		echo '<li>Starting to synchronize IGSNs</li>';
		flush();
		ob_flush();
		$apiUrlBase = 'https://data.neonscience.org/api/v0/samples/view?archiveGuid=';
		//$neonApiKey = (isset($GLOBALS['NEON_API_KEY'])?$GLOBALS['NEON_API_KEY']:'');
		$sql = 'SELECT o.occid, o.occurrenceID FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid WHERE o.occurrenceID IS NOT NULL AND (s.igsnPushedToNEON IS NULL ';
		if($uncheckedOnly) $sql .= 'OR s.igsnPushedToNEON = 0';
		$sql .= ') ';
		if($startIndex) $sql .= 'AND o.occurrenceID > "'.$this->cleanInStr($startIndex).'" ';
		$sql .= 'ORDER BY o.occurrenceID ';
		if($limit && is_numeric($limit)) $sql .= 'LIMIT '.$limit;
		$rs = $this->conn->query($sql);
		$totalCnt = 0;
		$syncCnt = 0;
		$unsyncCnt = 0;
		$finalIgsn = '';
		while($r = $rs->fetch_object()){
			//$url = $apiUrlBase.$r->occurrenceID.'&apiToken='.$neonApiKey;
			$url = $apiUrlBase.$r->occurrenceID;
			$igsnPushedToNEON = 0;
			if($json = @file_get_contents($url)){
				$resultArr = json_decode($json,true);
				if(!isset($resultArr['error']) && isset($resultArr['data']['sampleViews'][0])){
					$igsnPushedToNEON = 1;
					$syncCnt++;
				}
				else $unsyncCnt++;
			}
			else $unsyncCnt++;
			$sql = 'UPDATE NeonSample SET igsnPushedToNEON = '.$igsnPushedToNEON.' WHERE occid = '.$r->occid;
			if(!$this->conn->multi_query($sql)){
				echo '<li>ERROR updating igsnPushedToNEON field: '.$this->conn->error.'</li>';
			}
			$totalCnt++;
			if($totalCnt%100 == 0){
				echo '<li style="margin-left: 15px">'.$totalCnt.' checked</li>';
				flush();
				ob_flush();
			}
			$finalIgsn = $r->occurrenceID;
		}
		$rs->free();
		echo '<li>Complete: '.$totalCnt.' checked, '.$syncCnt.' synchronized, '.$unsyncCnt.' not synchronized</li>';
		return $finalIgsn;
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