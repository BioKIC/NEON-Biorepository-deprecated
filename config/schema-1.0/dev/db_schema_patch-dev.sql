INSERT IGNORE INTO schemaversion (versionnumber) values ("1.3");

ALTER TABLE `uploadspectemp` 
  ADD COLUMN `eventTime` VARCHAR(45) NULL AFTER `verbatimEventDate`,
  CHANGE COLUMN `LatestDateCollected` `eventDate2` DATE NULL DEFAULT NULL AFTER `eventDate`;

ALTER TABLE `omoccurrences` 
  ADD COLUMN `eventTime` VARCHAR(45) NULL AFTER `verbatimEventDate`;


