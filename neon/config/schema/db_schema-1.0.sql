CREATE TABLE `NeonShipment` (
  `shipmentPK` INT NOT NULL AUTO_INCREMENT,
  `shipmentID` VARCHAR(25) NOT NULL,
  `domainID` VARCHAR(10) NOT NULL,
  `dateShipped` DATE NOT NULL,
  `senderID` VARCHAR(45) NOT NULL,
  `shipmentService` VARCHAR(45) NULL,
  `shipmentMethod` VARCHAR(45) NULL,
  `trackingNumber` VARCHAR(45) NULL,
  `notes` VARCHAR(250) NULL,
  `importUid` INT NOT NULL,
  `modifiedByUid` INT NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`shipmentpk`));

CREATE TABLE `NeonSample` (
  `samplePK` INT NOT NULL AUTO_INCREMENT,
  `shipmentPK` INT NOT NULL,
  `sampleID` VARCHAR(45) NULL,
  `sampleClass` VARCHAR(45) NULL,
  `namedlocation` VARCHAR(45) NULL,
  `collectdate` DATE NULL,
  `quarantineStatus` VARCHAR(4) NULL,
  `notes` VARCHAR(250) NULL,
  `checkinUid` INT NULL,
  `checkinTimestamp` DATETIME NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`samplePK`),
  INDEX `FK_samples_shipmentid_idx` (`shipmentPK` ASC),
  CONSTRAINT `FK_samples_shipmentid`
    FOREIGN KEY (`shipmentPK`)
    REFERENCES `neon_biorepository`.`shipment` (`shipmentPK`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);
    
