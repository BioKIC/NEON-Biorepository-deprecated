<?php
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');
include_once($SERVER_ROOT.'/config/symbini.php');

class OccurrenceHarvester{

	private $conn;
	private $stateArr = array();
	private $sampleClassArr = array();
	private $domainSiteArr = array();
	private $replaceFieldValues = false;
	private $targetFieldArr = array();
	private $neonApiKey;
	private $errorStr;

 	public function __construct(){
		$this->conn = MySQLiConnectionFactory::getCon("write");
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
		set_time_limit(3600);
		//Set variables
		if(isset($postArr['replaceFieldValues']) && $postArr['replaceFieldValues']) $this->setReplaceFieldValues(true);
		$sqlWhere = '';
		if(isset($postArr['scbox'])){
			$sqlWhere = 'AND s.samplePK IN('.implode(',',$postArr['scbox']).')';
		}
		elseif($postArr['action'] == 'harvestOccurrences'){
			$sqlWhere = '';
			if(isset($postArr['nullOccurrencesOnly'])){
				$sqlWhere .= 'AND (s.occid IS NULL) ';
			}
			if($postArr['nullfilter']){
				$sqlWhere .= 'AND (o.'.$postArr['nullfilter'].' IS NULL) ';
				if($postArr['nullfilter'] == 'sciname') $sqlWhere .= 'AND (o.collid NOT IN(5,23,30,31,41,42)) AND (s.sampleid REGEXP BINARY "\.[0-9]{8}\.[A-Z]{3,8}[0-9]{0,2}\.") ';
			}
			if($postArr['errorStr'] == 'nullError'){
				$sqlWhere .= 'AND (s.errorMessage IS NULL) ';
			}
			elseif($postArr['errorStr']){
				$sqlWhere .= 'AND (s.errorMessage = "'.$this->cleanInStr($postArr['errorStr']).'") ';
			}
			$sqlWhere .= 'ORDER BY s.shipmentPK ';
			if(isset($postArr['limit']) && is_numeric($postArr['limit'])) $sqlWhere .= 'LIMIT '.$postArr['limit'];
			else $sqlWhere .= 'LIMIT 1000 ';
		}
		if(isset($postArr['targetFields']) && $postArr['targetFields']){
			$targetArr = $postArr['targetFields'];
			foreach($targetArr as $field){
				$this->setTargetFieldArr($field);
			}
		}
		//Start harvest
		if($sqlWhere){
			$this->setStateArr();
			$this->setDomainSiteArr();
			if($this->setSampleClassArr()){
				$collArr = array();
				$occidArr = array();
				$cnt = 1;
				$shipmentPK = '';
				$sql = 'SELECT s.samplePK, s.shipmentPK, s.sampleID, s.alternativeSampleID, s.sampleUuid, s.sampleCode, s.sampleClass, s.taxonID, '.
					's.individualCount, s.filterVolume, s.namedLocation, s.collectDate, s.symbiotaTarget, s.occid '.
					'FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
					'WHERE s.checkinuid IS NOT NULL AND s.sampleReceived = 1 AND (s.sampleCondition != "OPAL Sample" OR s.sampleCondition IS NULL) '.$sqlWhere;
				//echo $sql.'<br/>';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$this->errorStr = '';
					if($shipmentPK != $r->shipmentPK){
						$shipmentPK = $r->shipmentPK;
						echo '<li><b>Processing shipment #'.$shipmentPK.'</b></li>';
					}
					echo '<li style="margin-left:15px">'.$cnt.': '.($r->occid?($this->replaceFieldValues?'Rebuilding':'Appending'):'Harvesting').' '.$r->sampleID.'... ';
					$sampleArr = array();
					$sampleArr['samplePK'] = $r->samplePK;
					$sampleArr['sampleID'] = strtoupper($r->sampleID);
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
					if($this->validateSampleArr($sampleArr)){
						if($dwcArr = $this->harvestNeonOccurrence($sampleArr)){
							if($occid = $this->loadOccurrenceRecord($dwcArr, $r->samplePK, $r->occid)){
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
					$this->setNeonTaxonomy($occidArr);
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
			}
			else echo '<li>'.$this->errorStr.'</li>';
		}
		return false;
	}

	private function validateSampleArr(&$sampleArr){
		$viewArr = array();
		$this->setSampleErrorMessage($sampleArr['samplePK'], '');
		//Verify central identifiers
		if($sampleArr['sampleCode']){
			$url = 'https://data.neonscience.org/api/v0/samples/view?apiToken='.$this->neonApiKey.'&barcode='.$sampleArr['sampleCode'];
			$viewArr = $this->getSampleApiData($url);
			if($viewArr){
				if($viewArr['sampleTag'] != $sampleArr['sampleID']){
					//$this->errorStr = 'sampleID not matching: '.$viewArr['sampleTag'];
					//$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
					//return false;
				}
				elseif($viewArr['sampleClass'] != $sampleArr['sampleClass']){
					$this->errorStr = 'sampleClass not matching: '.$viewArr['sampleClass'];
					$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
					return false;
				}
			}
			else{
				//if(!$this->errorStr) $this->errorStr = 'NEON API failed searching by barcode';
				//$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
			}
		}
		if(!$viewArr){
			if($sampleArr['sampleID'] && $sampleArr['sampleClass']){
				//If sampleId and sampleClass are not correct, nothing will be returned
				$url = 'https://data.neonscience.org/api/v0/samples/view?apiToken='.$this->neonApiKey.'&sampleTag='.urlencode($sampleArr['sampleID']).'&sampleClass='.urlencode($sampleArr['sampleClass']);
				$viewArr = $this->getSampleApiData($url);
				if($viewArr){
					if($viewArr['barcode']) $sampleArr['sampleCode'] = $viewArr['barcode'];
				}
				else{
					$this->errorStr = 'NEON API failed searching by sampleID and sampleClass';
					$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
					return false;
				}
			}
			else{
				$this->errorStr = 'Sample identifiers incomplete';
				$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
				return false;
			}
		}

		//Update sampleUuid
		if(!$sampleArr['sampleUuid']){
			$sampleArr['sampleUuid'] = $viewArr['sampleUuid'];
			$this->conn->query('UPDATE NeonSample SET sampleUuid = "'.$viewArr['sampleUuid'].'" WHERE (sampleUuid IS NULL) AND (samplePK = '.$sampleArr['samplePK'].')');
		}
		//If available via API, override namedLocation that has been supplied with the manifest
		if(isset($viewArr['namedLocation']) && $viewArr['namedLocation']){
			$sampleArr['namedLocation'] = $viewArr['namedLocation'];
		}
		if(isset($viewArr['parentID']) && $viewArr['parentID']){
			$sampleArr['parentID'] = $viewArr['parentID'];
		}
		/*
		if($viewArr['collectDate'] != $sampleArr['collectDate']){
			$this->errorStr = 'collectDate failed to validate ('.$dateStr.' != '.$sampleArr['collectDate'].')';
			$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
			//return false;
		}
		*/
		return true;
	}

	private function getSampleApiData($url, $earliestDate = '2050-01-01'){
		//echo 'url: '.$url.'<br/>';
		$sampleViewArr = $this->getNeonApiArr($url);
		if(!isset($sampleViewArr['sampleViews'])){
			//$this->errorStr = 'no sampleViews exist';
			return false;
		}
		if(count($sampleViewArr['sampleViews']) > 1){
			$this->errorStr = 'multiple sampleViews exists';
			return false;
		}
		$viewArr = current($sampleViewArr['sampleViews']);
		//parse Sample Event details
		$eventArr = $viewArr['sampleEvents'];
		$preferredLocation = '';
		foreach($eventArr as $k => $eArr){
			if(substr($eArr['ingestTableName'],0,4) == 'scs_') continue;
			if(strpos($eArr['ingestTableName'],'shipment')) continue;
			if(strpos($eArr['ingestTableName'],'identification')) continue;
			if(strpos($eArr['ingestTableName'],'sorting')) continue;
			if(strpos($eArr['ingestTableName'],'archivepooling')) continue;
			if(strpos($eArr['ingestTableName'],'archivedata')) continue;
			if(strpos($eArr['ingestTableName'],'barcoding')) continue;

			$fateLocation = '';
			$fateDate = '';
			$fieldArr = $eArr['smsFieldEntries'];
			foreach($fieldArr as $k => $fArr){
				if($fArr['smsKey'] == 'fate_location') $fateLocation = $fArr['smsValue'];
				elseif($fArr['smsKey'] == 'fate_date' && $fArr['smsValue']) $fateDate = $this->formatDate($fArr['smsValue']);
			}
			if($fateLocation){
				if(strpos($viewArr['sampleClass'],$eArr['ingestTableName']) !== false){
					$preferredLocation = $fateLocation;
					break;
				}
				elseif($fateDate && $fateDate < $earliestDate){
					$earliestDate = $fateDate;
					$preferredLocation = $fateLocation;
				}
			}
		}

		//Get parent identifier
		if(isset($viewArr['parentSampleIdentifiers'][0]['sampleUuid'])){
			$viewArr['parentID'] = $viewArr['parentSampleIdentifiers'][0]['sampleUuid'];
			if(!$preferredLocation){
				//Try to get namedLocation from parent
				$parUrl = 'https://data.neonscience.org/api/v0/samples/view?sampleUuid='.$viewArr['parentID'];
				$parViewArr = $this->getSampleApiData($parUrl,$earliestDate);
				if(isset($parViewArr['namedLocation']) && $parViewArr['namedLocation']) $preferredLocation = $parViewArr['namedLocation'];
			}
		}
		if($preferredLocation) $viewArr['namedLocation'] = $preferredLocation;

		return $viewArr;
		/*
		Array (
			[sampleViews] => Array (
				[0] => Array (
					[sampleEvents] => Array (
						[0] => Array (
							[ingestTableName] => scs_shipmentCreation_in
							[smsFieldEntries] => Array (
								[0] => Array ( [smsKey] => fate [smsValue] => active )
								[1] => Array ( [smsKey] => fate_date [smsValue] => 2018-12-11 12:00:00.0 )
								[2] => Array ( [smsKey] => fate_location [smsValue] => D01 )
								[3] => Array ( [smsKey] => sample_tag [smsValue] => vt05/1/XT7PfMHKcefKjMiJPVCF+wvWbszL7d3ZUHtU= )
								[4] => Array ( [smsKey] => sample_type [smsValue] => carabid )
							)
						)
						[1] => Array (
							[ingestTableName] => scs_shipmentVerification_in
							[smsFieldEntries] => Array (
								[0] => Array ( [smsKey] => fate [smsValue] => active )
								[1] => Array ( [smsKey] => fate_date [smsValue] => 2018-12-14 12:00:00.0 )
								[2] => Array ( [smsKey] => fate_location [smsValue] => Arizona State University )
								[3] => Array ( [smsKey] => sample_tag [smsValue] => vt05/1/XT7PfMHKcefKjMiJPVCF+wvWbszL7d3ZUHtU= )
							)
						)
						[2] => Array (
							[ingestTableName] => bet_archivepooling_in
							[smsFieldEntries] => Array (
								[0] => Array ( [smsKey] => fate [smsValue] => active )
								[1] => Array ( [smsKey] => fate_date [smsValue] => 2018-02-14 12:00:00.0 )
								[2] => Array ( [smsKey] => fate_location [smsValue] => HARV_022.basePlot.bet )
								[3] => Array ( [smsKey] => sample_tag [smsValue] => vt05/1/XT7PfMHKcefKjMiJPVCF+wvWbszL7d3ZUHtU= )
								[4] => Array ( [smsKey] => sample_type [smsValue] => bet_archivepooling_in.subsampleID.bet )
							)
						)
					)
					[parentSampleIdentifiers] => Array (
						[0] => Array ( [sampleUuid] => 3e8d89d4-c8e3-4487-9732-ab9a697a00ba [sampleTag] => vt05/1/XT7NtAkDFor3rOa7g6uqo/nlzgZH7Y+Klbho= [sampleClass] => bet_sorting_in.subsampleID.bet [barcode] => [archiveGuid] => )
						[1] => Array ( [sampleUuid] => 2f211059-2663-4a77-9e5d-a854c76bc398 [sampleTag] => vt05/1/XT7OMLrgj+IivO9fmP8nQDgQfZX00jLJCB0Q= [sampleClass] => bet_sorting_in.subsampleID.bet [barcode] => [archiveGuid] => )
					)
					[childSampleIdentifiers] =>
					[sampleClass] => bet_archivepooling_in.subsampleID.bet
					[sampleTag] => vt05/1/XT7PfMHKcefKjMiJPVCF+wvWbszL7d3ZUHtU=
					[barcode] =>
					[archiveGuid] =>
					[sampleUuid] => 8a4f452e-49a7-4838-a9fc-215f5c91e080
				)
			)
		)
		*/
	}

	private function harvestNeonOccurrence($sampleArr){
		$dwcArr = array();
		if($sampleArr['samplePK']){
			if($this->setCollectionIdentifier($dwcArr,$sampleArr['sampleClass'])){
				//Get data that was provided within manifest
				$dwcArr['otherCatalogNumbers'] = $sampleArr['sampleID'];
				if($sampleArr['collectDate'] && $sampleArr['collectDate'] != '0000-00-00') $dwcArr['eventDate'] = $sampleArr['collectDate'];
				if($sampleArr['individualCount']) $dwcArr['individualCount'] = $sampleArr['individualCount'];
				if($sampleArr['filterVolume']) $dwcArr['occurrenceRemarks'] = 'filterVolume:'.$sampleArr['filterVolume'];

				//Set occurrence description using sampleClass
				if($sampleArr['sampleClass']){
					if(array_key_exists($sampleArr['sampleClass'], $this->sampleClassArr)) $dwcArr['verbatimAttributes'] = $this->sampleClassArr[$sampleArr['sampleClass']];
					else $dwcArr['verbatimAttributes'] = $sampleArr['sampleClass'];
				}
				//Get date from sampleID
				if(!isset($dwcArr['eventDate'])){
					if(preg_match('/\.(20\d{2})(\d{2})(\d{2})\./',$sampleArr['sampleID'],$m)){
						$dwcArr['eventDate'] = $m[1].'-'.$m[2].'-'.$m[3];
					}
				}
				//Build proper location code
				if($this->setNeonLocationData($dwcArr, $sampleArr['namedLocation'])){
					if(isset($dwcArr['locality']) && isset($dwcArr['domainID'])){
						$locStr = $this->domainSiteArr[$dwcArr['domainID']].' ('.$dwcArr['domainID'].'), ';
						if(isset($dwcArr['siteID'])) $locStr .= $this->domainSiteArr[$dwcArr['siteID']].' ('.$dwcArr['siteID'].'), ';
						$dwcArr['locality'] = trim($locStr.$dwcArr['locality']);
					}
					if(isset($dwcArr['plotDim'])){
						$dwcArr['locality'] .= $dwcArr['plotDim'];
						unset($dwcArr['plotDim']);
					}
					$dwcArr['locationID'] = $sampleArr['namedLocation'];
				}
				else{
					$dwcArr['locality'] = $sampleArr['namedLocation'];
					$this->errorStr = 'locality data failed to populate';
					$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
					//return false;
				}
				if(isset($dwcArr['locality']) && $dwcArr['locality']) $dwcArr['locality'] = trim($dwcArr['locality'],' ,;.');

				if($sampleArr['taxonID']){
					$dwcArr['sciname'] = $sampleArr['taxonID'];
					$dwcArr['taxonRemarks'] = 'Identification source: inferred from shipment manifest';
				}
				else{
					if(!in_array($dwcArr['collid'], array(5,21,22,23,30,31,41,42,50,57))){
						if(preg_match('/\.\d{8}\.([A-Z]{2,15}\d{0,2})\./',$sampleArr['sampleID'],$m)){
							$dwcArr['sciname'] = $m[1];
							$dwcArr['taxonRemarks'] = 'Identification source: parsed from NEON sampleID';
						}
					}
				}
				$this->setNeonCollector($dwcArr);
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

	private function setCollectionIdentifier(&$dwcArr,$sampleClass){
		$status = false;
		$sql = 'SELECT collid FROM omcollections WHERE (datasetID = "'.$sampleClass.'") OR (datasetID LIKE "%,'.$sampleClass.',%") OR (datasetID LIKE "'.$sampleClass.',%") OR (datasetID LIKE "%,'.$sampleClass.'")';
		$rs = $this->conn->query($sql);
		if($rs->num_rows == 1){
			$r = $rs->fetch_object();
			$dwcArr['collid'] = $r->collid;
			$status = true;
		}
		$rs->free();
		return $status;
	}

	private function setNeonLocationData(&$dwcArr, $locationName){
		//https://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam
		//echo 'loc name1: '.$locationName.'<br/>';
		$url = 'https://data.neonscience.org/api/v0/locations/'.urlencode($locationName).'?apiToken='.$this->neonApiKey;
		$resultArr = $this->getNeonApiArr($url);
		//echo 'url: '.$url.'<br/>'; print_r($resultArr); echo '<br/><br/>';
		if(!$resultArr) return false;
		if(isset($resultArr['locationType']) && $resultArr['locationType']){
			if($resultArr['locationType'] == 'SITE') $dwcArr['siteID'] = $resultArr['locationName'];
			elseif($resultArr['locationType'] == 'DOMAIN') $dwcArr['domainID'] = $resultArr['locationName'];
		}
		if(isset($resultArr['locationDescription']) && $resultArr['locationDescription']){
			$parStr = str_replace(array('"',', RELOCATABLE',', CORE','Parent'),'',$resultArr['locationDescription']);
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
		if(isset($resultArr['locationElevation']) && $resultArr['locationElevation']){
			$elevMin = round($resultArr['locationElevation']);
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

		if(isset($resultArr['locationParent']) && $resultArr['locationParent']){
			if($resultArr['locationParent'] != 'REALM'){
				$this->setNeonLocationData($dwcArr, $resultArr['locationParent']);
			}
		}

		return true;
	}

	private function setNeonCollector(&$dwcArr){
		//Not yet sure how to obtain this data

	}

	private function loadOccurrenceRecord($dwcArr, $samplePK, $occid){
		if($dwcArr){
			$domainID = (isset($dwcArr['domainID'])?$dwcArr['domainID']:0);
			$siteID = (isset($dwcArr['siteID'])?$dwcArr['siteID']:0);
			unset($dwcArr['domainID']);
			unset($dwcArr['siteID']);
			$numericFieldArr = array('collid','decimalLatitude','decimalLongitude','minimumElevationInMeters','maximumElevationInMeters');
			$sql = '';
			if($occid){
				$skipFieldArr = array('occid','collid');
				if($this->replaceFieldValues){
					//Only replace values that have not yet been explicitly modified
					$sqlEdit = 'SELECT DISTINCT fieldname FROM omoccuredits WHERE occid = '.$occid;
					$rsEdit = $this->conn->query($sqlEdit);
					while($rEdit = $rsEdit->fetch_object()){
						$skipFieldArr[] = $rEdit->fieldname;
					}
					$rsEdit->free();
				}
				foreach($dwcArr as $fieldName => $fieldValue){
					if(in_array(strtolower($fieldName),$skipFieldArr)) continue;
					if($this->targetFieldArr && !in_array(strtolower($fieldName),$this->targetFieldArr)) continue;
					if($this->replaceFieldValues){
						if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
							$sql .= ', '.$fieldName.' = '.$this->cleanInStr($fieldValue).' ';
						}
						else{
							$sql .= ', '.$fieldName.' = "'.$this->cleanInStr($fieldValue).'" ';
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
				$sql = 'INSERT INTO omoccurrences('.trim($sql1,',').',dateentered) VALUES('.trim($sql2,',').',NOW())';
			}
			if($sql){
				if($this->conn->query($sql)){
					if(!$occid){
						$occid = $this->conn->insert_id;
						if($occid){
							$this->conn->query('UPDATE NeonSample SET occid = '.$occid.' WHERE (occid IS NULL) AND (samplePK = '.$samplePK.')');
							$this->datasetIndexing($domainID,$occid);
							$this->datasetIndexing($siteID,$occid);
						}
					}
				}
				else{
					$this->errorStr = 'ERROR creating new occurrence record: '.$this->conn->error.'; '.$sql;
					return false;
				}
			}
		}
		return $occid;
	}

	private function datasetIndexing($datasetName, $occid){
		if($datasetName){
			$sql = 'INSERT INTO omoccurdatasetlink(datasetid, occid) SELECT datasetid, '.$occid.' FROM omoccurdatasets WHERE name = "'.$datasetName.'"';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR assigning occurrence to '.$datasetName.' dataset';
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

	private function setNeonTaxonomy($occidArr){
		if($occidArr){
			$sql = 'UPDATE taxaresourcelinks l INNER JOIN omoccurrences o ON l.sourceIdentifier = o.sciname '.
				'INNER JOIN omcollcatlink catlink ON o.collid = catlink.collid '.
				'INNER JOIN omcollcategories cat ON catlink.ccpk = cat.ccpk '.
				'INNER JOIN taxa t ON l.tid = t.tid '.
				'INNER JOIN taxaenumtree e2 ON t.tid = e2.tid '.
				'INNER JOIN taxa t2 ON e2.parenttid = t2.tid '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'SET o.sciname = t.sciname, o.scientificNameAuthorship = t.author, o.tidinterpreted = t.tid, o.family = ts.family '.
				'WHERE e2.taxauthid = 1 AND ts.taxauthid = 1 AND t2.rankid IN(10,30) AND cat.notes = t2.sciname AND o.tidinterpreted IS NULL ';
			if(!$this->conn->query($sql)){
				echo 'ERROR updating taxonomy codes: '.$sql;
			}

			//Adjustment for Mammal OTHE costs
			$sql = 'UPDATE omoccurrences '.
				'SET sciname = "Mammalia", scientificNameAuthorship = NULL, tidinterpreted = 21269, family = NULL '.
				'WHERE collid IN(17,19,24,25,26,27,28) AND (sciname = "Chordata") ';
			if(!$this->conn->query($sql)){
				echo 'ERROR updating mammalia taxonomy for OTHE taxon codes: '.$sql;
			}

			//Update Mosquito taxa details
			$sql = 'UPDATE omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid '.
				'INNER JOIN taxa t ON o.sciname = t.sciname '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'SET o.scientificNameAuthorship = t.author, o.tidinterpreted = t.tid, o.family = ts.family '.
				'WHERE (s.sampleClass = "mos_identification_in.individualIDList") AND (ts.taxauthid = 1) AND (o.scientificNameAuthorship IS NULL) AND (o.family IS NULL)';
			if(!$this->conn->query($sql)){
				echo 'ERROR updating taxonomy codes(2): '.$sql;
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
		$result = $this->getNeonApiArr('https://data.neonscience.org/api/v0/samples/supportedClasses?apiToken='.$this->neonApiKey);
		if(isset($result['entries'])){
			foreach($result['entries'] as $k => $classArr){
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

	//Occurrence listing functions


	//Setters and getters
	public function setReplaceFieldValues($bool){
		if($bool) $this->replaceFieldValues = true;
	}

	private function setTargetFieldArr($fieldName){
		if($fieldName){
			if($fieldName == 'sciname'){
				$this->targetFieldArr[] = 'sciname';
				$this->targetFieldArr[] = 'scientificnameauthorship';
				$this->targetFieldArr[] = 'family';
				$this->targetFieldArr[] = 'taxonremarks';
			}
			elseif($fieldName == 'recordedBy'){
				$this->targetFieldArr[] = 'recordedby';
				$this->targetFieldArr[] = 'recordnumber';
				$this->targetFieldArr[] = 'eventdate';
			}
			elseif($fieldName == 'country'){
				$this->targetFieldArr[] = 'country';
				$this->targetFieldArr[] = 'stateprovince';
				$this->targetFieldArr[] = 'county';
			}
			elseif($fieldName == 'decimalLatitude'){
				$this->targetFieldArr[] = 'decimallatitude';
				$this->targetFieldArr[] = 'decimallongitude';
				$this->targetFieldArr[] = 'coordinateuncertaintyinmeters';
				$this->targetFieldArr[] = 'verbatimcoordinates';
				$this->targetFieldArr[] = 'geodeticdatum';
				$this->targetFieldArr[] = 'georeferencesources';
				$this->targetFieldArr[] = 'minimumelevationinmeters';
				$this->targetFieldArr[] = 'maximumelevationinmeters';
			}
			elseif($fieldName == 'habitat'){
				$this->targetFieldArr[] = 'habitat';
				$this->targetFieldArr[] = 'verbatimattributes';
				$this->targetFieldArr[] = 'occurrenceremarks';
				$this->targetFieldArr[] = 'individualcount';
			}
			else{
				$this->targetFieldArr[] = $fieldName;
			}
		}
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