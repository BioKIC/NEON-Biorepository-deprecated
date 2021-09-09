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
		$apiUrlBase = 'https://data.neonscience.org/api/v0/samples/view?';
		//$neonApiKey = (isset($GLOBALS['NEON_API_KEY'])?$GLOBALS['NEON_API_KEY']:'');
		$sql = 'SELECT o.occid, o.occurrenceID, s.sampleCode, s.sampleUuid, s.sampleID, s.sampleClass FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid WHERE (o.occurrenceID IS NOT NULL) ';
		if(!$uncheckedOnly) $sql .= 'AND (s.igsnPushedToNEON IS NULL) ';
		elseif($uncheckedOnly==1) $sql .= 'AND (s.igsnPushedToNEON = 0) ';
		elseif($uncheckedOnly==2) $sql .= 'AND (s.igsnPushedToNEON IS NULL OR s.igsnPushedToNEON = 0) ';
		if($startIndex) $sql .= 'AND (o.occurrenceID > "'.$this->cleanInStr($startIndex).'") ';
		$sql .= 'ORDER BY o.occurrenceID ';
		if($limit && is_numeric($limit)) $sql .= 'LIMIT '.$limit;
		$rs = $this->conn->query($sql);
		$totalCnt = 0;
		$syncCnt = 0;
		$unsyncCnt = 0;
		$finalIgsn = '';
		while($r = $rs->fetch_object()){
			//$url = $apiUrlBase.$r->occurrenceID.'&apiToken='.$neonApiKey;
			$url = $apiUrlBase;
			if($r->sampleCode) $url .= 'barcode='.$r->sampleCode;
			elseif($r->sampleUuid) $url .= 'sampleUuid='.$r->sampleUuid;
			elseif($r->sampleID && $r=sampleClass) $url .= 'sampleTag='.$r->sampleID.'&sampleClass='.$r->sampleClass;
			else{
				echo '<li>ERROR unable to build NEON API url ('.$r->occid.')</li>';
				continue;
			}
			$igsnPushedToNEON = 0;
			$archiveMedium = '';
			if($json = @file_get_contents($url)){
				$resultArr = json_decode($json,true);
				if(!isset($resultArr['error']) && isset($resultArr['data']['sampleViews'])){
					foreach($resultArr['data']['sampleViews'] as $sampleViewArr){
						if(isset($sampleViewArr['archiveGuid']) && $sampleViewArr['archiveGuid'] == $r->occurrenceID) $igsnPushedToNEON = 1;
						if(isset($sampleViewArr['sampleEvents'])){
							foreach($sampleViewArr['sampleEvents'] as $sampleEventArr){
								if(isset($sampleEventArr['smsFieldEntries'])){
									foreach($sampleEventArr['smsFieldEntries'] as $fieldEntriesArr){
										if(isset($fieldEntriesArr['smsKey']) && $fieldEntriesArr['smsKey'] == 'preservative_type'){
											if($fieldEntriesArr['smsValue']) $archiveMedium = $fieldEntriesArr['smsValue'];
										}
									}
								}
							}
						}
					}
					$syncCnt++;
					if(!$igsnPushedToNEON && !$archiveMedium){
						echo '<li>WARNING: unable to harvest archiveMedium (<a href="../collections/individual/index.php?occid='.$r->occid.'" target="_blank">'.$r->occid.'</a>, ';
						echo '<a href="'.$url.'" target="_blank">'.$url.'</a>)</li>';
					}
				}
				else $unsyncCnt++;
			}
			else $unsyncCnt++;
			$sql = 'UPDATE NeonSample SET igsnPushedToNEON = '.$igsnPushedToNEON.', archiveMedium = '.($archiveMedium?'"'.$this->cleanInStr($archiveMedium).'"':'NULL').' WHERE occid = '.$r->occid;
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

	public function exportUnsynchronizedReport(){
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		$fieldMap = array('archiveStartDate' => '"" as archiveStartDate', 'sampleID' => 's.sampleID', 'sampleCode' => 's.sampleCode', 'sampleFate' => '"archived" as sampleFate',
			'sampleClass' => 's.sampleClass', 'archiveMedium' => 's.archiveMedium', 'archiveGuid' => 'o.occurrenceID', 'catalogueNumber' => 'o.catalogNumber',
			'externalURLs' => 'CONCAT("https://biorepo.neonscience.org/portal/collections/individual/index.php?occid=", o.occid) as referenceUrl',
			'collectionCode' => 'CONCAT_WS(":", c.institutionCode, c.collectionCode) as collectionCode');
		$sql = 'SELECT '.implode(', ',$fieldMap).' FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid
			INNER JOIN omcollections c ON c.collid = o.collid
			WHERE o.occurrenceID IS NOT NULL AND (s.igsnPushedToNEON = 0) AND s.archiveMedium IS NOT NULL';
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