#One of the following will fail, depending on what has been applied
ALTER TABLE `omcollections` 
  ADD COLUMN `collectionGuid` TEXT NULL AFTER `aggKeysStr`;

ALTER TABLE `omcollections` 
  ADD COLUMN `recordID` TEXT NULL AFTER `aggKeysStr`;

ALTER TABLE `omcollections` 
  CHANGE COLUMN `CollID` `collID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `InstitutionCode` `institutionCode` VARCHAR(45) NOT NULL ,
  CHANGE COLUMN `CollectionCode` `collectionCode` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `CollectionName` `collectionName` VARCHAR(150) NOT NULL ,
  CHANGE COLUMN `collectionId` `collectionID` VARCHAR(100) NULL DEFAULT NULL ,
  CHANGE COLUMN `fulldescription` `fullDescription` VARCHAR(2000) NULL DEFAULT NULL ,
  CHANGE COLUMN `Homepage` `homepage` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `IndividualUrl` `individualUrl` VARCHAR(500) NULL DEFAULT NULL ,
  CHANGE COLUMN `latitudedecimal` `latitudeDecimal` DOUBLE(8,6) NULL DEFAULT NULL ,
  CHANGE COLUMN `longitudedecimal` `longitudeDecimal` DOUBLE(9,6) NULL DEFAULT NULL ,
  CHANGE COLUMN `CollType` `collType` VARCHAR(45) NOT NULL DEFAULT 'Preserved Specimens' COMMENT 'Preserved Specimens, General Observations, Observations' ,
  CHANGE COLUMN `ManagementType` `managementType` VARCHAR(45) NULL DEFAULT 'Snapshot' COMMENT 'Snapshot, Live Data' ,
  CHANGE COLUMN `PublicEdits` `publicEdits` INT(1) UNSIGNED NOT NULL DEFAULT 1 ,
  CHANGE COLUMN `collectionguid` `collectionGuid` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `securitykey` `securityKey` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `guidtarget` `guidTarget` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `accessrights` `accessRights` VARCHAR(1000) NULL DEFAULT NULL ,
  CHANGE COLUMN `SortSeq` `sortSeq` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `InitialTimeStamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `omcollections` 
  ADD COLUMN `dwcTermJson` TEXT NULL AFTER `aggKeysStr`;



CREATE TABLE `portalindex` (
  `portalID` INT NOT NULL AUTO_INCREMENT,
  `portalName` VARCHAR(45) NOT NULL,
  `acronym` VARCHAR(45) NULL,
  `portalDescription` VARCHAR(250) NULL,
  `urlRoot` VARCHAR(150) NOT NULL,
  `securityKey` VARCHAR(45) NULL,
  `symbiotaVersion` VARCHAR(45) NULL,
  `guid` VARCHAR(45) NULL,
  `manager` VARCHAR(45) NULL,
  `managerEmail` VARCHAR(45) NULL,
  `primaryLead` VARCHAR(45) NULL,
  `primaryLeadEmail` VARCHAR(45) NULL,
  `notes` VARCHAR(250) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`portalID`)
);

ALTER TABLE `portalindex` 
  ADD UNIQUE INDEX `UQ_portalIndex_guid` (`guid` ASC);

ALTER TABLE `omcollpublications` 
  RENAME TO `portalpublications` ;

ALTER TABLE `omcollpuboccurlink` 
  RENAME TO  `portaloccurrences` ;

ALTER TABLE `portalpublications` 
  DROP FOREIGN KEY `FK_adminpub_collid`;

ALTER TABLE `portalpublications` 
  DROP COLUMN `securityguid`,
  DROP COLUMN `targeturl`,
  ADD COLUMN `portalID` INT NULL AFTER `collid`,
  CHANGE COLUMN `collid` `collid` INT(10) UNSIGNED NULL ,
  CHANGE COLUMN `criteriajson` `criteriaJson` TEXT NULL DEFAULT NULL ,
  CHANGE COLUMN `includedeterminations` `includeDeterminations` INT(11) NULL DEFAULT 1,
  CHANGE COLUMN `includeimages` `includeImages` INT(11) NULL DEFAULT 1,
  CHANGE COLUMN `autoupdate` `autoUpdate` INT(11) NULL DEFAULT 0,
  CHANGE COLUMN `lastdateupdate` `lastDateUpdate` DATETIME NULL DEFAULT NULL,
  CHANGE COLUMN `updateinterval` `updateInterval` INT(11) NULL DEFAULT NULL,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  ADD INDEX `FK_portalpub_portalID_idx` (`portalID` ASC);

ALTER TABLE `portalpublications` 
  ADD CONSTRAINT `FK_portalpub_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_portalpub_portalID`  FOREIGN KEY (`portalID`)  REFERENCES `portalindex` (`portalID`)  ON DELETE RESTRICT  ON UPDATE NO ACTION;

ALTER TABLE `portalpublications` 
  ADD COLUMN `pubTitle` VARCHAR(45) NULL AFTER `pubid`,
  ADD COLUMN `description` VARCHAR(250) NULL AFTER `pubTitle`,
  ADD COLUMN `createdUid` INT UNSIGNED NULL AFTER `updateInterval`;


ALTER TABLE `portalpublications` 
  DROP FOREIGN KEY `FK_portalpub_collid`,
  DROP FOREIGN KEY `FK_portalpub_portalID`;

ALTER TABLE `portalpublications` 
  CHANGE COLUMN `pubTitle` `pubTitle` VARCHAR(45) NOT NULL ,
  CHANGE COLUMN `collid` `collid` INT(10) UNSIGNED NOT NULL ,
  CHANGE COLUMN `portalID` `portalID` INT(11) NOT NULL ,
  ADD INDEX `FK_portalpub_uid_idx` (`createdUid` ASC);

ALTER TABLE `portalpublications` 
  ADD CONSTRAINT `FK_portalpub_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`collID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_portalpub_portalID`  FOREIGN KEY (`portalID`)  REFERENCES `portalindex` (`portalID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_portalpub_createdUid`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE;

ALTER TABLE `portalpublications` 
  ADD COLUMN `direction` VARCHAR(45) NOT NULL AFTER `portalID`;

INSERT INTO portalpublications(collid, portalID, pubTitle, direction)
  SELECT DISTINCT o.collid, i.portalID, "remote harvest" as title, "import" as direction 
  FROM portaloccurrences i INNER JOIN omoccurrences o ON i.occid = o.occid
  WHERE i.pubid IS NULL;

UPDATE portaloccurrences i INNER JOIN omoccurrences o ON i.occid = o.occid
  INNER JOIN portalpublications p ON o.collid = p.collid
  SET i.pubID = p.pubID
  WHERE i.pubID IS NULL;

ALTER TABLE `portaloccurrences` 
  DROP FOREIGN KEY `FK_portalOccur_pubID`,
  DROP FOREIGN KEY `FK_portalOccur_portalID`;

ALTER TABLE `portaloccurrences` 
  DROP COLUMN `portalID`,
  DROP COLUMN `portalOccurrencesID`,
  CHANGE COLUMN `pubid` `pubid` INT(10) UNSIGNED NOT NULL ,
  CHANGE COLUMN `targetOccid` `targetOccid` INT(11) NOT NULL ,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`occid`, `pubid`),
  DROP INDEX `UQ_portalOccur_UNIQUE` ,
  DROP INDEX `FK_portalOccur_portalID_idx` ;

ALTER TABLE `portaloccurrences` 
  ADD CONSTRAINT `FK_portalOccur_pubid`  FOREIGN KEY (`pubid`)  REFERENCES `portalpublications` (`pubid`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `portaloccurrences` 
  DROP FOREIGN KEY `FK_ompubpubid`;

ALTER TABLE `portaloccurrences` 
  ADD COLUMN `portalID` INT NOT NULL AFTER `occid`,
  ADD COLUMN `targetOccid` INT NULL AFTER `pubid`,
  CHANGE COLUMN `occid` `occid` INT(10) UNSIGNED NOT NULL FIRST,
  CHANGE COLUMN `pubid` `pubid` INT(10) UNSIGNED NULL DEFAULT NULL ,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`occid`, `portalID`),
  ADD INDEX `FK_portalOccur_portalID_idx` (`portalID` ASC);

ALTER TABLE `portaloccurrences` 
  ADD CONSTRAINT `FK_portalOccur_pubid`  FOREIGN KEY (`pubid`)  REFERENCES `portalpublications` (`pubid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_portalOccur_portalID`  FOREIGN KEY (`portalID`)  REFERENCES `portalindex` (`portalID`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `portalindex` 
  CHANGE COLUMN `portalName` `portalName` VARCHAR(150) NOT NULL ;

