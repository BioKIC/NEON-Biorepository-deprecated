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
  `modifiedByUid` INT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`shipmentpk`));
  
ALTER TABLE `neonshipment` 
  ADD UNIQUE INDEX `UNIQUE_INDEX` (`shipmentID` ASC);
  

CREATE TABLE `NeonSample` (
  `samplePK` INT NOT NULL AUTO_INCREMENT,
  `shipmentPK` INT NOT NULL,
  `sampleID` VARCHAR(45) NULL,
  `sampleCode` VARCHAR(45) NULL,
  `sampleClass` VARCHAR(45) NULL,
  `taxonID` VARCHAR(45) NULL,
  `individualCount` INT NULL,
  `filterVolume` INT NULL,
  `namedlocation` VARCHAR(45) NULL,
  `domainremarks` VARCHAR(250) NULL,
  `collectdate` DATE NULL,
  `quarantineStatus` VARCHAR(4) NULL,
  `notes` VARCHAR(250) NULL,
  `checkinUid` INT NULL,
  `checkinTimestamp` DATETIME NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`samplePK`),
  INDEX `FK_samples_shipmentid_idx` (`shipmentPK` ASC),
  CONSTRAINT `FK_samples_shipmentid`  FOREIGN KEY (`shipmentPK`)  REFERENCES `shipment` (`shipmentPK`)  ON DELETE CASCADE  ON UPDATE CASCADE);
    
ALTER TABLE `neonsample` 
  ADD UNIQUE INDEX `UNIQUE_INDEX` (`sampleID` ASC);

