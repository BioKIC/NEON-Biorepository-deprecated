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
			'WHERE c.institutionCode = "NEON" AND c.collectionCode != "OPAL-AM" AND o.occurrenceId IS NULL AND s.errorMessage IS NULL AND s.sampleReceived = 1 AND s.acceptedForAnalysis = 1 '.
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
			$code = $r->igsnPushedToNEON;
			if(is_numeric($code) && $code > 9 && $code < 99) $code = 10;
			$retArr[$code] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function synchronizeIgsn($recTarget, $startIndex, $limit, $resetSession){
		/* Synchronization codes
		 *   null = unchecked (also due to data return error)
		 *   0 = Unsynchronized
		 *   100 = Unsynchronized within current session
		 *   1 = IGSN succesfully synchronized
		 *   2 = IGSNs mismatch
		 *   10 to 99 = data return error, amount above the value of 10 indicates the number of times harvest fails
		 */
		set_time_limit(3600);
		echo '<li>Starting to synchronize IGSNs</li>';
		flush();
		ob_flush();
		if($resetSession) $this->resetSession();
		$apiUrlBase = 'https://data.neonscience.org/api/v0/samples/view?';
		//$neonApiKey = (isset($GLOBALS['NEON_API_KEY'])?$GLOBALS['NEON_API_KEY']:'');
		$sql = 'SELECT o.occid, o.occurrenceID, s.sampleCode, s.sampleUuid, s.sampleID, s.sampleClass, s.igsnPushedToNEON
			FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid
			WHERE (o.occurrenceID LIKE "NEON%") ';
		if($recTarget == 'unsynchronized') $sql .= 'AND (s.igsnPushedToNEON = 0) ';
		else $sql .= 'AND (s.igsnPushedToNEON IS NULL) ';
		if($startIndex) $sql .= 'AND (o.occurrenceID > "'.$this->cleanInStr($startIndex).'") ';
		$sql .= 'ORDER BY o.occurrenceID ';
		if(!is_numeric($limit)) $limit = 1000;
		$sql .= 'LIMIT '.$limit;
		$rs = $this->conn->query($sql);
		$totalCnt = 0;
		$syncCnt = 0;
		$mismatchCnt = 0;
		$unsyncCnt = 0;
		$errorCnt = 0;
		$finalIgsn = '';
		while($r = $rs->fetch_object()){
			//$url = $apiUrlBase.$r->occurrenceID.'&apiToken='.$neonApiKey;
			$url = $apiUrlBase;
			if($r->sampleCode) $url .= 'barcode='.$r->sampleCode;
			elseif($r->sampleUuid) $url .= 'sampleUuid='.$r->sampleUuid;
			elseif($r->sampleID && $r->sampleClass) $url .= 'sampleTag='.$r->sampleID.'&sampleClass='.$r->sampleClass;
			else{
				echo '<li>ERROR unable to build NEON API url ('.$r->occid.')</li>';
				continue;
			}
			$igsnPushedToNEON = '';
			if($json = @file_get_contents($url)){
				$resultArr = json_decode($json, true);
				if(!isset($resultArr['error']) && isset($resultArr['data']['sampleViews'])){
					foreach($resultArr['data']['sampleViews'] as $sampleViewArr){
						if(isset($sampleViewArr['archiveGuid']) && $sampleViewArr['archiveGuid']){
							if($sampleViewArr['archiveGuid'] == $r->occurrenceID){
								$igsnPushedToNEON = 1;
								$syncCnt++;
							}
							else{
								$igsnPushedToNEON = 2;
								$mismatchCnt++;
							}
						}
						else{
							$unsyncCnt++;
							if($r->igsnPushedToNEON == 0) $igsnPushedToNEON = 100;
							else $igsnPushedToNEON = 0;
						}
					}
				}
				else $igsnPushedToNEON = 10;
			}
			else $igsnPushedToNEON = 10;
			if($igsnPushedToNEON == 10){
				$errorCnt++;
				if($r->igsnPushedToNEON > 9 && $r->igsnPushedToNEON < 99) $igsnPushedToNEON = ++$r->igsnPushedToNEON;
				echo 'Data return error: '.$url.'<br>';
			}
			$this->updateIgsnStatus($r->occid, $igsnPushedToNEON);
			$totalCnt++;
			if($totalCnt%100 == 0){
				echo '<li style="margin-left: 15px">'.$totalCnt.' checked</li>';
				flush();
				ob_flush();
			}
			$finalIgsn = $r->occurrenceID;
		}
		$rs->free();
		echo '<li>Total checked: '.$totalCnt.', Synchronized: '.$syncCnt.', Not in NEON: '.$unsyncCnt.', Mismatched: '.$mismatchCnt.', API errors: '.$errorCnt.'</li>';
		return $finalIgsn;
	}

	private function updateIgsnStatus($occid, $igsnPushedToNEON){
		if(is_numeric($igsnPushedToNEON)){
			$sql = 'UPDATE NeonSample SET igsnPushedToNEON = '.$igsnPushedToNEON.' WHERE occid = '.$occid;
			if(!$this->conn->multi_query($sql)){
				echo '<li>ERROR updating igsnPushedToNEON field: '.$this->conn->error.'</li>';
			}
		}
	}

	private function resetSession(){
		$sql = 'UPDATE NeonSample SET igsnPushedToNEON = 0 WHERE igsnPushedToNEON = 100';
		$this->conn->query($sql);
	}

	public function exportReport($recTarget, $limit){
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		$fieldMap = array('archiveStartDate' => '"" as archiveStartDate', 'sampleID' => 's.sampleID', 'sampleCode' => 's.sampleCode', 'sampleFate' => '"archived" as sampleFate',
			'sampleClass' => 's.sampleClass', 'archiveMedium' => 's.archiveMedium', 'archiveGuid' => 'o.occurrenceID', 'catalogueNumber' => 'o.catalogNumber',
			'externalURLs' => 'CONCAT("https://biorepo.neonscience.org/portal/collections/individual/index.php?occid=", o.occid) as referenceUrl',
			'collectionCode' => 'CONCAT_WS(":", c.institutionCode, c.collectionCode) as collectionCode');
		$sql = 'SELECT '.implode(', ',$fieldMap).' FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid
			INNER JOIN omcollections c ON c.collid = o.collid
			WHERE (o.occurrenceID LIKE "NEON%") ';
		if($recTarget == 'unsynchronized') $sql .= 'AND (s.igsnPushedToNEON = 0) ';
		else $sql .= 'AND (s.igsnPushedToNEON IS NULL) ';
		$sql .= 'ORDER BY o.occurrenceID ';
		if(!is_numeric($limit)) $limit = 1000;
		$sql .= 'LIMIT '.$limit;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$fileName = 'biorepoIGSNReport_'.date('Y-d-m').'.csv';
			header ('Content-Type: text/csv');
			header ('Content-Disposition: attachment; filename="'.$fileName.'"');
			$out = fopen('php://output', 'w');
			fputcsv($out, array_keys($fieldMap));
			while($row = $rs->fetch_assoc()){
				$this->encodeArr($row);
				fputcsv($out, $row);
			}
			$rs->free();
			fclose($out);
			return true;
		}
		return false;
	}

	//Setters and getters
	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function encodeArr(&$inArr){
		foreach($inArr as $k => $v){
			$inArr[$k] = $this->encodeStr($v);
		}
	}

	private function encodeStr($inStr){
		$retStr = $inStr;
		if($inStr){
			if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') == 'UTF-8') $retStr = utf8_decode($inStr);
		}
		return $retStr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>