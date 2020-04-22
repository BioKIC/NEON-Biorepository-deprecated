<?php
class DwcArchiverOccurrence{

	private $occurDefArr = array();
	private $schemaType;
	private $extended = false;
	private $includePaleo = false;

	public function getOccurrenceArr(){
		if($this->schemaType == 'pensoft') $this->occurDefArr['fields']['Taxon_Local_ID'] = 'v.tid AS Taxon_Local_ID';
		else $this->occurDefArr['fields']['id'] = 'o.occid';
		$this->occurDefArr['terms']['institutionCode'] = 'http://rs.tdwg.org/dwc/terms/institutionCode';
		$this->occurDefArr['fields']['institutionCode'] = 'IFNULL(o.institutionCode,c.institutionCode) AS institutionCode';
		$this->occurDefArr['terms']['collectionCode'] = 'http://rs.tdwg.org/dwc/terms/collectionCode';
		$this->occurDefArr['fields']['collectionCode'] = 'IFNULL(o.collectionCode,c.collectionCode) AS collectionCode';
		$this->occurDefArr['terms']['ownerInstitutionCode'] = 'http://rs.tdwg.org/dwc/terms/ownerInstitutionCode';
		$this->occurDefArr['fields']['ownerInstitutionCode'] = 'o.ownerInstitutionCode';
		$this->occurDefArr['terms']['collectionID'] = 'http://rs.tdwg.org/dwc/terms/collectionID';
		$this->occurDefArr['fields']['collectionID'] = 'IFNULL(o.collectionID, c.collectionguid) AS collectionID';
		$this->occurDefArr['terms']['basisOfRecord'] = 'http://rs.tdwg.org/dwc/terms/basisOfRecord';
		$this->occurDefArr['fields']['basisOfRecord'] = 'o.basisOfRecord';
		$this->occurDefArr['terms']['occurrenceID'] = 'http://rs.tdwg.org/dwc/terms/occurrenceID';
		$this->occurDefArr['fields']['occurrenceID'] = 'o.occurrenceID';
		$this->occurDefArr['terms']['catalogNumber'] = 'http://rs.tdwg.org/dwc/terms/catalogNumber';
		$this->occurDefArr['fields']['catalogNumber'] = 'o.catalogNumber';
		$this->occurDefArr['terms']['otherCatalogNumbers'] = 'http://rs.tdwg.org/dwc/terms/otherCatalogNumbers';
		$this->occurDefArr['fields']['otherCatalogNumbers'] = 'o.otherCatalogNumbers';
		$this->occurDefArr['terms']['kingdom'] = 'http://rs.tdwg.org/dwc/terms/kingdom';
		$this->occurDefArr['fields']['kingdom'] = '';
		$this->occurDefArr['terms']['phylum'] = 'http://rs.tdwg.org/dwc/terms/phylum';
		$this->occurDefArr['fields']['phylum'] = '';
		$this->occurDefArr['terms']['class'] = 'http://rs.tdwg.org/dwc/terms/class';
		$this->occurDefArr['fields']['class'] = '';
		$this->occurDefArr['terms']['order'] = 'http://rs.tdwg.org/dwc/terms/order';
		$this->occurDefArr['fields']['order'] = '';
		$this->occurDefArr['terms']['family'] = 'http://rs.tdwg.org/dwc/terms/family';
		$this->occurDefArr['fields']['family'] = 'o.family';
		$this->occurDefArr['terms']['scientificName'] = 'http://rs.tdwg.org/dwc/terms/scientificName';
		$this->occurDefArr['fields']['scientificName'] = 'o.sciname AS scientificName';
		//$this->occurDefArr['terms']['verbatimScientificName'] = 'http://symbiota.org/terms/verbatimScientificName';
		//$this->occurDefArr['fields']['verbatimScientificName'] = 'o.scientificname AS verbatimScientificName';
		$this->occurDefArr['terms']['taxonID'] = 'http://rs.tdwg.org/dwc/terms/taxonID';
		$this->occurDefArr['fields']['taxonID'] = 'o.tidinterpreted as taxonID';
		$this->occurDefArr['terms']['scientificNameAuthorship'] = 'http://rs.tdwg.org/dwc/terms/scientificNameAuthorship';
		$this->occurDefArr['fields']['scientificNameAuthorship'] = 'IFNULL(t.author,o.scientificNameAuthorship) AS scientificNameAuthorship';
		$this->occurDefArr['terms']['genus'] = 'http://rs.tdwg.org/dwc/terms/genus';
		$this->occurDefArr['fields']['genus'] = 'IF(t.rankid >= 180,CONCAT_WS(" ",t.unitind1,t.unitname1),NULL) AS genus';
		$this->occurDefArr['terms']['specificEpithet'] = 'http://rs.tdwg.org/dwc/terms/specificEpithet';
		$this->occurDefArr['fields']['specificEpithet'] = 'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet';
		$this->occurDefArr['terms']['taxonRank'] = 'http://rs.tdwg.org/dwc/terms/taxonRank';
		$this->occurDefArr['fields']['taxonRank'] = 't.unitind3 AS taxonRank';
		$this->occurDefArr['terms']['infraspecificEpithet'] = 'http://rs.tdwg.org/dwc/terms/infraspecificEpithet';
		$this->occurDefArr['fields']['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
 		$this->occurDefArr['terms']['identifiedBy'] = 'http://rs.tdwg.org/dwc/terms/identifiedBy';
 		$this->occurDefArr['fields']['identifiedBy'] = 'o.identifiedBy';
 		$this->occurDefArr['terms']['dateIdentified'] = 'http://rs.tdwg.org/dwc/terms/dateIdentified';
 		$this->occurDefArr['fields']['dateIdentified'] = 'o.dateIdentified';
 		$this->occurDefArr['terms']['identificationReferences'] = 'http://rs.tdwg.org/dwc/terms/identificationReferences';
 		$this->occurDefArr['fields']['identificationReferences'] = 'o.identificationReferences';
 		$this->occurDefArr['terms']['identificationRemarks'] = 'http://rs.tdwg.org/dwc/terms/identificationRemarks';
 		$this->occurDefArr['fields']['identificationRemarks'] = 'o.identificationRemarks';
 		$this->occurDefArr['terms']['taxonRemarks'] = 'http://rs.tdwg.org/dwc/terms/taxonRemarks';
 		$this->occurDefArr['fields']['taxonRemarks'] = 'o.taxonRemarks';
 		$this->occurDefArr['terms']['identificationQualifier'] = 'http://rs.tdwg.org/dwc/terms/identificationQualifier';
 		$this->occurDefArr['fields']['identificationQualifier'] = 'o.identificationQualifier';
		$this->occurDefArr['terms']['typeStatus'] = 'http://rs.tdwg.org/dwc/terms/typeStatus';
		$this->occurDefArr['fields']['typeStatus'] = 'o.typeStatus';
		$this->occurDefArr['terms']['recordedBy'] = 'http://rs.tdwg.org/dwc/terms/recordedBy';
		$this->occurDefArr['fields']['recordedBy'] = 'o.recordedBy';
		//$this->occurDefArr['terms']['recordedByID'] = 'http://symbiota.org/terms/recordedByID';
		//$this->occurDefArr['fields']['recordedByID'] = 'o.recordedById';
		$this->occurDefArr['terms']['associatedCollectors'] = 'http://symbiota.org/terms/associatedCollectors';
		$this->occurDefArr['fields']['associatedCollectors'] = 'o.associatedCollectors';
		$this->occurDefArr['terms']['recordNumber'] = 'http://rs.tdwg.org/dwc/terms/recordNumber';
		$this->occurDefArr['fields']['recordNumber'] = 'o.recordNumber';
		$this->occurDefArr['terms']['eventDate'] = 'http://rs.tdwg.org/dwc/terms/eventDate';
		$this->occurDefArr['fields']['eventDate'] = 'o.eventDate';
		$this->occurDefArr['terms']['year'] = 'http://rs.tdwg.org/dwc/terms/year';
		$this->occurDefArr['fields']['year'] = 'o.year';
		$this->occurDefArr['terms']['month'] = 'http://rs.tdwg.org/dwc/terms/month';
		$this->occurDefArr['fields']['month'] = 'o.month';
		$this->occurDefArr['terms']['day'] = 'http://rs.tdwg.org/dwc/terms/day';
		$this->occurDefArr['fields']['day'] = 'o.day';
		$this->occurDefArr['terms']['startDayOfYear'] = 'http://rs.tdwg.org/dwc/terms/startDayOfYear';
		$this->occurDefArr['fields']['startDayOfYear'] = 'o.startDayOfYear';
		$this->occurDefArr['terms']['endDayOfYear'] = 'http://rs.tdwg.org/dwc/terms/endDayOfYear';
		$this->occurDefArr['fields']['endDayOfYear'] = 'o.endDayOfYear';
		$this->occurDefArr['terms']['verbatimEventDate'] = 'http://rs.tdwg.org/dwc/terms/verbatimEventDate';
		$this->occurDefArr['fields']['verbatimEventDate'] = 'o.verbatimEventDate';
		$this->occurDefArr['terms']['occurrenceRemarks'] = 'http://rs.tdwg.org/dwc/terms/occurrenceRemarks';
		$this->occurDefArr['terms']['habitat'] = 'http://rs.tdwg.org/dwc/terms/habitat';
		$this->occurDefArr['fields']['occurrenceRemarks'] = 'o.occurrenceRemarks';
		$this->occurDefArr['fields']['habitat'] = 'o.habitat';
		$this->occurDefArr['terms']['substrate'] = 'http://symbiota.org/terms/substrate';
		$this->occurDefArr['fields']['substrate'] = 'o.substrate';
		$this->occurDefArr['terms']['verbatimAttributes'] = 'http://symbiota.org/terms/verbatimAttributes';
		$this->occurDefArr['fields']['verbatimAttributes'] = 'o.verbatimAttributes';
		$this->occurDefArr['terms']['fieldNumber'] = 'http://rs.tdwg.org/dwc/terms/fieldNumber';
		$this->occurDefArr['fields']['fieldNumber'] = 'o.fieldNumber';
		$this->occurDefArr['terms']['informationWithheld'] = 'http://rs.tdwg.org/dwc/terms/informationWithheld';
		$this->occurDefArr['fields']['informationWithheld'] = 'o.informationWithheld';
		$this->occurDefArr['terms']['dataGeneralizations'] = 'http://rs.tdwg.org/dwc/terms/dataGeneralizations';
		$this->occurDefArr['fields']['dataGeneralizations'] = 'o.dataGeneralizations';
		$this->occurDefArr['terms']['dynamicProperties'] = 'http://rs.tdwg.org/dwc/terms/dynamicProperties';
		$this->occurDefArr['fields']['dynamicProperties'] = 'o.dynamicProperties';
		$this->occurDefArr['terms']['associatedTaxa'] = 'http://rs.tdwg.org/dwc/terms/associatedTaxa';
		$this->occurDefArr['fields']['associatedTaxa'] = 'o.associatedTaxa';
		$this->occurDefArr['terms']['reproductiveCondition'] = 'http://rs.tdwg.org/dwc/terms/reproductiveCondition';
		$this->occurDefArr['fields']['reproductiveCondition'] = 'o.reproductiveCondition';
		$this->occurDefArr['terms']['establishmentMeans'] = 'http://rs.tdwg.org/dwc/terms/establishmentMeans';
		$this->occurDefArr['fields']['establishmentMeans'] = 'o.establishmentMeans';
		$this->occurDefArr['terms']['cultivationStatus'] = 'http://symbiota.org/terms/cultivationStatus';
		$this->occurDefArr['fields']['cultivationStatus'] = 'cultivationStatus';
		$this->occurDefArr['terms']['lifeStage'] = 'http://rs.tdwg.org/dwc/terms/lifeStage';
		$this->occurDefArr['fields']['lifeStage'] = 'o.lifeStage';
		$this->occurDefArr['terms']['sex'] = 'http://rs.tdwg.org/dwc/terms/sex';
		$this->occurDefArr['fields']['sex'] = 'o.sex';
		$this->occurDefArr['terms']['individualCount'] = 'http://rs.tdwg.org/dwc/terms/individualCount';
		$this->occurDefArr['fields']['individualCount'] = 'CASE WHEN o.individualCount REGEXP("(^[0-9]+$)") THEN o.individualCount ELSE NULL END AS individualCount';
		//$this->occurDefArr['terms']['samplingProtocol'] = 'http://rs.tdwg.org/dwc/terms/samplingProtocol';
		//$this->occurDefArr['fields']['samplingProtocol'] = 'o.samplingProtocol';
		//$this->occurDefArr['terms']['samplingEffort'] = 'http://rs.tdwg.org/dwc/terms/samplingEffort';
		//$this->occurDefArr['fields']['samplingEffort'] = 'o.samplingEffort';
		$this->occurDefArr['terms']['preparations'] = 'http://rs.tdwg.org/dwc/terms/preparations';
		$this->occurDefArr['fields']['preparations'] = 'o.preparations';
		$this->occurDefArr['terms']['country'] = 'http://rs.tdwg.org/dwc/terms/country';
		$this->occurDefArr['fields']['country'] = 'o.country';
		$this->occurDefArr['terms']['stateProvince'] = 'http://rs.tdwg.org/dwc/terms/stateProvince';
		$this->occurDefArr['fields']['stateProvince'] = 'o.stateProvince';
		$this->occurDefArr['terms']['county'] = 'http://rs.tdwg.org/dwc/terms/county';
		$this->occurDefArr['fields']['county'] = 'o.county';
		$this->occurDefArr['terms']['municipality'] = 'http://rs.tdwg.org/dwc/terms/municipality';
		$this->occurDefArr['fields']['municipality'] = 'o.municipality';
		$this->occurDefArr['terms']['locality'] = 'http://rs.tdwg.org/dwc/terms/locality';
		$this->occurDefArr['fields']['locality'] = 'o.locality';
		$this->occurDefArr['terms']['locationRemarks'] = 'http://rs.tdwg.org/dwc/terms/locationRemarks';
		$this->occurDefArr['fields']['locationRemarks'] = 'o.locationremarks';
		$this->occurDefArr['terms']['localitySecurity'] = 'http://symbiota.org/terms/localitySecurity';
		$this->occurDefArr['fields']['localitySecurity'] = 'o.localitySecurity';
		$this->occurDefArr['terms']['localitySecurityReason'] = 'http://symbiota.org/terms/localitySecurityReason';
		$this->occurDefArr['fields']['localitySecurityReason'] = 'o.localitySecurityReason';
		$this->occurDefArr['terms']['decimalLatitude'] = 'http://rs.tdwg.org/dwc/terms/decimalLatitude';
		$this->occurDefArr['fields']['decimalLatitude'] = 'o.decimalLatitude';
		$this->occurDefArr['terms']['decimalLongitude'] = 'http://rs.tdwg.org/dwc/terms/decimalLongitude';
		$this->occurDefArr['fields']['decimalLongitude'] = 'o.decimalLongitude';
		$this->occurDefArr['terms']['geodeticDatum'] = 'http://rs.tdwg.org/dwc/terms/geodeticDatum';
		$this->occurDefArr['fields']['geodeticDatum'] = 'o.geodeticDatum';
		$this->occurDefArr['terms']['coordinateUncertaintyInMeters'] = 'http://rs.tdwg.org/dwc/terms/coordinateUncertaintyInMeters';
		$this->occurDefArr['fields']['coordinateUncertaintyInMeters'] = 'o.coordinateUncertaintyInMeters';
		//$this->occurDefArr['terms']['footprintWKT'] = 'http://rs.tdwg.org/dwc/terms/footprintWKT';
		//$this->occurDefArr['fields']['footprintWKT'] = 'o.footprintWKT';
		$this->occurDefArr['terms']['verbatimCoordinates'] = 'http://rs.tdwg.org/dwc/terms/verbatimCoordinates';
		$this->occurDefArr['fields']['verbatimCoordinates'] = 'o.verbatimCoordinates';
		$this->occurDefArr['terms']['georeferencedBy'] = 'http://rs.tdwg.org/dwc/terms/georeferencedBy';
		$this->occurDefArr['fields']['georeferencedBy'] = 'o.georeferencedBy';
		$this->occurDefArr['terms']['georeferenceProtocol'] = 'http://rs.tdwg.org/dwc/terms/georeferenceProtocol';
		$this->occurDefArr['fields']['georeferenceProtocol'] = 'o.georeferenceProtocol';
		$this->occurDefArr['terms']['georeferenceSources'] = 'http://rs.tdwg.org/dwc/terms/georeferenceSources';
		$this->occurDefArr['fields']['georeferenceSources'] = 'o.georeferenceSources';
		$this->occurDefArr['terms']['georeferenceVerificationStatus'] = 'http://rs.tdwg.org/dwc/terms/georeferenceVerificationStatus';
		$this->occurDefArr['fields']['georeferenceVerificationStatus'] = 'o.georeferenceVerificationStatus';
		$this->occurDefArr['terms']['georeferenceRemarks'] = 'http://rs.tdwg.org/dwc/terms/georeferenceRemarks';
		$this->occurDefArr['fields']['georeferenceRemarks'] = 'o.georeferenceRemarks';
		$this->occurDefArr['terms']['minimumElevationInMeters'] = 'http://rs.tdwg.org/dwc/terms/minimumElevationInMeters';
		$this->occurDefArr['fields']['minimumElevationInMeters'] = 'o.minimumElevationInMeters';
		$this->occurDefArr['terms']['maximumElevationInMeters'] = 'http://rs.tdwg.org/dwc/terms/maximumElevationInMeters';
		$this->occurDefArr['fields']['maximumElevationInMeters'] = 'o.maximumElevationInMeters';
		$this->occurDefArr['terms']['minimumDepthInMeters'] = 'http://rs.tdwg.org/dwc/terms/minimumDepthInMeters';
		$this->occurDefArr['fields']['minimumDepthInMeters'] = 'o.minimumDepthInMeters';
		$this->occurDefArr['terms']['maximumDepthInMeters'] = 'http://rs.tdwg.org/dwc/terms/maximumDepthInMeters';
		$this->occurDefArr['fields']['maximumDepthInMeters'] = 'o.maximumDepthInMeters';
		$this->occurDefArr['terms']['verbatimDepth'] = 'http://rs.tdwg.org/dwc/terms/verbatimDepth';
		$this->occurDefArr['fields']['verbatimDepth'] = 'o.verbatimDepth';
		$this->occurDefArr['terms']['verbatimElevation'] = 'http://rs.tdwg.org/dwc/terms/verbatimElevation';
		$this->occurDefArr['fields']['verbatimElevation'] = 'o.verbatimElevation';
		if($this->includePaleo){
			$this->occurDefArr['terms']['eon'] = 'http://symbiota.org/terms/paleo-eon';
			$this->occurDefArr['fields']['eon'] = 'paleo.eon';
			$this->occurDefArr['terms']['era'] = 'http://symbiota.org/terms/paleo-era';
			$this->occurDefArr['fields']['era'] = 'paleo.era';
			$this->occurDefArr['terms']['period'] = 'http://symbiota.org/terms/paleo-period';
			$this->occurDefArr['fields']['period'] = 'paleo.period';
			$this->occurDefArr['terms']['epoch'] = 'http://symbiota.org/terms/paleo-epoch';
			$this->occurDefArr['fields']['epoch'] = 'paleo.epoch';
			$this->occurDefArr['terms']['earlyInterval'] = 'http://symbiota.org/terms/paleo-earlyInterval';
			$this->occurDefArr['fields']['earlyInterval'] = 'paleo.earlyInterval';
			$this->occurDefArr['terms']['lateInterval'] = 'http://symbiota.org/terms/paleo-lateInterval';
			$this->occurDefArr['fields']['lateInterval'] = 'paleo.lateInterval';
			$this->occurDefArr['terms']['absoluteAge'] = 'http://symbiota.org/terms/paleo-absoluteAge';
			$this->occurDefArr['fields']['absoluteAge'] = 'paleo.absoluteAge';
			$this->occurDefArr['terms']['storageAge'] = 'http://symbiota.org/terms/paleo-storageAge';
			$this->occurDefArr['fields']['storageAge'] = 'paleo.storageAge';
			$this->occurDefArr['terms']['stage'] = 'http://symbiota.org/terms/paleo-stage';
			$this->occurDefArr['fields']['stage'] = 'paleo.stage';
			$this->occurDefArr['terms']['localStage'] = 'http://symbiota.org/terms/paleo-localStage';
			$this->occurDefArr['fields']['localStage'] = 'paleo.localStage';
			$this->occurDefArr['terms']['biota'] = 'http://symbiota.org/terms/paleo-biota';
			$this->occurDefArr['fields']['biota'] = 'paleo.biota';
			$this->occurDefArr['terms']['biostratigraphy'] = 'http://symbiota.org/terms/paleo-biostratigraphy';
			$this->occurDefArr['fields']['biostratigraphy'] = 'paleo.biostratigraphy';
			$this->occurDefArr['terms']['taxonEnvironment'] = 'http://symbiota.org/terms/paleo-taxonEnvironment';
			$this->occurDefArr['fields']['taxonEnvironment'] = 'paleo.taxonEnvironment';
			$this->occurDefArr['terms']['lithogroup'] = 'http://rs.tdwg.org/dwc/terms/group';
			$this->occurDefArr['fields']['lithogroup'] = 'paleo.lithogroup';
			$this->occurDefArr['terms']['formation'] = 'http://rs.tdwg.org/dwc/terms/formation';
			$this->occurDefArr['fields']['formation'] = 'paleo.formation';
			$this->occurDefArr['terms']['member'] = 'http://rs.tdwg.org/dwc/terms/member';
			$this->occurDefArr['fields']['member'] = 'paleo.member';
			$this->occurDefArr['terms']['bed'] = 'http://rs.tdwg.org/dwc/terms/bed';
			$this->occurDefArr['fields']['bed'] = 'paleo.bed';
			$this->occurDefArr['terms']['lithology'] = 'http://rs.tdwg.org/dwc/terms/lithostratigraphicTerms';
			$this->occurDefArr['fields']['lithology'] = 'paleo.lithology';
			$this->occurDefArr['terms']['stratRemarks'] = 'http://symbiota.org/terms/paleo-stratRemarks';
			$this->occurDefArr['fields']['stratRemarks'] = 'paleo.stratRemarks';
			$this->occurDefArr['terms']['lithDescription'] = 'http://symbiota.org/terms/paleo-lithDescription';
			$this->occurDefArr['fields']['lithDescription'] = 'paleo.lithDescription';
			$this->occurDefArr['terms']['element'] = 'http://symbiota.org/terms/paleo-element';
			$this->occurDefArr['fields']['element'] = 'paleo.element';
			$this->occurDefArr['terms']['slideProperties'] = 'http://symbiota.org/terms/paleo-slideProperties';
			$this->occurDefArr['fields']['slideProperties'] = 'paleo.slideProperties';
			$this->occurDefArr['terms']['geologicalContextID'] = 'http://rs.tdwg.org/dwc/terms/geologicalContextID';
			$this->occurDefArr['fields']['geologicalContextID'] = 'paleo.geologicalContextID';
		}
		$this->occurDefArr['terms']['disposition'] = 'http://rs.tdwg.org/dwc/terms/disposition';
		$this->occurDefArr['fields']['disposition'] = 'o.disposition';
		$this->occurDefArr['terms']['language'] = 'http://purl.org/dc/terms/language';
		$this->occurDefArr['fields']['language'] = 'o.language';
		//$this->occurDefArr['terms']['genericcolumn1'] = 'http://symbiota.org/terms/genericcolumn1';
		//$this->occurDefArr['fields']['genericcolumn1'] = 'o.genericcolumn1';
		//$this->occurDefArr['terms']['genericcolumn2'] = 'http://symbiota.org/terms/genericcolumn2';
		//$this->occurDefArr['fields']['genericcolumn2'] = 'o.genericcolumn2';
		//$this->occurDefArr['terms']['storageLocation'] = 'http://symbiota.org/terms/storageLocation';
		//$this->occurDefArr['fields']['storageLocation'] = 'o.storageLocation';
		$this->occurDefArr['terms']['observerUid'] = 'http://symbiota.org/terms/observerUid';
		$this->occurDefArr['fields']['observerUid'] = 'o.observeruid';
		$this->occurDefArr['terms']['processingStatus'] = 'http://symbiota.org/terms/processingStatus';
		$this->occurDefArr['fields']['processingStatus'] = 'o.processingstatus';
		$this->occurDefArr['terms']['duplicateQuantity'] = 'http://symbiota.org/terms/duplicateQuantity';
		$this->occurDefArr['fields']['duplicateQuantity'] = 'o.duplicateQuantity';
		$this->occurDefArr['terms']['recordEnteredBy'] = 'http://symbiota.org/terms/recordEnteredBy';
		$this->occurDefArr['fields']['recordEnteredBy'] = 'o.recordEnteredBy';
		$this->occurDefArr['terms']['dateEntered'] = 'http://symbiota.org/terms/dateEntered';
		$this->occurDefArr['fields']['dateEntered'] = 'o.dateEntered';
		$this->occurDefArr['terms']['dateLastModified'] = 'http://rs.tdwg.org/dwc/terms/dateLastModified';
		$this->occurDefArr['fields']['dateLastModified'] = 'o.datelastmodified';
		$this->occurDefArr['terms']['modified'] = 'http://purl.org/dc/terms/modified';
		$this->occurDefArr['fields']['modified'] = 'IFNULL(o.modified,o.datelastmodified) AS modified';
		$this->occurDefArr['terms']['rights'] = 'http://purl.org/dc/elements/1.1/rights';
		$this->occurDefArr['fields']['rights'] = 'c.rights';
		$this->occurDefArr['terms']['rightsHolder'] = 'http://purl.org/dc/terms/rightsHolder';
		$this->occurDefArr['fields']['rightsHolder'] = 'c.rightsHolder';
		$this->occurDefArr['terms']['accessRights'] = 'http://purl.org/dc/terms/accessRights';
		$this->occurDefArr['fields']['accessRights'] = 'c.accessRights';
		$this->occurDefArr['terms']['sourcePrimaryKey-dbpk'] = 'http://symbiota.org/terms/sourcePrimaryKey-dbpk';
		$this->occurDefArr['fields']['sourcePrimaryKey-dbpk'] = 'o.dbpk';
		$this->occurDefArr['terms']['collId'] = 'http://symbiota.org/terms/collId';
		$this->occurDefArr['fields']['collId'] = 'c.collid';
		$this->occurDefArr['terms']['recordId'] = 'http://portal.idigbio.org/terms/recordId';
		$this->occurDefArr['fields']['recordId'] = 'g.guid AS recordId';
		$this->occurDefArr['terms']['references'] = 'http://purl.org/dc/terms/references';
		$this->occurDefArr['fields']['references'] = '';
		if($this->schemaType == 'pensoft'){
			$this->occurDefArr['fields']['occid'] = 'o.occid';
		}

		foreach($this->occurDefArr as $k => $vArr){
			if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft'){
				$trimArr = array('recordedByID','associatedCollectors','substrate','verbatimAttributes','cultivationStatus',
					'localitySecurityReason','genericcolumn1','genericcolumn2','storageLocation','observerUid','processingStatus',
					'duplicateQuantity','dateEntered','dateLastModified','sourcePrimaryKey-dbpk');
				$this->occurDefArr[$k] = array_diff_key($vArr,array_flip($trimArr));
			}
			elseif($this->schemaType == 'symbiota'){
				$trimArr = array();
				if(!$this->extended){
					$trimArr = array('collectionID','rights','rightsHolder','accessRights','storageLocation','observerUid','processingStatus','duplicateQuantity','dateEntered','dateLastModified');
				}
				$this->occurDefArr[$k] = array_diff_key($vArr,array_flip($trimArr));
			}
			elseif($this->schemaType == 'backup'){
				$trimArr = array('collectionID','rights','rightsHolder','accessRights');
				$this->occurDefArr[$k] = array_diff_key($vArr,array_flip($trimArr));
			}
			elseif($this->schemaType == 'coge'){
				$targetArr = array('id','basisOfRecord','institutionCode','collectionCode','catalogNumber','occurrenceID','family','scientificName','scientificNameAuthorship',
					'kingdom','phylum','class','order','genus','specificEpithet','infraSpecificEpithet',
					'recordedBy','recordNumber','eventDate','year','month','day','fieldNumber','country','stateProvince','county','municipality',
					'locality','localitySecurity','geodeticDatum','decimalLatitude','decimalLongitude','verbatimCoordinates',
					'minimumElevationInMeters','maximumElevationInMeters','verbatimElevation','maximumDepthInMeters','minimumDepthInMeters',
					'sex','occurrenceRemarks','preparationType','individualCount','dateEntered','dateLastModified','recordId','references','collId');
				$this->occurDefArr[$k] = array_intersect_key($vArr,array_flip($targetArr));
			}
		}

		if($this->schemaType == 'dwc' || $this->schemaType == 'pensoft'){
			$this->occurDefArr['fields']['recordedBy'] = 'CONCAT_WS("; ",o.recordedBy,o.associatedCollectors) AS recordedBy';
			$this->occurDefArr['fields']['occurrenceRemarks'] = 'CONCAT_WS("; ",o.occurrenceRemarks,o.verbatimAttributes) AS occurrenceRemarks';
			$this->occurDefArr['fields']['habitat'] = 'CONCAT_WS("; ",o.habitat, o.substrate) AS habitat';
		}
		return $this->occurDefArr;
	}

	public function getSqlOccurrences($fieldArr, $fullSql = true){
		$sql = '';
		if($fullSql){
			$sqlFrag = '';
			foreach($fieldArr as $fieldName => $colName){
				if($colName){
					$sqlFrag .= ', '.$colName;
				}
				else{
					$sqlFrag .= ', "" AS t_'.$fieldName;
				}
			}
			$sql = 'SELECT DISTINCT '.trim($sqlFrag,', ');
		}
		$sql .= ' FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.collid '.
			'INNER JOIN guidoccurrences g ON o.occid = g.occid '.
			'LEFT JOIN taxa t ON o.tidinterpreted = t.TID ';
		if($this->includePaleo) $sql .= 'LEFT JOIN omoccurpaleo paleo ON o.occid = paleo.occid ';
		//if($fullSql) $sql .= ' ORDER BY c.collid ';
		//echo '<div>'.$sql.'</div>'; exit;
		return $sql;
	}

	public function setSchemaType($t){
		$this->schemaType = $t;
	}

	public function setExtended($e){
		if($e) $this->extended = true;
	}

	public function setIncludePaleo($bool){
		if($bool) $this->includePaleo = true;
	}
}
?>