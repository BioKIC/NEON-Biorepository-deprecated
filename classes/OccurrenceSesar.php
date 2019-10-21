<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceSesar extends Manager {

	private $collid;
	private $collArr = array();
	private $igsnDom;
	private $sesarUser;
	private $sesarPwd;
	private $namespace;
	private $generateIGSN = false;
	private $igsnSeed = false;
	private $registrationMethod;
	private $fieldMap = array();

	public function __construct($type = 'write'){
		parent::__construct(null, $type);
		$this->fieldMap['basisOfRecord']['sesar'] = 'collection_method_descr';
		$this->fieldMap['catalogNumber']['sesar'] = 'name';
		$this->fieldMap['catalogNumber']['sql'] = 'CONCAT_WS(" ",IFNULL(catalogNumber, otherCatalogNumbers),"[",occid,"]") AS catalogNumber';
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
		$this->logOrEcho('Starting batch IGSN processing ('.date('Y-m-d H:i:s').')');
		$this->logOrEcho('sesarUser: '.$this->sesarUser);
		$this->logOrEcho('namespace: '.$this->namespace);
		$this->logOrEcho('registrationMethod: '.$this->registrationMethod);
		$this->logOrEcho('generateIGSN locally: '.($this->generateIGSN?'true':'false'));
		if(!$this->namespace){
			$this->errorMessage = 'FATAL ERROR batch assigning IDs: namespace not set';
			$this->logOrEcho($this->errorMessage);
			return false;
		}
		$baseTenID = '';
		if($this->generateIGSN){
			if(!$this->igsnSeed){
				$this->errorMessage = 'FATAL ERROR batch assigning IDs: IGSN seed not set';
				$this->logOrEcho($this->errorMessage);
				return false;
			}
			$baseTenID = base_convert($this->igsnSeed,36,10);
		}

		$this->initiateDom();

		//Batch assign GUIDs
		$increment = 1;
		$sql = 'SELECT occid';
		foreach($this->fieldMap as $symbField => $mapArr){
			if(isset($mapArr['sql'])) $sql .= ','.$mapArr['sql'];
			else $sql .= ','.$symbField;
		}
		$sql .= ' FROM omoccurrences WHERE collid = '.$this->collid.' AND occurrenceid IS NULL ';
		if($processingCount) $sql .= 'LIMIT '.$processingCount;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$igsn = '';
			if($this->generateIGSN){
				$igsn = base_convert($baseTenID,10,36);
				$igsn = str_pad($igsn, (9-strlen($this->namespace)), '0', STR_PAD_LEFT);
				$igsn = strtoupper($igsn);
				//$igsn = $this->namespace.$igsn;
				$baseTenID++;
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
			$this->logOrEcho('#'.$increment.': IGSN created '.$this->fieldMap['catalogNumber']['value']);
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
			exit;
		}

		$this->logOrEcho('Finished ('.date('Y-m-d H:i:s').')');
		return $status;
	}

	// SESAR web service calls (http://www.geosamples.org/interop)
	// End point: https://app.geosamples.org/webservices/
	// Test end point: https://sesardev.geosamples.org/webservices/
	public function validateUser(){
		$userCodeArr = array();
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
						$userCodes = $dom->getElementsByTagName('user_code');
						foreach ($userCodes as $UserCodeElem) {
							$userCodeArr[] = $UserCodeElem->nodeValue;
						}
					}
					else{
						$this->errorMessage = 'Fatal Error validating user: ';
						$errCodes = $dom->getElementsByTagName('error');
						foreach ($errCodes as $errElem) {
							$this->errorMessage .= $errElem->nodeValue;
						}
						$userCodeArr = false;
					}
				}
			}
			else{
				$this->errorMessage = 'FATAL ERROR parsing response XML: '.htmlentities($responseXML);
				$userCodeArr = false;
			}
		}
		else{
			$this->errorMessage = 'FATAL CURL ERROR validating user: '.curl_error($ch).' (#'.curl_errno($ch).')';
			//$header = curl_getinfo($ch);
			$userCodeArr = false;
		}
		curl_close($ch);
		return $userCodeArr;
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
						if(!$this->generateIGSN){
							//Success get and load igsn, since it wasn't added prior
							$nameNodeList = $dom->getElementsByTagName('name');
							$nameNode = $nameNodeList[0];
							$nameStr = $nameNode->nodeValue;
							$igsnNodeList = $dom->getElementsByTagName('igsn');
							$igsnNode = $igsnNodeList[0];
							$igsn = $igsnNode->nodeValue;
							if(preg_match('/[(\d+)]$/', $nameStr,$m)){
								$this->updateOccurrenceID($igsn, $m[1]);
							}
							else{
								$this->warningArr[] = 'WARNING: unable to extract occid to add igsn';
							}
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

		$ns = $this->namespace;
		if($ns == 'NEON') $ns = 'NEO';
		$this->addSampleElem($this->igsnDom, $sampleElem, 'user_code', $ns);		//Required
		$this->addSampleElem($this->igsnDom, $sampleElem, 'sample_type', 'Individual Sample');		//Required
		$this->addSampleElem($this->igsnDom, $sampleElem, 'material', 'Biology');		//Required
		$igsnElem = $this->igsnDom->createElement('igsn');		//If blank, SESAR will generate new IGSN
		$igsnElem->appendChild($this->igsnDom->createTextNode($igsn));
		$sampleElem->appendChild($igsnElem);


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

		if(isset($this->fieldMap['minimumElevationInMeters']['value']) && $this->fieldMap['minimumElevationInMeters']['value'] !== '') $this->addSampleElem($this->igsnDom, $sampleElem, 'elevation_unit', 'meters');
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
		$urlElem->appendChild($this->igsnDom->createTextNode($url));
		$externalUrlElem->appendChild($urlElem);
		$descriptionElem = $this->igsnDom->createElement('description');
		$descriptionElem->appendChild($this->igsnDom->createTextNode('Source Reference URL'));
		$externalUrlElem->appendChild($descriptionElem);
		$urlTypeElem = $this->igsnDom->createElement('url_type');
		$urlTypeElem->appendChild($this->igsnDom->createTextNode('regular URL'));
		$externalUrlElem->appendChild($urlTypeElem);
		$externalUrlsElem->appendChild($externalUrlElem);
		$sampleElem->appendChild($externalUrlsElem);

		$rootElem = $this->igsnDom->documentElement;
		$rootElem->appendChild($sampleElem);
	}

	private function addSampleElem(&$dom, &$sampleElem, $elemName, $elemValue){
		if($elemValue){
			$newElem = $dom->createElement($elemName);
			$newElem->appendChild($dom->createTextNode($elemValue));
			$sampleElem->appendChild($newElem);
		}
	}

	private function updateOccurrenceID($igsn, $occid){
		$status = true;
		if(strlen($igsn) == 9){
			$sql = 'UPDATE omoccurrences SET occurrenceID = "'.$igsn.'" WHERE occurrenceID IS NULL AND occid = '.$occid;
			if(!$this->conn->query($sql)){
				$this->logOrEcho('ERROR adding IGSN to occurrence table: '.$this->conn->error,2);
				$status = false;
			}
		}
		else{
			$this->logOrEcho('ERROR adding IGSN to occurrence table: IGSN ('.$igsn.') not 9 digits',2);
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
		}
	}

	public function setCollArr(){
		if($this->collid){
			$sql = 'SELECT institutionCode, collectionCode, collectionName, contact, email FROM omcollections WHERE collid = '.$this->collid;
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
		if($ns == 'NEO') $ns .= 'N';
		$this->namespace = $ns;
	}

	public function generateIgsnSeed(){
		$igsnSeed = '';
		//Get maximum identifier
		if($this->collid && $this->namespace){
			$seed = 0;
			$sql = 'SELECT MAX(occurrenceID) as maxid FROM omoccurrences WHERE occurrenceID LIKE "'.$this->namespace.'%" AND length(occurrenceID) = 9';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$seed = $r->maxid;
			}
			$rs->free();
			//Increase max alphanumeric ID by 1
			if($seed){
				$seedBaseTen = base_convert($seed,36,10);
				$igsn = base_convert($seedBaseTen+1,10,36);
				$igsn = str_pad($igsn, (9-strlen($this->namespace)), '0', STR_PAD_LEFT);
				$igsnSeed = strtoupper($igsn);
			}
			else{
				$igsnSeed = $this->namespace.str_pad('1', (9-strlen($this->namespace)), '0', STR_PAD_LEFT);
			}
		}
		return $igsnSeed;
	}

	public function setIgsnSeed($seed){
		if($seed && preg_match('/^[A-Z0-9]+$/', $seed)){
			if($this->namespace && $this->collid){
				//Test seed
				$seedIsGood = true;
				$sql = 'SELECT occid FROM omoccurrences WHERE occurrenceID >= "'.$seed.'"';
				$rs = $this->conn->query($sql);
				if($rs->num_rows) $seedIsGood = false;
				$rs->free();
				if($seedIsGood) $this->igsnSeed = $seed;
				else $this->warningArr[] = 'ERROR: Seed ('.$seed.') already exists or is out of sequence ';
			}
		}
	}

	public function getIgsnSeed(){
		return $this->igsnSeed;
	}

	public function setRegistrationMethod($method){
		$this->registrationMethod = $method;
	}

	public function setGenerateIGSN($bool){
		if($bool == 1) $this->generateIGSN = true;
	}
}
?>