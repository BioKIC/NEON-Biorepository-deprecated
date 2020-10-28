INSERT IGNORE INTO schemaversion (versionnumber) values ("1.2");

ALTER TABLE `adminlanguages` 
  ADD COLUMN `ISO 639-3` VARCHAR(3) NULL AFTER `iso639_2`;

ALTER TABLE `lkupstateprovince` 
  CHANGE COLUMN `abbrev` `abbrev` VARCHAR(3) NULL DEFAULT NULL ;

ALTER TABLE `fmprojects` 
  CHANGE COLUMN `fulldescription` `fulldescription` VARCHAR(5000) NULL DEFAULT NULL ;

ALTER TABLE `fmchklstprojlink` 
   ADD COLUMN `sortSequence` INT NULL AFTER `mapChecklist`;

ALTER TABLE `fmchklsttaxalink` 
  ADD INDEX `FK_chklsttaxalink_tid` (`TID` ASC);


ALTER TABLE `kmcharacters` 
  CHANGE COLUMN `helpurl` `helpurl` VARCHAR(500) NULL DEFAULT NULL AFTER `description`,
  ADD COLUMN `referenceUrl` VARCHAR(250) NULL AFTER `helpurl`;

ALTER TABLE `kmcharacters` 
  CHANGE COLUMN `sortsequence` `sortsequence` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `notes`,
  ADD COLUMN `glossid` INT UNSIGNED NULL AFTER `description`,
  ADD INDEX `FK_kmchar_glossary_idx` (`glossid` ASC);
ALTER TABLE `kmcharacters` 
  ADD CONSTRAINT `FK_kmchar_glossary`  FOREIGN KEY (`glossid`)  REFERENCES `glossary` (`glossid`)  ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `kmcharacterlang` 
  CHANGE COLUMN `language` `language` VARCHAR(45) NULL ;

ALTER TABLE `kmcharheading` 
  CHANGE COLUMN `language` `language` VARCHAR(45) NULL DEFAULT 'English' ;
CREATE TABLE `kmcharheadinglang` (
  `hid` INT UNSIGNED NOT NULL,
  `langid` INT NOT NULL,
  `headingname` VARCHAR(100) NOT NULL,
  `notes` VARCHAR(250) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`hid`, `langid`),
  CONSTRAINT `FK_kmcharheadinglang_hid`  FOREIGN KEY (`hid`)  REFERENCES `kmcharheading` (`hid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_kmcharheadinglang_langid`  FOREIGN KEY (`langid`)  REFERENCES `adminlanguage` (`langid`)  ON DELETE CASCADE  ON UPDATE CASCADE
);

ALTER TABLE `kmcs` 
  ADD COLUMN `referenceUrl` VARCHAR(250) NULL AFTER `IllustrationUrl`;

ALTER TABLE `kmcs` 
  ADD COLUMN `glossid` INT UNSIGNED NULL AFTER `referenceUrl`,
  ADD INDEX `FK_kmcs_glossid_idx` (`glossid` ASC);
ALTER TABLE `kmcs` 
  ADD CONSTRAINT `FK_kmcs_glossid`  FOREIGN KEY (`glossid`)  REFERENCES `glossary` (`glossid`)  ON DELETE SET NULL  ON UPDATE CASCADE;


ALTER TABLE `glossary` 
  ADD COLUMN `langid` INT UNSIGNED NULL AFTER `language`;

ALTER TABLE `glossaryimages` 
  ADD COLUMN `sortSequence` INT NULL AFTER `structures`;


ALTER TABLE `uploadspectemp` 
  ADD COLUMN `paleoJSON` TEXT NULL AFTER `exsiccatiNotes`;

ALTER TABLE `uploadspectemp` 
  CHANGE COLUMN `basisOfRecord` `basisOfRecord` VARCHAR(32) NULL DEFAULT NULL COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation' ;

ALTER TABLE `uploadspectemp` 
  ADD INDEX `Index_uploadspec_othercatalognumbers` (`otherCatalogNumbers` ASC);

ALTER TABLE `uploadimagetemp` 
  CHANGE COLUMN `specimengui` `sourceIdentifier` VARCHAR(150) NULL DEFAULT NULL;

ALTER TABLE `uploadimagetemp` 
  ADD COLUMN `sourceUrl` VARCHAR(255) NULL AFTER `owner`,
  ADD COLUMN `referenceurl` VARCHAR(255) NULL AFTER `sourceUrl`,
  ADD COLUMN `copyright` VARCHAR(255) NULL AFTER `referenceurl`,
  ADD COLUMN `accessrights` VARCHAR(255) NULL AFTER `copyright`,
  ADD COLUMN `rights` VARCHAR(255) NULL AFTER `accessrights`,
  ADD COLUMN `locality` VARCHAR(250) NULL AFTER `rights`;

ALTER TABLE `uploadimagetemp` 
  CHANGE COLUMN `url` `url` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `images` 
  CHANGE COLUMN `url` `url` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `images` 
  ADD INDEX `Index_images_datelastmod` (`InitialTimeStamp` ASC);

ALTER TABLE `images` 
  ADD COLUMN `sortOccurrence` INT NULL DEFAULT 5 AFTER `sortsequence`;

  
ALTER TABLE `uploadspecparameters` 
  CHANGE COLUMN `Path` `Path` VARCHAR(500) NULL DEFAULT NULL ;

ALTER TABLE `uploadtaxa` 
  CHANGE COLUMN `UnitInd3` `UnitInd3` VARCHAR(45) NULL DEFAULT NULL ;

ALTER TABLE `uploadtaxa` 
  DROP INDEX `UNIQUE_sciname` ,
  ADD UNIQUE INDEX `UNIQUE_sciname` (`SciName` ASC, `RankId` ASC, `Author` ASC, `AcceptedStr` ASC);


ALTER TABLE `taxa` 
  ADD COLUMN `locked` INT NULL AFTER `Hybrid`,
  CHANGE COLUMN `UnitInd3` `UnitInd3` VARCHAR(15) NULL DEFAULT NULL ;

ALTER TABLE `taxstatus` 
  ADD COLUMN `modifiedBy` VARCHAR(45) NULL AFTER `SortSequence`;

ALTER TABLE `taxstatus` 
  DROP INDEX `Index_hierarchy`;

ALTER TABLE `taxstatus` 
  DROP PRIMARY KEY,
  ADD PRIMARY KEY USING BTREE (`tid`, `taxauthid`);

ALTER TABLE `taxstatus` 
ADD INDEX `Index_tid` (`tid` ASC);

UPDATE taxavernaculars v INNER JOIN adminlanguages l ON v.language = l.langname 
SET v.langid = l.langid
WHERE v.langid IS NULL;
UPDATE taxavernaculars v INNER JOIN adminlanguages l ON v.language = l.iso639_1 
SET v.langid = l.langid
WHERE v.langid IS NULL;
UPDATE taxavernaculars v INNER JOIN adminlanguages l ON v.language = l.iso639_2 
SET v.langid = l.langid
WHERE v.langid IS NULL;

ALTER TABLE `taxavernaculars` 
  CHANGE COLUMN `Language` `Language` VARCHAR(15) NULL ,
  DROP INDEX `unique-key` ,
  ADD UNIQUE INDEX `unique-key` (`VernacularName` ASC, `TID` ASC, `langid` ASC);

ALTER TABLE `taxaresourcelinks` 
  ADD UNIQUE INDEX `UNIQUE_taxaresource` (`tid` ASC, `sourcename` ASC);

ALTER TABLE `taxadescrblock` 
  DROP FOREIGN KEY `FK_taxadescrblock_tid`;
ALTER TABLE `taxadescrblock` 
  DROP INDEX `Index_unique` ;
ALTER TABLE `taxadescrblock` 
  ADD INDEX `FK_taxadescrblock_tid_idx` (`tid` ASC);
ALTER TABLE `taxadescrblock` 
  ADD CONSTRAINT `FK_taxadescrblock_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)  ON DELETE RESTRICT  ON UPDATE CASCADE;
ALTER TABLE `taxadescrstmts` 
  CHANGE COLUMN `heading` `heading` VARCHAR(75) NULL ;


#Paleo tables
CREATE TABLE `omoccurpaleo` (
  `paleoID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occid` INT UNSIGNED NOT NULL,
  `eon` VARCHAR(65) NULL,
  `era` VARCHAR(65) NULL,
  `period` VARCHAR(65) NULL,
  `epoch` VARCHAR(65) NULL,
  `earlyInterval` VARCHAR(65) NULL,
  `lateInterval` VARCHAR(65) NULL,
  `absoluteAge` VARCHAR(65) NULL,
  `storageAge` VARCHAR(65) NULL,
  `stage` VARCHAR(65) NULL,
  `localStage` VARCHAR(65) NULL,
  `biota` VARCHAR(65) NULL COMMENT 'Flora or Fanua',
  `biostratigraphy` VARCHAR(65) NULL COMMENT 'Biozone',
  `taxonEnvironment` VARCHAR(65) NULL COMMENT 'Marine or not',
  `lithogroup` VARCHAR(65) NULL,
  `formation` VARCHAR(65) NULL,
  `member` VARCHAR(65) NULL,
  `bed` VARCHAR(65) NULL,
  `lithology` VARCHAR(250) NULL,
  `stratRemarks` VARCHAR(250) NULL,
  `element` VARCHAR(250) NULL,
  `slideProperties` VARCHAR(1000) NULL,
  `geologicalContextID` VARCHAR(45) NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`paleoID`),
  INDEX `FK_paleo_occid_idx` (`occid` ASC),
  UNIQUE INDEX `UNIQUE_occid` (`occid` ASC),
  CONSTRAINT `FK_paleo_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE
) COMMENT = 'Occurrence Paleo tables';


#ALTER TABLE `omoccurpaleo` 
#  ADD COLUMN `geologicalContextID` VARCHAR(45) NULL AFTER `slideProperties`;

#ALTER TABLE `omoccurpaleo` 
#  CHANGE COLUMN `taxonEnvironment` `taxonEnvironment` VARCHAR(65) NULL DEFAULT NULL COMMENT 'Marine or not' AFTER `biostratigraphy`,
#  CHANGE COLUMN `lithology` `lithology` VARCHAR(250) NULL DEFAULT NULL ,
#  ADD COLUMN `bed` VARCHAR(65) NULL AFTER `member`;

#ALTER TABLE `uploadspectemp` 
#DROP COLUMN `bed`,
#DROP COLUMN `member`,
#DROP COLUMN `formation`,
#DROP COLUMN `lithogroup`,
#DROP COLUMN `lithostratigraphicTermsProperty`,
#DROP COLUMN `highestBiostratigraphicZone`,
#DROP COLUMN `lowestBiostratigraphicZone`,
#DROP COLUMN `latestAgeOrHighestStage`,
#DROP COLUMN `earliestAgeOrLowestStage`,
#DROP COLUMN `latestEpochOrHighestSeries`,
#DROP COLUMN `earliestEpochOrLowestSeries`,
#DROP COLUMN `latestPeriodOrHighestSystem`,
#DROP COLUMN `earliestPeriodOrLowestSystem`,
#DROP COLUMN `latestEraOrHighestErathem`,
#DROP COLUMN `earliestEraOrLowestErathem`,
#DROP COLUMN `latestEonOrHighestEonothem`,
#DROP COLUMN `earliestEonOrLowestEonothem`;


CREATE TABLE `omoccurpaleogts` (
  `gtsid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `gtsterm` VARCHAR(45) NOT NULL,
  `rankid` INT NOT NULL,
  `rankname` VARCHAR(45) NULL,
  `parentgtsid` INT UNSIGNED NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  UNIQUE INDEX  `UNIQUE_gtsterm` (`gtsid` ASC),
  PRIMARY KEY (`gtsid`)
);

ALTER TABLE `omoccurpaleogts` 
  ADD INDEX `FK_gtsparent_idx` (`parentgtsid` ASC);

ALTER TABLE `omoccurpaleogts` 
  ADD CONSTRAINT `FK_gtsparent` FOREIGN KEY (`parentgtsid`)  REFERENCES `omoccurpaleogts` (`gtsid`)  ON DELETE NO ACTION  ON UPDATE CASCADE;

INSERT INTO `omoccurpaleogts` (`gtsterm`, `rankid`, `rankname`) VALUES ('Precambrian', 10, 'supereon');
INSERT INTO omoccurpaleogts(gtsterm,rankid,rankname,parentgtsid) SELECT DISTINCT eon, 20, "eon", 1 FROM paleochronostratigraphy;
INSERT INTO omoccurpaleogts(gtsterm,rankid,rankname,parentgtsid)
  SELECT DISTINCT era, 30, "era", g.gtsid FROM paleochronostratigraphy p INNER JOIN omoccurpaleogts g ON p.eon = g.gtsterm WHERE era IS NOT NULL;
INSERT INTO omoccurpaleogts(gtsterm,rankid,rankname,parentgtsid)
  SELECT DISTINCT period, 40, "period", g.gtsid FROM paleochronostratigraphy p INNER JOIN omoccurpaleogts g ON p.era = g.gtsterm WHERE period IS NOT NULL;
INSERT INTO omoccurpaleogts(gtsterm,rankid,rankname,parentgtsid) 
  SELECT DISTINCT epoch, 50, "epoch", g.gtsid FROM paleochronostratigraphy p INNER JOIN omoccurpaleogts g ON p.period = g.gtsterm WHERE epoch IS NOT NULL;
INSERT INTO omoccurpaleogts(gtsterm,rankid,rankname,parentgtsid)
  SELECT DISTINCT p.stage, 60, "age", g.gtsid FROM paleochronostratigraphy p INNER JOIN omoccurpaleogts g ON p.epoch = g.gtsterm WHERE stage IS NOT NULL;

UPDATE omoccurpaleogts
SET rankid = 40, rankname = "period", parentgtsid = 13
WHERE gtsterm IN("Pennsylvanian","Mississippian");


DROP TABLE omoccurlithostratigraphy;
DROP TABLE paleochronostratigraphy;


ALTER TABLE `omcollections` 
  ADD COLUMN `dynamicProperties` TEXT NULL AFTER `accessrights`,
  ADD COLUMN `datasetID` VARCHAR(250) NULL AFTER `collectionId`;

ALTER TABLE `omcollections` 
  ADD COLUMN `contactJson` LONGTEXT NULL AFTER `email`;
ALTER TABLE `omcollections` 
  CHANGE COLUMN `contactJson` `contactJson` JSON NULL DEFAULT NULL ;

DROP TABLE `omcollectioncontacts`;

ALTER TABLE `omcollcategories` 
  ADD COLUMN `sortsequence` INT NULL AFTER `notes`;
  
ALTER TABLE `userroles` 
  ADD UNIQUE INDEX `Unique_userroles` (`uid` ASC, `role` ASC, `tablename` ASC, `tablepk` ASC);

#Tag all collection admin and editors as non-volunteer crowdsource editors   
UPDATE omcrowdsourcecentral c INNER JOIN omcrowdsourcequeue q ON c.omcsid = q.omcsid
  INNER JOIN userroles r ON c.collid = r.tablepk AND q.uidprocessor = r.uid
  SET q.isvolunteer = 0
  WHERE r.role IN("CollAdmin","CollEditor") AND q.isvolunteer = 1;


UPDATE omoccurgenetic SET initialtimestamp = now() WHERE initialtimestamp IS NULL;

ALTER TABLE `omoccurgenetic` 
  CHANGE COLUMN `resourceurl` `resourceurl` VARCHAR(500) NULL ,
  CHANGE COLUMN `notes` `notes` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `initialtimestamp` `initialtimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp ;

ALTER TABLE `omoccurgenetic` 
  ADD UNIQUE INDEX `UNIQUE_omoccurgenetic` (`occid` ASC, `resourceurl` ASC);

CREATE TABLE `omassociatedoccurrence` (
  `assocOccurID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occid` INT UNSIGNED NOT NULL,
  `relationship` VARCHAR(45) NOT NULL COMMENT 'subSample; parentSample; siblingSample',
  `subType` VARCHAR(45) NULL COMMENT 'tissue, skeleton, wiskers, genetic',
  `occidAssociate` INT UNSIGNED NULL,
  `resourceurl` VARCHAR(250) NULL,
  `externalIdentifier` VARCHAR(45) NULL,
  `dynamicProperties` TEXT NULL,
  `createdUid` INT UNSIGNED NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `modifiedTimestamp` TIMESTAMP NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`assocOccurID`),
  INDEX `FK_assocOccur_assocOccid_idx` (`occidAssociate` ASC),
  INDEX `FK_assocOccur_occid_idx` (`occid` ASC),
  INDEX `FK_assocOccur_uid_idx` (`createdUid` ASC),
  INDEX `FK_assocOccur_uidMod_idx` (`modifiedUid` ASC),
  CONSTRAINT `FK_assocOccur_assocOccid`  FOREIGN KEY (`occidAssociate`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_assocOccur_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_assocOccur_uid`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE,
  CONSTRAINT `FK_assocOccur_uidMod`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE);


CREATE TABLE `igsnverification` (
  `igsn` VARCHAR(15) NOT NULL,
  `occid` INT UNSIGNED NULL,
  `status` INT NULL,
  `initialtimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  INDEX `FK_igsn_occid_idx` (`occid` ASC),
  INDEX `INDEX_igsn` (`igsn` ASC),
  CONSTRAINT `FK_igsn_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE);


ALTER TABLE `omoccurrences`
  CHANGE COLUMN `labelProject` `labelProject` varchar(250) DEFAULT NULL,
  CHANGE COLUMN `georeferenceRemarks` `georeferenceRemarks` VARCHAR(500) NULL DEFAULT NULL,
  DROP INDEX `idx_occrecordedby`;
  
ALTER TABLE `omoccurrences` 
  ADD COLUMN `continent` VARCHAR(45) NULL AFTER `locationID`,
  ADD COLUMN `islandGroup` VARCHAR(75) NULL AFTER `waterBody`,
  ADD COLUMN `island` VARCHAR(75) NULL AFTER `islandGroup`,
  ADD COLUMN `countryCode` VARCHAR(5) NULL AFTER `island`;

ALTER TABLE `omoccurrences` 
  CHANGE COLUMN `waterBody` `waterBody` VARCHAR(75) NULL DEFAULT NULL AFTER `continent`;
  
  
ALTER TABLE `omoccurrences` 
  ADD INDEX `Index_locationID` (`locationID` ASC),
  ADD INDEX `Index_eventID` (`eventID` ASC);

ALTER TABLE `omoccurrences` 
  ADD UNIQUE INDEX `UNIQUE_occurrenceID` (`occurrenceID` ASC),
  ADD INDEX `Index_occur_localitySecurity` (`localitySecurity` ASC);

ALTER TABLE `omoccurrences` 
  DROP INDEX `Index_gui` ,
  ADD UNIQUE INDEX `Index_gui` (`occurrenceID` ASC);  

ALTER TABLE `omoccurrences` 
ADD INDEX `Index_latlng` (`decimalLatitude` ASC, `decimalLongitude` ASC);

DELETE FROM omoccurrencesfulltext 
WHERE locality IS NULL AND recordedby IS NULL;

REPLACE INTO omoccurrencesfulltext(occid,locality,recordedby) 
  SELECT occid, CONCAT_WS("; ", municipality, locality), recordedby
  FROM omoccurrences
  WHERE municipality IS NOT NULL OR locality IS NOT NULL OR recordedby IS NOT NULL;
OPTIMIZE table omoccurrencesfulltext;

REPLACE INTO omoccurpoints (occid,point)
SELECT occid, Point(decimalLatitude, decimalLongitude) 
FROM omoccurrences 
WHERE decimalLatitude IS NOT NULL AND decimalLongitude IS NOT NULL;
OPTIMIZE table omoccurpoints;


#Add edittype field and run update query to tag batch updates (edittype = 1)
ALTER TABLE `omoccuredits` 
  ADD COLUMN `editType` INT NULL DEFAULT 0 COMMENT '0 = general edit, 1 = batch edit' AFTER `AppliedStatus`;

UPDATE omoccuredits e INNER JOIN (SELECT initialtimestamp, uid, count(DISTINCT occid) as cnt
FROM omoccuredits
GROUP BY initialtimestamp, uid
HAVING cnt > 2) as inntab ON e.initialtimestamp = inntab.initialtimestamp AND e.uid = inntab.uid
SET edittype = 1;



#Occurrence Trait/Attribute adjustments
	#Add measurementID (GUID) to tmattribute table 
	#Add measurementAccuracy field
	#Add measurementUnitID field
	#Add measurementMethod field
	#Add exportHeader for trait name
	#Add exportHeader for state name



#Review pubprofile (adminpublications)



#Collection GUID issue


DELIMITER //

DROP TRIGGER IF EXISTS `omoccurrences_insert`//
CREATE TRIGGER `omoccurrences_insert` AFTER INSERT ON `omoccurrences`
FOR EACH ROW BEGIN
	IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		INSERT INTO omoccurpoints (`occid`,`point`) 
		VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
	END IF;
	IF NEW.`recordedby` IS NOT NULL OR NEW.`municipality` IS NOT NULL OR NEW.`locality` IS NOT NULL THEN
		INSERT INTO omoccurrencesfulltext (`occid`,`recordedby`,`locality`) 
		VALUES (NEW.`occid`,NEW.`recordedby`,CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`));
	END IF;
END
//

DROP TRIGGER IF EXISTS `omoccurrences_update`//
CREATE TRIGGER `omoccurrences_update` AFTER UPDATE ON `omoccurrences`
FOR EACH ROW BEGIN
	IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		IF EXISTS (SELECT `occid` FROM omoccurpoints WHERE `occid`=NEW.`occid`) THEN
			UPDATE omoccurpoints 
			SET `point` = Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`)
			WHERE `occid` = NEW.`occid`;
		ELSE 
			INSERT INTO omoccurpoints (`occid`,`point`) 
			VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
		END IF;
	ELSE
		DELETE FROM omoccurpoints WHERE `occid` = NEW.`occid`;
	END IF;

	IF NEW.`recordedby` IS NOT NULL OR NEW.`municipality` IS NOT NULL OR NEW.`locality` IS NOT NULL THEN
		IF EXISTS (SELECT `occid` FROM omoccurrencesfulltext WHERE `occid`=NEW.`occid`) THEN
			UPDATE omoccurrencesfulltext 
			SET `recordedby` = NEW.`recordedby`,`locality` = CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`)
			WHERE `occid` = NEW.`occid`;
		ELSE
			INSERT INTO omoccurrencesfulltext (`occid`,`recordedby`,`locality`) 
			VALUES (NEW.`occid`,NEW.`recordedby`,CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`));
		END IF;
	ELSE 
		DELETE FROM omoccurrencesfulltext WHERE `occid` = NEW.`occid`;
	END IF;
END
//

DELIMITER ;
