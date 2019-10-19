<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceSesar extends Manager {

	private $collid;
	private $collArr = array();
	private $igsnDom;
	private $sesarUser;
	private $sesarPwd;
	private $namespace;
	private $registrationMethod;
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

	public function batchProcessIdentifiers($processingCount){
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

		$this->initiateDom();

		//Batch assign GUIDs
		$ns = $this->namespace;
		if($ns == 'NEO') $ns .= 'N';
		$increment = 1;
		$sql = 'SELECT occid';
		foreach($this->fieldMap as $symbField => $mapArr){
			if(isset($mapArr['sql'])) $sql .= ','.$mapArr['sql'];
			$sql .= ','.$symbField;
		}
		$sql .= ' FROM omoccurrences WHERE collid = '.$this->collid.' AND occurrenceid IS NULL ';
		if($processingCount) $sql .= 'LIMIT '.$processingCount;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$igsn = '';
			if($this->generateIGSN){
				$igsn = base_convert($seedBaseTen+$increment,10,36);
				$igsn = str_pad($igsn, (strlen($ns)-9), '0', STR_PAD_LEFT);
				$igsn = strtoupper($igsn);
				$igsn = $ns.$igsn;
			}
			//Set Symbiota record values
			$this->fieldMap['occid']['value'] = $r['occid'];
			foreach($this->fieldMap as $symbField => $fieldArr){
				$this->fieldMap[$symbField]['value'] = $r[$symbField];
			}

			if($igsn){
				if($this->updateOccurrenceID($igsn, $r['occid'])){
					//First test to makes sure new locally assigned IGSN can be entered, and is unique within database
					$this->setSampleXmlNode($igsn);
				}
			}
			else{
				$this->setSampleXmlNode($igsn);
			}
			$increment++;
		}
		$rs->free();


		//Register identifier with SESAR
		if($this->registrationMethod == 'api'){
			$this->registerIdentifiersViaApi();
		}
		elseif($this->registrationMethod == 'csv'){

		}
		elseif($this->registrationMethod == 'xml'){
			header('Content-Description: ');
			header('Content-Type: application/xml');
			header('Content-Disposition: attachment; filename=SESAR_IGSN_registration_'.date('Y-m-d_His').'.xml');
			header('Content-Transfer-Encoding: UTF-8');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			$this->igsnDom->preserveWhiteSpace = false;
			$this->igsnDom->formatOutput = true;
			//echo $this->igsnDom->saveXML();
			$this->igsnDom->save('php://output');
		}

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

	private function registerIdentifiersViaApi(){
		$status = true;
		//$baseUrl = 'https://app.geosamples.org/webservices/upload.php';
		$baseUrl = 'https://sesardev.geosamples.org/webservices/upload.php';		// TEST URI
		$contentStr = $this->igsnDom->saveXML();
		$requestData = array ('username' => $this->sesarUser, 'password' => $this->sesarUser, 'content' => $contentStr);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $baseUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($responseXML = curl_exec($ch)){
			$this->processRegistrationResponse($responseXML);
		}
		else{
			$this->errorMessage = 'FATAL CURL ERROR registering IGSN: '.curl_error($ch).' (#'.curl_errno($ch).')';
			//$header = curl_getinfo($ch);
			$status = false;
		}
		curl_close($ch);
		return $status;
	}

	public function processRegistrationResponse($responseXML){
		$status = true;
		$dom = new DOMDocument('1.0','UTF-8');
		if($dom->loadXML($responseXML)){
			$rootElem = $dom->documentElement;
			if($validNodeList = $rootElem->getElementsByTagName('valid')){
				foreach ($validNodeList as $validNode) {
					if($validNode->nodeValue == 'no'){
						$codeStr = $validNode->getAttribute('code');
						$this->errorMessage = 'ERROR registering IGSN ('.$codeStr.'): ';
						$errCodeList = $rootElem->getElementsByTagName('error');
						foreach ($errCodeList as $errElem) {
							$this->warningArr[] = $errElem->nodeValue;
						}
					}
				}
				$status = false;
			}
			else{
				$sampleNodeList = $rootElem->getElementsByTagName('sample');
				foreach ($sampleNodeList as $sampleNode) {
					if($validNodeList = $sampleNode->getElementsByTagName('valid')){
						foreach ($validNodeList as $validNode) {
							if($validNode->nodeValue == 'no'){
								$codeStr = $validNode->getAttribute('code');
								$this->errorMessage = 'ERROR registering IGSN ('.$codeStr.'): ';
								$errCodeList = $validNode->getElementsByTagName('error');
								foreach ($errCodeList as $errElem) {
									$this->warningArr[] = $errElem->nodeValue;
								}
							}
						}
					}
					else{
						//Success get and load igsn, if they haven't already been added
						$nameNodeList = $dom->getElementsByTagName('name');



						$igsnNodeList = $dom->getElementsByTagName('igsn');
						foreach ($igsnNodeList as $igsnNode) {
							$igsn = $elem->nodeValue;
						}

					}
				}
			}
		}
		else{
			$this->errorMessage = 'FATAL ERROR parsing response XML: '.htmlentities($responseXML);
			$status = false;
		}
		return $status;
	}

	private function initiateDom(){
		$this->igsnDom = new DOMDocument('1.0','UTF-8');

		//Add root element
		$rootElem = $this->igsnDom->createElement('samples');
		$rootElem->setAttribute('xmlns','http://app.geosamples.org');
		$rootElem->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$rootElem->setAttribute('xsi:schemaLocation','http://app.geosamples.org/4.0/sample.xsd');
		$this->igsnDom->appendChild($rootElem);
	}

	private function setSampleXmlNode($igsn){
		$sampleElem = $this->igsnDom->createElement('sample');

		$this->addSampleElem($this->igsnDom, $sampleElem, 'user_code', $this->namespace);		//Required
		$this->addSampleElem($this->igsnDom, $sampleElem, 'sample_type', 'Individual Sample');		//Required
		$this->addSampleElem($this->igsnDom, $sampleElem, 'material', 'Biology');		//Required
		$this->addSampleElem($this->igsnDom, $sampleElem, 'igsn', $igsn);		//If blank, SESAR will generate new IGSN

		$classificationElem = $this->igsnDom->createElement('classification');
		$biologyElem = $this->igsnDom->createElement('Biology');
		$biologyElem->appendChild($this->igsnDom->createElement('Macrobiology'));
		$classificationElem->appendChild($biologyElem);
		$sampleElem->appendChild($classificationElem);

		$this->addSampleElem($this->igsnDom, $sampleElem, 'collection_method', 'Manual');
		$this->addSampleElem($this->igsnDom, $sampleElem, 'collection_date_precision', 'day');

		foreach($this->fieldMap as $symbArr){
			if(isset($symbArr['sesar'])) $this->addSampleElem($this->igsnDom, $sampleElem, $symbArr['sesar'], $symbArr['value']);
		}

		$this->addSampleElem($this->igsnDom, $sampleElem, 'elevation_unit', 'meters');
		$this->addSampleElem($this->igsnDom, $sampleElem, 'current_archive', $this->collArr['collectionName']);
		$this->addSampleElem($this->igsnDom, $sampleElem, 'current_archive_contact', $this->collArr['contact'].($this->collArr['email']?' ('.$this->collArr['email'].')':''));

		$serverDomain = "http://";
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $serverDomain = "https://";
		$serverDomain .= $_SERVER["SERVER_NAME"];
		if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443) $serverDomain .= ':'.$_SERVER["SERVER_PORT"];
		$url = $serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');
		$url .= 'collections/individual/index.php?occid='.$this->fieldMap['occid']['value'];
		$externalUrlsElem = $this->igsnDom->createElement('external_urls');
		$externalUrlElem = $this->igsnDom->createElement('external_url');
		$urlElem = $this->igsnDom->createElement('url');
		$urlElem->appendChild($this->igsnDom->createElement($url));
		$externalUrlElem->appendChild($urlElem);
		$descriptionElem = $this->igsnDom->createElement('description');
		$descriptionElem->appendChild($this->igsnDom->createElement('Source Reference URL'));
		$externalUrlElem->appendChild($descriptionElem);
		$urlTypeElem = $this->igsnDom->createElement('url_type');
		$urlTypeElem->appendChild($this->igsnDom->createElement('regular URL'));
		$externalUrlElem->appendChild($urlTypeElem);
		$externalUrlsElem->appendChild($externalUrlElem);
		$sampleElem->appendChild($externalUrlsElem);

		$rootElem = $this->igsnDom->documentElement;
		$rootElem->appendChild($sampleElem);
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

	public function setSesarUser($user){
		$this->sesarUser = $user;
	}

	public function setSesarPwd($pwd){
		$this->sesarPwd = $pwd;
	}

	public function setNamespace($ns){
		$this->namespace = $ns;
	}

	public function setRegistrationMethod($method){
		$this->registrationMethod = $method;
	}

	public function setGenerateIGSN($bool){
		if($bool == 1) $this->generateIGSN = true;
	}
}
?>