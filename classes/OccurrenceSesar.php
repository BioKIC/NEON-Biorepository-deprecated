<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceSesar extends Manager {

	private $collid;
	private $collArr = array();
	private $namespace;
	private $sesarUser;
	private $sesarPwd;
	private $generateIGSN = false;
	private $userCodeArr = array();
	private $fieldMap = array();

	public function __construct($type = 'write'){
		parent::__construct(null, $type);
		$this->fieldMap['basisOfRecord']['sesar'] = 'collection_method_descr';
		$this->fieldMap['catalogNumber']['sesar'] = 'name';
		$this->fieldMap['catalogNumber']['sql'] = 'IFNULL(catlaogNumber, otherCatalogNumbers) AS catalogNumber';
		$this->fieldMap['sciname']['sesar'] = 'field_name';
		$this->fieldMap['sciname']['sql'] = 'CONCAT_WS(" ",sciname, scientificNameAuthorship) AS sciname';
		$this->fieldMap['recordedBy']['sesar'] = 'collector';
		$this->fieldMap['eventDate']['sesar'] = 'collection_start_date';
		$this->fieldMap['verbatimAttributes']['sesar'] = 'description';
		$this->fieldMap['country']['sesar'] = 'country';
		$this->fieldMap['stateProvince']['sesar'] = 'province';
		$this->fieldMap['county']['sesar'] = 'county';
		$this->fieldMap['decimalLatitude']['sesar'] = 'latitude';
		$this->fieldMap['decimalLongitude']['sesar'] = 'longitude';
		$this->fieldMap['minimumElevationInMeters']['sesar'] = 'elevation';
		$this->fieldMap['maximumElevationInMeters']['sesar'] = 'elevation_end';
		//$this->fieldMap['parentOccurrenceID']['sesar'] = 'parent_igsn';
		//$this->fieldMap['parentOccurrenceID']['sql'] = ' AS parentOccurrenceID';
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function batchAssignIdentifiers(){
		$status = true;
		if(!$this->namespace){
			$this->errorMessage = 'FATAL ERROR batch assigning IDs: namespace not set';
			return false;
		}
		$seedBaseTen = '';
		if($this->generateIGSN){
			//Get maximum identifier
			$seed = 0;
			$sql = 'SELECT MAX(occurrenceID) as maxid FROM omoccurrences WHERE occurrenceID LIKE "'.$this->namespace.'%"';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()) $seed = $r->maxid;
			$rs->free();
			$seedBaseTen = base_convert($seed,36,10);
		}

		//Batch assign GUIDs
		$increment = 1;
		$sql = 'SELECT occid';
		foreach($this->fieldMap as $symbField => $mapArr){
			if(isset($mapArr['sql'])) $sql .= ','.$mapArr['sql'];
			$sql .= ','.$symbField;
		}
		$sql .= ' FROM omoccurrences WHERE collid = '.$this->collid.' AND occurrenceid IS NULL';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$igsn = '';
			if($this->generateIGSN){
				$igsn = base_convert($seedBaseTen+$increment,10,36);
				$igsn = str_pad($igsn, (strlen($this->namespace)-9), '0', STR_PAD_LEFT);
				$igsn = strtoupper($igsn);
				$igsn = $this->namespace.$igsn;
				if(!$this->updateOccurrenceID($igsn, $r['occid'])) $igsn = false;
			}
			//Set Symbiota record values
			if($igsn !== false){
				$this->fieldMap['occid']['value'] = $r['occid'];
				foreach($this->fieldMap as $symbField => $fieldArr){
					$this->fieldMap[$symbField]['value'] = $r[$symbField];
				}
				//Register identifier with SESAR
				$status = $this->registerIgsn($igsn);
				if($status && !$this->generateIGSN) $this->updateOccurrenceID($status, $r['occid']);
				$increment++;
			}
		}
		$rs->free();
		return $status;
	}

	// SESAR web service calls (http://www.geosamples.org/interop)
	// End point: https://app.geosamples.org/webservices/
	// Test end point: https://sesardev.geosamples.org/webservices/
	public function validateUser(){
		$status = false;
		$baseUrl = 'https://app.geosamples.org/webservices/credentials_service_v2.php';
		if(!$this->sesarUser || !$this->sesarUser){
			$this->errorMessage = 'Fatal Error validating user: SESAR username or password not set';
			return false;
		}
		$requestData = array ('username' => $this->sesarUser, 'password' => $this->sesarUser);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $baseUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($responseXML= curl_exec($ch)){
			$dom = new DOMDocument('1.0','UTF-8');
			if($dom->loadXML($responseXML)){
				$validElem = $dom->getElementsByTagName('valid');
				foreach ($validElem as $elem) {
					if($elem->nodeValue == 'yes'){
						$status = true;
						$userCodes = $dom->getElementsByTagName('user_code');
						foreach ($userCodes as $UserCodeElem) {
							$this->userCodeArr[] = $UserCodeElem->nodeValue;
						}
					}
					else{
						$this->errorMessage = 'Fatal Error validating user: ';
						$errCodes = $dom->getElementsByTagName('error');
						foreach ($errCodes as $errElem) {
							$this->errorMessage .= $errElem->nodeValue;
						}
						$status = false;
					}
				}
			}
			else{
				$this->errorMessage = 'FATAL ERROR parsing response XML: '.htmlentities($responseXML);
				$status = false;
			}
		}
		else{
			$this->errorMessage = 'FATAL CURL ERROR validating user: '.curl_error($ch).' (#'.curl_errno($ch).')';
			//$header = curl_getinfo($ch);
			$status = false;
		}
		curl_close($ch);
		return $status;
	}

	private function registerIgsn($igsn){
		$status = true;
		//$baseUrl = 'https://app.geosamples.org/webservices/upload.php';
		$baseUrl = 'https://sesardev.geosamples.org/webservices/upload.php';		// TEST URI
		$contentStr = $this->getSampleXml($igsn);
		$requestData = array ('username' => $this->sesarUser, 'password' => $this->sesarUser, 'content' => $contentStr);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $baseUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($responseXML = curl_exec($ch)){
			$dom = new DOMDocument('1.0','UTF-8');
			if($dom->loadXML($responseXML)){
				$validElem = $dom->getElementsByTagName('valid');
				if($validElem){
					foreach ($validElem as $elem) {
						if($elem->nodeValue == 'no'){
							$codeStr = $elem->getAttribute('code');
							$this->errorMessage = 'ERROR registering IGSN ('.$codeStr.'): ';
							$errCodes = $dom->getElementsByTagName('error');
							foreach ($errCodes as $errElem) {
								$this->errorMessage .= $errElem->nodeValue;
							}
						}
					}
					$status = false;
				}
				else{
					$igsnElem = $dom->getElementsByTagName('igsn');
					foreach ($igsnElem as $elem) {
						$status = $elem->nodeValue;
					}
				}
			}
			else{
				$this->errorMessage = 'FATAL ERROR parsing response XML: '.htmlentities($responseXML);
				$status = false;
			}
		}
		else{
			$this->errorMessage = 'FATAL CURL ERROR registering IGSN: '.curl_error($ch).' (#'.curl_errno($ch).')';
			//$header = curl_getinfo($ch);
			$status = false;
		}
		curl_close($ch);
		return $status;
	}

	private function getSampleXml($igsn){
		$dom = new DOMDocument('1.0','UTF-8');

		//Add root element
		$rootElem = $dom->createElement('samples');
		$rootElem->setAttribute('xmlns','http://app.geosamples.org');
		$rootElem->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$rootElem->setAttribute('xsi:schemaLocation','http://app.geosamples.org/4.0/sample.xsd');
		$dom->appendChild($rootElem);

		$sampleElem = $dom->createElement('sample');
		$rootElem->appendChild($rootElem);

		$this->addSampleElem($dom, $sampleElem, 'user_code', $this->namespace);		//Required
		$this->addSampleElem($dom, $sampleElem, 'sample_type', 'Individual Sample');		//Required
		$this->addSampleElem($dom, $sampleElem, 'material', 'Biology');		//Required
		$this->addSampleElem($dom, $sampleElem, 'igsn', $igsn);		//Required

		$classificationElem = $dom->createElement('classification');
		$biologyElem = $dom->createElement('Biology');
		$biologyElem->appendChild($dom->createElement('Macrobiology'));
		$classificationElem->appendChild($biologyElem);
		$sampleElem->appendChild($classificationElem);

		$this->addSampleElem($dom, $sampleElem, 'collection_method', 'Manual');
		$this->addSampleElem($dom, $sampleElem, 'collection_date_precision', 'day');

		foreach($this->fieldMap as $symbArr){
			if(isset($symbArr['sesar'])) $this->addSampleElem($dom, $sampleElem, $symbArr['sesar'], $symbArr['value']);
		}

		$this->addSampleElem($dom, $sampleElem, 'elevation_unit', 'meters');
		$this->addSampleElem($dom, $sampleElem, 'current_archive', $this->collArr['collectionName']);
		$this->addSampleElem($dom, $sampleElem, 'current_archive_contact', $this->collArr['contact'].($this->collArr['email']?' ('.$this->collArr['email'].')':''));

		$serverDomain = "http://";
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $serverDomain = "https://";
		$serverDomain .= $_SERVER["SERVER_NAME"];
		if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443) $serverDomain .= ':'.$_SERVER["SERVER_PORT"];
		$url = $serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');
		$url .= 'collections/individual/index.php?occid='.$this->fieldMap['occid']['value'];
		$externalUrlsElem = $dom->createElement('external_urls');
		$externalUrlElem = $dom->createElement('external_url');
		$urlElem = $dom->createElement('url');
		$urlElem->appendChild($dom->createElement($url));
		$externalUrlElem->appendChild($urlElem);
		$descriptionElem = $dom->createElement('description');
		$descriptionElem->appendChild($dom->createElement('Source Reference URL'));
		$externalUrlElem->appendChild($descriptionElem);
		$urlTypeElem = $dom->createElement('url_type');
		$urlTypeElem->appendChild($dom->createElement('regular URL'));
		$externalUrlElem->appendChild($urlTypeElem);
		$externalUrlsElem->appendChild($externalUrlElem);
		$sampleElem->appendChild($externalUrlsElem);

		return $dom->saveXML();
	}

	private function addSampleElem(&$dom, &$sampleElem, $elemName, $elemValue){
		$newElem = $dom->createElement($elemName);
		$newElem->appendChild($dom->createTextNode($elemValue));
		$sampleElem->appendChild($newElem);
	}

	private function updateOccurrenceID($igsn, $occid){
		$status = true;
		$sql = 'UPDATE omoccurrences SET occurrenceID = '.$igsn.' WHERE occid = '.$occid;
		if(!$this->conn($sql)){
			$this->logOrEcho('ERROR adding IGSN to occurrence table: '.$this->conn->error,2);
			$status = false;
		}
		return $status;
	}

	//Misc data return functions
	public function getMissingGuidCount(){
		$cnt = 0;
		$sql = 'SELECT COUNT(*) AS cnt FROM omoccurrences ';
		if($this->collid) $sql .= 'WHERE (occurrenceid IS NULL) AND (collid = '.$this->collid.')';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$cnt = $r->cnt;
		}
		$rs->free();
		return $cnt;
	}

	//Setters and getters
	public function setCollid($id){
		if($id && is_numeric($id)){
			$this->collid = $id;
			$sql = 'SELECT institutionCode, collectionCode, collectionName, contact, email FROM omcollections WHERE collid = '.$id;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$this->collArr['collectionName'] = $r->collectionName.' ('.$r->institutionCode.($r->collectionCode?$r->collectionCode:'').')';
				$this->collArr['contact'] = $r->contact;
				$this->collArr['email'] = $r->email;
			}
			$rs->free();
		}
	}

	public function getCollectionName(){
		if($this->collArr) return $this->collArr['collectionName'];
	}

	public function setNamespace($ns){
		$this->namespace = $ns;
	}

	public function setSesarUser($user){
		$this->sesarUser = $user;
	}

	public function setSesarPwd($pwd){
		$this->sesarPwd = $pwd;
	}

	public function setGenerateIGSN($bool){
		$this->generateIGSN = $bool;
	}
}
?>