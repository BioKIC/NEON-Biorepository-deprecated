<?php
class OccurrenceHarvester{

	private $conn;
	private $stateArr = array();
	private $sampleClassArr = array();
	private $errorStr;

 	public function __construct(){
 		$this->conn = MySQLiConnectionFactory::getCon("write");
 	}

 	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	//Occurrence harvesting functions
	public function batchHarvestOccid($postArr){
		set_time_limit(3600);
		$sqlWhere = '';
		if(isset($postArr['scbox'])){
			$sqlWhere = 'WHERE samplePK IN('.implode(',',$postArr['scbox']).')';
		}
		elseif($postArr['action'] == 'harvestAll'){
			$sqlWhere = 'WHERE (occid IS NULL) AND (errorMessage IS NULL) ORDER BY shipmentPK LIMIT 10';
		}
		if($sqlWhere){
			$this->setStateArr();
			if($this->setSampleClassArr()){
				$occidArr = array();
				$cnt = 1;
				$shipmentPK = '';
				$sql = 'SELECT samplePK, shipmentPK, sampleID, sampleCode, sampleClass, taxonID, individualCount, filterVolume, namedLocation, collectDate, occid FROM NeonSample '.$sqlWhere;
				//echo $sql.'<br/>';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					if($shipmentPK != $r->shipmentPK){
						$shipmentPK = $r->shipmentPK;
						echo '<li><b>Processing shipment #'.$shipmentPK.'</b></li>';
					}
					echo '<li style="margin-left:15px">'.$cnt.': '.($r->occid?'Appending':'Harvesting').' for sample '.$r->sampleID.'... ';
					$sampleArr = array();
					$sampleArr['samplePK'] = $r->samplePK;
					$sampleArr['sampleID'] = strtoupper($r->sampleID);
					$sampleArr['sampleCode'] = $r->sampleCode;
					$sampleArr['sampleClass'] = $r->sampleClass;
					$sampleArr['taxonID'] = $r->taxonID;
					$sampleArr['individualCount'] = $r->individualCount;
					$sampleArr['filterVolume'] = $r->filterVolume;
					$sampleArr['namedLocation'] = $r->namedLocation;
					$sampleArr['collectDate'] = $r->collectDate;
					if($this->validateSampleClass($sampleArr)){
						if($dwcArr = $this->harvestNeonOccurrence($sampleArr)){
							if($occid = $this->loadOccurrenceRecord($dwcArr, $r->samplePK, $r->occid)){
								$occidArr[] = $occid;
								echo 'success!</li>';
							}
						}
						else{
							echo '</li><li style="margin-left:30px">'.$this->errorStr.'</li>';
						}
					}
					else{
						echo '</li><li style="margin-left:30px">ERROR validating: '.$this->errorStr.'</li>';
					}
					$cnt++;
					flush();
					ob_flush();
				}
				$rs->free();
				$this->setNeonTaxonomy($occidArr);
			}
			else{
				echo '<li>'.$this->errorStr.'</li>';
			}
		}
		return false;
	}

	private function validateSampleClass(&$sampleArr){
		$viewArr = array();
		$this->setSampleErrorMessage($sampleArr['samplePK'], '');
		if($sampleArr['sampleCode']){
			$url = 'https://data.neonscience.org/api/v0/samples/view?barcode='.$sampleArr['sampleCode'];
			$viewArr = $this->getSampleViews($url,$sampleArr['samplePK']);
			if($viewArr['sampleTag'] != $sampleArr['sampleID']){
				$this->errorStr = 'sampleID not matching: '.$viewArr['sampleTag'];
				$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
				return false;
			}
			elseif($viewArr['sampleClass'] != $sampleArr['sampleClass']){
				$this->errorStr = 'sampleClass not matching: '.$viewArr['sampleClass'];
				$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
				return false;
			}
		}
		elseif($sampleArr['sampleID'] && $sampleArr['sampleClass']){
			//If sampleId and sampleClass are not correct, nothing will be returned
			$url = 'https://data.neonscience.org/api/v0/samples/view?sampleTag='.$sampleArr['sampleID'].'&sampleClass='.$sampleArr['sampleClass'];
			$viewArr = $this->getSampleViews($url,$sampleArr['samplePK']);
			if($viewArr){
				if($viewArr['barcode']) $sampleArr['sampleCode'] = $viewArr['barcode'];
			}
			else{
				$this->errorStr = 'sampleID and sampleClass failed to validate';
				$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
				return false;
			}
		}
		else{
			$this->errorStr = 'Sample identifiers incomplete';
			$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
			return false;
		}
		$eventArr = $viewArr['sampleEvents'];
		foreach($eventArr as $k => $eArr){
			if(substr($eArr['ingestTableName'],0,4) == 'scs_') continue;
			if(strpos($sampleArr['sampleClass'],$eArr['ingestTableName']) !== false){
				$fieldArr = $eArr['smsFieldEntries'];
				foreach($fieldArr as $k => $fArr){
					if($fArr['smsKey'] == 'fate_location'){
						//Override namedLocation that is in the manifest
						$sampleArr['namedLocation'] = $fArr['smsValue'];
					}
					elseif($fArr['smsKey'] == 'fate_date'){
						if($fArr['smsValue']){
							$dateStr = $this->formatDate($fArr['smsValue']);
							if($sampleArr['collectDate']){
								if($dateStr != $sampleArr['collectDate']){
									$this->errorStr = 'collectDate failed to validate ('.$dateStr.' != '.$sampleArr['collectDate'].')';
									$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
									return false;
								}
							}
							else{
								$sampleArr['collectDate'] = $dateStr;
							}
						}
					}
				}
				break;
			}
		}
		return true;
	}

	private function getSampleViews($url,$samplePK){
		$viewArr = $this->getNeonApiArr($url);
		if(!isset($viewArr['sampleViews'])){
			$this->errorStr = 'no sampleViews exist';
			$this->setSampleErrorMessage($samplePK, $this->errorStr);
			return false;
		}
		if(count($viewArr['sampleViews']) > 1){
			$this->errorStr = 'multiple sampleViews exists';
			$this->setSampleErrorMessage($samplePK, $this->errorStr);
			return false;
		}
		return current($viewArr['sampleViews']);
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
				$dwcArr['othercatalogNumbers'] = $sampleArr['sampleID'];
				if($sampleArr['collectDate']) $dwcArr['eventDate'] = $sampleArr['collectDate'];
				if($sampleArr['individualCount']) $dwcArr['individualCount'] = $sampleArr['individualCount'];
				if($sampleArr['filterVolume']) $dwcArr['occurrenceRemarks'] = 'filterVolume:'.$sampleArr['filterVolume'];

				//Set occurrence description using sampleClass
				if($sampleArr['sampleClass']){
					if(array_key_exists($sampleArr['sampleClass'], $this->sampleClassArr)) $dwcArr['verbatimAttributes'] = $this->sampleClassArr[$sampleArr['sampleClass']];
					else $dwcArr['verbatimAttributes'] = $sampleArr['sampleClass'];
				}

				//Build proper location code
				if(!$this->setNeonLocationData($dwcArr, $sampleArr['namedLocation'])){
					$this->setSampleErrorMessage($sampleArr['samplePK'], 'locatity data failed to populate');
					return false;
				}

				$dwcArr['sciname'] = $sampleArr['taxonID'];
				$this->setNeonCollector($dwcArr);
			}
			else{
				$this->errorStr = 'ERROR: unable to retrieve collid using sampleClass: '.$sampleArr['sampleClass'];
				$this->setSampleErrorMessage($sampleArr['samplePK'], 'unable to retrieve collid using sampleClass');
				return false;
			}
		}
		return $dwcArr;
	}

	private function loadOccurrenceRecord($dwcArr, $samplePK, $occid){
		if($dwcArr){
			$numericFieldArr = array('collid','decimalLatitude','decimalLongitude','minimumElevationInMeters');
			$sql = '';
			if($occid){
				foreach($dwcArr as $fieldName => $fieldValue){
					if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){

					}
					else{
						$sql .= ', '.$fieldName.' = IFNULL('.$fieldName.',"'.$this->cleanInStr($fieldValue).'") ';
					}
				}
				$sql = 'UPDATE omoccurrences SET '.substr($sql, 1).' WHERE (occid = '.$occid.')';
			}
			else{
				$sql1 = ''; $sql2 = '';
				foreach($dwcArr as $fieldName => $fieldValue){
					$sql1 .= $fieldName.',';
					if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
						$sql2 .= $fieldValue.',';
					}
					else{
						$sql2 .= '"'.$this->cleanInStr($fieldValue).'",';
					}
				}
				$sql = 'INSERT INTO omoccurrences('.trim($sql1,',').') VALUES('.trim($sql2,',').')';
			}
			//echo '<br/>'.$sql.'<br/>';
			if($this->conn->query($sql)){
				if(!$occid){
					$occid = $this->conn->insert_id;
					if($occid) $this->conn->query('UPDATE NeonSample SET occid = '.$occid.' WHERE (occid IS NULL) AND (samplePK = '.$samplePK.')');
				}
			}
			else{
				$this->errorStr = 'ERROR creating new occurrence record: '.$this->conn->error.'; '.$sql;
				return false;
			}
		}
		return $occid;
	}

	private function setCollectionIdentifier(&$dwcArr,$sampleClass){
		$status = false;
		$sql = 'SELECT collid FROM omcollections WHERE (collectionID = "'.$sampleClass.'")';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$dwcArr['collid'] = $r->collid;
			$status = true;
		}
		$rs->free();
		return $status;
	}

	private function setNeonLocationData(&$dwcArr, $locationName){
		//https://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam
		//echo 'loc name1: '.$locationName.'<br/>';
		$url = 'https://data.neonscience.org/api/v0/locations/'.$locationName;
		//echo 'url: '.$url.'<br/>';
		$resultArr = $this->getNeonApiArr($url);
		if(!$resultArr) return false;

		//Extract DwC values
		$locality = $this->getLocationParentStr($resultArr);

		$dwcArr['decimalLatitude'] = $resultArr['locationDecimalLatitude'];
		$dwcArr['decimalLongitude'] = $resultArr['locationDecimalLongitude'];
		$dwcArr['minimumElevationInMeters'] = round($resultArr['locationElevation']);
		$habitatArr = array();
		$locPropArr = $resultArr['locationProperties'];
		foreach($locPropArr as $propArr){
			if($propArr['locationPropertyName'] == 'Value for Coordinate source') $dwcArr['georeferenceSources'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Coordinate uncertainty') $dwcArr['coordinateUncertaintyInMeters'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Country'){
				$countryValue = $propArr['locationPropertyValue'];
				if($countryValue == 'unitedStates') $countryValue = 'United States';
				$dwcArr['country'] = $countryValue;
			}
			elseif($propArr['locationPropertyName'] == 'Value for County') $dwcArr['county'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Geodetic datum') $dwcArr['geodeticDatum'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Plot dimensions') $locality .= ' (plot dimensions: '.$propArr['locationPropertyValue'].')';
			elseif(strpos($propArr['locationPropertyName'],'Value for National Land Cover Database') !== false) $habitatArr['landcover'] = $propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Slope aspect') $habitatArr['aspect'] = 'slope aspect: '.$propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Slope gradient') $habitatArr['gradient'] = 'slope gradient: '.$propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for Soil type order') $habitatArr['soil'] = 'soil type order: '.$propArr['locationPropertyValue'];
			elseif($propArr['locationPropertyName'] == 'Value for State province'){
				$stateStr = $propArr['locationPropertyValue'];
				if(array_key_exists($stateStr, $this->stateArr)) $stateStr = $this->stateArr[$stateStr];
				$dwcArr['stateProvince'] = $stateStr;
			}
		}
		if($locality) $dwcArr['locality'] = trim($locality,', ');
		//Grab some habitat details availalbe with parent location
		if(preg_match('/basePlot\.bet\.[NSEW]{1}/',$locationName)){
			//echo 'loc name2: '.substr($locationName,0,-2).'<br/>';
			$urlHab = 'https://data.neonscience.org/api/v0/locations/'.substr($locationName,0,-2);
			$habArr = $this->getNeonApiArr($urlHab);
			if(isset($habArr['locationProperties'])){
				foreach($habArr['locationProperties'] as $propArr){
					if($propArr['locationPropertyName'] == 'Value for Slope aspect' && !isset($habitatArr['aspect'])) $habitatArr['aspect'] = 'slope aspect: '.$propArr['locationPropertyValue'];
					elseif($propArr['locationPropertyName'] == 'Value for Slope gradient' && !isset($habitatArr['gradient'])) $habitatArr['gradient'] = 'slope gradient: '.$propArr['locationPropertyValue'];
					elseif($propArr['locationPropertyName'] == 'Value for Soil type order' && !isset($habitatArr['soil'])) $habitatArr['soil'] = 'soil type order: '.$propArr['locationPropertyValue'];
				}
			}
		}
		if($habitatArr) $dwcArr['habitat'] = implode('; ',$habitatArr);
		return true;
	}

	private function getLocationParentStr($resultArr){
		$parStr = '';
		if(isset($resultArr['locationDescription'])){
			$parStr = str_replace(array('"',', RELOCATABLE'),'',$resultArr['locationDescription']);
			$parStr = preg_replace('/ at site [A-Z]+/', '', $parStr);
			if(isset($resultArr['locationParent'])){
				if($resultArr['locationParent'] == 'REALM') return '';
				//echo 'loc name3: '.$resultArr['locationParent'].'<br/>';
				$url = 'https://data.neonscience.org/api/v0/locations/'.$resultArr['locationParent'];
				$newLoc = $this->getLocationParentStr($this->getNeonApiArr($url));
				if($newLoc) $parStr = $newLoc.', '.$parStr;
			}
		}
		return $parStr;
	}

	private function setNeonCollector(&$dwcArr){
		//Not yet sure how to obtain this data

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
				$this->errorStr = 'ERROR: unable to access NEON API: '.$url;
				$retArr = false;
			}
			//curl_close($curl);
		}
		return $retArr;
	}

	private function setNeonTaxonomy($occidArr){
		if($occidArr){
			$sql = 'UPDATE omoccurrences o INNER JOIN taxaresourcelinks r ON o.sciname = r.sourceidentifier '.
				'INNER JOIN taxa t ON r.tid = t.tid '.
				'INNER JOIN taxstatus ts ON ts.tid = ts.tid '.
				'SET o.sciname = t.sciname, o.scientificNameAuthorship = t.author, o.tidinterpreted = t.tid, o.family = ts.family '.
				'WHERE (ts.taxauthid = 1) AND (o.occid IN('.(implode(',',$occidArr)).'))';
			//echo $sql;
			if(!$this->conn->query($sql)){
				echo 'ERROR updating taxonomy codes: '.$sql;
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
		$result = $this->getNeonApiArr('https://data.neonscience.org/api/v0/samples/supportedClasses');
		if(isset($result['entries'])){
			foreach($result['entries'] as $k => $classArr){
				$this->sampleClassArr[$classArr['key']] = $classArr['value'];
			}
			$status = true;
		}
		return $status;
	}

	private function setSampleErrorMessage($samplePK, $msg){
		$sql = 'UPDATE NeonSample SET errorMessage = '.($msg?'"'.$msg.'"':'NULL').' WHERE (samplePK = '.$samplePK.')';
		$this->conn->query($sql);
	}

	//Occurrence listing functions


	//Setters and getters
	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function formatDate($dateStr){
		if(preg_match('/^(20\d{2})(\d{2})(\d{2})$/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
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