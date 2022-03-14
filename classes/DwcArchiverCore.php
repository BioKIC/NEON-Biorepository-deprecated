<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once($SERVER_ROOT.'/classes/Manager.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverOccurrence.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverDetermination.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverImage.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverAttribute.php');
include_once($SERVER_ROOT.'/classes/DwcArchiverMaterialSample.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');
include_once($SERVER_ROOT.'/classes/OccurrenceTaxaManager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAccessStats.php');

class DwcArchiverCore extends Manager{

	private $dataConn;
	private $ts;

	protected $collArr;
	private $customWhereSql;
	private $conditionSql;
 	private $conditionArr = array();
	private $condAllowArr;
	private $overrideConditionLimit = false;

	private $targetPath;
	protected $serverDomain;

	private $schemaType = 'dwc';			//dwc, symbiota, backup, coge, pensoft
	private $limitToGuids = false;			//Limit output to only records with GUIDs
	private $extended = 0;
	private $delimiter = ',';
	private $fileExt = '.csv';
	private $occurrenceFieldArr = array();
	private $determinationFieldArr = array();
	private $imageFieldArr = array();
	private $attributeFieldArr = array();
	private $fieldArrMap = array();
	private $isPublicDownload = false;

	private $securityArr = array();
	private $includeDets = 1;
	private $includeImgs = 1;
	private $includeAttributes = 0;
	private $includeMaterialSample = 0;
	private $hasPaleo = false;
	private $redactLocalities = 1;
	private $rareReaderArr = array();
	private $charSetSource = '';
	protected $charSetOut = '';

	private $projectMetadataArr = array();

	private $geolocateVariables = array();

	public function __construct($conType='readonly'){
		parent::__construct(null,$conType);
		//Ensure that PHP DOMDocument class is installed
		if(!class_exists('DOMDocument')){
			exit('FATAL ERROR: PHP DOMDocument class is not installed, please contact your server admin');
		}
		$this->ts = time();
		if($this->verboseMode){
			$logPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1)=='/'?'':'/')."content/logs/DWCA_".date('Y-m-d').".log";
			$this->setLogFH($logPath);
		}

		//Character set
		$this->charSetSource = strtoupper($GLOBALS['CHARSET']);
		$this->charSetOut = $this->charSetSource;

		$this->condAllowArr = array('catalognumber','othercatalognumbers','occurrenceid','family','sciname','country','stateprovince','county','municipality',
			'recordedby','recordnumber','eventdate','decimallatitude','decimallongitude','minimumelevationinmeters','maximumelevationinmeters','cultivationstatus',
			'datelastmodified','dateentered','processingstatus','dbpk');

		$this->securityArr = array('eventDate','month','day','startDayOfYear','endDayOfYear','verbatimEventDate',
			'recordNumber','locality','locationRemarks','minimumElevationInMeters','maximumElevationInMeters','verbatimElevation',
			'decimalLatitude','decimalLongitude','geodeticDatum','coordinateUncertaintyInMeters','footprintWKT',
			'verbatimCoordinates','georeferenceRemarks','georeferencedBy','georeferenceProtocol','georeferenceSources',
			'georeferenceVerificationStatus','habitat');

		//ini_set('memory_limit','512M');
		set_time_limit(600);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function getOccurrenceCnt(){
		$retStr = 0;
		$this->applyConditions();
		$dwcOccurManager = new DwcArchiverOccurrence($this->conn);
		$dwcOccurManager->setSchemaType($this->schemaType);
		$dwcOccurManager->setExtended($this->extended);
		//$dwcOccurManager->setIncludePaleo($this->hasPaleo);
		if(!$this->occurrenceFieldArr) $this->occurrenceFieldArr = $dwcOccurManager->getOccurrenceArr();
		$sql = $dwcOccurManager->getSqlOccurrences($this->occurrenceFieldArr['fields'],false);
		$sql .= $this->getTableJoins().$this->conditionSql;
		//if($this->schemaType != 'backup') $sql .= ' LIMIT 1000000';
		if($sql){
			$sql = 'SELECT COUNT(o.occid) as cnt '.$sql;
			//echo $sql; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retStr = $r->cnt;
			}
			$rs->free();
		}
		return $retStr;
	}

	public function setTargetPath($tp = ''){
		if($tp){
			$this->targetPath = $tp;
		}
		else{
			//Set to temp download path
			$tPath = $GLOBALS["tempDirRoot"];
			if(!$tPath){
				$tPath = ini_get('upload_tmp_dir');
			}
			if(!$tPath){
				$tPath = $GLOBALS["serverRoot"];
				if(substr($tPath,-1) != '/' && substr($tPath,-1) != '\\'){
					$tPath .= '/';
				}
				$tPath .= "temp/";
			}
			if(substr($tPath,-1) != '/' && substr($tPath,-1) != '\\'){
				$tPath .= '/';
			}
			if(file_exists($tPath."downloads")){
				$tPath .= "downloads/";
			}
			$this->targetPath = $tPath;
		}
	}

	public function setCollArr($collTarget, $collType = ''){
		$collTarget = $this->cleanInStr($collTarget);
		$collType = $this->cleanInStr($collType);
		$sqlWhere = '';
		if($collType == 'specimens'){
			$sqlWhere = '(c.colltype = "Preserved Specimens") ';
		}
		elseif($collType == 'observations'){
			$sqlWhere = '(c.colltype = "Observations" OR c.colltype = "General Observations") ';
		}
		if($collTarget){
			$this->addCondition('collid', 'EQUALS', $collTarget);
			if($collTarget != 'all') $sqlWhere .= ($sqlWhere?'AND ':'').'(c.collid IN('.$collTarget.')) ';
		}
		else{
			//Don't limit by collection id
		}
		if($sqlWhere){
			$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.fulldescription, c.collectionguid, IFNULL(c.homepage,i.url) AS url, c.contact, c.email, '.
				'c.guidtarget, c.dwcaurl, c.latitudedecimal, c.longitudedecimal, c.icon, c.managementtype, c.colltype, c.rights, c.rightsholder, c.usageterm, c.dynamicproperties, '.
				'i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country, i.phone '.
				'FROM omcollections c LEFT JOIN institutions i ON c.iid = i.iid '.
				'WHERE '.$sqlWhere;
			//echo 'SQL: '.$sql.'<br/>';
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$this->collArr[$r->collid]['instcode'] = $r->institutioncode;
					$this->collArr[$r->collid]['collcode'] = $r->collectioncode;
					$this->collArr[$r->collid]['collname'] = $r->collectionname;
					$this->collArr[$r->collid]['description'] = $r->fulldescription;
					$this->collArr[$r->collid]['collectionguid'] = $r->collectionguid;
					$this->collArr[$r->collid]['url'] = $r->url;
					$this->collArr[$r->collid]['contact'][0]['individualName']['surName'] = $r->contact;
					$this->collArr[$r->collid]['contact'][0]['electronicMailAddress'] = $r->email;
					$this->collArr[$r->collid]['guidtarget'] = $r->guidtarget;
					$this->collArr[$r->collid]['dwcaurl'] = $r->dwcaurl;
					$this->collArr[$r->collid]['lat'] = $r->latitudedecimal;
					$this->collArr[$r->collid]['lng'] = $r->longitudedecimal;
					$this->collArr[$r->collid]['icon'] = $r->icon;
					$this->collArr[$r->collid]['colltype'] = $r->colltype;
					$this->collArr[$r->collid]['managementtype'] = $r->managementtype;
					$this->collArr[$r->collid]['rights'] = $r->rights;
					$this->collArr[$r->collid]['rightsholder'] = $r->rightsholder;
					$this->collArr[$r->collid]['usageterm'] = $r->usageterm;
					$this->collArr[$r->collid]['address1'] = $r->address1;
					$this->collArr[$r->collid]['address2'] = $r->address2;
					$this->collArr[$r->collid]['city'] = $r->city;
					$this->collArr[$r->collid]['state'] = $r->stateprovince;
					$this->collArr[$r->collid]['postalcode'] = $r->postalcode;
					$this->collArr[$r->collid]['country'] = $r->country;
					$this->collArr[$r->collid]['phone'] = $r->phone;
					if($r->dynamicproperties){
						if($propArr = json_decode($r->dynamicproperties,true)){
							if(isset($propArr['editorProps']['modules-panel'])){
								foreach($propArr['editorProps']['modules-panel'] as $k => $modArr){
									if(isset($modArr['paleo']['status'])) $this->hasPaleo = true;
									elseif(isset($modArr['matSample']['status'])) $this->collArr[$r->collid]['matSample'] = 1;
								}
							}
							if(isset($propArr['publicationProps']['titleOverride']) && $propArr['publicationProps']['titleOverride']){
								$this->collArr[$r->collid]['collname'] = $propArr['publicationProps']['titleOverride'];
							}
							if(isset($propArr['publicationProps']['project']) && $propArr['publicationProps']['project']){
								$this->projectMetadataArr = $propArr['publicationProps']['project'];
							}
						}
					}
				}
				$rs->free();
				$this->setJsonResources();
			}
		}
	}

	private function setJsonResources(){
		//Temporary function needed until pending patch is pushed to production
		$sql = 'SELECT collid, resourceJson, contactJson FROM omcollections WHERE collid IN('.implode(',',array_keys($this->collArr)).')';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				if($r->resourceJson){
					if($resourceArr = json_decode($r->resourceJson,true)){
						$this->collArr[$r->collid]['url'] = $resourceArr[0]['url'];
					}
				}
				if($r->contactJson){
					if($contactArr = json_decode($r->contactJson,true)){
						foreach($contactArr as $key => $cArr){
							$this->collArr[$r->collid]['contact'][$key]['individualName']['surName'] = $cArr['lastName'];
							$this->collArr[$r->collid]['contact'][$key]['individualName']['givenName'] = $cArr['firstName'];
							if(isset($cArr['role']) && $cArr['role']) $this->collArr[$r->collid]['contact'][$key]['positionName'] = $cArr['role'];
							if(isset($cArr['email']) && $cArr['email']) $this->collArr[$r->collid]['contact'][$key]['electronicMailAddress'] = $cArr['email'];
							if(isset($cArr['orcid']) && $cArr['orcid']) $this->collArr[$r->collid]['contact'][$key]['userId'] = 'https://orcid.org/'.$cArr['orcid'];
						}
					}
				}
			}
			$rs->free();
		}
	}

	public function getCollArr($id = 0){
		if($id && isset($this->collArr[$id])) return $this->collArr[$id];
		return $this->collArr;
	}

	public function setCustomWhereSql($sql){
		$this->customWhereSql = $sql;
	}

	public function addCondition($field, $cond, $value = ''){
		$cond = strtoupper(trim($cond));
		if(!preg_match('/^[A-Za-z]+$/',$field)) return false;
		if(!preg_match('/^[A-Z]+$/',$cond)) return false;
		if($field){
			if($this->overrideConditionLimit || in_array(strtolower($field),$this->condAllowArr)){
				if(!$cond) $cond = 'EQUALS';
				if($value != '' || ($cond == 'NULL' || $cond == 'NOTNULL')){
					if(is_array($value)) $this->conditionArr[$field][$cond] = $this->cleanInArray($value);
					else $this->conditionArr[$field][$cond][] = $this->cleanInStr($value);
				}
			}
		}
	}

	private function applyConditions(){
		$this->conditionSql = '';
		if($this->customWhereSql){
			$this->conditionSql = $this->customWhereSql.' ';
		}
		if(array_key_exists('collid', $this->conditionArr) && $this->conditionArr['collid']){
			if($this->conditionArr['collid']['EQUALS'][0] != 'all'){
				$this->conditionSql .= 'AND (o.collid IN('.$this->conditionArr['collid']['EQUALS'][0].')) ';
			}
			unset($this->conditionArr['collid']);
		}
		else{
			if($this->collArr && (!$this->conditionSql || !stripos($this->conditionSql,'collid in('))){
				$this->conditionSql .= 'AND (o.collid IN('.implode(',',array_keys($this->collArr)).')) ';
			}
		}
		$sqlFrag = '';
		if($this->conditionArr){
			foreach($this->conditionArr as $field => $condArr){
				if($field == 'stateid'){
					$sqlFrag .= 'AND (a.stateid IN('.implode(',',$condArr['EQUALS']).')) ';
				}
				elseif($field == 'traitid'){
					$sqlFrag .= 'AND (s.traitid IN('.implode(',',$condArr['EQUALS']).')) ';
				}
				elseif($field == 'clid'){
					$sqlFrag .= 'AND (v.clid IN('.implode(',',$condArr['EQUALS']).')) ';
				}
				elseif(($field == 'sciname' || $field == 'family') && isset($condArr['EQUALS'])){
					$taxaManager = new OccurrenceTaxaManager();
					$taxaArr = array();
					$taxaArr['taxa'] = implode(';',$condArr['EQUALS']);
					$taxaManager->setTaxonRequestVariable($taxaArr);
					$sqlFrag .= $taxaManager->getTaxonWhereFrag();
				}
				elseif($field == 'cultivationstatus'){
					if(current(current($condArr)) === '0') $sqlFrag .= 'AND (o.cultivationStatus = 0 OR o.cultivationStatus IS NULL) ';
					else $sqlFrag .= 'AND (o.cultivationStatus = 1) ';
				}
				else{
					if($field == 'datelastmodified') $field = 'IFNULL(o.modified,o.datelastmodified)';
					else $field = 'o.'.$field;
					$sqlFrag2 = '';
					foreach($condArr as $cond => $valueArr){
						if($field == 'o.otherCatalogNumbers'){
							$conj = 'OR';
							if($cond == 'NOTEQUALS' || $cond == 'NOTLIKE' || $cond == 'NULL') $conj = 'AND';
							$sqlFrag2 .= 'AND ('.substr($this->getSqlFragment($field, $cond, $valueArr),3).' ';
							$sqlFrag2 .= $conj.' ('.substr($this->getSqlFragment('id.identifierValue', $cond, $valueArr),3);
							if($cond == 'NOTEQUALS' || $cond == 'NOTLIKE') $sqlFrag2 .= ' OR id.identifierValue IS NULL';
							$sqlFrag2 .= ')) ';
						}
						else{
							$sqlFrag2 = $this->getSqlFragment($field, $cond, $valueArr);
						}
					}
					if($sqlFrag2) $sqlFrag .= 'AND ('.substr($sqlFrag2,4).') ';
				}
			}
		}
		if($sqlFrag){
			$this->conditionSql .= $sqlFrag;
		}
		if($this->conditionSql){
			//Make sure it starts with WHERE
			if(substr($this->conditionSql,0,4) == 'AND '){
				$this->conditionSql = 'WHERE'.substr($this->conditionSql,3);
			}
			elseif(substr($this->conditionSql,0,6) != 'WHERE '){
				$this->conditionSql = 'WHERE '.$this->conditionSql;
			}
		}
	}

	private function getSqlFragment($field, $cond, $valueArr){
		$sql = '';
		if($cond == 'NULL'){
			$sql .= 'AND ('.$field.' IS NULL) ';
		}
		elseif($cond == 'NOTNULL'){
			$sql .= 'AND ('.$field.' IS NOT NULL) ';
		}
		elseif($cond == 'EQUALS'){
			$sql .= 'AND ('.$field.' IN("'.implode('","',$valueArr).'")) ';
		}
		elseif($cond == 'NOTEQUALS'){
			$sql .= 'AND ('.$field.' NOT IN("'.implode('","',$valueArr).'") OR '.$field.' IS NULL) ';
		}
		else{
			$sqlFrag = '';
			foreach($valueArr as $value){
				if($cond == 'STARTS'){
					$sqlFrag .= 'OR ('.$field.' LIKE "'.$value.'%") ';
				}
				elseif($cond == 'LIKE'){
					$sqlFrag .= 'OR ('.$field.' LIKE "%'.$value.'%") ';
				}
				elseif($cond == 'NOTLIKE'){
					$sqlFrag .= 'OR ('.$field.' NOT LIKE "%'.$value.'%" OR '.$field.' IS NULL) ';
				}
				elseif($cond == 'LESSTHAN'){
					$sqlFrag .= 'OR ('.$field.' < "'.$value.'") ';
				}
				elseif($cond == 'GREATERTHAN'){
					$sqlFrag .= 'OR ('.$field.' > "'.$value.'") ';
				}
			}
			$sql .= 'AND ('.substr($sqlFrag,3).') ';
		}
		return $sql;
	}

	private function getTableJoins(){
		$sql = '';
		if($this->conditionSql){
			if(stripos($this->conditionSql,'ts.')){
				$sql = 'LEFT JOIN taxstatus ts ON o.tidinterpreted = ts.tid ';
			}
			if(stripos($this->conditionSql,'e.parenttid')){
				$sql .= 'LEFT JOIN taxaenumtree e ON o.tidinterpreted = e.tid ';
			}
			if(stripos($this->conditionSql,'v.clid')){
				//Search criteria came from custom search page
				$sql .= 'LEFT JOIN fmvouchers v ON o.occid = v.occid ';
			}
			if(stripos($this->conditionSql,'d.datasetid')){
				$sql .= 'INNER JOIN omoccurdatasetlink d ON o.occid = d.occid ';
			}
			if(stripos($this->conditionSql,'p.point')){
				//Search criteria came from map search page
				$sql .= 'LEFT JOIN omoccurpoints p ON o.occid = p.occid ';
			}
			if(strpos($this->conditionSql,'MATCH(f.recordedby)') || strpos($this->conditionSql,'MATCH(f.locality)')){
				$sql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
			}
			if(stripos($this->conditionSql,'a.stateid')){
				//Search is limited by occurrence attribute
				$sql .= 'INNER JOIN tmattributes a ON o.occid = a.occid ';
			}
			elseif(stripos($this->conditionSql,'s.traitid')){
				//Search is limited by occurrence trait
				$sql .= 'INNER JOIN tmattributes a ON o.occid = a.occid INNER JOIN tmstates s ON a.stateid = s.stateid ';
			}
			if(strpos($this->conditionSql,'id.identifierValue')){
				$sql .= 'LEFT JOIN omoccuridentifiers id ON o.occid = id.occid ';
			}
		}
		return $sql;
	}

    public function getAsJson() {
        $this->schemaType='dwc';
        $arr = $this->getDwcArray();
        return json_encode($arr[0]);
    }

    /**
     * Render the records as RDF in a turtle serialization following the TDWG
     *  DarwinCore RDF Guide.
     *
     * @return string containing turtle serialization of selected dwc records.
     */
    public function getAsTurtle() {
       $debug = false;
       $returnvalue  = "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .\n";
       $returnvalue .= "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .\n";
       $returnvalue .= "@prefix owl: <http://www.w3.org/2002/07/owl#> .\n";
       $returnvalue .= "@prefix foaf: <http://xmlns.com/foaf/0.1/> .\n";
       $returnvalue .= "@prefix dwc: <http://rs.tdwg.org/dwc/terms/> .\n";
       $returnvalue .= "@prefix dwciri: <http://rs.tdwg.org/dwc/iri/> .\n";
       $returnvalue .= "@prefix dc: <http://purl.org/dc/elements/1.1/> . \n";
       $returnvalue .= "@prefix dcterms: <http://purl.org/dc/terms/> . \n";
       $returnvalue .= "@prefix dcmitype: <http://purl.org/dc/dcmitype/> . \n";
       $this->schemaType='dwc';
       $arr = $this->getDwcArray();
	   $occurTermArr = $this->occurrenceFieldArr['terms'];
       $dwcguide223 = "";
       foreach ($arr as $dwcArray)  {
          if ($debug) { print_r($dwcArray);  }
          if (isset($dwcArray['occurrenceID'])||(isset($dwcArray['catalogNumber']) && isset($dwcArray['collectionCode']))) {
             $occurrenceid = $dwcArray['occurrenceID'];
             if (UuidFactory::is_valid($occurrenceid)) {
                $occurrenceid = "urn:uuid:$occurrenceid";
             } else {
                $catalogNumber = $dwcArray['catalogNumber'];
                if (strlen($occurrenceid)==0 || $occurrenceid==$catalogNumber) {
                    // If no occurrenceID is present, construct one with a urn:catalog: scheme.
                    // Pathology may also exist of an occurrenceID equal to the catalog number, fix this.
                    $institutionCode = $dwcArray['institutionCode'];
                    $collectionCode = $dwcArray['collectionCode'];
                    $occurrenceid = "urn:catalog:$institutionCode:$collectionCode:$catalogNumber";
                }
             }
             $returnvalue .= "<$occurrenceid>\n";
             $returnvalue .= "    a dwc:Occurrence ";
             $separator = " ; \n ";
             foreach($dwcArray as $key => $value) {
                if (strlen($value)>0) {
                  switch ($key) {
                    case "recordID":
                    case "occurrenceID":
                    case "verbatimScientificName":
                         // skip
                      break;
                    case "collectionID":
                         // RDF Guide Section 2.3.3 owl:sameAs for urn:lsid and resolvable IRI.
                         if (stripos("urn:uuid:",$value)===false && UuidFactory::is_valid($value)) {
                           $lsid = "urn:uuid:$value";
                         } elseif (stripos("urn:lsid:biocol.org",$value)===0) {
                           $lsid = "http://biocol.org/$value";
                           $dwcguide223 .= "<http://biocol.org/$value>\n";
                           $dwcguide223 .= "    owl:sameAs <$value> .\n";
                         } else {
                           $lsid = $value;
                         }
                         $returnvalue .= "$separator   dwciri:inCollection <$lsid>";
                      break;
                    case "basisOfRecord":
                          if (preg_match("/(PreservedSpecimen|FossilSpecimen)/",$value)==1) {
                             $returnvalue .= "$separator   a dcmitype:PhysicalObject";
                          }
                          $returnvalue .= "$separator   dwc:$key  \"$value\"";
                      break;
                    case "modified":
                         $returnvalue .= "$separator   dcterms:$key \"$value\"";
                      break;
                    case "rights":
                          // RDF Guide Section 3.3 dcterms:licence for IRI, xmpRights:UsageTerms for literal
                          if (stripos("http://creativecommons.org/licenses/",$value)==0) {
                             $returnvalue .= "$separator   dcterms:license <$value>";
                          } else {
                             $returnvalue .= "$separator   dc:$key \"$value\"";
                          }
                      break;
                    case "rightsHolder":
                          // RDF Guide Section 3.3  dcterms:rightsHolder for IRI, xmpRights:Owner for literal
                          if (stripos("http://",$value)==0 || stripos("urn:",$value)==0) {
                             $returnvalue .= "$separator   dcterms:rightsHolder <$value>";
                          } else {
                             $returnvalue .= "$separator   xmpRights:Owner \"$value\"";
                          }
                      break;
                    case "day":
                    case "month":
                    case "year":
                         if ($value!="0") {
                           $returnvalue .= "$separator   dwc:$key  \"$value\"";
                         }
                      break;
                    case "eventDate":
                         if ($value!="0000-00-00" && strlen($value)>0) {
                           $value = str_replace("-00","",$value);
                           $returnvalue .= "$separator   dwc:$key  \"$value\"";
                         }
                      break;
                    default:
                        if (isset($occurTermArr[$key])) {
                           $ns = RdfUtility::namespaceAbbrev($occurTermArr[$key]);
                           $returnvalue .= $separator . "   " . $ns . " \"$value\"";
                        }
                  }
                }
             }

             $returnvalue .= ".\n";
          }
       }
       if ($dwcguide223!="") {
          $returnvalue .= $dwcguide223;
       }
       return $returnvalue;
    }

    /**
     * Render the records as RDF in a rdf/xml serialization following the TDWG
     *  DarwinCore RDF Guide.
     *
     * @return string containing rdf/xml serialization of selected dwc records.
     */
    public function getAsRdfXml() {
       $debug = false;
	   $newDoc = new DOMDocument('1.0',$this->charSetOut);
       $newDoc->formatOutput = true;

       $rootElem = $newDoc->createElement('rdf:RDF');
       $rootElem->setAttribute('xmlns:rdf','http://www.w3.org/1999/02/22-rdf-syntax-ns#');
       $rootElem->setAttribute('xmlns:rdfs','http://www.w3.org/2000/01/rdf-schema#');
       $rootElem->setAttribute('xmlns:owl','http://www.w3.org/2002/07/owl#');
       $rootElem->setAttribute('xmlns:foaf','http://xmlns.com/foaf/0.1/');
       $rootElem->setAttribute('xmlns:dwc','http://rs.tdwg.org/dwc/terms/');
       $rootElem->setAttribute('xmlns:dwciri','http://rs.tdwg.org/dwc/iri/');
       $rootElem->setAttribute('xmlns:dc','http://purl.org/dc/elements/1.1/');
       $rootElem->setAttribute('xmlns:dcterms','http://purl.org/dc/terms/');
       $rootElem->setAttribute('xmlns:dcmitype','http://purl.org/dc/dcmitype/');
       $newDoc->appendChild($rootElem);

       $this->schemaType='dwc';
       $arr = $this->getDwcArray();
	   $occurTermArr = $this->occurrenceFieldArr['terms'];
       foreach ($arr as $dwcArray)  {
          if ($debug) { print_r($dwcArray);  }
          if (isset($dwcArray['occurrenceID'])||(isset($dwcArray['catalogNumber']) && isset($dwcArray['collectionCode']))) {
             $occurrenceid = $dwcArray['occurrenceID'];
             if (UuidFactory::is_valid($occurrenceid)) {
                $occurrenceid = "urn:uuid:$occurrenceid";
             } else {
                $catalogNumber = $dwcArray['catalogNumber'];
                if (strlen($occurrenceid)==0 || $occurrenceid==$catalogNumber) {
                    // If no occurrenceID is present, construct one with a urn:catalog: scheme.
                    // Pathology may also exist of an occurrenceID equal to the catalog number, fix this.
                    $institutionCode = $dwcArray['institutionCode'];
                    $collectionCode = $dwcArray['collectionCode'];
                    $occurrenceid = "urn:catalog:$institutionCode:$collectionCode:$catalogNumber";
                }
             }
             $occElem = $newDoc->createElement('dwc:Occurrence');
             $occElem->setAttribute("rdf:about","$occurrenceid");
             $sameAsElem = null;
             foreach($dwcArray as $key => $value) {
                $flags = ENT_NOQUOTES;
                if(defined('ENT_XML1')) $flags = ENT_NOQUOTES | ENT_XML1 | ENT_DISALLOWED;
                $value = htmlentities($value,$flags,$this->charSetOut);
                // TODO: Figure out how to use mb_encode_numericentity() here.
                $value = str_replace("&copy;","&#169;",$value);  // workaround, need to fix &copy; rendering
                if (strlen($value)>0) {
                  $elem = null;
                  switch ($key) {
                    case "recordID":
                    case "occurrenceID":
                    case "verbatimScientificName":
                         // skip
                      break;
                    case "collectionID":
                         // RDF Guide Section 2.3.3 owl:sameAs for urn:lsid and resolvable IRI.
                         if (stripos("urn:uuid:",$value)===false && UuidFactory::is_valid($value)) {
                           $lsid = "urn:uuid:$value";
                         }elseif (stripos("urn:lsid:biocol.org",$value)===0) {
                           $lsid = "http://biocol.org/$value";
                           $sameAsElem = $newDoc->createElement("rdf:Description");
                           $sameAsElem->setAttribute("rdf:about","http://biocol.org/$value");
                           $sameAsElemC = $newDoc->createElement("owl:sameAs");
                           $sameAsElemC->setAttribute("rdf:resource","$value");
                           $sameAsElem->appendChild($sameAsElemC);
                         } else {
                           $lsid = $value;
                         }
                         $elem = $newDoc->createElement("dwciri:inCollection");
                         $elem->setAttribute("rdf:resource","$lsid");
                      break;
                    case "basisOfRecord":
                          if (preg_match("/(PreservedSpecimen|FossilSpecimen)/",$value)==1) {
                             $elem = $newDoc->createElement("rdf:type");
                             $elem->setAttribute("rdf:resource","http://purl.org/dc/dcmitype/PhysicalObject");
                          }
                          $elem = $newDoc->createElement("dwc:$key",$value);
                      break;
                    case "rights":
                          // RDF Guide Section 3.3 dcterms:licence for IRI, xmpRights:UsageTerms for literal
                          if (stripos("http://creativecommons.org/licenses/",$value)==0) {
                             $elem = $newDoc->createElement("dcterms:license");
                             $elem->setAttribute("rdf:resource","$value");
                          } else {
                             $elem = $newDoc->createElement("xmpRights:UsageTerms",$value);
                          }
                      break;
                    case "rightsHolder":
                          // RDF Guide Section 3.3  dcterms:rightsHolder for IRI, xmpRights:Owner for literal
                          if (stripos("http://",$value)==0 || stripos("urn:",$value)==0) {
                             $elem = $newDoc->createElement("dcterms:rightsHolder");
                             $elem->setAttribute("rdf:resource","$value");
                          } else {
                             $elem = $newDoc->createElement("xmpRights:Owner",$value);
                          }
                      break;
                    case "modified":
                          $elem = $newDoc->createElement("dcterms:$key",$value);
                      break;
                    case "day":
                    case "month":
                    case "year":
                         if ($value!="0") {
                            $elem = $newDoc->createElement("dwc:$key",$value);
                         }
                      break;
                    case "eventDate":
                         if ($value!="0000-00-00" || strlen($value)>0) {
                           $value = str_replace("-00","",$value);
                           $elem = $newDoc->createElement("dwc:$key",$value);
                         }
                      break;
                    default:
                         if (isset($occurTermArr[$key])) {
                            $ns = RdfUtility::namespaceAbbrev($occurTermArr[$key]);
                            $elem = $newDoc->createElement($ns);
                            $elem->appendChild($newDoc->createTextNode($value));
                         }
                  }
                  if ($elem!=null) {
                     $occElem->appendChild($elem);
                  }
                }
             }
             $node = $newDoc->importNode($occElem);
             $newDoc->documentElement->appendChild($node);
             if ($sameAsElem!=null) {
                $node = $newDoc->importNode($sameAsElem);
                $newDoc->documentElement->appendChild($node);
             }
             // For many matching rows this is a point where partial serialization could occur
             // to prevent creation of a large DOM model in memmory.
          }
       }
       $returnvalue = $newDoc->saveXML();
       return $returnvalue;
    }

    public function getDwcArray() {
		$retArr = Array();
		$dwcOccurManager = new DwcArchiverOccurrence($this->conn);
		$dwcOccurManager->setSchemaType($this->schemaType);
		$dwcOccurManager->setExtended($this->extended);
		$dwcOccurManager->setIncludePaleo($this->hasPaleo);
		if(!$this->occurrenceFieldArr) $this->occurrenceFieldArr = $dwcOccurManager->getOccurrenceArr($this->schemaType, $this->extended);
		$this->applyConditions();
		$sql = $dwcOccurManager->getSqlOccurrences($this->occurrenceFieldArr['fields']);
		$sql .= $this->getTableJoins().$this->conditionSql;
		if(!$sql) return false;
		$sql .= ' LIMIT 1000000';
		$fieldArr = $this->occurrenceFieldArr['fields'];
		if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft'){
			unset($fieldArr['localitySecurity']);
		}
		if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft' || $this->schemaType == 'backup'){
			unset($fieldArr['collID']);
		}
		if(!$this->collArr){
			//Collection array not previously primed by source
			$sql1 = 'SELECT DISTINCT o.collid FROM omoccurrences o ';
			if($this->conditionSql){
				$sql1 .= $this->getTableJoins().$this->conditionSql;
			}
			$rs1 = $this->conn->query($sql1);
			$collidStr = '';
			while($r1 = $rs1->fetch_object()){
				$collidStr .= ','.$r1->collid;
			}
			$rs1->free();
			if($collidStr) $this->setCollArr(trim($collidStr,','));
		}

		$dwcOccurManager->setUpperTaxonomy();
		$dwcOccurManager->setTaxonRank();
		if(!$this->dataConn) $this->dataConn = MySQLiConnectionFactory::getCon('readonly');
		if($rs = $this->dataConn->query($sql,MYSQLI_USE_RESULT)){
			$typeArr = null;
			if($this->schemaType == 'pensoft'){
				$typeArr = array('Other material', 'Holotype', 'Paratype', 'Isotype', 'Isoparatype', 'Isolectotype', 'Isoneotype', 'Isosyntype');
				//$typeArr = array('Other material', 'Holotype', 'Paratype', 'Hapantotype', 'Syntype', 'Isotype', 'Neotype', 'Lectotype', 'Paralectotype', 'Isoparatype', 'Isolectotype', 'Isoneotype', 'Isosyntype');
			}
			$this->setServerDomain();
			$urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');
			$cnt = 0;
			while($r = $rs->fetch_assoc()){
				//Protect sensitive records
				if($this->redactLocalities && $r["localitySecurity"] == 1 && !in_array($r['collid'],$this->rareReaderArr)){
					$protectedFields = array();
					foreach($this->securityArr as $v){
						if(array_key_exists($v,$r) && $r[$v]){
							$r[$v] = '';
							$protectedFields[] = $v;
						}
					}
					if($protectedFields){
						$r['informationWithheld'] = trim($r['informationWithheld'].'; field values redacted: '.implode(', ',$protectedFields),' ;');
					}
				}
				if(!$r['occurrenceID']){
					//Set occurrence GUID based on GUID target, but only if occurrenceID field isn't already populated
					$guidTarget = $this->collArr[$r['collid']]['guidtarget'];
					if($guidTarget == 'catalogNumber'){
						$r['occurrenceID'] = $r['catalogNumber'];
					}
					elseif($guidTarget == 'symbiotaUUID'){
						$r['occurrenceID'] = $r['recordID'];
					}
				}

				$r['recordID'] = 'urn:uuid:'.$r['recordID'];
				//Add collection GUID based on management type
				$managementType = $this->collArr[$r['collid']]['managementtype'];
				if($managementType && $managementType == 'Live Data'){
					if(array_key_exists('collectionID',$r) && !$r['collectionID']){
						$guid = $this->collArr[$r['collid']]['collectionguid'];
						if(strlen($guid) == 36) $guid = 'urn:uuid:'.$guid;
						$r['collectionID'] = $guid;
					}
				}
				if($this->schemaType == 'dwc'){
					unset($r['localitySecurity']);
				}
				if($this->schemaType == 'dwc' || $this->schemaType == 'backup'){
					unset($r['collid']);
				}
				if($this->schemaType == 'pensoft'){
					unset($r['localitySecurity']);
					unset($r['collid']);
					if($r['typeStatus']){
						$typeValue = strtolower($r['typeStatus']);
						$typeInvalid = true;
						$invalidText = '';
						foreach($typeArr as $testStr){
							if($typeValue == strtolower($testStr)){
								$typeInvalid = false;
								break;
							}
							elseif(stripos($typeValue, $testStr)){
								$invalidText = $r['typeStatus'];
								$r['typeStatus'] = $testStr;
								$typeInvalid = false;
								break;
							}
						}
						if($typeInvalid){
							$invalidText = $r['typeStatus'];
							$r['typeStatus'] = 'Other material';
						}
						if($invalidText){
							if($r['occurrenceRemarks']) $invalidText = $r['occurrenceRemarks'].'; '.$invalidText;
							$r['occurrenceRemarks'] = $invalidText;
						}
					}
					else{
						$r['typeStatus'] = 'Other material';
					}
				}

				$dwcOccurManager->appendUpperTaxonomy($r);
				if($rankStr = $dwcOccurManager->getTaxonRank($r['rankid'])) $r['t_taxonRank'] = $rankStr;
				unset($r['rankid']);

				if($urlPathPrefix) $r['t_references'] = $urlPathPrefix.'collections/individual/index.php?occid='.$r['occid'];

				foreach($r as $rKey => $rValue){
					if(substr($rKey, 0, 2) == 't_') $rKey = substr($rKey,2);
					$retArr[$cnt][$rKey] = $rValue;
				}
				$cnt++;
			}
			$rs->free();
			//$retArr[0]['associatedMedia'] = $this->getAssociatedMedia();
		}
		else{
			$this->logOrEcho("ERROR creating occurrence file: ".$this->dataConn->error."\n");
			$this->logOrEcho("\tSQL: ".$sql."\n");
		}
		$this->dataConn->close();
		return $retArr;
    }

    private function getAssociatedMedia(){
    	$retStr = '';
    	$sql = 'SELECT originalurl FROM images '.str_replace('o.','',$this->conditionSql);
    	$rs = $this->conn->query($sql);
    	while($r = $rs->fetch_object()){
    		$retStr .= ';'.$r->originalurl;
    	}
    	$rs->free();
    	return trim($retStr,';');
    }

	public function createDwcArchive($fileNameSeed = ''){
		$status = false;
		if(!$fileNameSeed){
			if($this->collArr && count($this->collArr) == 1){
				$firstColl = current($this->collArr);
				if($firstColl){
					$fileNameSeed = $firstColl['instcode'];
					if($firstColl['collcode']) $fileNameSeed .= '-'.$firstColl['collcode'];
				}
				if($this->schemaType == 'backup'){
					$fileNameSeed .= '_backup_'.date('Y-m-d_His',$this->ts);
				}
			}
			else{
				$fileNameSeed = 'SymbOutput_'.date('Y-m-d_His',$this->ts);
			}
		}
		$fileName = str_replace(array(' ','"',"'"),'',$fileNameSeed).'_DwC-A.zip';

		if(!$this->targetPath) $this->setTargetPath();
		$archiveFile = '';
		$this->logOrEcho('Creating DwC-A file: '.$fileName."\n");

		if(!class_exists('ZipArchive')){
			$this->logOrEcho("FATAL ERROR: PHP ZipArchive class is not installed, please contact your server admin\n");
			exit('FATAL ERROR: PHP ZipArchive class is not installed, please contact your server admin');
		}
		$this->dataConn = MySQLiConnectionFactory::getCon('readonly');
		$status = $this->writeOccurrenceFile();
		if($status){
			$archiveFile = $this->targetPath.$fileName;
			if(file_exists($archiveFile)) unlink($archiveFile);
			$zipArchive = new ZipArchive;
			$status = $zipArchive->open($archiveFile, ZipArchive::CREATE);
			if($status !== true){
				exit('FATAL ERROR: unable to create archive file: '.$status);
			}
			//$this->logOrEcho("DWCA created: ".$archiveFile."\n");

			$zipArchive->addFile($this->targetPath.$this->ts.'-occur'.$this->fileExt);
			$zipArchive->renameName($this->targetPath.$this->ts.'-occur'.$this->fileExt,'occurrences'.$this->fileExt);
			if($this->includeDets) {
				$this->writeDeterminationFile();
				$zipArchive->addFile($this->targetPath.$this->ts.'-det'.$this->fileExt);
				$zipArchive->renameName($this->targetPath.$this->ts.'-det'.$this->fileExt,'identifications'.$this->fileExt);
			}
			if($this->includeImgs){
				$this->writeImageFile();
				$zipArchive->addFile($this->targetPath.$this->ts.'-multimedia'.$this->fileExt);
				$zipArchive->renameName($this->targetPath.$this->ts.'-multimedia'.$this->fileExt,'multimedia'.$this->fileExt);
			}
			if($this->includeAttributes){
				$this->writeAttributeFile();
				$zipArchive->addFile($this->targetPath.$this->ts.'-attr'.$this->fileExt);
				$zipArchive->renameName($this->targetPath.$this->ts.'-attr'.$this->fileExt,'measurementOrFact'.$this->fileExt);
			}
			if($this->includeMaterialSample && file_exists($this->targetPath.$this->ts.'-matSample'.$this->fileExt)){
				$zipArchive->addFile($this->targetPath.$this->ts.'-matSample'.$this->fileExt);
				$zipArchive->renameName($this->targetPath.$this->ts.'-matSample'.$this->fileExt,'materialSample'.$this->fileExt);
			}
			//Meta file
			$this->writeMetaFile();
			$zipArchive->addFile($this->targetPath.$this->ts.'-meta.xml');
    		$zipArchive->renameName($this->targetPath.$this->ts.'-meta.xml','meta.xml');
			//EML file
			$this->writeEmlFile();
			$zipArchive->addFile($this->targetPath.$this->ts.'-eml.xml');
    		$zipArchive->renameName($this->targetPath.$this->ts.'-eml.xml','eml.xml');

			$zipArchive->close();
			unlink($this->targetPath.$this->ts.'-occur'.$this->fileExt);
			if($this->includeDets) unlink($this->targetPath.$this->ts.'-det'.$this->fileExt);
			if($this->includeImgs) unlink($this->targetPath.$this->ts.'-multimedia'.$this->fileExt);
			if($this->includeAttributes) unlink($this->targetPath.$this->ts.'-attr'.$this->fileExt);
			if($this->includeMaterialSample && file_exists($this->targetPath.$this->ts.'-matSample'.$this->fileExt)) unlink($this->targetPath.$this->ts.'-matSample'.$this->fileExt);
			unlink($this->targetPath.$this->ts.'-meta.xml');
			if($this->schemaType == 'dwc') rename($this->targetPath.$this->ts.'-eml.xml',$this->targetPath.str_replace('.zip','.eml',$fileName));
			else unlink($this->targetPath.$this->ts.'-eml.xml');
		}
		else{
			$this->errorMessage = 'FAILED to create archive file due to failure to return occurrence records; check and adjust search variables';
			$this->logOrEcho($this->errorMessage);
			$collid = key($this->collArr);
			if($collid) $this->deleteArchive($collid);
			unset($this->collArr[$collid]);
		}
		$this->logOrEcho("\n-----------------------------------------------------\n");
		$this->dataConn->close();
		return $archiveFile;
	}

	//Generate DwC support files
	private function writeMetaFile(){
		$this->logOrEcho("Creating meta.xml (".date('h:i:s A').")... ");

		//Create new DOM document
		$newDoc = new DOMDocument('1.0',$this->charSetOut);

		//Add root element
		$rootElem = $newDoc->createElement('archive');
		$rootElem->setAttribute('metadata','eml.xml');
		$rootElem->setAttribute('xmlns','http://rs.tdwg.org/dwc/text/');
		$rootElem->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$rootElem->setAttribute('xsi:schemaLocation','http://rs.tdwg.org/dwc/text/   http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd');
		$newDoc->appendChild($rootElem);

		//Core file definition
		$coreElem = $newDoc->createElement('core');
		$coreElem->setAttribute('dateFormat','YYYY-MM-DD');
		$coreElem->setAttribute('encoding',$this->charSetOut);
		$coreElem->setAttribute('fieldsTerminatedBy',$this->delimiter);
		$coreElem->setAttribute('linesTerminatedBy','\n');
		$coreElem->setAttribute('fieldsEnclosedBy','"');
		$coreElem->setAttribute('ignoreHeaderLines','1');
		$coreElem->setAttribute('rowType','http://rs.tdwg.org/dwc/terms/Occurrence');

		$filesElem = $newDoc->createElement('files');
		$filesElem->appendChild($newDoc->createElement('location','occurrences'.$this->fileExt));
		$coreElem->appendChild($filesElem);

		$idElem = $newDoc->createElement('id');
		$idElem->setAttribute('index','0');
		$coreElem->appendChild($idElem);

		$occCnt = 1;
		$termArr = $this->occurrenceFieldArr['terms'];
		if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft'){
			unset($termArr['localitySecurity']);
		}
		if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft' || $this->schemaType == 'backup'){
			unset($termArr['collID']);
		}
		foreach($termArr as $v){
			$fieldElem = $newDoc->createElement('field');
			$fieldElem->setAttribute('index',$occCnt);
			$fieldElem->setAttribute('term',$v);
			$coreElem->appendChild($fieldElem);
			$occCnt++;
		}
		$rootElem->appendChild($coreElem);

		//Identification extension
		if($this->includeDets){
			$extElem1 = $newDoc->createElement('extension');
			$extElem1->setAttribute('encoding',$this->charSetOut);
			$extElem1->setAttribute('fieldsTerminatedBy',$this->delimiter);
			$extElem1->setAttribute('linesTerminatedBy','\n');
			$extElem1->setAttribute('fieldsEnclosedBy','"');
			$extElem1->setAttribute('ignoreHeaderLines','1');
			$extElem1->setAttribute('rowType','http://rs.tdwg.org/dwc/terms/Identification');

			$filesElem1 = $newDoc->createElement('files');
			$filesElem1->appendChild($newDoc->createElement('location','identifications'.$this->fileExt));
			$extElem1->appendChild($filesElem1);

			$coreIdElem1 = $newDoc->createElement('coreid');
			$coreIdElem1->setAttribute('index','0');
			$extElem1->appendChild($coreIdElem1);


			//List identification fields
			$detCnt = 1;
			$termArr = $this->determinationFieldArr['terms'];
			unset($termArr['detID']);
			foreach($termArr as $v){
				$fieldElem = $newDoc->createElement('field');
				$fieldElem->setAttribute('index',$detCnt);
				$fieldElem->setAttribute('term',$v);
				$extElem1->appendChild($fieldElem);
				$detCnt++;
			}
			$rootElem->appendChild($extElem1);
		}

		//Image extension
		if($this->includeImgs){
			$extElem2 = $newDoc->createElement('extension');
			$extElem2->setAttribute('encoding',$this->charSetOut);
			$extElem2->setAttribute('fieldsTerminatedBy',$this->delimiter);
			$extElem2->setAttribute('linesTerminatedBy','\n');
			$extElem2->setAttribute('fieldsEnclosedBy','"');
			$extElem2->setAttribute('ignoreHeaderLines','1');
			$extElem2->setAttribute('rowType','http://rs.tdwg.org/ac/terms/Multimedia');

			$filesElem2 = $newDoc->createElement('files');
			$filesElem2->appendChild($newDoc->createElement('location','multimedia'.$this->fileExt));
			$extElem2->appendChild($filesElem2);

			$coreIdElem2 = $newDoc->createElement('coreid');
			$coreIdElem2->setAttribute('index','0');
			$extElem2->appendChild($coreIdElem2);

			//List image fields
			$imgCnt = 1;
			$termArr = $this->imageFieldArr['terms'];
			unset($termArr['imgID']);
			foreach($termArr as $v){
				$fieldElem = $newDoc->createElement('field');
				$fieldElem->setAttribute('index',$imgCnt);
				$fieldElem->setAttribute('term',$v);
				$extElem2->appendChild($fieldElem);
				$imgCnt++;
			}
			$rootElem->appendChild($extElem2);
		}

		//MeasurementOrFact extension
		if($this->includeAttributes){
			$extElem3 = $newDoc->createElement('extension');
			$extElem3->setAttribute('encoding',$this->charSetOut);
			$extElem3->setAttribute('fieldsTerminatedBy',$this->delimiter);
			$extElem3->setAttribute('linesTerminatedBy','\n');
			$extElem3->setAttribute('fieldsEnclosedBy','"');
			$extElem3->setAttribute('ignoreHeaderLines','1');
			$extElem3->setAttribute('rowType','http://rs.iobis.org/obis/terms/ExtendedMeasurementOrFact');

			$filesElem3 = $newDoc->createElement('files');
			$filesElem3->appendChild($newDoc->createElement('location','measurementOrFact'.$this->fileExt));
			$extElem3->appendChild($filesElem3);

			$coreIdElem3 = $newDoc->createElement('coreid');
			$coreIdElem3->setAttribute('index','0');
			$extElem3->appendChild($coreIdElem3);

			$mofCnt = 1;
			$termArr = $this->attributeFieldArr['terms'];
			foreach($termArr as $v){
				$fieldElem = $newDoc->createElement('field');
				$fieldElem->setAttribute('index',$mofCnt);
				$fieldElem->setAttribute('term',$v);
				$extElem3->appendChild($fieldElem);
				$mofCnt++;
			}
			$rootElem->appendChild($extElem3);
		}

		//MaterialSample extension
		if($this->includeMaterialSample && isset($this->fieldArrMap['materialSample'])){
			$extElem3 = $newDoc->createElement('extension');
			$extElem3->setAttribute('encoding',$this->charSetOut);
			$extElem3->setAttribute('fieldsTerminatedBy',$this->delimiter);
			$extElem3->setAttribute('linesTerminatedBy','\n');
			$extElem3->setAttribute('fieldsEnclosedBy','"');
			$extElem3->setAttribute('ignoreHeaderLines','1');
			$extElem3->setAttribute('rowType','http://data.ggbn.org/schemas/ggbn/terms/MaterialSample');

			$filesElem3 = $newDoc->createElement('files');
			$filesElem3->appendChild($newDoc->createElement('location','materialSample'.$this->fileExt));
			$extElem3->appendChild($filesElem3);

			$coreIdElem3 = $newDoc->createElement('coreid');
			$coreIdElem3->setAttribute('index','0');
			$extElem3->appendChild($coreIdElem3);

			$msCnt = 1;
			foreach($this->fieldArrMap['materialSample'] as $term){
				$fieldElem = $newDoc->createElement('field');
				$fieldElem->setAttribute('index',$msCnt);
				$fieldElem->setAttribute('term',$term);
				$extElem3->appendChild($fieldElem);
				$msCnt++;
			}
			$rootElem->appendChild($extElem3);
		}
		$newDoc->save($this->targetPath.$this->ts.'-meta.xml');

    	$this->logOrEcho('Done! ('.date('h:i:s A').")\n");
	}

	private function getEmlArr(){

		$this->setServerDomain();
		$urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');
		$localDomain = $this->serverDomain;

		$emlArr = array();
		if(count($this->collArr) == 1){
			$collId = key($this->collArr);
			$emlArr['alternateIdentifier'][] = $urlPathPrefix.'collections/misc/collprofiles.php?collid='.$collId;
			$emlArr['title'] = $this->collArr[$collId]['collname'];
			$emlArr['description'] = $this->collArr[$collId]['description'];

			if(isset($this->collArr[$collId]['contact'][0]['givenName'])) $emlArr['contact']['givenName'] = $this->collArr[$collId]['contact'][0]['givenName'];
			if(isset($this->collArr[$collId]['contact'][0]['surName'])) $emlArr['contact']['surName'] = $this->collArr[$collId]['contact'][0]['surName'];
			if(isset($this->collArr[$collId]['collname'])) $emlArr['contact']['organizationName'] = $this->collArr[$collId]['collname'];
			if(isset($this->collArr[$collId]['phone'])) $emlArr['contact']['phone'] = $this->collArr[$collId]['phone'];
			if(isset($this->collArr[$collId]['contact'][0]['electronicMailAddress'])) $emlArr['contact']['electronicMailAddress'] = $this->collArr[$collId]['contact'][0]['electronicMailAddress'];
			if(isset($this->collArr[$collId]['contact'][0]['userId'])) $emlArr['contact']['userId'] = $this->collArr[$collId]['contact'][0]['userId'];
			if($this->collArr[$collId]['url']) $emlArr['contact']['onlineUrl'] = $this->collArr[$collId]['url'];
			$addrStr = $this->collArr[$collId]['address1'];
			if($this->collArr[$collId]['address2']) $addrStr .= ', '.$this->collArr[$collId]['address2'];
			if($addrStr) $emlArr['contact']['addr']['deliveryPoint'] = $addrStr;
			if($this->collArr[$collId]['city']) $emlArr['contact']['addr']['city'] = $this->collArr[$collId]['city'];
			if($this->collArr[$collId]['state']) $emlArr['contact']['addr']['administrativeArea'] = $this->collArr[$collId]['state'];
			if($this->collArr[$collId]['postalcode']) $emlArr['contact']['addr']['postalCode'] = $this->collArr[$collId]['postalcode'];
			if($this->collArr[$collId]['country']) $emlArr['contact']['addr']['country'] = $this->collArr[$collId]['country'];
			if($this->collArr[$collId]['rights']) $emlArr['intellectualRights'] = $this->collArr[$collId]['rights'];
		}
		else{
			//Dataset contains multiple collection data
			$emlArr['title'] = $GLOBALS['DEFAULT_TITLE'].' general data extract';
			if(isset($GLOBALS['SYMB_UID']) && $GLOBALS['SYMB_UID']){
				$sql = 'SELECT uid, lastname, firstname, title, institution, department, address, city, state, zip, country, phone, email, ispublic FROM users WHERE (uid = '.$GLOBALS['SYMB_UID'].')';
				$rs = $this->conn->query($sql);
				if($r = $rs->fetch_object()){
					$emlArr['associatedParty'][0]['individualName']['surName'] = $r->lastname;
					if($r->firstname) $emlArr['associatedParty'][0]['individualName']['givenName'] = $r->firstname;
					if($r->email) $emlArr['associatedParty'][0]['electronicMailAddress'] = $r->email;
					$emlArr['associatedParty'][0]['role'] = 'datasetOriginator';
					if($r->ispublic){
						if($r->institution) $emlArr['associatedParty'][0]['organizationName'] = $r->institution;
						if($r->title) $emlArr['associatedParty'][0]['positionName'] = $r->title;
						if($r->phone) $emlArr['associatedParty'][0]['phone'] = $r->phone;
						if($r->state){
							if($r->department) $emlArr['associatedParty'][0]['address']['deliveryPoint'][] = $r->department;
							if($r->address) $emlArr['associatedParty'][0]['address']['deliveryPoint'][] = $r->address;
							if($r->city) $emlArr['associatedParty'][0]['address']['city'] = $r->city;
							$emlArr['associatedParty'][0]['address']['administrativeArea'] = $r->state;
							if($r->zip) $emlArr['associatedParty'][0]['address']['postalCode'] = $r->zip;
							if($r->country) $emlArr['associatedParty'][0]['address']['country'] = $r->country;
						}
					}
					$rs->free();
				}
			}
		}

		if(array_key_exists('PORTAL_GUID',$GLOBALS) && $GLOBALS['PORTAL_GUID']){
			$emlArr['creator'][0]['attr']['id'] = $GLOBALS['PORTAL_GUID'];
		}
		$emlArr['creator'][0]['organizationName'] = $GLOBALS['DEFAULT_TITLE'];
		$emlArr['creator'][0]['electronicMailAddress'] = $GLOBALS['ADMIN_EMAIL'];
		$emlArr['creator'][0]['onlineUrl'] = $urlPathPrefix.'index.php';

		$emlArr['metadataProvider'][0]['organizationName'] = $GLOBALS['DEFAULT_TITLE'];
		$emlArr['metadataProvider'][0]['electronicMailAddress'] = $GLOBALS['ADMIN_EMAIL'];
		$emlArr['metadataProvider'][0]['onlineUrl'] = $urlPathPrefix.'index.php';

		$emlArr['pubDate'] = date("Y-m-d");

		//Append collection metadata
		foreach($this->collArr as $id => $collArr){
			//Collection metadata section (additionalMetadata)
			$emlArr['collMetadata'][$id]['attr']['identifier'] = $collArr['collectionguid'];
			$emlArr['collMetadata'][$id]['attr']['id'] = $id;
			$emlArr['collMetadata'][$id]['alternateIdentifier'] = $urlPathPrefix.'collections/misc/collprofiles.php?collid='.$id;
			$emlArr['collMetadata'][$id]['parentCollectionIdentifier'] = $collArr['instcode'];
			$emlArr['collMetadata'][$id]['collectionIdentifier'] = $collArr['collcode'];
			$emlArr['collMetadata'][$id]['collectionName'] = $collArr['collname'];
			if($collArr['icon']){
				$imgLink = '';
				if(substr($collArr['icon'],0,17) == 'images/collicons/'){
					$imgLink = $urlPathPrefix.$collArr['icon'];
				}
				elseif(substr($collArr['icon'],0,1) == '/'){
					$imgLink = $localDomain.$collArr['icon'];
				}
				else{
					$imgLink = $collArr['icon'];
				}
				$emlArr['collMetadata'][$id]['resourceLogoUrl'] = $imgLink;
			}
			$emlArr['collMetadata'][$id]['onlineUrl'] = $collArr['url'];
			$emlArr['collMetadata'][$id]['intellectualRights'] = $collArr['rights'];
			if($collArr['rightsholder']) $emlArr['collMetadata'][$id]['additionalInfo'] = $collArr['rightsholder'];
			if($collArr['usageterm']) $emlArr['collMetadata'][$id]['additionalInfo'] = $collArr['usageterm'];
			$emlArr['collMetadata'][$id]['abstract'] = $collArr['description'];
			if(isset($collArr['contact'])){
				$contactArr = $collArr['contact'];
				foreach($contactArr as $cnt => $cArr){
					if(count($this->collArr) == 1){
						//Set contacts within associated party element
						$cArr['role'] = 'contentProvider';
						$emlArr['associatedParty'][] = $cArr;
					}
					//Also set info within collMetadata element
					$keepContactArr = array('userId','individualName','electronicMailAddress','positionName','onlineUrl');
					$emlArr['collMetadata'][$id]['contact'][$cnt] = array_intersect_key($cArr, array_flip($keepContactArr));
				}
			}
		}
		$emlArr = $this->utf8EncodeArr($emlArr);
		return $emlArr;
	}

	private function writeEmlFile(){
		$this->logOrEcho("Creating eml.xml (".date('h:i:s A').")... ");

		$emlDoc = $this->getEmlDom();

		$emlDoc->save($this->targetPath.$this->ts.'-eml.xml');

    	$this->logOrEcho('Done! ('.date('h:i:s A').")\n");
	}

	/*
	 * Input: Array containing the eml data
	 * OUTPUT: XML String representing the EML
	 * USED BY: this class, and emlhandler.php
	 */
	public function getEmlDom($emlArr = null){
		global $RIGHTS_TERMS_DEFS;

		if(!$emlArr) $emlArr = $this->getEmlArr();

		//Create new DOM document
		$newDoc = new DOMDocument('1.0',$this->charSetOut);

		//Add root element
		$rootElem = $newDoc->createElement('eml:eml');
		$rootElem->setAttribute('xmlns:eml','eml://ecoinformatics.org/eml-2.1.1');
		$rootElem->setAttribute('xmlns:dc','http://purl.org/dc/terms/');
		$rootElem->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$rootElem->setAttribute('xsi:schemaLocation','eml://ecoinformatics.org/eml-2.1.1 http://rs.gbif.org/schema/eml-gbif-profile/1.0.1/eml.xsd');
		$rootElem->setAttribute('packageId',UuidFactory::getUuidV4());
		$rootElem->setAttribute('system','https://symbiota.org');
		$rootElem->setAttribute('scope','system');
		$rootElem->setAttribute('xml:lang','eng');

		$newDoc->appendChild($rootElem);

		$datasetElem = $newDoc->createElement('dataset');
		$rootElem->appendChild($datasetElem);

		if(array_key_exists('alternateIdentifier',$emlArr)){
			foreach($emlArr['alternateIdentifier'] as $v){
				$altIdElem = $newDoc->createElement('alternateIdentifier');
				$altIdElem->appendChild($newDoc->createTextNode($v));
				$datasetElem->appendChild($altIdElem);
			}
		}

		if(array_key_exists('title',$emlArr)){
			$titleElem = $newDoc->createElement('title');
			$titleElem->setAttribute('xml:lang','eng');
			$titleElem->appendChild($newDoc->createTextNode($emlArr['title']));
			$datasetElem->appendChild($titleElem);
		}

		if(array_key_exists('creator',$emlArr)){
			$createArr = $emlArr['creator'];
			foreach($createArr as $childArr){
				$creatorElem = $newDoc->createElement('creator');
				if(isset($childArr['attr'])){
					$attrArr = $childArr['attr'];
					unset($childArr['attr']);
					foreach($attrArr as $atKey => $atValue){
						$creatorElem->setAttribute($atKey,$atValue);
					}
				}
				foreach($childArr as $k => $v){
					$newChildElem = $newDoc->createElement($k);
					$newChildElem->appendChild($newDoc->createTextNode($v));
					$creatorElem->appendChild($newChildElem);
				}
				$datasetElem->appendChild($creatorElem);
			}
		}

		if(array_key_exists('metadataProvider',$emlArr)){
			$mdArr = $emlArr['metadataProvider'];
			foreach($mdArr as $childArr){
				$mdElem = $newDoc->createElement('metadataProvider');
				foreach($childArr as $k => $v){
					$newChildElem = $newDoc->createElement($k);
					$newChildElem->appendChild($newDoc->createTextNode($v));
					$mdElem->appendChild($newChildElem);
				}
				$datasetElem->appendChild($mdElem);
			}
		}

		if(array_key_exists('pubDate',$emlArr) && $emlArr['pubDate']){
			$pubElem = $newDoc->createElement('pubDate');
			$pubElem->appendChild($newDoc->createTextNode($emlArr['pubDate']));
			$datasetElem->appendChild($pubElem);
		}
		$langStr = 'eng';
		if(array_key_exists('language',$emlArr) && $emlArr) $langStr = $emlArr['language'];
		$langElem = $newDoc->createElement('language');
		$langElem->appendChild($newDoc->createTextNode($langStr));
		$datasetElem->appendChild($langElem);

		if(array_key_exists('description',$emlArr) && $emlArr['description']){
			$abstractElem = $newDoc->createElement('abstract');
			$paraElem = $newDoc->createElement('para');
			$paraElem->appendChild($newDoc->createTextNode(strip_tags($emlArr['description'])));
			$abstractElem->appendChild($paraElem);
			$datasetElem->appendChild($abstractElem);
		}

		if(array_key_exists('contact',$emlArr)){
			$contactArr = $emlArr['contact'];
			$contactNode = $this->getNode($newDoc, 'contact', $contactArr);
			$datasetElem->appendChild($contactNode);
		}
		if(array_key_exists('associatedParty',$emlArr)){
			$associatedPartyArr = $emlArr['associatedParty'];
			foreach($associatedPartyArr as $assocArr){
				$assocElem = $this->getNode($newDoc, 'associatedParty', $assocArr);
				$datasetElem->appendChild($assocElem);
			}
		}

		if(array_key_exists('intellectualRights',$emlArr)){
			$rightsElem = $newDoc->createElement('intellectualRights');
			$paraElem = $newDoc->createElement('para');
			$paraElem->appendChild($newDoc->createTextNode('To the extent possible under law, the publisher has waived all rights to these data and has dedicated them to the '));
            $ulinkElem = $newDoc->createElement('ulink');
            $citetitleElem = $newDoc->createElement('citetitle');
            $citetitleElem->appendChild($newDoc->createTextNode(($RIGHTS_TERMS_DEFS && array_key_exists('title',$RIGHTS_TERMS_DEFS)?$RIGHTS_TERMS_DEFS['title']:'')));
            $ulinkElem->appendChild($citetitleElem);
            $ulinkElem->setAttribute('url',($RIGHTS_TERMS_DEFS && array_key_exists('url',$RIGHTS_TERMS_DEFS)?$RIGHTS_TERMS_DEFS['url']:$emlArr['intellectualRights']));
            $paraElem->appendChild($ulinkElem);
            $paraElem->appendChild($newDoc->createTextNode(($RIGHTS_TERMS_DEFS && array_key_exists('def',$RIGHTS_TERMS_DEFS)?$RIGHTS_TERMS_DEFS['def']:'')));
            $rightsElem->appendChild($paraElem);
			$datasetElem->appendChild($rightsElem);
		}

		if($this->projectMetadataArr){
			$projectElem = $this->getNode($newDoc, 'project', $this->projectMetadataArr);
			$datasetElem->appendChild($projectElem);
			/*
			 * Example EML: http://ipt.gbifbenin.org/eml.do?r=mbi_groupe3_menacees
			 * $projectMetadataArr = array('nodeAttribute' => array( 'id' => 'BID-AF2020-122-NAC'), 'title' => 'The Gabon Biodiversity Portal', 'abstract' => array('para' => 'https://www.gbif.org/project/BID-AF2020-122-NAC/the-gabon-biodiversity-portal'))
			 * json: {"publicationProps":{"project":{"nodeAttribute":{"id":"BID-AF2020-122-NAC"},"title":"The Gabon Biodiversity Portal","abstract":{"para":"https://www.gbif.org/project/BID-AF2020-122-NAC/the-gabon-biodiversity-portal"}}}}
			*/
		}

		$symbElem = $newDoc->createElement('symbiota');
		if(isset($GLOBALS['PORTAL_GUID'])) $symbElem->setAttribute('id',$GLOBALS['PORTAL_GUID']);
		$dateElem = $newDoc->createElement('dateStamp');
		$dateElem->appendChild($newDoc->createTextNode(date("c")));
		$symbElem->appendChild($dateElem);
		//Citation
		$id = UuidFactory::getUuidV4();
		$citeElem = $newDoc->createElement('citation');
		$citeElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'].' - '.$id));
		$citeElem->setAttribute('identifier',$id);
		$symbElem->appendChild($citeElem);
		//Physical
		$physicalElem = $newDoc->createElement('physical');
		$physicalElem->appendChild($newDoc->createElement('characterEncoding',$this->charSetOut));
		//format
		$dfElem = $newDoc->createElement('dataFormat');
		$edfElem = $newDoc->createElement('externallyDefinedFormat');
		$dfElem->appendChild($edfElem);
		$edfElem->appendChild($newDoc->createElement('formatName','Darwin Core Archive'));
		$physicalElem->appendChild($dfElem);
		$symbElem->appendChild($physicalElem);
		//Collection data
		if(array_key_exists('collMetadata',$emlArr)){
			foreach($emlArr['collMetadata'] as $k => $collArr){
				$collArr = $this->utf8EncodeArr($collArr);
				$collElem = $newDoc->createElement('collection');
				if(isset($collArr['attr']) && $collArr['attr']){
					$attrArr = $collArr['attr'];
					unset($collArr['attr']);
					foreach($attrArr as $attrKey => $attrValue){
						$collElem->setAttribute($attrKey,$attrValue);
					}
				}
				$abstractStr = '';
				if(isset($collArr['abstract']) && $collArr['abstract']){
					$abstractStr = $collArr['abstract'];
					unset($collArr['abstract']);
				}
				foreach($collArr as $collKey => $collValue){
					if($collKey == 'contact'){
						foreach($collValue as $apArr){
							$assocElem = $this->getNode($newDoc, 'associatedParty', $apArr);
							$collElem->appendChild($assocElem);
						}
					}
					else{
						$collElem2 = $newDoc->createElement($collKey);
						$collElem2->appendChild($newDoc->createTextNode($collValue));
						$collElem->appendChild($collElem2);
					}
				}
				if($abstractStr){
					$abstractElem = $newDoc->createElement('abstract');
					$abstractElem2 = $newDoc->createElement('para');
					$abstractElem2->appendChild($newDoc->createTextNode($abstractStr));
					$abstractElem->appendChild($abstractElem2);
					$collElem->appendChild($abstractElem);
				}
				$symbElem->appendChild($collElem);
			}
		}

		$metaElem = $newDoc->createElement('metadata');
		$metaElem->appendChild($symbElem);
		if($this->schemaType == 'coge' && $this->geolocateVariables){
			$this->setServerDomain();
			$urlPathPrefix = '';
			if($this->serverDomain){
				$urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');
				$urlPathPrefix .= 'collections/individual/index.php';
				//Add Geolocate metadata
				$glElem = $newDoc->createElement('geoLocate');
				$glElem->appendChild($newDoc->createElement('dataSourcePrimaryName',$this->geolocateVariables['cogename']));
				$glElem->appendChild($newDoc->createElement('dataSourceSecondaryName',$this->geolocateVariables['cogedescr']));
				$glElem->appendChild($newDoc->createElement('targetCommunityName',$this->geolocateVariables['cogecomm']));
				#if(isset($this->geolocateVariables['targetcommunityidentifier'])) $glElem->appendChild($newDoc->createElement('targetCommunityIdentifier',''));
				$glElem->appendChild($newDoc->createElement('specimenHyperlinkBase',$urlPathPrefix));
				$glElem->appendChild($newDoc->createElement('specimenHyperlinkParameter','occid'));
				$glElem->appendChild($newDoc->createElement('specimenHyperlinkValueField','Id'));
				$metaElem->appendChild($glElem);
			}
		}
		$addMetaElem = $newDoc->createElement('additionalMetadata');
		$addMetaElem->appendChild($metaElem);
		$rootElem->appendChild($addMetaElem);

		return $newDoc;
	}

	private function getNode($newDoc, $elmentTag, $nodeArr){
		$newNode = $newDoc->createElement($elmentTag);
		foreach($nodeArr as $nodeKey => $nodeValue){
			if($nodeKey == 'nodeAttribute'){
				foreach($nodeValue as $attrKey => $attrValue){
					$newNode->setAttribute($attrKey,$attrValue);
				}
			}
			elseif(is_array($nodeValue)){
				$childNode = $this->getNode($newDoc, $nodeKey, $nodeValue);
				$newNode->appendChild($childNode);
			}
			elseif($nodeKey == 'nodeValue') $newNode->appendChild($newDoc->createTextNode($nodeValue));
			else{
				$childElem = $newDoc->createElement($nodeKey);
				$childElem->appendChild($newDoc->createTextNode($nodeValue));
				$newNode->appendChild($childElem);
			}
		}
		return $newNode;
	}

	public function getFullRss(){
		//Create new document and write out to target
		$newDoc = new DOMDocument('1.0',$this->charSetOut);

		//Add root element
		$rootElem = $newDoc->createElement('rss');
		$rootAttr = $newDoc->createAttribute('version');
		$rootAttr->value = '2.0';
		$rootElem->appendChild($rootAttr);
		$newDoc->appendChild($rootElem);

		//Add Channel
		$channelElem = $newDoc->createElement('channel');
		$rootElem->appendChild($channelElem);

		//Add title, link, description, language
		$titleElem = $newDoc->createElement('title');
		$titleElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'].' Biological Occurrences RSS feed'));
		$channelElem->appendChild($titleElem);

		$this->setServerDomain();
		$urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');

		$localDomain = $this->serverDomain;

		$linkElem = $newDoc->createElement('link');
		$linkElem->appendChild($newDoc->createTextNode($urlPathPrefix));
		$channelElem->appendChild($linkElem);
		$descriptionElem = $newDoc->createElement('description');
		$descriptionElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'].' Natural History Collections and Observation Project feed'));
		$channelElem->appendChild($descriptionElem);
		$languageElem = $newDoc->createElement('language','en-us');
		$channelElem->appendChild($languageElem);

		//Create new item for target archives and load into array
		$sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.icon, c.collectionguid, c.dwcaurl, c.managementtype, s.uploaddate '.
			'FROM omcollections c INNER JOIN omcollectionstats s ON c.collid = s.collid '.
			'WHERE s.recordcnt > 0 '.
			'ORDER BY c.SortSeq, c.CollectionName';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$cArr = $this->utf8EncodeArr($r);
			$itemElem = $newDoc->createElement('item');
			$itemAttr = $newDoc->createAttribute('collid');
			$itemAttr->value = $cArr['collid'];
			$itemElem->appendChild($itemAttr);
			//Add title
			$instCode = $cArr['institutioncode'];
			if($cArr['collectioncode']) $instCode .= '-'.$cArr['collectioncode'];
			$title = $instCode;
			$itemTitleElem = $newDoc->createElement('title');
			$itemTitleElem->appendChild($newDoc->createTextNode($title));
			$itemElem->appendChild($itemTitleElem);
			//Icon
			$imgLink = '';
			if(substr($cArr['icon'],0,17) == 'images/collicons/'){
				//Link is a
				$imgLink = $urlPathPrefix.$cArr['icon'];
			}
			elseif(substr($cArr['icon'],0,1) == '/'){
				$imgLink = $localDomain.$cArr['icon'];
			}
			else{
				$imgLink = $cArr['icon'];
			}
			$iconElem = $newDoc->createElement('image');
			$iconElem->appendChild($newDoc->createTextNode($imgLink));
			$itemElem->appendChild($iconElem);

			//description
			$descTitleElem = $newDoc->createElement('description');
			$descTitleElem->appendChild($newDoc->createTextNode($cArr['collectionname']));
			$itemElem->appendChild($descTitleElem);
			//GUIDs
			$guidElem = $newDoc->createElement('guid');
			$guidElem->appendChild($newDoc->createTextNode($cArr['collectionguid']));
			$itemElem->appendChild($guidElem);

			$emlElem = $newDoc->createElement('emllink');
			$emlElem->appendChild($newDoc->createTextNode($urlPathPrefix.'collections/datasets/emlhandler.php?collid='.$cArr['collid']));
			$itemElem->appendChild($emlElem);

			$link = $cArr['dwcaurl'];
			$type = 'DWCA';
			if(!$link){
				$link = $urlPathPrefix.'collections/misc/collprofiles.php?collid='.$cArr['collid'];
				$type = 'HTML';
			}
			$typeTitleElem = $newDoc->createElement('type',$type);
			$itemElem->appendChild($typeTitleElem);

			//link
			$linkTitleElem = $newDoc->createElement('link');
			$linkTitleElem->appendChild($newDoc->createTextNode($link));
			$itemElem->appendChild($linkTitleElem);
			$dateStr = '';
			if($cArr['managementtype'] == 'Live Data'){
				$dateStr = date("D, d M Y H:i:s");
			}
			elseif($cArr['uploaddate']){
				$dateStr = date("D, d M Y H:i:s",strtotime($cArr['uploaddate']));
			}
			$pubDateTitleElem = $newDoc->createElement('pubDate');
			$pubDateTitleElem->appendChild($newDoc->createTextNode($dateStr));
			$itemElem->appendChild($pubDateTitleElem);
			$channelElem->appendChild($itemElem);
		}
		return $newDoc->saveXML();
	}

	//Generate Data files
	private function writeOccurrenceFile(){
		$this->logOrEcho('Creating occurrence file ('.date('h:i:s A').')... ');
		$filePath = $this->targetPath.$this->ts.'-occur'.$this->fileExt;
		$fh = fopen($filePath, 'w');
		if(!$fh){
			$this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
			return false;
		}
		$hasRecords = false;

		$dwcOccurManager = new DwcArchiverOccurrence($this->conn);
		$dwcOccurManager->setSchemaType($this->schemaType);
		$dwcOccurManager->setExtended($this->extended);
		$dwcOccurManager->setIncludeExsiccatae();
		$dwcOccurManager->setIncludeAssociatedSequences();
		$dwcOccurManager->setIncludePaleo($this->hasPaleo);
		if(!$this->occurrenceFieldArr) $this->occurrenceFieldArr = $dwcOccurManager->getOccurrenceArr($this->schemaType, $this->extended);
		//Output records
		$this->applyConditions();
		$sql = $dwcOccurManager->getSqlOccurrences($this->occurrenceFieldArr['fields']);
		$sql .= $this->getTableJoins().$this->conditionSql;
		if(!$this->conditionSql) return false;
		if($this->schemaType != 'backup') $sql .= ' LIMIT 1000000';

		//Output header
		$fieldArr = $this->occurrenceFieldArr['fields'];
		if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft'){
			unset($fieldArr['localitySecurity']);
			unset($fieldArr['collID']);
		}
		elseif($this->schemaType == 'backup') unset($fieldArr['collID']);
		$fieldOutArr = array();
		if($this->schemaType == 'coge'){
			//Convert to GeoLocate flavor
			$glFields = array('specificEpithet'=>'Species','scientificNameAuthorship'=>'ScientificNameAuthor','recordedBy'=>'Collector','recordNumber'=>'CollectorNumber',
				'year'=>'YearCollected','month'=>'MonthCollected','day'=>'DayCollected','decimalLatitude'=>'Latitude','decimalLongitude'=>'Longitude',
				'minimumElevationInMeters'=>'MinimumElevation','maximumElevationInMeters'=>'MaximumElevation','maximumDepthInMeters'=>'MaximumDepth','minimumDepthInMeters'=>'MinimumDepth',
				'occurrenceRemarks'=>'Notes','dateEntered','dateLastModified','collID'=>'collId','recordID'=>'recordId','references');
			foreach($fieldArr as $k => $v){
				if(array_key_exists($k,$glFields)) $fieldOutArr[] = $glFields[$k];
				else $fieldOutArr[] = strtoupper(substr($k,0,1)).substr($k,1);
			}
		}
		else $fieldOutArr = array_keys($fieldArr);
		$this->writeOutRecord($fh,$fieldOutArr);
		if(!$this->collArr){
			//Collection array not previously primed by source
			$sql1 = 'SELECT DISTINCT o.collid FROM omoccurrences o ';
			if($this->conditionSql){
				$sql1 .= $this->getTableJoins().$this->conditionSql;
			}
			$rs1 = $this->conn->query($sql1);
			$collidStr = '';
			while($r1 = $rs1->fetch_object()){
				$collidStr .= ','.$r1->collid;
			}
			$rs1->free();
			if($collidStr) $this->setCollArr(trim($collidStr,','));
		}

		//$dwcOccurManager->setUpperTaxonomy();
		$dwcOccurManager->setTaxonRank();

		$materialSampleHandler = null;
		if($this->includeMaterialSample){
			$collid = key($this->collArr);
			if(isset($this->collArr[$collid]['matSample'])){
				$this->logOrEcho('Creating material sample extension file ('.date('h:i:s A').')... ');
				$materialSampleHandler = new DwcArchiverMaterialSample($this->conn);
				$materialSampleHandler->initiateProcess($this->targetPath.$this->ts.'-matSample'.$this->fileExt);
				$materialSampleHandler->setSchemaType($this->schemaType);
				$this->fieldArrMap['materialSample'] = $materialSampleHandler->getFieldArrTerms();
			}
		}
		//echo $sql; exit;
		if($rs = $this->dataConn->query($sql,MYSQLI_USE_RESULT)){
			$this->setServerDomain();
			$urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');
			$typeArr = null;
			if($this->schemaType == 'pensoft'){
				$typeArr = array('Other material', 'Holotype', 'Paratype', 'Isotype', 'Isoparatype', 'Isolectotype', 'Isoneotype', 'Isosyntype');
				//$typeArr = array('Other material', 'Holotype', 'Paratype', 'Hapantotype', 'Syntype', 'Isotype', 'Neotype', 'Lectotype', 'Paralectotype', 'Isoparatype', 'Isolectotype', 'Isoneotype', 'Isosyntype');
			}
			$statsManager = new OccurrenceAccessStats();
			$batchOccidArr = array();
			while($r = $rs->fetch_assoc()){
				if(!$r['occurrenceID']){
					//Set occurrence GUID based on GUID target, but only if occurrenceID field isn't already populated
					$guidTarget = $this->collArr[$r['collID']]['guidtarget'];
					if($guidTarget == 'catalogNumber') $r['occurrenceID'] = $r['catalogNumber'];
					elseif($guidTarget == 'symbiotaUUID') $r['occurrenceID'] = $r['recordID'];
				}
				if($this->limitToGuids && (!$r['occurrenceID'] || !$r['basisOfRecord'])){
					// Skip record because there is no occurrenceID guid
					continue;
				}
				$hasRecords = true;
				//Protect sensitive records
				if($this->redactLocalities && $r['localitySecurity'] == 1 && !in_array($r['collID'],$this->rareReaderArr)){
					$protectedFields = array();
					foreach($this->securityArr as $v){
						if(array_key_exists($v,$r) && $r[$v]){
							$r[$v] = '';
							$protectedFields[] = $v;
						}
					}
					if($protectedFields) $r['informationWithheld'] = trim($r['informationWithheld'].'; field values redacted: '.implode(', ',$protectedFields),' ;');
				}

				if($urlPathPrefix) $r['t_references'] = $urlPathPrefix.'collections/individual/index.php?occid='.$r['occid'];
				$r['recordID'] = 'urn:uuid:'.$r['recordID'];
				//Add collection GUID based on management type
				$managementType = $this->collArr[$r['collID']]['managementtype'];
				if($managementType && $managementType == 'Live Data'){
					if(array_key_exists('collectionID',$r) && !$r['collectionID']){
						$guid = $this->collArr[$r['collID']]['collectionguid'];
						if(strlen($guid) == 36) $guid = 'urn:uuid:'.$guid;
						$r['collectionID'] = $guid;
					}
				}
				if($this->schemaType == 'dwc'){
					unset($r['localitySecurity']);
					unset($r['collID']);
				}
				elseif($this->schemaType == 'pensoft'){
					unset($r['localitySecurity']);
					unset($r['collID']);
					if($r['typeStatus']){
						$typeValue = strtolower($r['typeStatus']);
						$typeInvalid = true;
						$invalidText = '';
						foreach($typeArr as $testStr){
							if($typeValue == strtolower($testStr)){
								$typeInvalid = false;
								break;
							}
							elseif(stripos($typeValue, $testStr)){
								$invalidText = $r['typeStatus'];
								$r['typeStatus'] = $testStr;
								$typeInvalid = false;
								break;
							}
						}
						if($typeInvalid){
							$invalidText = $r['typeStatus'];
							$r['typeStatus'] = 'Other material';
						}
						if($invalidText){
							if($r['occurrenceRemarks']) $invalidText = $r['occurrenceRemarks'].'; '.$invalidText;
							$r['occurrenceRemarks'] = $invalidText;
						}
					}
					else $r['typeStatus'] = 'Other material';
				}
				elseif($this->schemaType == 'backup') unset($r['collID']);

				if($ocnStr = $dwcOccurManager->getAdditionalCatalogNumberStr($r['occid'])) $r['otherCatalogNumbers'] = $ocnStr;
				if($exsStr = $dwcOccurManager->getExsiccateStr($r['occid'])){
					if(isset($r['occurrenceRemarks']) && $r['occurrenceRemarks']) $exsStr = $r['occurrenceRemarks'].'; '.$exsStr;
					$r['occurrenceRemarks'] = $exsStr;
				}
				if($assocOccurStr = $dwcOccurManager->getAssociationStr($r['occid'])) $r['t_associatedOccurrences'] = $assocOccurStr;
				if($assocSeqStr = $dwcOccurManager->getAssociatedSequencesStr($r['occid'])) $r['t_associatedSequences'] = $assocSeqStr;
				if($assocTaxa = $dwcOccurManager->getAssocTaxa($r['occid'])) $r['associatedTaxa'] = $assocTaxa;
				//$dwcOccurManager->appendUpperTaxonomy($r);
				$dwcOccurManager->appendUpperTaxonomy2($r);
				if($rankStr = $dwcOccurManager->getTaxonRank($r['rankid'])) $r['t_taxonRank'] = $rankStr;
				unset($r['rankid']);

				$this->encodeArr($r);
				$this->addcslashesArr($r);
				$this->writeOutRecord($fh,$r);

				$batchOccidArr[] = $r['occid'];
				if(count($batchOccidArr) > 1000){
					if($materialSampleHandler) $materialSampleHandler->writeOutRecordBlock($batchOccidArr);
					unset($batchOccidArr);
					$batchOccidArr = array();
				}
				//Set access statistics
				if($this->isPublicDownload){
					if($this->schemaType == 'dwc' || $this->schemaType == 'symbiota'){
						//Don't count if dl is backup, GeoLocate transfer, or pensoft
						$statsManager->recordAccessEvent($r['occid'], 'download');
					}
				}
			}
			$rs->free();
			if($materialSampleHandler){
				if($batchOccidArr) $materialSampleHandler->writeOutRecordBlock($batchOccidArr);
				$materialSampleHandler->__destruct();
			}
		}
		else{
			$this->errorMessage = 'ERROR creating occurrence file: '.$this->conn->error;
			$this->logOrEcho($this->errorMessage);
			//$this->logOrEcho("\tSQL: ".$sql."\n");
		}

		fclose($fh);
		if(!$hasRecords){
			$filePath = false;
			//$this->writeOutRecord($fh,array('No records returned. Modify query variables to be more inclusive.'));
			$this->errorMessage = 'No records returned. Modify query variables to be more inclusive.';
			$this->logOrEcho($this->errorMessage);
		}
		$this->logOrEcho('Done! ('.date('h:i:s A').")\n");
		return $filePath;
	}

	public function getOccurrenceFile(){
		if(!$this->targetPath) $this->setTargetPath();
		$this->dataConn = MySQLiConnectionFactory::getCon('readonly');
		$filePath = $this->writeOccurrenceFile();
		$this->dataConn->close();
		return $filePath;
	}

	private function writeDeterminationFile(){
		$this->logOrEcho("Creating identification extension file (".date('h:i:s A').")... ");
		$filePath = $this->targetPath.$this->ts.'-det'.$this->fileExt;
		$fh = fopen($filePath, 'w');
		if(!$fh){
			$this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
			return false;
		}

		if(!$this->determinationFieldArr){
			$this->determinationFieldArr = DwcArchiverDetermination::getDeterminationArr($this->schemaType,$this->extended);
		}
		//Output header
		$headerArr = array_keys($this->determinationFieldArr['fields']);
		array_pop($headerArr);
		$this->writeOutRecord($fh,$headerArr);

		//Output records
		$sql = DwcArchiverDetermination::getSqlDeterminations($this->determinationFieldArr['fields'],$this->conditionSql);
		if($rs = $this->dataConn->query($sql,MYSQLI_USE_RESULT)){
			$previousDetID = 0;
			while($r = $rs->fetch_assoc()){
				if($previousDetID == $r['detID']) continue;
				$previousDetID = $r['detID'];
				unset($r['detID']);
				$r['recordID'] = 'urn:uuid:'.$r['recordID'];
				$this->encodeArr($r);
				$this->addcslashesArr($r);
				$this->writeOutRecord($fh,$r);
			}
			$rs->free();
		}
		else{
			$this->logOrEcho("ERROR creating identification extension file: ".$this->dataConn->error."\n");
			$this->logOrEcho("\tSQL: ".$sql."\n");
		}

		fclose($fh);
    	$this->logOrEcho('Done! ('.date('h:i:s A').")\n");
	}

	private function writeImageFile(){
		$this->logOrEcho("Creating image extension file (".date('h:i:s A').")... ");
		$filePath = $this->targetPath.$this->ts.'-multimedia'.$this->fileExt;
		$fh = fopen($filePath, 'w');
		if(!$fh){
			$this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
			return false;
		}

		if(!$this->imageFieldArr) $this->imageFieldArr = DwcArchiverImage::getImageArr($this->schemaType);

		//Output header
		$headerArr = array_keys($this->imageFieldArr['fields']);
		array_pop($headerArr);
		$this->writeOutRecord($fh,$headerArr);

		//Output records
		$sql = DwcArchiverImage::getSqlImages($this->imageFieldArr['fields'], $this->conditionSql, $this->redactLocalities, $this->rareReaderArr);
		if($rs = $this->dataConn->query($sql,MYSQLI_USE_RESULT)){
			$this->setServerDomain();
			$urlPathPrefix = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1)=='/'?'':'/');

			$localDomain = '';
			if(isset($GLOBALS['IMAGE_DOMAIN']) && $GLOBALS['IMAGE_DOMAIN']){
				$localDomain = $GLOBALS['IMAGE_DOMAIN'];
			}
			else{
				$localDomain = $this->serverDomain;
			}
			$previousImgID = 0;
			while($r = $rs->fetch_assoc()){
				if($previousImgID == $r['imgID']) continue;
				$previousImgID = $r['imgID'];
				unset($r['imgID']);
				if(substr($r['identifier'],0,1) == '/') $r['identifier'] = $localDomain.$r['identifier'];
				if(substr($r['accessURI'],0,1) == '/') $r['accessURI'] = $localDomain.$r['accessURI'];
				if(substr($r['thumbnailAccessURI'],0,1) == '/') $r['thumbnailAccessURI'] = $localDomain.$r['thumbnailAccessURI'];
				if(substr($r['goodQualityAccessURI'],0,1) == '/') $r['goodQualityAccessURI'] = $localDomain.$r['goodQualityAccessURI'];

				if($r['goodQualityAccessURI'] == 'empty' || substr($r['goodQualityAccessURI'],0,10) == 'processing') $r['goodQualityAccessURI'] = '';
				if(substr($r['thumbnailAccessURI'],0,10) == 'processing') $r['thumbnailAccessURI'] = '';
				if($this->schemaType != 'backup'){
					if(stripos($r['rights'],'http://creativecommons.org') === 0){
						$r['webstatement'] = $r['rights'];
						$r['rights'] = '';
						if(!$r['usageterms']){
							if($r['webstatement'] == 'http://creativecommons.org/publicdomain/zero/1.0/'){
								$r['usageterms'] = 'CC0 1.0 (Public-domain)';
							}
							elseif($r['webstatement'] == 'http://creativecommons.org/licenses/by/3.0/'){
								$r['usageterms'] = 'CC BY (Attribution)';
							}
							elseif($r['webstatement'] == 'http://creativecommons.org/licenses/by-sa/3.0/'){
								$r['usageterms'] = 'CC BY-SA (Attribution-ShareAlike)';
							}
							elseif($r['webstatement'] == 'http://creativecommons.org/licenses/by-nc/3.0/'){
								$r['usageterms'] = 'CC BY-NC (Attribution-Non-Commercial)';
							}
							elseif($r['webstatement'] == 'http://creativecommons.org/licenses/by-nc-sa/3.0/'){
								$r['usageterms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
							}
						}
					}
					if(!$r['usageterms']) $r['usageterms'] = 'CC BY-NC-SA (Attribution-NonCommercial-ShareAlike)';
				}
				$r['providermanagedid'] = 'urn:uuid:'.$r['providermanagedid'];
				$r['associatedSpecimenReference'] = $urlPathPrefix.'collections/individual/index.php?occid='.$r['occid'];
				$r['type'] = 'StillImage';
				$r['subtype'] = 'Photograph';
				$extStr = strtolower(substr($r['accessURI'],strrpos($r['accessURI'],'.')+1));
				if($r['format'] == ''){
					if($extStr == 'jpg' || $extStr == 'jpeg'){
						$r['format'] = 'image/jpeg';
					}
					elseif($extStr == 'gif'){
						$r['format'] = 'image/gif';
					}
					elseif($extStr == 'png'){
						$r['format'] = 'image/png';
					}
					elseif($extStr == 'tiff' || $extStr == 'tif'){
						$r['format'] = 'image/tiff';
					}
					else{
						$r['format'] = '';
					}
				}
				$r['metadataLanguage'] = 'en';
				//Load record array into output file
				//$this->encodeArr($r);
				//$this->addcslashesArr($r);
				$this->writeOutRecord($fh,$r);
			}
			$rs->free();
		}
		else{
			$this->logOrEcho("ERROR creating image extension file: ".$this->dataConn->error."\n");
			$this->logOrEcho("\tSQL: ".$sql."\n");
		}

		fclose($fh);

    	$this->logOrEcho('Done! ('.date('h:i:s A').")\n");
	}

	private function writeAttributeFile(){
		$this->logOrEcho("Creating occurrence Attributes file as MeasurementsOrFact extension (".date('h:i:s A').")... ");
		$filePath = $this->targetPath.$this->ts.'-attr'.$this->fileExt;
		$fh = fopen($filePath, 'w');
		if(!$fh){
			$this->logOrEcho('ERROR establishing output file ('.$filePath.'), perhaps target folder is not readable by web server.');
			return false;
		}

		if(!$this->attributeFieldArr) $this->attributeFieldArr = DwcArchiverAttribute::getFieldArr();

		//Output header
		$this->writeOutRecord($fh,array_keys($this->attributeFieldArr['fields']));

		//Output records
		$sql = DwcArchiverAttribute::getSql($this->attributeFieldArr['fields'],$this->conditionSql);
		//echo $sql; exit;
		if($rs = $this->dataConn->query($sql,MYSQLI_USE_RESULT)){
			while($r = $rs->fetch_assoc()){
				$this->encodeArr($r);
				$this->addcslashesArr($r);
				$this->writeOutRecord($fh,$r);
			}
			$rs->free();
		}
		else{
			$this->logOrEcho("ERROR creating attribute (MeasurementOrFact) extension file: ".$this->dataConn->error."\n");
			$this->logOrEcho("\tSQL: ".$sql."\n");
		}

		fclose($fh);
    	$this->logOrEcho('Done! ('.date('h:i:s A').")\n");
	}

	private function writeOutRecord($fh,$outputArr){
		if($this->delimiter == ","){
			fputcsv($fh, $outputArr);
		}
		else{
			foreach($outputArr as $k => $v){
				$outputArr[$k] = str_replace($this->delimiter,'',$v);
			}
			fwrite($fh, implode($this->delimiter,$outputArr)."\n");
		}
	}

	public function deleteArchive($collid){
		//Remove archive instance from RSS feed
		$rssFile = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1)=='/'?'':'/').'webservices/dwc/rss.xml';
		if(!file_exists($rssFile)) return false;
		$doc = new DOMDocument();
		$doc->load($rssFile);
		$cElem = $doc->getElementsByTagName("channel")->item(0);
		$items = $cElem->getElementsByTagName("item");
		foreach($items as $i){
			if($i->getAttribute('collid') == $collid){
				$link = $i->getElementsByTagName("link");
				$nodeValue = $link->item(0)->nodeValue;
				$filePath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1)=='/'?'':'/');
				$filePath1 = $filePath.'content/dwca'.substr($nodeValue,strrpos($nodeValue,'/'));
				if(file_exists($filePath1)) unlink($filePath1);
				$emlPath1 = str_replace('.zip','.eml',$filePath1);
				if(file_exists($emlPath1)) unlink($emlPath1);
				//Following lines temporarly needed to support previous versions
				$filePath2 = $filePath.'collections/datasets/dwc'.substr($nodeValue,strrpos($nodeValue,'/'));
				if(file_exists($filePath2)) unlink($filePath2);
				$emlPath2 = str_replace('.zip','.eml',$filePath2);
				if(file_exists($emlPath2)) unlink($emlPath2);
				$cElem->removeChild($i);
			}
		}
		$doc->save($rssFile);
		//Remove DWCA path from database
		$sql = 'UPDATE omcollections SET dwcaUrl = NULL WHERE collid = '.$collid;
		if(!$this->conn->query($sql)){
			$this->logOrEcho('ERROR nullifying dwcaUrl while removing DWCA instance: '.$this->conn->error);
			return false;
		}
		return true;
	}

	// misc support functions
	//getters, setters, and misc functions
	public function setOverrideConditionLimit($bool){
		if($bool) $this->overrideConditionLimit = true;
		else $this->overrideConditionLimit = false;
	}

	public function setSchemaType($type){
		//dwc, symbiota, backup, coge
		if(in_array($type, array('dwc','backup','coge','pensoft'))){
			$this->schemaType = $type;
		}
		else{
			$this->schemaType = 'symbiota';
		}
	}

	public function setLimitToGuids($testValue){
		if($testValue) $this->limitToGuids = true;
	}

	public function setExtended($e){
		$this->extended = $e;
	}

	public function setDelimiter($d){
		if($d == 'tab' || $d == "\t"){
			$this->delimiter = "\t";
			$this->fileExt = '.tab';
		}
		elseif($d == 'csv' || $d == 'comma' || $d == ','){
			$this->delimiter = ",";
			$this->fileExt = '.csv';
		}
		else{
			$this->delimiter = $d;
			$this->fileExt = '.txt';
		}
	}

	public function setIncludeDets($includeDets){
		$this->includeDets = $includeDets;
	}

	public function setIncludeImgs($includeImgs){
		$this->includeImgs = $includeImgs;
	}

	public function setIncludeAttributes($include){
		$this->includeAttributes = $include;
	}

	public function setIncludeMaterialSample($include){
		$this->includeMaterialSample = $include;
	}

	public function hasAttributes(){
		$bool = false;
		$sql = 'SELECT occid FROM tmattributes LIMIT 1';
		$rs = $this->conn->query($sql);
		if($rs->num_rows) $bool = true;
		$rs->free();
		return $bool;
	}

	public function hasMaterialSamples(){
		$bool = false;
		$sql = 'SELECT occid FROM ommaterialsample LIMIT 1';
		if($rs = $this->conn->query($sql)){
			if($rs->num_rows) $bool = true;
			$rs->free();
		}
		return $bool;
	}

	public function setRedactLocalities($redact){
		$this->redactLocalities = $redact;
	}

	public function setRareReaderArr($approvedCollid){
		if(is_array($approvedCollid)){
			$this->rareReaderArr = $approvedCollid;
		}
		elseif(is_string($approvedCollid)){
			$this->rareReaderArr = explode(',',$approvedCollid);
		}
	}

	public function setIsPublicDownload(){
		$this->isPublicDownload = true;
	}

	public function setCharSetOut($cs){
		$cs = strtoupper($cs);
		if($cs == 'ISO-8859-1' || $cs == 'UTF-8'){
			$this->charSetOut = $cs;
		}
	}

	public function setGeolocateVariables($geolocateArr){
		$this->geolocateVariables = $geolocateArr;
	}

	//Misc functions
	public function setServerDomain($domain = ''){
		if($domain){
			$this->serverDomain = $domain;
		}
		elseif(!$this->serverDomain){
			$this->serverDomain = "http://";
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $this->serverDomain = "https://";
			$this->serverDomain .= $_SERVER["SERVER_NAME"];
			if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER['SERVER_PORT'] != 443) $this->serverDomain .= ':'.$_SERVER["SERVER_PORT"];
		}
	}

	public function getServerDomain(){
		$this->setServerDomain();
		return $this->serverDomain;
	}

	protected function utf8EncodeArr($inArr){
		$retArr = $inArr;
		if($this->charSetSource == 'ISO-8859-1'){
			foreach($retArr as $k => $v){
				if(is_array($v)){
					$retArr[$k] = $this->utf8EncodeArr($v);
				}
				elseif(is_string($v)){
					if(mb_detect_encoding($v,'UTF-8,ISO-8859-1',true) == "ISO-8859-1"){
						$retArr[$k] = utf8_encode($v);
					}
				}
				else{
					$retArr[$k] = $v;
				}
			}
		}
		return $retArr;
	}

	private function encodeArr(&$inArr){
		if($this->charSetSource && $this->charSetOut != $this->charSetSource){
			foreach($inArr as $k => $v){
				$inArr[$k] = $this->encodeStr($v);
			}
		}
	}

	private function encodeStr($inStr){
		$retStr = $inStr;
		if($inStr && $this->charSetSource){
			if($this->charSetOut == 'UTF-8' && $this->charSetSource == 'ISO-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) == "ISO-8859-1"){
					$retStr = utf8_encode($inStr);
					//$retStr = iconv("ISO-8859-1//TRANSLIT","UTF-8",$inStr);
				}
			}
			elseif($this->charSetOut == "ISO-8859-1" && $this->charSetSource == 'UTF-8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') == "UTF-8"){
					$retStr = utf8_decode($inStr);
					//$retStr = iconv("UTF-8","ISO-8859-1//TRANSLIT",$inStr);
				}
			}
		}
		return $retStr;
	}

	private function addcslashesArr(&$arr){
		foreach($arr as $k => $v){
			if($v) $arr[$k] = addcslashes($v,"\n\r\\");
		}
	}
}
?>