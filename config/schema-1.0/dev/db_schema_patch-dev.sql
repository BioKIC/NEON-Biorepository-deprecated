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

ALTER TABLE `omoccurassociations` 
  CHANGE COLUMN `condition` `conditionOfAssociate` VARCHAR(250) NULL DEFAULT NULL ;


ALTER TABLE `omoccurrences` 
  DROP FOREIGN KEY `FK_omoccurrences_recbyid`;

ALTER TABLE `omoccurrences` 
  DROP COLUMN `recordedbyid`,
  DROP INDEX `FK_recordedbyid` ;

DROP TABLE omcollectors;


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

