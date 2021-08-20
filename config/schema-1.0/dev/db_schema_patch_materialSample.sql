CREATE TABLE `ommaterialsample` (
  `matSampleID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occid` INT UNSIGNED NOT NULL,
  `sampleType` VARCHAR(45) NOT NULL,
  `catalogNumber` VARCHAR(45) NULL,
  `guid` VARCHAR(150) NULL,
  `sampleCondition` VARCHAR(45) NULL,
  `disposition` VARCHAR(45) NULL,
  `preservationType` VARCHAR(45) NULL,
  `preparationDetails` VARCHAR(250) NULL,
  `preparationDate` DATETIME NULL,
  `preparedByUid` INT UNSIGNED NULL,
  `individualCount` VARCHAR(45) NULL,
  `sampleSize` VARCHAR(45) NULL,
  `storageLocation` VARCHAR(45) NULL,
  `remarks` VARCHAR(250) NULL,
  `dynamicFields` JSON NULL,
  `recordID` VARCHAR(45) NULL,
  `initialtimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`matSampleID`),
  INDEX `FK_ommatsample_occid_idx` (`occid` ASC),
  INDEX `FK_ommatsample_prepUid_idx` (`preparedByUid` ASC),
  CONSTRAINT `FK_ommatsample_occid` FOREIGN KEY (`occid`)   REFERENCES `omoccurrences` (`occid`)   ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_ommatsample_prepUid`   FOREIGN KEY (`preparedByUid`)   REFERENCES `users` (`uid`)   ON DELETE CASCADE  ON UPDATE CASCADE);


INSERT INTO ctcontrolvocab(title,tableName,fieldName, limitToList)
  VALUES("Material Sample Type","ommaterialsample","materialSampleType",1);

INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "tissue", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "culture strain", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "specimen", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "DNA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "RNA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Protein", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "materialSampleType";


CREATE TABLE `ommaterialsampleextended` (
  `matSampleExtendedID` INT NOT NULL AUTO_INCREMENT,
  `matSampleID` INT UNSIGNED NOT NULL,
  `fieldName` VARCHAR(45) NOT NULL,
  `fieldValue` VARCHAR(250) NOT NULL,
  `fieldUnits` VARCHAR(45) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`matSampleExtendedID`),
  INDEX `FK_matsampleextend_matSampleID_idx` (`matSampleID` ASC),
  INDEX `IX_matsampleextend_fieldName` (`fieldName` ASC),
  INDEX `IX_matsampleextend_fieldValue` (`fieldValue` ASC),
  CONSTRAINT `FK_matsampleextend_matSampleID`  FOREIGN KEY (`matSampleID`)   REFERENCES `ommaterialsample` (`matSampleID`)   ON DELETE CASCADE   ON UPDATE CASCADE);


INSERT INTO ctcontrolvocab(title,tableName,fieldName, limitToList)
  VALUES("Material Sample Type","ommaterialsampleextended","fieldName",0);

INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "concentration", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "concentrationMethod", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "ratioOfAbsorbance260_230", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "ratioOfAbsorbance260_280", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "volume", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "weight", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "weightMethod", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "purificationMethod", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "quality", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "qualityRemarks", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "qualityCheckDate", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "sieving", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "dnaHybridization", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "dnaMeltingPoint", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "estimatedSize", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "poolDnaExtracts", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "sampleDesignation", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";




//Skip following for now
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


