<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceSesar extends Manager {

	private $collid;
	private $namespace;

	public function __construct($type = 'write'){
		parent::__construct(null, $type);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function batchAssignIdentifiers(){
		//Get maximum identifier
		$seed = 0;
		$sql = 'SELECT MAX(occurrenceID) as maxid FROM omoccurrences WHERE occurrenceID LIKE "'.$this->namespace.'%"';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()) $seed = $r->maxid;
		$rs->free();
		$seedBaseTen = base_convert($seed,36,10);

		//Batch assign GUIDs
		$increment = 1;
		$sql = 'SELECT occid FROM omoccurrences WHERE collid = '.$this->collid.' AND occurrenceid IS NULL';
		$rs = $this->conn->query($sql);
		while($rs = $rs->fetch_object()){
			$igsn = base_convert($seedBaseTen+$increment,10,36);
			$igsn = str_pad($igsn, 5, '0', STR_PAD_LEFT);
			$igsn = strtoupper($igsn);
			$igsn = $this->namespace.$igsn;
			if($this->conn('UPDATE omoccurrences SET occurrenceID = '.$igsn.' WHERE occid = '.$r->occid)){

				//Register identifier with SESAR
				$this->registerIgsn($igsn);
				$increment++;
			}
			else{
				echo 'ERROR adding IGSN to occurrence table: '.$this->conn->error;
			}
		}
		$rs->free();


	}

	// SESAR web service calls (http://www.geosamples.org/interop)
	// End point: https://app.geosamples.org/webservices/
	// Test end point: https://sesardev.geosamples.org/webservices/
	private function registerIgsn($igsn){
		$result = array();

		//$baseurl = 'https://app.geosamples.org/webservices/upload.php';
		$baseurl = 'https://sesardev.geosamples.org/webservices/upload.php';		// TEST URI
		$username = 'your_user_name';
		$password = 'your_password';
		$requestData = array ('username'=>$username, 'password'=>$password);

		/*
		 * <?xml version="1.0" encoding="UTF-8"?> <samples xmlns="http://app.geosamples.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://app.geosamples.org/4.0/sample.xsd "> <sample> <user_code>ABC</user_code> <!-- required --> <sample_type>Individual Sample</sample_type> <!-- required --> <sample_subtype>Thin Section</sample_subtype> <name>TestSample123</name> <!-- Required --> <material>Rock</material> <!-- required, --> <igsn>ABC123456</igsn> <!-- Not Required, If it is not specified, the system will create it automatically. --> <!-- If no classification.xsd is specified, the older format for classification is still supported e.g., Igneous>Plutonic>Felsic --> <classification xmlns="http://app.geosamples.org" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://app.geosamples.org/classifications.xsd"> <Rock><Igneous> <Plutonic> <PlutonicType>Felsic</PlutonicType> </Plutonic> </Igneous></Rock> </classification> <description>arkose</description> <age_min>6.5</age_min> <age_max>13</age_max> <age_unit>years</age_unit> <collection_method>Grab</collection_method> <latitude>35.5134</latitude> <longitude>-117.3463</longitude> <elevation>781.4</elevation> <primary_location_name>Lava Mountains, Mojave Desert, California </primary_location_name> <country>United States</country> <province>California</province> <county>San Bernardino</county> <collector>J. E. Andrew</collector> <collection_start_date>2002-05-30T09:30:10Z </collection_start_date> <collection_date_precision>time</collection_date_precision> <original_archive>University of Kansas</original_archive> </sample> </samples>
		 */

		$xml = '';
		$loginStr = $SESAR_USERNAME.':'.$SESAR_PASSWORD;

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_URL, $baseurl);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result['content']= curl_exec($ch);
		$result['errno']= curl_errno($ch);
		$result['errmsg'] = curl_error($ch);
		$result['header'] = curl_getinfo($ch);
		curl_close($ch);

		return $result;
	}

	private function validateUser($requestdata){
		$result = array();
		$baseurl = 'https://app.geosamples.org/ webservices/credentials_service_v2.php';
		$username = 'your_user_name';
		$password = 'your_password';
		$requestData = array ('username'=>$username, 'password'=>$password);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_URL, $baseurl);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $requestData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result['content']= curl_exec($ch);
		$result['errno']= curl_errno($ch);
		$result['errmsg'] = curl_error($ch);
		$result['header'] = curl_getinfo($ch);
		curl_close($ch);
		return $result;
	}

	private function sesarFields(){
		/*
		Sample type (Object): "Individual Sample"
		Classification (Biology): Macrobiology
		Sample Name: ? catalogNumber, otherCatalogNumbers, or recordedBy/recordNumber -- Mandatory --
		IGSN: igsn, or leave blank for SESAR to assign
		Parent IGSN: leave blank, or enter parent IGSN
		Release Date: leave null to default to current date
		Material: Biology
		Field name (informal classification): sciname
		Classification: sciname + scientificNameAuthorship
		Sample Description: verbatimAttributes
		Collection method: leave blank? or "manual"?
		Purpose: leave blank
		Latitude: decimalLatitude
		Longitude: decimalLongitude
		Elevation start: minimumElevationInMeters
		Elevation end: maximumElevationInMeters
		Elevation unit: 'meters'
		Navigation type: leave blank
		Primary physiographic feature: leave blank
		Name of physiographic feature: leave blank
		Locality: locality
		Country: country
		State/Province: stateProvince
		County: county
		Field program/cruise: leave blank
		Collector/Chief Scientist: recordedBy
		Collection date: eventDate
		Collection date precision: "day"
		Current archive: leave blank
		Current archive contact: collections main contact (name and email)
		*/
	}

	//Setters and getters
	public function setCollid($id){
		$this->collid = $id;
	}

	public function setNamespace(){


	}
}
?>