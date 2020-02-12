INSERT IGNORE INTO schemaversion (versionnumber) values ("1.2");

ALTER TABLE `lkupstateprovince` 
  CHANGE COLUMN `abbrev` `abbrev` VARCHAR(3) NULL DEFAULT NULL ;

ALTER TABLE `fmprojects` 
  CHANGE COLUMN `fulldescription` `fulldescription` VARCHAR(5000) NULL DEFAULT NULL ;

ALTER TABLE `fmchklstprojlink` 
   ADD COLUMN `sortSequence` INT NULL AFTER `mapChecklist`;


ALTER TABLE `uploadspectemp` 
  ADD COLUMN `geologicalcontextid` VARCHAR(150) NULL AFTER `exsiccatiNotes`,
  ADD COLUMN `earliestEonOrLowestEonothem` VARCHAR(255) NULL AFTER `geologicalcontextid`,
  ADD COLUMN `latestEonOrHighestEonothem` VARCHAR(255) NULL AFTER `earliestEonOrLowestEonothem`,
  ADD COLUMN `earliestEraOrLowestErathem` VARCHAR(255) NULL AFTER `latestEonOrHighestEonothem`,
  ADD COLUMN `latestEraOrHighestErathem` VARCHAR(255) NULL AFTER `earliestEraOrLowestErathem`,
  ADD COLUMN `earliestPeriodOrLowestSystem` VARCHAR(255) NULL AFTER `latestEraOrHighestErathem`,
  ADD COLUMN `latestPeriodOrHighestSystem` VARCHAR(255) NULL AFTER `earliestPeriodOrLowestSystem`,
  ADD COLUMN `earliestEpochOrLowestSeries` VARCHAR(255) NULL AFTER `latestPeriodOrHighestSystem`,
  ADD COLUMN `latestEpochOrHighestSeries` VARCHAR(255) NULL AFTER `earliestEpochOrLowestSeries`,
  ADD COLUMN `earliestAgeOrLowestStage` VARCHAR(255) NULL AFTER `latestEpochOrHighestSeries`,
  ADD COLUMN `latestAgeOrHighestStage` VARCHAR(255) NULL AFTER `earliestAgeOrLowestStage`,
  ADD COLUMN `lowestBiostratigraphicZone` VARCHAR(255) NULL AFTER `latestAgeOrHighestStage`,
  ADD COLUMN `highestBiostratigraphicZone` VARCHAR(255) NULL AFTER `lowestBiostratigraphicZone`,
  ADD COLUMN `lithostratigraphicTermsProperty` VARCHAR(255) NULL AFTER `highestBiostratigraphicZone`,
  ADD COLUMN `group` VARCHAR(255) NULL AFTER `lithostratigraphicTermsProperty`,
  ADD COLUMN `formation` VARCHAR(255) NULL AFTER `group`,
  ADD COLUMN `member` VARCHAR(255) NULL AFTER `formation`,
  ADD COLUMN `bed` VARCHAR(255) NULL AFTER `member`;

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
  CHANGE COLUMN `url` `url` VARCHAR(255) NULL ;

ALTER TABLE `images` 
  CHANGE COLUMN `url` `url` VARCHAR(255) NULL ;

ALTER TABLE `images` 
  ADD INDEX `Index_images_datelastmod` (`InitialTimeStamp` ASC);

  
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
  DROP INDEX `Index_upper` ;

ALTER TABLE `taxstatus` 
  DROP PRIMARY KEY,
  ADD PRIMARY KEY USING BTREE (`tid`, `taxauthid`);

ALTER TABLE `taxstatus` 
ADD INDEX `Index_tid` (`tid` ASC);

ALTER TABLE `taxaresourcelinks` 
  ADD UNIQUE INDEX `UNIQUE_taxaresource` (`tid` ASC, `sourcename` ASC);

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
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`paleoID`),
  INDEX `FK_paleo_occid_idx` (`occid` ASC),
  UNIQUE INDEX `UNIQUE_occid` (`occid` ASC),
  CONSTRAINT `FK_paleo_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE
) COMMENT = 'Occurrence Paleo tables';

#ALTER TABLE `omoccurpaleo` 
#CHANGE COLUMN `taxonEnvironment` `taxonEnvironment` VARCHAR(65) NULL DEFAULT NULL COMMENT 'Marine or not' AFTER `biostratigraphy`,
#CHANGE COLUMN `lithology` `lithology` VARCHAR(250) NULL DEFAULT NULL ,
#ADD COLUMN `bed` VARCHAR(65) NULL AFTER `member`;

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

DROP TABLE omoccurlithostratigraphy;
DROP TABLE paleochronostratigraphy;


ALTER TABLE `omcollectioncontacts` 
  DROP FOREIGN KEY `FK_contact_uid`;
  
ALTER TABLE `omcollectioncontacts` 
  DROP FOREIGN KEY `FK_contact_collid`;

ALTER TABLE `omcollectioncontacts` 
  CHANGE COLUMN `uid` `uid` INT(10) UNSIGNED NULL ,
  ADD COLUMN `nameoverride` VARCHAR(100) NULL AFTER `uid`,
  ADD COLUMN `emailoverride` VARCHAR(100) NULL AFTER `nameoverride`,
  ADD COLUMN `collcontid` INT NOT NULL AUTO_INCREMENT FIRST,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`collcontid`);

ALTER TABLE `omcollectioncontacts` 
  ADD CONSTRAINT `FK_contact_uid` FOREIGN KEY (`uid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_contact_collid` FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`collid`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `omcollectioncontacts` 
  ADD UNIQUE INDEX `UNIQUE_coll_contact` (`collid` ASC, `uid` ASC, `nameoverride` ASC, `emailoverride` ASC);

ALTER TABLE `omcollections` 
  ADD COLUMN `dynamicProperties` TEXT NULL AFTER `accessrights`,
  ADD COLUMN `datasetID` VARCHAR(250) NULL AFTER `collectionId`;

ALTER TABLE `omcollcategories` 
  ADD COLUMN `sortsequence` INT NULL AFTER `notes`;
  

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
  ADD INDEX `Index_locationID` (`locationID` ASC),
  ADD INDEX `Index_eventID` (`eventID` ASC);

ALTER TABLE `omoccurrences` 
  ADD UNIQUE INDEX `UNIQUE_occurrenceID` (`occurrenceID` ASC),
  ADD INDEX `Index_occur_localitySecurity` (`localitySecurity` ASC);

ALTER TABLE `omoccurrences` 
  DROP INDEX `Index_gui` ,
  ADD UNIQUE INDEX `Index_gui` (`occurrenceID` ASC);  

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
