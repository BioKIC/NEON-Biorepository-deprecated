<?php
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');
include_once($SERVER_ROOT.'/config/symbini.php');

class OccurrenceHarvester{

	private $conn;
	private $fateLocationArr;
	private $taxonomyArr = array();
	private $stateArr = array();
	private $sampleClassArr = array();
	private $domainSiteArr = array();
	private $replaceFieldValues = false;
	private $neonApiBaseUrl;
	private $neonApiKey;
	private $errorStr;
	private $errorLogArr = array();

	public function __construct(){
		$this->conn = MySQLiConnectionFactory::getCon("write");
		$this->neonApiBaseUrl = 'https://data.neonscience.org/api/v0';
		if(isset($GLOBALS['NEON_API_KEY'])) $this->neonApiKey = $GLOBALS['NEON_API_KEY'];
	}

	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	//Occurrence harvesting functions
	public function getHarvestReport($shipmentPK){
		$retArr = array();
		$sql = 'SELECT s.errorMessage AS errMsg, COUNT(s.samplePK) as sampleCnt, COUNT(o.occid) as occurrenceCnt '.
			'FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
			'WHERE s.checkinuid IS NOT NULL AND s.sampleReceived = 1 AND s.acceptedForAnalysis = 1 AND (s.sampleCondition != "OPAL Sample" OR s.sampleCondition IS NULL) ';
		if($shipmentPK) $sql .= 'AND s.shipmentPK = '.$shipmentPK;
		$sql .= ' GROUP BY errMsg';
		$rs= $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$errMsg = $r->errMsg;
			if(!$errMsg) $errMsg = 'null';
			$retArr[$errMsg]['s-cnt'] = $r->sampleCnt;
			$retArr[$errMsg]['o-cnt'] = $r->occurrenceCnt;
		}
		$rs->free();
		return $retArr;
	}

	public function batchHarvestOccid($postArr){
		//Set variables
		$status = false;
		if(isset($postArr['replaceFieldValues']) && $postArr['replaceFieldValues']) $this->setReplaceFieldValues(true);
		$sqlWhere = '';
		$sqlPrefix = '';
		if(isset($postArr['scbox'])){
			$sqlWhere = 'AND s.samplePK IN('.implode(',',$postArr['scbox']).')';
		}
		elseif($postArr['action'] == 'harvestOccurrences'){
			if(isset($postArr['nullOccurrencesOnly'])){
				$sqlWhere .= 'AND (s.occid IS NULL) ';
			}
			if($postArr['collid']){
				$sqlWhere .= 'AND (o.collid = '.$postArr['collid'].') ';
			}
			if($postArr['errorStr'] == 'nullError'){
				$sqlWhere .= 'AND (s.errorMessage IS NULL) ';
			}
			elseif($postArr['errorStr']){
				$sqlWhere .= 'AND (s.errorMessage = "'.$this->cleanInStr($postArr['errorStr']).'") ';
			}
			if($postArr['harvestDate']){
				$sqlWhere .= 'AND (s.harvestTimestamp IS NULL OR s.harvestTimestamp < "'.$postArr['harvestDate'].'") ';
			}
			$sqlPrefix = 'ORDER BY s.shipmentPK ';
			if(isset($postArr['limit']) && is_numeric($postArr['limit'])) $sqlPrefix .= 'LIMIT '.$postArr['limit'];
			else $sqlPrefix .= 'LIMIT 1000 ';
		}
		if($sqlWhere){
			$sqlWhere = 'WHERE s.checkinuid IS NOT NULL AND s.acceptedForAnalysis = 1 AND s.sampleReceived = 1 AND (s.sampleCondition != "OPAL Sample" OR s.sampleCondition IS NULL) '.$sqlWhere;
			$status = $this->batchHarvestOccurrences($sqlWhere.$sqlPrefix);
		}
		return $status;
	}

	private function batchHarvestOccurrences($sqlWhere){
		set_time_limit(3600);
		if($sqlWhere){
			$this->setStateArr();
			$this->setDomainSiteArr();
			//if(!$this->setSampleClassArr()) echo '<li>'.$this->errorStr.'</li>';
			echo '<li>Target record count: '.number_format($this->getTargetCount($sqlWhere)).'</li>';
			$collArr = array();
			$occidArr = array();
			$cnt = 1;
			$shipmentPK = '';
			$sql = 'SELECT s.samplePK, s.shipmentPK, s.sampleID, s.hashedSampleID, s.alternativeSampleID, s.sampleUuid, s.sampleCode, s.sampleClass, s.taxonID, '.
				's.individualCount, s.filterVolume, s.namedLocation, s.collectDate, s.symbiotaTarget, s.igsnPushedToNEON, s.occid '.
				'FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
				$sqlWhere;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->errorStr = '';
				if($shipmentPK != $r->shipmentPK){
					$shipmentPK = $r->shipmentPK;
					echo '<li><b>Processing shipment #'.$shipmentPK.'</b></li>';
				}
				echo '<li style="margin-left:15px">'.$cnt.': '.($r->occid?($this->replaceFieldValues?'Rebuilding':'Appending'):'Harvesting').' '.($r->sampleID?$r->sampleID:$r->sampleCode).'... ';
				$sampleArr = array();
				$sampleArr['samplePK'] = $r->samplePK;
				$sampleArr['sampleID'] = strtoupper($r->sampleID);
				$sampleArr['hashedSampleID'] = $r->hashedSampleID;
				$sampleArr['alternativeSampleID'] = strtoupper($r->alternativeSampleID);
				$sampleArr['sampleUuid'] = $r->sampleUuid;
				$sampleArr['sampleCode'] = $r->sampleCode;
				$sampleArr['sampleClass'] = $r->sampleClass;
				$sampleArr['taxonID'] = $r->taxonID;
				$sampleArr['individualCount'] = $r->individualCount;
				$sampleArr['filterVolume'] = $r->filterVolume;
				$sampleArr['namedLocation'] = $r->namedLocation;
				$sampleArr['collectDate'] = $r->collectDate;
				$sampleArr['symbiotaTarget'] = $r->symbiotaTarget;
				$sampleArr['igsnPushedToNEON'] = $r->igsnPushedToNEON;
				$sampleArr['occid'] = $r->occid;
				$currentOccurArr = $this->getOccurrenceRecord($r->occid);
				if(isset($currentOccurArr['occurrenceID'])) $sampleArr['occurrenceID'] = $currentOccurArr['occurrenceID'];
				if($this->harvestNeonApi($sampleArr)){
					if($dwcArr = $this->getDarwinCoreArr($sampleArr, $currentOccurArr)){
						if($occid = $this->loadOccurrenceRecord($dwcArr, $currentOccurArr, $r->samplePK, $r->occid)){
							if(!in_array($dwcArr['collid'],$collArr)) $collArr[] = $dwcArr['collid'];
							$occidArr[] = $occid;
							echo '<a href="'.$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$occid.'" target="_blank">success!</a>';
						}
						if($this->errorStr) echo '</li><li style="margin-left:30px">WARNING: '.$this->errorStr.'</li>';
						else echo '</li>';
					}
					else{
						echo '</li><li style="margin-left:30px">'.$this->errorStr.'</li>';
					}
				}
				else{
					echo '</li><li style="margin-left:30px">ERROR: '.$this->errorStr.'</li>';
				}
				$cnt++;
				flush();
				ob_flush();
			}
			$rs->free();
			if($shipmentPK){
				$this->adjustTaxonomy($occidArr);
				//Set recordID GUIDs
				echo '<li>Setting recordID UUIDs for all occurrence records...</li>';
				$uuidManager = new UuidFactory();
				$uuidManager->setSilent(1);
				$uuidManager->populateGuids();
				//Update stats for each collection affected
				if($collArr){
					echo '<li>Update stats for each collection...</li>';
					$collManager = new OccurrenceCollectionProfile();
					foreach($collArr as $collID){
						echo '<li style="margin-left:15px">Stat update for collection <a href="'.$GLOBALS['CLIENT_ROOT'].'/collections/misc/collprofiles.php?collid='.$collID.'" target="_blank">#'.$collID.'</a>...</li>';
						$collManager->setCollid($collID);
						$collManager->updateStatistics(false);
						flush();
						ob_flush();
					}
				}
			}
			else echo '<li><b>No records processed. Note that records have to be checked in before occurrences can be harvested.</b></li>';
			//Log any notices, warnings, and errors
			if($this->errorLogArr){
				$logPath = $GLOBALS['SERVER_ROOT'].'/neon/content/logs/occurHarvest_error_'.date('Y-m-d').'.log';
				$logFH = fopen($logPath, 'a');
				fwrite($logFH,'Harvesting event: '.date('Y-m-d H:i:s')."\n");
				fwrite($logFH,'-------------------------'."\n");
				foreach($this->errorLogArr as $errStr){
					fwrite($logFH,$errStr."\n");
				}
				fwrite($logFH,"\n\n");
				fclose($logFH);
			}
		}
		return false;
	}

	private function getTargetCount($sqlWhere){
		$retCnt = 0;
		$sql = 'SELECT COUNT(s.samplePK) AS cnt FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.$sqlWhere;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	private function getOccurrenceRecord($occid){
		$retArr = array();
		if($occid){
			$sql = 'SELECT catalogNumber, occurrenceID, eventID, sciname, taxonRemarks, recordedBy, eventDate, individualCount, reproductiveCondition, sex, lifeStage, occurrenceRemarks,
				preparations, dynamicProperties, verbatimAttributes, habitat, country, stateProvince, county, locality, locationID, decimalLatitude, decimalLongitude,
				verbatimCoordinates, georeferenceSources, minimumElevationInMeters, maximumElevationInMeters
				FROM omoccurrences WHERE occid = '.$occid;
			$rs = $this->conn->query($sql);
			$retArr = $rs->fetch_assoc();
			$rs->free();
		}
		return $retArr;
	}

	private function harvestNeonApi(&$sampleArr){
		$this->setSampleErrorMessage($sampleArr['samplePK'], '');
		$url = '';
		$sampleViewArr = array();
		if(isset($sampleArr['occurrenceID']) && $sampleArr['occurrenceID']){
			$url = $this->neonApiBaseUrl.'/samples/view?archiveGuid='.$sampleArr['occurrenceID'].'&apiToken='.$this->neonApiKey;
			$sampleViewArr = $this->getNeonApiArr($url);
		}

		if(!isset($sampleViewArr['sampleViews'])){
			if($sampleArr['sampleCode']){
				$url = $this->neonApiBaseUrl.'/samples/view?barcode='.$sampleArr['sampleCode'].'&apiToken='.$this->neonApiKey;
			}
			elseif($sampleArr['sampleUuid']){
				$url = $this->neonApiBaseUrl.'/samples/view?sampleUuid='.$sampleArr['sampleUuid'].'&apiToken='.$this->neonApiKey;
			}
			elseif($sampleArr['sampleID'] && $sampleArr['sampleClass']){
				$url = $this->neonApiBaseUrl.'/samples/view?sampleTag='.urlencode($sampleArr['sampleID']).'&sampleClass='.urlencode($sampleArr['sampleClass']).'&apiToken='.$this->neonApiKey;
			}
			else{
				$this->errorStr = 'Sample identifiers incomplete';
				$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
				return false;
			}
			$sampleViewArr = $this->getNeonApiArr($url);
		}
		//echo 'url: '.$url.'<br/>';

		if(!isset($sampleViewArr['sampleViews'])){
			$this->errorStr = 'NEON API failed to return sample data ';
			$this->updateSampleRecord(array('errorMessage'=>$this->errorStr),$sampleArr['samplePK']);
			return false;
		}
		if(count($sampleViewArr['sampleViews']) > 1){
			$this->errorStr = 'Harvest skipped: NEON API returned multiple sampleViews ';
			$this->updateSampleRecord(array('errorMessage'=>$this->errorStr),$sampleArr['samplePK']);
			return false;
		}
		$viewArr = current($sampleViewArr['sampleViews']);
		$neonSampleUpdate = array();
		//Check and populate identifiers
		if(isset($viewArr['sampleUuid']) && $viewArr['sampleUuid']){
			//Populate or verify/coordinate sampleUuid
			if(!$sampleArr['sampleUuid'] || $sampleArr['sampleUuid'] != $viewArr['sampleUuid']){
				$sampleArr['sampleUuid'] = $viewArr['sampleUuid'];
				$neonSampleUpdate['sampleUuid'] = $viewArr['sampleUuid'];
				if($sampleArr['sampleUuid'] != $viewArr['sampleUuid']){
					$this->errorLogArr[] = 'NOTICE: sampleUuid updated from '.$sampleArr['sampleUuid'].' to '.$viewArr['sampleUuid'];
					$errMsg = 'DATA ISSUE: sampleUuid updated from '.$sampleArr['sampleUuid'].' to '.$viewArr['sampleUuid'];
					$this->setSampleErrorMessage($sampleArr['samplePK'], $errMsg);
				}
			}
		}
		if(isset($viewArr['archiveGuid']) && $viewArr['archiveGuid']){
			$igsnMatch = array();
			if(preg_match('/(NEON[A-Z,0-9]{5})/', $viewArr['archiveGuid'], $igsnMatch)){
				if($sampleArr['occid']){
					//Reharvest event
					if(isset($sampleArr['occurrenceID']) && $sampleArr['occurrenceID']){
						if($sampleArr['occurrenceID'] == $igsnMatch[1]){
							$neonSampleUpdate['igsnPushedToNEON'] = 1;
						}
						else{
							$neonSampleUpdate['errorMessage'] = 'DATA ISSUE: IGSN failing to match with API value';
							$neonSampleUpdate['igsnPushedToNEON'] = 2;
						}
					}
					else{
						if(!$this->igsnExists($igsnMatch[1],$sampleArr)){
							if(!$this->updateOccurrenceIgsn($igsnMatch[1], $sampleArr['occid'])){
								$this->errorLogArr[] = 'NOTICE: unable to update igsn: '.$this->conn->error;
								$neonSampleUpdate['igsnPushedToNEON'] = 3;
							}
						}
					}
				}
				else{
					//New record should use ISGN, if it is not already assigned to another record
					if(!$this->igsnExists($igsnMatch[1],$sampleArr)) $sampleArr['occurrenceID'] = $igsnMatch[1];
				}
			}
		}
		if($sampleArr['sampleClass'] && isset($viewArr['sampleClass']) && $sampleArr['sampleClass'] != $viewArr['sampleClass']){
			//sampleClass are not equal; don't update, just record within error file that this is an issue
			$errMsg = (isset($neonSampleUpdate['errorMessage'])?$neonSampleUpdate['errorMessage'].'; ':'');
			$errMsg .= 'DATA ISSUE: sampleClass failing to match with API value';
			$this->setSampleErrorMessage($sampleArr['samplePK'], $errMsg);
		}
		if($sampleArr['sampleID'] && isset($viewArr['sampleTag']) && $sampleArr['sampleID'] != $viewArr['sampleTag'] && $sampleArr['hashedSampleID'] != $viewArr['sampleTag']){
			//sampleIDs (sampleTags) are not equal; update our system
			if(substr($viewArr['sampleTag'],-1) == '=' || !preg_match('/[_\.]+/',$viewArr['sampleTag'])){
				$neonSampleUpdate['hashedSampleID'] = $viewArr['sampleTag'];
				$sampleArr['hashedSampleID'] = $viewArr['sampleTag'];
			}
			else{
				$this->setSampleErrorMessage($sampleArr['samplePK'], 'DATA ISSUE: sampleID different than NEON API value');
				/*
				if($this->updateSampleID($viewArr['sampleTag'], $sampleArr['sampleID'], $sampleArr['samplePK'], $sampleArr['occid'])){
					$this->errorLogArr[] = 'NOTICE: sampleID updated from '.$sampleArr['sampleID'].' to '.$viewArr['sampleTag'].' (samplePK: '.$sampleArr['samplePK'].', occid: '.$sampleArr['occid'].')';
				}
				else{
					$errMsg = (isset($neonSampleUpdate['errorMessage'])?$neonSampleUpdate['errorMessage'].'; ':'');
					$errMsg .= 'DATA ISSUE: failed to reset sampleID using changed API value';
					$this->setSampleErrorMessage($sampleArr['samplePK'], $errMsg);
				}
				*/
			}
		}
		$this->updateSampleRecord($neonSampleUpdate,$sampleArr['samplePK']);
		//Get fateLocation and process parent samples
		unset($this->fateLocationArr);
		$this->fateLocationArr = array();
		$this->processViewArr($sampleArr, $sampleViewArr);
		if($this->fateLocationArr){
			ksort($this->fateLocationArr);
			$locArr = current($this->fateLocationArr);
			$sampleArr['fate_location'] = $locArr['loc'];
			if(!isset($sampleArr['collect_end_date'])) $sampleArr['collect_end_date'] = $locArr['date'];
		}
		return true;
	}

	private function updateSampleRecord($neonSampleUpdate,$samplePK){
		if($neonSampleUpdate){
			$sqlInsert = '';
			foreach($neonSampleUpdate as $field => $value){
				$sqlInsert .= $field.' = "'.$this->cleanInStr($value).'", ';
			}
			$sql = 'UPDATE NeonSample SET '.trim($sqlInsert,', ').' WHERE (samplePK = '.$samplePK.')';
			if(!$this->conn->query($sql)){
				echo '</li><li style="margin-left:30px">ERROR updating NeonSample record: '.$this->conn->error.'</li>';
			}
		}
	}

	private function igsnExists($igsn, &$sampleArr){
		$occid = 0;
		$sql = 'SELECT occid FROM omoccurrences WHERE occurrenceid = "'.$igsn.'" ';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$occid = $r->occid;
		}
		$rs->free();
		if($occid){
			//Another records exists within the portal with the same IGSN (not ideal)
			$sampleArr['occurrenceID'] = $igsn.'-dupe (data issue!)';
			$errMsg = 'DATA ISSUE: another record exists with duplicate IGSN registered within NEON API';
			$this->setSampleErrorMessage($sampleArr['samplePK'], $errMsg);
			return true;
		}
		return false;
	}

	private function updateOccurrenceIgsn($igsn, $occid){
		$status = false;
		$sql = 'UPDATE omoccurrences SET occurrenceID = "'.$igsn.'" WHERE occurrenceid IS NULL AND occid = '.$occid;
		if($this->conn->query($sql)) $status = true;
		return $status;
	}

	private function processViewArr(&$sampleArr, $viewArr){
		if(!isset($viewArr['sampleViews'])){
			$this->errorStr = 'sampleViews object failed to be returned from NEON API';
			return false;
		}
		$viewArr = current($viewArr['sampleViews']);
		//parse Sample Event details
		$eventArr = $viewArr['sampleEvents'];
		if($eventArr){
			foreach($eventArr as $eArr){
				$tableName = $eArr['ingestTableName'];
				if(strpos($tableName,'shipment')) continue;
				if(strpos($tableName,'identification')) continue;
				if(strpos($tableName,'sorting')) continue;
				if(strpos($tableName,'archive')) continue;
				if(strpos($tableName,'barcoding')) continue;
				if(strpos($tableName,'dnaStandardTaxon')) continue;
				if(strpos($tableName,'dnaExtraction')) continue;
				if(strpos($tableName,'markerGeneSequencing')) continue;
				if(strpos($tableName,'metagenomeSequencing')) continue;
				if(strpos($tableName,'metabarcodeTaxonomy')) continue;
				if(strpos($tableName,'pcrAmplification')) continue;
				if(strpos($tableName,'perarchivesample')) continue;
				if(strpos($tableName,'persample')) continue;
				if(strpos($tableName,'pertaxon')) continue;
				if(strpos($tableName,'pervial')) continue;
				if($tableName == 'mpr_perpitprofile_in') continue;
				$fieldArr = $eArr['smsFieldEntries'];
				$fateLocation = ''; $fateDate = ''; $collLoc = ''; $identArr = array(); $assocMedia = array();
				$tableArr = array();
				foreach($fieldArr as $fArr){
					if($fArr['smsKey'] == 'fate_location') $fateLocation = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'collection_location' && $fArr['smsValue']) $collLoc = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'fate_date' && $fArr['smsValue']) $fateDate = $this->formatDate($fArr['smsValue']);
					elseif($fArr['smsKey'] == 'event_id' && $fArr['smsValue']) $tableArr['event_id'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'taxon' && $fArr['smsValue']) $tableArr['taxon'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'taxon_published' && $fArr['smsValue']) $tableArr['taxon_published'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'identified_by' && $fArr['smsValue']) $identArr['identifiedBy'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'identified_date' && $fArr['smsValue']) $identArr['dateIdentified'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'identification_remarks' && $fArr['smsValue']) $identArr['identificationRemarks'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'identification_references' && $fArr['smsValue']) $identArr['identificationReferences'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'identification_qualifier' && $fArr['smsValue']) $identArr['identificationQualifier'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'collected_by' && $fArr['smsValue']) $tableArr['collected_by'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'collect_start_date' && $fArr['smsValue']) $tableArr['collect_start_date'] = $this->formatDate($fArr['smsValue']);
					elseif($fArr['smsKey'] == 'collect_end_date' && $fArr['smsValue']) $tableArr['collect_end_date'] = $this->formatDate($fArr['smsValue']);
					elseif($fArr['smsKey'] == 'specimen_count' && $fArr['smsValue']) $tableArr['specimen_count'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'temperature' && $fArr['smsValue']) $tableArr['temperature'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'decimal_latitude' && $fArr['smsValue']) $tableArr['decimal_latitude'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'decimal_longitude' && $fArr['smsValue']) $tableArr['decimal_longitude'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'coordinate_uncertainty' && $fArr['smsValue']) $tableArr['coordinate_uncertainty'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'elevation' && $fArr['smsValue']) $tableArr['elevation'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'elevation_uncertainty' && $fArr['smsValue']) $tableArr['elevation_uncertainty'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'verbatim_depth' && $fArr['smsValue']) $tableArr['verbatim_depth'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'minimum_depth_in_meters' && $fArr['smsValue']) $tableArr['minimum_depth_in_meters'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'maximum_depth_in_meters' && $fArr['smsValue']) $tableArr['maximum_depth_in_meters'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'reproductive_condition' && $fArr['smsValue']) $tableArr['reproductive_condition'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'sex' && $fArr['smsValue']) $tableArr['sex'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'life_stage' && $fArr['smsValue']) $tableArr['life_stage'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'remarks' && $fArr['smsValue']) $tableArr['remarks'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'preservative_concentration' && $fArr['smsValue']) $tableArr['preservative_concentration'] = $fArr['smsValue'].', ';
					elseif($fArr['smsKey'] == 'preservative_volume' && $fArr['smsValue']) $tableArr['preservative_volume'] = $fArr['smsValue'].', ';
					elseif($fArr['smsKey'] == 'preservative_type' && $fArr['smsValue']) $tableArr['preservative_type'] = $fArr['smsValue'].', ';
					elseif($fArr['smsKey'] == 'sample_type' && $fArr['smsValue']) $tableArr['sample_type'] = $fArr['smsValue'];
					//elseif($fArr['smsKey'] == 'sample_condition' && $fArr['smsValue']) $tableArr['sample_condition'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'sample_mass' && $fArr['smsValue']) $tableArr['sample_mass'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'sample_volume' && $fArr['smsValue']) $tableArr['sample_volume'] = $fArr['smsValue'];
					elseif($fArr['smsKey'] == 'associated_media'){
						if(!strpos($fArr['smsValue'],'biorepo.neonscience.org/portal')) $assocMedia['url'] = $fArr['smsValue'];
					}
					elseif($fArr['smsKey'] == 'photographed_by') $assocMedia['photographer'] = $fArr['smsValue'];
				}
				if($identArr) $tableArr['identifications'][] = $identArr;
				if($assocMedia && isset($assocMedia['url'])) $tableArr['assocMedia'][] = $assocMedia;

				if($collLoc){
					$score = 1;
					$this->fateLocationArr[$score]['loc'] = $collLoc;
					$this->fateLocationArr[$score]['date'] = $fateDate;
				}
				elseif($fateDate && $fateLocation){
					$score = $fateDate;
					if(strpos($tableName,'fielddata')) $score = 2;
					$this->fateLocationArr[$score]['loc'] = $fateLocation;
					$this->fateLocationArr[$score]['date'] = $fateDate;
				}
				if(isset($tableArr['identifications'])){
					foreach($tableArr['identifications'] as $idKey => $idValue){
						if(isset($tableArr['taxon'])) $tableArr['identifications'][$idKey]['sciname'] = $tableArr['taxon'];
					}
				}
				$sampleArr = array_merge($tableArr,$sampleArr);
			}
		}
		if(isset($viewArr['parentSampleIdentifiers'][0]['sampleUuid'])){
			//Get parent data
			$url = $this->neonApiBaseUrl.'/samples/view?sampleUuid='.$viewArr['parentSampleIdentifiers'][0]['sampleUuid'].'&apiToken='.$this->neonApiKey;
			$parentViewArr = $this->getNeonApiArr($url);
			$this->processViewArr($sampleArr, $parentViewArr);
		}
	}

	private function getDarwinCoreArr($sampleArr, $currentOccurArr){
		$dwcArr = array();
		if($sampleArr['samplePK']){
			if($this->setCollectionIdentifier($dwcArr,$sampleArr['sampleClass'])){
				//Get data that was provided within manifest
				$dwcArr['identifiers']['NEON sampleCode (barcode)'] = (isset($sampleArr['sampleCode'])?$sampleArr['sampleCode']:'');
				$dwcArr['identifiers']['NEON sampleID'] = (isset($sampleArr['sampleID'])?$sampleArr['sampleID']:'');
				$dwcArr['identifiers']['NEON sampleUUID'] = (isset($sampleArr['sampleUuid'])?$sampleArr['sampleUuid']:'');
				$dwcArr['identifiers']['NEON sampleID Hash'] = (isset($sampleArr['hashedSampleID'])?$sampleArr['hashedSampleID']:'');
				if(isset($sampleArr['event_id'])) $dwcArr['eventID'] = $sampleArr['event_id'];
				if(isset($sampleArr['specimen_count'])) $dwcArr['individualCount'] = $sampleArr['specimen_count'];
				elseif(isset($sampleArr['individualCount'])) $dwcArr['individualCount'] = $sampleArr['individualCount'];
				if(isset($sampleArr['reproductive_condition'])) $dwcArr['reproductiveCondition'] = $sampleArr['reproductive_condition'];
				if(isset($sampleArr['sex'])) $dwcArr['sex'] = $sampleArr['sex'];
				if(isset($sampleArr['life_stage'])) $dwcArr['lifeStage'] = $sampleArr['life_stage'];
				if(isset($sampleArr['remarks'])) $dwcArr['occurrenceRemarks'] = $sampleArr['remarks'];
				if(isset($sampleArr['assocMedia'])) $dwcArr['assocMedia'] = $sampleArr['assocMedia'];
				if(isset($sampleArr['coordinate_uncertainty'])) $dwcArr['coordinateUncertaintyInMeters'] = $sampleArr['coordinate_uncertainty'];
				if(isset($sampleArr['decimal_latitude'])) $dwcArr['decimalLatitude'] = $sampleArr['decimal_latitude'];
				if(isset($sampleArr['decimal_longitude'])) $dwcArr['decimalLongitude'] = $sampleArr['decimal_longitude'];
				if(isset($sampleArr['elevation'])){
					if(isset($sampleArr['elevation_uncertainty'])){
						$dwcArr['minimumElevationInMeters'] = round($sampleArr['elevation']-$sampleArr['elevation_uncertainty']);
						$dwcArr['maximumElevationInMeters'] = round($sampleArr['elevation']+$sampleArr['elevation_uncertainty']);
						$dwcArr['verbatimElevation'] = $sampleArr['elevation'].'m (+-'.$sampleArr['elevation_uncertainty'].'m)';
					}
					else{
						$dwcArr['minimumElevationInMeters'] = $sampleArr['elevation'];
						$dwcArr['maximumElevationInMeters'] = $sampleArr['elevation'];
					}
				}
				$prepArr = array();
				if(isset($sampleArr['preservative_type'])) $prepArr[] = 'preservative type: '.$sampleArr['preservative_type'];
				if(isset($sampleArr['preservative_volume'])) $prepArr[] = 'preservative volume: '.$sampleArr['preservative_volume'];
				if(isset($sampleArr['preservative_concentration'])) $prepArr[] = 'preservative concentration: '.$sampleArr['preservative_concentration'];
				if(!in_array($dwcArr['collid'], array(19,28,42))){
					if(isset($sampleArr['sample_mass']) && strpos($sampleArr['symbiotaTarget'],'sample mass') === false) $prepArr[] = 'sample mass: '.$sampleArr['sample_mass'];
					if(isset($sampleArr['sample_volume']) && strpos($sampleArr['symbiotaTarget'],'sample volume') === false) $prepArr[] = 'sample volume: '.$sampleArr['sample_volume'];
				}
				if($prepArr) $dwcArr['preparations'] = implode(', ',$prepArr);
				$dynProp = array();
				if(isset($sampleArr['filterVolume'])) $dynProp[] = 'filterVolume: '.$sampleArr['filterVolume'];
				if(isset($sampleArr['temperature'])) $dynProp[] = 'temperature: '.$sampleArr['temperature'];
				if(isset($sampleArr['minimum_depth_in_meters'])) $dynProp[] = 'minimum depth: '.$sampleArr['minimum_depth_in_meters'].'m ';
				if(isset($sampleArr['maximum_depth_in_meters'])) $dynProp[] = 'maximum depth: '.$sampleArr['maximum_depth_in_meters'].'m ';
				if(isset($sampleArr['verbatim_depth'])) $dynProp[] = 'verbatim depth: '.$sampleArr['verbatim_depth'];
				if(isset($sampleArr['sample_type'])){
					$sampleType = $sampleArr['sample_type'];
					if($sampleType == 'M') $sampleType = 'mineral';
					elseif($sampleType == 'O') $sampleType = 'organic';
					$dynProp[] = 'sample type: '.$sampleType;
				}
				//if(isset($sampleArr['sample_condition'])) $dynProp[] = 'sample condition: '.$sampleArr['sample_condition'];
				if($dynProp) $dwcArr['dynamicProperties'] = implode(', ',$dynProp);

				if(isset($sampleArr['collected_by']) && $sampleArr['collected_by']) $dwcArr['recordedBy'] = $sampleArr['collected_by'];
				if(isset($sampleArr['collect_end_date']) && $sampleArr['collect_end_date']) $dwcArr['eventDate'] = $sampleArr['collect_end_date'];
				elseif(isset($sampleArr['collectDate']) && $sampleArr['collectDate'] && $sampleArr['collectDate'] != '0000-00-00') $dwcArr['eventDate'] = $sampleArr['collectDate'];
				elseif($sampleArr['sampleID']){
					if(preg_match('/\.(20\d{2})(\d{2})(\d{2})\./',$sampleArr['sampleID'],$m)){
						//Get date from sampleID
						$dwcArr['eventDate'] = $m[1].'-'.$m[2].'-'.$m[3];
					}
				}
				/*
				if(isset($sampleArr['collect_end_date'])){
					if(!isset($dwcArr['eventDate']) || !$dwcArr['eventDate']) $dwcArr['eventDate'] = $sampleArr['collect_end_date'];
					elseif($dwcArr['eventDate'] != $sampleArr['collect_end_date']) $dwcArr['eventDate2'] = $sampleArr['collect_end_date'];
				}
				*/
				//Build proper location code
				$locationStr = '';
				if(isset($sampleArr['fate_location']) && $sampleArr['fate_location']) $locationStr = $sampleArr['fate_location'];
				elseif($sampleArr['namedLocation']) $locationStr = $sampleArr['namedLocation'];
				if($locationStr){
					if($this->setNeonLocationData($dwcArr, $locationStr)){
						if(isset($dwcArr['domainID'])){
							$locStr = $this->domainSiteArr[$dwcArr['domainID']].' ('.$dwcArr['domainID'].'), ';
							if(isset($dwcArr['siteID'])) $locStr .= $this->domainSiteArr[$dwcArr['siteID']].' ('.$dwcArr['siteID'].'), ';
							if(isset($dwcArr['locality'])) $locStr .= $dwcArr['locality'];
							$dwcArr['locality'] = trim($locStr,', ');
						}
						if(isset($dwcArr['plotDim'])){
							$dwcArr['locality'] .= $dwcArr['plotDim'];
							unset($dwcArr['plotDim']);
						}
						$dwcArr['locationID'] = $locationStr;
					}
					else{
						$dwcArr['locality'] = $sampleArr['namedLocation'];
						$this->errorStr = 'locality data failed to populate';
						$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
						//return false;
					}
					if(isset($dwcArr['locality']) && $dwcArr['locality']) $dwcArr['locality'] = trim($dwcArr['locality'],' ,;.');
				}

				//Taxonomic fields
				$skipTaxonomy = array(5,6,10,13,16,21,23,31,41,42,45,58,60,61,62,67,68,69,76);
				if(!in_array($dwcArr['collid'],$skipTaxonomy)){
					if(isset($sampleArr['taxon']) && $sampleArr['taxon']){
						$dwcArr['sciname'] = $sampleArr['taxon'];
						$dwcArr['taxonRemarks'] = 'Identification source: harvested from NEON API';
						if(isset($sampleArr['taxon_published']) && $sampleArr['taxon_published']){
							if($sampleArr['taxon_published'] != $sampleArr['taxon']){
								$dwcArr['localitySecurity'] = 2;
								$dwcArr['localitySecurityReason'] = '[Security Setting Locked]';
							}
						}
					}
					elseif($sampleArr['taxonID']){
						$dwcArr['sciname'] = $sampleArr['taxonID'];
						$dwcArr['taxonRemarks'] = 'Identification source: inferred from shipment manifest';
					}
					else{
						if($dwcArr['collid'] == 56){
							if(preg_match('/\.\d{4}\.\d{1,2}\.([A-Z]{2,15}\d{0,2})\./',$sampleArr['sampleID'],$m)){
								$dwcArr['sciname'] = $m[1];
								$dwcArr['taxonRemarks'] = 'Identification source: parsed from NEON sampleID';
							}
						}
						elseif(!in_array($dwcArr['collid'], array(5,21,22,23,30,31,41,42,50,56,57))){
							if(preg_match('/\.\d{8}\.([A-Z]{2,15}\d{0,2})\./',$sampleArr['sampleID'],$m)){
								$dwcArr['sciname'] = $m[1];
								$dwcArr['taxonRemarks'] = 'Identification source: parsed from NEON sampleID';
							}
						}
					}
					if(in_array($dwcArr['collid'], array(30)) && !isset($dwcArr['sciname'])) $dwcArr['sciname'] = 'Soil';
					if(isset($dwcArr['sciname'])){
						if(preg_match('/^[A-Z0-9]+$/', $dwcArr['sciname']) || in_array($dwcArr['collid'], array(30))) $this->setTaxonomy($dwcArr);
						if(preg_match('/^[A-Z0-9]+$/', $dwcArr['sciname'])){
							if(!preg_match('/^[A-Z0-9]+$/', $currentOccurArr['sciname'])){
								echo '<li style="margin-left:25px">Notice: translation of NEON taxon code ('.$dwcArr['sciname'].') failed, thus keeping current name ('.$currentOccurArr['sciname'].')</li>';
								unset($dwcArr['sciname']);
							}
						}
					}
					if(isset($sampleArr['identifications'])){
						//Group of identifications will be inserted into omoccurdeterminations, and most recent will go into omoccurrences
						$activeArr = array();
						foreach($sampleArr['identifications'] as $idKey => $idArr){
							if(isset($idArr['sciname']) && preg_match('/^[A-Z0-9]+$/', $idArr['sciname'])) $this->setTaxonomy($idArr);
							if(!$activeArr) $activeArr = $idArr;
							elseif(!isset($activeArr['identifiedBy'])){
								if(isset($idArr['identifiedBy'])) $activeArr = $idArr;
							}
							elseif(!isset($activeArr['dateIdentified'])){
								if(isset($idArr['dateIdentified'])) $activeArr = $idArr;
							}
							elseif(isset($idArr['dateIdentified']) && $idArr['dateIdentified'] > $activeArr['dateIdentified']) $activeArr = $idArr;
							$sampleArr['identifications'][$idKey] = $idArr;
						}
						$dwcArr['identifications'] = $sampleArr['identifications'];
						if($activeArr){
							foreach($activeArr as $k => $v){
								$dwcArr[$k] = $v;
							}
						}
					}
				}
				//Add DwC fields that were imported as part of the manifest file
				if($sampleArr['symbiotaTarget']){
					if($symbArr = json_decode($sampleArr['symbiotaTarget'],true)){
						foreach($symbArr as $symbField => $symbValue){
							if($symbValue !== '' && !isset($dwcArr[$symbField])) $dwcArr[$symbField] = $symbValue;
						}
					}
				}
			}
			else{
				$this->errorStr = 'ERROR: unable to retrieve collid using sampleClass: '.$sampleArr['sampleClass'];
				$this->setSampleErrorMessage($sampleArr['samplePK'], 'unable to retrieve collid using sampleClass');
				return false;
			}
		}
		return $dwcArr;
	}

	private function updateSampleID($newSampleID, $oldSampleID, $samplePK, $occid){
		$status = true;
		$sql = 'UPDATE NeonSample SET sampleID = "'.$newSampleID.'", alternativeSampleID = CONCAT_WS(", ",alternativeSampleID,"'.$oldSampleID.'") WHERE samplePK = '.$samplePK;
		if(!$this->conn->query($sql)){
			$status = false;
		}
		if($occid){
			$sql = 'UPDATE omoccuridentifiers SET identifierValue = "'.$newSampleID.'" WHERE identifiername = "NEON sampleID" AND occid = '.$occid;
			if(!$this->conn->query($sql)){
				$status = false;
			}
		}
		return $status;
	}

	private function setCollectionIdentifier(&$dwcArr,$sampleClass){
		$status = false;
		if($sampleClass){
			$sql = 'SELECT collid, datasetName FROM omcollections
				WHERE (datasetID = "'.$sampleClass.'") OR (datasetID LIKE "%,'.$sampleClass.',%") OR (datasetID LIKE "'.$sampleClass.',%") OR (datasetID LIKE "%,'.$sampleClass.'")';
			$rs = $this->conn->query($sql);
			if($rs->num_rows == 1){
				$r = $rs->fetch_object();
				$dwcArr['collid'] = $r->collid;
				if($r->datasetName) $dwcArr['verbatimAttributes'] = $r->datasetName;
				else $dwcArr['verbatimAttributes'] = $sampleClass;
				$status = true;
			}
			$rs->free();
		}
		return $status;
	}

	private function setNeonLocationData(&$dwcArr, $locationName){
		//echo 'loc name1: '.$locationName.'<br/>';
		$url = $this->neonApiBaseUrl.'/locations/'.urlencode($locationName).'?apiToken='.$this->neonApiKey;
		$resultArr = $this->getNeonApiArr($url);
		if(!$resultArr) return false;
		if(isset($resultArr['locationType']) && $resultArr['locationType']){
			if($resultArr['locationType'] == 'SITE') $dwcArr['siteID'] = $resultArr['locationName'];
			elseif($resultArr['locationType'] == 'DOMAIN') $dwcArr['domainID'] = $resultArr['locationName'];
		}
		if(isset($resultArr['locationDescription']) && $resultArr['locationDescription']){
			$parStr = str_replace(array('"',', RELOCATABLE',', CORE','Parent'),'',$resultArr['locationDescription']);
			$parStr = str_replace('re - Reach','Reach',$parStr);
			$parStr = preg_replace('/ at site [A-Z]+/', '', $parStr);
			$parStr = trim($parStr,' ,;');
			if($parStr){
				if($resultArr['locationType'] != 'SITE' && $resultArr['locationType'] != 'DOMAIN'){
					$localityStr = '';
					if(isset($dwcArr['locality'])) $localityStr = $dwcArr['locality'];
					$dwcArr['locality'] = $parStr.', '.$localityStr;
				}
			}
		}

		if(!isset($dwcArr['decimalLatitude']) && isset($resultArr['locationDecimalLatitude']) && $resultArr['locationDecimalLatitude']){
			$dwcArr['decimalLatitude'] = $resultArr['locationDecimalLatitude'];
		}
		if(!isset($dwcArr['decimalLongitude']) && isset($resultArr['locationDecimalLongitude']) && $resultArr['locationDecimalLongitude']){
			$dwcArr['decimalLongitude'] = $resultArr['locationDecimalLongitude'];
		}
		if(!isset($dwcArr['verbatimCoordinates']) && isset($resultArr['locationUtmEasting']) && $resultArr['locationUtmEasting']){
			$dwcArr['verbatimCoordinates'] = trim($resultArr['locationUtmZone'].$resultArr['locationUtmHemisphere'].' '.$resultArr['locationUtmEasting'].'E '.$resultArr['locationUtmNorthing'].'N');
		}
		$elevMin = '';
		$elevMax = '';
		$elevUncertainty = '';
		if(isset($resultArr['locationElevation']) && $resultArr['locationElevation']){
			$elevMin = round($resultArr['locationElevation']);
		}
		if(isset($resultArr['elevation_uncertainty']) && $resultArr['elevation_uncertainty']){
			$elevUncertainty = $resultArr['elevation_uncertainty'];
		}
		$locPropArr = $resultArr['locationProperties'];
		if($locPropArr){
			$habitatArr = array();
			foreach($locPropArr as $propArr){
				if(!isset($dwcArr['georeferenceSources']) && $propArr['locationPropertyName'] == 'Value for Coordinate source'){
					$dwcArr['georeferenceSources'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['coordinateUncertaintyInMeters']) && $propArr['locationPropertyName'] == 'Value for Coordinate uncertainty'){
					$dwcArr['coordinateUncertaintyInMeters'] = $propArr['locationPropertyValue'];
				}
				elseif($propArr['locationPropertyName'] == 'Value for Minimum elevation'){
					$elevMin = round($propArr['locationPropertyValue']);
				}
				elseif($propArr['locationPropertyName'] == 'Value for Maximum elevation'){
					$elevMax = round($propArr['locationPropertyValue']);
				}
				elseif(!isset($dwcArr['country']) && $propArr['locationPropertyName'] == 'Value for Country'){
					$countryValue = $propArr['locationPropertyValue'];
					if($countryValue == 'unitedStates') $countryValue = 'United States';
					elseif($countryValue == 'USA') $countryValue = 'United States';
					$dwcArr['country'] = $countryValue;
				}
				elseif(!isset($dwcArr['county']) && $propArr['locationPropertyName'] == 'Value for County'){
					$dwcArr['county'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['geodeticDatum']) && $propArr['locationPropertyName'] == 'Value for Geodetic datum'){
					$dwcArr['geodeticDatum'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['plotDim']) && $propArr['locationPropertyName'] == 'Value for Plot dimensions'){
					$dwcArr['plotDim'] = ' (plot dimensions: '.$propArr['locationPropertyValue'].')';
				}
				elseif(!isset($habitatArr['landcover']) && strpos($propArr['locationPropertyName'],'Value for National Land Cover Database') !== false){
					$habitatArr['landcover'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($habitatArr['aspect']) && $propArr['locationPropertyName'] == 'Value for Slope aspect'){
					$habitatArr['aspect'] = 'slope aspect: '.$propArr['locationPropertyValue'];
				}
				elseif(!isset($habitatArr['gradient']) && $propArr['locationPropertyName'] == 'Value for Slope gradient'){
					$habitatArr['gradient'] = 'slope gradient: '.$propArr['locationPropertyValue'];
				}
				elseif(!isset($habitatArr['soil']) && $propArr['locationPropertyName'] == 'Value for Soil type order'){
					if($dwcArr['collid'] == 30 && (!isset($dwcArr['sciname']) || !$dwcArr['sciname'])) $dwcArr['sciname'] = $propArr['locationPropertyValue'];
					$habitatArr['soil'] = 'soil type order: '.$propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['stateProvince']) && $propArr['locationPropertyName'] == 'Value for State province'){
					$stateStr = $propArr['locationPropertyValue'];
					if(array_key_exists($stateStr, $this->stateArr)) $stateStr = $this->stateArr[$stateStr];
					$dwcArr['stateProvince'] = $stateStr;
				}
			}
			if($habitatArr) $dwcArr['habitat'] = implode('; ',$habitatArr);
		}
		if($elevMin && !isset($dwcArr['minimumElevationInMeters'])) $dwcArr['minimumElevationInMeters'] = $elevMin;
		if($elevMax && $elevMax != $elevMin && !isset($dwcArr['maximumElevationInMeters'])) $dwcArr['maximumElevationInMeters'] = $elevMax;
		if($elevUncertainty) $dwcArr['verbatimElevation'] = trim($elevMin.' - '.$elevMax,' -').' ('.$elevUncertainty.')';

		if(isset($resultArr['locationParent']) && $resultArr['locationParent']){
			if($resultArr['locationParent'] != 'REALM'){
				$this->setNeonLocationData($dwcArr, $resultArr['locationParent']);
			}
		}
		return true;
	}

	private function loadOccurrenceRecord($dwcArr, $currentOccurArr, $samplePK, $occid){
		if($dwcArr){
			$domainID = (isset($dwcArr['domainID'])?$dwcArr['domainID']:0);
			$siteID = (isset($dwcArr['siteID'])?$dwcArr['siteID']:0);
			unset($dwcArr['domainID']);
			unset($dwcArr['siteID']);
			$numericFieldArr = array('collid','decimalLatitude','decimalLongitude','minimumElevationInMeters','maximumElevationInMeters');
			$sql = '';
			$skipFieldArr = array('occid','collid','identifiers','assocmedia','identifications');
			if($occid){
				if($this->replaceFieldValues){
					//Only replace values that have not yet been explicitly modified
					$skipFieldArr = array_merge($skipFieldArr, $this->getOccurrenceEdits($occid));
				}
				foreach($dwcArr as $fieldName => $fieldValue){
					if(in_array(strtolower($fieldName),$skipFieldArr)) continue;
					if($this->replaceFieldValues){
						if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
							$sql .= ', '.$fieldName.' = '.$this->cleanInStr($fieldValue).' ';
						}
						else{
							$sql .= ', '.$fieldName.' = "'.$this->cleanInStr($fieldValue).'" ';
						}
						if(array_key_exists($fieldName, $currentOccurArr) && $currentOccurArr[$fieldName] != $fieldValue){
							$this->versionEdit($occid, $fieldName, $currentOccurArr[$fieldName], $fieldValue);
						}
					}
					else{
						if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
							$sql .= ', '.$fieldName.' = IFNULL('.$fieldName.','.$this->cleanInStr($fieldValue).') ';
						}
						else{
							$sql .= ', '.$fieldName.' = IFNULL('.$fieldName.',"'.$this->cleanInStr($fieldValue).'") ';
						}
					}
				}
				if($sql) $sql = 'UPDATE omoccurrences SET '.substr($sql, 1).' WHERE (occid = '.$occid.')';
			}
			else{
				$sql1 = ''; $sql2 = '';
				foreach($dwcArr as $fieldName => $fieldValue){
					if(in_array(strtolower($fieldName),$skipFieldArr)) continue;
					$fieldValue = $this->cleanInStr($fieldValue);
					if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
						$sql1 .= $fieldName.',';
						$sql2 .= $fieldValue.',';
					}
					else{
						if($fieldValue){
							$sql1 .= $fieldName.',';
							$sql2 .= '"'.trim($fieldValue,',; ').'",';
						}
					}
				}
				$sql = 'INSERT INTO omoccurrences(collid,'.$sql1.'dateentered) VALUES('.$dwcArr['collid'].','.$sql2.'NOW())';
			}
			if($sql){
				if($this->conn->query($sql)){
					if(!$occid){
						$occid = $this->conn->insert_id;
						if($occid) $this->conn->query('UPDATE NeonSample SET occid = '.$occid.', occidOriginal = IFNULL(occidOriginal,'.$occid.') WHERE (occid IS NULL) AND (samplePK = '.$samplePK.')');
					}
					$this->conn->query('UPDATE NeonSample SET harvestTimestamp = now() WHERE (samplePK = '.$samplePK.')');
					if(isset($dwcArr['identifiers'])) $this->setOccurrenceIdentifiers($dwcArr['identifiers'], $occid);
					if(isset($dwcArr['assocMedia'])) $this->setAssociatedMedia($dwcArr['assocMedia'], $occid);
					if(isset($dwcArr['identifications'])) $this->setIdentifications($dwcArr['identifications'], $occid);
					$this->setDatasetIndexing($domainID,$occid);
					$this->setDatasetIndexing($siteID,$occid);
				}
				else{
					$this->errorStr = 'ERROR creating new occurrence record: '.$this->conn->error;
					return false;
				}
			}
		}
		return $occid;
	}

	private function getOccurrenceEdits($occid){
		$retArr = array();
		$sql = 'SELECT DISTINCT fieldname FROM omoccuredits WHERE uid != 50 AND occid = '.$occid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[] = $r->fieldname;
		}
		$rs->free();
		return $retArr;
	}

	private function versionEdit($occid, $fieldName, $oldValue, $newValue){
		if(strtolower(trim($oldValue)) != strtolower(trim($newValue))){
			$sql = 'INSERT INTO omoccuredits(occid, fieldName, fieldValueOld, fieldValueNew, appliedStatus, uid)
				VALUES('.$occid.',"'.$fieldName.'","'.$this->cleanInStr($oldValue).'","'.$this->cleanInStr($newValue).'", 1, 50)';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR versioning edit: '.$this->conn->error;
				echo $this->errorStr;
			}
		}
	}

	private function setOccurrenceIdentifiers($idArr, $occid){
		if($idArr && $occid){
			$delSql = 'DELETE FROM omoccuridentifiers WHERE identifiername IN("'.implode('","',array_keys($idArr)).'") AND (occid = '.$occid.')';
			$this->conn->query($delSql);
			foreach($idArr as $idName => $idValue){
				if($idValue){
					$sortBy = 'NULL';
					if($idName=='NEON sampleID') $sortBy = 5;
					elseif($idName=='NEON sampleID Hash') $sortBy = 10;
					elseif($idName=='NEON sampleCode (barcode)') $sortBy = 15;
					elseif($idName=='NEON sampleUUID') $sortBy = 20;
					$sql = 'INSERT INTO omoccuridentifiers(occid, identifiername, identifierValue, sortBy)
						VALUES('.$occid.',"'.$this->cleanInStr($idName).'","'.$this->cleanInStr($idValue).'",'.$sortBy.')';
					if(!$this->conn->query($sql)){
						//$this->errorStr = 'ERROR loading occurrence identifiers: '.$this->conn->error;
					}
				}
			}
		}
	}

	private function setAssociatedMedia($assocMedia, $occid){
		if($assocMedia && $occid){
			foreach($assocMedia as $mediaArr){
				$loadMedia = true;
				$sqlTest = 'SELECT url, originalUrl FROM images WHERE occid = '.$occid;
				$rsTest = $this->conn->query($sqlTest);
				while($rTest = $rsTest->fetch_object()){
					if($rTest->originalUrl == $mediaArr['url'] || $rTest->url == $mediaArr['url']){
						$loadMedia = false;
						break;
					}
				}
				$rsTest->free();
				if($loadMedia){
					$sql = 'INSERT INTO images(occid, originalUrl, photographer) VALUES('.$occid.',"'.$mediaArr['url'].'",'.(isset($mediaArr['photographer'])?'"'.$mediaArr['photographer'].'"':'NULL').')';
					if(!$this->conn->query($sql)){
						//$this->errorStr = 'ERROR loading associatedMedia: '.$this->conn->error;
					}
				}
			}
		}
	}

	private function setIdentifications($identArr, $occid){
		if($occid){
			foreach($identArr as $idArr){
				if(isset($idArr['identifiedBy']) && isset($idArr['sciname'])){
					$sqlInsert = '';
					$sqlValue = '';
					foreach($idArr as $k => $v){
						$sqlInsert .= ', '.$k;
						$sqlValue .= ', "'.$this->cleanInStr($v).'"';
					}
					$sql = 'REPLACE INTO omoccurdeterminations(occid'.$sqlInsert.') VALUES('.$occid.$sqlValue.')';
					if(!$this->conn->query($sql)){
						$this->errorStr = 'ERROR adding identification to omoccurdetermination table: '.$this->conn->errno.' - '.$this->conn->error;
					}
				}
			}
		}
	}

	private function setDatasetIndexing($datasetName, $occid){
		if($datasetName && $occid){
			$sql = 'INSERT INTO omoccurdatasetlink(datasetid, occid) SELECT datasetid, '.$occid.' FROM omoccurdatasets WHERE name = "'.$datasetName.'"';
			if(!$this->conn->query($sql)){
				if($this->conn->errno != 1062) $this->errorStr = 'ERROR assigning occurrence to '.$datasetName.' dataset: '.$this->conn->errno.' - '.$this->conn->error;
			}
		}
	}

	private function getNeonApiArr($url){
		$retArr = array();
		//echo 'url: '.$url.'<br/>';
		if($url){
			//Request URL example: https://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam
			$json = @file_get_contents($url);
			//echo 'json1: '.$json; exit;

			/*
			//curl -X GET --header 'Accept: application/json' 'https://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam'
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_PUT, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Accept: application/json') );
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			$json = curl_exec($curl);
			*/
			if($json){
				$resultArr = json_decode($json,true);
				if(isset($resultArr['data'])){
					$retArr = $resultArr['data'];
				}
				elseif(isset($resultArr['error'])){
					$this->errorStr = 'ERROR thrown accessing NEON API: url ='.$url;
					if(isset($resultArr['error']['status'])) '; '.$this->errorStr .= $resultArr['error']['status'];
					if(isset($resultArr['error']['detail'])) '; '.$this->errorStr .= $resultArr['error']['detail'];
					$retArr = false;
				}
				else{
					$this->errorStr = 'ERROR retrieving NEON data: '.$url;
					$retArr = false;
				}
			}
			else{
				//$this->errorStr = 'ERROR: unable to access NEON API: '.$url;
				$retArr = false;
			}
			//curl_close($curl);
		}
		return $retArr;
	}

	private function setTaxonomy(&$dwcArr){
		$taxonCode = $dwcArr['sciname'];
		if($taxonCode){
			if($taxonCode == 'OTHE'){
				if(in_array($dwcArr['collid'], array(17,19,24,25,26,27,28,71))){
					$dwcArr['sciname'] = 'Mammalia';
					$dwcArr['tidinterpreted'] = '21269';
					/*
					* 	//Adjustment for Mammal OTHE taxonomic code
					* 	$sql = 'UPDATE omoccurrences
					* 	SET sciname = "Mammalia", scientificNameAuthorship = NULL, tidinterpreted = 21269, family = NULL
					* 	WHERE collid IN(17,19,24,25,26,27,28,71) AND (sciname IN("OTHE")) ';
					* 	if(!$this->conn->query($sql)){
					* 	echo 'ERROR updating Mammalia taxonomy for OTHE taxon codes: '.$sql;
					* 	}
					*/
				}
				elseif(in_array($dwcArr['collid'], array(66,20,12,15,70))){
					$dwcArr['sciname'] = 'Chordata';
					$dwcArr['tidinterpreted'] = '57';
					/*
					* 	//Adjustment for non-Mammal vertebrates with OTHE taxonomic code
					* 	$sql = 'UPDATE omoccurrences
					* 	SET sciname = "Chordata", scientificNameAuthorship = NULL, tidinterpreted = 57, family = NULL
					* 	WHERE collid IN(66,20,12,15,70) AND (sciname IN("OTHE"))';
					* 	if(!$this->conn->query($sql)){
					* 	echo 'ERROR updating Chordata taxonomy for OTHE taxon codes: '.$sql;
					* 	}
					*/
				}
			}
			else{
				if(!isset($this->taxonomyArr[$taxonCode])){
					$sql = 'SELECT t.tid, t.sciname, t.author, ts.family
						FROM taxaresourcelinks l INNER JOIN taxa t ON l.tid = t.tid
						INNER JOIN taxstatus ts ON t.tid = ts.tid
						WHERE ts.taxauthid = 1 AND l.sourceIdentifier = "'.$taxonCode.'"';
					if(in_array($dwcArr['collid'], array(30))){
						//Is a soil collection
						$taxonCode2 = '';
						if(substr($taxonCode,-1) == 's') $taxonCode2 = substr($taxonCode,0,-1);
						$sql = 'SELECT t.tid, t.sciname, t.author, ts.family
							FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
							WHERE ts.taxauthid = 1 AND t.sciname IN("'.$taxonCode.'"'.($taxonCode2?',"'.$taxonCode2.'"':'').')';
					}
					if($rs = $this->conn->query($sql)){
						while($r = $rs->fetch_object()){
							$this->taxonomyArr[$taxonCode][$r->tid]['sciname'] = $r->sciname;
							$this->taxonomyArr[$taxonCode][$r->tid]['author'] = $r->author;
							$this->taxonomyArr[$taxonCode][$r->tid]['family'] = $r->family;
						}
						$rs->free();
						if(isset($this->taxonomyArr[$taxonCode]) && count($this->taxonomyArr[$taxonCode]) > 1){
							$sql = 'SELECT DISTINCT t.tid, cl.collid
								FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
								INNER JOIN taxaenumtree e ON t.tid = e.tid
								INNER JOIN taxa p ON e.parenttid = p.tid
								INNER JOIN omcollcategories cat ON p.sciname = cat.notes
								INNER JOIN omcollcatlink cl ON cat.ccpk = cl.ccpk
								WHERE e.taxauthid = 1 AND ts.taxauthid = 1 AND p.rankid IN(10,30) AND  t.tid IN('.implode(',',array_keys($this->taxonomyArr[$taxonCode])).')';
							if($rs = $this->conn->query($sql)){
								while($r = $rs->fetch_object()){
									$this->taxonomyArr[$taxonCode][$r->tid]['collid'][] = $r->collid;
								}
							}
							$rs->free();
						}
					}
					else echo 'ERROR updating taxonomy codes: '.$sql;
				}
				if(isset($this->taxonomyArr[$taxonCode])){
					foreach($this->taxonomyArr[$taxonCode] as $tid => $taxonArr){
						if(!isset($taxonArr['collid']) || in_array($dwcArr['collid'], $taxonArr['collid'])){
							$dwcArr['tidinterpreted'] = $tid;
							$dwcArr['sciname'] = $taxonArr['sciname'];
							if($taxonArr['author']) $dwcArr['scientificNameAuthorship'] = $taxonArr['author'];
							if($taxonArr['family']) $dwcArr['family'] = $taxonArr['family'];
						}
					}
				}
				/*
				$sql = 'UPDATE taxaresourcelinks l INNER JOIN omoccurrences o ON l.sourceIdentifier = o.sciname
					INNER JOIN omcollcatlink catlink ON o.collid = catlink.collid
					INNER JOIN omcollcategories cat ON catlink.ccpk = cat.ccpk
					INNER JOIN taxa t ON l.tid = t.tid
					INNER JOIN taxaenumtree e2 ON t.tid = e2.tid
					INNER JOIN taxa t2 ON e2.parenttid = t2.tid
					INNER JOIN taxstatus ts ON t.tid = ts.tid
					SET o.sciname = t.sciname, o.scientificNameAuthorship = t.author, o.tidinterpreted = t.tid, o.family = ts.family
					WHERE e2.taxauthid = 1 AND ts.taxauthid = 1 AND t2.rankid IN(10,30) AND cat.notes = t2.sciname AND o.tidinterpreted IS NULL ';
				if(!$this->conn->query($sql)){
					echo 'ERROR updating taxonomy codes: '.$sql;
				}
				*/
			}
		}
	}

	private function adjustTaxonomy($occidArr){
		//Update tidInterpreted index value
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname SET o.tidinterpreted = t.tid WHERE (o.tidinterpreted IS NULL)';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating tidInterpreted: '.$sql;
		}

		//Update Mosquito taxa details
		$sql = 'UPDATE omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid '.
			'INNER JOIN taxa t ON o.sciname = t.sciname '.
			'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'SET o.scientificNameAuthorship = t.author, o.tidinterpreted = t.tid, o.family = ts.family '.
			'WHERE (o.collid = 29) AND (o.scientificNameAuthorship IS NULL) AND (o.family IS NULL) AND (ts.taxauthid = 1)';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating taxonomy codes: '.$sql;
		}

		//Temporary code needed until identification details are included within the NEON API
		//Populate missing sciname
		$sql = 'UPDATE neon_taxonomy nt INNER JOIN NeonSample s ON nt.sampleID = s.sampleID '.
			'INNER JOIN omoccurrences o ON s.occid = o.occid '.
			'LEFT JOIN taxstatus ts ON nt.tid = ts.tid '.
			'SET o.sciname = IFNULL(nt.sciname, nt.taxonid), o.tidinterpreted = nt.tid, o.family = ts.family, o.taxonRemarks = IFNULL(o.taxonRemarks,"Identification source: 2019 taxonomy extract from NEON central db") '.
			'WHERE (nt.sciname IS NOT NULL OR nt.taxonID IS NOT NULL) AND o.sciname IS NULL AND o.tidinterpreted IS NULL AND o.family IS NULL';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating taxonomy using temporary NEON taxon tables: '.$sql;
		}

		/*
		 //Populate missing dateIdentified
		 $sql = 'UPDATE neon_taxonomy nt INNER JOIN NeonSample s ON nt.sampleID = s.sampleID '.
		 'INNER JOIN omoccurrences o ON s.occid = o.occid '.
		 'SET o.dateIdentified = SUBSTRING(nt.identifiedDate,1,10) '.
		 'WHERE o.dateIdentified IS NULL AND nt.identifiedDate IS NOT NULL AND o.sciname IS NOT NULL ';
		 if(!$this->conn->query($sql)){
		 echo 'ERROR updating dateIdentified using temporary NEON taxon tables: '.$sql;
		 }

		 //Populate missing identifiedBy
		 $sql = 'UPDATE neon_taxonomy nt INNER JOIN NeonSample s ON nt.sampleID = s.sampleID '.
		 'INNER JOIN omoccurrences o ON s.occid = o.occid '.
		 'SET o.identifiedBy = nt.identifiedBy '.
		 'WHERE o.identifiedBy IS NULL AND nt.identifiedBy IS NOT NULL AND o.sciname IS NOT NULL';
		 if(!$this->conn->query($sql)){
		 echo 'ERROR updating identifiedBy using temporary NEON taxon tables: '.$sql;
		 }
		 */

		//Populate missing eventDate; needed until collectionDate is added to NEON API
		$sql = 'UPDATE neon_taxonomy nt INNER JOIN NeonSample s ON nt.sampleID = s.sampleID '.
			'INNER JOIN omoccurrences o ON s.occid = o.occid '.
			'SET o.eventDate = SUBSTRING(nt.collectDate,1,10) '.
			'WHERE o.eventDate IS NULL AND nt.collectDate IS NOT NULL';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating eventDate using temporary NEON taxon tables: '.$sql;
		}

		#Update mismatched eventDates (e.g. possibiliy incorrect within manifest)
		$sql = 'UPDATE IGNORE neon_taxonomy nt INNER JOIN NeonSample s ON nt.sampleID = s.sampleID '.
			'INNER JOIN omoccurrences o ON s.occid = o.occid '.
			'SET o.eventDate = SUBSTRING(nt.collectDate,1,10) '.
			'WHERE o.eventDate IS NOT NULL AND o.eventDate != DATE(nt.collectDate)';
		/*
		if(!$this->conn->query($sql)){
			echo 'ERROR mixing problematic eventDate using temporary NEON taxon tables: '.$sql;
		}
		*/

		//Run custon stored procedure that preforms some special assignment tasks
		if(!$this->conn->query('call occurrence_harvesting_sql()')){
			echo 'ERROR running stored procedure: occurrence_harvesting_sql';
		}

		//Run stored procedure that protects rare and sensitive species
		if(!$this->conn->query('call sensitive_species_protection()')){
			echo 'ERROR running stored procedure: sensitive_species_protection';
		}
	}

	private function setStateArr(){
		$sql = 'SELECT DISTINCT s.abbrev, s.statename '.
			'FROM lkupstateprovince s INNER JOIN lkupcountry c ON s.countryId = c.countryId '.
			'WHERE c.iso = "us" ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->stateArr[$r->abbrev] = $r->statename;
		}
		$rs->free();
	}

	private function setSampleClassArr(){
		$status = false;
		$result = $this->getNeonApiArr($this->neonApiBaseUrl.'/samples/supportedClasses?apiToken='.$this->neonApiKey);
		if(isset($result['entries'])){
			foreach($result['entries'] as $classArr){
				$this->sampleClassArr[$classArr['key']] = $classArr['value'];
			}
			$status = true;
		}
		return $status;
	}

	private function setDomainSiteArr(){
		$sql = 'SELECT domainNumber, domainName, siteID, siteName FROM neon_field_sites';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->domainSiteArr[$r->domainNumber] = $r->domainName;
			$this->domainSiteArr[$r->siteID] = $r->siteName;
		}
		$rs->free();
	}

	private function setSampleErrorMessage($samplePK, $msg){
		$sql = 'UPDATE NeonSample SET errorMessage = '.($msg?'"'.$msg.'"':'NULL').' WHERE (samplePK = '.$samplePK.')';
		$this->conn->query($sql);
	}

	//General data return functions
	public function getTargetCollectionArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT c.collid, CONCAT(c.collectionName, " (",CONCAT_WS(":",c.institutionCode,c.collectionCode),")") as name
			FROM omcollections c INNER JOIN omoccurrences o ON c.collid = o.collid INNER JOIN NeonSample s ON o.occid = s.occid
			WHERE c.institutioncode = "NEON"';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid] = $r->name;
		}
		$rs->free();
		return $retArr;
	}


	//Setters and getters
	public function setReplaceFieldValues($bool){
		if($bool) $this->replaceFieldValues = true;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function formatDate($dateStr){
		if(preg_match('/^(20\d{2})(\d{2})(\d{2})\D+/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(20\d{2})-(\d{2})-(\d{2})\D*/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(20\d{2})/', $dateStr, $m)){
			$month = $m[1];
			if(strlen($month) == 1) $month = '0'.$month;
			$day = $m[2];
			if(strlen($day) == 1) $day = '0'.$day;
			$dateStr = $m[3].'-'.$month.'-'.$day;
		}
		return $dateStr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>