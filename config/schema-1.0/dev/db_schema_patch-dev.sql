INSERT IGNORE INTO schemaversion (versionnumber) values ("1.3");

ALTER TABLE `agents`
  ADD INDEX `FK_agents_familyname` (`familyName` ASC),
  ADD UNIQUE INDEX `UQ_agents_guid` (`guid` ASC);

ALTER TABLE `agents` 
  RENAME INDEX `firstname` TO `FK_agents_firstname`;

ALTER TABLE `agents` 
  ALTER INDEX `FK_agents_firstname`;

CREATE TABLE `agentoccurrencelink` (
  `agentID` BIGINT(20) NOT NULL,
  `occid` INT UNSIGNED NOT NULL,
  `isPrimary` INT NULL,
  `createdUid` INT UNSIGNED NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `dateLastModified` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`agentID`, `occid`),
  INDEX `FK_agentoccurlink_occid_idx` (`occid` ASC),
  INDEX `FK_agentoccurlink_created_idx` (`createdUid` ASC),
  INDEX `FK_agentoccurlink_modified_idx` (`modifiedUid` ASC),
  INDEX `FK_agentoccurlink_isPrimary` (`isPrimary` ASC),
  CONSTRAINT `FK_agentoccurlink_agentID`  FOREIGN KEY (`agentID`)  REFERENCES `agents` (`agentID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_created`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_modified` FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE);

INSERT IGNORE INTO agents(familyName,firstName,middleName,startYearActive,endYearActive,notes,rating,guid)
  SELECT DISTINCT familyname, firstname, middlename, startyearactive, endyearactive, notes, rating, guid 
  FROM omcollectors c LEFT JOIN agents a ON c.guid = a.guid
  WHERE a.guid IS NULL;

INSERT IGNORE INTO agentoccurrencelink(agentID, occid, isPrimary)
  SELECT a.agentID, o.occid, 1 as isPrimary
  FROM agents a INNER JOIN omcollectors c ON a.guid = c.guid
  INNER JOIN omoccurrences o ON c.recordedbyid = o.recordedbyid;

ALTER TABLE `ctcontrolvocab` 
  ADD COLUMN `collid` INT UNSIGNED NULL DEFAULT NULL AFTER `cvID`,
  ADD INDEX `FK_ctControlVocab_collid_idx` (`collid` ASC);

ALTER TABLE `ctcontrolvocab` 
  ADD CONSTRAINT `FK_ctControlVocab_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)  ON DELETE CASCADE  ON UPDATE CASCADE;

CREATE TABLE `omcollproperties` (
  `collPropID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `collid` INT UNSIGNED NOT NULL,
  `propCategory` VARCHAR(45) NOT NULL,
  `propTitle` VARCHAR(45) NOT NULL,
  `propJson` LONGTEXT NULL,
  `notes` VARCHAR(255) NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `modifiedTimestamp` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`collPropID`),
  INDEX `FK_omcollproperties_collid_idx` (`collid` ASC),
  INDEX `FK_omcollproperties_uid_idx` (`modifiedUid` ASC),
  CONSTRAINT `FK_omcollproperties_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)   ON DELETE CASCADE   ON UPDATE CASCADE,
  CONSTRAINT `FK_omcollproperties_uid`   FOREIGN KEY (`modifiedUid`)   REFERENCES `users` (`uid`)   ON DELETE CASCADE   ON UPDATE CASCADE);

ALTER TABLE `geographicthesaurus` 
  ADD COLUMN `geoLevel` INT NOT NULL AFTER `category`;

ALTER TABLE `geographicthesaurus` 
  DROP FOREIGN KEY `FK_geothes_parentID`;

ALTER TABLE `geographicthesaurus` 
ADD CONSTRAINT `FK_geothes_parentID`  FOREIGN KEY (`parentID`)  REFERENCES `geographicthesaurus` (`geoThesID`)  ON DELETE RESTRICT  ON UPDATE CASCADE;

ALTER TABLE `geographicthesaurus` 
  ADD UNIQUE INDEX `UQ_geothes` (`geoterm` ASC, `parentID` ASC);


CREATE TABLE `omcrowdsourceproject` (
  `csProjID` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `description` VARCHAR(250) NULL,
  `instructions` TEXT NULL,
  `trainingurl` VARCHAR(250) NULL,
  `managers` VARCHAR(150) NULL,
  `criteria` VARCHAR(1500) NULL,
  `notes` VARCHAR(250) NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `modifiedTimestamp` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`csProjID`));

ALTER TABLE `omcrowdsourceproject` 
  ADD INDEX `FK_croudsourceproj_uid_idx` (`modifiedUid` ASC) ;

ALTER TABLE `omcrowdsourceproject`
  ADD CONSTRAINT `FK_croudsourceproj_uid`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `omcrowdsourcequeue` 
  ADD COLUMN `csProjID` INT NULL AFTER `omcsid`,
  ADD INDEX `FK_omcrowdsourcequeue_csProjID_idx` (`csProjID` ASC);

ALTER TABLE `omcrowdsourcequeue` 
  ADD CONSTRAINT `FK_omcrowdsourcequeue_csProjID`  FOREIGN KEY (`csProjID`)  REFERENCES `omcrowdsourceproject` (`csProjID`)  ON DELETE SET NULL  ON UPDATE CASCADE;


ALTER TABLE `omoccurassociations` 
  CHANGE COLUMN `condition` `conditionOfAssociate` VARCHAR(250) NULL DEFAULT NULL ;

ALTER TABLE `omoccurrences` 
  DROP FOREIGN KEY `FK_omoccurrences_recbyid`;

ALTER TABLE `omoccurrences` 
  DROP COLUMN `recordedbyid`,
  DROP INDEX `FK_recordedbyid` ;

DROP TABLE omcollectors;

CREATE TABLE `portalindex` (
  `portalIndexID` INT NOT NULL AUTO_INCREMENT,
  `portalName` VARCHAR(45) NOT NULL,
  `acronym` VARCHAR(45) NULL,
  `portalDescription` VARCHAR(250) NULL,
  `urlRoot` VARCHAR(150) NOT NULL,
  `securityKey` VARCHAR(45) NULL,
  `symbVersion` VARCHAR(45) NULL,
  `guid` VARCHAR(45) NULL,
  `manager` VARCHAR(45) NULL,
  `managerEmail` VARCHAR(45) NULL,
  `primaryLead` VARCHAR(45) NULL,
  `primaryLeadEmail` VARCHAR(45) NULL,
  `notes` VARCHAR(250) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`portalIndexID`));

ALTER TABLE `omcollpublications` 
  DROP FOREIGN KEY `FK_adminpub_collid`;

ALTER TABLE `omcollpublications` 
  DROP COLUMN `securityguid`,
  DROP COLUMN `targeturl`,
  ADD COLUMN `portalIndexID` INT NULL AFTER `collid`,
  CHANGE COLUMN `collid` `collid` INT(10) UNSIGNED NULL ,
  CHANGE COLUMN `criteriajson` `criteriaJson` TEXT NULL DEFAULT NULL ,
  CHANGE COLUMN `includedeterminations` `includeDeterminations` INT(11) NULL DEFAULT 1,
  CHANGE COLUMN `includeimages` `includeImages` INT(11) NULL DEFAULT 1,
  CHANGE COLUMN `autoupdate` `autoUpdate` INT(11) NULL DEFAULT 0,
  CHANGE COLUMN `lastdateupdate` `lastDateUpdate` DATETIME NULL DEFAULT NULL,
  CHANGE COLUMN `updateinterval` `updateInterval` INT(11) NULL DEFAULT NULL,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  ADD INDEX `FK_collPub_portalID_idx` (`portalIndexID` ASC);

ALTER TABLE `omcollpublications` 
  ADD CONSTRAINT `FK_collPub_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_collPub_portalID`  FOREIGN KEY (`portalIndexID`)  REFERENCES `portalindex` (`portalIndexID`)  ON DELETE RESTRICT  ON UPDATE NO ACTION;

ALTER TABLE `omcollpublications` 
  ADD COLUMN `pubTitle` VARCHAR(45) NULL AFTER `pubid`,
  ADD COLUMN `description` VARCHAR(250) NULL AFTER `pubTitle`,
  ADD COLUMN `createdUid` INT UNSIGNED NULL AFTER `updateInterval`;

ALTER TABLE `omcollpublications` 
  RENAME TO `ompublication` ;

ALTER TABLE `omcollpuboccurlink` 
  RENAME TO  `ompublicationoccurlink` ;

ALTER TABLE `taxa` 
  CHANGE COLUMN `Author` `Author` VARCHAR(100) NOT NULL ;

UPDATE IGNORE taxa SET author = "" WHERE author IS NULL;

ALTER TABLE `taxa` 
  DROP INDEX `sciname_unique` ,
  ADD UNIQUE INDEX `sciname_unique` (`SciName` ASC, `RankId` ASC);

ALTER TABLE `uploadspectemp` 
  ADD COLUMN `eventTime` VARCHAR(45) NULL AFTER `verbatimEventDate`,
  CHANGE COLUMN `LatestDateCollected` `eventDate2` DATE NULL DEFAULT NULL AFTER `eventDate`;

ALTER TABLE `omoccurrences` 
  ADD COLUMN `eventTime` VARCHAR(45) NULL AFTER `verbatimEventDate`;



