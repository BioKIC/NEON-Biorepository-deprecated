
CREATE TABLE `ommaterialsample` (
  `msID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occid` INT UNSIGNED NOT NULL,
  `sampleType` VARCHAR(45) NOT NULL,
  barcode/catalogNumber
  `guid` VARCHAR(150) NULL,
   
   Condition, 
   Disposition
   Remarks
   preservationType 
   preparationType (preparationProcess, preparationMaterials, preparedBy, preparationDate)
   individualCount
   storageLocation

  `sampleSize` VARCHAR(45) NULL,
  `dynamicProperties` TEXT NULL,
  recordID
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`msID`),
  INDEX `FK_ommaterialsample_occid_idx` (`occid` ASC),
  CONSTRAINT `FK_ommaterialsample_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE);

dynamicAttributes
  `concentration` DOUBLE NULL COMMENT 'Concentration of DNA (weight ng/volume µl)',
  `concentrationUnit` VARCHAR(45) NULL COMMENT 'Examples: ng/µl',
  `concentrationMethod` VARCHAR(45) NULL COMMENT 'Examples: Nanodrop, Qubit',
  `ratioOfAbsorbance260_230` DOUBLE NULL,
  `ratioOfAbsorbance260_280` DOUBLE NULL,
  `volume` DOUBLE NULL,
  `volumeUnit` VARCHAR(45) NULL COMMENT 'Examples: µl, ml',
  `weight` DOUBLE NULL,
  `weightUnit` VARCHAR(45) NULL COMMENT 'Examples: ng, g',
  `weightMethod` VARCHAR(45) NULL COMMENT 'Examples: Agarose gel, bioanalyzer, tape station',
  `purificationMethod` VARCHAR(45) NULL COMMENT 'Examples: QIAquick Purification Kit Qiagen',
  `quality` VARCHAR(45) NULL,
  `qualityRemarks` VARCHAR(45) NULL,
  `qualityCheckDate` VARCHAR(45) NULL,
  `sieving` VARCHAR(45) NULL,
  `dnaHybridization` VARCHAR(45) NULL,
  `dnaMeltingPoint` VARCHAR(45) NULL,
  `estimatedSize` VARCHAR(45) NULL,
  `poolDnaExtracts` VARCHAR(45) NULL,
  `sampleDesignation` VARCHAR(45) NULL,



INSERT INTO ctcontrolvocab(title,tableName,fieldName, limitToList)
  VALUES("Material Sample Type","ommaterialsample","materialSampleType",1);

INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "tissue", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "culture strain", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "specimen", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "DNA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "RNA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Protein", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";


CREATE TABLE `ommatsampamplification` (
  `msAmpID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `msID` INT UNSIGNED NOT NULL,
  `eventDate` DATE NULL,
  `performedBy` VARCHAR(45) NULL,
  `ampStatus` VARCHAR(45) NULL COMMENT 'true/false or yes/no whether the ampliciation was successful in general; highly recommended to report unsuccessful runs',
  `ampStatusDetails` VARCHAR(250) NULL COMMENT 'Details about the amplification, e.g. including why it has failed',
  `method` VARCHAR(45) NULL COMMENT 'Method used for amplification',
  `primerSequenceForward` TEXT NULL COMMENT 'Sequence of forward primer used for this amplification',
  `primerNameForward` VARCHAR(150) NULL COMMENT 'Name of forward primer used for this amplification',
  `primerRefCitationForward` VARCHAR(250) NULL COMMENT 'First reference of the primer',
  `primerRefLinkForward` VARCHAR(250) NULL COMMENT 'Link to the first reference of the primer',
  `primerSequenceReverse` TEXT NULL COMMENT 'Sequence of reverse primer used for this amplification',
  `primerNameReverse` VARCHAR(150) NULL COMMENT 'Name of reverse primer used for this amplification',
  `primerRefCitationReverse` VARCHAR(250) NULL COMMENT 'First reference of the primer',
  `primerRefLinkReverse` VARCHAR(250) NULL COMMENT 'Link to the first reference of the primer',
  `purificationMethod` VARCHAR(45) NULL COMMENT 'Method or protocol used to purify the PCR product',
  `consensusSequence` TEXT NULL COMMENT 'Consensus sequence derived from all individual sequences',
  `consensusSeqLength` INT NULL COMMENT 'Length of the consensus sequence (number of base pairs)',
  `consensusSeqChromFileURI` VARCHAR(250) NULL COMMENT 'Link to the chromatogram of the consensus sequence',
  `barcodeSequence` VARCHAR(45) NULL COMMENT 'DNA Barcode sequence (part or 100% of the consensus sequence)',
  `haplotype` VARCHAR(45) NULL,
  `marker` VARCHAR(45) NULL COMMENT 'Genetic locus/marker or DNA fragment amplified by PCR',
  `markerSubfragment` VARCHAR(45) NULL COMMENT 'Name of subfragment of a gene or locus. Important to e.g. identify special regions on marker genes',
  `geneticAccessionNumber` VARCHAR(45) NULL COMMENT 'Definite number or ID under which the DNA sequence is deposited in a public database (GenBank/EMBL/DDBJ accession number)',
  `boldProcessID` VARCHAR(45) NULL COMMENT 'Definite number or ID under which the DNA sequence is deposited in the BOLD database',
  `geneticAccessionURI` VARCHAR(150) NULL COMMENT 'URI of the related record in a public database (GenBank/DDBJ/EMBL record).',
  `gcContent` VARCHAR(45) NULL COMMENT 'guanine-cytosine content in mol %',
  `chimeraCheck` TEXT NULL COMMENT 'A chimeric sequence, or chimera for short, is a sequence comprised of two or more phylogenetically distinct parent sequences. Chimeras are usually PCR artifacts thought to occur when a prematurely terminated amplicon reanneals to a foreign DNA strand and is copied to completion in the following PCR cycles. The point at which the chimeric sequence changes from one parent to the next is called the breakpoint or conversion point',
  `assembly` VARCHAR(45) NULL COMMENT 'How was the assembly done (e.g. with a text based assembler like phrap or a flowgram assembler etc). Input: CV',
  `sop` VARCHAR(45) NULL COMMENT 'Relevant standard operating procedures',
  `finishingStrategy` VARCHAR(45) NULL COMMENT 'Was the genome project intended to produce a complete or draft genome, Coverage, the fold coverage of the sequencing expressed as 2x, 3x, 18x etc, and how many contigs were produced for the genome',
  `annotationSource` VARCHAR(45) NULL COMMENT 'For cases where annotation was provided by a community jamboree or model organism database rather than by a specific submitter',
  `markerAccordance` VARCHAR(45) NULL COMMENT 'Result of comparison of two markers of two specimens or strains. Name or TAX-ID (NCBI) of compared specimens/strain and the relative identity percentage must be given',
  `seqQualityCheck` VARCHAR(45) NULL COMMENT 'Indicate if the sequence has been called by automatic systems (none) or undergone a manual editing procedure (e.g. by inspecting the raw data or chromatograms). Applied only for sequences that are not submitted to SRA or DRA e.g. none or manually edited',
  `adapters` VARCHAR(45) NULL COMMENT 'Adapters provide priming sequences for both amplification and sequencing of the sample-library fragments. Both adapters should be reported; in uppercase letters',
  `multiplexIdentifiers` VARCHAR(45) NULL COMMENT 'Molecular barcodes, called Multiplex Identifiers (MIDs), that are used to specifically tag unique samples in a sequencing run. Sequence should be reported in uppercase letters',
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`msAmpID`),
  INDEX `FK_ommatsampamplification_msID_idx` (`msID` ASC),
  CONSTRAINT `FK_ommatsampamplification_msID`  FOREIGN KEY (`msID`)  REFERENCES `ommaterialsample` (`msID`)  ON DELETE CASCADE  ON UPDATE CASCADE)
COMMENT = 'https://tools.gbif.org/dwca-validator/extension.do?id=http://data.ggbn.org/schemas/ggbn/terms/Amplification';

CREATE TABLE `ommatsampcloning` (
  `msCloneID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `msID` INT UNSIGNED NOT NULL,
  `eventDate` DATE NULL,
  `performedBy` VARCHAR(45) NULL COMMENT 'Person or Institution who performed the cloning',
  `cloningMethod` VARCHAR(45) NULL COMMENT 'Method or protocol used for DNA cloning',
  `cloneStrain` VARCHAR(45) NULL COMMENT 'Name of the individual DNA clone',
  `primerNameForward` VARCHAR(150) NULL COMMENT 'Name of forward primer used for this cloning',
  `primerRefCitationForward` VARCHAR(250) NULL COMMENT 'First reference of this cloning primer',
  `primerRefLinkForward` VARCHAR(250) NULL COMMENT 'Link to the first reference of this cloning primer',
  `primerNameReverse` VARCHAR(150) NULL COMMENT 'Name of reverse primer used for this cloning',
  `primerRefCitationReverse` VARCHAR(250) NULL COMMENT 'First reference of this cloning primer',
  `primerRefLinkReverse` VARCHAR(250) NULL COMMENT 'Link to the first reference of this cloning primer',
  `libraryReadsSeqd` VARCHAR(45) NULL COMMENT 'Total number of clones sequenced from the library',
  `libraryScreen` VARCHAR(45) NULL COMMENT 'Specific enrichment or screening methods applied before and/or after creating clone libraries in order to select a specific group of sequences e.g. enriched, screened, normalized',
  `librarySize` VARCHAR(45) NULL COMMENT 'Total number of clones in the library prepared for the project',
  `libraryVector` VARCHAR(45) NULL COMMENT 'Cloning vector type(s) used in construction of libraries',
  `libraryConstructionMethod` VARCHAR(45) NULL COMMENT 'Library construction method used for clone libraries e.g. paired-end,single,vector',
  `plasmID` VARCHAR(45) NULL COMMENT 'Name of plasmid used for sequencing',
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`msCloneID`),
  INDEX `FK_ommatsampcloning_msID_idx` (`msID` ASC),
  CONSTRAINT `FK_ommatsampcloning_msID`  FOREIGN KEY (`msID`)  REFERENCES `ommaterialsample` (`msID`)  ON DELETE CASCADE  ON UPDATE CASCADE)
COMMENT = 'https://tools.gbif.org/dwca-validator/extension.do?id=http://data.ggbn.org/schemas/ggbn/terms/Cloning';


