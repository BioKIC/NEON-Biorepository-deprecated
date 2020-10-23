
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actionrequest`
--

DROP TABLE IF EXISTS `actionrequest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actionrequest` (
  `actionrequestid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fk` int(11) NOT NULL,
  `tablename` varchar(255) DEFAULT NULL,
  `requesttype` varchar(30) NOT NULL,
  `uid_requestor` int(10) unsigned NOT NULL,
  `requestdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `requestremarks` varchar(900) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `uid_fullfillor` int(10) unsigned NOT NULL,
  `state` varchar(12) DEFAULT NULL,
  `resolution` varchar(12) DEFAULT NULL,
  `statesetdate` datetime DEFAULT NULL,
  `resolutionremarks` varchar(900) DEFAULT NULL,
  PRIMARY KEY (`actionrequestid`),
  KEY `FK_actionreq_uid1_idx` (`uid_requestor`),
  KEY `FK_actionreq_uid2_idx` (`uid_fullfillor`),
  KEY `FK_actionreq_type_idx` (`requesttype`),
  CONSTRAINT `FK_actionreq_uid1` FOREIGN KEY (`uid_requestor`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_actionreq_uid2` FOREIGN KEY (`uid_fullfillor`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_actionreq_type` FOREIGN KEY (`requesttype`) REFERENCES `actionrequesttype` (`requesttype`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `actionrequesttype`
--

DROP TABLE IF EXISTS `actionrequesttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actionrequesttype` (
  `requesttype` varchar(30) NOT NULL,
  `context` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`requesttype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adminlanguages`
--

DROP TABLE IF EXISTS `adminlanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminlanguages` (
  `langid` int(11) NOT NULL AUTO_INCREMENT,
  `langname` varchar(45) NOT NULL,
  `iso639_1` varchar(10) DEFAULT NULL,
  `iso639_2` varchar(10) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`langid`),
  UNIQUE KEY `index_langname_unique` (`langname`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adminstats`
--

DROP TABLE IF EXISTS `adminstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminstats` (
  `idadminstats` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(45) NOT NULL,
  `statname` varchar(45) NOT NULL,
  `statvalue` int(11) DEFAULT NULL,
  `statpercentage` int(11) DEFAULT NULL,
  `dynamicProperties` text,
  `groupid` int(11) NOT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `note` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idadminstats`),
  KEY `FK_adminstats_collid_idx` (`collid`),
  KEY `FK_adminstats_uid_idx` (`uid`),
  KEY `Index_adminstats_ts` (`initialtimestamp`),
  KEY `Index_category` (`category`),
  CONSTRAINT `FK_adminstats_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_adminstats_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agentlinks`
--

DROP TABLE IF EXISTS `agentlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentlinks` (
  `agentlinksid` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentid` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `link` varchar(900) DEFAULT NULL,
  `isprimarytopicof` tinyint(1) NOT NULL DEFAULT '1',
  `text` varchar(50) DEFAULT NULL,
  `timestampcreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdbyuid` int(11) NOT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `lastmodifiedbyuid` int(11) DEFAULT NULL,
  PRIMARY KEY (`agentlinksid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agentnames`
--

DROP TABLE IF EXISTS `agentnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentnames` (
  `agentnamesid` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentid` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'Full Name',
  `name` varchar(255) DEFAULT NULL,
  `language` varchar(6) DEFAULT 'en_us',
  `timestampcreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdbyuid` int(11) DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `lastmodifiedbyuid` int(11) DEFAULT NULL,
  PRIMARY KEY (`agentnamesid`),
  UNIQUE KEY `agentid` (`agentid`,`type`,`name`),
  KEY `type` (`type`),
  FULLTEXT KEY `ft_collectorname` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agentnumberpattern`
--

DROP TABLE IF EXISTS `agentnumberpattern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentnumberpattern` (
  `agentnumberpatternid` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentid` bigint(20) NOT NULL,
  `numbertype` varchar(50) DEFAULT 'Collector number',
  `numberpattern` varchar(255) DEFAULT NULL,
  `numberpatterndescription` varchar(900) DEFAULT NULL,
  `startyear` int(11) DEFAULT NULL,
  `endyear` int(11) DEFAULT NULL,
  `integerincrement` int(11) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`agentnumberpatternid`),
  KEY `agentid` (`agentid`),
  CONSTRAINT `agentnumberpattern_ibfk_1` FOREIGN KEY (`agentid`) REFERENCES `agents` (`agentid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agentrelations`
--

DROP TABLE IF EXISTS `agentrelations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentrelations` (
  `agentrelationsid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fromagentid` bigint(20) NOT NULL,
  `toagentid` bigint(20) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `notes` varchar(900) DEFAULT NULL,
  `timestampcreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdbyuid` int(11) DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `lastmodifiedbyuid` int(11) DEFAULT NULL,
  PRIMARY KEY (`agentrelationsid`),
  KEY `fromagentid` (`fromagentid`),
  KEY `toagentid` (`toagentid`),
  KEY `relationship` (`relationship`),
  CONSTRAINT `agentrelations_ibfk_1` FOREIGN KEY (`fromagentid`) REFERENCES `agents` (`agentid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `agentrelations_ibfk_2` FOREIGN KEY (`toagentid`) REFERENCES `agents` (`agentid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `agentrelations_ibfk_3` FOREIGN KEY (`relationship`) REFERENCES `ctrelationshiptypes` (`relationship`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agents` (
  `agentid` bigint(20) NOT NULL AUTO_INCREMENT,
  `familyname` varchar(45) NOT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `middlename` varchar(45) DEFAULT NULL,
  `startyearactive` int(11) DEFAULT NULL,
  `endyearactive` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT '10',
  `guid` varchar(900) DEFAULT NULL,
  `preferredrecbyid` bigint(20) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` char(43) DEFAULT NULL,
  `biography` text,
  `taxonomicgroups` varchar(900) DEFAULT NULL,
  `collectionsat` varchar(900) DEFAULT NULL,
  `curated` tinyint(1) DEFAULT '0',
  `nototherwisespecified` tinyint(1) DEFAULT '0',
  `type` enum('Individual','Team','Organization') DEFAULT NULL,
  `prefix` varchar(32) DEFAULT NULL,
  `suffix` varchar(32) DEFAULT NULL,
  `namestring` text,
  `mbox_sha1sum` char(40) DEFAULT NULL,
  `yearofbirth` int(11) DEFAULT NULL,
  `yearofbirthmodifier` varchar(12) DEFAULT '',
  `yearofdeath` int(11) DEFAULT NULL,
  `yearofdeathmodifier` varchar(12) DEFAULT '',
  `living` enum('Y','N','?') NOT NULL DEFAULT '?',
  `datelastmodified` datetime DEFAULT NULL,
  `lastmodifiedbyuid` int(11) DEFAULT NULL,
  PRIMARY KEY (`agentid`),
  KEY `firstname` (`firstname`),
  KEY `FK_preferred_recby` (`preferredrecbyid`),
  CONSTRAINT `FK_preferred_recby` FOREIGN KEY (`preferredrecbyid`) REFERENCES `agents` (`agentid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agentsfulltext`
--

DROP TABLE IF EXISTS `agentsfulltext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentsfulltext` (
  `agentsfulltextid` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentid` int(11) NOT NULL,
  `biography` text,
  `taxonomicgroups` text,
  `collectionsat` text,
  `notes` text,
  `name` text,
  PRIMARY KEY (`agentsfulltextid`),
  FULLTEXT KEY `ft_collectorbio` (`biography`,`taxonomicgroups`,`collectionsat`,`notes`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agentteams`
--

DROP TABLE IF EXISTS `agentteams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentteams` (
  `agentteamid` bigint(20) NOT NULL AUTO_INCREMENT,
  `teamagentid` bigint(20) NOT NULL,
  `memberagentid` bigint(20) NOT NULL,
  `ordinal` int(11) DEFAULT NULL,
  PRIMARY KEY (`agentteamid`),
  KEY `teamagentid` (`teamagentid`),
  KEY `memberagentid` (`memberagentid`),
  CONSTRAINT `agentteams_ibfk_1` FOREIGN KEY (`teamagentid`) REFERENCES `agents` (`agentid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `agentteams_ibfk_2` FOREIGN KEY (`memberagentid`) REFERENCES `agents` (`agentid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chotomouskey`
--

DROP TABLE IF EXISTS `chotomouskey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chotomouskey` (
  `stmtid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `statement` varchar(300) NOT NULL,
  `nodeid` int(10) unsigned NOT NULL,
  `parentid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stmtid`),
  KEY `FK_chotomouskey_taxa` (`tid`),
  CONSTRAINT `FK_chotomouskey_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configpage`
--

DROP TABLE IF EXISTS `configpage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configpage` (
  `configpageid` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(45) NOT NULL,
  `title` varchar(150) NOT NULL,
  `cssname` varchar(45) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'english',
  `displaymode` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`configpageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configpageattributes`
--

DROP TABLE IF EXISTS `configpageattributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configpageattributes` (
  `attributeid` int(11) NOT NULL AUTO_INCREMENT,
  `configpageid` int(11) NOT NULL,
  `objid` varchar(45) DEFAULT NULL,
  `objname` varchar(45) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT 'text, submit, div',
  `width` int(11) DEFAULT NULL,
  `top` int(11) DEFAULT NULL,
  `left` int(11) DEFAULT NULL,
  `stylestr` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attributeid`),
  KEY `FK_configpageattributes_id_idx` (`configpageid`),
  CONSTRAINT `FK_configpageattributes_id` FOREIGN KEY (`configpageid`) REFERENCES `configpage` (`configpageid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ctnametypes`
--

DROP TABLE IF EXISTS `ctnametypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctnametypes` (
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ctrelationshiptypes`
--

DROP TABLE IF EXISTS `ctrelationshiptypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctrelationshiptypes` (
  `relationship` varchar(50) NOT NULL,
  `inverse` varchar(50) DEFAULT NULL,
  `collective` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`relationship`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmchecklists`
--

DROP TABLE IF EXISTS `fmchecklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchecklists` (
  `CLID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Title` varchar(150) DEFAULT NULL,
  `Locality` varchar(500) DEFAULT NULL,
  `Publication` varchar(500) DEFAULT NULL,
  `Abstract` text,
  `Authors` varchar(250) DEFAULT NULL,
  `Type` varchar(50) DEFAULT 'static',
  `dynamicsql` varchar(500) DEFAULT NULL,
  `Parent` varchar(50) DEFAULT NULL,
  `parentclid` int(10) unsigned DEFAULT NULL,
  `Notes` varchar(500) DEFAULT NULL,
  `LatCentroid` double(9,6) DEFAULT NULL,
  `LongCentroid` double(9,6) DEFAULT NULL,
  `pointradiusmeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text,
  `percenteffort` int(11) DEFAULT NULL,
  `Access` varchar(45) DEFAULT 'private',
  `defaultSettings` varchar(250) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `SortSequence` int(10) unsigned NOT NULL DEFAULT '50',
  `expiration` int(10) unsigned DEFAULT NULL,
  `DateLastModified` datetime DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CLID`),
  KEY `FK_checklists_uid` (`uid`),
  KEY `name` (`Name`,`Type`) USING BTREE,
  CONSTRAINT `FK_checklists_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmchklstchildren`
--

DROP TABLE IF EXISTS `fmchklstchildren`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklstchildren` (
  `clid` int(10) unsigned NOT NULL,
  `clidchild` int(10) unsigned NOT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clid`,`clidchild`),
  KEY `FK_fmchklstchild_clid_idx` (`clid`),
  KEY `FK_fmchklstchild_child_idx` (`clidchild`),
  CONSTRAINT `FK_fmchklstchild_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_fmchklstchild_child` FOREIGN KEY (`clidchild`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmchklstcoordinates`
--

DROP TABLE IF EXISTS `fmchklstcoordinates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklstcoordinates` (
  `chklstcoordid` int(11) NOT NULL AUTO_INCREMENT,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `decimallatitude` double NOT NULL,
  `decimallongitude` double NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chklstcoordid`),
  UNIQUE KEY `IndexUnique` (`clid`,`tid`,`decimallatitude`,`decimallongitude`),
  KEY `FKchklsttaxalink` (`clid`,`tid`),
  CONSTRAINT `FKchklsttaxalink` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmchklstprojlink`
--

DROP TABLE IF EXISTS `fmchklstprojlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklstprojlink` (
  `pid` int(10) unsigned NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`,`clid`),
  KEY `FK_chklst` (`clid`),
  CONSTRAINT `FK_chklstprojlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`),
  CONSTRAINT `FK_chklstprojlink_proj` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmchklsttaxalink`
--

DROP TABLE IF EXISTS `fmchklsttaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklsttaxalink` (
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `CLID` int(10) unsigned NOT NULL DEFAULT '0',
  `morphospecies` varchar(45) NOT NULL DEFAULT '',
  `familyoverride` varchar(50) DEFAULT NULL,
  `Habitat` varchar(250) DEFAULT NULL,
  `Abundance` varchar(50) DEFAULT NULL,
  `Notes` varchar(2000) DEFAULT NULL,
  `explicitExclude` smallint(6) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `Nativity` varchar(50) DEFAULT NULL COMMENT 'native, introducted',
  `Endemic` varchar(45) DEFAULT NULL,
  `invasive` varchar(45) DEFAULT NULL,
  `internalnotes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TID`,`CLID`,`morphospecies`),
  KEY `FK_chklsttaxalink_cid` (`CLID`),
  CONSTRAINT `FK_chklsttaxalink_cid` FOREIGN KEY (`CLID`) REFERENCES `fmchecklists` (`CLID`),
  CONSTRAINT `FK_chklsttaxalink_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmchklsttaxastatus`
--

DROP TABLE IF EXISTS `fmchklsttaxastatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklsttaxastatus` (
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `geographicRange` int(11) NOT NULL DEFAULT '0',
  `populationRank` int(11) NOT NULL DEFAULT '0',
  `abundance` int(11) NOT NULL DEFAULT '0',
  `habitatSpecificity` int(11) NOT NULL DEFAULT '0',
  `intrinsicRarity` int(11) NOT NULL DEFAULT '0',
  `threatImminence` int(11) NOT NULL DEFAULT '0',
  `populationTrends` int(11) NOT NULL DEFAULT '0',
  `nativeStatus` varchar(45) DEFAULT NULL,
  `endemicStatus` int(11) NOT NULL DEFAULT '0',
  `protectedStatus` varchar(45) DEFAULT NULL,
  `localitySecurity` int(11) DEFAULT NULL,
  `localitySecurityReason` varchar(45) DEFAULT NULL,
  `invasiveStatus` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clid`,`tid`),
  KEY `FK_fmchklsttaxastatus_clid_idx` (`clid`,`tid`),
  CONSTRAINT `FK_fmchklsttaxastatus_clidtid` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmcltaxacomments`
--

DROP TABLE IF EXISTS `fmcltaxacomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmcltaxacomments` (
  `cltaxacommentsid` int(11) NOT NULL AUTO_INCREMENT,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `ispublic` int(11) NOT NULL DEFAULT '1',
  `parentid` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cltaxacommentsid`),
  KEY `FK_clcomment_users` (`uid`),
  KEY `FK_clcomment_cltaxa` (`clid`,`tid`),
  CONSTRAINT `FK_clcomment_users` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_clcomment_cltaxa` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmdynamicchecklists`
--

DROP TABLE IF EXISTS `fmdynamicchecklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmdynamicchecklists` (
  `dynclid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `details` varchar(250) DEFAULT NULL,
  `uid` varchar(45) DEFAULT NULL,
  `type` varchar(45) NOT NULL DEFAULT 'DynamicList',
  `notes` varchar(250) DEFAULT NULL,
  `expiration` datetime NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dynclid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmdyncltaxalink`
--

DROP TABLE IF EXISTS `fmdyncltaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmdyncltaxalink` (
  `dynclid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dynclid`,`tid`),
  KEY `FK_dyncltaxalink_taxa` (`tid`),
  CONSTRAINT `FK_dyncltaxalink_dynclid` FOREIGN KEY (`dynclid`) REFERENCES `fmdynamicchecklists` (`dynclid`) ON DELETE CASCADE,
  CONSTRAINT `FK_dyncltaxalink_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmprojectcategories`
--

DROP TABLE IF EXISTS `fmprojectcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmprojectcategories` (
  `projcatid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `categoryname` varchar(150) NOT NULL,
  `managers` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `parentpid` int(11) DEFAULT NULL,
  `occurrencesearch` int(11) DEFAULT '0',
  `ispublic` int(11) DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`projcatid`),
  KEY `FK_fmprojcat_pid_idx` (`pid`),
  CONSTRAINT `FK_fmprojcat_pid` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmprojects`
--

DROP TABLE IF EXISTS `fmprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmprojects` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projname` varchar(45) NOT NULL,
  `displayname` varchar(150) DEFAULT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `briefdescription` varchar(300) DEFAULT NULL,
  `fulldescription` varchar(2000) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `occurrencesearch` int(10) unsigned NOT NULL DEFAULT '0',
  `ispublic` int(10) unsigned NOT NULL DEFAULT '0',
  `parentpid` int(10) unsigned DEFAULT NULL,
  `SortSequence` int(10) unsigned NOT NULL DEFAULT '50',
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`),
  KEY `FK_parentpid_proj` (`parentpid`),
  CONSTRAINT `FK_parentpid_proj` FOREIGN KEY (`parentpid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fmvouchers`
--

DROP TABLE IF EXISTS `fmvouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmvouchers` (
  `TID` int(10) unsigned NOT NULL,
  `CLID` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `Collector` varchar(100) DEFAULT NULL,
  `editornotes` varchar(50) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`CLID`,`TID`),
  KEY `chklst_taxavouchers` (`TID`,`CLID`),
  CONSTRAINT `FK_fmvouchers_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_vouchers_cl` FOREIGN KEY (`TID`, `CLID`) REFERENCES `fmchklsttaxalink` (`TID`, `CLID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geothescontinent`
--

DROP TABLE IF EXISTS `geothescontinent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothescontinent` (
  `gtcid` int(11) NOT NULL AUTO_INCREMENT,
  `continentterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtcid`),
  KEY `FK_geothescontinent_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothescontinent_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothescontinent` (`gtcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geothescountry`
--

DROP TABLE IF EXISTS `geothescountry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothescountry` (
  `gtcid` int(11) NOT NULL AUTO_INCREMENT,
  `countryterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `iso` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `numcode` int(11) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `continentid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtcid`),
  KEY `FK_geothescountry__idx` (`continentid`),
  KEY `FK_geothescountry_parent_idx` (`acceptedid`),
  CONSTRAINT `FK_geothescountry_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothescountry` (`gtcid`),
  CONSTRAINT `FK_geothescountry_gtcid` FOREIGN KEY (`continentid`) REFERENCES `geothescontinent` (`gtcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geothescounty`
--

DROP TABLE IF EXISTS `geothescounty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothescounty` (
  `gtcoid` int(11) NOT NULL AUTO_INCREMENT,
  `countyterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `stateid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtcoid`),
  KEY `FK_geothescounty_state_idx` (`stateid`),
  KEY `FK_geothescounty_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothescounty_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothescounty` (`gtcoid`),
  CONSTRAINT `FK_geothescounty_state` FOREIGN KEY (`stateid`) REFERENCES `geothesstateprovince` (`gtspid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geothesmunicipality`
--

DROP TABLE IF EXISTS `geothesmunicipality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothesmunicipality` (
  `gtmid` int(11) NOT NULL AUTO_INCREMENT,
  `municipalityterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `countyid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtmid`),
  KEY `FK_geothesmunicipality_county_idx` (`countyid`),
  KEY `FK_geothesmunicipality_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothesmunicipality_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothesmunicipality` (`gtmid`),
  CONSTRAINT `FK_geothesmunicipality_county` FOREIGN KEY (`countyid`) REFERENCES `geothescounty` (`gtcoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `geothesstateprovince`
--

DROP TABLE IF EXISTS `geothesstateprovince`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothesstateprovince` (
  `gtspid` int(11) NOT NULL AUTO_INCREMENT,
  `stateterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `countryid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtspid`),
  KEY `FK_geothesstate_country_idx` (`countryid`),
  KEY `FK_geothesstate_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothesstate_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothesstateprovince` (`gtspid`),
  CONSTRAINT `FK_geothesstate_country` FOREIGN KEY (`countryid`) REFERENCES `geothescountry` (`gtcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `glossary`
--

DROP TABLE IF EXISTS `glossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossary` (
  `glossid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(150) NOT NULL,
  `definition` varchar(600) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'English',
  `source` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossid`),
  KEY `Index_term` (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `glossaryimages`
--

DROP TABLE IF EXISTS `glossaryimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossaryimages` (
  `glimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `glossid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnailurl` varchar(255) DEFAULT NULL,
  `structures` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glimgid`),
  KEY `FK_glossaryimages_gloss` (`glossid`),
  CONSTRAINT `FK_glossaryimages_gloss` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `glossarytaxalink`
--

DROP TABLE IF EXISTS `glossarytaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossarytaxalink` (
  `glossgrpid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossgrpid`,`tid`),
  KEY `glossarytaxalink_ibfk_1` (`tid`),
  CONSTRAINT `glossarytaxalink_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `glossarytaxalink_ibfk_2` FOREIGN KEY (`glossgrpid`) REFERENCES `glossarytermlink` (`glossgrpid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `glossarytermlink`
--

DROP TABLE IF EXISTS `glossarytermlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossarytermlink` (
  `gltlinkid` int(10) NOT NULL AUTO_INCREMENT,
  `glossgrpid` int(10) unsigned NOT NULL,
  `glossid` int(10) unsigned NOT NULL,
  `relationshipType` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gltlinkid`),
  UNIQUE KEY `Unique_termkeys` (`glossgrpid`,`glossid`),
  KEY `glossarytermlink_ibfk_1` (`glossid`),
  CONSTRAINT `glossarytermlink_ibfk_1` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guidimages`
--

DROP TABLE IF EXISTS `guidimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guidimages` (
  `guid` varchar(45) NOT NULL,
  `imgid` int(10) unsigned DEFAULT NULL,
  `archivestatus` int(3) NOT NULL DEFAULT '0',
  `archiveobj` text,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `guidimages_imgid_unique` (`imgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guidoccurdeterminations`
--

DROP TABLE IF EXISTS `guidoccurdeterminations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guidoccurdeterminations` (
  `guid` varchar(45) NOT NULL,
  `detid` int(10) unsigned DEFAULT NULL,
  `archivestatus` int(3) NOT NULL DEFAULT '0',
  `archiveobj` text,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `guidoccurdet_detid_unique` (`detid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `guidoccurrences`
--

DROP TABLE IF EXISTS `guidoccurrences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guidoccurrences` (
  `guid` varchar(45) NOT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `archivestatus` int(3) NOT NULL DEFAULT '0',
  `archiveobj` text,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `guidoccurrences_occid_unique` (`occid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imageannotations`
--

DROP TABLE IF EXISTS `imageannotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imageannotations` (
  `tid` int(10) unsigned DEFAULT NULL,
  `imgid` int(10) unsigned NOT NULL DEFAULT '0',
  `AnnDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Annotator` varchar(100) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`,`AnnDate`) USING BTREE,
  KEY `TID` (`tid`) USING BTREE,
  CONSTRAINT `FK_resourceannotations_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`),
  CONSTRAINT `FK_resourceannotations_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imagekeywords`
--

DROP TABLE IF EXISTS `imagekeywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagekeywords` (
  `imgkeywordid` int(11) NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned NOT NULL,
  `keyword` varchar(45) NOT NULL,
  `uidassignedby` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgkeywordid`),
  KEY `FK_imagekeywords_imgid_idx` (`imgid`),
  KEY `FK_imagekeyword_uid_idx` (`uidassignedby`),
  KEY `INDEX_imagekeyword` (`keyword`),
  CONSTRAINT `FK_imagekeywords_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imagekeyword_uid` FOREIGN KEY (`uidassignedby`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imageprojectlink`
--

DROP TABLE IF EXISTS `imageprojectlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imageprojectlink` (
  `imgid` int(10) unsigned NOT NULL,
  `imgprojid` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`,`imgprojid`),
  KEY `FK_imageprojlink_imgprojid_idx` (`imgprojid`),
  CONSTRAINT `FK_imageprojectlink_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imageprojlink_imgprojid` FOREIGN KEY (`imgprojid`) REFERENCES `imageprojects` (`imgprojid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imageprojects`
--

DROP TABLE IF EXISTS `imageprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imageprojects` (
  `imgprojid` int(11) NOT NULL AUTO_INCREMENT,
  `projectname` varchar(75) NOT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `ispublic` int(11) NOT NULL DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `uidcreated` int(11) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT '50',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgprojid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `imgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnailurl` varchar(255) DEFAULT NULL,
  `originalurl` varchar(255) DEFAULT NULL,
  `archiveurl` varchar(255) DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `photographeruid` int(10) unsigned DEFAULT NULL,
  `imagetype` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `owner` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `rights` varchar(255) DEFAULT NULL,
  `accessrights` varchar(255) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(350) DEFAULT NULL,
  `anatomy` varchar(100) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT '50',
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`) USING BTREE,
  KEY `Index_tid` (`tid`),
  KEY `FK_images_occ` (`occid`),
  KEY `FK_photographeruid` (`photographeruid`),
  CONSTRAINT `FK_images_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`),
  CONSTRAINT `FK_photographeruid` FOREIGN KEY (`photographeruid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_taxaimagestid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imagetag`
--

DROP TABLE IF EXISTS `imagetag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagetag` (
  `imagetagid` bigint(20) NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned NOT NULL,
  `keyvalue` varchar(30) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imagetagid`),
  UNIQUE KEY `imgid` (`imgid`,`keyvalue`),
  KEY `keyvalue` (`keyvalue`),
  KEY `FK_imagetag_imgid_idx` (`imgid`),
  CONSTRAINT `FK_imagetag_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imagetag_tagkey` FOREIGN KEY (`keyvalue`) REFERENCES `imagetagkey` (`tagkey`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imagetagkey`
--

DROP TABLE IF EXISTS `imagetagkey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagetagkey` (
  `tagkey` varchar(30) NOT NULL,
  `shortlabel` varchar(30) NOT NULL,
  `description_en` varchar(255) NOT NULL,
  `sortorder` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tagkey`),
  KEY `sortorder` (`sortorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `iid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `InstitutionCode` varchar(45) NOT NULL,
  `InstitutionName` varchar(150) NOT NULL,
  `InstitutionName2` varchar(150) DEFAULT NULL,
  `Address1` varchar(150) DEFAULT NULL,
  `Address2` varchar(150) DEFAULT NULL,
  `City` varchar(45) DEFAULT NULL,
  `StateProvince` varchar(45) DEFAULT NULL,
  `PostalCode` varchar(45) DEFAULT NULL,
  `Country` varchar(45) DEFAULT NULL,
  `Phone` varchar(45) DEFAULT NULL,
  `Contact` varchar(65) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Url` varchar(250) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `IntialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iid`),
  KEY `FK_inst_uid_idx` (`modifieduid`),
  CONSTRAINT `FK_inst_uid` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmcharacterlang`
--

DROP TABLE IF EXISTS `kmcharacterlang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcharacterlang` (
  `cid` int(10) unsigned NOT NULL,
  `charname` varchar(150) NOT NULL,
  `language` varchar(45) NOT NULL,
  `langid` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `helpurl` varchar(500) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`,`langid`) USING BTREE,
  CONSTRAINT `FK_characterlang_1` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmcharacters`
--

DROP TABLE IF EXISTS `kmcharacters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcharacters` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `charname` varchar(150) NOT NULL,
  `chartype` varchar(2) NOT NULL DEFAULT 'UM',
  `defaultlang` varchar(45) NOT NULL DEFAULT 'English',
  `difficultyrank` smallint(5) unsigned NOT NULL DEFAULT '1',
  `hid` int(10) unsigned DEFAULT NULL,
  `units` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `helpurl` varchar(500) DEFAULT NULL,
  `enteredby` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`),
  KEY `Index_charname` (`charname`),
  KEY `Index_sort` (`sortsequence`),
  KEY `FK_charheading_idx` (`hid`),
  CONSTRAINT `FK_charheading` FOREIGN KEY (`hid`) REFERENCES `kmcharheading` (`hid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmchardependance`
--

DROP TABLE IF EXISTS `kmchardependance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmchardependance` (
  `CID` int(10) unsigned NOT NULL,
  `CIDDependance` int(10) unsigned NOT NULL,
  `CSDependance` varchar(16) NOT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CSDependance`,`CIDDependance`,`CID`) USING BTREE,
  KEY `FK_chardependance_cid_idx` (`CID`),
  KEY `FK_chardependance_cs_idx` (`CIDDependance`,`CSDependance`),
  CONSTRAINT `FK_chardependance_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_chardependance_cs` FOREIGN KEY (`CIDDependance`, `CSDependance`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmcharheading`
--

DROP TABLE IF EXISTS `kmcharheading`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcharheading` (
  `hid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `headingname` varchar(255) NOT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'English',
  `langid` int(11) NOT NULL,
  `notes` longtext,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hid`,`langid`) USING BTREE,
  UNIQUE KEY `unique_kmcharheading` (`headingname`,`langid`),
  KEY `HeadingName` (`headingname`) USING BTREE,
  KEY `FK_kmcharheading_lang_idx` (`langid`),
  CONSTRAINT `FK_kmcharheading_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmchartaxalink`
--

DROP TABLE IF EXISTS `kmchartaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmchartaxalink` (
  `CID` int(10) unsigned NOT NULL DEFAULT '0',
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `Status` varchar(50) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  `Relation` varchar(45) NOT NULL DEFAULT 'include',
  `EditabilityInherited` bit(1) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CID`,`TID`),
  KEY `FK_CharTaxaLink-TID` (`TID`),
  CONSTRAINT `FK_chartaxalink_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`),
  CONSTRAINT `FK_chartaxalink_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmcs`
--

DROP TABLE IF EXISTS `kmcs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcs` (
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `cs` varchar(16) NOT NULL,
  `CharStateName` varchar(255) DEFAULT NULL,
  `Implicit` tinyint(1) NOT NULL DEFAULT '0',
  `Notes` longtext,
  `Description` varchar(255) DEFAULT NULL,
  `IllustrationUrl` varchar(250) DEFAULT NULL,
  `StateID` int(10) unsigned DEFAULT NULL,
  `SortSequence` int(10) unsigned DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EnteredBy` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cs`,`cid`),
  KEY `FK_cs_chars` (`cid`),
  CONSTRAINT `FK_cs_chars` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmcsimages`
--

DROP TABLE IF EXISTS `kmcsimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcsimages` (
  `csimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) unsigned NOT NULL,
  `cs` varchar(16) NOT NULL,
  `url` varchar(255) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` varchar(45) NOT NULL DEFAULT '50',
  `username` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`csimgid`),
  KEY `FK_kscsimages_kscs_idx` (`cid`,`cs`),
  CONSTRAINT `FK_kscsimages_kscs` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmcslang`
--

DROP TABLE IF EXISTS `kmcslang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcslang` (
  `cid` int(10) unsigned NOT NULL,
  `cs` varchar(16) NOT NULL,
  `charstatename` varchar(150) NOT NULL,
  `language` varchar(45) NOT NULL,
  `langid` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `intialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`,`cs`,`langid`),
  CONSTRAINT `FK_cslang_1` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmdescr`
--

DROP TABLE IF EXISTS `kmdescr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmdescr` (
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `CID` int(10) unsigned NOT NULL DEFAULT '0',
  `Modifier` varchar(255) DEFAULT NULL,
  `CS` varchar(16) NOT NULL,
  `X` double(15,5) DEFAULT NULL,
  `TXT` longtext,
  `PseudoTrait` int(5) unsigned DEFAULT '0',
  `Frequency` int(5) unsigned NOT NULL DEFAULT '5' COMMENT 'Frequency of occurrence; 1 = rare... 5 = common',
  `Inherited` varchar(50) DEFAULT NULL,
  `Source` varchar(100) DEFAULT NULL,
  `Seq` int(10) DEFAULT NULL,
  `Notes` longtext,
  `DateEntered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TID`,`CID`,`CS`),
  KEY `CSDescr` (`CID`,`CS`),
  CONSTRAINT `FK_descr_cs` FOREIGN KEY (`CID`, `CS`) REFERENCES `kmcs` (`cid`, `cs`),
  CONSTRAINT `FK_descr_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kmdescrdeletions`
--

DROP TABLE IF EXISTS `kmdescrdeletions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmdescrdeletions` (
  `TID` int(10) unsigned NOT NULL,
  `CID` int(10) unsigned NOT NULL,
  `CS` varchar(16) NOT NULL,
  `Modifier` varchar(255) DEFAULT NULL,
  `X` double(15,5) DEFAULT NULL,
  `TXT` longtext,
  `Inherited` varchar(50) DEFAULT NULL,
  `Source` varchar(100) DEFAULT NULL,
  `Seq` int(10) unsigned DEFAULT NULL,
  `Notes` longtext,
  `InitialTimeStamp` datetime DEFAULT NULL,
  `DeletedBy` varchar(100) NOT NULL,
  `DeletedTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `PK` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`PK`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lkupcountry`
--

DROP TABLE IF EXISTS `lkupcountry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkupcountry` (
  `countryId` int(11) NOT NULL AUTO_INCREMENT,
  `countryName` varchar(100) NOT NULL,
  `iso` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `numcode` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`countryId`),
  UNIQUE KEY `country_unique` (`countryName`),
  KEY `Index_lkupcountry_iso` (`iso`),
  KEY `Index_lkupcountry_iso3` (`iso3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lkupcounty`
--

DROP TABLE IF EXISTS `lkupcounty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkupcounty` (
  `countyId` int(11) NOT NULL AUTO_INCREMENT,
  `stateId` int(11) NOT NULL,
  `countyName` varchar(100) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`countyId`),
  UNIQUE KEY `unique_county` (`stateId`,`countyName`),
  KEY `fk_stateprovince` (`stateId`),
  KEY `index_countyname` (`countyName`),
  CONSTRAINT `fk_stateprovince` FOREIGN KEY (`stateId`) REFERENCES `lkupstateprovince` (`stateId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lkupstateprovince`
--

DROP TABLE IF EXISTS `lkupstateprovince`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkupstateprovince` (
  `stateId` int(11) NOT NULL AUTO_INCREMENT,
  `countryId` int(11) NOT NULL,
  `stateName` varchar(100) NOT NULL,
  `abbrev` varchar(2) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stateId`),
  UNIQUE KEY `state_index` (`stateName`,`countryId`),
  KEY `fk_country` (`countryId`),
  KEY `index_statename` (`stateName`),
  KEY `Index_lkupstate_abbr` (`abbrev`),
  CONSTRAINT `fk_country` FOREIGN KEY (`countryId`) REFERENCES `lkupcountry` (`countryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `mediaid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `url` varchar(250) NOT NULL,
  `caption` varchar(250) DEFAULT NULL,
  `authoruid` int(10) unsigned DEFAULT NULL,
  `author` varchar(45) DEFAULT NULL,
  `mediatype` varchar(45) DEFAULT NULL,
  `owner` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(250) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mediaid`),
  KEY `FK_media_taxa_idx` (`tid`),
  KEY `FK_media_occid_idx` (`occid`),
  KEY `FK_media_uid_idx` (`authoruid`),
  CONSTRAINT `FK_media_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_media_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_media_uid` FOREIGN KEY (`authoruid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcollcategories`
--

DROP TABLE IF EXISTS `omcollcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollcategories` (
  `ccpk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(75) NOT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `acronym` varchar(45) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `inclusive` int(2) DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ccpk`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcollcatlink`
--

DROP TABLE IF EXISTS `omcollcatlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollcatlink` (
  `ccpk` int(10) unsigned NOT NULL,
  `collid` int(10) unsigned NOT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ccpk`,`collid`),
  KEY `FK_collcatlink_coll` (`collid`),
  CONSTRAINT `FK_collcatlink_cat` FOREIGN KEY (`ccpk`) REFERENCES `omcollcategories` (`ccpk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_collcatlink_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcollections`
--

DROP TABLE IF EXISTS `omcollections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollections` (
  `CollID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `InstitutionCode` varchar(45) NOT NULL,
  `CollectionCode` varchar(45) DEFAULT NULL,
  `CollectionName` varchar(150) NOT NULL,
  `collectionId` varchar(100) DEFAULT NULL,
  `datasetName` varchar(100) DEFAULT NULL,
  `iid` int(10) unsigned DEFAULT NULL,
  `fulldescription` varchar(2000) DEFAULT NULL,
  `Homepage` varchar(250) DEFAULT NULL,
  `IndividualUrl` varchar(500) DEFAULT NULL,
  `Contact` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `latitudedecimal` decimal(8,6) DEFAULT NULL,
  `longitudedecimal` decimal(9,6) DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `CollType` varchar(45) NOT NULL DEFAULT 'Preserved Specimens' COMMENT 'Preserved Specimens, General Observations, Observations',
  `ManagementType` varchar(45) DEFAULT 'Snapshot' COMMENT 'Snapshot, Live Data',
  `PublicEdits` int(1) unsigned NOT NULL DEFAULT '1',
  `collectionguid` varchar(45) DEFAULT NULL,
  `securitykey` varchar(45) DEFAULT NULL,
  `guidtarget` varchar(45) DEFAULT NULL,
  `rightsHolder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `usageTerm` varchar(250) DEFAULT NULL,
  `publishToGbif` int(11) DEFAULT NULL,
  `bibliographicCitation` varchar(1000) DEFAULT NULL,
  `accessrights` varchar(1000) DEFAULT NULL,
  `SortSeq` int(10) unsigned DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CollID`) USING BTREE,
  UNIQUE KEY `Index_inst` (`InstitutionCode`,`CollectionCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcollectionstats`
--

DROP TABLE IF EXISTS `omcollectionstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollectionstats` (
  `collid` int(10) unsigned NOT NULL,
  `recordcnt` int(10) unsigned NOT NULL DEFAULT '0',
  `georefcnt` int(10) unsigned DEFAULT NULL,
  `familycnt` int(10) unsigned DEFAULT NULL,
  `genuscnt` int(10) unsigned DEFAULT NULL,
  `speciescnt` int(10) unsigned DEFAULT NULL,
  `uploaddate` datetime DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `uploadedby` varchar(45) DEFAULT NULL,
  `dynamicProperties` varchar(500) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`collid`),
  CONSTRAINT `FK_collectionstats_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcollectors`
--

DROP TABLE IF EXISTS `omcollectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollectors` (
  `recordedById` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `familyname` varchar(45) NOT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `middlename` varchar(45) DEFAULT NULL,
  `startyearactive` int(11) DEFAULT NULL,
  `endyearactive` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT '10',
  `guid` varchar(45) DEFAULT NULL,
  `preferredrecbyid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recordedById`),
  KEY `fullname` (`familyname`,`firstname`),
  KEY `FK_preferred_recby_idx` (`preferredrecbyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcollpublications`
--

DROP TABLE IF EXISTS `omcollpublications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollpublications` (
  `pubid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `targeturl` varchar(250) NOT NULL,
  `securityguid` varchar(45) NOT NULL,
  `criteriajson` varchar(250) DEFAULT NULL,
  `includedeterminations` int(11) DEFAULT '1',
  `includeimages` int(11) DEFAULT '1',
  `autoupdate` int(11) DEFAULT '0',
  `lastdateupdate` datetime DEFAULT NULL,
  `updateinterval` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pubid`),
  KEY `FK_adminpub_collid_idx` (`collid`),
  CONSTRAINT `FK_adminpub_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcollsecondary`
--

DROP TABLE IF EXISTS `omcollsecondary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollsecondary` (
  `ocsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `InstitutionCode` varchar(45) NOT NULL,
  `CollectionCode` varchar(45) DEFAULT NULL,
  `CollectionName` varchar(150) NOT NULL,
  `BriefDescription` varchar(300) DEFAULT NULL,
  `FullDescription` varchar(1000) DEFAULT NULL,
  `Homepage` varchar(250) DEFAULT NULL,
  `IndividualUrl` varchar(500) DEFAULT NULL,
  `Contact` varchar(45) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `LatitudeDecimal` double DEFAULT NULL,
  `LongitudeDecimal` double DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `CollType` varchar(45) DEFAULT NULL,
  `SortSeq` int(10) unsigned DEFAULT NULL,
  `InitialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocsid`),
  KEY `FK_omcollsecondary_coll` (`collid`),
  CONSTRAINT `FK_omcollsecondary_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcrowdsourcecentral`
--

DROP TABLE IF EXISTS `omcrowdsourcecentral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcrowdsourcecentral` (
  `omcsid` int(11) NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `instructions` text,
  `trainingurl` varchar(500) DEFAULT NULL,
  `editorlevel` int(11) NOT NULL DEFAULT '0' COMMENT '0=public, 1=public limited, 2=private',
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omcsid`),
  UNIQUE KEY `Index_omcrowdsourcecentral_collid` (`collid`),
  KEY `FK_omcrowdsourcecentral_collid` (`collid`),
  CONSTRAINT `FK_omcrowdsourcecentral_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omcrowdsourcequeue`
--

DROP TABLE IF EXISTS `omcrowdsourcequeue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcrowdsourcequeue` (
  `idomcrowdsourcequeue` int(11) NOT NULL AUTO_INCREMENT,
  `omcsid` int(11) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `reviewstatus` int(11) NOT NULL DEFAULT '0' COMMENT '0=open,5=pending review, 10=closed',
  `uidprocessor` int(10) unsigned DEFAULT NULL,
  `points` int(11) DEFAULT NULL COMMENT '0=fail, 1=minor edits, 2=no edits <default>, 3=excelled',
  `isvolunteer` int(2) NOT NULL DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idomcrowdsourcequeue`),
  UNIQUE KEY `Index_omcrowdsource_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_uid` (`uidprocessor`),
  CONSTRAINT `FK_omcrowdsourcequeue_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omcrowdsourcequeue_uid` FOREIGN KEY (`uidprocessor`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omexsiccatinumbers`
--

DROP TABLE IF EXISTS `omexsiccatinumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omexsiccatinumbers` (
  `omenid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exsnumber` varchar(45) NOT NULL,
  `ometid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omenid`),
  UNIQUE KEY `Index_omexsiccatinumbers_unique` (`exsnumber`,`ometid`),
  KEY `FK_exsiccatiTitleNumber` (`ometid`),
  CONSTRAINT `FK_exsiccatiTitleNumber` FOREIGN KEY (`ometid`) REFERENCES `omexsiccatititles` (`ometid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omexsiccatiocclink`
--

DROP TABLE IF EXISTS `omexsiccatiocclink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omexsiccatiocclink` (
  `omenid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `ranking` int(11) NOT NULL DEFAULT '50',
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omenid`,`occid`),
  UNIQUE KEY `UniqueOmexsiccatiOccLink` (`occid`),
  KEY `FKExsiccatiNumOccLink1` (`omenid`),
  KEY `FKExsiccatiNumOccLink2` (`occid`),
  CONSTRAINT `FKExsiccatiNumOccLink1` FOREIGN KEY (`omenid`) REFERENCES `omexsiccatinumbers` (`omenid`) ON DELETE CASCADE,
  CONSTRAINT `FKExsiccatiNumOccLink2` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omexsiccatititles`
--

DROP TABLE IF EXISTS `omexsiccatititles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omexsiccatititles` (
  `ometid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `abbreviation` varchar(100) DEFAULT NULL,
  `editor` varchar(150) DEFAULT NULL,
  `exsrange` varchar(45) DEFAULT NULL,
  `startdate` varchar(45) DEFAULT NULL,
  `enddate` varchar(45) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `notes` varchar(2000) DEFAULT NULL,
  `lasteditedby` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ometid`),
  KEY `index_exsiccatiTitle` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurassococcurrences`
--

DROP TABLE IF EXISTS `omoccurassococcurrences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurassococcurrences` (
  `aoid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `occidassociate` int(10) unsigned DEFAULT NULL,
  `relationship` varchar(150) NOT NULL,
  `identifier` varchar(250) DEFAULT NULL COMMENT 'e.g. GUID',
  `resourceurl` varchar(250) DEFAULT NULL,
  `sciname` varchar(250) DEFAULT NULL,
  `tid` int(11) DEFAULT NULL,
  `locationOnHost` varchar(250) DEFAULT NULL,
  `condition` varchar(250) DEFAULT NULL,
  `dateEmerged` datetime DEFAULT NULL,
  `dynamicProperties` text,
  `notes` varchar(250) DEFAULT NULL,
  `createdby` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`aoid`),
  KEY `omossococcur_occid_idx` (`occid`),
  KEY `omossococcur_occidassoc_idx` (`occidassociate`),
  CONSTRAINT `omossococcur_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `omossococcur_occidassoc` FOREIGN KEY (`occidassociate`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurassoctaxa`
--

DROP TABLE IF EXISTS `omoccurassoctaxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurassoctaxa` (
  `assoctaxaid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned DEFAULT NULL,
  `verbatimstr` varchar(250) DEFAULT NULL,
  `relationship` varchar(45) DEFAULT NULL,
  `verificationscore` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`assoctaxaid`),
  KEY `FK_assoctaxa_occid_idx` (`occid`),
  KEY `FK_aooctaxa_tid_idx` (`tid`),
  KEY `INDEX_verbatim_str` (`verbatimstr`),
  CONSTRAINT `FK_assoctaxa_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_aooctaxa_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurcomments`
--

DROP TABLE IF EXISTS `omoccurcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurcomments` (
  `comid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `reviewstatus` int(10) unsigned NOT NULL DEFAULT '0',
  `parentcomid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comid`),
  KEY `fk_omoccurcomments_occid` (`occid`),
  KEY `fk_omoccurcomments_uid` (`uid`),
  CONSTRAINT `fk_omoccurcomments_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccurcomments_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurdatasetlink`
--

DROP TABLE IF EXISTS `omoccurdatasetlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurdatasetlink` (
  `occid` int(10) unsigned NOT NULL,
  `datasetid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`datasetid`),
  KEY `FK_omoccurdatasetlink_datasetid` (`datasetid`),
  KEY `FK_omoccurdatasetlink_occid` (`occid`),
  CONSTRAINT `FK_omoccurdatasetlink_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasetlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurdatasets`
--

DROP TABLE IF EXISTS `omoccurdatasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurdatasets` (
  `datasetid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `uid` int(11) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`datasetid`),
  KEY `FK_omoccurdatasets_uid_idx` (`uid`),
  KEY `FK_omcollections_collid_idx` (`collid`),
  CONSTRAINT `FK_omoccurdatasets_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omcollections_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurdeterminations`
--

DROP TABLE IF EXISTS `omoccurdeterminations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurdeterminations` (
  `detid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifiedBy` varchar(60) NOT NULL,
  `idbyid` int(10) unsigned DEFAULT NULL,
  `dateIdentified` varchar(45) NOT NULL,
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `sciname` varchar(100) NOT NULL,
  `tidinterpreted` int(10) unsigned DEFAULT NULL,
  `scientificNameAuthorship` varchar(100) DEFAULT NULL,
  `identificationQualifier` varchar(45) DEFAULT NULL,
  `iscurrent` int(2) DEFAULT '0',
  `printqueue` int(2) DEFAULT '0',
  `appliedStatus` int(2) DEFAULT '1',
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(255) DEFAULT NULL,
  `identificationRemarks` varchar(255) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT '10',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`detid`),
  UNIQUE KEY `Index_unique` (`occid`,`dateIdentified`,`identifiedBy`,`sciname`),
  KEY `FK_omoccurdets_tid` (`tidinterpreted`),
  KEY `FK_omoccurdets_idby_idx` (`idbyid`),
  KEY `Index_dateIdentInterpreted` (`dateIdentifiedInterpreted`),
  CONSTRAINT `FK_omoccurdets_idby` FOREIGN KEY (`idbyid`) REFERENCES `omcollectors` (`recordedById`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_omoccurdets_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdets_tid` FOREIGN KEY (`tidinterpreted`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurduplicatelink`
--

DROP TABLE IF EXISTS `omoccurduplicatelink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurduplicatelink` (
  `occid` int(10) unsigned NOT NULL,
  `duplicateid` int(11) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`duplicateid`),
  KEY `FK_omoccurdupelink_occid_idx` (`occid`),
  KEY `FK_omoccurdupelink_dupeid_idx` (`duplicateid`),
  CONSTRAINT `FK_omoccurdupelink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdupelink_dupeid` FOREIGN KEY (`duplicateid`) REFERENCES `omoccurduplicates` (`duplicateid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurduplicates`
--

DROP TABLE IF EXISTS `omoccurduplicates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurduplicates` (
  `duplicateid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `dupeType` varchar(45) NOT NULL DEFAULT 'Exact Duplicate',
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`duplicateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccureditlocks`
--

DROP TABLE IF EXISTS `omoccureditlocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccureditlocks` (
  `occid` int(10) unsigned NOT NULL,
  `uid` int(11) NOT NULL,
  `ts` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccuredits`
--

DROP TABLE IF EXISTS `omoccuredits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuredits` (
  `ocedid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `FieldName` varchar(45) NOT NULL,
  `FieldValueNew` text NOT NULL,
  `FieldValueOld` text NOT NULL,
  `ReviewStatus` int(1) NOT NULL DEFAULT '1' COMMENT '1=Open;2=Pending;3=Closed',
  `AppliedStatus` int(1) NOT NULL DEFAULT '0' COMMENT '0=Not Applied;1=Applied',
  `uid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocedid`),
  KEY `fk_omoccuredits_uid` (`uid`),
  KEY `fk_omoccuredits_occid` (`occid`),
  CONSTRAINT `fk_omoccuredits_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccuredits_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurexchange`
--

DROP TABLE IF EXISTS `omoccurexchange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurexchange` (
  `exchangeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(30) DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `iid` int(10) unsigned DEFAULT NULL,
  `transactionType` varchar(10) DEFAULT NULL,
  `in_out` varchar(3) DEFAULT NULL,
  `dateSent` date DEFAULT NULL,
  `dateReceived` date DEFAULT NULL,
  `totalBoxes` int(5) DEFAULT NULL,
  `shippingMethod` varchar(50) DEFAULT NULL,
  `totalExMounted` int(5) DEFAULT NULL,
  `totalExUnmounted` int(5) DEFAULT NULL,
  `totalGift` int(5) DEFAULT NULL,
  `totalGiftDet` int(5) DEFAULT NULL,
  `adjustment` int(5) DEFAULT NULL,
  `invoiceBalance` int(6) DEFAULT NULL,
  `invoiceMessage` varchar(500) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `createdBy` varchar(20) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`exchangeid`),
  KEY `FK_occexch_coll` (`collid`),
  CONSTRAINT `FK_occexch_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurgenetic`
--

DROP TABLE IF EXISTS `omoccurgenetic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurgenetic` (
  `idoccurgenetic` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifier` varchar(150) DEFAULT NULL,
  `resourcename` varchar(150) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `locus` varchar(500) DEFAULT NULL,
  `resourceurl` varchar(500) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `initialtimestamp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idoccurgenetic`),
  KEY `FK_omoccurgenetic` (`occid`),
  KEY `INDEX_omoccurgenetic_name` (`resourcename`),
  CONSTRAINT `FK_omoccurgenetic` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurgeoindex`
--

DROP TABLE IF EXISTS `omoccurgeoindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurgeoindex` (
  `tid` int(10) unsigned NOT NULL,
  `decimallatitude` double NOT NULL,
  `decimallongitude` double NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`decimallatitude`,`decimallongitude`),
  CONSTRAINT `FK_specgeoindex_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccuridentifiers`
--

DROP TABLE IF EXISTS `omoccuridentifiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuridentifiers` (
  `idomoccuridentifiers` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifiervalue` varchar(45) NOT NULL,
  `identifiername` varchar(45) DEFAULT NULL COMMENT 'barcode, accession number, old catalog number, NPS, etc',
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idomoccuridentifiers`),
  KEY `FK_omoccuridentifiers_occid_idx` (`occid`),
  KEY `Index_value` (`identifiervalue`),
  CONSTRAINT `FK_omoccuridentifiers_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurloans`
--

DROP TABLE IF EXISTS `omoccurloans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurloans` (
  `loanid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loanIdentifierOwn` varchar(30) DEFAULT NULL,
  `loanIdentifierBorr` varchar(30) DEFAULT NULL,
  `collidOwn` int(10) unsigned DEFAULT NULL,
  `collidBorr` int(10) unsigned DEFAULT NULL,
  `iidOwner` int(10) unsigned DEFAULT NULL,
  `iidBorrower` int(10) unsigned DEFAULT NULL,
  `dateSent` date DEFAULT NULL,
  `dateSentReturn` date DEFAULT NULL,
  `receivedStatus` varchar(250) DEFAULT NULL,
  `totalBoxes` int(5) DEFAULT NULL,
  `totalBoxesReturned` int(5) DEFAULT NULL,
  `numSpecimens` int(5) DEFAULT NULL,
  `shippingMethod` varchar(50) DEFAULT NULL,
  `shippingMethodReturn` varchar(50) DEFAULT NULL,
  `dateDue` date DEFAULT NULL,
  `dateReceivedOwn` date DEFAULT NULL,
  `dateReceivedBorr` date DEFAULT NULL,
  `dateClosed` date DEFAULT NULL,
  `forWhom` varchar(50) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `invoiceMessageOwn` varchar(500) DEFAULT NULL,
  `invoiceMessageBorr` varchar(500) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `createdByOwn` varchar(30) DEFAULT NULL,
  `createdByBorr` varchar(30) DEFAULT NULL,
  `processingStatus` int(5) unsigned DEFAULT '1',
  `processedByOwn` varchar(30) DEFAULT NULL,
  `processedByBorr` varchar(30) DEFAULT NULL,
  `processedByReturnOwn` varchar(30) DEFAULT NULL,
  `processedByReturnBorr` varchar(30) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`loanid`),
  KEY `FK_occurloans_owninst` (`iidOwner`),
  KEY `FK_occurloans_borrinst` (`iidBorrower`),
  KEY `FK_occurloans_owncoll` (`collidOwn`),
  KEY `FK_occurloans_borrcoll` (`collidBorr`),
  CONSTRAINT `FK_occurloans_borrcoll` FOREIGN KEY (`collidBorr`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_borrinst` FOREIGN KEY (`iidBorrower`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owncoll` FOREIGN KEY (`collidOwn`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owninst` FOREIGN KEY (`iidOwner`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurloanslink`
--

DROP TABLE IF EXISTS `omoccurloanslink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurloanslink` (
  `loanid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `returndate` date DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`loanid`,`occid`),
  KEY `FK_occurloanlink_occid` (`occid`),
  KEY `FK_occurloanlink_loanid` (`loanid`),
  CONSTRAINT `FK_occurloanlink_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloanlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurpoints`
--

DROP TABLE IF EXISTS `omoccurpoints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurpoints` (
  `geoID` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(11) NOT NULL,
  `point` point NOT NULL,
  `errradiuspoly` polygon DEFAULT NULL,
  `footprintpoly` polygon DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`geoID`),
  UNIQUE KEY `occid` (`occid`),
  SPATIAL KEY `point` (`point`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurrences`
--

DROP TABLE IF EXISTS `omoccurrences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurrences` (
  `occid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `basisOfRecord` varchar(32) DEFAULT 'PreservedSpecimen' COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
  `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
  `catalogNumber` varchar(32) DEFAULT NULL,
  `otherCatalogNumbers` varchar(255) DEFAULT NULL,
  `ownerInstitutionCode` varchar(32) DEFAULT NULL,
  `institutionID` varchar(255) DEFAULT NULL,
  `collectionID` varchar(255) DEFAULT NULL,
  `datasetID` varchar(255) DEFAULT NULL,
  `institutionCode` varchar(64) DEFAULT NULL,
  `collectionCode` varchar(64) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `scientificName` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) DEFAULT NULL,
  `tidinterpreted` int(10) unsigned DEFAULT NULL,
  `genus` varchar(255) DEFAULT NULL,
  `specificEpithet` varchar(255) DEFAULT NULL,
  `taxonRank` varchar(32) DEFAULT NULL,
  `infraspecificEpithet` varchar(255) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `taxonRemarks` text,
  `identifiedBy` varchar(255) DEFAULT NULL,
  `dateIdentified` varchar(45) DEFAULT NULL,
  `identificationReferences` text,
  `identificationRemarks` text,
  `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
  `typeStatus` varchar(255) DEFAULT NULL,
  `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
  `recordNumber` varchar(45) DEFAULT NULL COMMENT 'Collector Number',
  `recordedbyid` bigint(20) DEFAULT NULL,
  `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `eventDate` date DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `day` int(10) DEFAULT NULL,
  `startDayOfYear` int(10) DEFAULT NULL,
  `endDayOfYear` int(10) DEFAULT NULL,
  `verbatimEventDate` varchar(255) DEFAULT NULL,
  `habitat` text COMMENT 'Habitat, substrait, etc',
  `substrate` varchar(500) DEFAULT NULL,
  `fieldNotes` text,
  `fieldnumber` varchar(45) DEFAULT NULL,
  `occurrenceRemarks` text COMMENT 'General Notes',
  `informationWithheld` varchar(250) DEFAULT NULL,
  `dataGeneralizations` varchar(250) DEFAULT NULL,
  `associatedOccurrences` text,
  `associatedTaxa` text COMMENT 'Associated Species',
  `dynamicProperties` text,
  `verbatimAttributes` text,
  `behavior` varchar(500) DEFAULT NULL,
  `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
  `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
  `establishmentMeans` varchar(45) DEFAULT NULL COMMENT 'cultivated, invasive, escaped from captivity, wild, native',
  `lifeStage` varchar(45) DEFAULT NULL,
  `sex` varchar(45) DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `samplingProtocol` varchar(100) DEFAULT NULL,
  `samplingEffort` varchar(200) DEFAULT NULL,
  `preparations` varchar(100) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `stateProvince` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `locality` text,
  `localitySecurity` int(10) DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
  `localitySecurityReason` varchar(100) DEFAULT NULL,
  `decimalLatitude` double DEFAULT NULL,
  `decimalLongitude` double DEFAULT NULL,
  `geodeticDatum` varchar(255) DEFAULT NULL,
  `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text,
  `coordinatePrecision` decimal(9,7) DEFAULT NULL,
  `locationRemarks` text,
  `verbatimCoordinates` varchar(255) DEFAULT NULL,
  `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
  `georeferencedBy` varchar(255) DEFAULT NULL,
  `georeferenceProtocol` varchar(255) DEFAULT NULL,
  `georeferenceSources` varchar(255) DEFAULT NULL,
  `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
  `georeferenceRemarks` varchar(255) DEFAULT NULL,
  `minimumElevationInMeters` int(6) DEFAULT NULL,
  `maximumElevationInMeters` int(6) DEFAULT NULL,
  `verbatimElevation` varchar(255) DEFAULT NULL,
  `minimumDepthInMeters` int(11) DEFAULT NULL,
  `maximumDepthInMeters` int(11) DEFAULT NULL,
  `verbatimDepth` varchar(50) DEFAULT NULL,
  `previousIdentifications` text,
  `disposition` varchar(100) DEFAULT NULL,
  `storageLocation` varchar(100) DEFAULT NULL,
  `genericcolumn1` varchar(100) DEFAULT NULL,
  `genericcolumn2` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `observeruid` int(10) unsigned DEFAULT NULL,
  `processingstatus` varchar(45) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `duplicateQuantity` int(10) unsigned DEFAULT NULL,
  `labelProject` varchar(50) DEFAULT NULL,
  `dateEntered` datetime DEFAULT NULL,
  `dateLastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`) USING BTREE,
  UNIQUE KEY `Index_collid` (`collid`,`dbpk`),
  KEY `Index_sciname` (`sciname`),
  KEY `Index_family` (`family`),
  KEY `Index_country` (`country`),
  KEY `Index_state` (`stateProvince`),
  KEY `Index_county` (`county`),
  KEY `Index_collector` (`recordedBy`),
  KEY `Index_gui` (`occurrenceID`),
  KEY `Index_ownerInst` (`ownerInstitutionCode`),
  KEY `FK_omoccurrences_tid` (`tidinterpreted`),
  KEY `FK_omoccurrences_uid` (`observeruid`),
  KEY `Index_municipality` (`municipality`),
  KEY `Index_collnum` (`recordNumber`),
  KEY `Index_catalognumber` (`catalogNumber`),
  KEY `FK_recordedbyid` (`recordedbyid`),
  KEY `Index_eventDate` (`eventDate`),
  KEY `Index_occurrences_procstatus` (`processingstatus`),
  KEY `occelevmax` (`maximumElevationInMeters`),
  KEY `occelevmin` (`minimumElevationInMeters`),
  KEY `Index_occurrences_cult` (`cultivationStatus`),
  KEY `Index_occurrences_typestatus` (`typeStatus`),
  KEY `idx_occrecordedby` (`recordedBy`),
  KEY `Index_occurDateLastModifed` (`dateLastModified`),
  KEY `Index_occurDateEntered` (`dateEntered`),
  KEY `Index_occurRecordEnteredBy` (`recordEnteredBy`),
  CONSTRAINT `FK_omoccurrences_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_recbyid` FOREIGN KEY (`recordedbyid`) REFERENCES `agents` (`agentid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_tid` FOREIGN KEY (`tidinterpreted`) REFERENCES `taxa` (`TID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_uid` FOREIGN KEY (`observeruid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `omoccurrencesfulltext_insert` AFTER INSERT ON `omoccurrences`
FOR EACH ROW BEGIN
  INSERT INTO omoccurrencesfulltext (
    `occid`,
    `recordedby`,
    `locality`
  ) VALUES (
    NEW.`occid`,
    NEW.`recordedby`,
    NEW.`locality`
  );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `omoccurrencesfulltext_update` AFTER UPDATE ON `omoccurrences`
FOR EACH ROW BEGIN
  UPDATE omoccurrencesfulltext SET
    `recordedby` = NEW.`recordedby`,
    `locality` = NEW.`locality`
  WHERE `occid` = NEW.`occid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `omoccurrencesfulltext_delete` BEFORE DELETE ON `omoccurrences`
FOR EACH ROW BEGIN
  DELETE FROM omoccurrencesfulltext WHERE `occid` = OLD.`occid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `omoccurrencesfulltext`
--

DROP TABLE IF EXISTS `omoccurrencesfulltext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurrencesfulltext` (
  `occid` int(11) NOT NULL,
  `locality` text,
  `recordedby` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`occid`),
  FULLTEXT KEY `ft_occur_locality` (`locality`),
  FULLTEXT KEY `ft_occur_recordedby` (`recordedby`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omoccurverification`
--

DROP TABLE IF EXISTS `omoccurverification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurverification` (
  `ovsid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `category` varchar(45) NOT NULL,
  `ranking` int(11) NOT NULL,
  `protocol` varchar(100) DEFAULT NULL,
  `source` varchar(45) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ovsid`),
  UNIQUE KEY `UNIQUE_omoccurverification` (`occid`,`category`),
  KEY `FK_omoccurverification_occid_idx` (`occid`),
  KEY `FK_omoccurverification_uid_idx` (`uid`),
  CONSTRAINT `FK_omoccurverification_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurverification_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omsurveyoccurlink`
--

DROP TABLE IF EXISTS `omsurveyoccurlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omsurveyoccurlink` (
  `occid` int(10) unsigned NOT NULL,
  `surveyid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`surveyid`),
  KEY `FK_omsurveyoccurlink_sur` (`surveyid`),
  CONSTRAINT `FK_omsurveyoccurlink_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`),
  CONSTRAINT `FK_omsurveyoccurlink_sur` FOREIGN KEY (`surveyid`) REFERENCES `omsurveys` (`surveyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omsurveyprojlink`
--

DROP TABLE IF EXISTS `omsurveyprojlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omsurveyprojlink` (
  `surveyid` int(10) unsigned NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`surveyid`,`pid`) USING BTREE,
  KEY `FK_specprojcatlink_cat` (`pid`) USING BTREE,
  CONSTRAINT `FK_omsurveyprojlink_proj` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`),
  CONSTRAINT `FK_omsurveyprojlink_sur` FOREIGN KEY (`surveyid`) REFERENCES `omsurveys` (`surveyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `omsurveys`
--

DROP TABLE IF EXISTS `omsurveys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omsurveys` (
  `surveyid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projectname` varchar(75) NOT NULL,
  `locality` varchar(1000) DEFAULT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `latcentroid` double(9,6) DEFAULT NULL,
  `longcentroid` double(9,6) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `ispublic` int(10) unsigned NOT NULL DEFAULT '0',
  `sortsequence` int(10) unsigned NOT NULL DEFAULT '50',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`surveyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referenceagentlinks`
--

DROP TABLE IF EXISTS `referenceagentlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceagentlinks` (
  `refid` int(11) NOT NULL,
  `agentid` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdbyid` int(11) NOT NULL,
  PRIMARY KEY (`refid`,`agentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referenceauthorlink`
--

DROP TABLE IF EXISTS `referenceauthorlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceauthorlink` (
  `refid` int(11) NOT NULL,
  `refauthid` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`refauthid`),
  KEY `FK_refauthlink_refid_idx` (`refid`),
  KEY `FK_refauthlink_refauthid_idx` (`refauthid`),
  CONSTRAINT `FK_refauthlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refauthlink_refauthid` FOREIGN KEY (`refauthid`) REFERENCES `referenceauthors` (`refauthorid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referenceauthors`
--

DROP TABLE IF EXISTS `referenceauthors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceauthors` (
  `refauthorid` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refauthorid`),
  KEY `INDEX_refauthlastname` (`lastname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referencechecklistlink`
--

DROP TABLE IF EXISTS `referencechecklistlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencechecklistlink` (
  `refid` int(11) NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`clid`),
  KEY `FK_refcheckllistlink_refid_idx` (`refid`),
  KEY `FK_refcheckllistlink_clid_idx` (`clid`),
  CONSTRAINT `FK_refchecklistlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchecklistlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referencechklsttaxalink`
--

DROP TABLE IF EXISTS `referencechklsttaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencechklsttaxalink` (
  `refid` int(11) NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`clid`,`tid`),
  KEY `FK_refchktaxalink_clidtid_idx` (`clid`,`tid`),
  CONSTRAINT `FK_refchktaxalink_ref` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchktaxalink_clidtid` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referencecollectionlink`
--

DROP TABLE IF EXISTS `referencecollectionlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencecollectionlink` (
  `refid` int(11) NOT NULL,
  `collid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`collid`),
  KEY `FK_refcollectionlink_collid_idx` (`collid`),
  CONSTRAINT `FK_refcollectionlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_refcollectionlink_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referenceobject`
--

DROP TABLE IF EXISTS `referenceobject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceobject` (
  `refid` int(11) NOT NULL AUTO_INCREMENT,
  `parentRefId` int(11) DEFAULT NULL,
  `ReferenceTypeId` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `secondarytitle` varchar(250) DEFAULT NULL,
  `shorttitle` varchar(250) DEFAULT NULL,
  `tertiarytitle` varchar(250) DEFAULT NULL,
  `alternativetitle` varchar(250) DEFAULT NULL,
  `typework` varchar(150) DEFAULT NULL,
  `figures` varchar(150) DEFAULT NULL,
  `pubdate` varchar(45) DEFAULT NULL,
  `edition` varchar(45) DEFAULT NULL,
  `volume` varchar(45) DEFAULT NULL,
  `numbervolumnes` varchar(45) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `pages` varchar(45) DEFAULT NULL,
  `section` varchar(45) DEFAULT NULL,
  `placeofpublication` varchar(45) DEFAULT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `isbn_issn` varchar(45) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `ispublished` varchar(45) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `cheatauthors` varchar(250) DEFAULT NULL,
  `cheatcitation` varchar(250) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`),
  KEY `INDEX_refobj_title` (`title`),
  KEY `FK_refobj_parentrefid_idx` (`parentRefId`),
  KEY `FK_refobj_typeid_idx` (`ReferenceTypeId`),
  CONSTRAINT `FK_refobj_parentrefid` FOREIGN KEY (`parentRefId`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refobj_reftypeid` FOREIGN KEY (`ReferenceTypeId`) REFERENCES `referencetype` (`ReferenceTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referenceoccurlink`
--

DROP TABLE IF EXISTS `referenceoccurlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceoccurlink` (
  `refid` int(11) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`occid`),
  KEY `FK_refoccurlink_refid_idx` (`refid`),
  KEY `FK_refoccurlink_occid_idx` (`occid`),
  CONSTRAINT `FK_refoccurlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refoccurlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referencetaxalink`
--

DROP TABLE IF EXISTS `referencetaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencetaxalink` (
  `refid` int(11) NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`tid`),
  KEY `FK_reftaxalink_refid_idx` (`refid`),
  KEY `FK_reftaxalink_tid_idx` (`tid`),
  CONSTRAINT `FK_reftaxalink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_reftaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referencetype`
--

DROP TABLE IF EXISTS `referencetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencetype` (
  `ReferenceTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `ReferenceType` varchar(45) NOT NULL,
  `IsParent` int(11) DEFAULT NULL,
  `Title` varchar(45) DEFAULT NULL,
  `SecondaryTitle` varchar(45) DEFAULT NULL,
  `PlacePublished` varchar(45) DEFAULT NULL,
  `Publisher` varchar(45) DEFAULT NULL,
  `Volume` varchar(45) DEFAULT NULL,
  `NumberVolumes` varchar(45) DEFAULT NULL,
  `Number` varchar(45) DEFAULT NULL,
  `Pages` varchar(45) DEFAULT NULL,
  `Section` varchar(45) DEFAULT NULL,
  `TertiaryTitle` varchar(45) DEFAULT NULL,
  `Edition` varchar(45) DEFAULT NULL,
  `Date` varchar(45) DEFAULT NULL,
  `TypeWork` varchar(45) DEFAULT NULL,
  `ShortTitle` varchar(45) DEFAULT NULL,
  `AlternativeTitle` varchar(45) DEFAULT NULL,
  `ISBN_ISSN` varchar(45) DEFAULT NULL,
  `Figures` varchar(45) DEFAULT NULL,
  `addedByUid` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReferenceTypeId`),
  UNIQUE KEY `ReferenceType_UNIQUE` (`ReferenceType`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `salixwordstats`
--

DROP TABLE IF EXISTS `salixwordstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salixwordstats` (
  `swsid` int(11) NOT NULL AUTO_INCREMENT,
  `firstword` varchar(45) NOT NULL,
  `secondword` varchar(45) DEFAULT NULL,
  `locality` int(4) NOT NULL DEFAULT '0',
  `localityFreq` int(4) NOT NULL DEFAULT '0',
  `habitat` int(4) NOT NULL DEFAULT '0',
  `habitatFreq` int(4) NOT NULL DEFAULT '0',
  `substrate` int(4) NOT NULL DEFAULT '0',
  `substrateFreq` int(4) NOT NULL DEFAULT '0',
  `verbatimAttributes` int(4) NOT NULL DEFAULT '0',
  `verbatimAttributesFreq` int(4) NOT NULL DEFAULT '0',
  `occurrenceRemarks` int(4) NOT NULL DEFAULT '0',
  `occurrenceRemarksFreq` int(4) NOT NULL DEFAULT '0',
  `totalcount` int(4) NOT NULL DEFAULT '0',
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`swsid`),
  UNIQUE KEY `INDEX_unique` (`firstword`,`secondword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schemaversion`
--

DROP TABLE IF EXISTS `schemaversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schemaversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `versionnumber` varchar(20) NOT NULL,
  `dateapplied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specprocessorprojects`
--

DROP TABLE IF EXISTS `specprocessorprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocessorprojects` (
  `spprid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `specKeyPattern` varchar(45) DEFAULT NULL,
  `speckeyretrieval` varchar(45) DEFAULT NULL,
  `coordX1` int(10) unsigned DEFAULT NULL,
  `coordX2` int(10) unsigned DEFAULT NULL,
  `coordY1` int(10) unsigned DEFAULT NULL,
  `coordY2` int(10) unsigned DEFAULT NULL,
  `sourcePath` varchar(250) DEFAULT NULL,
  `targetPath` varchar(250) DEFAULT NULL,
  `imgUrl` varchar(250) DEFAULT NULL,
  `webPixWidth` int(10) unsigned DEFAULT '1200',
  `tnPixWidth` int(10) unsigned DEFAULT '130',
  `lgPixWidth` int(10) unsigned DEFAULT '2400',
  `jpgcompression` int(11) DEFAULT '70',
  `createTnImg` int(10) unsigned DEFAULT '1',
  `createLgImg` int(10) unsigned DEFAULT '1',
  `source` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spprid`),
  KEY `FK_specprocessorprojects_coll` (`collid`),
  CONSTRAINT `FK_specprocessorprojects_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specprocessorrawlabels`
--

DROP TABLE IF EXISTS `specprocessorrawlabels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocessorrawlabels` (
  `prlid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `rawstr` text NOT NULL,
  `processingvariables` varchar(250) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `source` varchar(150) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prlid`),
  KEY `FK_specproc_images` (`imgid`),
  KEY `FK_specproc_occid` (`occid`),
  CONSTRAINT `FK_specproc_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_specproc_images` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `specprocessorrawlabelsfulltext_insert` AFTER INSERT ON `specprocessorrawlabels`
FOR EACH ROW BEGIN
  INSERT INTO specprocessorrawlabelsfulltext (
    `prlid`,
    `imgid`,
    `rawstr`
  ) VALUES (
    NEW.`prlid`,
    NEW.`imgid`,
    NEW.`rawstr`
  );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `specprocessorrawlabelsfulltext_update` AFTER UPDATE ON `specprocessorrawlabels`
FOR EACH ROW BEGIN
  UPDATE specprocessorrawlabelsfulltext SET
    `imgid` = NEW.`imgid`,
    `rawstr` = NEW.`rawstr`
  WHERE `prlid` = NEW.`prlid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `specprocessorrawlabelsfulltext`
--

DROP TABLE IF EXISTS `specprocessorrawlabelsfulltext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocessorrawlabelsfulltext` (
  `prlid` int(11) NOT NULL,
  `imgid` int(11) NOT NULL,
  `rawstr` text NOT NULL,
  PRIMARY KEY (`prlid`),
  KEY `Index_ocr_imgid` (`imgid`),
  FULLTEXT KEY `Index_ocr_fulltext` (`rawstr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `specprocessorrawlabelsfulltext_delete` BEFORE DELETE ON `specprocessorrawlabelsfulltext`
FOR EACH ROW BEGIN
  DELETE FROM specprocessorrawlabelsfulltext WHERE `prlid` = OLD.`prlid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `specprocnlp`
--

DROP TABLE IF EXISTS `specprocnlp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocnlp` (
  `spnlpid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `sqlfrag` varchar(250) NOT NULL,
  `patternmatch` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `collid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spnlpid`),
  KEY `FK_specprocnlp_collid` (`collid`),
  CONSTRAINT `FK_specprocnlp_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specprocnlpfrag`
--

DROP TABLE IF EXISTS `specprocnlpfrag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocnlpfrag` (
  `spnlpfragid` int(10) NOT NULL AUTO_INCREMENT,
  `spnlpid` int(10) NOT NULL,
  `fieldname` varchar(45) NOT NULL,
  `patternmatch` varchar(250) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortseq` int(5) DEFAULT '50',
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spnlpfragid`),
  KEY `FK_specprocnlpfrag_spnlpid` (`spnlpid`),
  CONSTRAINT `FK_specprocnlpfrag_spnlpid` FOREIGN KEY (`spnlpid`) REFERENCES `specprocnlp` (`spnlpid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specprocnlpversion`
--

DROP TABLE IF EXISTS `specprocnlpversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocnlpversion` (
  `nlpverid` int(11) NOT NULL AUTO_INCREMENT,
  `prlid` int(10) unsigned NOT NULL,
  `archivestr` text NOT NULL,
  `processingvariables` varchar(250) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `source` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nlpverid`),
  KEY `FK_specprocnlpver_rawtext_idx` (`prlid`),
  CONSTRAINT `FK_specprocnlpver_rawtext` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Archives field name - value pairs of NLP results loading into an omoccurrence record. This way, results can be easily redone at a later date without copying over date modifed afterward by another user or process ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specprococrfrag`
--

DROP TABLE IF EXISTS `specprococrfrag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprococrfrag` (
  `ocrfragid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prlid` int(10) unsigned NOT NULL,
  `firstword` varchar(45) NOT NULL,
  `secondword` varchar(45) DEFAULT NULL,
  `keyterm` varchar(45) DEFAULT NULL,
  `wordorder` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocrfragid`),
  KEY `FK_specprococrfrag_prlid_idx` (`prlid`),
  KEY `Index_keyterm` (`keyterm`),
  CONSTRAINT `FK_specprococrfrag_prlid` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxa`
--

DROP TABLE IF EXISTS `taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxa` (
  `TID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kingdomName` varchar(45) DEFAULT NULL,
  `KingdomID` tinyint(3) unsigned DEFAULT NULL,
  `RankId` smallint(5) unsigned DEFAULT NULL,
  `SciName` varchar(250) NOT NULL,
  `UnitInd1` varchar(1) DEFAULT NULL,
  `UnitName1` varchar(50) NOT NULL,
  `UnitInd2` varchar(1) DEFAULT NULL,
  `UnitName2` varchar(50) DEFAULT NULL,
  `UnitInd3` varchar(7) DEFAULT NULL,
  `UnitName3` varchar(35) DEFAULT NULL,
  `Author` varchar(100) DEFAULT NULL,
  `PhyloSortSequence` tinyint(3) unsigned DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `Source` varchar(250) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `Hybrid` varchar(50) DEFAULT NULL,
  `SecurityStatus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TID`),
  UNIQUE KEY `sciname_unique` (`SciName`),
  KEY `rankid_index` (`RankId`),
  KEY `unitname1_index` (`UnitName1`,`UnitName2`) USING BTREE,
  KEY `FK_taxa_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_taxa_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxadescrblock`
--

DROP TABLE IF EXISTS `taxadescrblock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxadescrblock` (
  `tdbid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `caption` varchar(20) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(250) DEFAULT NULL,
  `language` varchar(45) DEFAULT 'English',
  `displaylevel` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = short descr, 2 = intermediate descr',
  `uid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdbid`),
  UNIQUE KEY `Index_unique` (`tid`,`displaylevel`,`language`),
  CONSTRAINT `FK_taxadescrblock_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxadescrstmts`
--

DROP TABLE IF EXISTS `taxadescrstmts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxadescrstmts` (
  `tdsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tdbid` int(10) unsigned NOT NULL,
  `heading` varchar(75) NOT NULL,
  `statement` text NOT NULL,
  `displayheader` int(10) unsigned NOT NULL DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT '89',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdsid`),
  KEY `FK_taxadescrstmts_tblock` (`tdbid`),
  CONSTRAINT `FK_taxadescrstmts_tblock` FOREIGN KEY (`tdbid`) REFERENCES `taxadescrblock` (`tdbid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxaenumtree`
--

DROP TABLE IF EXISTS `taxaenumtree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaenumtree` (
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL,
  `parenttid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`taxauthid`,`parenttid`),
  KEY `FK_tet_taxa` (`tid`),
  KEY `FK_tet_taxauth` (`taxauthid`),
  KEY `FK_tet_taxa2` (`parenttid`),
  CONSTRAINT `FK_tet_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxauth` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxa2` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxalinks`
--

DROP TABLE IF EXISTS `taxalinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxalinks` (
  `tlid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` varchar(100) NOT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT '50',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tlid`),
  KEY `Index_unique` (`tid`,`url`(255)),
  CONSTRAINT `FK_taxalinks_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxamaps`
--

DROP TABLE IF EXISTS `taxamaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxamaps` (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`),
  KEY `FK_tid_idx` (`tid`),
  CONSTRAINT `FK_taxamaps_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxanestedtree`
--

DROP TABLE IF EXISTS `taxanestedtree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxanestedtree` (
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL,
  `leftindex` int(10) unsigned NOT NULL,
  `rightindex` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`taxauthid`),
  KEY `leftindex` (`leftindex`),
  KEY `rightindex` (`rightindex`),
  KEY `FK_tnt_taxa` (`tid`),
  KEY `FK_tnt_taxauth` (`taxauthid`),
  CONSTRAINT `FK_tnt_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tnt_taxauth` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxaprofilepubdesclink`
--

DROP TABLE IF EXISTS `taxaprofilepubdesclink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubdesclink` (
  `tdbid` int(10) unsigned NOT NULL,
  `tppid` int(11) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `editornotes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdbid`,`tppid`),
  KEY `FK_tppubdesclink_id_idx` (`tppid`),
  CONSTRAINT `FK_tppubdesclink_tdbid` FOREIGN KEY (`tdbid`) REFERENCES `taxadescrblock` (`tdbid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tppubdesclink_id` FOREIGN KEY (`tppid`) REFERENCES `taxaprofilepubs` (`tppid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxaprofilepubimagelink`
--

DROP TABLE IF EXISTS `taxaprofilepubimagelink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubimagelink` (
  `imgid` int(10) unsigned NOT NULL,
  `tppid` int(11) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `editornotes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`,`tppid`),
  KEY `FK_tppubimagelink_id_idx` (`tppid`),
  CONSTRAINT `FK_tppubimagelink_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tppubimagelink_id` FOREIGN KEY (`tppid`) REFERENCES `taxaprofilepubs` (`tppid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxaprofilepubmaplink`
--

DROP TABLE IF EXISTS `taxaprofilepubmaplink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubmaplink` (
  `mid` int(10) unsigned NOT NULL,
  `tppid` int(11) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `editornotes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`,`tppid`),
  KEY `FK_tppubmaplink_id_idx` (`tppid`),
  CONSTRAINT `FK_tppubmaplink_tdbid` FOREIGN KEY (`mid`) REFERENCES `taxamaps` (`mid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tppubmaplink_id` FOREIGN KEY (`tppid`) REFERENCES `taxaprofilepubs` (`tppid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxaprofilepubs`
--

DROP TABLE IF EXISTS `taxaprofilepubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubs` (
  `tppid` int(11) NOT NULL AUTO_INCREMENT,
  `pubtitle` varchar(150) NOT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `abstract` text,
  `uidowner` int(10) unsigned DEFAULT NULL,
  `externalurl` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `usageterm` varchar(250) DEFAULT NULL,
  `accessrights` varchar(250) DEFAULT NULL,
  `ispublic` int(11) DEFAULT NULL,
  `inclusive` int(11) DEFAULT NULL,
  `dynamicProperties` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tppid`),
  KEY `FK_taxaprofilepubs_uid_idx` (`uidowner`),
  KEY `INDEX_taxaprofilepubs_title` (`pubtitle`),
  CONSTRAINT `FK_taxaprofilepubs_uid` FOREIGN KEY (`uidowner`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxaresourcelinks`
--

DROP TABLE IF EXISTS `taxaresourcelinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaresourcelinks` (
  `taxaresourceid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `sourcename` varchar(150) NOT NULL,
  `sourceidentifier` varchar(45) DEFAULT NULL,
  `sourceguid` varchar(150) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `ranking` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxaresourceid`),
  KEY `taxaresource_name` (`sourcename`),
  KEY `FK_taxaresource_tid_idx` (`tid`),
  CONSTRAINT `FK_taxaresource_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxauthority`
--

DROP TABLE IF EXISTS `taxauthority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxauthority` (
  `taxauthid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isprimary` int(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `editors` varchar(150) DEFAULT NULL,
  `contact` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `isactive` int(1) unsigned NOT NULL DEFAULT '1',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxauthid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxavernaculars`
--

DROP TABLE IF EXISTS `taxavernaculars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxavernaculars` (
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `VernacularName` varchar(80) NOT NULL,
  `Language` varchar(15) NOT NULL DEFAULT 'English',
  `Source` varchar(50) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `isupperterm` int(2) DEFAULT '0',
  `SortSequence` int(10) DEFAULT '50',
  `VID` int(10) NOT NULL AUTO_INCREMENT,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`VID`),
  UNIQUE KEY `unique-key` (`Language`,`VernacularName`,`TID`),
  KEY `tid1` (`TID`),
  KEY `vernacularsnames` (`VernacularName`),
  CONSTRAINT `FK_vernaculars_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxonunits`
--

DROP TABLE IF EXISTS `taxonunits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxonunits` (
  `taxonunitid` int(11) NOT NULL AUTO_INCREMENT,
  `kingdomid` tinyint(3) unsigned DEFAULT NULL,
  `kingdomName` varchar(45) NOT NULL DEFAULT 'Organism',
  `rankid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rankname` varchar(15) NOT NULL,
  `suffix` varchar(45) DEFAULT NULL,
  `dirparentrankid` smallint(6) NOT NULL,
  `reqparentrankid` smallint(6) DEFAULT NULL,
  `modifiedby` varchar(45) DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxonunitid`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `taxstatus`
--

DROP TABLE IF EXISTS `taxstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxstatus` (
  `tid` int(10) unsigned NOT NULL,
  `tidaccepted` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL COMMENT 'taxon authority id',
  `parenttid` int(10) unsigned DEFAULT NULL,
  `hierarchystr` varchar(200) DEFAULT NULL,
  `family` varchar(50) DEFAULT NULL,
  `UnacceptabilityReason` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `SortSequence` int(10) unsigned DEFAULT '50',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`tidaccepted`,`taxauthid`) USING BTREE,
  KEY `FK_taxstatus_tidacc` (`tidaccepted`),
  KEY `FK_taxstatus_taid` (`taxauthid`),
  KEY `Index_ts_family` (`family`),
  KEY `Index_parenttid` (`parenttid`),
  KEY `Index_hierarchy` (`hierarchystr`) USING BTREE,
  CONSTRAINT `FK_taxstatus_parent` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`TID`),
  CONSTRAINT `FK_taxstatus_taid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_taxstatus_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`),
  CONSTRAINT `FK_taxstatus_tidacc` FOREIGN KEY (`tidaccepted`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unknowncomments`
--

DROP TABLE IF EXISTS `unknowncomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknowncomments` (
  `unkcomid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unkid` int(10) unsigned NOT NULL,
  `comment` varchar(500) NOT NULL,
  `username` varchar(45) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unkcomid`) USING BTREE,
  KEY `FK_unknowncomments` (`unkid`),
  CONSTRAINT `FK_unknowncomments` FOREIGN KEY (`unkid`) REFERENCES `unknowns` (`unkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unknownimages`
--

DROP TABLE IF EXISTS `unknownimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknownimages` (
  `unkimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unkid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unkimgid`),
  KEY `FK_unknowns` (`unkid`),
  CONSTRAINT `FK_unknowns` FOREIGN KEY (`unkid`) REFERENCES `unknowns` (`unkid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unknowns`
--

DROP TABLE IF EXISTS `unknowns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknowns` (
  `unkid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `latdecimal` double DEFAULT NULL,
  `longdecimal` double DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `username` varchar(45) NOT NULL,
  `idstatus` varchar(45) NOT NULL DEFAULT 'ID pending',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unkid`) USING BTREE,
  KEY `FK_unknowns_username` (`username`),
  KEY `FK_unknowns_tid` (`tid`),
  CONSTRAINT `FK_unknowns_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`),
  CONSTRAINT `FK_unknowns_username` FOREIGN KEY (`username`) REFERENCES `userlogin` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploaddetermtemp`
--

DROP TABLE IF EXISTS `uploaddetermtemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaddetermtemp` (
  `occid` int(10) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `identifiedBy` varchar(60) NOT NULL,
  `dateIdentified` varchar(45) NOT NULL,
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `sciname` varchar(100) NOT NULL,
  `scientificNameAuthorship` varchar(100) DEFAULT NULL,
  `identificationQualifier` varchar(45) DEFAULT NULL,
  `iscurrent` int(2) DEFAULT '0',
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(255) DEFAULT NULL,
  `identificationRemarks` varchar(255) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT '10',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Index_uploaddet_occid` (`occid`),
  KEY `Index_uploaddet_collid` (`collid`),
  KEY `Index_uploaddet_dbpk` (`dbpk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploadimagetemp`
--

DROP TABLE IF EXISTS `uploadimagetemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadimagetemp` (
  `tid` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnailurl` varchar(255) DEFAULT NULL,
  `originalurl` varchar(255) DEFAULT NULL,
  `archiveurl` varchar(255) DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `photographeruid` int(10) unsigned DEFAULT NULL,
  `imagetype` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `specimengui` varchar(45) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Index_uploadimg_occid` (`occid`),
  KEY `Index_uploadimg_collid` (`collid`),
  KEY `Index_uploadimg_dbpk` (`dbpk`),
  KEY `Index_uploadimg_ts` (`initialtimestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploadspecmap`
--

DROP TABLE IF EXISTS `uploadspecmap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadspecmap` (
  `usmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uspid` int(10) unsigned NOT NULL,
  `sourcefield` varchar(45) NOT NULL,
  `symbdatatype` varchar(45) NOT NULL DEFAULT 'string' COMMENT 'string, numeric, datetime',
  `symbspecfield` varchar(45) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usmid`),
  UNIQUE KEY `Index_unique` (`uspid`,`symbspecfield`,`sourcefield`),
  CONSTRAINT `FK_uploadspecmap_usp` FOREIGN KEY (`uspid`) REFERENCES `uploadspecparameters` (`uspid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploadspecparameters`
--

DROP TABLE IF EXISTS `uploadspecparameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadspecparameters` (
  `uspid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CollID` int(10) unsigned NOT NULL,
  `UploadType` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = Direct; 2 = DiGIR; 3 = File',
  `title` varchar(45) NOT NULL,
  `Platform` varchar(45) DEFAULT '1' COMMENT '1 = MySQL; 2 = MSSQL; 3 = ORACLE; 11 = MS Access; 12 = FileMaker',
  `server` varchar(150) DEFAULT NULL,
  `port` int(10) unsigned DEFAULT NULL,
  `driver` varchar(45) DEFAULT NULL,
  `Code` varchar(45) DEFAULT NULL,
  `Path` varchar(150) DEFAULT NULL,
  `PkField` varchar(45) DEFAULT NULL,
  `Username` varchar(45) DEFAULT NULL,
  `Password` varchar(45) DEFAULT NULL,
  `SchemaName` varchar(150) DEFAULT NULL,
  `QueryStr` varchar(2000) DEFAULT NULL,
  `cleanupsp` varchar(45) DEFAULT NULL,
  `dlmisvalid` int(10) unsigned DEFAULT '0',
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uspid`),
  KEY `FK_uploadspecparameters_coll` (`CollID`),
  CONSTRAINT `FK_uploadspecparameters_coll` FOREIGN KEY (`CollID`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploadspectemp`
--

DROP TABLE IF EXISTS `uploadspectemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadspectemp` (
  `collid` int(10) unsigned NOT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `basisOfRecord` varchar(32) DEFAULT 'PreservedSpecimen' COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
  `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
  `catalogNumber` varchar(32) DEFAULT NULL,
  `otherCatalogNumbers` varchar(255) DEFAULT NULL,
  `ownerInstitutionCode` varchar(32) DEFAULT NULL,
  `institutionID` varchar(255) DEFAULT NULL,
  `collectionID` varchar(255) DEFAULT NULL,
  `datasetID` varchar(255) DEFAULT NULL,
  `institutionCode` varchar(64) DEFAULT NULL,
  `collectionCode` varchar(64) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `scientificName` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) DEFAULT NULL,
  `tidinterpreted` int(10) unsigned DEFAULT NULL,
  `genus` varchar(255) DEFAULT NULL,
  `specificEpithet` varchar(255) DEFAULT NULL,
  `taxonRank` varchar(32) DEFAULT NULL,
  `infraspecificEpithet` varchar(255) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `taxonRemarks` text,
  `identifiedBy` varchar(255) DEFAULT NULL,
  `dateIdentified` varchar(45) DEFAULT NULL,
  `identificationReferences` text,
  `identificationRemarks` text,
  `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
  `typeStatus` varchar(255) DEFAULT NULL,
  `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
  `recordNumberPrefix` varchar(45) DEFAULT NULL,
  `recordNumberSuffix` varchar(45) DEFAULT NULL,
  `recordNumber` varchar(32) DEFAULT NULL COMMENT 'Collector Number',
  `CollectorFamilyName` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `CollectorInitials` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `eventDate` date DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `day` int(10) DEFAULT NULL,
  `startDayOfYear` int(10) DEFAULT NULL,
  `endDayOfYear` int(10) DEFAULT NULL,
  `LatestDateCollected` date DEFAULT NULL,
  `verbatimEventDate` varchar(255) DEFAULT NULL,
  `habitat` text COMMENT 'Habitat, substrait, etc',
  `substrate` varchar(500) DEFAULT NULL,
  `fieldNotes` text,
  `fieldnumber` varchar(45) DEFAULT NULL,
  `occurrenceRemarks` text COMMENT 'General Notes',
  `informationWithheld` varchar(250) DEFAULT NULL,
  `dataGeneralizations` varchar(250) DEFAULT NULL,
  `associatedOccurrences` text,
  `associatedMedia` text,
  `associatedReferences` text,
  `associatedSequences` text,
  `associatedTaxa` text COMMENT 'Associated Species',
  `dynamicProperties` text COMMENT 'Plant Description?',
  `verbatimAttributes` text,
  `behavior` varchar(500) DEFAULT NULL,
  `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
  `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
  `establishmentMeans` varchar(32) DEFAULT NULL COMMENT 'cultivated, invasive, escaped from captivity, wild, native',
  `lifeStage` varchar(45) DEFAULT NULL,
  `sex` varchar(45) DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `samplingProtocol` varchar(100) DEFAULT NULL,
  `samplingEffort` varchar(200) DEFAULT NULL,
  `preparations` varchar(100) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `stateProvince` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `locality` text,
  `localitySecurity` int(10) DEFAULT '0' COMMENT '0 = display locality, 1 = hide locality',
  `localitySecurityReason` varchar(100) DEFAULT NULL,
  `decimalLatitude` double DEFAULT NULL,
  `decimalLongitude` double DEFAULT NULL,
  `geodeticDatum` varchar(255) DEFAULT NULL,
  `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text,
  `coordinatePrecision` decimal(9,7) DEFAULT NULL,
  `locationRemarks` text,
  `verbatimCoordinates` varchar(255) DEFAULT NULL,
  `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
  `latDeg` int(11) DEFAULT NULL,
  `latMin` double DEFAULT NULL,
  `latSec` double DEFAULT NULL,
  `latNS` varchar(3) DEFAULT NULL,
  `lngDeg` int(11) DEFAULT NULL,
  `lngMin` double DEFAULT NULL,
  `lngSec` double DEFAULT NULL,
  `lngEW` varchar(3) DEFAULT NULL,
  `verbatimLatitude` varchar(45) DEFAULT NULL,
  `verbatimLongitude` varchar(45) DEFAULT NULL,
  `UtmNorthing` varchar(45) DEFAULT NULL,
  `UtmEasting` varchar(45) DEFAULT NULL,
  `UtmZoning` varchar(45) DEFAULT NULL,
  `trsTownship` varchar(45) DEFAULT NULL,
  `trsRange` varchar(45) DEFAULT NULL,
  `trsSection` varchar(45) DEFAULT NULL,
  `trsSectionDetails` varchar(45) DEFAULT NULL,
  `georeferencedBy` varchar(255) DEFAULT NULL,
  `georeferenceProtocol` varchar(255) DEFAULT NULL,
  `georeferenceSources` varchar(255) DEFAULT NULL,
  `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
  `georeferenceRemarks` varchar(255) DEFAULT NULL,
  `minimumElevationInMeters` int(6) DEFAULT NULL,
  `maximumElevationInMeters` int(6) DEFAULT NULL,
  `elevationNumber` varchar(45) DEFAULT NULL,
  `elevationUnits` varchar(45) DEFAULT NULL,
  `verbatimElevation` varchar(255) DEFAULT NULL,
  `minimumDepthInMeters` int(11) DEFAULT NULL,
  `maximumDepthInMeters` int(11) DEFAULT NULL,
  `verbatimDepth` varchar(50) DEFAULT NULL,
  `previousIdentifications` text,
  `disposition` varchar(32) DEFAULT NULL COMMENT 'Dups to',
  `storageLocation` varchar(100) DEFAULT NULL,
  `genericcolumn1` varchar(100) DEFAULT NULL,
  `genericcolumn2` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `duplicateQuantity` int(10) unsigned DEFAULT NULL,
  `labelProject` varchar(45) DEFAULT NULL,
  `processingStatus` varchar(45) DEFAULT NULL,
  `tempfield01` text,
  `tempfield02` text,
  `tempfield03` text,
  `tempfield04` text,
  `tempfield05` text,
  `tempfield06` text,
  `tempfield07` text,
  `tempfield08` text,
  `tempfield09` text,
  `tempfield10` text,
  `tempfield11` text,
  `tempfield12` text,
  `tempfield13` text,
  `tempfield14` text,
  `tempfield15` text,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `FK_uploadspectemp_coll` (`collid`),
  KEY `Index_uploadspectemp_occid` (`occid`),
  KEY `Index_uploadspectemp_dbpk` (`dbpk`),
  KEY `Index_uploadspec_sciname` (`sciname`),
  KEY `Index_uploadspec_catalognumber` (`catalogNumber`),
  CONSTRAINT `FK_uploadspectemp_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploadtaxa`
--

DROP TABLE IF EXISTS `uploadtaxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadtaxa` (
  `TID` int(10) unsigned DEFAULT NULL,
  `SourceId` int(10) unsigned DEFAULT NULL,
  `KingdomID` tinyint(3) unsigned DEFAULT '3',
  `Family` varchar(50) DEFAULT NULL,
  `RankId` smallint(5) DEFAULT NULL,
  `scinameinput` varchar(250) NOT NULL,
  `SciName` varchar(250) DEFAULT NULL,
  `UnitInd1` varchar(1) DEFAULT NULL,
  `UnitName1` varchar(50) DEFAULT NULL,
  `UnitInd2` varchar(1) DEFAULT NULL,
  `UnitName2` varchar(50) DEFAULT NULL,
  `UnitInd3` varchar(7) DEFAULT NULL,
  `UnitName3` varchar(35) DEFAULT NULL,
  `Author` varchar(100) DEFAULT NULL,
  `InfraAuthor` varchar(100) DEFAULT NULL,
  `Acceptance` int(10) unsigned DEFAULT '1' COMMENT '0 = not accepted; 1 = accepted',
  `TidAccepted` int(10) unsigned DEFAULT NULL,
  `AcceptedStr` varchar(250) DEFAULT NULL,
  `SourceAcceptedId` int(10) unsigned DEFAULT NULL,
  `UnacceptabilityReason` varchar(24) DEFAULT NULL,
  `ParentTid` int(10) DEFAULT NULL,
  `ParentStr` varchar(250) DEFAULT NULL,
  `SourceParentId` int(10) unsigned DEFAULT NULL,
  `SecurityStatus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
  `Source` varchar(250) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `vernacular` varchar(250) DEFAULT NULL,
  `vernlang` varchar(15) DEFAULT NULL,
  `Hybrid` varchar(50) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `sourceID_index` (`SourceId`),
  KEY `sourceAcceptedId_index` (`SourceAcceptedId`),
  KEY `sciname_index` (`SciName`),
  KEY `scinameinput_index` (`scinameinput`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userlogin`
--

DROP TABLE IF EXISTS `userlogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userlogin` (
  `uid` int(10) unsigned NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `alias` varchar(45) DEFAULT NULL,
  `lastlogindate` datetime DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`) USING BTREE,
  UNIQUE KEY `Index_userlogin_unique` (`alias`),
  KEY `FK_login_user` (`uid`),
  CONSTRAINT `FK_login_user` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userroles`
--

DROP TABLE IF EXISTS `userroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userroles` (
  `userroleid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL,
  `tablename` varchar(45) DEFAULT NULL,
  `tablepk` int(11) DEFAULT NULL,
  `secondaryVariable` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `uidassignedby` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userroleid`),
  KEY `FK_userroles_uid_idx` (`uid`),
  KEY `FK_usrroles_uid2_idx` (`uidassignedby`),
  KEY `Index_userroles_table` (`tablename`,`tablepk`),
  CONSTRAINT `FK_userrole_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_userrole_uid_assigned` FOREIGN KEY (`uidassignedby`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `institution` varchar(200) DEFAULT NULL,
  `department` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `RegionOfInterest` varchar(45) DEFAULT NULL,
  `url` varchar(400) DEFAULT NULL,
  `Biography` varchar(1500) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `ispublic` int(10) unsigned NOT NULL DEFAULT '0',
  `defaultrights` varchar(250) DEFAULT NULL,
  `rightsholder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `accessrights` varchar(250) DEFAULT NULL,
  `validated` varchar(45) NOT NULL DEFAULT '0',
  `usergroups` varchar(100) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `Index_email` (`email`,`lastname`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usertaxonomy`
--

DROP TABLE IF EXISTS `usertaxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertaxonomy` (
  `idusertaxonomy` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL DEFAULT '1',
  `editorstatus` varchar(45) DEFAULT NULL,
  `geographicScope` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idusertaxonomy`),
  UNIQUE KEY `usertaxonomy_UNIQUE` (`uid`,`tid`,`taxauthid`,`editorstatus`),
  KEY `FK_usertaxonomy_uid_idx` (`uid`),
  KEY `FK_usertaxonomy_tid_idx` (`tid`),
  KEY `FK_usertaxonomy_taxauthid_idx` (`taxauthid`),
  CONSTRAINT `FK_usertaxonomy_taxauthid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'symbtest'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-02-19 22:35:51


-- Prime some tables with default data values
INSERT INTO schemaversion (versionnumber) values ("1.0");

-- Create the general admin user
INSERT INTO users(uid,firstname,lastname,state,country,email) VALUES (1,"General","Administrator","NA","NA","NA");
INSERT INTO userlogin(uid,username,password) VALUES (1,"admin",password("admin"));
INSERT INTO userroles(uid,role) VALUES (1,"SuperAdmin");

-- Prime taxonunits table
INSERT IGNORE INTO `taxonunits`(kingdomName,rankid,rankName, dirparentrankid, reqparentrankid) 
  VALUES ("Organism",1,"Organism",1,1),("Organism",10,"Kingdom",1,1),("Organism",20,"Subkingdom",10,10),("Organism",30,"Division",20,10),("Organism",40,"Subdivision",30,30),("Organism",50,"Superclass",40,30),("Organism",60,"Class",50,30),("Organism",70,"Subclass",60,60),("Organism",100,"Order",70,60),("Organism",110,"Suborder",100,100),("Organism",140,"Family",110,100),("Organism",150,"Subfamily",140,140),("Organism",160,"Tribe",150,140),("Organism",170,"Subtribe",160,140),("Organism",180,"Genus",170,140),("Organism",190,"Subgenus",180,180),("Organism",200,"Section",190,180),("Organism",210,"Subsection",200,180),("Organism",220,"Species",210,180),("Organism",230,"Subspecies",220,180),("Organism",240,"Variety",220,180),("Organism",250,"Subvariety",240,180),("Organism",260,"Form",220,180),("Organism",270,"Subform",260,180),("Organism",300,"Cultivated",220,220);
INSERT IGNORE INTO `taxonunits`(kingdomName,rankid,rankname, dirparentrankid, reqparentrankid) 
  VALUES ("Monera",1,"Organism",1,1),("Monera",10,"Kingdom",1,1),("Monera",20,"Subkingdom",10,10),("Monera",30,"Phylum",20,10),("Monera",40,"Subphylum",30,30),("Monera",60,"Class",50,30),("Monera",70,"Subclass",60,60),("Monera",100,"Order",70,60),("Monera",110,"Suborder",100,100),("Monera",140,"Family",110,100),("Monera",150,"Subfamily",140,140),("Monera",160,"Tribe",150,140),("Monera",170,"Subtribe",160,140),("Monera",180,"Genus",170,140),("Monera",190,"Subgenus",180,180),("Monera",220,"Species",210,180),("Monera",230,"Subspecies",220,180),("Monera",240,"Morph",220,180);
INSERT IGNORE INTO `taxonunits`(kingdomName,rankid,rankname, dirparentrankid, reqparentrankid) 
  VALUES ("Protista",1,"Organism",1,1),("Protista",10,"Kingdom",1,1),("Protista",20,"Subkingdom",10,10),("Protista",30,"Phylum",20,10),("Protista",40,"Subphylum",30,30),("Protista",60,"Class",50,30),("Protista",70,"Subclass",60,60),("Protista",100,"Order",70,60),("Protista",110,"Suborder",100,100),("Protista",140,"Family",110,100),("Protista",150,"Subfamily",140,140),("Protista",160,"Tribe",150,140),("Protista",170,"Subtribe",160,140),("Protista",180,"Genus",170,140),("Protista",190,"Subgenus",180,180),("Protista",220,"Species",210,180),("Protista",230,"Subspecies",220,180),("Protista",240,"Morph",220,180);
INSERT IGNORE INTO `taxonunits`(kingdomName,rankid,rankname, dirparentrankid, reqparentrankid) 
  VALUES ("Plantae",1,"Organism",1,1),("Plantae",10,"Kingdom",1,1),("Plantae",20,"Subkingdom",10,10),("Plantae",30,"Division",20,10),("Plantae",40,"Subdivision",30,30),("Plantae",50,"Superclass",40,30),("Plantae",60,"Class",50,30),("Plantae",70,"Subclass",60,60),("Plantae",100,"Order",70,60),("Plantae",110,"Suborder",100,100),("Plantae",140,"Family",110,100),("Plantae",150,"Subfamily",140,140),("Plantae",160,"Tribe",150,140),("Plantae",170,"Subtribe",160,140),("Plantae",180,"Genus",170,140),("Plantae",190,"Subgenus",180,180),("Plantae",200,"Section",190,180),("Plantae",210,"Subsection",200,180),("Plantae",220,"Species",210,180),("Plantae",230,"Subspecies",220,180),("Plantae",240,"Variety",220,180),("Plantae",250,"Subvariety",240,180),("Plantae",260,"Form",220,180),("Plantae",270,"Subform",260,180),("Plantae",300,"Cultivated",220,220);
INSERT IGNORE INTO `taxonunits`(kingdomName,rankid,rankname, dirparentrankid, reqparentrankid) 
  VALUES ("Fungi",1,"Organism",1,1),("Fungi",10,"Kingdom",1,1),("Fungi",20,"Subkingdom",10,10),("Fungi",30,"Division",20,10),("Fungi",40,"Subdivision",30,30),("Fungi",50,"Superclass",40,30),("Fungi",60,"Class",50,30),("Fungi",70,"Subclass",60,60),("Fungi",100,"Order",70,60),("Fungi",110,"Suborder",100,100),("Fungi",140,"Family",110,100),("Fungi",150,"Subfamily",140,140),("Fungi",160,"Tribe",150,140),("Fungi",170,"Subtribe",160,140),("Fungi",180,"Genus",170,140),("Fungi",190,"Subgenus",180,180),("Fungi",200,"Section",190,180),("Fungi",210,"Subsection",200,180),("Fungi",220,"Species",210,180),("Fungi",230,"Subspecies",220,180),("Fungi",240,"Variety",220,180),("Fungi",250,"Subvariety",240,180),("Fungi",260,"Form",220,180),("Fungi",270,"Subform",260,180),("Fungi",300,"Cultivated",220,220);
INSERT IGNORE INTO `taxonunits`(kingdomName,rankid,rankname, dirparentrankid, reqparentrankid) 
  VALUES ("Animalia",1,"Organism",1,1),("Animalia",10,"Kingdom",1,1),("Animalia",20,"Subkingdom",10,10),("Animalia",30,"Phylum",20,10),("Animalia",40,"Subphylum",30,30),("Animalia",60,"Class",50,30),("Animalia",70,"Subclass",60,60),("Animalia",100,"Order",70,60),("Animalia",110,"Suborder",100,100),("Animalia",140,"Family",110,100),("Animalia",150,"Subfamily",140,140),("Animalia",160,"Tribe",150,140),("Animalia",170,"Subtribe",160,140),("Animalia",180,"Genus",170,140),("Animalia",190,"Subgenus",180,180),("Animalia",220,"Species",210,180),("Animalia",230,"Subspecies",220,180),("Animalia",240,"Morph",220,180);

INSERT INTO `taxauthority` (`taxauthid`, `isprimary`, `name`) VALUES ('1', '1', 'Central Thesaurus');

INSERT INTO `taxa` (`TID`, `RankId`, `SciName`, `UnitName1`) VALUES ("1", "1", "Organism", "Organism");
INSERT INTO `taxa` (`TID`, `RankId`, `SciName`, `UnitName1`) VALUES ("2", "10", "Monera", "Monera");
INSERT INTO `taxa` (`TID`, `RankId`, `SciName`, `UnitName1`) VALUES ("3", "10", "Protista", "Protista");
INSERT INTO `taxa` (`TID`, `RankId`, `SciName`, `UnitName1`) VALUES ("4", "10", "Plantae", "Plantae");
INSERT INTO `taxa` (`TID`, `RankId`, `SciName`, `UnitName1`) VALUES ("5", "10", "Fungi", "Fungi");
INSERT INTO `taxa` (`TID`, `RankId`, `SciName`, `UnitName1`) VALUES ("6", "10", "Animalia", "Animalia");

INSERT INTO `taxstatus` (`tid`, `tidaccepted`, `taxauthid`, `parenttid`) VALUES ("1", "1", "1", "1");
INSERT INTO `taxstatus` (`tid`, `tidaccepted`, `taxauthid`, `parenttid`) VALUES ("2", "2", "1", "1");
INSERT INTO `taxstatus` (`tid`, `tidaccepted`, `taxauthid`, `parenttid`) VALUES ("3", "3", "1", "1");
INSERT INTO `taxstatus` (`tid`, `tidaccepted`, `taxauthid`, `parenttid`) VALUES ("4", "4", "1", "1");
INSERT INTO `taxstatus` (`tid`, `tidaccepted`, `taxauthid`, `parenttid`) VALUES ("5", "5", "1", "1");
INSERT INTO `taxstatus` (`tid`, `tidaccepted`, `taxauthid`, `parenttid`) VALUES ("6", "6", "1", "1");


-- Geographic thesaurus tables
INSERT INTO `lkupcountry` VALUES (1,'Andorra','AD','AND',20,'2011-06-01 01:35:22'),(2,'United Arab Emirates','AE','ARE',784,'2011-06-01 01:35:22'),(3,'Afghanistan','AF','AFG',4,'2011-06-01 01:35:22'),(4,'Antigua and Barbuda','AG','ATG',28,'2011-06-01 01:35:22'),(5,'Anguilla','AI','AIA',660,'2011-06-01 01:35:22'),(6,'Albania','AL','ALB',8,'2011-06-01 01:35:22'),(7,'Armenia','AM','ARM',51,'2011-06-01 01:35:22'),(8,'Netherlands Antilles','AN','ANT',530,'2011-06-01 01:35:22'),(9,'Angola','AO','AGO',24,'2011-06-01 01:35:22'),(10,'Antarctica','AQ',NULL,NULL,'2011-06-01 01:35:22'),(11,'Argentina','AR','ARG',32,'2011-06-01 01:35:22'),(12,'American Samoa','AS','ASM',16,'2011-06-01 01:35:22'),(13,'Austria','AT','AUT',40,'2011-06-01 01:35:22'),(14,'Australia','AU','AUS',36,'2011-06-01 01:35:22'),(15,'Aruba','AW','ABW',533,'2011-06-01 01:35:22'),(16,'Azerbaijan','AZ','AZE',31,'2011-06-01 01:35:22'),(17,'Bosnia and Herzegovina','BA','BIH',70,'2011-06-01 01:35:22'),(18,'Barbados','BB','BRB',52,'2011-06-01 01:35:22'),(19,'Bangladesh','BD','BGD',50,'2011-06-01 01:35:22'),(20,'Belgium','BE','BEL',56,'2011-06-01 01:35:22'),(21,'Burkina Faso','BF','BFA',854,'2011-06-01 01:35:22'),(22,'Bulgaria','BG','BGR',100,'2011-06-01 01:35:22'),(23,'Bahrain','BH','BHR',48,'2011-06-01 01:35:22'),(24,'Burundi','BI','BDI',108,'2011-06-01 01:35:22'),(25,'Benin','BJ','BEN',204,'2011-06-01 01:35:22'),(26,'Bermuda','BM','BMU',60,'2011-06-01 01:35:22'),(27,'Brunei Darussalam','BN','BRN',96,'2011-06-01 01:35:22'),(28,'Bolivia','BO','BOL',68,'2011-06-01 01:35:22'),(29,'Brazil','BR','BRA',76,'2011-06-01 01:35:22'),(30,'Bahamas','BS','BHS',44,'2011-06-01 01:35:22'),(31,'Bhutan','BT','BTN',64,'2011-06-01 01:35:22'),(32,'Bouvet Island','BV',NULL,NULL,'2011-06-01 01:35:22'),(33,'Botswana','BW','BWA',72,'2011-06-01 01:35:22'),(34,'Belarus','BY','BLR',112,'2011-06-01 01:35:22'),(35,'Belize','BZ','BLZ',84,'2011-06-01 01:35:22'),(36,'Canada','CA','CAN',124,'2011-06-01 01:35:22'),(37,'Cocos (Keeling) Islands','CC',NULL,NULL,'2011-06-01 01:35:22'),(38,'Congo, the Democratic Republic of the','CD','COD',180,'2011-06-01 01:35:22'),(39,'Central African Republic','CF','CAF',140,'2011-06-01 01:35:22'),(40,'Congo','CG','COG',178,'2011-06-01 01:35:22'),(41,'Switzerland','CH','CHE',756,'2011-06-01 01:35:22'),(42,'Cote D\'Ivoire','CI','CIV',384,'2011-06-01 01:35:22'),(43,'Cook Islands','CK','COK',184,'2011-06-01 01:35:22'),(44,'Chile','CL','CHL',152,'2011-06-01 01:35:22'),(45,'Cameroon','CM','CMR',120,'2011-06-01 01:35:22'),(46,'China','CN','CHN',156,'2011-06-01 01:35:22'),(47,'Colombia','CO','COL',170,'2011-06-01 01:35:22'),(48,'Costa Rica','CR','CRI',188,'2011-06-01 01:35:22'),(49,'Serbia and Montenegro','CS',NULL,NULL,'2011-06-01 01:35:22'),(50,'Cuba','CU','CUB',192,'2011-06-01 01:35:22'),(51,'Cape Verde','CV','CPV',132,'2011-06-01 01:35:22'),(52,'Christmas Island','CX',NULL,NULL,'2011-06-01 01:35:22'),(53,'Cyprus','CY','CYP',196,'2011-06-01 01:35:22'),(54,'Czech Republic','CZ','CZE',203,'2011-06-01 01:35:22'),(55,'Germany','DE','DEU',276,'2011-06-01 01:35:22'),(56,'Djibouti','DJ','DJI',262,'2011-06-01 01:35:22'),(57,'Denmark','DK','DNK',208,'2011-06-01 01:35:22'),(58,'Dominica','DM','DMA',212,'2011-06-01 01:35:22'),(59,'Dominican Republic','DO','DOM',214,'2011-06-01 01:35:22'),(60,'Algeria','DZ','DZA',12,'2011-06-01 01:35:22'),(61,'Ecuador','EC','ECU',218,'2011-06-01 01:35:22'),(62,'Estonia','EE','EST',233,'2011-06-01 01:35:22'),(63,'Egypt','EG','EGY',818,'2011-06-01 01:35:22'),(64,'Western Sahara','EH','ESH',732,'2011-06-01 01:35:22'),(65,'Eritrea','ER','ERI',232,'2011-06-01 01:35:22'),(66,'Spain','ES','ESP',724,'2011-06-01 01:35:22'),(67,'Ethiopia','ET','ETH',231,'2011-06-01 01:35:22'),(68,'Finland','FI','FIN',246,'2011-06-01 01:35:22'),(69,'Fiji','FJ','FJI',242,'2011-06-01 01:35:22'),(70,'Falkland  Islands (Malvinas)','FK','FLK',238,'2011-06-01 01:35:22'),(71,'Micronesia, Federated States of','FM','FSM',583,'2011-06-01 01:35:22'),(72,'Faroe Islands','FO','FRO',234,'2011-06-01 01:35:22');
INSERT INTO `lkupcountry` VALUES (73,'France','FR','FRA',250,'2011-06-01 01:35:22'),(74,'Gabon','GA','GAB',266,'2011-06-01 01:35:22'),(75,'United Kingdom','GB','GBR',826,'2011-06-01 01:35:22'),(76,'Grenada','GD','GRD',308,'2011-06-01 01:35:22'),(77,'Georgia','GE','GEO',268,'2011-06-01 01:35:22'),(78,'French Guiana','GF','GUF',254,'2011-06-01 01:35:22'),(79,'Ghana','GH','GHA',288,'2011-06-01 01:35:22'),(80,'Gibraltar','GI','GIB',292,'2011-06-01 01:35:22'),(81,'Greenland','GL','GRL',304,'2011-06-01 01:35:22'),(82,'Gambia','GM','GMB',270,'2011-06-01 01:35:22'),(83,'Guinea','GN','GIN',324,'2011-06-01 01:35:22'),(84,'Guadeloupe','GP','GLP',312,'2011-06-01 01:35:22'),(85,'Equatorial Guinea','GQ','GNQ',226,'2011-06-01 01:35:22'),(86,'Greece','GR','GRC',300,'2011-06-01 01:35:22'),(87,'South Georgia and the South Sandwich Islands','GS',NULL,NULL,'2011-06-01 01:35:22'),(88,'Guatemala','GT','GTM',320,'2011-06-01 01:35:22'),(89,'Guam','GU','GUM',316,'2011-06-01 01:35:22'),(90,'Guinea-Bissau','GW','GNB',624,'2011-06-01 01:35:22'),(91,'Guyana','GY','GUY',328,'2011-06-01 01:35:22'),(92,'Hong Kong','HK','HKG',344,'2011-06-01 01:35:22'),(93,'Heard Island and Mcdonald Islands','HM',NULL,NULL,'2011-06-01 01:35:22'),(94,'Honduras','HN','HND',340,'2011-06-01 01:35:22'),(95,'Croatia','HR','HRV',191,'2011-06-01 01:35:22'),(96,'Haiti','HT','HTI',332,'2011-06-01 01:35:22'),(97,'Hungary','HU','HUN',348,'2011-06-01 01:35:22'),(98,'Indonesia','ID','IDN',360,'2011-06-01 01:35:22'),(99,'Ireland','IE','IRL',372,'2011-06-01 01:35:22'),(100,'Israel','IL','ISR',376,'2011-06-01 01:35:22'),(101,'India','IN','IND',356,'2011-06-01 01:35:22'),(102,'British Indian Ocean Territory','IO',NULL,NULL,'2011-06-01 01:35:22'),(103,'Iraq','IQ','IRQ',368,'2011-06-01 01:35:22'),(104,'Iran, Islamic Republic of','IR','IRN',364,'2011-06-01 01:35:22'),(105,'Iceland','IS','ISL',352,'2011-06-01 01:35:22'),(106,'Italy','IT','ITA',380,'2011-06-01 01:35:22'),(107,'Jamaica','JM','JAM',388,'2011-06-01 01:35:22'),(108,'Jordan','JO','JOR',400,'2011-06-01 01:35:22'),(109,'Japan','JP','JPN',392,'2011-06-01 01:35:22'),(110,'Kenya','KE','KEN',404,'2011-06-01 01:35:22'),(111,'Kyrgyzstan','KG','KGZ',417,'2011-06-01 01:35:22'),(112,'Cambodia','KH','KHM',116,'2011-06-01 01:35:22'),(113,'Kiribati','KI','KIR',296,'2011-06-01 01:35:22'),(114,'Comoros','KM','COM',174,'2011-06-01 01:35:22'),(115,'Saint Kitts and Nevis','KN','KNA',659,'2011-06-01 01:35:22'),(116,'Korea, Democratic People\'s Republic of','KP','PRK',408,'2011-06-01 01:35:22'),(117,'Korea, Republic of','KR','KOR',410,'2011-06-01 01:35:22'),(118,'Kuwait','KW','KWT',414,'2011-06-01 01:35:22'),(119,'Cayman Islands','KY','CYM',136,'2011-06-01 01:35:22'),(120,'Kazakhstan','KZ','KAZ',398,'2011-06-01 01:35:22'),(121,'Lao People\'s Democratic Republic','LA','LAO',418,'2011-06-01 01:35:22'),(122,'Lebanon','LB','LBN',422,'2011-06-01 01:35:22'),(123,'Saint Lucia','LC','LCA',662,'2011-06-01 01:35:22'),(124,'Liechtenstein','LI','LIE',438,'2011-06-01 01:35:22'),(125,'Sri Lanka','LK','LKA',144,'2011-06-01 01:35:22'),(126,'Liberia','LR','LBR',430,'2011-06-01 01:35:22'),(127,'Lesotho','LS','LSO',426,'2011-06-01 01:35:22'),(128,'Lithuania','LT','LTU',440,'2011-06-01 01:35:22'),(129,'Luxembourg','LU','LUX',442,'2011-06-01 01:35:22'),(130,'Latvia','LV','LVA',428,'2011-06-01 01:35:22'),(131,'Libyan Arab Jamahiriya','LY','LBY',434,'2011-06-01 01:35:22'),(132,'Morocco','MA','MAR',504,'2011-06-01 01:35:22'),(133,'Monaco','MC','MCO',492,'2011-06-01 01:35:22'),(134,'Moldova, Republic of','MD','MDA',498,'2011-06-01 01:35:22'),(135,'Madagascar','MG','MDG',450,'2011-06-01 01:35:22'),(136,'Marshall Islands','MH','MHL',584,'2011-06-01 01:35:22'),(137,'Macedonia, the Former Yugoslav Republic of','MK','MKD',807,'2011-06-01 01:35:22'),(138,'Mali','ML','MLI',466,'2011-06-01 01:35:22'),(139,'Myanmar','MM','MMR',104,'2011-06-01 01:35:22'),(140,'Mongolia','MN','MNG',496,'2011-06-01 01:35:22'),(141,'Macao','MO','MAC',446,'2011-06-01 01:35:22');
INSERT INTO `lkupcountry` VALUES (142,'Northern Mariana Islands','MP','MNP',580,'2011-06-01 01:35:22'),(143,'Martinique','MQ','MTQ',474,'2011-06-01 01:35:22'),(144,'Mauritania','MR','MRT',478,'2011-06-01 01:35:22'),(145,'Montserrat','MS','MSR',500,'2011-06-01 01:35:22'),(146,'Malta','MT','MLT',470,'2011-06-01 01:35:22'),(147,'Mauritius','MU','MUS',480,'2011-06-01 01:35:22'),(148,'Maldives','MV','MDV',462,'2011-06-01 01:35:22'),(149,'Malawi','MW','MWI',454,'2011-06-01 01:35:22'),(150,'Mexico','MX','MEX',484,'2011-06-01 01:35:22'),(151,'Malaysia','MY','MYS',458,'2011-06-01 01:35:22'),(152,'Mozambique','MZ','MOZ',508,'2011-06-01 01:35:22'),(153,'Namibia','NA','NAM',516,'2011-06-01 01:35:22'),(154,'New Caledonia','NC','NCL',540,'2011-06-01 01:35:22'),(155,'Niger','NE','NER',562,'2011-06-01 01:35:22'),(156,'Norfolk Island','NF','NFK',574,'2011-06-01 01:35:22'),(157,'Nigeria','NG','NGA',566,'2011-06-01 01:35:22'),(158,'Nicaragua','NI','NIC',558,'2011-06-01 01:35:22'),(159,'Netherlands','NL','NLD',528,'2011-06-01 01:35:22'),(160,'Norway','NO','NOR',578,'2011-06-01 01:35:22'),(161,'Nepal','NP','NPL',524,'2011-06-01 01:35:22'),(162,'Nauru','NR','NRU',520,'2011-06-01 01:35:22'),(163,'Niue','NU','NIU',570,'2011-06-01 01:35:22'),(164,'New Zealand','NZ','NZL',554,'2011-06-01 01:35:22'),(165,'Oman','OM','OMN',512,'2011-06-01 01:35:22'),(166,'Panama','PA','PAN',591,'2011-06-01 01:35:22'),(167,'Peru','PE','PER',604,'2011-06-01 01:35:22'),(168,'French Polynesia','PF','PYF',258,'2011-06-01 01:35:22'),(169,'Papua New Guinea','PG','PNG',598,'2011-06-01 01:35:22'),(170,'Philippines','PH','PHL',608,'2011-06-01 01:35:22'),(171,'Pakistan','PK','PAK',586,'2011-06-01 01:35:22'),(172,'Poland','PL','POL',616,'2011-06-01 01:35:22'),(173,'Saint Pierre and Miquelon','PM','SPM',666,'2011-06-01 01:35:22'),(174,'Pitcairn','PN','PCN',612,'2011-06-01 01:35:22'),(175,'Puerto Rico','PR','PRI',630,'2011-06-01 01:35:22'),(176,'Palestinian Territory, Occupied','PS',NULL,NULL,'2011-06-01 01:35:22'),(177,'Portugal','PT','PRT',620,'2011-06-01 01:35:22'),(178,'Palau','PW','PLW',585,'2011-06-01 01:35:22'),(179,'Paraguay','PY','PRY',600,'2011-06-01 01:35:22'),(180,'Qatar','QA','QAT',634,'2011-06-01 01:35:22'),(181,'Reunion','RE','REU',638,'2011-06-01 01:35:22'),(182,'Romania','RO','ROM',642,'2011-06-01 01:35:22'),(183,'Russian Federation','RU','RUS',643,'2011-06-01 01:35:22'),(184,'Rwanda','RW','RWA',646,'2011-06-01 01:35:22'),(185,'Saudi Arabia','SA','SAU',682,'2011-06-01 01:35:22'),(186,'Solomon Islands','SB','SLB',90,'2011-06-01 01:35:22'),(187,'Seychelles','SC','SYC',690,'2011-06-01 01:35:22'),(188,'Sudan','SD','SDN',736,'2011-06-01 01:35:22'),(189,'Sweden','SE','SWE',752,'2011-06-01 01:35:22'),(190,'Singapore','SG','SGP',702,'2011-06-01 01:35:22'),(191,'Saint Helena','SH','SHN',654,'2011-06-01 01:35:22'),(192,'Slovenia','SI','SVN',705,'2011-06-01 01:35:22'),(193,'Svalbard and Jan Mayen','SJ','SJM',744,'2011-06-01 01:35:22'),(194,'Slovakia','SK','SVK',703,'2011-06-01 01:35:22'),(195,'Sierra Leone','SL','SLE',694,'2011-06-01 01:35:22'),(196,'San Marino','SM','SMR',674,'2011-06-01 01:35:22'),(197,'Senegal','SN','SEN',686,'2011-06-01 01:35:22'),(198,'Somalia','SO','SOM',706,'2011-06-01 01:35:22'),(199,'Suriname','SR','SUR',740,'2011-06-01 01:35:22'),(200,'Sao Tome and Principe','ST','STP',678,'2011-06-01 01:35:22'),(201,'El Salvador','SV','SLV',222,'2011-06-01 01:35:22'),(202,'Syrian Arab Republic','SY','SYR',760,'2011-06-01 01:35:22'),(203,'Swaziland','SZ','SWZ',748,'2011-06-01 01:35:22'),(204,'Turks and Caicos Islands','TC','TCA',796,'2011-06-01 01:35:22'),(205,'Chad','TD','TCD',148,'2011-06-01 01:35:22'),(206,'French Southern Territories','TF',NULL,NULL,'2011-06-01 01:35:22'),(207,'Togo','TG','TGO',768,'2011-06-01 01:35:22'),(208,'Thailand','TH','THA',764,'2011-06-01 01:35:22'),(209,'Tajikistan','TJ','TJK',762,'2011-06-01 01:35:22'),(210,'Tokelau','TK','TKL',772,'2011-06-01 01:35:22'),(211,'Timor-Leste','TL',NULL,NULL,'2011-06-01 01:35:22');
INSERT INTO `lkupcountry` VALUES (212,'Turkmenistan','TM','TKM',795,'2011-06-01 01:35:22'),(213,'Tunisia','TN','TUN',788,'2011-06-01 01:35:22'),(214,'Tonga','TO','TON',776,'2011-06-01 01:35:22'),(215,'Turkey','TR','TUR',792,'2011-06-01 01:35:22'),(216,'Trinidad and Tobago','TT','TTO',780,'2011-06-01 01:35:22'),(217,'Tuvalu','TV','TUV',798,'2011-06-01 01:35:22'),(218,'Taiwan, Province of China','TW','TWN',158,'2011-06-01 01:35:22'),(219,'Tanzania, United Republic of','TZ','TZA',834,'2011-06-01 01:35:22'),(220,'Ukraine','UA','UKR',804,'2011-06-01 01:35:22'),(221,'Uganda','UG','UGA',800,'2011-06-01 01:35:22'),(222,'United States Minor Outlying Islands','UM',NULL,NULL,'2011-06-01 01:35:22'),(223,'United States','US','USA',840,'2011-06-01 01:35:22'),(224,'Uruguay','UY','URY',858,'2011-06-01 01:35:22'),(225,'Uzbekistan','UZ','UZB',860,'2011-06-01 01:35:22'),(226,'Holy See (Vatican City State)','VA','VAT',336,'2011-06-01 01:35:22'),(227,'Saint Vincent and the Grenadines','VC','VCT',670,'2011-06-01 01:35:22'),(228,'Venezuela','VE','VEN',862,'2011-06-01 01:35:22'),(229,'Virgin Islands, British','VG','VGB',92,'2011-06-01 01:35:22'),(230,'Virgin Islands,  U.s.','VI','VIR',850,'2011-06-01 01:35:22'),(231,'Viet Nam','VN','VNM',704,'2011-06-01 01:35:22'),(232,'Vanuatu','VU','VUT',548,'2011-06-01 01:35:22'),(233,'Wallis and Futuna','WF','WLF',876,'2011-06-01 01:35:22'),(234,'Samoa','WS','WSM',882,'2011-06-01 01:35:22'),(235,'Yemen','YE','YEM',887,'2011-06-01 01:35:22'),(236,'Mayotte','YT',NULL,NULL,'2011-06-01 01:35:22'),(237,'South Africa','ZA','ZAF',710,'2011-06-01 01:35:22'),(238,'Zambia','ZM','ZMB',894,'2011-06-01 01:35:22'),(239,'Zimbabwe','ZW','ZWE',716,'2011-06-01 01:35:22'),(256,'USA','US','USA',840,'2011-06-01 01:41:38');

INSERT INTO `lkupstateprovince` VALUES (1,256,'Alaska','AK','2011-06-01 00:45:09'),(2,256,'Alabama','AL','2011-06-01 00:45:09'),(3,256,'American Samoa','AS','2011-06-01 00:45:09'),(4,256,'Arizona','AZ','2011-06-01 00:45:09'),(5,256,'Arkansas','AR','2011-06-01 00:45:09'),(6,256,'California','CA','2011-06-01 00:45:09'),(7,256,'Colorado','CO','2011-06-01 00:45:09'),(8,256,'Connecticut','CT','2011-06-01 00:45:09'),(9,256,'Delaware','DE','2011-06-01 00:45:09'),(10,256,'District of Columbia','DC','2011-06-01 00:45:09'),(11,256,'Federated States of Micronesia','FM','2011-06-01 00:45:09'),(12,256,'Florida','FL','2011-06-01 00:45:09'),(13,256,'Georgia','GA','2011-06-01 00:45:09'),(14,256,'Guam','GU','2011-06-01 00:45:09'),(15,256,'Hawaii','HI','2011-06-01 00:45:09'),(16,256,'Idaho','ID','2011-06-01 00:45:09'),(17,256,'Illinois','IL','2011-06-01 00:45:09'),(18,256,'Indiana','IN','2011-06-01 00:45:09'),(19,256,'Iowa','IA','2011-06-01 00:45:09'),(20,256,'Kansas','KS','2011-06-01 00:45:09'),(21,256,'Kentucky','KY','2011-06-01 00:45:09'),(22,256,'Louisiana','LA','2011-06-01 00:45:09'),(23,256,'Maine','ME','2011-06-01 00:45:09'),(24,256,'Marshall Islands','MH','2011-06-01 00:45:09'),(25,256,'Maryland','MD','2011-06-01 00:45:09'),(26,256,'Massachusetts','MA','2011-06-01 00:45:09'),(27,256,'Michigan','MI','2011-06-01 00:45:09'),(28,256,'Minnesota','MN','2011-06-01 00:45:09'),(29,256,'Mississippi','MS','2011-06-01 00:45:09'),(30,256,'Missouri','MO','2011-06-01 00:45:09'),(31,256,'Montana','MT','2011-06-01 00:45:09'),(32,256,'Nebraska','NE','2011-06-01 00:45:09'),(33,256,'Nevada','NV','2011-06-01 00:45:09'),(34,256,'New Hampshire','NH','2011-06-01 00:45:09'),(35,256,'New Jersey','NJ','2011-06-01 00:45:09'),(36,256,'New Mexico','NM','2011-06-01 00:45:09'),(37,256,'New York','NY','2011-06-01 00:45:09'),(38,256,'North Carolina','NC','2011-06-01 00:45:09'),(39,256,'North Dakota','ND','2011-06-01 00:45:09'),(40,256,'Northern Mariana Islands','MP','2011-06-01 00:45:09'),(41,256,'Ohio','OH','2011-06-01 00:45:09'),(42,256,'Oklahoma','OK','2011-06-01 00:45:09'),(43,256,'Oregon','OR','2011-06-01 00:45:09'),(44,256,'Palau','PW','2011-06-01 00:45:09'),(45,256,'Pennsylvania','PA','2011-06-01 00:45:09'),(46,256,'Puerto Rico','PR','2011-06-01 00:45:09'),(47,256,'Rhode Island','RI','2011-06-01 00:45:09'),(48,256,'South Carolina','SC','2011-06-01 00:45:09'),(49,256,'South Dakota','SD','2011-06-01 00:45:09'),(50,256,'Tennessee','TN','2011-06-01 00:45:09'),(51,256,'Texas','TX','2011-06-01 00:45:09'),(52,256,'Utah','UT','2011-06-01 00:45:09'),(53,256,'Vermont','VT','2011-06-01 00:45:09'),(54,256,'Virgin Islands','VI','2011-06-01 00:45:09'),(55,256,'Virginia','VA','2011-06-01 00:45:09'),(56,256,'Washington','WA','2011-06-01 00:45:09'),(57,256,'West Virginia','WV','2011-06-01 00:45:09'),(58,256,'Wisconsin','WI','2011-06-01 00:45:09'),(59,256,'Wyoming','WY','2011-06-01 00:45:09'),(60,256,'Armed Forces Africa','AE','2011-06-01 00:45:09'),(61,256,'Armed Forces Americas (except Canada)','AA','2011-06-01 00:45:09'),(62,256,'Armed Forces Canada','AE','2011-06-01 00:45:09'),(63,256,'Armed Forces Europe','AE','2011-06-01 00:45:09'),(64,256,'Armed Forces Middle East','AE','2011-06-01 00:45:09'),(65,256,'Armed Forces Pacific','AP','2011-06-01 00:45:09'),(128,223,'Alaska','AK','2011-06-01 00:45:18'),(129,223,'Alabama','AL','2011-06-01 00:45:18'),(130,223,'American Samoa','AS','2011-06-01 00:45:18'),(131,223,'Arizona','AZ','2011-06-01 00:45:18'),(132,223,'Arkansas','AR','2011-06-01 00:45:18'),(133,223,'California','CA','2011-06-01 00:45:18'),(134,223,'Colorado','CO','2011-06-01 00:45:18'),(135,223,'Connecticut','CT','2011-06-01 00:45:18'),(136,223,'Delaware','DE','2011-06-01 00:45:18'),(137,223,'District of Columbia','DC','2011-06-01 00:45:18'),(138,223,'Federated States of Micronesia','FM','2011-06-01 00:45:18'),(139,223,'Florida','FL','2011-06-01 00:45:18'),(140,223,'Georgia','GA','2011-06-01 00:45:18'),(141,223,'Guam','GU','2011-06-01 00:45:18'),(142,223,'Hawaii','HI','2011-06-01 00:45:18'),(143,223,'Idaho','ID','2011-06-01 00:45:18'),(144,223,'Illinois','IL','2011-06-01 00:45:18'),(145,223,'Indiana','IN','2011-06-01 00:45:18'),(146,223,'Iowa','IA','2011-06-01 00:45:18'),(147,223,'Kansas','KS','2011-06-01 00:45:18'),(148,223,'Kentucky','KY','2011-06-01 00:45:18'),(149,223,'Louisiana','LA','2011-06-01 00:45:18'),(150,223,'Maine','ME','2011-06-01 00:45:18'),(151,223,'Marshall Islands','MH','2011-06-01 00:45:18'),(152,223,'Maryland','MD','2011-06-01 00:45:18'),(153,223,'Massachusetts','MA','2011-06-01 00:45:18'),(154,223,'Michigan','MI','2011-06-01 00:45:18'),(155,223,'Minnesota','MN','2011-06-01 00:45:18'),(156,223,'Mississippi','MS','2011-06-01 00:45:18'),(157,223,'Missouri','MO','2011-06-01 00:45:18'),(158,223,'Montana','MT','2011-06-01 00:45:18'),(159,223,'Nebraska','NE','2011-06-01 00:45:18'),(160,223,'Nevada','NV','2011-06-01 00:45:18'),(161,223,'New Hampshire','NH','2011-06-01 00:45:18'),(162,223,'New Jersey','NJ','2011-06-01 00:45:18'),(163,223,'New Mexico','NM','2011-06-01 00:45:18'),(164,223,'New York','NY','2011-06-01 00:45:18'),(165,223,'North Carolina','NC','2011-06-01 00:45:18'),(166,223,'North Dakota','ND','2011-06-01 00:45:18'),(167,223,'Northern Mariana Islands','MP','2011-06-01 00:45:18'),(168,223,'Ohio','OH','2011-06-01 00:45:18'),(169,223,'Oklahoma','OK','2011-06-01 00:45:18'),(170,223,'Oregon','OR','2011-06-01 00:45:18'),(171,223,'Palau','PW','2011-06-01 00:45:18'),(172,223,'Pennsylvania','PA','2011-06-01 00:45:18'),(173,223,'Puerto Rico','PR','2011-06-01 00:45:18'),(174,223,'Rhode Island','RI','2011-06-01 00:45:18'),(175,223,'South Carolina','SC','2011-06-01 00:45:18'),(176,223,'South Dakota','SD','2011-06-01 00:45:18'),(177,223,'Tennessee','TN','2011-06-01 00:45:18'),(178,223,'Texas','TX','2011-06-01 00:45:18'),(179,223,'Utah','UT','2011-06-01 00:45:18'),(180,223,'Vermont','VT','2011-06-01 00:45:18'),(181,223,'Virgin Islands','VI','2011-06-01 00:45:18'),(182,223,'Virginia','VA','2011-06-01 00:45:18'),(183,223,'Washington','WA','2011-06-01 00:45:18'),(184,223,'West Virginia','WV','2011-06-01 00:45:18'),(185,223,'Wisconsin','WI','2011-06-01 00:45:18'),(186,223,'Wyoming','WY','2011-06-01 00:45:18'),(187,223,'Armed Forces Africa','AE','2011-06-01 00:45:18'),(188,223,'Armed Forces Americas (except Canada)','AA','2011-06-01 00:45:18'),(189,223,'Armed Forces Canada','AE','2011-06-01 00:45:18'),(190,223,'Armed Forces Europe','AE','2011-06-01 00:45:18'),(191,223,'Armed Forces Middle East','AE','2011-06-01 00:45:18'),(192,223,'Armed Forces Pacific','AP','2011-06-01 00:45:18'),(193,150,'Quintana Roo',NULL,'2011-09-12 20:59:42'),(194,44,'Los Rios',NULL,'2011-09-12 21:13:20'),(195,44,'Los Lagos',NULL,'2011-09-12 21:28:08'),(196,44,'Araucania',NULL,'2011-09-12 21:36:25'),(197,150,'Sonora',NULL,'2011-09-14 20:29:58'),(198,150,'Baja California',NULL,'2011-09-14 21:02:02'),(199,150,'San Luis Potosi',NULL,'2011-09-14 21:07:23'),(200,150,'Sinaloa',NULL,'2011-09-14 21:22:30'),(202,36,'Manitoba',NULL,'2011-09-16 23:30:11'),(203,36,'British Columbia',NULL,'2011-09-16 23:49:22'),(204,36,'Alberta',NULL,'2011-09-17 00:08:38'),(205,150,'Coahuila',NULL,'2011-09-18 15:35:19'),(206,150,'Chihuahua',NULL,'2011-09-19 17:01:06'),(207,150,'Zacatecas',NULL,'2011-09-19 17:23:48'),(208,150,'Nuevo Leon',NULL,'2011-09-19 17:37:09'),(209,36,'Ottawa',NULL,'2011-09-20 06:54:20'),(210,36,'Ontario',NULL,'2011-09-20 21:21:28'),(211,12,'Sonora',NULL,'2011-09-23 18:18:25'),(212,228,'Aragua',NULL,'2011-09-27 17:44:08'),(214,150,'Tamaulipas',NULL,'2011-09-29 17:29:04'),(215,150,'Hidalgo',NULL,'2011-09-29 17:31:05'),(217,48,'San Jose',NULL,'2011-09-29 18:52:48'),(218,29,'Espirito Santo',NULL,'2011-09-30 19:55:32'),(219,11,'Neuquen',NULL,'2011-09-30 23:00:34'),(221,44,'Magallanes y Antartica Chilena',NULL,'2011-09-30 23:19:08'),(222,28,'Cochabamba',NULL,'2011-09-30 23:26:16'),(223,28,'La Paz',NULL,'2011-09-30 23:34:10'),(225,167,'Cajamarca',NULL,'2011-09-30 23:47:12'),(228,29,'Rio Grande do Sul',NULL,'2011-10-03 21:59:10'),(229,256,'Flagstaff',NULL,'2011-10-03 22:57:35'),(230,29,'Acre',NULL,'2011-10-04 19:14:29'),(231,150,'Aguascalientes',NULL,'2011-10-04 19:14:29'),(232,44,'Aisen',NULL,'2011-10-04 19:14:29'),(233,29,'Alagoas',NULL,'2011-10-04 19:14:30'),(234,48,'Alajuela',NULL,'2011-10-04 19:14:30'),(235,29,'Amapa',NULL,'2011-10-04 19:14:30'),(236,29,'Amazonas',NULL,'2011-10-04 19:14:30'),(237,47,'Amazonas',NULL,'2011-10-04 19:14:30'),(238,167,'Amazonas',NULL,'2011-10-04 19:14:30'),(239,228,'Amazonas',NULL,'2011-10-04 19:14:30'),(240,167,'Ancash',NULL,'2011-10-04 19:14:30'),(241,47,'Antioquia',NULL,'2011-10-04 19:14:30'),(242,44,'Antofagasta',NULL,'2011-10-04 19:14:30'),(243,228,'Anzoategui',NULL,'2011-10-04 19:14:30'),(244,228,'Apure',NULL,'2011-10-04 19:14:30'),(245,167,'Apurimac',NULL,'2011-10-04 19:14:30'),(246,47,'Arauca',NULL,'2011-10-04 19:14:30'),(247,167,'Arequipa',NULL,'2011-10-04 19:14:30'),(248,44,'Arica y Parinacota',NULL,'2011-10-04 19:14:30'),(249,44,'Atacama',NULL,'2011-10-04 19:14:30'),(250,47,'Atlantico',NULL,'2011-10-04 19:14:30'),(251,167,'Ayacucho',NULL,'2011-10-04 19:14:31'),(252,29,'Bahia',NULL,'2011-10-04 19:14:31'),(253,150,'Baja California Sur',NULL,'2011-10-04 19:14:31'),(254,228,'Barinas',NULL,'2011-10-04 19:14:31'),(255,28,'Beni',NULL,'2011-10-04 19:14:31'),(256,44,'Bio Bio',NULL,'2011-10-04 19:14:31'),(257,47,'Bolivar',NULL,'2011-10-04 19:14:31'),(258,228,'Bolivar',NULL,'2011-10-04 19:14:31'),(259,47,'Boyaca',NULL,'2011-10-04 19:14:31'),(260,11,'Buenos Aires',NULL,'2011-10-04 19:14:31'),(261,47,'Caldas',NULL,'2011-10-04 19:14:31'),(262,167,'Callao',NULL,'2011-10-04 19:14:31'),(263,150,'Campeche',NULL,'2011-10-04 19:14:31'),(264,47,'Caqueta',NULL,'2011-10-04 19:14:31'),(265,228,'Carabobo',NULL,'2011-10-04 19:14:31'),(266,48,'Cartago',NULL,'2011-10-04 19:14:31'),(267,47,'Casanare',NULL,'2011-10-04 19:14:31'),(268,11,'Catamarca',NULL,'2011-10-04 19:14:31'),(269,47,'Cauca',NULL,'2011-10-04 19:14:31'),(270,29,'Ceara',NULL,'2011-10-04 19:14:31'),(271,47,'Cesar',NULL,'2011-10-04 19:14:31'),(272,11,'Chaco',NULL,'2011-10-04 19:14:31'),(273,150,'Chiapas',NULL,'2011-10-04 19:14:31'),(274,47,'Choco',NULL,'2011-10-04 19:14:31'),(275,11,'Chubut',NULL,'2011-10-04 19:14:32'),(276,28,'Chuquisaca',NULL,'2011-10-04 19:14:32'),(277,228,'Cojedes',NULL,'2011-10-04 19:14:32'),(278,150,'Colima',NULL,'2011-10-04 19:14:32'),(279,44,'Coquimbo',NULL,'2011-10-04 19:14:32'),(280,11,'Cordoba',NULL,'2011-10-04 19:14:32'),(281,47,'Cordoba',NULL,'2011-10-04 19:14:32'),(282,11,'Corrientes',NULL,'2011-10-04 19:14:32'),(283,47,'Cundinamarca',NULL,'2011-10-04 19:14:32'),(284,167,'Cuzco',NULL,'2011-10-04 19:14:32'),(285,228,'Delta Amacuro',NULL,'2011-10-04 19:14:32'),(286,47,'Distrito Capital',NULL,'2011-10-04 19:14:32'),(287,11,'Distrito Federal',NULL,'2011-10-04 19:14:32'),(288,29,'Distrito Federal',NULL,'2011-10-04 19:14:32'),(289,150,'Distrito Federal',NULL,'2011-10-04 19:14:32'),(290,150,'Durango',NULL,'2011-10-04 19:14:32'),(291,11,'Entre Rios',NULL,'2011-10-04 19:14:32'),(292,228,'Falcon',NULL,'2011-10-04 19:14:32'),(293,11,'Formosa',NULL,'2011-10-04 19:14:32'),(294,29,'Goias',NULL,'2011-10-04 19:14:32'),(295,47,'Guainia',NULL,'2011-10-04 19:14:32'),(296,48,'Guanacaste',NULL,'2011-10-04 19:14:32'),(297,150,'Guanajuato',NULL,'2011-10-04 19:14:32'),(298,228,'Guarico',NULL,'2011-10-04 19:14:32'),(299,47,'Guaviare',NULL,'2011-10-04 19:14:33'),(300,150,'Guerrero',NULL,'2011-10-04 19:14:33'),(301,48,'Heredia',NULL,'2011-10-04 19:14:33'),(302,167,'Huancavelica',NULL,'2011-10-04 19:14:33'),(303,167,'Huanuco',NULL,'2011-10-04 19:14:33'),(304,47,'Huila',NULL,'2011-10-04 19:14:33'),(305,167,'Ica',NULL,'2011-10-04 19:14:33'),(306,150,'Jalisco',NULL,'2011-10-04 19:14:33'),(307,11,'Jujuy',NULL,'2011-10-04 19:14:33'),(308,167,'Junin',NULL,'2011-10-04 19:14:33'),(309,47,'La Guajira',NULL,'2011-10-04 19:14:33'),(310,167,'La Libertad',NULL,'2011-10-04 19:14:33'),(311,11,'La Pampa',NULL,'2011-10-04 19:14:33'),(312,11,'La Rioja',NULL,'2011-10-04 19:14:33'),(313,167,'Lambayeque',NULL,'2011-10-04 19:14:33'),(314,228,'Lara',NULL,'2011-10-04 19:14:33'),(315,167,'Lima',NULL,'2011-10-04 19:14:33'),(316,48,'Limon',NULL,'2011-10-04 19:14:34'),(317,167,'Loreto',NULL,'2011-10-04 19:14:34'),(318,167,'Madre de Dios',NULL,'2011-10-04 19:14:34'),(319,47,'Magdalena',NULL,'2011-10-04 19:14:34'),(320,29,'Maranhao',NULL,'2011-10-04 19:14:34'),(321,29,'Mato Grosso',NULL,'2011-10-04 19:14:34'),(322,29,'Mato Grosso do Sul',NULL,'2011-10-04 19:14:34'),(323,44,'Maule',NULL,'2011-10-04 19:14:34'),(324,11,'Mendoza',NULL,'2011-10-04 19:14:34'),(325,228,'Merida',NULL,'2011-10-04 19:14:34'),(326,47,'Meta',NULL,'2011-10-04 19:14:34'),(327,44,'Metropolitana',NULL,'2011-10-04 19:14:34'),(328,150,'Mexico',NULL,'2011-10-04 19:14:34'),(329,150,'Michoacan',NULL,'2011-10-04 19:14:34'),(330,29,'Minas Gerais',NULL,'2011-10-04 19:14:34'),(331,228,'Miranda',NULL,'2011-10-04 19:14:34'),(332,11,'Misiones',NULL,'2011-10-04 19:14:34'),(333,228,'Monagas',NULL,'2011-10-04 19:14:34'),(334,167,'Moquegua',NULL,'2011-10-04 19:14:34'),(335,150,'Morelos',NULL,'2011-10-04 19:14:34'),(336,47,'Narino',NULL,'2011-10-04 19:14:34'),(337,150,'Nayarit',NULL,'2011-10-04 19:14:34'),(338,47,'Norte de Santander',NULL,'2011-10-04 19:14:34'),(339,228,'Nueva Esparta',NULL,'2011-10-04 19:14:34'),(340,150,'Oaxaca',NULL,'2011-10-04 19:14:35'),(341,44,'O\'Higgins',NULL,'2011-10-04 19:14:35'),(342,28,'Oruro',NULL,'2011-10-04 19:14:35'),(343,28,'Pando',NULL,'2011-10-04 19:14:35'),(344,29,'Para',NULL,'2011-10-04 19:14:35'),(345,29,'Paraiba',NULL,'2011-10-04 19:14:35'),(346,29,'Parana',NULL,'2011-10-04 19:14:35'),(347,167,'Pasco',NULL,'2011-10-04 19:14:35'),(348,29,'Pernambuco',NULL,'2011-10-04 19:14:35'),(349,29,'Piaui',NULL,'2011-10-04 19:14:36'),(350,167,'Piura',NULL,'2011-10-04 19:14:37'),(351,228,'Portuguesa',NULL,'2011-10-04 19:14:37'),(352,28,'Potosi',NULL,'2011-10-04 19:14:37'),(353,150,'Puebla',NULL,'2011-10-04 19:14:37'),(354,167,'Puno',NULL,'2011-10-04 19:14:37'),(355,48,'Puntarenas',NULL,'2011-10-04 19:14:37'),(356,47,'Putumayo',NULL,'2011-10-04 19:14:37'),(357,150,'Queretaro',NULL,'2011-10-04 19:14:37'),(358,47,'Quindio',NULL,'2011-10-04 19:14:37'),(359,29,'Rio de Janeiro',NULL,'2011-10-04 19:14:37'),(360,29,'Rio Grande do Norte',NULL,'2011-10-04 19:14:37'),(361,11,'Rio Negro',NULL,'2011-10-04 19:14:37'),(362,47,'Risaralda',NULL,'2011-10-04 19:14:37'),(363,29,'Rondonia',NULL,'2011-10-04 19:14:38'),(364,29,'Roraima',NULL,'2011-10-04 19:14:38'),(365,11,'Salta',NULL,'2011-10-04 19:14:38'),(366,47,'San Andres y Providencia',NULL,'2011-10-04 19:14:38'),(367,11,'San Juan',NULL,'2011-10-04 19:14:38'),(368,11,'San Luis',NULL,'2011-10-04 19:14:38'),(369,167,'San Martin',NULL,'2011-10-04 19:14:38'),(370,29,'Santa Catarina',NULL,'2011-10-04 19:14:38'),(371,11,'Santa Cruz',NULL,'2011-10-04 19:14:38'),(372,28,'Santa Cruz',NULL,'2011-10-04 19:14:38'),(373,11,'Santa Fe',NULL,'2011-10-04 19:14:38'),(374,47,'Santander',NULL,'2011-10-04 19:14:38'),(375,11,'Santiago del Estero',NULL,'2011-10-04 19:14:38'),(376,29,'Sao Paulo',NULL,'2011-10-04 19:14:38'),(377,29,'Sergipe',NULL,'2011-10-04 19:14:38'),(378,47,'Sucre',NULL,'2011-10-04 19:14:38'),(379,228,'Sucre',NULL,'2011-10-04 19:14:38'),(380,150,'Tabasco',NULL,'2011-10-04 19:14:38'),(381,228,'Tachira',NULL,'2011-10-04 19:14:39'),(382,167,'Tacna',NULL,'2011-10-04 19:14:39'),(383,44,'Tarapaca',NULL,'2011-10-04 19:14:39'),(384,28,'Tarija',NULL,'2011-10-04 19:14:39'),(385,11,'Tierra del Fuego',NULL,'2011-10-04 19:14:39'),(386,150,'Tlaxcala',NULL,'2011-10-04 19:14:39'),(387,29,'Tocantins',NULL,'2011-10-04 19:14:39'),(388,47,'Tolima',NULL,'2011-10-04 19:14:39'),(389,228,'Trujillo',NULL,'2011-10-04 19:14:39'),(390,11,'Tucuman',NULL,'2011-10-04 19:14:39'),(391,167,'Tumbes',NULL,'2011-10-04 19:14:39'),(392,167,'Ucayali',NULL,'2011-10-04 19:14:39'),(393,47,'Valle del Cauca',NULL,'2011-10-04 19:14:39'),(394,44,'Valparaiso',NULL,'2011-10-04 19:14:39'),(395,228,'Vargas',NULL,'2011-10-04 19:14:39'),(396,47,'Vaupes',NULL,'2011-10-04 19:14:39'),(397,150,'Veracruz',NULL,'2011-10-04 19:14:39'),(398,47,'Vichada',NULL,'2011-10-04 19:14:39'),(399,228,'Yaracuy',NULL,'2011-10-04 19:14:39'),(400,150,'Yucatan',NULL,'2011-10-04 19:14:39'),(401,228,'Zulia',NULL,'2011-10-04 19:14:39'),(403,36,'Newfoundland and Labrador',NULL,'2011-10-07 13:00:19'),(404,55,'Nordrhein-Westfalen',NULL,'2011-10-07 17:18:08'),(406,12,'Mexico',NULL,'2011-10-07 21:37:14'),(407,61,'Pichincha',NULL,'2011-10-07 22:17:24'),(409,159,'Overijssel',NULL,'2011-10-10 15:06:55'),(410,66,'Castile-La Mancha',NULL,'2011-10-10 21:49:04'),(411,166,'Panama',NULL,'2011-10-10 21:55:57'),(413,88,'Chiquimula',NULL,'2011-10-12 23:03:00'),(414,94,'Comayagua',NULL,'2011-10-13 21:04:26'),(415,4,'Saint George',NULL,'2011-10-13 21:40:35'),(416,143,'La Trinite',NULL,'2011-10-13 22:23:18'),(417,107,'Saint James',NULL,'2011-10-13 22:24:48'),(418,179,'Alto Parana',NULL,'2011-10-13 23:37:08'),(419,61,'Napo',NULL,'2011-10-14 16:59:41'),(421,179,'Caaguazu',NULL,'2011-10-18 17:08:45'),(422,101,'Punjab',NULL,'2011-10-26 21:05:25'),(424,125,'Central',NULL,'2011-10-28 21:10:54'),(425,79,'Tarkwa',NULL,'2011-10-28 21:29:02'),(426,79,'Western',NULL,'2011-10-28 21:37:27'),(427,109,'Miyagi',NULL,'2011-10-31 20:15:18'),(428,161,'Sagarmatha',NULL,'2011-10-31 21:12:23'),(429,35,'Cayo',NULL,'2011-10-31 21:22:26'),(430,120,'Aktobe',NULL,'2011-11-01 17:59:16'),(431,161,'Dhawalagiri',NULL,'2011-11-02 20:36:31'),(432,68,'Unknown',NULL,'2011-11-04 17:33:24'),(433,192,'Unknown',NULL,'2011-11-04 17:41:30'),(434,179,'Paraguari',NULL,'2011-11-04 20:02:26'),(435,179,'Cordillera',NULL,'2011-11-04 20:06:46'),(436,179,'Itapua',NULL,'2011-11-04 20:28:16'),(437,91,'Upper Takutu-Upper Essequibo',NULL,'2011-11-04 20:33:51'),(438,91,'Cuyuni-Mazaruni',NULL,'2011-11-04 20:36:50'),(439,59,'Elias Pina',NULL,'2011-11-04 21:57:13'),(440,59,'Azua',NULL,'2011-11-04 22:02:26'),(442,59,'La Vega',NULL,'2011-11-07 16:31:48'),(443,61,'Chimborazo',NULL,'2011-11-07 16:36:10'),(444,88,'Peten',NULL,'2011-11-10 20:00:14'),(445,201,'Chalatenango',NULL,'2011-11-11 15:49:22'),(446,224,'Maldonado',NULL,'2011-11-14 18:50:18'),(447,172,'Pomerania',NULL,'2011-11-16 18:07:46'),(448,189,'unknown',NULL,'2011-11-16 18:16:36'),(449,55,'Saxony',NULL,'2011-11-16 18:30:01'),(450,189,'SkÃƒÂ¥ne',NULL,'2011-11-16 21:27:13'),(451,256,'D.C.',NULL,'2011-11-17 23:50:56'),(453,224,'Artigas',NULL,'2012-07-18 18:28:02'),(454,224,'Canelones',NULL,'2012-07-18 18:28:11'),(455,224,'Cerro Largo',NULL,'2012-07-18 18:28:17'),(456,224,'Colonia',NULL,'2012-07-18 18:28:21'),(457,224,'Durazno',NULL,'2012-07-18 18:28:36'),(458,224,'Flores',NULL,'2012-07-18 18:28:42'),(459,224,'Florida',NULL,'2012-07-18 18:28:51'),(460,224,'Lavalleja',NULL,'2012-07-18 18:28:57'),(461,224,'Montevideo',NULL,'2012-07-18 18:29:04'),(462,224,'Paysandu',NULL,'2012-07-18 18:29:09'),(463,224,'Rio Negro',NULL,'2012-07-18 18:29:15'),(464,224,'Rivera',NULL,'2012-07-18 18:29:21'),(465,224,'Rocha',NULL,'2012-07-18 18:29:25'),(466,224,'Salto',NULL,'2012-07-18 18:29:29'),(467,224,'San Jose',NULL,'2012-07-18 18:29:32'),(468,224,'Soriano',NULL,'2012-07-18 18:29:37'),(469,224,'Tacuarembo',NULL,'2012-07-18 18:29:52'),(470,224,'Treinta y Tres',NULL,'2012-07-18 18:29:58'),(471,230,'Saint Croix',NULL,'2012-07-18 18:31:15'),(472,230,'Saint John',NULL,'2012-07-18 18:31:22'),(473,230,'Saint Thomas',NULL,'2012-07-18 18:31:28'),(474,229,'Anegada',NULL,'2012-07-18 18:31:55'),(475,229,'Jost Van Dyke',NULL,'2012-07-18 18:32:00'),(476,229,'Tortola',NULL,'2012-07-18 18:32:04'),(477,229,'Virgin Gorda',NULL,'2012-07-18 18:32:09'),(478,216,'Tobago',NULL,'2012-07-18 18:32:40'),(479,216,'Trinidad',NULL,'2012-07-18 18:32:44'),(480,227,'Charlotte',NULL,'2012-07-18 18:33:06'),(481,227,'Grenadines',NULL,'2012-07-18 18:33:14'),(482,227,'Saint Andrew',NULL,'2012-07-18 18:33:18'),(483,227,'Saint David',NULL,'2012-07-18 18:33:24'),(484,227,'Saint George',NULL,'2012-07-18 18:33:28'),(485,227,'Saint Patrick',NULL,'2012-07-18 18:33:34'),(486,123,'Anse la Raye',NULL,'2012-07-18 18:33:52'),(487,123,'Castries',NULL,'2012-07-18 18:33:56'),(488,123,'Choiseul',NULL,'2012-07-18 18:34:00'),(489,123,'Dauphin',NULL,'2012-07-18 18:34:04'),(490,123,'Dennery',NULL,'2012-07-18 18:34:09'),(491,123,'Gros Islet',NULL,'2012-07-18 18:34:13'),(492,123,'Laborie',NULL,'2012-07-18 18:34:19'),(493,123,'Micoud',NULL,'2012-07-18 18:34:23'),(494,123,'Praslin',NULL,'2012-07-18 18:34:27'),(495,123,'Soufriere',NULL,'2012-07-18 18:34:33'),(496,123,'Vieux Fort',NULL,'2012-07-18 18:34:41'),(497,115,'Nevis',NULL,'2012-07-18 18:35:03'),(498,115,'Saint Kitts',NULL,'2012-07-18 18:35:07'),(499,179,'Alto Paraguay',NULL,'2012-07-18 18:37:47'),(500,179,'Amambay',NULL,'2012-07-18 18:37:53'),(501,179,'Asuncion',NULL,'2012-07-18 18:37:59'),(502,179,'Boqueron',NULL,'2012-07-18 18:38:08'),(503,179,'Caazapa',NULL,'2012-07-18 18:38:17'),(504,179,'Canindeyu',NULL,'2012-07-18 18:38:26'),(505,179,'Central',NULL,'2012-07-18 18:38:31'),(506,179,'Concepcion',NULL,'2012-07-18 18:38:38'),(507,179,'Misiones',NULL,'2012-07-18 18:38:45'),(508,179,'Neembucu',NULL,'2012-07-18 18:38:52'),(509,179,'Presidente Hayes',NULL,'2012-07-18 18:39:00'),(510,179,'San Pedro',NULL,'2012-07-18 18:39:06'),(511,179,'Distrito Capital',NULL,'2012-07-18 18:39:32'),(512,179,'Guaira',NULL,'2012-07-18 18:40:02'),(513,166,'Bocas del Toro',NULL,'2012-07-18 18:40:31'),(514,166,'Chiriqui',NULL,'2012-07-18 18:40:35'),(515,166,'Cocle',NULL,'2012-07-18 18:40:38'),(516,166,'Colon',NULL,'2012-07-18 18:40:42'),(517,166,'Darien',NULL,'2012-07-18 18:40:45'),(518,166,'Herrera',NULL,'2012-07-18 18:40:49'),(519,166,'Los Santos',NULL,'2012-07-18 18:40:53'),(520,166,'Veraguas',NULL,'2012-07-18 18:40:58'),(521,158,'Boaco',NULL,'2012-07-18 18:41:19'),(522,158,'Carazo',NULL,'2012-07-18 18:41:24'),(523,158,'Chinandega',NULL,'2012-07-18 18:41:30'),(524,158,'Chontales',NULL,'2012-07-18 18:41:33'),(525,158,'Esteli',NULL,'2012-07-18 18:41:37'),(526,158,'Granada',NULL,'2012-07-18 18:41:42'),(527,158,'Jinotega',NULL,'2012-07-18 18:41:47'),(528,158,'Leon',NULL,'2012-07-18 18:41:51'),(529,158,'Madriz',NULL,'2012-07-18 18:41:56'),(530,158,'Managua',NULL,'2012-07-18 18:42:00'),(531,158,'Masaya',NULL,'2012-07-18 18:42:05'),(532,158,'Matagalpa',NULL,'2012-07-18 18:42:09'),(533,158,'Nueva Segovia',NULL,'2012-07-18 18:42:17'),(534,158,'Region Autonoma del Atlantica Sur',NULL,'2012-07-18 18:42:29'),(535,158,'Region Autonoma del Atlantico Norte',NULL,'2012-07-18 18:43:00'),(536,158,'Rio San Juan',NULL,'2012-07-18 18:43:07'),(537,158,'Rivas',NULL,'2012-07-18 18:43:13'),(538,8,'Aruba',NULL,'2012-07-18 18:43:27'),(539,8,'Bonaire',NULL,'2012-07-18 18:43:31'),(540,8,'Curacao',NULL,'2012-07-18 18:43:35'),(541,8,'Saba',NULL,'2012-07-18 18:43:37'),(542,8,'Sint Eustatius',NULL,'2012-07-18 18:43:42'),(543,8,'Sint Maarten',NULL,'2012-07-18 18:43:50'),(544,143,'Fort-de-France',NULL,'2012-07-18 19:49:53'),(545,143,'Le Marin',NULL,'2012-07-18 19:49:58'),(546,143,'Saint-Pierre',NULL,'2012-07-18 19:50:05'),(547,107,'Clarendon',NULL,'2012-07-18 19:50:40'),(550,107,'Kingston',NULL,'2012-07-18 19:50:50'),(551,107,'Hanover',NULL,'2012-07-18 19:50:58'),(552,107,'Manchester',NULL,'2012-07-18 19:51:03'),(553,107,'Portland',NULL,'2012-07-18 19:51:06'),(554,107,'Saint Andrew',NULL,'2012-07-18 19:51:13'),(555,107,'Saint Ann',NULL,'2012-07-18 19:51:16'),(556,107,'Saint Catherine',NULL,'2012-07-18 19:51:21'),(557,107,'Saint Elizabeth',NULL,'2012-07-18 19:51:25'),(558,107,'Saint Mary',NULL,'2012-07-18 19:51:29'),(559,107,'Saint Thomas',NULL,'2012-07-18 19:51:32'),(560,107,'Trelawny',NULL,'2012-07-18 19:51:37'),(561,107,'Westmoreland',NULL,'2012-07-18 19:51:41'),(562,94,'Atlantida',NULL,'2012-07-18 19:52:30'),(563,94,'Choluteca',NULL,'2012-07-18 19:52:35'),(564,94,'Colon',NULL,'2012-07-18 19:52:40'),(565,94,'Copan',NULL,'2012-07-18 19:52:45'),(566,94,'Cortes',NULL,'2012-07-18 19:52:49'),(567,94,'El Paraiso',NULL,'2012-07-18 19:52:53'),(568,94,'Francisco Morazan',NULL,'2012-07-18 19:52:59'),(569,94,'Gracias a Dios',NULL,'2012-07-18 19:53:06'),(570,94,'Intibuca',NULL,'2012-07-18 19:53:10'),(571,94,'Islas de la Bahia',NULL,'2012-07-18 19:53:15'),(572,94,'La Paz',NULL,'2012-07-18 19:53:18'),(573,94,'Lempira',NULL,'2012-07-18 19:53:22'),(574,94,'Ocotepeque',NULL,'2012-07-18 19:53:25'),(575,94,'Olancho',NULL,'2012-07-18 19:53:29'),(576,94,'Santa Barbara',NULL,'2012-07-18 19:53:33'),(577,94,'Valle',NULL,'2012-07-18 19:53:36'),(578,94,'Yoro',NULL,'2012-07-18 19:53:44'),(579,96,'Artibonite',NULL,'2012-07-18 19:55:19'),(580,96,'Centre',NULL,'2012-07-18 19:55:22'),(581,96,'Grand\'Anse',NULL,'2012-07-18 19:55:26'),(582,96,'Nippes',NULL,'2012-07-18 19:55:28'),(583,96,'Nord',NULL,'2012-07-18 19:55:32'),(584,96,'Nord-Est',NULL,'2012-07-18 19:55:37'),(585,96,'Nord-Ouest',NULL,'2012-07-18 19:55:42'),(586,96,'Ouest',NULL,'2012-07-18 19:55:48'),(587,96,'Sud',NULL,'2012-07-18 19:55:52'),(588,96,'Sud-Est',NULL,'2012-07-18 19:55:56'),(589,91,'Barima-Waini',NULL,'2012-07-18 19:57:03'),(590,91,'Demerara-Mahaica',NULL,'2012-07-18 19:59:36'),(591,91,'East Berbice-Corentyne',NULL,'2012-07-18 19:59:48'),(592,91,'Essequibo Islands-West Demerara',NULL,'2012-07-18 19:59:59'),(593,91,'Mahaica-Berbice',NULL,'2012-07-18 20:00:07'),(594,91,'Pomeroon-Supenaam',NULL,'2012-07-18 20:00:17'),(595,91,'Potaro-Siparuni',NULL,'2012-07-18 20:00:23'),(596,91,'Upper Demerara-Berbice',NULL,'2012-07-18 20:00:31'),(597,88,'Alta Verapaz',NULL,'2012-07-18 20:01:35'),(598,88,'Baja Verapaz',NULL,'2012-07-18 20:01:39'),(599,88,'Chimaltenango',NULL,'2012-07-18 20:01:45'),(600,88,'El Progreso',NULL,'2012-07-18 20:01:54'),(601,88,'El Quiche',NULL,'2012-07-18 20:01:58'),(602,88,'Escuintla',NULL,'2012-07-18 20:02:02'),(603,88,'Guatemala',NULL,'2012-07-18 20:02:06'),(604,88,'Huehuetenango',NULL,'2012-07-18 20:02:11'),(605,88,'Izabal',NULL,'2012-07-18 20:02:14'),(606,88,'Jalapa',NULL,'2012-07-18 20:02:19'),(607,88,'Jutiapa',NULL,'2012-07-18 20:02:23'),(608,88,'Quetzaltenango',NULL,'2012-07-18 20:02:28'),(609,88,'Retalhuleu',NULL,'2012-07-18 20:02:37'),(610,88,'Sacatepequez',NULL,'2012-07-18 20:02:42'),(611,88,'San Marcos',NULL,'2012-07-18 20:02:46'),(612,88,'Santa Rosa',NULL,'2012-07-18 20:02:49'),(613,88,'Solola',NULL,'2012-07-18 20:02:52'),(614,88,'Suchitepequez',NULL,'2012-07-18 20:02:58'),(615,88,'Totonicapan',NULL,'2012-07-18 20:03:04'),(616,88,'Zacapa',NULL,'2012-07-18 20:03:08'),(617,76,'Saint Andrew',NULL,'2012-07-18 20:06:34'),(618,76,'Saint David',NULL,'2012-07-18 20:06:43'),(619,76,'Saint George',NULL,'2012-07-18 20:06:46'),(620,76,'Saint John',NULL,'2012-07-18 20:06:50'),(621,76,'Saint Mark',NULL,'2012-07-18 20:06:54'),(622,76,'Saint Patrick',NULL,'2012-07-18 20:07:01'),(623,78,'Cayenne',NULL,'2012-07-18 20:07:21'),(624,78,'Saint Laurent du Maroni',NULL,'2012-07-18 20:07:28'),(625,201,'Ahuachapan',NULL,'2012-07-18 20:07:54'),(626,201,'Cabanas',NULL,'2012-07-18 20:07:58'),(627,201,'Cuscatlan',NULL,'2012-07-18 20:08:45'),(628,201,'La Libertad',NULL,'2012-07-18 20:09:16'),(629,201,'La Paz',NULL,'2012-07-18 20:09:21'),(630,201,'La Union',NULL,'2012-07-18 20:09:25'),(631,201,'Morazan',NULL,'2012-07-18 20:09:31'),(632,201,'San Miguel',NULL,'2012-07-18 20:09:35'),(633,201,'San Salvador',NULL,'2012-07-18 20:09:57'),(634,201,'Santa Ana',NULL,'2012-07-18 20:10:07'),(635,201,'Sonsonate',NULL,'2012-07-18 20:10:13'),(636,201,'Usulutan',NULL,'2012-07-18 20:10:18'),(637,201,'San Vicente',NULL,'2012-07-18 20:10:49'),(638,61,'Azuay',NULL,'2012-07-18 20:11:12'),(639,61,'Bolivar',NULL,'2012-07-18 20:11:16'),(640,61,'Canar',NULL,'2012-07-18 20:11:19'),(641,61,'Carchi',NULL,'2012-07-18 20:11:23'),(642,61,'Cotopaxi',NULL,'2012-07-18 20:11:30'),(643,61,'El Oro',NULL,'2012-07-18 20:11:33'),(644,61,'Esmeraldas',NULL,'2012-07-18 20:11:38'),(645,61,'Galapagos',NULL,'2012-07-18 20:11:48'),(646,61,'Guayas',NULL,'2012-07-18 20:11:53'),(647,61,'Imbabura',NULL,'2012-07-18 20:11:57'),(648,61,'Loja',NULL,'2012-07-18 20:12:00'),(649,61,'Los Rios',NULL,'2012-07-18 20:12:06'),(650,61,'Manabi',NULL,'2012-07-18 20:12:09'),(651,61,'Morona Santiago',NULL,'2012-07-18 20:12:15'),(652,61,'Orellana',NULL,'2012-07-18 20:12:23'),(653,61,'Pastaza',NULL,'2012-07-18 20:12:26'),(654,61,'Santa Elena',NULL,'2012-07-18 20:12:32'),(655,61,'Santo Domingo de los Tsachilas',NULL,'2012-07-18 20:12:48'),(656,61,'Sucumbios',NULL,'2012-07-18 20:12:52'),(657,61,'Tungurahua',NULL,'2012-07-18 20:12:57'),(658,61,'Zamora Chinchipe',NULL,'2012-07-18 20:13:02'),(659,59,'Baoruco',NULL,'2012-07-18 20:14:11'),(660,59,'Barahona',NULL,'2012-07-18 20:14:14'),(661,59,'Dajabon',NULL,'2012-07-18 20:14:18'),(662,59,'Distrito Nacional',NULL,'2012-07-18 20:14:23'),(663,59,'Duarte',NULL,'2012-07-18 20:14:28'),(664,59,'El Seibo',NULL,'2012-07-18 20:14:33'),(665,59,'Espaillat',NULL,'2012-07-18 20:14:41'),(666,59,'Hato Mayor',NULL,'2012-07-18 20:14:45'),(667,59,'Hermanas Mirabel',NULL,'2012-07-18 20:14:50'),(668,59,'Independencia',NULL,'2012-07-18 20:14:55'),(669,59,'La Altagracia',NULL,'2012-07-18 20:15:02'),(670,59,'La Romana',NULL,'2012-07-18 20:15:06'),(674,59,'Maria Trinidad Sanchez',NULL,'2012-07-18 20:15:33'),(675,59,'Monsenor Nouel',NULL,'2012-07-18 20:15:39'),(676,59,'Monte Cristi',NULL,'2012-07-18 20:15:49'),(677,59,'Monte Plata',NULL,'2012-07-18 20:15:53'),(678,59,'Pedernales',NULL,'2012-07-18 20:15:57'),(679,59,'Peravia',NULL,'2012-07-18 20:16:00'),(680,59,'Puerto Plata',NULL,'2012-07-18 20:16:06'),(681,59,'Samana',NULL,'2012-07-18 20:16:10'),(682,59,'San Cristobal',NULL,'2012-07-18 20:16:14'),(683,59,'San Jose de Ocoa',NULL,'2012-07-18 20:16:20'),(684,59,'San Juan',NULL,'2012-07-18 20:16:23'),(685,59,'San Pedro de Macoris',NULL,'2012-07-18 20:16:28'),(686,59,'Sanchez Ramirez',NULL,'2012-07-18 20:16:33'),(687,59,'Santiago',NULL,'2012-07-18 20:16:36'),(688,59,'Santiago Rodriguez',NULL,'2012-07-18 20:16:41'),(689,59,'Santo Domingo',NULL,'2012-07-18 20:16:49'),(690,59,'Valverde',NULL,'2012-07-18 20:16:52'),(691,58,'Saint Andrew',NULL,'2012-07-18 20:18:12'),(692,58,'Saint David',NULL,'2012-07-18 20:18:18'),(693,58,'Saint George',NULL,'2012-07-18 20:18:21'),(694,58,'Saint John',NULL,'2012-07-18 20:18:25'),(695,58,'Saint Joseph',NULL,'2012-07-18 20:18:28'),(696,58,'Saint Luke',NULL,'2012-07-18 20:18:32'),(697,58,'Saint Mark',NULL,'2012-07-18 20:18:35'),(698,58,'Saint Patrick',NULL,'2012-07-18 20:18:40'),(699,58,'Saint Paul',NULL,'2012-07-18 20:18:44'),(700,58,'Saint Peter',NULL,'2012-07-18 20:18:48'),(701,50,'Artemisa',NULL,'2012-07-18 20:19:15'),(702,50,'Camaguey',NULL,'2012-07-18 20:19:19'),(703,50,'Ciego de Avila',NULL,'2012-07-18 20:19:23'),(704,50,'Cienfuegos',NULL,'2012-07-18 20:19:26'),(705,50,'Granma',NULL,'2012-07-18 20:19:31'),(706,50,'Guantanamo',NULL,'2012-07-18 20:19:37'),(707,50,'Holguin',NULL,'2012-07-18 20:19:41'),(708,50,'Isla de la Juventud',NULL,'2012-07-18 20:19:47'),(709,50,'La Habana',NULL,'2012-07-18 20:19:50'),(710,50,'Las Tunas',NULL,'2012-07-18 20:19:53'),(711,50,'Matanzas',NULL,'2012-07-18 20:19:57'),(712,50,'Mayabeque',NULL,'2012-07-18 20:20:01'),(713,50,'Pinar del Rio',NULL,'2012-07-18 20:20:05'),(714,50,'Sancti Spiritus',NULL,'2012-07-18 20:20:09'),(715,50,'Santiago de Cuba',NULL,'2012-07-18 20:20:13'),(716,50,'Villa Clara',NULL,'2012-07-18 20:20:17'),(717,263,'El Hierro',NULL,'2012-07-18 20:25:55'),(718,263,'Fuerteventura',NULL,'2012-07-18 20:26:01'),(719,263,'Gran Canaria',NULL,'2012-07-18 20:26:05'),(720,263,'La Gomera',NULL,'2012-07-18 20:26:11'),(721,263,'La Palma',NULL,'2012-07-18 20:26:15'),(722,263,'Lanzarote',NULL,'2012-07-18 20:26:19'),(723,263,'Rio Negro',NULL,'2012-07-18 20:26:23'),(724,263,'Tenerife',NULL,'2012-07-18 20:26:26'),(725,35,'Belize',NULL,'2012-07-18 20:28:33'),(726,35,'Corozal',NULL,'2012-07-18 20:28:42'),(727,35,'Orange Walk',NULL,'2012-07-18 20:28:45'),(728,35,'Stann Creek',NULL,'2012-07-18 20:28:48'),(729,35,'Toledo',NULL,'2012-07-18 20:28:52'),(730,18,'Christ Church',NULL,'2012-07-18 20:29:06'),(731,18,'Saint Andrew',NULL,'2012-07-18 20:29:11'),(732,18,'Saint George',NULL,'2012-07-18 20:29:15'),(733,18,'Saint James',NULL,'2012-07-18 20:29:18'),(734,18,'Saint John',NULL,'2012-07-18 20:29:21'),(735,18,'Saint Joseph',NULL,'2012-07-18 20:29:25'),(736,18,'Saint Lucy',NULL,'2012-07-18 20:29:28'),(737,18,'Saint Michael',NULL,'2012-07-18 20:29:31'),(738,18,'Saint Peter',NULL,'2012-07-18 20:29:34'),(739,18,'Saint Philip',NULL,'2012-07-18 20:29:39'),(740,18,'Saint Thomas',NULL,'2012-07-18 20:29:42'),(741,30,'Acklins',NULL,'2012-07-18 20:36:44'),(742,30,'Berry Islands',NULL,'2012-07-18 20:36:58'),(743,30,'Bimini',NULL,'2012-07-18 20:37:14'),(744,30,'Black Point',NULL,'2012-07-18 20:37:32'),(746,30,'Cat Island',NULL,'2012-07-18 20:37:46'),(747,30,'Central Abaco',NULL,'2012-07-18 20:38:59'),(748,30,'Central Andros',NULL,'2012-07-18 20:39:15'),(749,30,'Central Eleuthera',NULL,'2012-07-18 20:41:07'),(750,30,'City of Freeport',NULL,'2012-07-18 20:41:13'),(751,30,'Crooked Island',NULL,'2012-07-18 20:41:17'),(752,30,'East Grand Bahama',NULL,'2012-07-18 20:41:21'),(753,30,'Exuma',NULL,'2012-07-18 20:41:26'),(754,30,'Grand Cay',NULL,'2012-07-18 20:41:29'),(755,30,'Green Turtle Cay',NULL,'2012-07-18 20:41:36'),(756,30,'Harbour Island',NULL,'2012-07-18 20:41:47'),(757,30,'Hope Town',NULL,'2012-07-18 20:43:24'),(758,30,'Inagua',NULL,'2012-07-18 20:44:26'),(759,30,'Long Island',NULL,'2012-07-18 20:44:30'),(760,30,'Mangrove Cay',NULL,'2012-07-18 20:44:34'),(761,30,'Mayaguana',NULL,'2012-07-18 20:44:39'),(762,30,'Moore\'s Island',NULL,'2012-07-18 20:44:43'),(763,30,'North Abaco',NULL,'2012-07-18 20:44:48'),(764,30,'North Andros',NULL,'2012-07-18 20:44:53'),(765,30,'North Eleuthera',NULL,'2012-07-18 20:44:59'),(766,30,'Ragged Island',NULL,'2012-07-18 20:45:02'),(767,30,'Rum Cay',NULL,'2012-07-18 20:45:06'),(768,30,'San Salvador',NULL,'2012-07-18 20:45:11'),(769,30,'South Abaco',NULL,'2012-07-18 20:45:15'),(770,30,'South Andros',NULL,'2012-07-18 20:45:18'),(771,30,'South Eleuthera',NULL,'2012-07-18 20:45:24'),(772,30,'Spanish Wells',NULL,'2012-07-18 20:45:33'),(773,30,'West Grand Bahama',NULL,'2012-07-18 20:45:38'),(774,4,'Barbuda',NULL,'2012-07-18 20:48:14'),(775,4,'Redonda',NULL,'2012-07-18 20:48:18'),(776,4,'Saint John',NULL,'2012-07-18 20:48:22'),(777,4,'Saint Mary',NULL,'2012-07-18 20:48:25'),(778,4,'Saint Paul',NULL,'2012-07-18 20:48:30'),(779,4,'Saint Peter',NULL,'2012-07-18 20:48:35'),(780,4,'Saint Philip',NULL,'2012-07-18 20:48:39'),(781,228,'Distrito Capital',NULL,'2012-07-18 21:56:17'),(782,100,'North',NULL,'2012-10-24 23:42:34'),(783,100,'Haifa',NULL,'2012-10-24 23:42:46'),(784,100,'Center',NULL,'2012-10-24 23:42:57'),(785,100,'Tel Aviv',NULL,'2012-10-24 23:43:04'),(786,100,'Jerusalem',NULL,'2012-10-24 23:43:15'),(787,100,'South',NULL,'2012-10-24 23:43:21'),(788,100,'Judea and Samaria',NULL,'2012-10-24 23:43:37'),(789,63,'Alexandria',NULL,'2012-10-24 23:44:42'),(790,63,'Aswan',NULL,'2012-10-24 23:44:47'),(791,63,'Asyut',NULL,'2012-10-24 23:44:51'),(792,63,'Beheira',NULL,'2012-10-24 23:44:58'),(793,63,'Beni Suef',NULL,'2012-10-24 23:45:04'),(794,63,'Cairo',NULL,'2012-10-24 23:45:19'),(795,63,'Dakahlia',NULL,'2012-10-24 23:46:05'),(796,63,'Damietta',NULL,'2012-10-24 23:46:12'),(797,63,'Faiyum',NULL,'2012-10-24 23:46:17'),(798,63,'Gharbia',NULL,'2012-10-24 23:46:23'),(799,63,'Giza',NULL,'2012-10-24 23:46:27'),(800,63,'Ismailia',NULL,'2012-10-24 23:46:32'),(801,63,'Kafr el-Sheikh',NULL,'2012-10-24 23:46:38'),(802,63,'Matruh',NULL,'2012-10-24 23:46:43'),(803,63,'Minya',NULL,'2012-10-24 23:46:48'),(804,63,'Monufia',NULL,'2012-10-24 23:46:55'),(805,63,'New Valley',NULL,'2012-10-24 23:47:02'),(806,63,'North Sinai',NULL,'2012-10-24 23:47:07'),(807,63,'Port Said',NULL,'2012-10-24 23:47:13'),(808,63,'Qalyubia',NULL,'2012-10-24 23:47:21'),(809,63,'Qena',NULL,'2012-10-24 23:47:26'),(810,63,'Red Sea',NULL,'2012-10-24 23:47:31'),(811,63,'Al Sharqia',NULL,'2012-10-24 23:47:40'),(812,63,'Sohag',NULL,'2012-10-24 23:47:45'),(813,63,'South Sinai',NULL,'2012-10-24 23:47:49'),(814,63,'Suez',NULL,'2012-10-24 23:47:54'),(815,63,'Luxor',NULL,'2012-10-24 23:47:59'),(816,36,'Quebec',NULL,'2012-10-24 23:48:58'),(817,36,'Nova Scotia',NULL,'2012-10-24 23:49:08'),(818,36,'New Brunswick',NULL,'2012-10-24 23:49:16'),(819,36,'Prince Edward Island',NULL,'2012-10-24 23:49:30'),(820,36,'Saskatchewan',NULL,'2012-10-24 23:49:42'),(821,208,'Bangkok',NULL,'2012-10-24 23:53:20'),(822,208,'Amnat Charoen',NULL,'2012-10-24 23:53:32'),(823,208,'Ang Thong',NULL,'2012-10-24 23:53:37'),(824,208,'Bueng Kan',NULL,'2012-10-24 23:53:44'),(825,208,'Buriram',NULL,'2012-10-24 23:53:50'),(826,208,'Chachoengsao',NULL,'2012-10-24 23:54:01'),(827,208,'Chainat',NULL,'2012-10-24 23:54:06'),(828,208,'Chaiyaphum',NULL,'2012-10-24 23:54:15'),(829,208,'Chanthaburi',NULL,'2012-10-24 23:54:22'),(830,208,'Chiang Mai',NULL,'2012-10-24 23:54:31'),(831,208,'Chiang Rai',NULL,'2012-10-24 23:54:37'),(832,208,'Chonburi',NULL,'2012-10-24 23:54:41'),(833,208,'Chumphon',NULL,'2012-10-24 23:55:02'),(834,208,'Kalasin',NULL,'2012-10-24 23:55:13'),(835,208,'Kamphaeng',NULL,'2012-10-24 23:55:19'),(836,208,'Kanchanaburi',NULL,'2012-10-24 23:55:30'),(837,208,'Khon Kaen',NULL,'2012-10-24 23:55:35'),(838,208,'Krabi',NULL,'2012-10-24 23:56:51'),(839,208,'Lampang',NULL,'2012-10-24 23:56:56'),(840,208,'Lamphun',NULL,'2012-10-24 23:57:02'),(841,208,'Loei',NULL,'2012-10-24 23:57:06'),(842,208,'Lopburi',NULL,'2012-10-24 23:57:11'),(843,208,'Mae Hong Son',NULL,'2012-10-24 23:57:17'),(844,208,'Maha Sarakham',NULL,'2012-10-24 23:57:34'),(845,208,'Mukdahan',NULL,'2012-10-24 23:57:41'),(846,208,'Nakhon Nayok',NULL,'2012-10-24 23:57:48'),(847,208,'Nakhon Pathom',NULL,'2012-10-24 23:58:05'),(848,208,'Nakhon Phanom',NULL,'2012-10-24 23:58:12'),(849,208,'Nakhon Ratchasima',NULL,'2012-10-24 23:58:21'),(850,208,'Nakhon Sawan',NULL,'2012-10-24 23:58:35'),(851,208,'Nakhon Si Thammarat',NULL,'2012-10-24 23:58:44'),(852,208,'Nan',NULL,'2012-10-24 23:58:48'),(853,208,'Narathiwat',NULL,'2012-10-24 23:58:53'),(854,208,'Nong Bua Lamphu',NULL,'2012-10-24 23:59:01'),(855,208,'Nong Khai',NULL,'2012-10-24 23:59:06'),(856,208,'Nonthaburi',NULL,'2012-10-24 23:59:26'),(857,208,'Pathum Thani',NULL,'2012-10-24 23:59:32'),(858,208,'Pattani',NULL,'2012-10-24 23:59:37'),(859,208,'Phang Nga',NULL,'2012-10-24 23:59:52'),(860,208,'Phatthalung',NULL,'2012-10-25 00:00:00'),(861,208,'Phayao',NULL,'2012-10-25 00:00:06'),(862,208,'Phetchabun',NULL,'2012-10-25 00:00:13'),(863,208,'Phetchaburi',NULL,'2012-10-25 00:00:19'),(864,208,'Phichit',NULL,'2012-10-25 00:00:24'),(865,208,'Phitsanulok',NULL,'2012-10-25 00:00:30'),(866,208,'Phra Nakhon Si Ayutthaya',NULL,'2012-10-25 00:00:42'),(867,208,'Phrae',NULL,'2012-10-25 00:00:47'),(868,208,'Phuket',NULL,'2012-10-25 00:00:51'),(869,208,'Prachinburi',NULL,'2012-10-25 00:00:57'),(870,208,'Prachuap Khiri Khan',NULL,'2012-10-25 00:01:07'),(871,208,'Ranong',NULL,'2012-10-25 00:01:12'),(872,208,'Ratchaburi',NULL,'2012-10-25 00:01:17'),(873,208,'Rayong',NULL,'2012-10-25 00:01:29'),(874,208,'Roi Et',NULL,'2012-10-25 00:01:34'),(875,208,'Sa Kaeo',NULL,'2012-10-25 00:01:40'),(876,208,'Sakon Nakhon',NULL,'2012-10-25 00:01:46'),(877,208,'Samut Prakan',NULL,'2012-10-25 00:01:51'),(878,208,'Samut Sakhon',NULL,'2012-10-25 00:02:01'),(879,208,'Samut Songkhram',NULL,'2012-10-25 00:02:08'),(880,208,'Saraburi',NULL,'2012-10-25 00:02:13'),(881,208,'Satun',NULL,'2012-10-25 00:02:17'),(882,208,'Sing Buri',NULL,'2012-10-25 00:02:22'),(883,208,'Sisaket',NULL,'2012-10-25 00:02:25'),(884,208,'Songkhla',NULL,'2012-10-25 00:02:39'),(885,208,'Sukhothai',NULL,'2012-10-25 00:02:44'),(886,208,'Suphan Buri',NULL,'2012-10-25 00:02:50'),(887,208,'Surat Thani',NULL,'2012-10-25 00:02:55'),(888,208,'Surin',NULL,'2012-10-25 00:02:59'),(889,208,'Tak',NULL,'2012-10-25 00:03:05'),(890,208,'Trang',NULL,'2012-10-25 00:03:08'),(891,208,'Trat',NULL,'2012-10-25 00:03:12'),(892,208,'Ubon Ratchathani',NULL,'2012-10-25 00:03:20'),(893,208,'Udon Thani',NULL,'2012-10-25 00:03:26'),(894,208,'Uthai Thani',NULL,'2012-10-25 00:03:33'),(895,208,'Uttaradit',NULL,'2012-10-25 00:03:45'),(896,208,'Yala',NULL,'2012-10-25 00:03:50'),(897,208,'Yasothon',NULL,'2012-10-25 00:04:47'),(898,161,'Bagmati',NULL,'2012-10-25 00:05:03'),(899,161,'Bheri',NULL,'2012-10-25 00:05:10'),(900,161,'Gandaki',NULL,'2012-10-25 00:05:27'),(901,161,'Janakpur',NULL,'2012-10-25 00:05:33'),(902,161,'Karnali',NULL,'2012-10-25 00:05:40'),(903,161,'Koshi',NULL,'2012-10-25 00:05:44'),(904,161,'Lumbini',NULL,'2012-10-25 00:05:50'),(905,161,'Mahakali',NULL,'2012-10-25 00:05:56'),(906,161,'Mechi',NULL,'2012-10-25 00:06:00'),(907,161,'Narayani',NULL,'2012-10-25 00:06:05'),(908,161,'Rapti',NULL,'2012-10-25 00:06:10'),(910,161,'Seti',NULL,'2012-10-25 00:07:04'),(911,66,'Andalusia',NULL,'2012-10-25 00:11:25'),(912,66,'Aragon',NULL,'2012-10-25 00:11:28'),(913,66,'Asturias',NULL,'2012-10-25 00:11:32'),(914,66,'Balearic Islands',NULL,'2012-10-25 00:11:38'),(915,66,'Basque Country',NULL,'2012-10-25 00:11:44'),(916,66,'Canary Islands',NULL,'2012-10-25 00:11:50'),(917,66,'Cantabria',NULL,'2012-10-25 00:11:54'),(918,66,'Castile and Leon',NULL,'2012-10-25 00:12:03'),(919,66,'Catalonia',NULL,'2012-10-25 00:12:07'),(920,66,'Community of Madrid',NULL,'2012-10-25 00:12:14'),(921,66,'Extremadura',NULL,'2012-10-25 00:12:19'),(922,66,'Galicia',NULL,'2012-10-25 00:12:23'),(923,66,'La Rioja',NULL,'2012-10-25 00:12:27'),(924,66,'Murcia',NULL,'2012-10-25 00:12:31'),(925,66,'Navarre',NULL,'2012-10-25 00:12:36'),(926,66,'Valencian Community',NULL,'2012-10-25 00:12:46'),(927,14,'New South Wales',NULL,'2012-10-25 20:45:34'),(928,14,'Queensland',NULL,'2012-10-25 20:45:40'),(929,14,'South Australia',NULL,'2012-10-25 20:45:45'),(930,14,'Tasmania',NULL,'2012-10-25 20:45:51'),(931,14,'Victoria',NULL,'2012-10-25 20:45:55'),(932,14,'Western Australia',NULL,'2012-10-25 20:46:16'),(933,14,'Australian Capital Territory',NULL,'2012-10-25 20:46:25'),(934,14,'Northern Territory',NULL,'2012-10-25 20:46:41'),(935,199,'Brokopondo',NULL,'2012-11-05 23:24:16'),(936,199,'Commewijne',NULL,'2012-11-05 23:24:24'),(937,199,'Coronie',NULL,'2012-11-05 23:24:28'),(938,199,'Marowijne',NULL,'2012-11-05 23:24:33'),(939,199,'Nickerie',NULL,'2012-11-05 23:24:37'),(940,199,'Para',NULL,'2012-11-05 23:24:44'),(941,199,'Paramaribo',NULL,'2012-11-05 23:24:49'),(942,199,'Saramacca',NULL,'2012-11-05 23:24:54'),(943,199,'Sipaliwini',NULL,'2012-11-05 23:24:58'),(944,199,'Wanica',NULL,'2012-11-05 23:25:02'),(945,36,'Nunavut',NULL,'2013-12-09 00:51:38'),(946,36,'Northwest Territories',NULL,'2017-02-22 15:52:55'),(947,36,'Yukon',NULL,'2017-02-22 15:52:55'),(948,55,'Baden-WÃ¼rttemberg',NULL,'2017-02-22 16:07:42'),(949,55,'Bavaria',NULL,'2017-02-22 16:07:49'),(950,55,'Freistaat Bayern',NULL,'2017-02-22 16:07:49'),(951,55,'Berlin',NULL,'2017-02-22 16:07:50'),(952,55,'Brandenburg',NULL,'2017-02-22 16:07:50'),(953,55,'Bremen',NULL,'2017-02-22 16:07:50'),(954,55,'Freie Hansestadt Bremen',NULL,'2017-02-22 16:07:50'),(955,55,'Hamburg',NULL,'2017-02-22 16:07:51'),(956,55,'Freie und Hansestadt Hamburg',NULL,'2017-02-22 16:07:51'),(957,55,'Hesse',NULL,'2017-02-22 16:07:51'),(958,55,'Hessen',NULL,'2017-02-22 16:07:51'),(959,55,'Lower Saxony',NULL,'2017-02-22 16:07:52'),(960,55,'Niedersachsen',NULL,'2017-02-22 16:07:52'),(961,55,'Mecklenburg-Vorpommern',NULL,'2017-02-22 16:07:52'),(962,55,'North Rhine-Westphalia',NULL,'2017-02-22 16:07:52'),(963,55,'Rhineland-Palatinate',NULL,'2017-02-22 16:07:53'),(964,55,'Rheinland-Pfalz',NULL,'2017-02-22 16:07:53'),(965,55,'Saarland',NULL,'2017-02-22 16:07:53'),(966,55,'Freistaat Sachsen',NULL,'2017-02-22 16:07:53'),(967,55,'Saxony-Anhalt',NULL,'2017-02-22 16:07:54'),(968,55,'Sachsen-Anhalt',NULL,'2017-02-22 16:07:54'),(969,55,'Schleswig-Holstein',NULL,'2017-02-22 16:07:54'),(970,55,'Thuringia',NULL,'2017-02-22 16:07:54'),(971,55,'Freistaat ThÃ¼ringen',NULL,'2017-02-22 16:07:54'),(972,101,'Assam',NULL,'2017-02-22 16:10:09'),(973,101,'Kerala',NULL,'2017-02-22 16:10:09'),(974,66,'Huelva',NULL,'2017-02-22 16:10:09'),(975,66,'Malaga',NULL,'2017-02-22 16:10:10');

INSERT INTO `lkupcounty` VALUES (1,164,'Suffolk','2011-06-01 01:06:40'),(2,173,'Adjuntas','2011-06-01 01:06:40'),(3,173,'Aguada','2011-06-01 01:06:40'),(4,173,'Aguadilla','2011-06-01 01:06:40'),(5,155,'Mower','2011-06-01 01:06:40'),(6,172,'Susquehanna','2011-06-01 01:06:40'),(7,158,'Glacier','2011-06-01 01:06:40'),(8,179,'Garfield','2011-06-01 01:06:40'),(9,173,'Maricao','2011-06-01 01:06:40'),(10,173,'Anasco','2011-06-01 01:06:40'),(11,173,'Utuado','2011-06-01 01:06:40'),(12,173,'Arecibo','2011-06-01 01:06:40'),(13,173,'Barceloneta','2011-06-01 01:06:40'),(14,173,'Cabo Rojo','2011-06-01 01:06:40'),(15,173,'Penuelas','2011-06-01 01:06:40'),(16,173,'Camuy','2011-06-01 01:06:40'),(17,173,'Lares','2011-06-01 01:06:40'),(18,173,'San German','2011-06-01 01:06:40'),(19,173,'Sabana Grande','2011-06-01 01:06:40'),(20,173,'Ciales','2011-06-01 01:06:40'),(21,173,'Dorado','2011-06-01 01:06:40'),(22,173,'Guanica','2011-06-01 01:06:40'),(23,173,'Florida','2011-06-01 01:06:40'),(24,173,'Guayanilla','2011-06-01 01:06:40'),(25,173,'Hatillo','2011-06-01 01:06:40'),(26,173,'Hormigueros','2011-06-01 01:06:40'),(27,173,'Isabela','2011-06-01 01:06:40'),(28,173,'Jayuya','2011-06-01 01:06:40'),(29,173,'Lajas','2011-06-01 01:06:40'),(30,173,'Las Marias','2011-06-01 01:06:40'),(31,173,'Manati','2011-06-01 01:06:40'),(32,173,'Moca','2011-06-01 01:06:40'),(33,173,'Rincon','2011-06-01 01:06:40'),(34,173,'Quebradillas','2011-06-01 01:06:40'),(35,173,'Mayaguez','2011-06-01 01:06:40'),(36,173,'San Sebastian','2011-06-01 01:06:40'),(37,173,'Morovis','2011-06-01 01:06:40'),(38,173,'Vega Alta','2011-06-01 01:06:40'),(39,173,'Vega Baja','2011-06-01 01:06:40'),(40,173,'Yauco','2011-06-01 01:06:40'),(41,173,'Aguas Buenas','2011-06-01 01:06:40'),(42,173,'Guayama','2011-06-01 01:06:40'),(43,173,'Aibonito','2011-06-01 01:06:40'),(44,173,'Maunabo','2011-06-01 01:06:40'),(45,173,'Arroyo','2011-06-01 01:06:40'),(46,173,'Ponce','2011-06-01 01:06:40'),(47,173,'Naguabo','2011-06-01 01:06:40'),(48,173,'Naranjito','2011-06-01 01:06:40'),(49,173,'Orocovis','2011-06-01 01:06:40'),(50,173,'Rio Grande','2011-06-01 01:06:40'),(51,173,'Patillas','2011-06-01 01:06:40'),(52,173,'Caguas','2011-06-01 01:06:40'),(53,173,'Canovanas','2011-06-01 01:06:40'),(54,173,'Ceiba','2011-06-01 01:06:40'),(55,173,'Cayey','2011-06-01 01:06:40'),(56,173,'Fajardo','2011-06-01 01:06:40'),(57,173,'Cidra','2011-06-01 01:06:40'),(58,173,'Humacao','2011-06-01 01:06:40'),(59,173,'Salinas','2011-06-01 01:06:40'),(60,173,'San Lorenzo','2011-06-01 01:06:40'),(61,173,'Santa Isabel','2011-06-01 01:06:40'),(62,173,'Vieques','2011-06-01 01:06:40'),(63,173,'Villalba','2011-06-01 01:06:40'),(64,173,'Yabucoa','2011-06-01 01:06:40'),(65,173,'Coamo','2011-06-01 01:06:40'),(66,173,'Las Piedras','2011-06-01 01:06:40'),(67,173,'Loiza','2011-06-01 01:06:40'),(68,173,'Luquillo','2011-06-01 01:06:40'),(69,173,'Culebra','2011-06-01 01:06:40'),(70,173,'Juncos','2011-06-01 01:06:40'),(71,173,'Gurabo','2011-06-01 01:06:40'),(72,173,'Comerio','2011-06-01 01:06:40'),(73,173,'Corozal','2011-06-01 01:06:40'),(74,173,'Barranquitas','2011-06-01 01:06:40'),(75,173,'Juana Diaz','2011-06-01 01:06:40'),(76,181,'Saint Thomas','2011-06-01 01:06:40'),(77,181,'Saint Croix','2011-06-01 01:06:40'),(78,181,'Saint John','2011-06-01 01:06:40'),(79,173,'San Juan','2011-06-01 01:06:40'),(80,173,'Bayamon','2011-06-01 01:06:40'),(81,173,'Toa Baja','2011-06-01 01:06:40'),(82,173,'Toa Alta','2011-06-01 01:06:40'),(83,173,'Catano','2011-06-01 01:06:40'),(84,173,'Guaynabo','2011-06-01 01:06:40'),(85,173,'Trujillo Alto','2011-06-01 01:06:40'),(86,173,'Carolina','2011-06-01 01:06:40'),(87,153,'Hampden','2011-06-01 01:06:40'),(88,153,'Hampshire','2011-06-01 01:06:40'),(89,153,'Worcester','2011-06-01 01:06:40'),(90,153,'Berkshire','2011-06-01 01:06:40'),(91,153,'Franklin','2011-06-01 01:06:40'),(92,153,'Middlesex','2011-06-01 01:06:40'),(93,153,'Essex','2011-06-01 01:06:40'),(94,153,'Plymouth','2011-06-01 01:06:40'),(95,153,'Norfolk','2011-06-01 01:06:40'),(96,153,'Bristol','2011-06-01 01:06:40'),(97,153,'Suffolk','2011-06-01 01:06:40'),(98,153,'Barnstable','2011-06-01 01:06:40'),(99,153,'Dukes','2011-06-01 01:06:40'),(100,153,'Nantucket','2011-06-01 01:06:40'),(101,174,'Newport','2011-06-01 01:06:40'),(102,174,'Providence','2011-06-01 01:06:40'),(103,174,'Washington','2011-06-01 01:06:40'),(104,174,'Bristol','2011-06-01 01:06:40'),(105,174,'Kent','2011-06-01 01:06:40'),(106,161,'Hillsborough','2011-06-01 01:06:40'),(107,161,'Rockingham','2011-06-01 01:06:40'),(108,161,'Merrimack','2011-06-01 01:06:40'),(109,161,'Grafton','2011-06-01 01:06:40'),(110,161,'Belknap','2011-06-01 01:06:40'),(111,161,'Carroll','2011-06-01 01:06:40'),(112,161,'Sullivan','2011-06-01 01:06:40'),(113,161,'Cheshire','2011-06-01 01:06:40'),(114,161,'Coos','2011-06-01 01:06:40'),(115,161,'Strafford','2011-06-01 01:06:40'),(116,150,'York','2011-06-01 01:06:40'),(117,150,'Cumberland','2011-06-01 01:06:40'),(118,150,'Sagadahoc','2011-06-01 01:06:40'),(119,150,'Oxford','2011-06-01 01:06:40'),(120,150,'Androscoggin','2011-06-01 01:06:40'),(121,150,'Franklin','2011-06-01 01:06:40'),(122,150,'Kennebec','2011-06-01 01:06:40'),(123,150,'Lincoln','2011-06-01 01:06:40'),(124,150,'Waldo','2011-06-01 01:06:40'),(125,150,'Penobscot','2011-06-01 01:06:40'),(126,150,'Piscataquis','2011-06-01 01:06:40'),(127,150,'Hancock','2011-06-01 01:06:40'),(128,150,'Washington','2011-06-01 01:06:40'),(129,150,'Aroostook','2011-06-01 01:06:40'),(130,150,'Somerset','2011-06-01 01:06:40'),(132,150,'Knox','2011-06-01 01:06:40'),(133,180,'Windsor','2011-06-01 01:06:40'),(134,180,'Orange','2011-06-01 01:06:40'),(135,180,'Caledonia','2011-06-01 01:06:40'),(136,180,'Windham','2011-06-01 01:06:40'),(137,180,'Bennington','2011-06-01 01:06:40'),(138,180,'Chittenden','2011-06-01 01:06:40'),(139,180,'Grand Isle','2011-06-01 01:06:40'),(140,180,'Franklin','2011-06-01 01:06:40'),(141,180,'Lamoille','2011-06-01 01:06:40'),(142,180,'Addison','2011-06-01 01:06:40'),(143,180,'Washington','2011-06-01 01:06:40'),(144,180,'Rutland','2011-06-01 01:06:40'),(145,180,'Orleans','2011-06-01 01:06:40'),(146,180,'Essex','2011-06-01 01:06:40'),(147,135,'Hartford','2011-06-01 01:06:40'),(148,135,'Litchfield','2011-06-01 01:06:40'),(149,135,'Tolland','2011-06-01 01:06:40'),(150,135,'Windham','2011-06-01 01:06:40'),(151,135,'New London','2011-06-01 01:06:40'),(152,135,'New Haven','2011-06-01 01:06:40'),(153,135,'Fairfield','2011-06-01 01:06:40'),(154,135,'Middlesex','2011-06-01 01:06:40'),(155,162,'Middlesex','2011-06-01 01:06:40'),(156,162,'Hudson','2011-06-01 01:06:40'),(157,162,'Essex','2011-06-01 01:06:40'),(158,162,'Morris','2011-06-01 01:06:40'),(159,162,'Bergen','2011-06-01 01:06:40'),(160,162,'Passaic','2011-06-01 01:06:40'),(161,162,'Union','2011-06-01 01:06:40'),(162,162,'Somerset','2011-06-01 01:06:40'),(163,162,'Sussex','2011-06-01 01:06:40'),(164,162,'Monmouth','2011-06-01 01:06:40'),(165,162,'Warren','2011-06-01 01:06:40'),(166,162,'Hunterdon','2011-06-01 01:06:40'),(167,162,'Salem','2011-06-01 01:06:40'),(168,162,'Camden','2011-06-01 01:06:40'),(169,162,'Ocean','2011-06-01 01:06:40'),(170,162,'Burlington','2011-06-01 01:06:40'),(171,162,'Gloucester','2011-06-01 01:06:40'),(172,162,'Atlantic','2011-06-01 01:06:40'),(173,162,'Cape May','2011-06-01 01:06:40'),(174,162,'Cumberland','2011-06-01 01:06:40'),(175,162,'Mercer','2011-06-01 01:06:40'),(176,164,'New York','2011-06-01 01:06:40'),(177,164,'Richmond','2011-06-01 01:06:40'),(178,164,'Bronx','2011-06-01 01:06:40'),(179,164,'Westchester','2011-06-01 01:06:40'),(180,164,'Putnam','2011-06-01 01:06:40'),(181,164,'Rockland','2011-06-01 01:06:40'),(182,164,'Orange','2011-06-01 01:06:40'),(183,164,'Nassau','2011-06-01 01:06:40'),(184,164,'Queens','2011-06-01 01:06:40'),(185,164,'Kings','2011-06-01 01:06:40'),(186,164,'Albany','2011-06-01 01:06:40'),(187,164,'Schenectady','2011-06-01 01:06:40'),(188,164,'Montgomery','2011-06-01 01:06:40'),(189,164,'Greene','2011-06-01 01:06:40'),(190,164,'Columbia','2011-06-01 01:06:40'),(191,164,'Rensselaer','2011-06-01 01:06:40'),(192,164,'Saratoga','2011-06-01 01:06:40'),(193,164,'Fulton','2011-06-01 01:06:40'),(194,164,'Schoharie','2011-06-01 01:06:40'),(195,164,'Washington','2011-06-01 01:06:40'),(196,164,'Otsego','2011-06-01 01:06:40'),(197,164,'Hamilton','2012-05-25 07:35:33'),(198,164,'Delaware','2011-06-01 01:06:40'),(199,164,'Ulster','2011-06-01 01:06:40'),(200,164,'Dutchess','2011-06-01 01:06:40'),(201,164,'Sullivan','2011-06-01 01:06:40'),(202,164,'Warren','2011-06-01 01:06:40'),(203,164,'Essex','2011-06-01 01:06:40'),(204,164,'Clinton','2011-06-01 01:06:40'),(205,164,'Franklin','2011-06-01 01:06:40'),(206,164,'Saint Lawrence','2011-06-01 01:06:40'),(207,164,'Onondaga','2011-06-01 01:06:40'),(208,164,'Cayuga','2011-06-01 01:06:40'),(209,164,'Oswego','2011-06-01 01:06:40'),(210,164,'Madison','2011-06-01 01:06:40'),(211,164,'Cortland','2012-05-25 07:35:58'),(212,164,'Tompkins','2011-06-01 01:06:40'),(213,164,'Oneida','2011-06-01 01:06:40'),(214,164,'Seneca','2011-06-01 01:06:40'),(215,164,'Chenango','2011-06-01 01:06:40'),(216,164,'Wayne','2011-06-01 01:06:40'),(217,164,'Lewis','2011-06-01 01:06:40'),(218,164,'Herkimer','2011-06-01 01:06:40'),(219,164,'Jefferson','2011-06-01 01:06:40'),(220,164,'Tioga','2011-06-01 01:06:40'),(221,164,'Broome','2011-06-01 01:06:40'),(222,164,'Erie','2011-06-01 01:06:40'),(223,164,'Genesee','2011-06-01 01:06:40'),(224,164,'Niagara','2011-06-01 01:06:40'),(225,164,'Wyoming','2011-06-01 01:06:40'),(226,164,'Allegany','2011-06-01 01:06:40'),(227,164,'Cattaraugus','2011-06-01 01:06:40'),(228,164,'Chautauqua','2011-06-01 01:06:40'),(229,164,'Orleans','2011-06-01 01:06:40'),(230,164,'Monroe','2011-06-01 01:06:40'),(231,164,'Livingston','2011-06-01 01:06:40'),(232,164,'Yates','2011-06-01 01:06:40'),(233,164,'Ontario','2011-06-01 01:06:40'),(234,164,'Steuben','2011-06-01 01:06:40'),(235,164,'Schuyler','2011-06-01 01:06:40'),(236,164,'Chemung','2011-06-01 01:06:40'),(237,172,'Beaver','2011-06-01 01:06:40'),(238,172,'Washington','2011-06-01 01:06:40'),(239,172,'Allegheny','2011-06-01 01:06:40'),(240,172,'Fayette','2011-06-01 01:06:40'),(241,172,'Westmoreland','2011-06-01 01:06:40'),(242,172,'Greene','2011-06-01 01:06:40'),(243,172,'Somerset','2011-06-01 01:06:40'),(244,172,'Bedford','2011-06-01 01:06:40'),(245,172,'Fulton','2011-06-01 01:06:40'),(246,172,'Armstrong','2011-06-01 01:06:40'),(247,172,'Indiana','2011-06-01 01:06:40'),(248,172,'Jefferson','2011-06-01 01:06:40'),(249,172,'Cambria','2011-06-01 01:06:40'),(250,172,'Clearfield','2011-06-01 01:06:40'),(251,172,'Elk','2011-06-01 01:06:40'),(252,172,'Forest','2011-06-01 01:06:40'),(253,172,'Cameron','2011-06-01 01:06:40'),(254,172,'Butler','2011-06-01 01:06:40'),(255,172,'Clarion','2011-06-01 01:06:40'),(256,172,'Lawrence','2011-06-01 01:06:40'),(257,172,'Crawford','2011-06-01 01:06:40'),(258,172,'Mercer','2011-06-01 01:06:40'),(259,172,'Venango','2011-06-01 01:06:40'),(260,172,'Warren','2011-06-01 01:06:40'),(261,172,'McKean','2011-06-01 01:06:40'),(262,172,'Erie','2011-06-01 01:06:40'),(263,172,'Blair','2011-06-01 01:06:40'),(264,172,'Huntingdon','2011-06-01 01:06:40'),(265,172,'Centre','2011-06-01 01:06:40'),(266,172,'Potter','2011-06-01 01:06:40'),(267,172,'Clinton','2011-06-01 01:06:40'),(268,172,'Tioga','2011-06-01 01:06:40'),(269,172,'Bradford','2011-06-01 01:06:40'),(270,172,'Cumberland','2011-06-01 01:06:40'),(271,172,'Mifflin','2011-06-01 01:06:40'),(272,172,'Lebanon','2011-06-01 01:06:40'),(273,172,'Dauphin','2011-06-01 01:06:40'),(274,172,'Perry','2011-06-01 01:06:40'),(275,172,'Juniata','2011-06-01 01:06:40'),(276,172,'Northumberland','2011-06-01 01:06:40'),(277,172,'York','2011-06-01 01:06:40'),(278,172,'Lancaster','2011-06-01 01:06:40'),(279,172,'Franklin','2011-06-01 01:06:40'),(280,172,'Adams','2011-06-01 01:06:40'),(281,172,'Lycoming','2011-06-01 01:06:40'),(282,172,'Sullivan','2011-06-01 01:06:40'),(283,172,'Union','2011-06-01 01:06:40'),(284,172,'Snyder','2011-06-01 01:06:40'),(285,172,'Columbia','2011-06-01 01:06:40'),(286,172,'Montour','2011-06-01 01:06:40'),(287,172,'Schuylkill','2011-06-01 01:06:40'),(288,172,'Northampton','2011-06-01 01:06:40'),(289,172,'Lehigh','2011-06-01 01:06:40'),(290,172,'Carbon','2011-06-01 01:06:40'),(291,172,'Bucks','2011-06-01 01:06:40'),(292,172,'Montgomery','2011-06-01 01:06:40'),(293,172,'Berks','2011-06-01 01:06:40'),(294,172,'Monroe','2011-06-01 01:06:40'),(295,172,'Luzerne','2011-06-01 01:06:40'),(296,172,'Pike','2011-06-01 01:06:40'),(297,172,'Lackawanna','2011-06-01 01:06:40'),(298,172,'Wayne','2011-06-01 01:06:40'),(299,172,'Wyoming','2011-06-01 01:06:40'),(300,172,'Delaware','2011-06-01 01:06:40'),(301,172,'Philadelphia','2011-06-01 01:06:40'),(302,172,'Chester','2011-06-01 01:06:40'),(303,136,'New Castle','2011-06-01 01:06:40'),(304,136,'Kent','2011-06-01 01:06:40'),(305,136,'Sussex','2011-06-01 01:06:40'),(306,137,'District of Columbia','2011-06-01 01:06:40'),(307,182,'Loudoun','2011-06-01 01:06:40'),(308,182,'Rappahannock','2011-06-01 01:06:40'),(309,182,'Manassas City','2011-06-01 01:06:40'),(310,182,'Manassas Park City','2011-06-01 01:06:40'),(311,182,'Fauquier','2011-06-01 01:06:40'),(312,182,'Fairfax','2011-06-01 01:06:40'),(313,182,'Prince William','2011-06-01 01:06:40'),(314,152,'Charles','2011-06-01 01:06:40'),(315,152,'Saint Marys','2011-06-01 01:06:40'),(316,152,'Prince Georges','2011-06-01 01:06:40'),(317,152,'Calvert','2011-06-01 01:06:40'),(318,152,'Howard','2011-06-01 01:06:40'),(319,152,'Anne Arundel','2011-06-01 01:06:40'),(320,152,'Montgomery','2011-06-01 01:06:40'),(321,152,'Harford','2011-06-01 01:06:40'),(322,152,'Baltimore','2011-06-01 01:06:40'),(323,152,'Carroll','2011-06-01 01:06:40'),(324,152,'Baltimore City','2011-06-01 01:06:40'),(325,152,'Allegany','2011-06-01 01:06:40'),(326,152,'Garrett','2011-06-01 01:06:40'),(327,152,'Talbot','2011-06-01 01:06:40'),(328,152,'Queen Annes','2011-06-01 01:06:40'),(329,152,'Caroline','2011-06-01 01:06:40'),(330,152,'Kent','2011-06-01 01:06:40'),(331,152,'Dorchester','2011-06-01 01:06:40'),(332,152,'Frederick','2011-06-01 01:06:40'),(333,152,'Washington','2011-06-01 01:06:40'),(334,152,'Wicomico','2011-06-01 01:06:40'),(335,152,'Worcester','2011-06-01 01:06:40'),(336,152,'Somerset','2011-06-01 01:06:40'),(337,152,'Cecil','2011-06-01 01:06:40'),(338,182,'Fairfax City','2011-06-01 01:06:40'),(339,182,'Falls Church City','2011-06-01 01:06:40'),(340,182,'Arlington','2011-06-01 01:06:40'),(341,182,'Alexandria City','2011-06-01 01:06:40'),(342,182,'Fredericksburg City','2011-06-01 01:06:40'),(343,182,'Stafford','2011-06-01 01:06:40'),(344,182,'Spotsylvania','2011-06-01 01:06:40'),(345,182,'Caroline','2011-06-01 01:06:40'),(346,182,'Northumberland','2011-06-01 01:06:40'),(347,182,'Orange','2011-06-01 01:06:40'),(348,182,'Essex','2011-06-01 01:06:40'),(349,182,'Westmoreland','2011-06-01 01:06:40'),(350,182,'King George','2011-06-01 01:06:40'),(351,182,'Richmond','2011-06-01 01:06:40'),(352,182,'Lancaster','2011-06-01 01:06:40'),(353,182,'Winchester City','2011-06-01 01:06:40'),(354,182,'Frederick','2011-06-01 01:06:40'),(355,182,'Warren','2011-06-01 01:06:40'),(356,182,'Clarke','2011-06-01 01:06:40'),(357,182,'Shenandoah','2011-06-01 01:06:40'),(358,182,'Page','2011-06-01 01:06:40'),(359,182,'Culpeper','2011-06-01 01:06:40'),(360,182,'Madison','2011-06-01 01:06:40'),(361,182,'Harrisonburg City','2011-06-01 01:06:40'),(362,182,'Rockingham','2011-06-01 01:06:40'),(363,182,'Augusta','2011-06-01 01:06:40'),(364,182,'Albemarle','2011-06-01 01:06:40'),(365,182,'Charlottesville City','2011-06-01 01:06:40'),(366,182,'Nelson','2011-06-01 01:06:40'),(367,182,'Greene','2011-06-01 01:06:40'),(368,182,'Fluvanna','2011-06-01 01:06:40'),(369,182,'Waynesboro City','2011-06-01 01:06:40'),(370,182,'Gloucester','2011-06-01 01:06:40'),(371,182,'Amelia','2011-06-01 01:06:40'),(372,182,'Buckingham','2011-06-01 01:06:40'),(373,182,'Hanover','2011-06-01 01:06:40'),(374,182,'King William','2011-06-01 01:06:40'),(375,182,'New Kent','2011-06-01 01:06:40'),(376,182,'Goochland','2011-06-01 01:06:40'),(377,182,'Mathews','2011-06-01 01:06:40'),(378,182,'King And Queen','2011-06-01 01:06:40'),(379,182,'Louisa','2011-06-01 01:06:40'),(380,182,'Cumberland','2011-06-01 01:06:40'),(381,182,'Charles City','2011-06-01 01:06:40'),(382,182,'Middlesex','2011-06-01 01:06:40'),(383,182,'Henrico','2011-06-01 01:06:40'),(384,182,'James City','2011-06-01 01:06:40'),(385,182,'York','2011-06-01 01:06:40'),(386,182,'Powhatan','2011-06-01 01:06:40'),(387,182,'Chesterfield','2011-06-01 01:06:40'),(388,182,'Richmond City','2011-06-01 01:06:40'),(389,182,'Williamsburg City','2011-06-01 01:06:40'),(390,182,'Accomack','2011-06-01 01:06:40'),(391,182,'Isle of Wight','2011-06-01 01:06:40'),(392,182,'Northampton','2011-06-01 01:06:40'),(393,182,'Chesapeake City','2011-06-01 01:06:40'),(394,182,'Suffolk City','2011-06-01 01:06:40'),(395,182,'Virginia Beach City','2011-06-01 01:06:40'),(396,182,'Norfolk City','2011-06-01 01:06:40'),(397,182,'Newport News City','2011-06-01 01:06:40'),(398,182,'Hampton City','2011-06-01 01:06:40'),(399,182,'Poquoson City','2011-06-01 01:06:40'),(400,182,'Portsmouth City','2011-06-01 01:06:40'),(401,182,'Prince George','2011-06-01 01:06:40'),(402,182,'Petersburg City','2011-06-01 01:06:40'),(403,182,'Brunswick','2011-06-01 01:06:40'),(404,182,'Dinwiddie','2011-06-01 01:06:40'),(405,182,'Nottoway','2011-06-01 01:06:40'),(406,182,'Southampton','2011-06-01 01:06:40'),(407,182,'Colonial Heights City','2011-06-01 01:06:40'),(408,182,'Surry','2011-06-01 01:06:40'),(409,182,'Emporia City','2011-06-01 01:06:40'),(410,182,'Franklin City','2011-06-01 01:06:40'),(411,182,'Hopewell City','2011-06-01 01:06:40'),(412,182,'Sussex','2011-06-01 01:06:40'),(413,182,'Greensville','2011-06-01 01:06:40'),(414,182,'Prince Edward','2011-06-01 01:06:40'),(415,182,'Mecklenburg','2011-06-01 01:06:40'),(416,182,'Charlotte','2011-06-01 01:06:40'),(417,182,'Lunenburg','2011-06-01 01:06:40'),(418,182,'Appomattox','2011-06-01 01:06:40'),(419,182,'Roanoke City','2011-06-01 01:06:40'),(420,182,'Roanoke','2011-06-01 01:06:40'),(421,182,'Botetourt','2011-06-01 01:06:40'),(422,182,'Montgomery','2011-06-01 01:06:40'),(423,182,'Patrick','2011-06-01 01:06:40'),(424,182,'Henry','2011-06-01 01:06:40'),(425,182,'Pulaski','2011-06-01 01:06:40'),(426,182,'Franklin','2011-06-01 01:06:40'),(427,182,'Pittsylvania','2011-06-01 01:06:40'),(428,182,'Floyd','2011-06-01 01:06:40'),(429,182,'Giles','2011-06-01 01:06:40'),(430,182,'Bedford','2011-06-01 01:06:40'),(431,182,'Martinsville City','2011-06-01 01:06:40'),(432,182,'Craig','2011-06-01 01:06:40'),(433,182,'Salem','2011-06-01 01:06:40'),(434,182,'Bristol','2011-06-01 01:06:40'),(435,182,'Washington','2011-06-01 01:06:40'),(436,182,'Wise','2011-06-01 01:06:40'),(437,182,'Dickenson','2011-06-01 01:06:40'),(438,182,'Lee','2011-06-01 01:06:40'),(439,182,'Russell','2011-06-01 01:06:40'),(440,182,'Buchanan','2011-06-01 01:06:40'),(441,182,'Scott','2011-06-01 01:06:40'),(442,182,'Norton City','2011-06-01 01:06:40'),(443,182,'Grayson','2011-06-01 01:06:40'),(444,182,'Smyth','2011-06-01 01:06:40'),(445,182,'Wythe','2011-06-01 01:06:40'),(446,182,'Bland','2011-06-01 01:06:40'),(447,182,'Carroll','2011-06-01 01:06:40'),(448,182,'Galax City','2011-06-01 01:06:40'),(449,182,'Tazewell','2011-06-01 01:06:40'),(450,182,'Staunton City','2011-06-01 01:06:40'),(451,182,'Bath','2011-06-01 01:06:40'),(452,182,'Highland','2011-06-01 01:06:40'),(453,182,'Rockbridge','2011-06-01 01:06:40'),(454,182,'Buena Vista City','2011-06-01 01:06:40'),(455,182,'Clifton Forge City','2011-06-01 01:06:40'),(456,182,'Covington City','2011-06-01 01:06:40'),(457,182,'Alleghany','2011-06-01 01:06:40'),(458,182,'Lexington City','2011-06-01 01:06:40'),(459,182,'Lynchburg City','2011-06-01 01:06:40'),(460,182,'Campbell','2011-06-01 01:06:40'),(461,182,'Halifax','2011-06-01 01:06:40'),(462,182,'Amherst','2011-06-01 01:06:40'),(463,182,'Bedford City','2011-06-01 01:06:40'),(464,182,'Danville City','2011-06-01 01:06:40'),(465,184,'Mercer','2011-06-01 01:06:40'),(466,184,'Wyoming','2011-06-01 01:06:40'),(467,184,'McDowell','2011-06-01 01:06:40'),(468,184,'Mingo','2011-06-01 01:06:40'),(469,184,'Greenbrier','2011-06-01 01:06:40'),(470,184,'Pocahontas','2011-06-01 01:06:40'),(471,184,'Monroe','2011-06-01 01:06:40'),(472,184,'Summers','2011-06-01 01:06:40'),(473,184,'Fayette','2011-06-01 01:06:40'),(474,184,'Kanawha','2011-06-01 01:06:40'),(475,184,'Roane','2011-06-01 01:06:40'),(476,184,'Raleigh','2011-06-01 01:06:40'),(477,184,'Boone','2011-06-01 01:06:40'),(478,184,'Putnam','2011-06-01 01:06:40'),(479,184,'Clay','2011-06-01 01:06:40'),(480,184,'Logan','2011-06-01 01:06:40'),(481,184,'Nicholas','2011-06-01 01:06:40'),(482,184,'Mason','2011-06-01 01:06:40'),(483,184,'Jackson','2011-06-01 01:06:40'),(484,184,'Calhoun','2011-06-01 01:06:40'),(485,184,'Gilmer','2011-06-01 01:06:40'),(486,184,'Berkeley','2011-06-01 01:06:40'),(487,184,'Jefferson','2011-06-01 01:06:40'),(488,184,'Morgan','2011-06-01 01:06:40'),(489,184,'Hampshire','2011-06-01 01:06:40'),(490,184,'Lincoln','2011-06-01 01:06:40'),(491,184,'Cabell','2011-06-01 01:06:40'),(492,184,'Wayne','2011-06-01 01:06:40'),(493,184,'Ohio','2011-06-01 01:06:40'),(494,184,'Brooke','2011-06-01 01:06:40'),(495,184,'Marshall','2011-06-01 01:06:40'),(496,184,'Hancock','2011-06-01 01:06:40'),(497,184,'Wood','2011-06-01 01:06:40'),(498,184,'Pleasants','2011-06-01 01:06:40'),(499,184,'Wirt','2011-06-01 01:06:40'),(500,184,'Tyler','2011-06-01 01:06:40'),(501,184,'Ritchie','2011-06-01 01:06:40'),(502,184,'Wetzel','2011-06-01 01:06:40'),(503,184,'Upshur','2011-06-01 01:06:40'),(504,184,'Webster','2011-06-01 01:06:40'),(505,184,'Randolph','2011-06-01 01:06:40'),(506,184,'Barbour','2011-06-01 01:06:40'),(507,184,'Tucker','2011-06-01 01:06:40'),(508,184,'Harrison','2011-06-01 01:06:40'),(509,184,'Lewis','2011-06-01 01:06:40'),(510,184,'Braxton','2011-06-01 01:06:40'),(511,184,'Doddridge','2011-06-01 01:06:40'),(512,184,'Taylor','2011-06-01 01:06:40'),(513,184,'Preston','2011-06-01 01:06:40'),(514,184,'Monongalia','2011-06-01 01:06:40'),(515,184,'Marion','2011-06-01 01:06:40'),(516,184,'Grant','2011-06-01 01:06:40'),(517,184,'Mineral','2011-06-01 01:06:40'),(518,184,'Hardy','2011-06-01 01:06:40'),(519,184,'Pendleton','2011-06-01 01:06:40'),(520,165,'Davie','2011-06-01 01:06:40'),(521,165,'Surry','2011-06-01 01:06:40'),(522,165,'Forsyth','2011-06-01 01:06:40'),(523,165,'Yadkin','2011-06-01 01:06:40'),(524,165,'Rowan','2011-06-01 01:06:40'),(525,165,'Stokes','2011-06-01 01:06:40'),(526,165,'Rockingham','2011-06-01 01:06:40'),(527,165,'Alamance','2011-06-01 01:06:40'),(528,165,'Randolph','2011-06-01 01:06:40'),(529,165,'Chatham','2011-06-01 01:06:40'),(530,165,'Montgomery','2011-06-01 01:06:40'),(531,165,'Caswell','2011-06-01 01:06:40'),(532,165,'Guilford','2011-06-01 01:06:40'),(533,165,'Orange','2011-06-01 01:06:40'),(534,165,'Lee','2011-06-01 01:06:40'),(535,165,'Davidson','2011-06-01 01:06:40'),(536,165,'Moore','2011-06-01 01:06:40'),(537,165,'Person','2011-06-01 01:06:40'),(538,165,'Harnett','2011-06-01 01:06:40'),(539,165,'Wake','2011-06-01 01:06:40'),(540,165,'Durham','2011-06-01 01:06:40'),(541,165,'Johnston','2011-06-01 01:06:40'),(542,165,'Granville','2011-06-01 01:06:40'),(543,165,'Franklin','2011-06-01 01:06:40'),(544,165,'Wayne','2011-06-01 01:06:40'),(545,165,'Vance','2011-06-01 01:06:40'),(546,165,'Warren','2011-06-01 01:06:40'),(547,165,'Edgecombe','2011-06-01 01:06:40'),(548,165,'Nash','2011-06-01 01:06:40'),(549,165,'Bertie','2011-06-01 01:06:40'),(550,165,'Beaufort','2011-06-01 01:06:40'),(551,165,'Pitt','2011-06-01 01:06:40'),(552,165,'Wilson','2011-06-01 01:06:40'),(553,165,'Hertford','2011-06-01 01:06:40'),(554,165,'Northampton','2011-06-01 01:06:40'),(555,165,'Halifax','2011-06-01 01:06:40'),(556,165,'Hyde','2011-06-01 01:06:40'),(557,165,'Martin','2011-06-01 01:06:40'),(558,165,'Greene','2011-06-01 01:06:40'),(559,165,'Pasquotank','2011-06-01 01:06:40'),(560,165,'Dare','2011-06-01 01:06:40'),(561,165,'Currituck','2011-06-01 01:06:40'),(562,165,'Perquimans','2011-06-01 01:06:40'),(563,165,'Camden','2011-06-01 01:06:40'),(564,165,'Tyrrell','2011-06-01 01:06:40'),(565,165,'Gates','2011-06-01 01:06:40'),(566,165,'Washington','2011-06-01 01:06:40'),(567,165,'Chowan','2011-06-01 01:06:40'),(568,165,'Stanly','2011-06-01 01:06:40'),(569,165,'Gaston','2011-06-01 01:06:40'),(570,165,'Anson','2011-06-01 01:06:40'),(571,165,'Iredell','2011-06-01 01:06:40'),(572,165,'Cleveland','2011-06-01 01:06:40'),(573,165,'Rutherford','2011-06-01 01:06:40'),(574,165,'Cabarrus','2011-06-01 01:06:40'),(575,165,'Mecklenburg','2011-06-01 01:06:40'),(576,165,'Lincoln','2011-06-01 01:06:40'),(577,165,'Union','2011-06-01 01:06:40'),(578,165,'Cumberland','2011-06-01 01:06:40'),(579,165,'Sampson','2011-06-01 01:06:40'),(580,165,'Robeson','2011-06-01 01:06:40'),(581,165,'Bladen','2011-06-01 01:06:40'),(582,165,'Duplin','2011-06-01 01:06:40'),(583,165,'Richmond','2011-06-01 01:06:40'),(584,165,'Scotland','2011-06-01 01:06:40'),(585,165,'Hoke','2011-06-01 01:06:40'),(586,165,'New Hanover','2011-06-01 01:06:40'),(587,165,'Brunswick','2011-06-01 01:06:40'),(588,165,'Pender','2011-06-01 01:06:40'),(589,165,'Columbus','2011-06-01 01:06:40'),(590,165,'Onslow','2011-06-01 01:06:40'),(591,165,'Lenoir','2011-06-01 01:06:40'),(592,165,'Pamlico','2011-06-01 01:06:40'),(593,165,'Carteret','2011-06-01 01:06:40'),(594,165,'Craven','2011-06-01 01:06:40'),(595,165,'Jones','2011-06-01 01:06:40'),(596,165,'Catawba','2011-06-01 01:06:40'),(597,165,'Avery','2011-06-01 01:06:40'),(598,165,'Watauga','2011-06-01 01:06:40'),(599,165,'Wilkes','2011-06-01 01:06:40'),(600,165,'Caldwell','2011-06-01 01:06:40'),(601,165,'Burke','2011-06-01 01:06:40'),(602,165,'Ashe','2011-06-01 01:06:40'),(603,165,'Alleghany','2011-06-01 01:06:40'),(604,165,'Alexander','2011-06-01 01:06:40'),(605,165,'Buncombe','2011-06-01 01:06:40'),(606,165,'Swain','2011-06-01 01:06:40'),(607,165,'Mitchell','2011-06-01 01:06:40'),(608,165,'Jackson','2011-06-01 01:06:40'),(609,165,'Transylvania','2011-06-01 01:06:40'),(610,165,'Henderson','2011-06-01 01:06:40'),(611,165,'Yancey','2011-06-01 01:06:40'),(612,165,'Haywood','2011-06-01 01:06:40'),(613,165,'Polk','2011-06-01 01:06:40'),(614,165,'Graham','2011-06-01 01:06:40'),(615,165,'Macon','2011-06-01 01:06:40'),(616,165,'McDowell','2011-06-01 01:06:40'),(617,165,'Madison','2011-06-01 01:06:40'),(618,165,'Cherokee','2011-06-01 01:06:40'),(619,165,'Clay','2011-06-01 01:06:40'),(620,175,'Clarendon','2011-06-01 01:06:40'),(621,175,'Richland','2011-06-01 01:06:40'),(622,175,'Bamberg','2011-06-01 01:06:40'),(623,175,'Lexington','2011-06-01 01:06:40'),(624,175,'Kershaw','2011-06-01 01:06:40'),(625,175,'Lee','2011-06-01 01:06:40'),(626,175,'Chester','2011-06-01 01:06:40'),(627,175,'Fairfield','2011-06-01 01:06:40'),(628,175,'Orangeburg','2011-06-01 01:06:40'),(629,175,'Calhoun','2011-06-01 01:06:40'),(630,175,'Union','2011-06-01 01:06:40'),(631,175,'Newberry','2011-06-01 01:06:40'),(632,175,'Sumter','2011-06-01 01:06:40'),(633,175,'Williamsburg','2011-06-01 01:06:40'),(634,175,'Lancaster','2011-06-01 01:06:40'),(635,175,'Darlington','2011-06-01 01:06:40'),(636,175,'Colleton','2011-06-01 01:06:40'),(637,175,'Chesterfield','2011-06-01 01:06:40'),(638,175,'Saluda','2011-06-01 01:06:40'),(639,175,'Florence','2011-06-01 01:06:40'),(640,175,'Aiken','2011-06-01 01:06:40'),(641,175,'Spartanburg','2011-06-01 01:06:40'),(642,175,'Laurens','2011-06-01 01:06:40'),(643,175,'Cherokee','2011-06-01 01:06:40'),(644,175,'Charleston','2011-06-01 01:06:40'),(645,175,'Berkeley','2011-06-01 01:06:40'),(646,175,'Dorchester','2011-06-01 01:06:40'),(647,175,'Georgetown','2011-06-01 01:06:40'),(648,175,'Horry','2011-06-01 01:06:40'),(649,175,'Marlboro','2011-06-01 01:06:40'),(650,175,'Marion','2011-06-01 01:06:40'),(651,175,'Dillon','2011-06-01 01:06:40'),(652,175,'Greenville','2011-06-01 01:06:40'),(653,175,'Abbeville','2011-06-01 01:06:40'),(654,175,'Anderson','2011-06-01 01:06:40'),(655,175,'Pickens','2011-06-01 01:06:40'),(656,175,'Oconee','2011-06-01 01:06:40'),(657,175,'Greenwood','2011-06-01 01:06:40'),(658,175,'York','2011-06-01 01:06:40'),(659,175,'Allendale','2011-06-01 01:06:40'),(660,175,'Barnwell','2011-06-01 01:06:40'),(661,175,'McCormick','2011-06-01 01:06:40'),(662,175,'Edgefield','2011-06-01 01:06:40'),(663,175,'Beaufort','2011-06-01 01:06:40'),(664,175,'Hampton','2011-06-01 01:06:40'),(665,175,'Jasper','2011-06-01 01:06:40'),(666,140,'Dekalb','2011-06-01 01:06:40'),(667,140,'Gwinnett','2011-06-01 01:06:40'),(668,140,'Fulton','2011-06-01 01:06:40'),(669,140,'Cobb','2011-06-01 01:06:40'),(670,140,'Barrow','2011-06-01 01:06:40'),(671,140,'Rockdale','2011-06-01 01:06:40'),(672,140,'Newton','2011-06-01 01:06:40'),(673,140,'Walton','2011-06-01 01:06:40'),(674,140,'Forsyth','2011-06-01 01:06:40'),(675,140,'Jasper','2011-06-01 01:06:40'),(676,140,'Bartow','2011-06-01 01:06:40'),(677,140,'Polk','2011-06-01 01:06:40'),(678,140,'Floyd','2011-06-01 01:06:40'),(679,140,'Cherokee','2011-06-01 01:06:40'),(680,140,'Carroll','2011-06-01 01:06:40'),(681,140,'Haralson','2011-06-01 01:06:40'),(682,140,'Douglas','2011-06-01 01:06:40'),(683,140,'Paulding','2011-06-01 01:06:40'),(684,140,'Gordon','2011-06-01 01:06:40'),(685,140,'Pickens','2011-06-01 01:06:40'),(686,140,'Lamar','2011-06-01 01:06:40'),(687,140,'Fayette','2011-06-01 01:06:40'),(688,140,'Pike','2011-06-01 01:06:40'),(689,140,'Spalding','2011-06-01 01:06:40'),(690,140,'Butts','2011-06-01 01:06:40'),(691,140,'Heard','2011-06-01 01:06:40'),(692,140,'Meriwether','2011-06-01 01:06:40'),(693,140,'Coweta','2011-06-01 01:06:40'),(694,140,'Henry','2011-06-01 01:06:40'),(695,140,'Troup','2011-06-01 01:06:40'),(696,140,'Clayton','2011-06-01 01:06:40'),(697,140,'Upson','2011-06-01 01:06:40'),(698,140,'Emanuel','2011-06-01 01:06:40'),(699,140,'Montgomery','2011-06-01 01:06:40'),(700,140,'Wheeler','2011-06-01 01:06:40'),(701,140,'Jefferson','2011-06-01 01:06:40'),(702,140,'Evans','2011-06-01 01:06:40'),(703,140,'Bulloch','2011-06-01 01:06:40'),(704,140,'Tattnall','2011-06-01 01:06:40'),(705,140,'Screven','2011-06-01 01:06:40'),(706,140,'Burke','2011-06-01 01:06:40'),(707,140,'Toombs','2011-06-01 01:06:40'),(708,140,'Candler','2011-06-01 01:06:40'),(709,140,'Jenkins','2011-06-01 01:06:40'),(710,140,'Laurens','2011-06-01 01:06:40'),(711,140,'Treutlen','2011-06-01 01:06:40'),(712,140,'Hall','2011-06-01 01:06:40'),(713,140,'Habersham','2011-06-01 01:06:40'),(714,140,'Banks','2011-06-01 01:06:40'),(715,140,'Union','2011-06-01 01:06:40'),(716,140,'Fannin','2011-06-01 01:06:40'),(717,140,'Hart','2011-06-01 01:06:40'),(718,140,'Jackson','2011-06-01 01:06:40'),(719,140,'Franklin','2011-06-01 01:06:40'),(720,140,'Gilmer','2011-06-01 01:06:40'),(721,140,'Rabun','2011-06-01 01:06:40'),(722,140,'White','2011-06-01 01:06:40'),(723,140,'Lumpkin','2011-06-01 01:06:40'),(724,140,'Dawson','2011-06-01 01:06:40'),(725,140,'Stephens','2011-06-01 01:06:40'),(726,140,'Towns','2011-06-01 01:06:40'),(727,140,'Clarke','2011-06-01 01:06:40'),(728,140,'Oglethorpe','2011-06-01 01:06:40'),(729,140,'Oconee','2011-06-01 01:06:40'),(730,140,'Morgan','2011-06-01 01:06:40'),(731,140,'Elbert','2011-06-01 01:06:40'),(732,140,'Madison','2011-06-01 01:06:40'),(733,140,'Taliaferro','2011-06-01 01:06:40'),(734,140,'Greene','2011-06-01 01:06:40'),(735,140,'Wilkes','2011-06-01 01:06:40'),(736,140,'Murray','2011-06-01 01:06:40'),(737,140,'Walker','2011-06-01 01:06:40'),(738,140,'Whitfield','2011-06-01 01:06:40'),(739,140,'Catoosa','2011-06-01 01:06:40'),(740,140,'Chattooga','2011-06-01 01:06:40'),(741,140,'Dade','2011-06-01 01:06:40'),(742,140,'Columbia','2011-06-01 01:06:40'),(743,140,'Richmond','2011-06-01 01:06:40'),(744,140,'McDuffie','2011-06-01 01:06:40'),(745,140,'Warren','2011-06-01 01:06:40'),(746,140,'Glascock','2011-06-01 01:06:40'),(747,140,'Lincoln','2011-06-01 01:06:40'),(748,140,'Wilcox','2011-06-01 01:06:40'),(749,140,'Wilkinson','2011-06-01 01:06:40'),(750,140,'Monroe','2011-06-01 01:06:40'),(751,140,'Houston','2011-06-01 01:06:40'),(752,140,'Taylor','2011-06-01 01:06:40'),(753,140,'Dooly','2011-06-01 01:06:40'),(754,140,'Peach','2011-06-01 01:06:40'),(755,140,'Crisp','2011-06-01 01:06:40'),(756,140,'Dodge','2011-06-01 01:06:40'),(757,140,'Bleckley','2011-06-01 01:06:40'),(758,140,'Twiggs','2011-06-01 01:06:40'),(759,140,'Washington','2011-06-01 01:06:40'),(760,140,'Putnam','2011-06-01 01:06:40'),(761,140,'Jones','2011-06-01 01:06:40'),(762,140,'Baldwin','2011-06-01 01:06:40'),(763,140,'Pulaski','2011-06-01 01:06:40'),(764,140,'Telfair','2011-06-01 01:06:40'),(765,140,'Macon','2011-06-01 01:06:40'),(766,140,'Johnson','2011-06-01 01:06:40'),(767,140,'Crawford','2011-06-01 01:06:40'),(768,140,'Hancock','2011-06-01 01:06:40'),(769,140,'Bibb','2011-06-01 01:06:40'),(770,140,'Liberty','2011-06-01 01:06:40'),(771,140,'Chatham','2011-06-01 01:06:40'),(772,140,'Effingham','2011-06-01 01:06:40'),(773,140,'McIntosh','2011-06-01 01:06:40'),(774,140,'Bryan','2011-06-01 01:06:40'),(775,140,'Long','2011-06-01 01:06:40'),(776,140,'Ware','2011-06-01 01:06:40'),(777,140,'Bacon','2011-06-01 01:06:40'),(778,140,'Coffee','2011-06-01 01:06:40'),(779,140,'Appling','2011-06-01 01:06:40'),(780,140,'Pierce','2011-06-01 01:06:40'),(781,140,'Glynn','2011-06-01 01:06:40'),(782,140,'Jeff Davis','2011-06-01 01:06:40'),(783,140,'Charlton','2011-06-01 01:06:40'),(784,140,'Brantley','2011-06-01 01:06:40'),(785,140,'Wayne','2011-06-01 01:06:40'),(786,140,'Camden','2011-06-01 01:06:40'),(787,140,'Decatur','2011-06-01 01:06:40'),(788,140,'Lowndes','2011-06-01 01:06:40'),(789,140,'Cook','2011-06-01 01:06:40'),(790,140,'Berrien','2011-06-01 01:06:40'),(791,140,'Clinch','2011-06-01 01:06:40'),(792,140,'Atkinson','2011-06-01 01:06:40'),(793,140,'Brooks','2011-06-01 01:06:40'),(794,140,'Thomas','2011-06-01 01:06:40'),(795,140,'Lanier','2011-06-01 01:06:40'),(796,140,'Echols','2011-06-01 01:06:40'),(797,140,'Dougherty','2011-06-01 01:06:40'),(798,140,'Sumter','2011-06-01 01:06:40'),(799,140,'Turner','2011-06-01 01:06:40'),(800,140,'Mitchell','2011-06-01 01:06:40'),(801,140,'Colquitt','2011-06-01 01:06:40'),(802,140,'Tift','2011-06-01 01:06:40'),(803,140,'Ben Hill','2011-06-01 01:06:40'),(804,140,'Irwin','2011-06-01 01:06:40'),(805,140,'Lee','2011-06-01 01:06:40'),(806,140,'Worth','2011-06-01 01:06:40'),(807,140,'Talbot','2011-06-01 01:06:40'),(808,140,'Marion','2011-06-01 01:06:40'),(809,140,'Harris','2011-06-01 01:06:40'),(810,140,'Chattahoochee','2011-06-01 01:06:40'),(811,140,'Schley','2011-06-01 01:06:40'),(812,140,'Muscogee','2011-06-01 01:06:40'),(813,140,'Stewart','2011-06-01 01:06:40'),(814,140,'Webster','2011-06-01 01:06:40'),(815,139,'Clay','2011-06-01 01:06:40'),(816,139,'Saint Johns','2011-06-01 01:06:40'),(817,139,'Putnam','2011-06-01 01:06:40'),(818,139,'Suwannee','2011-06-01 01:06:40'),(819,139,'Nassau','2011-06-01 01:06:40'),(820,139,'Lafayette','2011-06-01 01:06:40'),(821,139,'Columbia','2011-06-01 01:06:40'),(822,139,'Union','2011-06-01 01:06:40'),(823,139,'Baker','2011-06-01 01:06:40'),(824,139,'Bradford','2011-06-01 01:06:40'),(825,139,'Hamilton','2011-06-01 01:06:40'),(826,139,'Madison','2011-06-01 01:06:40'),(827,139,'Duval','2011-06-01 01:06:40'),(828,139,'Lake','2011-06-01 01:06:40'),(829,139,'Volusia','2011-06-01 01:06:40'),(830,139,'Flagler','2011-06-01 01:06:40'),(831,139,'Marion','2011-06-01 01:06:40'),(832,139,'Sumter','2011-06-01 01:06:40'),(833,139,'Leon','2011-06-01 01:06:40'),(834,139,'Wakulla','2011-06-01 01:06:40'),(835,139,'Franklin','2011-06-01 01:06:40'),(836,139,'Liberty','2011-06-01 01:06:40'),(837,139,'Gadsden','2011-06-01 01:06:40'),(838,139,'Jefferson','2011-06-01 01:06:40'),(839,139,'Taylor','2011-06-01 01:06:40'),(840,139,'Bay','2011-06-01 01:06:40'),(841,139,'Jackson','2011-06-01 01:06:40'),(842,139,'Calhoun','2011-06-01 01:06:40'),(843,139,'Walton','2011-06-01 01:06:40'),(844,139,'Holmes','2011-06-01 01:06:40'),(845,139,'Washington','2011-06-01 01:06:40'),(846,139,'Gulf','2011-06-01 01:06:40'),(847,139,'Escambia','2011-06-01 01:06:40'),(848,139,'Santa Rosa','2011-06-01 01:06:40'),(849,139,'Okaloosa','2011-06-01 01:06:40'),(850,139,'Alachua','2011-06-01 01:06:40'),(851,139,'Gilchrist','2011-06-01 01:06:40'),(852,139,'Levy','2011-06-01 01:06:40'),(853,139,'Dixie','2011-06-01 01:06:40'),(854,139,'Seminole','2011-06-01 01:06:40'),(855,139,'Orange','2011-06-01 01:06:40'),(856,139,'Brevard','2011-06-01 01:06:40'),(857,139,'Indian River','2011-06-01 01:06:40'),(858,139,'Monroe','2011-06-01 01:06:40'),(859,139,'Miami Dade','2011-06-01 01:06:40'),(860,139,'Broward','2011-06-01 01:06:40'),(861,139,'Palm Beach','2011-06-01 01:06:40'),(862,139,'Hendry','2011-06-01 01:06:40'),(863,139,'Martin','2011-06-01 01:06:40'),(864,139,'Glades','2011-06-01 01:06:40'),(865,139,'Hillsborough','2011-06-01 01:06:40'),(866,139,'Pasco','2011-06-01 01:06:40'),(867,139,'Pinellas','2011-06-01 01:06:40'),(868,139,'Polk','2011-06-01 01:06:40'),(869,139,'Highlands','2011-06-01 01:06:40'),(870,139,'Hardee','2011-06-01 01:06:40'),(871,139,'Osceola','2011-06-01 01:06:40'),(872,139,'Lee','2011-06-01 01:06:40'),(873,139,'Charlotte','2011-06-01 01:06:40'),(874,139,'Collier','2011-06-01 01:06:40'),(875,139,'Manatee','2011-06-01 01:06:40'),(876,139,'Sarasota','2011-06-01 01:06:40'),(877,139,'De Soto','2011-06-01 01:06:40'),(878,139,'Citrus','2011-06-01 01:06:40'),(879,139,'Hernando','2011-06-01 01:06:40'),(880,139,'Saint Lucie','2011-06-01 01:06:40'),(881,139,'Okeechobee','2011-06-01 01:06:40'),(882,129,'Saint Clair','2011-06-01 01:06:40'),(883,129,'Jefferson','2011-06-01 01:06:40'),(884,129,'Shelby','2011-06-01 01:06:40'),(885,129,'Tallapoosa','2011-06-01 01:06:40'),(886,129,'Blount','2011-06-01 01:06:40'),(887,129,'Talladega','2011-06-01 01:06:40'),(888,129,'Marshall','2011-06-01 01:06:40'),(889,129,'Cullman','2011-06-01 01:06:40'),(890,129,'Bibb','2011-06-01 01:06:40'),(891,129,'Walker','2011-06-01 01:06:40'),(892,129,'Chilton','2011-06-01 01:06:40'),(893,129,'Coosa','2011-06-01 01:06:40'),(894,129,'Clay','2011-06-01 01:06:40'),(895,129,'Tuscaloosa','2011-06-01 01:06:40'),(896,129,'Hale','2011-06-01 01:06:40'),(897,129,'Pickens','2011-06-01 01:06:40'),(898,129,'Greene','2011-06-01 01:06:40'),(899,129,'Sumter','2011-06-01 01:06:40'),(900,129,'Winston','2011-06-01 01:06:40'),(901,129,'Fayette','2011-06-01 01:06:40'),(902,129,'Marion','2011-06-01 01:06:40'),(903,129,'Lamar','2011-06-01 01:06:40'),(904,129,'Franklin','2011-06-01 01:06:40'),(905,129,'Morgan','2011-06-01 01:06:40'),(906,129,'Lauderdale','2011-06-01 01:06:40'),(907,129,'Limestone','2011-06-01 01:06:40'),(908,129,'Colbert','2011-06-01 01:06:40'),(909,129,'Lawrence','2011-06-01 01:06:40'),(910,129,'Jackson','2011-06-01 01:06:40'),(911,129,'Madison','2011-06-01 01:06:40'),(912,129,'Etowah','2011-06-01 01:06:40'),(913,129,'Cherokee','2011-06-01 01:06:40'),(914,129,'De Kalb','2011-06-01 01:06:40'),(915,129,'Autauga','2011-06-01 01:06:40'),(916,129,'Pike','2011-06-01 01:06:40'),(917,129,'Crenshaw','2011-06-01 01:06:40'),(918,129,'Montgomery','2011-06-01 01:06:40'),(919,129,'Butler','2011-06-01 01:06:40'),(920,129,'Barbour','2011-06-01 01:06:40'),(921,129,'Elmore','2011-06-01 01:06:40'),(922,129,'Bullock','2011-06-01 01:06:40'),(923,129,'Macon','2011-06-01 01:06:40'),(924,129,'Lowndes','2011-06-01 01:06:40'),(925,129,'Covington','2011-06-01 01:06:40'),(926,129,'Calhoun','2011-06-01 01:06:40'),(927,129,'Cleburne','2011-06-01 01:06:40'),(928,129,'Randolph','2011-06-01 01:06:40'),(929,129,'Houston','2011-06-01 01:06:40'),(930,129,'Henry','2011-06-01 01:06:40'),(931,129,'Dale','2011-06-01 01:06:40'),(932,129,'Geneva','2011-06-01 01:06:40'),(933,129,'Coffee','2011-06-01 01:06:40'),(934,129,'Conecuh','2011-06-01 01:06:40'),(935,129,'Monroe','2011-06-01 01:06:40'),(936,129,'Escambia','2011-06-01 01:06:40'),(937,129,'Wilcox','2011-06-01 01:06:40'),(938,129,'Clarke','2011-06-01 01:06:40'),(939,129,'Mobile','2011-06-01 01:06:40'),(940,129,'Baldwin','2011-06-01 01:06:40'),(941,129,'Washington','2011-06-01 01:06:40'),(942,129,'Dallas','2011-06-01 01:06:40'),(943,129,'Marengo','2011-06-01 01:06:40'),(944,129,'Perry','2011-06-01 01:06:40'),(945,129,'Lee','2011-06-01 01:06:40'),(946,129,'Russell','2011-06-01 01:06:40'),(947,129,'Chambers','2011-06-01 01:06:40'),(948,129,'Choctaw','2011-06-01 01:06:40'),(949,177,'Robertson','2011-06-01 01:06:40'),(950,177,'Davidson','2011-06-01 01:06:40'),(951,177,'Dekalb','2011-06-01 01:06:40'),(952,177,'Williamson','2011-06-01 01:06:40'),(953,177,'Cheatham','2011-06-01 01:06:40'),(954,177,'Cannon','2011-06-01 01:06:40'),(955,177,'Coffee','2011-06-01 01:06:40'),(956,177,'Marshall','2011-06-01 01:06:40'),(957,177,'Bedford','2011-06-01 01:06:40'),(958,177,'Sumner','2011-06-01 01:06:40'),(959,177,'Stewart','2011-06-01 01:06:40'),(960,177,'Hickman','2011-06-01 01:06:40'),(961,177,'Dickson','2011-06-01 01:06:40'),(962,177,'Smith','2011-06-01 01:06:40'),(963,177,'Rutherford','2011-06-01 01:06:40'),(964,177,'Montgomery','2011-06-01 01:06:40'),(965,177,'Houston','2011-06-01 01:06:40'),(966,177,'Wilson','2011-06-01 01:06:40'),(967,177,'Trousdale','2011-06-01 01:06:40'),(968,177,'Humphreys','2011-06-01 01:06:40'),(969,177,'Macon','2011-06-01 01:06:40'),(970,177,'Perry','2011-06-01 01:06:40'),(971,177,'Warren','2011-06-01 01:06:40'),(972,177,'Lincoln','2011-06-01 01:06:40'),(973,177,'Maury','2011-06-01 01:06:40'),(974,177,'Grundy','2011-06-01 01:06:40'),(975,177,'Hamilton','2011-06-01 01:06:40'),(976,177,'McMinn','2011-06-01 01:06:40'),(977,177,'Franklin','2011-06-01 01:06:40'),(978,177,'Polk','2011-06-01 01:06:40'),(979,177,'Bradley','2011-06-01 01:06:40'),(980,177,'Monroe','2011-06-01 01:06:40'),(981,177,'Rhea','2011-06-01 01:06:40'),(982,177,'Meigs','2011-06-01 01:06:40'),(983,177,'Sequatchie','2011-06-01 01:06:40'),(984,177,'Marion','2011-06-01 01:06:40'),(985,177,'Moore','2011-06-01 01:06:40'),(986,177,'Bledsoe','2011-06-01 01:06:40'),(987,177,'Shelby','2011-06-01 01:06:40'),(988,177,'Washington','2011-06-01 01:06:40'),(989,177,'Greene','2011-06-01 01:06:40'),(990,177,'Sullivan','2011-06-01 01:06:40'),(991,177,'Johnson','2011-06-01 01:06:40'),(992,177,'Hawkins','2011-06-01 01:06:40'),(993,177,'Carter','2011-06-01 01:06:40'),(994,177,'Unicoi','2011-06-01 01:06:40'),(995,177,'Blount','2011-06-01 01:06:40'),(996,177,'Anderson','2011-06-01 01:06:40'),(997,177,'Claiborne','2011-06-01 01:06:40'),(998,177,'Grainger','2011-06-01 01:06:40'),(999,177,'Cocke','2011-06-01 01:06:40'),(1000,177,'Campbell','2011-06-01 01:06:40'),(1001,177,'Morgan','2011-06-01 01:06:40'),(1002,177,'Knox','2011-06-01 01:06:40'),(1003,177,'Cumberland','2011-06-01 01:06:40'),(1004,177,'Jefferson','2011-06-01 01:06:40'),(1005,177,'Scott','2011-06-01 01:06:40'),(1006,177,'Sevier','2011-06-01 01:06:40'),(1007,177,'Loudon','2011-06-01 01:06:40'),(1008,177,'Roane','2011-06-01 01:06:40'),(1009,177,'Hancock','2011-06-01 01:06:40'),(1010,177,'Hamblen','2011-06-01 01:06:40'),(1011,177,'Union','2011-06-01 01:06:40'),(1012,177,'Crockett','2011-06-01 01:06:40'),(1013,177,'Fayette','2011-06-01 01:06:40'),(1014,177,'Tipton','2011-06-01 01:06:40'),(1015,177,'Dyer','2011-06-01 01:06:40'),(1016,177,'Hardeman','2011-06-01 01:06:40'),(1017,177,'Haywood','2011-06-01 01:06:40'),(1018,177,'Lauderdale','2011-06-01 01:06:40'),(1019,177,'Lake','2011-06-01 01:06:40'),(1020,177,'Carroll','2011-06-01 01:06:40'),(1021,177,'Benton','2011-06-01 01:06:40'),(1022,177,'Henry','2011-06-01 01:06:40'),(1023,177,'Weakley','2011-06-01 01:06:40'),(1024,177,'Obion','2011-06-01 01:06:40'),(1025,177,'Gibson','2011-06-01 01:06:40'),(1026,177,'Madison','2011-06-01 01:06:40'),(1027,177,'McNairy','2011-06-01 01:06:40'),(1028,177,'Decatur','2011-06-01 01:06:40'),(1029,177,'Hardin','2011-06-01 01:06:40'),(1030,177,'Henderson','2011-06-01 01:06:40'),(1031,177,'Chester','2011-06-01 01:06:40'),(1032,177,'Wayne','2011-06-01 01:06:40'),(1033,177,'Giles','2011-06-01 01:06:40'),(1034,177,'Lawrence','2011-06-01 01:06:40'),
   (1035,177,'Lewis','2011-06-01 01:06:40'),(1036,177,'Putnam','2011-06-01 01:06:40'),(1037,177,'Fentress','2011-06-01 01:06:40'),(1038,177,'Overton','2011-06-01 01:06:40'),(1039,177,'Pickett','2011-06-01 01:06:40'),(1040,177,'Clay','2011-06-01 01:06:40'),(1041,177,'White','2011-06-01 01:06:40'),(1042,177,'Jackson','2011-06-01 01:06:40'),(1043,177,'Van Buren','2011-06-01 01:06:40'),(1044,156,'Lafayette','2011-06-01 01:06:40'),(1045,156,'Tate','2011-06-01 01:06:40'),(1046,156,'Benton','2011-06-01 01:06:40'),(1047,156,'Panola','2011-06-01 01:06:40'),(1048,156,'Quitman','2011-06-01 01:06:40'),(1049,156,'Tippah','2011-06-01 01:06:40'),(1050,156,'Marshall','2011-06-01 01:06:40'),(1051,156,'Coahoma','2011-06-01 01:06:40'),(1052,156,'Tunica','2011-06-01 01:06:40'),(1053,156,'Union','2011-06-01 01:06:40'),(1054,156,'De Soto','2011-06-01 01:06:40'),(1055,156,'Washington','2011-06-01 01:06:40'),(1056,156,'Bolivar','2011-06-01 01:06:40'),(1057,156,'Sharkey','2011-06-01 01:06:40'),(1058,156,'Sunflower','2011-06-01 01:06:40'),(1059,156,'Issaquena','2011-06-01 01:06:40'),(1060,156,'Humphreys','2011-06-01 01:06:40'),(1061,156,'Lee','2011-06-01 01:06:40'),(1062,156,'Pontotoc','2011-06-01 01:06:40'),(1063,156,'Monroe','2011-06-01 01:06:40'),(1064,156,'Tishomingo','2011-06-01 01:06:40'),(1065,156,'Prentiss','2011-06-01 01:06:40'),(1066,156,'Alcorn','2011-06-01 01:06:40'),(1067,156,'Calhoun','2011-06-01 01:06:40'),(1068,156,'Itawamba','2011-06-01 01:06:40'),(1069,156,'Chickasaw','2011-06-01 01:06:40'),(1070,156,'Grenada','2011-06-01 01:06:40'),(1071,156,'Carroll','2011-06-01 01:06:40'),(1072,156,'Tallahatchie','2011-06-01 01:06:40'),(1073,156,'Yalobusha','2011-06-01 01:06:40'),(1074,156,'Holmes','2011-06-01 01:06:40'),(1075,156,'Montgomery','2011-06-01 01:06:40'),(1076,156,'Leflore','2011-06-01 01:06:40'),(1077,156,'Yazoo','2011-06-01 01:06:40'),(1078,156,'Hinds','2011-06-01 01:06:40'),(1079,156,'Rankin','2011-06-01 01:06:40'),(1080,156,'Simpson','2011-06-01 01:06:40'),(1081,156,'Madison','2011-06-01 01:06:40'),(1082,156,'Leake','2011-06-01 01:06:40'),(1083,156,'Newton','2011-06-01 01:06:40'),(1084,156,'Copiah','2011-06-01 01:06:40'),(1085,156,'Attala','2011-06-01 01:06:40'),(1086,156,'Jefferson','2011-06-01 01:06:40'),(1087,156,'Scott','2011-06-01 01:06:40'),(1088,156,'Claiborne','2011-06-01 01:06:40'),(1089,156,'Smith','2011-06-01 01:06:40'),(1090,156,'Covington','2011-06-01 01:06:40'),(1091,156,'Adams','2011-06-01 01:06:40'),(1092,156,'Lawrence','2011-06-01 01:06:40'),(1093,156,'Warren','2011-06-01 01:06:40'),(1094,156,'Lauderdale','2011-06-01 01:06:40'),(1095,156,'Wayne','2011-06-01 01:06:40'),(1096,156,'Kemper','2011-06-01 01:06:40'),(1097,156,'Clarke','2011-06-01 01:06:40'),(1098,156,'Jasper','2011-06-01 01:06:40'),(1099,156,'Winston','2011-06-01 01:06:40'),(1100,156,'Noxubee','2011-06-01 01:06:40'),(1101,156,'Neshoba','2011-06-01 01:06:40'),(1102,156,'Greene','2011-06-01 01:06:40'),(1103,156,'Forrest','2011-06-01 01:06:40'),(1104,156,'Jefferson Davis','2011-06-01 01:06:40'),(1105,156,'Perry','2011-06-01 01:06:40'),(1106,156,'Pearl River','2011-06-01 01:06:40'),(1107,156,'Marion','2011-06-01 01:06:40'),(1108,156,'Jones','2011-06-01 01:06:40'),(1109,156,'George','2011-06-01 01:06:40'),(1110,156,'Lamar','2011-06-01 01:06:40'),(1111,156,'Harrison','2011-06-01 01:06:40'),(1112,156,'Hancock','2011-06-01 01:06:40'),(1113,156,'Jackson','2011-06-01 01:06:40'),(1114,156,'Stone','2011-06-01 01:06:40'),(1115,156,'Lincoln','2011-06-01 01:06:40'),(1116,156,'Franklin','2011-06-01 01:06:40'),(1117,156,'Wilkinson','2011-06-01 01:06:40'),(1118,156,'Pike','2011-06-01 01:06:40'),(1119,156,'Amite','2011-06-01 01:06:40'),(1120,156,'Walthall','2011-06-01 01:06:40'),(1121,156,'Lowndes','2011-06-01 01:06:40'),(1122,156,'Choctaw','2011-06-01 01:06:40'),(1123,156,'Webster','2011-06-01 01:06:40'),(1124,156,'Clay','2011-06-01 01:06:40'),(1125,156,'Oktibbeha','2011-06-01 01:06:40'),(1126,140,'Calhoun','2011-06-01 01:06:40'),(1127,140,'Early','2011-06-01 01:06:40'),(1128,140,'Clay','2011-06-01 01:06:40'),(1129,140,'Phelps','2011-06-01 01:06:40'),(1130,140,'Terrell','2011-06-01 01:06:40'),(1131,140,'Grady','2011-06-01 01:06:40'),(1132,140,'Seminole','2011-06-01 01:06:40'),(1133,140,'Quitman','2011-06-01 01:06:40'),(1134,140,'Baker','2011-06-01 01:06:40'),(1135,140,'Randolph','2011-06-01 01:06:40'),(1136,148,'Shelby','2011-06-01 01:06:40'),(1137,148,'Nelson','2011-06-01 01:06:40'),(1138,148,'Trimble','2011-06-01 01:06:40'),(1139,148,'Henry','2011-06-01 01:06:40'),(1140,148,'Marion','2011-06-01 01:06:40'),(1141,148,'Oldham','2011-06-01 01:06:40'),(1142,148,'Jefferson','2011-06-01 01:06:40'),(1143,148,'Washington','2011-06-01 01:06:40'),(1144,148,'Spencer','2011-06-01 01:06:40'),(1145,148,'Bullitt','2011-06-01 01:06:40'),(1146,148,'Meade','2011-06-01 01:06:40'),(1147,148,'Breckinridge','2011-06-01 01:06:40'),(1148,148,'Grayson','2011-06-01 01:06:40'),(1149,148,'Hardin','2011-06-01 01:06:40'),(1150,148,'Mercer','2011-06-01 01:06:40'),(1151,148,'Nicholas','2011-06-01 01:06:40'),(1152,148,'Powell','2011-06-01 01:06:40'),(1153,148,'Rowan','2011-06-01 01:06:40'),(1154,148,'Menifee','2011-06-01 01:06:40'),(1155,148,'Scott','2011-06-01 01:06:40'),(1156,148,'Montgomery','2011-06-01 01:06:40'),(1157,148,'Estill','2011-06-01 01:06:40'),(1158,148,'Jessamine','2011-06-01 01:06:40'),(1159,148,'Anderson','2011-06-01 01:06:40'),(1160,148,'Woodford','2011-06-01 01:06:40'),(1161,148,'Bourbon','2011-06-01 01:06:40'),(1162,148,'Owen','2011-06-01 01:06:40'),(1163,148,'Bath','2011-06-01 01:06:40'),(1164,148,'Madison','2011-06-01 01:06:40'),(1165,148,'Clark','2011-06-01 01:06:40'),(1166,148,'Jackson','2011-06-01 01:06:40'),(1167,148,'Rockcastle','2011-06-01 01:06:40'),(1168,148,'Garrard','2011-06-01 01:06:40'),(1169,148,'Lincoln','2011-06-01 01:06:40'),(1170,148,'Boyle','2011-06-01 01:06:40'),(1171,148,'Fayette','2011-06-01 01:06:40'),(1172,148,'Franklin','2011-06-01 01:06:40'),(1173,148,'Whitley','2011-06-01 01:06:40'),(1174,148,'Laurel','2011-06-01 01:06:40'),(1175,148,'Knox','2011-06-01 01:06:40'),(1176,148,'Harlan','2011-06-01 01:06:40'),(1177,148,'Leslie','2011-06-01 01:06:40'),(1178,148,'Bell','2011-06-01 01:06:40'),(1179,148,'Letcher','2011-06-01 01:06:40'),(1180,148,'Clay','2011-06-01 01:06:40'),(1181,148,'Perry','2011-06-01 01:06:40'),(1182,148,'Campbell','2011-06-01 01:06:40'),(1183,148,'Bracken','2011-06-01 01:06:40'),(1184,148,'Harrison','2011-06-01 01:06:40'),(1185,148,'Boone','2011-06-01 01:06:40'),(1186,148,'Pendleton','2011-06-01 01:06:40'),(1187,148,'Carroll','2011-06-01 01:06:40'),(1188,148,'Grant','2011-06-01 01:06:40'),(1189,148,'Kenton','2011-06-01 01:06:40'),(1190,148,'Mason','2011-06-01 01:06:40'),(1191,148,'Fleming','2011-06-01 01:06:40'),(1192,148,'Gallatin','2011-06-01 01:06:40'),(1193,148,'Robertson','2011-06-01 01:06:40'),(1194,148,'Boyd','2011-06-01 01:06:40'),(1195,148,'Greenup','2011-06-01 01:06:40'),(1196,148,'Lawrence','2011-06-01 01:06:40'),(1197,148,'Carter','2011-06-01 01:06:40'),(1198,148,'Lewis','2011-06-01 01:06:40'),(1199,148,'Elliott','2011-06-01 01:06:40'),(1200,148,'Martin','2011-06-01 01:06:40'),(1201,148,'Johnson','2011-06-01 01:06:40'),(1202,148,'Wolfe','2011-06-01 01:06:40'),(1203,148,'Breathitt','2011-06-01 01:06:40'),(1204,148,'Lee','2011-06-01 01:06:40'),(1205,148,'Owsley','2011-06-01 01:06:40'),(1206,148,'Morgan','2011-06-01 01:06:40'),(1207,148,'Magoffin','2011-06-01 01:06:40'),(1208,148,'Pike','2011-06-01 01:06:40'),(1209,148,'Floyd','2011-06-01 01:06:40'),(1210,148,'Knott','2011-06-01 01:06:40'),(1211,148,'McCracken','2011-06-01 01:06:40'),(1212,148,'Calloway','2011-06-01 01:06:40'),(1213,148,'Carlisle','2011-06-01 01:06:40'),(1214,148,'Ballard','2011-06-01 01:06:40'),(1215,148,'Marshall','2011-06-01 01:06:40'),(1216,148,'Graves','2011-06-01 01:06:40'),(1217,148,'Livingston','2011-06-01 01:06:40'),(1218,148,'Hickman','2011-06-01 01:06:40'),(1219,148,'Crittenden','2011-06-01 01:06:40'),(1220,148,'Lyon','2011-06-01 01:06:40'),(1221,148,'Fulton','2011-06-01 01:06:40'),(1222,148,'Warren','2011-06-01 01:06:40'),(1223,148,'Allen','2011-06-01 01:06:40'),(1224,148,'Barren','2011-06-01 01:06:40'),(1225,148,'Metcalfe','2011-06-01 01:06:40'),(1226,148,'Monroe','2011-06-01 01:06:40'),(1227,148,'Simpson','2011-06-01 01:06:40'),(1228,148,'Edmonson','2011-06-01 01:06:40'),(1229,148,'Butler','2011-06-01 01:06:40'),(1230,148,'Logan','2011-06-01 01:06:40'),(1231,148,'Todd','2011-06-01 01:06:40'),(1232,148,'Trigg','2011-06-01 01:06:40'),(1233,148,'Christian','2011-06-01 01:06:40'),(1234,148,'Daviess','2011-06-01 01:06:40'),(1235,148,'Ohio','2011-06-01 01:06:40'),(1236,148,'Muhlenberg','2011-06-01 01:06:40'),(1237,148,'McLean','2011-06-01 01:06:40'),(1238,148,'Hancock','2011-06-01 01:06:40'),(1239,148,'Henderson','2011-06-01 01:06:40'),(1240,148,'Webster','2011-06-01 01:06:40'),(1241,148,'Hopkins','2011-06-01 01:06:40'),(1242,148,'Caldwell','2011-06-01 01:06:40'),(1243,148,'Union','2011-06-01 01:06:40'),(1244,148,'Pulaski','2011-06-01 01:06:40'),(1245,148,'Casey','2011-06-01 01:06:40'),(1246,148,'Clinton','2011-06-01 01:06:40'),(1247,148,'Russell','2011-06-01 01:06:40'),(1248,148,'McCreary','2011-06-01 01:06:40'),(1249,148,'Wayne','2011-06-01 01:06:40'),(1250,148,'Hart','2011-06-01 01:06:40'),(1251,148,'Adair','2011-06-01 01:06:40'),(1252,148,'Larue','2011-06-01 01:06:40'),(1253,148,'Cumberland','2011-06-01 01:06:40'),(1254,148,'Taylor','2011-06-01 01:06:40'),(1255,148,'Green','2011-06-01 01:06:40'),(1256,168,'Licking','2011-06-01 01:06:40'),(1257,168,'Franklin','2011-06-01 01:06:40'),(1258,168,'Delaware','2011-06-01 01:06:40'),(1259,168,'Knox','2011-06-01 01:06:40'),(1260,168,'Union','2011-06-01 01:06:40'),(1261,168,'Champaign','2011-06-01 01:06:40'),(1262,168,'Clark','2011-06-01 01:06:40'),(1263,168,'Fairfield','2011-06-01 01:06:40'),(1264,168,'Madison','2011-06-01 01:06:40'),(1265,168,'Perry','2011-06-01 01:06:40'),(1266,168,'Ross','2011-06-01 01:06:40'),(1267,168,'Pickaway','2011-06-01 01:06:40'),(1268,168,'Fayette','2011-06-01 01:06:40'),(1269,168,'Hocking','2011-06-01 01:06:40'),(1270,168,'Marion','2011-06-01 01:06:40'),(1271,168,'Logan','2011-06-01 01:06:40'),(1272,168,'Morrow','2011-06-01 01:06:40'),(1273,168,'Wyandot','2011-06-01 01:06:40'),(1274,168,'Hardin','2011-06-01 01:06:40'),(1275,168,'Wood','2011-06-01 01:06:40'),(1276,168,'Sandusky','2011-06-01 01:06:40'),(1277,168,'Ottawa','2011-06-01 01:06:40'),(1278,168,'Lucas','2011-06-01 01:06:40'),(1279,168,'Erie','2011-06-01 01:06:40'),(1280,168,'Williams','2011-06-01 01:06:40'),(1281,168,'Fulton','2011-06-01 01:06:40'),(1282,168,'Henry','2011-06-01 01:06:40'),(1283,168,'Defiance','2011-06-01 01:06:40'),(1284,168,'Muskingum','2011-06-01 01:06:40'),(1285,168,'Noble','2011-06-01 01:06:40'),(1286,168,'Belmont','2011-06-01 01:06:40'),(1287,168,'Monroe','2011-06-01 01:06:40'),(1288,168,'Guernsey','2011-06-01 01:06:40'),(1289,168,'Morgan','2011-06-01 01:06:40'),(1290,168,'Coshocton','2011-06-01 01:06:40'),(1291,168,'Tuscarawas','2011-06-01 01:06:40'),(1292,168,'Jefferson','2011-06-01 01:06:40'),(1293,168,'Harrison','2011-06-01 01:06:40'),(1294,168,'Columbiana','2011-06-01 01:06:40'),(1295,168,'Lorain','2011-06-01 01:06:40'),(1296,168,'Ashtabula','2011-06-01 01:06:40'),(1297,168,'Cuyahoga','2011-06-01 01:06:40'),(1298,168,'Geauga','2011-06-01 01:06:40'),(1299,168,'Lake','2011-06-01 01:06:40'),(1300,168,'Summit','2011-06-01 01:06:40'),(1301,168,'Portage','2011-06-01 01:06:40'),(1302,168,'Medina','2011-06-01 01:06:40'),(1303,168,'Wayne','2011-06-01 01:06:40'),(1304,168,'Mahoning','2011-06-01 01:06:40'),(1305,168,'Trumbull','2011-06-01 01:06:40'),(1306,168,'Stark','2011-06-01 01:06:40'),(1307,168,'Carroll','2011-06-01 01:06:40'),(1308,168,'Holmes','2011-06-01 01:06:40'),(1309,168,'Seneca','2011-06-01 01:06:40'),(1310,168,'Hancock','2011-06-01 01:06:40'),(1311,168,'Ashland','2011-06-01 01:06:40'),(1312,168,'Huron','2011-06-01 01:06:40'),(1313,168,'Richland','2011-06-01 01:06:40'),(1314,168,'Crawford','2011-06-01 01:06:40'),(1315,168,'Hamilton','2011-06-01 01:06:40'),(1316,168,'Butler','2011-06-01 01:06:40'),(1317,168,'Warren','2011-06-01 01:06:40'),(1318,168,'Preble','2011-06-01 01:06:40'),(1319,168,'Brown','2011-06-01 01:06:40'),(1320,168,'Clermont','2011-06-01 01:06:40'),(1321,168,'Adams','2011-06-01 01:06:40'),(1322,168,'Clinton','2011-06-01 01:06:40'),(1323,168,'Highland','2011-06-01 01:06:40'),(1324,168,'Greene','2011-06-01 01:06:40'),(1325,168,'Shelby','2011-06-01 01:06:40'),(1326,168,'Darke','2011-06-01 01:06:40'),(1327,168,'Miami','2011-06-01 01:06:40'),(1328,168,'Montgomery','2011-06-01 01:06:40'),(1329,168,'Mercer','2011-06-01 01:06:40'),(1330,168,'Pike','2011-06-01 01:06:40'),(1331,168,'Gallia','2011-06-01 01:06:40'),(1332,168,'Lawrence','2011-06-01 01:06:40'),(1333,168,'Jackson','2011-06-01 01:06:40'),(1334,168,'Vinton','2011-06-01 01:06:40'),(1335,168,'Scioto','2011-06-01 01:06:40'),(1336,168,'Athens','2011-06-01 01:06:40'),(1337,168,'Washington','2011-06-01 01:06:40'),(1338,168,'Meigs','2011-06-01 01:06:40'),(1339,168,'Allen','2011-06-01 01:06:40'),(1340,168,'Auglaize','2011-06-01 01:06:40'),(1341,168,'Paulding','2011-06-01 01:06:40'),(1342,168,'Putnam','2011-06-01 01:06:40'),(1343,168,'Van Wert','2011-06-01 01:06:40'),(1344,145,'Madison','2011-06-01 01:06:40'),(1345,145,'Hamilton','2011-06-01 01:06:40'),(1346,145,'Clinton','2011-06-01 01:06:40'),(1347,145,'Hancock','2011-06-01 01:06:40'),(1348,145,'Tipton','2011-06-01 01:06:40'),(1349,145,'Boone','2011-06-01 01:06:40'),(1350,145,'Hendricks','2011-06-01 01:06:40'),(1351,145,'Rush','2011-06-01 01:06:40'),(1352,145,'Putnam','2011-06-01 01:06:40'),(1353,145,'Johnson','2011-06-01 01:06:40'),(1354,145,'Marion','2011-06-01 01:06:40'),(1355,145,'Shelby','2011-06-01 01:06:40'),(1356,145,'Morgan','2011-06-01 01:06:40'),(1357,145,'Fayette','2011-06-01 01:06:40'),(1358,145,'Henry','2011-06-01 01:06:40'),(1359,145,'Brown','2011-06-01 01:06:40'),(1360,145,'Porter','2011-06-01 01:06:40'),(1361,145,'Lake','2011-06-01 01:06:40'),(1362,145,'Jasper','2011-06-01 01:06:40'),(1363,145,'La Porte','2011-06-01 01:06:40'),(1364,145,'Newton','2011-06-01 01:06:40'),(1365,145,'Starke','2011-06-01 01:06:40'),(1366,145,'Marshall','2011-06-01 01:06:40'),(1367,145,'Kosciusko','2011-06-01 01:06:40'),(1368,145,'Elkhart','2011-06-01 01:06:40'),(1369,145,'St Joseph','2011-06-01 01:06:40'),(1370,145,'Lagrange','2011-06-01 01:06:40'),(1371,145,'Noble','2011-06-01 01:06:40'),(1372,145,'Huntington','2011-06-01 01:06:40'),(1373,145,'Steuben','2011-06-01 01:06:40'),(1374,145,'Allen','2011-06-01 01:06:40'),(1375,145,'De Kalb','2011-06-01 01:06:40'),(1376,145,'Adams','2011-06-01 01:06:40'),(1377,145,'Wells','2011-06-01 01:06:40'),(1378,145,'Whitley','2011-06-01 01:06:40'),(1379,145,'Howard','2011-06-01 01:06:40'),(1380,145,'Fulton','2011-06-01 01:06:40'),(1381,145,'Miami','2011-06-01 01:06:40'),(1382,145,'Carroll','2011-06-01 01:06:40'),(1383,145,'Grant','2011-06-01 01:06:40'),(1384,145,'Cass','2011-06-01 01:06:40'),(1385,145,'Wabash','2011-06-01 01:06:40'),(1386,145,'Pulaski','2011-06-01 01:06:40'),(1387,145,'Dearborn','2011-06-01 01:06:40'),(1388,145,'Union','2011-06-01 01:06:40'),(1389,145,'Ripley','2011-06-01 01:06:40'),(1390,145,'Franklin','2011-06-01 01:06:40'),(1391,145,'Switzerland','2011-06-01 01:06:40'),(1392,145,'Ohio','2011-06-01 01:06:40'),(1393,145,'Scott','2011-06-01 01:06:40'),(1394,145,'Clark','2011-06-01 01:06:40'),(1395,145,'Harrison','2011-06-01 01:06:40'),(1396,145,'Washington','2011-06-01 01:06:40'),(1397,145,'Crawford','2011-06-01 01:06:40'),(1398,145,'Floyd','2011-06-01 01:06:40'),(1399,145,'Bartholomew','2011-06-01 01:06:40'),(1400,145,'Jackson','2011-06-01 01:06:40'),(1401,145,'Jennings','2011-06-01 01:06:40'),(1402,145,'Jefferson','2011-06-01 01:06:40'),(1403,145,'Decatur','2011-06-01 01:06:40'),(1404,145,'Delaware','2011-06-01 01:06:40'),(1405,145,'Wayne','2011-06-01 01:06:40'),(1406,145,'Jay','2011-06-01 01:06:40'),(1407,145,'Randolph','2011-06-01 01:06:40'),(1408,145,'Blackford','2011-06-01 01:06:40'),(1409,145,'Monroe','2011-06-01 01:06:40'),(1410,145,'Lawrence','2011-06-01 01:06:40'),(1411,145,'Greene','2011-06-01 01:06:40'),(1412,145,'Owen','2011-06-01 01:06:40'),(1413,145,'Orange','2011-06-01 01:06:40'),(1414,145,'Daviess','2011-06-01 01:06:40'),(1415,145,'Knox','2011-06-01 01:06:40'),(1416,145,'Dubois','2011-06-01 01:06:40'),(1417,145,'Perry','2011-06-01 01:06:40'),(1418,145,'Martin','2011-06-01 01:06:40'),(1419,145,'Spencer','2011-06-01 01:06:40'),(1420,145,'Pike','2011-06-01 01:06:40'),(1421,145,'Warrick','2011-06-01 01:06:40'),(1422,145,'Posey','2011-06-01 01:06:40'),(1423,145,'Vanderburgh','2011-06-01 01:06:40'),(1424,145,'Gibson','2011-06-01 01:06:40'),(1425,145,'Vigo','2011-06-01 01:06:40'),(1426,145,'Parke','2011-06-01 01:06:40'),(1427,145,'Vermillion','2011-06-01 01:06:40'),(1428,145,'Clay','2011-06-01 01:06:40'),(1429,145,'Sullivan','2011-06-01 01:06:40'),(1430,145,'Tippecanoe','2011-06-01 01:06:40'),(1431,145,'Montgomery','2011-06-01 01:06:40'),(1432,145,'Benton','2011-06-01 01:06:40'),(1433,145,'Fountain','2011-06-01 01:06:40'),(1434,145,'White','2011-06-01 01:06:40'),(1435,145,'Warren','2011-06-01 01:06:40'),(1436,154,'Saint Clair','2011-06-01 01:06:40'),(1437,154,'Lapeer','2011-06-01 01:06:40'),(1438,154,'Macomb','2011-06-01 01:06:40'),(1439,154,'Oakland','2011-06-01 01:06:40'),(1440,154,'Wayne','2011-06-01 01:06:40'),(1441,154,'Washtenaw','2011-06-01 01:06:40'),(1442,154,'Monroe','2011-06-01 01:06:40'),(1443,154,'Livingston','2011-06-01 01:06:40'),(1444,154,'Sanilac','2011-06-01 01:06:40'),(1445,154,'Genesee','2011-06-01 01:06:40'),(1446,154,'Huron','2011-06-01 01:06:40'),(1447,154,'Shiawassee','2011-06-01 01:06:40'),(1448,154,'Saginaw','2011-06-01 01:06:40'),(1449,154,'Tuscola','2011-06-01 01:06:40'),(1450,154,'Ogemaw','2011-06-01 01:06:40'),(1451,154,'Bay','2011-06-01 01:06:40'),(1452,154,'Gladwin','2011-06-01 01:06:40'),(1453,154,'Gratiot','2011-06-01 01:06:40'),(1454,154,'Clare','2011-06-01 01:06:40'),(1455,154,'Midland','2011-06-01 01:06:40'),(1456,154,'Oscoda','2011-06-01 01:06:40'),(1457,154,'Roscommon','2011-06-01 01:06:40'),(1458,154,'Arenac','2011-06-01 01:06:40'),(1459,154,'Alcona','2011-06-01 01:06:40'),(1460,154,'Iosco','2011-06-01 01:06:40'),(1461,154,'Isabella','2011-06-01 01:06:40'),(1462,154,'Ingham','2011-06-01 01:06:40'),(1463,154,'Clinton','2011-06-01 01:06:40'),(1464,154,'Ionia','2011-06-01 01:06:40'),(1465,154,'Montcalm','2011-06-01 01:06:40'),(1466,154,'Eaton','2011-06-01 01:06:40'),(1467,154,'Barry','2011-06-01 01:06:40'),(1468,154,'Kalamazoo','2011-06-01 01:06:40'),(1469,154,'Allegan','2011-06-01 01:06:40'),(1470,154,'Calhoun','2011-06-01 01:06:40'),(1471,154,'Van Buren','2011-06-01 01:06:40'),(1472,154,'Berrien','2011-06-01 01:06:40'),(1473,154,'Branch','2011-06-01 01:06:40'),(1474,154,'Saint Joseph','2011-06-01 01:06:40'),(1475,154,'Cass','2011-06-01 01:06:40'),(1476,154,'Jackson','2011-06-01 01:06:40'),(1477,154,'Lenawee','2011-06-01 01:06:40'),(1478,154,'Hillsdale','2011-06-01 01:06:40'),(1479,154,'Kent','2011-06-01 01:06:40'),(1480,154,'Muskegon','2011-06-01 01:06:40'),(1481,154,'Lake','2011-06-01 01:06:40'),(1482,154,'Mecosta','2011-06-01 01:06:40'),(1483,154,'Newaygo','2011-06-01 01:06:40'),(1484,154,'Ottawa','2011-06-01 01:06:40'),(1485,154,'Mason','2011-06-01 01:06:40'),(1486,154,'Oceana','2011-06-01 01:06:40'),(1487,154,'Wexford','2011-06-01 01:06:40'),(1488,154,'Grand Traverse','2011-06-01 01:06:40'),(1489,154,'Antrim','2011-06-01 01:06:40'),(1490,154,'Manistee','2011-06-01 01:06:40'),(1491,154,'Benzie','2011-06-01 01:06:40'),(1492,154,'Leelanau','2011-06-01 01:06:40'),(1493,154,'Osceola','2011-06-01 01:06:40'),(1494,154,'Missaukee','2011-06-01 01:06:40'),(1495,154,'Kalkaska','2011-06-01 01:06:40'),(1496,154,'Cheboygan','2011-06-01 01:06:40'),(1497,154,'Emmet','2011-06-01 01:06:40'),(1498,154,'Alpena','2011-06-01 01:06:40'),(1499,154,'Montmorency','2011-06-01 01:06:40'),(1500,154,'Chippewa','2011-06-01 01:06:40'),(1501,154,'Charlevoix','2011-06-01 01:06:40'),(1502,154,'Mackinac','2011-06-01 01:06:40'),(1503,154,'Otsego','2011-06-01 01:06:40'),(1504,154,'Crawford','2011-06-01 01:06:40'),(1505,154,'Presque Isle','2011-06-01 01:06:40'),(1506,154,'Dickinson','2011-06-01 01:06:40'),(1507,154,'Keweenaw','2011-06-01 01:06:40'),(1508,154,'Alger','2011-06-01 01:06:40'),(1509,154,'Delta','2011-06-01 01:06:40'),(1510,154,'Marquette','2011-06-01 01:06:40'),(1511,154,'Menominee','2011-06-01 01:06:40'),(1512,154,'Schoolcraft','2011-06-01 01:06:40'),(1513,154,'Luce','2011-06-01 01:06:40'),(1514,154,'Iron','2011-06-01 01:06:40'),(1515,154,'Houghton','2011-06-01 01:06:40'),(1516,154,'Baraga','2011-06-01 01:06:40'),(1517,154,'Ontonagon','2011-06-01 01:06:40'),(1518,154,'Gogebic','2011-06-01 01:06:40'),(1519,146,'Warren','2011-06-01 01:06:40'),(1520,146,'Adair','2011-06-01 01:06:40'),(1521,146,'Dallas','2011-06-01 01:06:40'),(1522,146,'Marshall','2011-06-01 01:06:40'),(1523,146,'Hardin','2011-06-01 01:06:40'),(1524,146,'Polk','2011-06-01 01:06:40'),(1525,146,'Wayne','2011-06-01 01:06:40'),(1526,146,'Story','2011-06-01 01:06:40'),(1527,146,'Cass','2011-06-01 01:06:40'),(1528,146,'Audubon','2011-06-01 01:06:40'),(1529,146,'Guthrie','2011-06-01 01:06:40'),(1530,146,'Mahaska','2011-06-01 01:06:40'),(1531,146,'Jasper','2011-06-01 01:06:40'),(1532,146,'Boone','2011-06-01 01:06:40'),(1533,146,'Madison','2011-06-01 01:06:40'),(1534,146,'Hamilton','2011-06-01 01:06:40'),(1535,146,'Franklin','2011-06-01 01:06:40'),(1536,146,'Marion','2011-06-01 01:06:40'),(1537,146,'Lucas','2011-06-01 01:06:40'),(1538,146,'Greene','2011-06-01 01:06:40'),(1539,146,'Carroll','2011-06-01 01:06:40'),(1540,146,'Decatur','2011-06-01 01:06:40'),(1541,146,'Wright','2011-06-01 01:06:40'),(1542,146,'Ringgold','2011-06-01 01:06:40'),(1543,146,'Keokuk','2011-06-01 01:06:40'),(1544,146,'Poweshiek','2011-06-01 01:06:40'),(1545,146,'Union','2011-06-01 01:06:40'),(1546,146,'Monroe','2011-06-01 01:06:40'),(1547,146,'Tama','2011-06-01 01:06:40'),(1548,146,'Clarke','2011-06-01 01:06:40'),(1549,146,'Cerro Gordo','2011-06-01 01:06:40'),(1550,146,'Hancock','2011-06-01 01:06:40'),(1551,146,'Winnebago','2011-06-01 01:06:40'),(1552,146,'Mitchell','2011-06-01 01:06:40'),(1553,146,'Worth','2011-06-01 01:06:40'),(1554,146,'Floyd','2011-06-01 01:06:40'),(1555,146,'Kossuth','2011-06-01 01:06:40'),(1556,146,'Howard','2011-06-01 01:06:40'),(1557,146,'Webster','2011-06-01 01:06:40'),(1558,146,'Buena Vista','2011-06-01 01:06:40'),(1559,146,'Emmet','2011-06-01 01:06:40'),(1560,146,'Palo Alto','2011-06-01 01:06:40'),(1561,146,'Humboldt','2011-06-01 01:06:40'),(1562,146,'Sac','2011-06-01 01:06:40'),(1563,146,'Calhoun','2011-06-01 01:06:40'),(1564,146,'Pocahontas','2011-06-01 01:06:40'),(1565,146,'Butler','2011-06-01 01:06:40'),(1566,146,'Chickasaw','2011-06-01 01:06:40'),(1567,146,'Fayette','2011-06-01 01:06:40'),(1568,146,'Buchanan','2011-06-01 01:06:40'),(1569,146,'Grundy','2011-06-01 01:06:40'),(1570,146,'Black Hawk','2011-06-01 01:06:40'),(1571,146,'Bremer','2011-06-01 01:06:40'),(1572,146,'Delaware','2011-06-01 01:06:40'),(1573,146,'Taylor','2011-06-01 01:06:40'),(1574,146,'Adams','2011-06-01 01:06:40'),(1575,146,'Montgomery','2011-06-01 01:06:40'),(1576,146,'Plymouth','2011-06-01 01:06:40'),(1577,146,'Sioux','2011-06-01 01:06:40'),(1578,146,'Woodbury','2011-06-01 01:06:40'),(1579,146,'Cherokee','2011-06-01 01:06:40'),(1580,146,'Ida','2011-06-01 01:06:40'),(1581,146,'Obrien','2011-06-01 01:06:40'),(1582,146,'Monona','2011-06-01 01:06:40'),(1583,146,'Clay','2011-06-01 01:06:40'),(1584,146,'Lyon','2011-06-01 01:06:40'),(1585,146,'Osceola','2011-06-01 01:06:40'),(1586,146,'Dickinson','2011-06-01 01:06:40'),(1587,146,'Crawford','2011-06-01 01:06:40'),(1588,146,'Shelby','2011-06-01 01:06:40'),(1589,146,'Pottawattamie','2011-06-01 01:06:40'),(1590,146,'Harrison','2011-06-01 01:06:40'),(1591,146,'Mills','2011-06-01 01:06:40'),(1592,146,'Page','2011-06-01 01:06:40'),(1593,146,'Fremont','2011-06-01 01:06:40'),(1594,146,'Dubuque','2011-06-01 01:06:40'),(1595,146,'Jackson','2011-06-01 01:06:40'),(1596,146,'Clinton','2011-06-01 01:06:40'),(1597,146,'Clayton','2011-06-01 01:06:40'),(1598,146,'Winneshiek','2011-06-01 01:06:40'),(1599,146,'Allamakee','2011-06-01 01:06:40'),(1600,146,'Washington','2011-06-01 01:06:40'),(1601,146,'Linn','2011-06-01 01:06:40'),(1602,146,'Iowa','2011-06-01 01:06:40'),(1603,146,'Jones','2011-06-01 01:06:40'),(1604,146,'Benton','2011-06-01 01:06:40'),(1605,146,'Cedar','2011-06-01 01:06:40'),(1606,146,'Johnson','2011-06-01 01:06:40'),(1607,146,'Wapello','2011-06-01 01:06:40'),(1608,146,'Jefferson','2011-06-01 01:06:40'),(1609,146,'Van Buren','2011-06-01 01:06:40'),(1610,146,'Davis','2011-06-01 01:06:40'),(1611,146,'Appanoose','2011-06-01 01:06:40'),(1612,146,'Des Moines','2011-06-01 01:06:40'),(1613,146,'Lee','2011-06-01 01:06:40'),(1614,146,'Henry','2011-06-01 01:06:40'),(1615,146,'Louisa','2011-06-01 01:06:40'),(1616,146,'Muscatine','2011-06-01 01:06:40'),(1617,146,'Scott','2011-06-01 01:06:40'),(1618,185,'Sheboygan','2011-06-01 01:06:40'),(1619,185,'Washington','2011-06-01 01:06:40'),(1620,185,'Dodge','2011-06-01 01:06:40'),(1621,185,'Ozaukee','2011-06-01 01:06:40'),(1622,185,'Waukesha','2011-06-01 01:06:40'),(1623,185,'Fond Du Lac','2011-06-01 01:06:40'),(1624,185,'Calumet','2011-06-01 01:06:40'),(1625,185,'Manitowoc','2011-06-01 01:06:40'),(1626,185,'Jefferson','2011-06-01 01:06:40'),(1627,185,'Kenosha','2011-06-01 01:06:40'),(1628,185,'Racine','2011-06-01 01:06:40'),(1629,185,'Milwaukee','2011-06-01 01:06:40'),(1630,185,'Walworth','2011-06-01 01:06:40'),(1631,185,'Rock','2011-06-01 01:06:40'),(1632,185,'Green','2011-06-01 01:06:40'),(1633,185,'Iowa','2011-06-01 01:06:40'),(1634,185,'Lafayette','2011-06-01 01:06:40'),(1635,185,'Dane','2011-06-01 01:06:40'),(1636,185,'Grant','2011-06-01 01:06:40'),(1637,185,'Richland','2011-06-01 01:06:40'),(1638,185,'Columbia','2011-06-01 01:06:40'),(1639,185,'Sauk','2011-06-01 01:06:40'),(1640,185,'Crawford','2011-06-01 01:06:40'),(1641,185,'Adams','2011-06-01 01:06:40'),(1642,185,'Marquette','2011-06-01 01:06:40'),(1643,185,'Green Lake','2011-06-01 01:06:40'),(1644,185,'Juneau','2011-06-01 01:06:40'),(1645,185,'Polk','2011-06-01 01:06:40'),(1646,185,'Saint Croix','2011-06-01 01:06:40'),(1647,185,'Pierce','2011-06-01 01:06:40'),(1648,185,'Oconto','2011-06-01 01:06:40'),(1649,185,'Marinette','2011-06-01 01:06:40'),(1650,185,'Forest','2011-06-01 01:06:40'),(1651,185,'Outagamie','2011-06-01 01:06:40'),(1652,185,'Shawano','2011-06-01 01:06:40'),(1653,185,'Brown','2011-06-01 01:06:40'),(1654,185,'Florence','2011-06-01 01:06:40'),(1655,185,'Menominee','2011-06-01 01:06:40'),(1656,185,'Kewaunee','2011-06-01 01:06:40'),(1657,185,'Door','2011-06-01 01:06:40'),(1658,185,'Marathon','2011-06-01 01:06:40'),(1659,185,'Wood','2011-06-01 01:06:40'),(1660,185,'Clark','2011-06-01 01:06:40'),(1661,185,'Portage','2011-06-01 01:06:40'),(1662,185,'Langlade','2011-06-01 01:06:40'),(1663,185,'Taylor','2011-06-01 01:06:40'),(1664,185,'Lincoln','2011-06-01 01:06:40'),(1665,185,'Price','2011-06-01 01:06:40'),(1666,185,'Oneida','2011-06-01 01:06:40'),(1667,185,'Vilas','2011-06-01 01:06:40'),(1668,185,'Ashland','2011-06-01 01:06:40'),(1669,185,'Iron','2011-06-01 01:06:40'),(1670,185,'Rusk','2011-06-01 01:06:40'),(1671,185,'La Crosse','2011-06-01 01:06:40'),(1672,185,'Buffalo','2011-06-01 01:06:40'),(1673,185,'Jackson','2011-06-01 01:06:40'),(1674,185,'Trempealeau','2011-06-01 01:06:40'),(1675,185,'Monroe','2011-06-01 01:06:40'),(1676,185,'Vernon','2011-06-01 01:06:40'),(1677,185,'Eau Claire','2011-06-01 01:06:40'),(1678,185,'Pepin','2011-06-01 01:06:40'),(1679,185,'Chippewa','2011-06-01 01:06:40'),(1680,185,'Dunn','2011-06-01 01:06:40'),(1681,185,'Barron','2011-06-01 01:06:40'),(1682,185,'Washburn','2011-06-01 01:06:40'),(1683,185,'Bayfield','2011-06-01 01:06:40'),(1684,185,'Douglas','2011-06-01 01:06:40'),(1685,185,'Sawyer','2011-06-01 01:06:40'),(1686,185,'Burnett','2011-06-01 01:06:40'),(1687,185,'Winnebago','2011-06-01 01:06:40'),(1688,185,'Waupaca','2011-06-01 01:06:40'),(1689,185,'Waushara','2011-06-01 01:06:40'),(1690,155,'Washington','2011-06-01 01:06:40'),(1691,155,'Chisago','2011-06-01 01:06:40'),(1692,155,'Anoka','2011-06-01 01:06:40'),(1693,155,'Isanti','2011-06-01 01:06:40'),(1694,155,'Pine','2011-06-01 01:06:40'),(1695,155,'Goodhue','2011-06-01 01:06:40'),(1696,155,'Dakota','2011-06-01 01:06:40'),(1697,155,'Rice','2011-06-01 01:06:40'),(1698,155,'Scott','2011-06-01 01:06:40'),(1699,155,'Wabasha','2011-06-01 01:06:40'),(1700,155,'Steele','2011-06-01 01:06:40'),(1701,155,'Kanabec','2011-06-01 01:06:40'),(1702,155,'Ramsey','2011-06-01 01:06:40'),(1703,155,'Hennepin','2011-06-01 01:06:40'),(1704,155,'Wright','2011-06-01 01:06:40'),(1705,155,'Sibley','2011-06-01 01:06:40'),(1706,155,'Sherburne','2011-06-01 01:06:40'),(1707,155,'Renville','2011-06-01 01:06:40'),(1708,155,'McLeod','2011-06-01 01:06:40'),(1709,155,'Carver','2011-06-01 01:06:40'),(1710,155,'Meeker','2011-06-01 01:06:40'),(1711,155,'Stearns','2011-06-01 01:06:40'),(1712,155,'Mille Lacs','2011-06-01 01:06:40'),(1713,155,'Lake','2011-06-01 01:06:40'),(1714,155,'Saint Louis','2011-06-01 01:06:40'),(1715,155,'Cook','2011-06-01 01:06:40'),(1716,155,'Carlton','2011-06-01 01:06:40'),(1717,155,'Itasca','2011-06-01 01:06:40'),(1718,155,'Aitkin','2011-06-01 01:06:40'),(1719,155,'Olmsted','2011-06-01 01:06:40'),(1720,155,'Winona','2011-06-01 01:06:40'),(1721,155,'Houston','2011-06-01 01:06:40'),(1722,155,'Fillmore','2011-06-01 01:06:40'),(1723,155,'Dodge','2011-06-01 01:06:40'),(1724,155,'Blue Earth','2011-06-01 01:06:40'),(1725,155,'Nicollet','2011-06-01 01:06:40'),(1726,155,'Freeborn','2011-06-01 01:06:40'),(1727,155,'Faribault','2011-06-01 01:06:40'),(1728,155,'Le Sueur','2011-06-01 01:06:40'),(1729,155,'Brown','2011-06-01 01:06:40'),(1730,155,'Watonwan','2011-06-01 01:06:40'),(1731,155,'Martin','2011-06-01 01:06:40'),(1732,155,'Waseca','2011-06-01 01:06:40'),(1733,155,'Redwood','2011-06-01 01:06:40'),(1734,155,'Cottonwood','2011-06-01 01:06:40'),(1735,155,'Nobles','2011-06-01 01:06:40'),(1736,155,'Jackson','2011-06-01 01:06:40'),(1737,155,'Lincoln','2011-06-01 01:06:40'),(1738,155,'Murray','2011-06-01 01:06:40'),(1739,155,'Lyon','2011-06-01 01:06:40'),(1740,155,'Rock','2011-06-01 01:06:40'),(1741,155,'Pipestone','2011-06-01 01:06:40'),(1742,155,'Kandiyohi','2011-06-01 01:06:40'),(1743,155,'Stevens','2011-06-01 01:06:40'),(1744,155,'Swift','2011-06-01 01:06:40'),(1745,155,'Big Stone','2011-06-01 01:06:40'),(1746,155,'Lac Qui Parle','2011-06-01 01:06:40'),(1747,155,'Traverse','2011-06-01 01:06:40'),(1748,155,'Yellow Medicine','2011-06-01 01:06:40'),(1749,155,'Chippewa','2011-06-01 01:06:40'),(1750,155,'Grant','2011-06-01 01:06:40'),(1751,155,'Douglas','2011-06-01 01:06:40'),(1752,155,'Morrison','2011-06-01 01:06:40'),(1753,155,'Todd','2011-06-01 01:06:40'),(1754,155,'Pope','2011-06-01 01:06:40'),(1755,155,'Otter Tail','2011-06-01 01:06:40'),(1756,155,'Benton','2011-06-01 01:06:40'),(1757,155,'Crow Wing','2011-06-01 01:06:40'),(1758,155,'Cass','2011-06-01 01:06:40'),(1759,155,'Hubbard','2011-06-01 01:06:40'),(1760,155,'Wadena','2011-06-01 01:06:40'),(1761,155,'Becker','2011-06-01 01:06:40'),(1762,155,'Norman','2011-06-01 01:06:40'),(1763,155,'Clay','2011-06-01 01:06:40'),(1764,155,'Mahnomen','2011-06-01 01:06:40'),(1765,155,'Polk','2011-06-01 01:06:40'),(1766,155,'Wilkin','2011-06-01 01:06:40'),(1767,155,'Beltrami','2011-06-01 01:06:40'),(1768,155,'Clearwater','2011-06-01 01:06:40'),(1769,155,'Lake of the Woods','2011-06-01 01:06:40'),(1770,155,'Koochiching','2011-06-01 01:06:40'),(1771,155,'Roseau','2011-06-01 01:06:40'),(1772,155,'Pennington','2011-06-01 01:06:40'),(1773,155,'Marshall','2011-06-01 01:06:40'),(1774,155,'Red Lake','2011-06-01 01:06:40'),(1775,155,'Kittson','2011-06-01 01:06:40'),(1776,176,'Union','2011-06-01 01:06:40'),(1777,176,'Brookings','2011-06-01 01:06:40'),(1778,176,'Minnehaha','2011-06-01 01:06:40'),(1779,176,'Clay','2011-06-01 01:06:40'),(1780,176,'McCook','2011-06-01 01:06:40'),(1781,176,'Lincoln','2011-06-01 01:06:40'),(1782,176,'Turner','2011-06-01 01:06:40'),(1783,176,'Lake','2011-06-01 01:06:40'),(1784,176,'Moody','2011-06-01 01:06:40'),(1785,176,'Hutchinson','2011-06-01 01:06:40'),(1786,176,'Yankton','2011-06-01 01:06:40'),(1787,176,'Kingsbury','2011-06-01 01:06:40'),(1788,176,'Bon Homme','2011-06-01 01:06:40'),(1789,176,'Codington','2011-06-01 01:06:40'),(1790,176,'Deuel','2011-06-01 01:06:40'),(1791,176,'Grant','2011-06-01 01:06:40'),(1792,176,'Clark','2011-06-01 01:06:40'),(1793,176,'Day','2011-06-01 01:06:40'),(1794,176,'Hamlin','2011-06-01 01:06:40'),(1795,176,'Roberts','2011-06-01 01:06:40'),(1796,176,'Marshall','2011-06-01 01:06:40'),(1797,176,'Davison','2011-06-01 01:06:40'),(1798,176,'Hanson','2011-06-01 01:06:40'),(1799,176,'Jerauld','2011-06-01 01:06:40'),(1800,176,'Douglas','2011-06-01 01:06:40'),(1801,176,'Sanborn','2011-06-01 01:06:40'),(1802,176,'Gregory','2011-06-01 01:06:40'),(1803,176,'Miner','2011-06-01 01:06:40'),(1804,176,'Beadle','2011-06-01 01:06:40'),(1805,176,'Brule','2011-06-01 01:06:40'),(1806,176,'Charles Mix','2011-06-01 01:06:40'),(1807,176,'Buffalo','2011-06-01 01:06:40'),(1808,176,'Hyde','2011-06-01 01:06:40'),(1809,176,'Hand','2011-06-01 01:06:40'),(1810,176,'Lyman','2011-06-01 01:06:40'),(1811,176,'Aurora','2011-06-01 01:06:40'),(1812,176,'Brown','2011-06-01 01:06:40'),(1813,176,'Walworth','2011-06-01 01:06:40'),(1814,176,'Spink','2011-06-01 01:06:40'),(1815,176,'Edmunds','2011-06-01 01:06:40'),(1816,176,'Faulk','2011-06-01 01:06:40'),(1817,176,'McPherson','2011-06-01 01:06:40'),(1818,176,'Potter','2011-06-01 01:06:40'),(1819,176,'Hughes','2011-06-01 01:06:40'),(1820,176,'Sully','2011-06-01 01:06:40'),(1821,176,'Jackson','2011-06-01 01:06:40'),(1822,176,'Tripp','2011-06-01 01:06:40'),(1823,176,'Jones','2011-06-01 01:06:40'),(1824,176,'Stanley','2011-06-01 01:06:40'),(1825,176,'Bennett','2011-06-01 01:06:40'),(1826,176,'Haakon','2011-06-01 01:06:40'),(1827,176,'Todd','2011-06-01 01:06:40'),(1828,176,'Mellette','2011-06-01 01:06:40'),(1829,176,'Perkins','2011-06-01 01:06:40'),(1830,176,'Corson','2011-06-01 01:06:40'),(1831,176,'Ziebach','2011-06-01 01:06:40'),(1832,176,'Dewey','2011-06-01 01:06:40'),(1833,176,'Meade','2011-06-01 01:06:40'),(1834,176,'Campbell','2011-06-01 01:06:40'),(1835,176,'Harding','2011-06-01 01:06:40'),(1836,176,'Pennington','2011-06-01 01:06:40'),(1837,176,'Shannon','2011-06-01 01:06:40'),(1838,176,'Butte','2011-06-01 01:06:40'),(1839,176,'Custer','2011-06-01 01:06:40'),(1840,176,'Lawrence','2011-06-01 01:06:40'),(1841,176,'Fall River','2011-06-01 01:06:40'),(1842,166,'Richland','2011-06-01 01:06:40'),(1843,166,'Cass','2011-06-01 01:06:40'),(1844,166,'Traill','2011-06-01 01:06:40'),(1845,166,'Sargent','2011-06-01 01:06:40'),(1846,166,'Ransom','2011-06-01 01:06:40'),(1847,166,'Barnes','2011-06-01 01:06:40'),(1848,166,'Steele','2011-06-01 01:06:40'),(1849,166,'Grand Forks','2011-06-01 01:06:40'),(1850,166,'Walsh','2011-06-01 01:06:40'),(1851,166,'Nelson','2011-06-01 01:06:40'),(1852,166,'Pembina','2011-06-01 01:06:40'),(1853,166,'Cavalier','2011-06-01 01:06:40'),(1854,166,'Ramsey','2011-06-01 01:06:40'),(1855,166,'Rolette','2011-06-01 01:06:40'),(1856,166,'Pierce','2011-06-01 01:06:40'),(1857,166,'Towner','2011-06-01 01:06:40'),(1858,166,'Bottineau','2011-06-01 01:06:40'),(1859,166,'Wells','2011-06-01 01:06:40'),(1860,166,'Benson','2011-06-01 01:06:40'),(1861,166,'Eddy','2011-06-01 01:06:40'),(1862,166,'Stutsman','2011-06-01 01:06:40'),(1863,166,'McIntosh','2011-06-01 01:06:40'),(1864,166,'Lamoure','2011-06-01 01:06:40'),(1865,166,'Griggs','2011-06-01 01:06:40'),(1866,166,'Foster','2011-06-01 01:06:40'),(1867,166,'Kidder','2011-06-01 01:06:40'),(1868,166,'Sheridan','2011-06-01 01:06:40'),(1869,166,'Dickey','2011-06-01 01:06:40'),(1870,166,'Logan','2011-06-01 01:06:40'),(1871,166,'Burleigh','2011-06-01 01:06:40'),(1872,166,'Morton','2011-06-01 01:06:40'),(1873,166,'Mercer','2011-06-01 01:06:40'),(1874,166,'Emmons','2011-06-01 01:06:40'),(1875,166,'Sioux','2011-06-01 01:06:40'),(1876,166,'Grant','2011-06-01 01:06:40'),(1877,166,'Oliver','2011-06-01 01:06:40'),(1878,166,'McLean','2011-06-01 01:06:40'),(1879,166,'Stark','2011-06-01 01:06:40'),(1880,166,'Slope','2011-06-01 01:06:40'),(1881,166,'Golden Valley','2011-06-01 01:06:40'),(1882,166,'Bowman','2011-06-01 01:06:40'),(1883,166,'Dunn','2011-06-01 01:06:40'),(1884,166,'Billings','2011-06-01 01:06:40'),(1885,166,'McKenzie','2011-06-01 01:06:40'),(1886,166,'Adams','2011-06-01 01:06:40'),(1887,166,'Hettinger','2011-06-01 01:06:40'),(1888,166,'Ward','2011-06-01 01:06:40'),(1889,166,'McHenry','2011-06-01 01:06:40'),(1890,166,'Burke','2011-06-01 01:06:40'),(1891,166,'Divide','2011-06-01 01:06:40'),(1892,166,'Renville','2011-06-01 01:06:40'),(1893,166,'Williams','2011-06-01 01:06:40'),(1894,166,'Mountrail','2011-06-01 01:06:40'),(1895,158,'Stillwater','2011-06-01 01:06:40'),(1896,158,'Yellowstone','2011-06-01 01:06:40'),(1897,158,'Rosebud','2011-06-01 01:06:40'),(1898,158,'Carbon','2011-06-01 01:06:40'),(1899,158,'Treasure','2011-06-01 01:06:40'),(1900,158,'Sweet Grass','2011-06-01 01:06:40'),(1901,158,'Big Horn','2011-06-01 01:06:40'),(1902,158,'Park','2011-06-01 01:06:40'),(1903,158,'Fergus','2011-06-01 01:06:40'),(1904,158,'Wheatland','2011-06-01 01:06:40'),(1905,158,'Golden Valley','2011-06-01 01:06:40'),(1906,158,'Meagher','2011-06-01 01:06:40'),(1907,158,'Musselshell','2011-06-01 01:06:40'),(1908,158,'Garfield','2011-06-01 01:06:40'),(1909,158,'Powder River','2011-06-01 01:06:40'),(1910,158,'Petroleum','2011-06-01 01:06:40'),(1911,158,'Roosevelt','2011-06-01 01:06:40'),(1912,158,'Sheridan','2011-06-01 01:06:40'),(1913,158,'McCone','2011-06-01 01:06:40'),(1914,158,'Richland','2011-06-01 01:06:40'),(1915,158,'Daniels','2011-06-01 01:06:40'),(1916,158,'Valley','2011-06-01 01:06:40'),(1917,158,'Dawson','2011-06-01 01:06:40'),(1918,158,'Phillips','2011-06-01 01:06:40'),(1919,158,'Custer','2011-06-01 01:06:40'),(1920,158,'Carter','2011-06-01 01:06:40'),(1921,158,'Fallon','2011-06-01 01:06:40'),(1922,158,'Prairie','2011-06-01 01:06:40'),(1923,158,'Wibaux','2011-06-01 01:06:40'),(1924,158,'Cascade','2011-06-01 01:06:40'),(1925,158,'Lewis And Clark','2011-06-01 01:06:40'),(1926,158,'Pondera','2011-06-01 01:06:40'),(1927,158,'Teton','2011-06-01 01:06:40'),(1928,158,'Chouteau','2011-06-01 01:06:40'),(1929,158,'Toole','2011-06-01 01:06:40'),(1930,158,'Judith Basin','2011-06-01 01:06:40'),(1931,158,'Liberty','2011-06-01 01:06:40'),(1932,158,'Hill','2011-06-01 01:06:40'),(1933,158,'Blaine','2011-06-01 01:06:40'),(1934,158,'Jefferson','2011-06-01 01:06:40'),(1935,158,'Broadwater','2011-06-01 01:06:40'),(1936,158,'Silver Bow','2011-06-01 01:06:40'),(1937,158,'Madison','2011-06-01 01:06:40'),(1938,158,'Deer Lodge','2011-06-01 01:06:40'),(1939,158,'Powell','2011-06-01 01:06:40'),(1940,158,'Gallatin','2011-06-01 01:06:40'),(1941,158,'Beaverhead','2011-06-01 01:06:40'),(1942,158,'Missoula','2011-06-01 01:06:40'),(1943,158,'Mineral','2011-06-01 01:06:40'),(1944,158,'Lake','2011-06-01 01:06:40'),(1945,158,'Ravalli','2011-06-01 01:06:40'),(1946,158,'Sanders','2011-06-01 01:06:40'),(1947,158,'Granite','2011-06-01 01:06:40'),(1948,158,'Flathead','2011-06-01 01:06:40'),(1949,158,'Lincoln','2011-06-01 01:06:40'),(1950,144,'McHenry','2011-06-01 01:06:40'),(1951,144,'Lake','2011-06-01 01:06:40'),(1952,144,'Cook','2011-06-01 01:06:40'),(1953,144,'Du Page','2011-06-01 01:06:40'),(1954,144,'Kane','2011-06-01 01:06:40'),(1955,144,'De Kalb','2011-06-01 01:06:40'),(1956,144,'Ogle','2011-06-01 01:06:40'),(1957,144,'Will','2011-06-01 01:06:40'),(1958,144,'Grundy','2011-06-01 01:06:40'),(1959,144,'Livingston','2011-06-01 01:06:40'),(1960,144,'La Salle','2011-06-01 01:06:40'),(1961,144,'Kendall','2011-06-01 01:06:40'),(1962,144,'Lee','2011-06-01 01:06:40'),(1963,144,'Kankakee','2011-06-01 01:06:40'),(1964,144,'Iroquois','2011-06-01 01:06:40'),(1965,144,'Ford','2011-06-01 01:06:40'),(1966,144,'Vermilion','2011-06-01 01:06:40'),(1967,144,'Champaign','2011-06-01 01:06:40'),(1968,144,'Jo Daviess','2011-06-01 01:06:40'),(1969,144,'Boone','2011-06-01 01:06:40'),(1970,144,'Stephenson','2011-06-01 01:06:40'),(1971,144,'Carroll','2011-06-01 01:06:40'),(1972,144,'Winnebago','2011-06-01 01:06:40'),(1973,144,'Whiteside','2011-06-01 01:06:40'),(1974,144,'Rock Island','2011-06-01 01:06:40'),(1975,144,'Mercer','2011-06-01 01:06:40'),(1976,144,'Henry','2011-06-01 01:06:40'),(1977,144,'Bureau','2011-06-01 01:06:40'),(1978,144,'Putnam','2011-06-01 01:06:40'),(1979,144,'Marshall','2011-06-01 01:06:40'),(1980,144,'Knox','2011-06-01 01:06:40'),(1981,144,'McDonough','2011-06-01 01:06:40'),(1982,144,'Fulton','2011-06-01 01:06:40'),(1983,144,'Warren','2011-06-01 01:06:40'),(1984,144,'Henderson','2011-06-01 01:06:40'),(1985,144,'Stark','2011-06-01 01:06:40'),(1986,144,'Hancock','2011-06-01 01:06:40'),(1987,144,'Peoria','2011-06-01 01:06:40'),(1988,144,'Schuyler','2011-06-01 01:06:40'),(1989,144,'Woodford','2011-06-01 01:06:40'),(1990,144,'Mason','2011-06-01 01:06:40'),(1991,144,'Tazewell','2011-06-01 01:06:40'),(1992,144,'McLean','2011-06-01 01:06:40'),(1993,144,'Logan','2011-06-01 01:06:40'),(1994,144,'Dewitt','2011-06-01 01:06:40'),(1995,144,'Macon','2011-06-01 01:06:40'),(1996,144,'Piatt','2011-06-01 01:06:40'),(1997,144,'Douglas','2011-06-01 01:06:40'),(1998,144,'Coles','2011-06-01 01:06:40'),(1999,144,'Moultrie','2011-06-01 01:06:40'),(2000,144,'Edgar','2011-06-01 01:06:40'),(2001,144,'Shelby','2011-06-01 01:06:40'),(2002,144,'Madison','2011-06-01 01:06:40'),(2003,144,'Calhoun','2011-06-01 01:06:40'),(2004,144,'Macoupin','2011-06-01 01:06:40'),(2005,144,'Fayette','2011-06-01 01:06:40'),(2006,144,'Jersey','2011-06-01 01:06:40'),(2007,144,'Montgomery','2011-06-01 01:06:40'),(2008,144,'Greene','2011-06-01 01:06:40'),(2009,144,'Bond','2011-06-01 01:06:40'),(2010,144,'Saint Clair','2011-06-01 01:06:40'),(2011,144,'Christian','2011-06-01 01:06:40'),(2012,144,'Washington','2011-06-01 01:06:40'),(2013,144,'Clinton','2011-06-01 01:06:40'),(2014,144,'Randolph','2011-06-01 01:06:40'),(2015,144,'Monroe','2011-06-01 01:06:40'),(2016,144,'Perry','2011-06-01 01:06:40'),(2017,144,'Adams','2011-06-01 01:06:40'),(2018,144,'Pike','2011-06-01 01:06:40'),(2019,144,'Brown','2011-06-01 01:06:40'),(2020,144,'Effingham','2011-06-01 01:06:40'),(2021,144,'Wabash','2011-06-01 01:06:40'),(2022,144,'Crawford','2011-06-01 01:06:40'),(2023,144,'Lawrence','2011-06-01 01:06:40'),(2024,144,'Richland','2011-06-01 01:06:40'),(2025,144,'Clark','2011-06-01 01:06:40'),(2026,144,'Cumberland','2011-06-01 01:06:40'),(2027,144,'Jasper','2011-06-01 01:06:40'),(2028,144,'Clay','2011-06-01 01:06:40'),(2029,144,'Wayne','2011-06-01 01:06:40'),(2030,144,'Edwards','2011-06-01 01:06:40'),(2031,144,'Sangamon','2011-06-01 01:06:40'),(2032,144,'Morgan','2011-06-01 01:06:40'),(2033,144,'Scott','2011-06-01 01:06:40'),(2034,144,'Cass','2011-06-01 01:06:40'),(2035,144,'Menard','2011-06-01 01:06:40'),(2036,144,'Marion','2011-06-01 01:06:40'),(2037,144,'Franklin','2011-06-01 01:06:40'),(2038,144,'Jefferson','2011-06-01 01:06:40'),(2039,144,'Hamilton','2011-06-01 01:06:40'),(2040,144,'White','2011-06-01 01:06:40'),(2041,144,'Williamson','2011-06-01 01:06:40'),(2042,144,'Gallatin','2011-06-01 01:06:40'),(2043,144,'Jackson','2011-06-01 01:06:40'),(2044,144,'Union','2011-06-01 01:06:40'),(2045,144,'Johnson','2011-06-01 01:06:40'),(2046,144,'Massac','2011-06-01 01:06:40'),(2047,144,'Alexander','2011-06-01 01:06:40'),(2048,144,'Saline','2011-06-01 01:06:40'),(2049,144,'Hardin','2011-06-01 01:06:40'),(2050,144,'Pope','2011-06-01 01:06:40'),(2051,144,'Pulaski','2011-06-01 01:06:40'),(2052,157,'Saint Louis','2011-06-01 01:06:40'),(2053,157,'Jefferson','2011-06-01 01:06:40'),(2054,157,'Franklin','2011-06-01 01:06:40'),(2055,157,'Saint Francois','2011-06-01 01:06:40'),(2056,157,'Washington','2011-06-01 01:06:40'),(2057,157,'Gasconade','2011-06-01 01:06:40'),(2058,157,'Saint Louis City','2011-06-01 01:06:40'),
   (2059,157,'Saint Charles','2011-06-01 01:06:40'),(2060,157,'Pike','2011-06-01 01:06:40'),(2061,157,'Montgomery','2011-06-01 01:06:40'),(2062,157,'Warren','2011-06-01 01:06:40'),(2063,157,'Lincoln','2011-06-01 01:06:40'),(2064,157,'Audrain','2011-06-01 01:06:40'),(2065,157,'Callaway','2011-06-01 01:06:40'),(2066,157,'Marion','2011-06-01 01:06:40'),(2067,157,'Clark','2011-06-01 01:06:40'),(2068,157,'Macon','2011-06-01 01:06:40'),(2069,157,'Scotland','2011-06-01 01:06:40'),(2070,157,'Shelby','2011-06-01 01:06:40'),(2071,157,'Lewis','2011-06-01 01:06:40'),(2072,157,'Ralls','2011-06-01 01:06:40'),(2073,157,'Knox','2011-06-01 01:06:40'),(2074,157,'Monroe','2011-06-01 01:06:40'),(2075,157,'Adair','2011-06-01 01:06:40'),(2076,157,'Schuyler','2011-06-01 01:06:40'),(2077,157,'Sullivan','2011-06-01 01:06:40'),(2078,157,'Putnam','2011-06-01 01:06:40'),(2079,157,'Linn','2011-06-01 01:06:40'),(2080,157,'Iron','2011-06-01 01:06:40'),(2081,157,'Reynolds','2011-06-01 01:06:40'),(2082,157,'Sainte Genevieve','2011-06-01 01:06:40'),(2083,157,'Wayne','2011-06-01 01:06:40'),(2084,157,'Madison','2011-06-01 01:06:40'),(2085,157,'Bollinger','2011-06-01 01:06:40'),(2086,157,'Cape Girardeau','2011-06-01 01:06:40'),(2087,157,'Stoddard','2011-06-01 01:06:40'),(2088,157,'Perry','2011-06-01 01:06:40'),(2089,157,'Scott','2011-06-01 01:06:40'),(2090,157,'Mississippi','2011-06-01 01:06:40'),(2091,157,'Dunklin','2011-06-01 01:06:40'),(2092,157,'Pemiscot','2011-06-01 01:06:40'),(2093,157,'New Madrid','2011-06-01 01:06:40'),(2094,157,'Butler','2011-06-01 01:06:40'),(2095,157,'Ripley','2011-06-01 01:06:40'),(2096,157,'Carter','2011-06-01 01:06:40'),(2097,157,'Lafayette','2011-06-01 01:06:40'),(2098,157,'Cass','2011-06-01 01:06:40'),(2099,157,'Jackson','2011-06-01 01:06:40'),(2100,157,'Ray','2011-06-01 01:06:40'),(2101,157,'Platte','2011-06-01 01:06:40'),(2102,157,'Johnson','2011-06-01 01:06:40'),(2103,157,'Clay','2011-06-01 01:06:40'),(2104,157,'Buchanan','2011-06-01 01:06:40'),(2105,157,'Gentry','2011-06-01 01:06:40'),(2106,157,'Worth','2011-06-01 01:06:40'),(2107,157,'Andrew','2011-06-01 01:06:40'),(2108,157,'Dekalb','2011-06-01 01:06:40'),(2109,157,'Nodaway','2011-06-01 01:06:40'),(2110,157,'Harrison','2011-06-01 01:06:40'),(2111,157,'Clinton','2011-06-01 01:06:40'),(2112,157,'Holt','2011-06-01 01:06:40'),(2113,157,'Atchison','2011-06-01 01:06:40'),(2114,157,'Livingston','2011-06-01 01:06:40'),(2115,157,'Daviess','2011-06-01 01:06:40'),(2116,157,'Carroll','2011-06-01 01:06:40'),(2117,157,'Caldwell','2011-06-01 01:06:40'),(2118,157,'Grundy','2011-06-01 01:06:40'),(2119,157,'Chariton','2011-06-01 01:06:40'),(2120,157,'Mercer','2011-06-01 01:06:40'),(2121,157,'Bates','2011-06-01 01:06:40'),(2122,157,'Saint Clair','2011-06-01 01:06:40'),(2123,157,'Henry','2011-06-01 01:06:40'),(2124,157,'Vernon','2011-06-01 01:06:40'),(2125,157,'Cedar','2011-06-01 01:06:40'),(2126,157,'Barton','2011-06-01 01:06:40'),(2127,157,'Jasper','2011-06-01 01:06:40'),(2128,157,'McDonald','2011-06-01 01:06:40'),(2129,157,'Newton','2011-06-01 01:06:40'),(2130,157,'Barry','2011-06-01 01:06:40'),(2131,157,'Osage','2011-06-01 01:06:40'),(2132,157,'Boone','2011-06-01 01:06:40'),(2133,157,'Morgan','2011-06-01 01:06:40'),(2134,157,'Maries','2011-06-01 01:06:40'),(2135,157,'Miller','2011-06-01 01:06:40'),(2136,157,'Moniteau','2011-06-01 01:06:40'),(2137,157,'Camden','2011-06-01 01:06:40'),(2138,157,'Cole','2011-06-01 01:06:40'),(2139,157,'Cooper','2011-06-01 01:06:40'),(2140,157,'Howard','2011-06-01 01:06:40'),(2141,157,'Randolph','2011-06-01 01:06:40'),(2142,157,'Pettis','2011-06-01 01:06:40'),(2143,157,'Saline','2011-06-01 01:06:40'),(2144,157,'Benton','2011-06-01 01:06:40'),(2145,157,'Phelps','2011-06-01 01:06:40'),(2146,157,'Shannon','2011-06-01 01:06:40'),(2147,157,'Dent','2011-06-01 01:06:40'),(2148,157,'Crawford','2011-06-01 01:06:40'),(2149,157,'Texas','2011-06-01 01:06:40'),(2150,157,'Pulaski','2011-06-01 01:06:40'),(2151,157,'Laclede','2011-06-01 01:06:40'),(2152,157,'Howell','2011-06-01 01:06:40'),(2153,157,'Dallas','2011-06-01 01:06:40'),(2154,157,'Polk','2011-06-01 01:06:40'),(2155,157,'Dade','2011-06-01 01:06:40'),(2156,157,'Greene','2011-06-01 01:06:40'),(2157,157,'Lawrence','2011-06-01 01:06:40'),(2158,157,'Oregon','2011-06-01 01:06:40'),(2159,157,'Douglas','2011-06-01 01:06:40'),(2160,157,'Ozark','2011-06-01 01:06:40'),(2161,157,'Christian','2011-06-01 01:06:40'),(2162,157,'Stone','2011-06-01 01:06:40'),(2163,157,'Taney','2011-06-01 01:06:40'),(2164,157,'Hickory','2011-06-01 01:06:40'),(2165,157,'Webster','2011-06-01 01:06:40'),(2166,157,'Wright','2011-06-01 01:06:40'),(2167,147,'Atchison','2011-06-01 01:06:40'),(2168,147,'Douglas','2011-06-01 01:06:40'),(2169,147,'Leavenworth','2011-06-01 01:06:40'),(2170,147,'Doniphan','2011-06-01 01:06:40'),(2171,147,'Linn','2011-06-01 01:06:40'),(2172,147,'Wyandotte','2011-06-01 01:06:40'),(2173,147,'Miami','2011-06-01 01:06:40'),(2174,147,'Anderson','2011-06-01 01:06:40'),(2175,147,'Johnson','2011-06-01 01:06:40'),(2176,147,'Franklin','2011-06-01 01:06:40'),(2177,147,'Jefferson','2011-06-01 01:06:40'),(2178,147,'Wabaunsee','2011-06-01 01:06:40'),(2179,147,'Shawnee','2011-06-01 01:06:40'),(2180,147,'Marshall','2011-06-01 01:06:40'),(2181,147,'Nemaha','2011-06-01 01:06:40'),(2182,147,'Pottawatomie','2011-06-01 01:06:40'),(2183,147,'Osage','2011-06-01 01:06:40'),(2184,147,'Jackson','2011-06-01 01:06:40'),(2185,147,'Brown','2011-06-01 01:06:40'),(2186,147,'Geary','2011-06-01 01:06:40'),(2187,147,'Riley','2011-06-01 01:06:40'),(2188,147,'Bourbon','2011-06-01 01:06:40'),(2189,147,'Wilson','2011-06-01 01:06:40'),(2190,147,'Crawford','2011-06-01 01:06:40'),(2191,147,'Cherokee','2011-06-01 01:06:40'),(2192,147,'Neosho','2011-06-01 01:06:40'),(2193,147,'Allen','2011-06-01 01:06:40'),(2194,147,'Woodson','2011-06-01 01:06:40'),(2195,147,'Lyon','2011-06-01 01:06:40'),(2196,147,'Morris','2011-06-01 01:06:40'),(2197,147,'Coffey','2011-06-01 01:06:40'),(2198,147,'Marion','2011-06-01 01:06:40'),(2199,147,'Butler','2011-06-01 01:06:40'),(2200,147,'Chase','2011-06-01 01:06:40'),(2201,147,'Greenwood','2011-06-01 01:06:40'),(2202,147,'Cloud','2011-06-01 01:06:40'),(2203,147,'Republic','2011-06-01 01:06:40'),(2204,147,'Smith','2011-06-01 01:06:40'),(2205,147,'Washington','2011-06-01 01:06:40'),(2206,147,'Jewell','2011-06-01 01:06:40'),(2207,147,'Sedgwick','2011-06-01 01:06:40'),(2208,147,'Harper','2011-06-01 01:06:40'),(2209,147,'Sumner','2011-06-01 01:06:40'),(2210,147,'Cowley','2011-06-01 01:06:40'),(2211,147,'Harvey','2011-06-01 01:06:40'),(2212,147,'Pratt','2011-06-01 01:06:40'),(2213,147,'Chautauqua','2011-06-01 01:06:40'),(2214,147,'Comanche','2011-06-01 01:06:40'),(2215,147,'Kingman','2011-06-01 01:06:40'),(2216,147,'Kiowa','2011-06-01 01:06:40'),(2217,147,'Barber','2011-06-01 01:06:40'),(2218,147,'McPherson','2011-06-01 01:06:40'),(2219,147,'Montgomery','2011-06-01 01:06:40'),(2220,147,'Labette','2011-06-01 01:06:40'),(2221,147,'Elk','2011-06-01 01:06:40'),(2222,147,'Saline','2011-06-01 01:06:40'),(2223,147,'Dickinson','2011-06-01 01:06:40'),(2224,147,'Lincoln','2011-06-01 01:06:40'),(2225,147,'Mitchell','2011-06-01 01:06:40'),(2226,147,'Ottawa','2011-06-01 01:06:40'),(2227,147,'Rice','2011-06-01 01:06:40'),(2228,147,'Clay','2011-06-01 01:06:40'),(2229,147,'Osborne','2011-06-01 01:06:40'),(2230,147,'Ellsworth','2011-06-01 01:06:40'),(2231,147,'Reno','2011-06-01 01:06:40'),(2232,147,'Barton','2011-06-01 01:06:40'),(2233,147,'Rush','2011-06-01 01:06:40'),(2234,147,'Ness','2011-06-01 01:06:40'),(2235,147,'Edwards','2011-06-01 01:06:40'),(2236,147,'Pawnee','2011-06-01 01:06:40'),(2237,147,'Stafford','2011-06-01 01:06:40'),(2238,147,'Ellis','2011-06-01 01:06:40'),(2239,147,'Phillips','2011-06-01 01:06:40'),(2240,147,'Norton','2011-06-01 01:06:40'),(2241,147,'Graham','2011-06-01 01:06:40'),(2242,147,'Russell','2011-06-01 01:06:40'),(2243,147,'Trego','2011-06-01 01:06:40'),(2244,147,'Rooks','2011-06-01 01:06:40'),(2245,147,'Decatur','2011-06-01 01:06:40'),(2246,147,'Thomas','2011-06-01 01:06:40'),(2247,147,'Rawlins','2011-06-01 01:06:40'),(2248,147,'Cheyenne','2011-06-01 01:06:40'),(2249,147,'Sherman','2011-06-01 01:06:40'),(2250,147,'Gove','2011-06-01 01:06:40'),(2251,147,'Sheridan','2011-06-01 01:06:40'),(2252,147,'Logan','2011-06-01 01:06:40'),(2253,147,'Wallace','2011-06-01 01:06:40'),(2254,147,'Ford','2011-06-01 01:06:40'),(2255,147,'Clark','2011-06-01 01:06:40'),(2256,147,'Gray','2011-06-01 01:06:40'),(2257,147,'Hamilton','2011-06-01 01:06:40'),(2258,147,'Kearny','2011-06-01 01:06:40'),(2259,147,'Lane','2011-06-01 01:06:40'),(2260,147,'Meade','2011-06-01 01:06:40'),(2261,147,'Finney','2011-06-01 01:06:40'),(2262,147,'Hodgeman','2011-06-01 01:06:40'),(2263,147,'Stanton','2011-06-01 01:06:40'),(2264,147,'Seward','2011-06-01 01:06:40'),(2265,147,'Wichita','2011-06-01 01:06:40'),(2266,147,'Haskell','2011-06-01 01:06:40'),(2267,147,'Scott','2011-06-01 01:06:40'),(2268,147,'Greeley','2011-06-01 01:06:40'),(2269,147,'Grant','2011-06-01 01:06:40'),(2270,147,'Morton','2011-06-01 01:06:40'),(2271,147,'Stevens','2011-06-01 01:06:40'),(2272,159,'Butler','2011-06-01 01:06:40'),(2273,159,'Washington','2011-06-01 01:06:40'),(2274,159,'Saunders','2011-06-01 01:06:40'),(2275,159,'Cuming','2011-06-01 01:06:40'),(2276,159,'Sarpy','2011-06-01 01:06:40'),(2277,159,'Douglas','2011-06-01 01:06:40'),(2278,159,'Cass','2011-06-01 01:06:40'),(2279,159,'Burt','2011-06-01 01:06:40'),(2280,159,'Dodge','2011-06-01 01:06:40'),(2281,159,'Dakota','2011-06-01 01:06:40'),(2282,159,'Thurston','2011-06-01 01:06:40'),(2283,159,'Gage','2011-06-01 01:06:40'),(2284,159,'Thayer','2011-06-01 01:06:40'),(2285,159,'Nemaha','2011-06-01 01:06:40'),(2286,159,'Seward','2011-06-01 01:06:40'),(2287,159,'York','2011-06-01 01:06:40'),(2288,159,'Lancaster','2011-06-01 01:06:40'),(2289,159,'Pawnee','2011-06-01 01:06:40'),(2290,159,'Otoe','2011-06-01 01:06:40'),(2291,159,'Johnson','2011-06-01 01:06:40'),(2292,159,'Saline','2011-06-01 01:06:40'),(2293,159,'Richardson','2011-06-01 01:06:40'),(2294,159,'Jefferson','2011-06-01 01:06:40'),(2295,159,'Fillmore','2011-06-01 01:06:40'),(2296,159,'Clay','2011-06-01 01:06:40'),(2297,159,'Platte','2011-06-01 01:06:40'),(2298,159,'Boone','2011-06-01 01:06:40'),(2299,159,'Wheeler','2011-06-01 01:06:40'),(2300,159,'Nance','2011-06-01 01:06:40'),(2301,159,'Merrick','2011-06-01 01:06:40'),(2302,159,'Colfax','2011-06-01 01:06:40'),(2303,159,'Antelope','2011-06-01 01:06:40'),(2304,159,'Polk','2011-06-01 01:06:40'),(2305,159,'Greeley','2011-06-01 01:06:40'),(2306,159,'Madison','2011-06-01 01:06:40'),(2307,159,'Dixon','2011-06-01 01:06:40'),(2308,159,'Holt','2011-06-01 01:06:40'),(2309,159,'Rock','2011-06-01 01:06:40'),(2310,159,'Cedar','2011-06-01 01:06:40'),(2311,159,'Knox','2011-06-01 01:06:40'),(2312,159,'Boyd','2011-06-01 01:06:40'),(2313,159,'Wayne','2011-06-01 01:06:40'),(2314,159,'Pierce','2011-06-01 01:06:40'),(2315,159,'Keya Paha','2011-06-01 01:06:40'),(2316,159,'Stanton','2011-06-01 01:06:40'),(2317,159,'Hall','2011-06-01 01:06:40'),(2318,159,'Buffalo','2011-06-01 01:06:40'),(2319,159,'Custer','2011-06-01 01:06:40'),(2320,159,'Valley','2011-06-01 01:06:40'),(2321,159,'Sherman','2011-06-01 01:06:40'),(2322,159,'Hamilton','2011-06-01 01:06:40'),(2323,159,'Howard','2011-06-01 01:06:40'),(2324,159,'Blaine','2011-06-01 01:06:40'),(2325,159,'Garfield','2011-06-01 01:06:40'),(2326,159,'Dawson','2011-06-01 01:06:40'),(2327,159,'Loup','2011-06-01 01:06:40'),(2328,159,'Adams','2011-06-01 01:06:40'),(2329,159,'Harlan','2011-06-01 01:06:40'),(2330,159,'Furnas','2011-06-01 01:06:40'),(2331,159,'Phelps','2011-06-01 01:06:40'),(2332,159,'Kearney','2011-06-01 01:06:40'),(2333,159,'Webster','2011-06-01 01:06:40'),(2334,159,'Franklin','2011-06-01 01:06:40'),(2335,159,'Gosper','2011-06-01 01:06:40'),(2336,159,'Nuckolls','2011-06-01 01:06:40'),(2337,159,'Red Willow','2011-06-01 01:06:40'),(2338,159,'Dundy','2011-06-01 01:06:40'),(2339,159,'Chase','2011-06-01 01:06:40'),(2340,159,'Hitchcock','2011-06-01 01:06:40'),(2341,159,'Frontier','2011-06-01 01:06:40'),(2342,159,'Hayes','2011-06-01 01:06:40'),(2343,159,'Lincoln','2011-06-01 01:06:40'),(2344,159,'Arthur','2011-06-01 01:06:40'),(2345,159,'Deuel','2011-06-01 01:06:40'),(2346,159,'Morrill','2011-06-01 01:06:40'),(2347,159,'Keith','2011-06-01 01:06:40'),(2348,159,'Kimball','2011-06-01 01:06:40'),(2349,159,'Cheyenne','2011-06-01 01:06:40'),(2350,159,'Perkins','2011-06-01 01:06:40'),(2351,159,'Cherry','2011-06-01 01:06:40'),(2352,159,'Thomas','2011-06-01 01:06:40'),(2353,159,'Garden','2011-06-01 01:06:40'),(2354,159,'Hooker','2011-06-01 01:06:40'),(2355,159,'Logan','2011-06-01 01:06:40'),(2356,159,'McPherson','2011-06-01 01:06:40'),(2357,159,'Brown','2011-06-01 01:06:40'),(2358,159,'Box Butte','2011-06-01 01:06:40'),(2359,159,'Grant','2011-06-01 01:06:40'),(2360,159,'Sheridan','2011-06-01 01:06:40'),(2361,159,'Dawes','2011-06-01 01:06:40'),(2362,159,'Scotts Bluff','2011-06-01 01:06:40'),(2363,159,'Banner','2011-06-01 01:06:40'),(2364,159,'Sioux','2011-06-01 01:06:40'),(2365,149,'Jefferson','2011-06-01 01:06:40'),(2366,149,'Saint Charles','2011-06-01 01:06:40'),(2367,149,'Saint Bernard','2011-06-01 01:06:40'),(2368,149,'Plaquemines','2011-06-01 01:06:40'),(2369,149,'St John the Baptist','2011-06-01 01:06:40'),(2370,149,'Saint James','2011-06-01 01:06:40'),(2371,149,'Orleans','2011-06-01 01:06:40'),(2372,149,'Lafourche','2011-06-01 01:06:40'),(2373,149,'Assumption','2011-06-01 01:06:40'),(2374,149,'Saint Mary','2011-06-01 01:06:40'),(2375,149,'Terrebonne','2011-06-01 01:06:40'),(2376,149,'Ascension','2011-06-01 01:06:40'),(2377,149,'Tangipahoa','2011-06-01 01:06:40'),(2378,149,'Saint Tammany','2011-06-01 01:06:40'),(2379,149,'Washington','2011-06-01 01:06:40'),(2380,149,'Saint Helena','2011-06-01 01:06:40'),(2381,149,'Livingston','2011-06-01 01:06:40'),(2382,149,'Lafayette','2011-06-01 01:06:40'),(2383,149,'Vermilion','2011-06-01 01:06:40'),(2384,149,'Saint Landry','2011-06-01 01:06:40'),(2385,149,'Iberia','2011-06-01 01:06:40'),(2386,149,'Evangeline','2011-06-01 01:06:40'),(2387,149,'Acadia','2011-06-01 01:06:40'),(2388,149,'Saint Martin','2011-06-01 01:06:40'),(2389,149,'Jefferson Davis','2011-06-01 01:06:40'),(2390,149,'Calcasieu','2011-06-01 01:06:40'),(2391,149,'Cameron','2011-06-01 01:06:40'),(2392,149,'Beauregard','2011-06-01 01:06:40'),(2393,149,'Allen','2011-06-01 01:06:40'),(2394,149,'Vernon','2011-06-01 01:06:40'),(2395,149,'East Baton Rouge','2011-06-01 01:06:40'),(2396,149,'West Baton Rouge','2011-06-01 01:06:40'),(2397,149,'West Feliciana','2011-06-01 01:06:40'),(2398,149,'Pointe Coupee','2011-06-01 01:06:40'),(2399,149,'Iberville','2011-06-01 01:06:40'),(2400,149,'East Feliciana','2011-06-01 01:06:40'),(2401,149,'Bienville','2011-06-01 01:06:40'),(2402,149,'Natchitoches','2011-06-01 01:06:40'),(2403,149,'Claiborne','2011-06-01 01:06:40'),(2404,149,'Caddo','2011-06-01 01:06:40'),(2405,149,'Bossier','2011-06-01 01:06:40'),(2406,149,'Webster','2011-06-01 01:06:40'),(2407,149,'Red River','2011-06-01 01:06:40'),(2408,149,'De Soto','2011-06-01 01:06:40'),(2409,149,'Sabine','2011-06-01 01:06:40'),(2410,149,'Ouachita','2011-06-01 01:06:40'),(2411,149,'Richland','2011-06-01 01:06:40'),(2412,149,'Franklin','2011-06-01 01:06:40'),(2413,149,'Morehouse','2011-06-01 01:06:40'),(2414,149,'Union','2011-06-01 01:06:40'),(2415,149,'Jackson','2011-06-01 01:06:40'),(2416,149,'Lincoln','2011-06-01 01:06:40'),(2417,149,'Madison','2011-06-01 01:06:40'),(2418,149,'West Carroll','2011-06-01 01:06:40'),(2419,149,'East Carroll','2011-06-01 01:06:40'),(2420,149,'Rapides','2011-06-01 01:06:40'),(2421,149,'Concordia','2011-06-01 01:06:40'),(2422,149,'Avoyelles','2011-06-01 01:06:40'),(2423,149,'Catahoula','2011-06-01 01:06:40'),(2424,149,'La Salle','2011-06-01 01:06:40'),(2425,149,'Tensas','2011-06-01 01:06:40'),(2426,149,'Winn','2011-06-01 01:06:40'),(2427,149,'Grant','2011-06-01 01:06:40'),(2428,149,'Caldwell','2011-06-01 01:06:40'),(2429,132,'Jefferson','2011-06-01 01:06:40'),(2430,132,'Desha','2011-06-01 01:06:40'),(2431,132,'Bradley','2011-06-01 01:06:40'),(2432,132,'Ashley','2011-06-01 01:06:40'),(2433,132,'Chicot','2011-06-01 01:06:40'),(2434,132,'Lincoln','2011-06-01 01:06:40'),(2435,132,'Cleveland','2011-06-01 01:06:40'),(2436,132,'Drew','2011-06-01 01:06:40'),(2437,132,'Ouachita','2011-06-01 01:06:40'),(2438,132,'Clark','2011-06-01 01:06:40'),(2439,132,'Nevada','2011-06-01 01:06:40'),(2440,132,'Union','2011-06-01 01:06:40'),(2441,132,'Dallas','2011-06-01 01:06:40'),(2442,132,'Columbia','2011-06-01 01:06:40'),(2443,132,'Calhoun','2011-06-01 01:06:40'),(2444,132,'Hempstead','2011-06-01 01:06:40'),(2445,132,'Little River','2011-06-01 01:06:40'),(2446,132,'Sevier','2011-06-01 01:06:40'),(2447,132,'Lafayette','2011-06-01 01:06:40'),(2448,132,'Howard','2011-06-01 01:06:40'),(2449,132,'Miller','2011-06-01 01:06:40'),(2450,132,'Garland','2011-06-01 01:06:40'),(2451,132,'Pike','2011-06-01 01:06:40'),(2452,132,'Hot Spring','2011-06-01 01:06:40'),(2453,132,'Polk','2011-06-01 01:06:40'),(2454,132,'Montgomery','2011-06-01 01:06:40'),(2455,132,'Perry','2011-06-01 01:06:40'),(2456,132,'Pulaski','2011-06-01 01:06:40'),(2457,132,'Arkansas','2011-06-01 01:06:40'),(2458,132,'Jackson','2011-06-01 01:06:40'),(2459,132,'Woodruff','2011-06-01 01:06:40'),(2460,132,'Lonoke','2011-06-01 01:06:40'),(2461,132,'White','2011-06-01 01:06:40'),(2462,132,'Saline','2011-06-01 01:06:40'),(2463,132,'Van Buren','2011-06-01 01:06:40'),(2464,132,'Prairie','2011-06-01 01:06:40'),(2465,132,'Monroe','2011-06-01 01:06:40'),(2466,132,'Conway','2011-06-01 01:06:40'),(2467,132,'Faulkner','2011-06-01 01:06:40'),(2468,132,'Cleburne','2011-06-01 01:06:40'),(2469,132,'Stone','2011-06-01 01:06:40'),(2470,132,'Grant','2011-06-01 01:06:40'),(2471,132,'Independence','2011-06-01 01:06:40'),(2472,132,'Crittenden','2011-06-01 01:06:40'),(2473,132,'Mississippi','2011-06-01 01:06:40'),(2474,132,'Lee','2011-06-01 01:06:40'),(2475,132,'Phillips','2011-06-01 01:06:40'),(2476,132,'Saint Francis','2011-06-01 01:06:40'),(2477,132,'Cross','2011-06-01 01:06:40'),(2478,132,'Poinsett','2011-06-01 01:06:40'),(2479,132,'Craighead','2011-06-01 01:06:40'),(2480,132,'Lawrence','2011-06-01 01:06:40'),(2481,132,'Greene','2011-06-01 01:06:40'),(2482,132,'Randolph','2011-06-01 01:06:40'),(2483,132,'Clay','2011-06-01 01:06:40'),(2484,132,'Sharp','2011-06-01 01:06:40'),(2485,132,'Izard','2011-06-01 01:06:40'),(2486,132,'Fulton','2011-06-01 01:06:40'),(2487,132,'Baxter','2011-06-01 01:06:40'),(2488,132,'Boone','2011-06-01 01:06:40'),(2489,132,'Carroll','2011-06-01 01:06:40'),(2490,132,'Marion','2011-06-01 01:06:40'),(2491,132,'Newton','2011-06-01 01:06:40'),(2492,132,'Searcy','2011-06-01 01:06:40'),(2493,132,'Pope','2011-06-01 01:06:40'),(2494,132,'Washington','2011-06-01 01:06:40'),(2495,132,'Benton','2011-06-01 01:06:40'),(2496,132,'Madison','2011-06-01 01:06:40'),(2497,132,'Franklin','2011-06-01 01:06:40'),(2498,132,'Yell','2011-06-01 01:06:40'),(2499,132,'Logan','2011-06-01 01:06:40'),(2500,132,'Johnson','2011-06-01 01:06:40'),(2501,132,'Scott','2011-06-01 01:06:40'),(2502,132,'Sebastian','2011-06-01 01:06:40'),(2503,132,'Crawford','2011-06-01 01:06:40'),(2504,169,'Caddo','2011-06-01 01:06:40'),(2505,169,'Grady','2011-06-01 01:06:40'),(2506,169,'Oklahoma','2011-06-01 01:06:40'),(2507,169,'McClain','2011-06-01 01:06:40'),(2508,169,'Stephens','2011-06-01 01:06:40'),(2509,169,'Canadian','2011-06-01 01:06:40'),(2510,169,'Kingfisher','2011-06-01 01:06:40'),(2511,169,'Cleveland','2011-06-01 01:06:40'),(2512,169,'Washita','2011-06-01 01:06:40'),(2513,169,'Logan','2011-06-01 01:06:40'),(2514,169,'Murray','2011-06-01 01:06:40'),(2515,169,'Blaine','2011-06-01 01:06:40'),(2516,169,'Kiowa','2011-06-01 01:06:40'),(2517,169,'Garvin','2011-06-01 01:06:40'),(2518,169,'Noble','2011-06-01 01:06:40'),(2519,169,'Custer','2011-06-01 01:06:40'),(2520,178,'Travis','2011-06-01 01:06:40'),(2521,169,'Carter','2011-06-01 01:06:40'),(2522,169,'Love','2011-06-01 01:06:40'),(2523,169,'Johnston','2011-06-01 01:06:40'),(2524,169,'Marshall','2011-06-01 01:06:40'),(2525,169,'Bryan','2011-06-01 01:06:40'),(2526,169,'Jefferson','2011-06-01 01:06:40'),(2527,169,'Comanche','2011-06-01 01:06:40'),(2528,169,'Jackson','2011-06-01 01:06:40'),(2529,169,'Tillman','2011-06-01 01:06:40'),(2530,169,'Cotton','2011-06-01 01:06:40'),(2531,169,'Harmon','2011-06-01 01:06:40'),(2532,169,'Greer','2011-06-01 01:06:40'),(2533,169,'Beckham','2011-06-01 01:06:40'),(2534,169,'Roger Mills','2011-06-01 01:06:40'),(2535,169,'Dewey','2011-06-01 01:06:40'),(2536,169,'Garfield','2011-06-01 01:06:40'),(2537,169,'Alfalfa','2011-06-01 01:06:40'),(2538,169,'Woods','2011-06-01 01:06:40'),(2539,169,'Major','2011-06-01 01:06:40'),(2540,169,'Grant','2011-06-01 01:06:40'),(2541,169,'Woodward','2011-06-01 01:06:40'),(2542,169,'Ellis','2011-06-01 01:06:40'),(2543,169,'Harper','2011-06-01 01:06:40'),(2544,169,'Beaver','2011-06-01 01:06:40'),(2545,169,'Texas','2011-06-01 01:06:40'),(2546,169,'Cimarron','2011-06-01 01:06:40'),(2547,169,'Osage','2011-06-01 01:06:40'),(2548,169,'Washington','2011-06-01 01:06:40'),(2549,169,'Tulsa','2011-06-01 01:06:40'),(2550,169,'Creek','2011-06-01 01:06:40'),(2551,169,'Wagoner','2011-06-01 01:06:40'),(2552,169,'Rogers','2011-06-01 01:06:40'),(2553,169,'Pawnee','2011-06-01 01:06:40'),(2554,169,'Payne','2011-06-01 01:06:40'),(2555,169,'Lincoln','2011-06-01 01:06:40'),(2556,169,'Nowata','2011-06-01 01:06:40'),(2557,169,'Craig','2011-06-01 01:06:40'),(2558,169,'Mayes','2011-06-01 01:06:40'),(2559,169,'Ottawa','2011-06-01 01:06:40'),(2560,169,'Delaware','2011-06-01 01:06:40'),(2561,169,'Muskogee','2011-06-01 01:06:40'),(2562,169,'Okmulgee','2011-06-01 01:06:40'),(2563,169,'Pittsburg','2011-06-01 01:06:40'),(2564,169,'McIntosh','2011-06-01 01:06:40'),(2565,169,'Cherokee','2011-06-01 01:06:40'),(2566,169,'Sequoyah','2011-06-01 01:06:40'),(2567,169,'Haskell','2011-06-01 01:06:40'),(2568,169,'Adair','2011-06-01 01:06:40'),(2569,169,'Pushmataha','2011-06-01 01:06:40'),(2570,169,'Atoka','2011-06-01 01:06:40'),(2571,169,'Hughes','2011-06-01 01:06:40'),(2572,169,'Coal','2011-06-01 01:06:40'),(2573,169,'Latimer','2011-06-01 01:06:40'),(2574,169,'Le Flore','2011-06-01 01:06:40'),(2575,169,'Kay','2011-06-01 01:06:40'),(2576,169,'McCurtain','2011-06-01 01:06:40'),(2577,169,'Choctaw','2011-06-01 01:06:40'),(2578,169,'Pottawatomie','2011-06-01 01:06:40'),(2579,169,'Seminole','2011-06-01 01:06:40'),(2580,169,'Pontotoc','2011-06-01 01:06:40'),(2581,169,'Okfuskee','2011-06-01 01:06:40'),(2582,178,'Dallas','2011-06-01 01:06:40'),(2583,178,'Collin','2011-06-01 01:06:40'),(2584,178,'Denton','2011-06-01 01:06:40'),(2585,178,'Grayson','2011-06-01 01:06:40'),(2586,178,'Rockwall','2011-06-01 01:06:40'),(2587,178,'Ellis','2011-06-01 01:06:40'),(2588,178,'Navarro','2011-06-01 01:06:40'),(2589,178,'Van Zandt','2011-06-01 01:06:40'),(2590,178,'Kaufman','2011-06-01 01:06:40'),(2591,178,'Henderson','2011-06-01 01:06:40'),(2592,178,'Hunt','2011-06-01 01:06:40'),(2593,178,'Wood','2011-06-01 01:06:40'),(2594,178,'Lamar','2011-06-01 01:06:40'),(2595,178,'Red River','2011-06-01 01:06:40'),(2596,178,'Fannin','2011-06-01 01:06:40'),(2597,178,'Delta','2011-06-01 01:06:40'),(2598,178,'Hopkins','2011-06-01 01:06:40'),(2599,178,'Rains','2011-06-01 01:06:40'),(2600,178,'Camp','2011-06-01 01:06:40'),(2601,178,'Titus','2011-06-01 01:06:40'),(2602,178,'Franklin','2011-06-01 01:06:40'),(2603,178,'Bowie','2011-06-01 01:06:40'),(2604,178,'Cass','2011-06-01 01:06:40'),(2605,178,'Marion','2011-06-01 01:06:40'),(2606,178,'Morris','2011-06-01 01:06:40'),(2607,178,'Gregg','2011-06-01 01:06:40'),(2608,178,'Panola','2011-06-01 01:06:40'),(2609,178,'Upshur','2011-06-01 01:06:40'),(2610,178,'Harrison','2011-06-01 01:06:40'),(2611,178,'Rusk','2011-06-01 01:06:40'),(2612,178,'Smith','2011-06-01 01:06:40'),(2613,178,'Cherokee','2011-06-01 01:06:40'),(2614,178,'Nacogdoches','2011-06-01 01:06:40'),(2615,178,'Anderson','2011-06-01 01:06:40'),(2616,178,'Leon','2011-06-01 01:06:40'),(2617,178,'Trinity','2011-06-01 01:06:40'),(2618,178,'Houston','2011-06-01 01:06:40'),(2619,178,'Freestone','2011-06-01 01:06:40'),(2620,178,'Madison','2011-06-01 01:06:40'),(2621,178,'Angelina','2011-06-01 01:06:40'),(2622,178,'Newton','2011-06-01 01:06:40'),(2623,178,'San Augustine','2011-06-01 01:06:40'),(2624,178,'Sabine','2011-06-01 01:06:40'),(2625,178,'Polk','2011-06-01 01:06:40'),(2626,178,'Shelby','2011-06-01 01:06:40'),(2627,178,'Tyler','2011-06-01 01:06:40'),(2628,178,'Jasper','2011-06-01 01:06:40'),(2629,178,'Tarrant','2011-06-01 01:06:40'),(2630,178,'Parker','2011-06-01 01:06:40'),(2631,178,'Johnson','2011-06-01 01:06:40'),(2632,178,'Wise','2011-06-01 01:06:40'),(2633,178,'Hood','2011-06-01 01:06:40'),(2634,178,'Somervell','2011-06-01 01:06:40'),(2635,178,'Hill','2011-06-01 01:06:40'),(2636,178,'Palo Pinto','2011-06-01 01:06:40'),(2637,178,'Clay','2011-06-01 01:06:40'),(2638,178,'Montague','2011-06-01 01:06:40'),(2639,178,'Cooke','2011-06-01 01:06:40'),(2640,178,'Wichita','2011-06-01 01:06:40'),(2641,178,'Archer','2011-06-01 01:06:40'),(2642,178,'Knox','2011-06-01 01:06:40'),(2643,178,'Wilbarger','2011-06-01 01:06:40'),(2644,178,'Young','2011-06-01 01:06:40'),(2645,178,'Baylor','2011-06-01 01:06:40'),(2646,178,'Haskell','2011-06-01 01:06:40'),(2647,178,'Erath','2011-06-01 01:06:40'),(2648,178,'Stephens','2011-06-01 01:06:40'),(2649,178,'Jack','2011-06-01 01:06:40'),(2650,178,'Shackelford','2011-06-01 01:06:40'),(2651,178,'Brown','2011-06-01 01:06:40'),(2652,178,'Eastland','2011-06-01 01:06:40'),(2653,178,'Hamilton','2011-06-01 01:06:40'),(2654,178,'Comanche','2011-06-01 01:06:40'),(2655,178,'Callahan','2011-06-01 01:06:40'),(2656,178,'Throckmorton','2011-06-01 01:06:40'),(2657,178,'Bell','2011-06-01 01:06:40'),(2658,178,'Milam','2011-06-01 01:06:40'),(2659,178,'Coryell','2011-06-01 01:06:40'),(2660,178,'McLennan','2011-06-01 01:06:40'),(2661,178,'Williamson','2011-06-01 01:06:40'),(2662,178,'Lampasas','2011-06-01 01:06:40'),(2663,178,'Falls','2011-06-01 01:06:40'),(2664,178,'Robertson','2011-06-01 01:06:40'),(2665,178,'Bosque','2011-06-01 01:06:40'),(2666,178,'Limestone','2011-06-01 01:06:40'),(2667,178,'Mason','2011-06-01 01:06:40'),(2668,178,'Runnels','2011-06-01 01:06:40'),(2669,178,'McCulloch','2011-06-01 01:06:40'),(2670,178,'Coleman','2011-06-01 01:06:40'),(2671,178,'Llano','2011-06-01 01:06:40'),(2672,178,'San Saba','2011-06-01 01:06:40'),(2673,178,'Concho','2011-06-01 01:06:40'),(2674,178,'Menard','2011-06-01 01:06:40'),(2675,178,'Mills','2011-06-01 01:06:40'),(2676,178,'Kimble','2011-06-01 01:06:40'),(2677,178,'Edwards','2011-06-01 01:06:40'),(2678,178,'Tom Green','2011-06-01 01:06:40'),(2679,178,'Irion','2011-06-01 01:06:40'),(2680,178,'Reagan','2011-06-01 01:06:40'),(2681,178,'Coke','2011-06-01 01:06:40'),(2682,178,'Schleicher','2011-06-01 01:06:40'),(2683,178,'Crockett','2011-06-01 01:06:40'),(2684,178,'Sutton','2011-06-01 01:06:40'),(2685,178,'Sterling','2011-06-01 01:06:40'),(2686,178,'Harris','2011-06-01 01:06:40'),(2687,178,'Montgomery','2011-06-01 01:06:40'),(2688,178,'Walker','2011-06-01 01:06:40'),(2689,178,'Liberty','2011-06-01 01:06:40'),(2690,178,'San Jacinto','2011-06-01 01:06:40'),(2691,178,'Grimes','2011-06-01 01:06:40'),(2692,178,'Hardin','2011-06-01 01:06:40'),(2693,178,'Matagorda','2011-06-01 01:06:40'),(2694,178,'Fort Bend','2011-06-01 01:06:40'),(2695,178,'Colorado','2011-06-01 01:06:40'),(2696,178,'Austin','2011-06-01 01:06:40'),(2697,178,'Wharton','2011-06-01 01:06:40'),(2698,178,'Brazoria','2011-06-01 01:06:40'),(2699,178,'Waller','2011-06-01 01:06:40'),(2700,178,'Washington','2011-06-01 01:06:40'),(2701,178,'Galveston','2011-06-01 01:06:40'),(2702,178,'Chambers','2011-06-01 01:06:40'),(2703,178,'Orange','2011-06-01 01:06:40'),(2704,178,'Jefferson','2011-06-01 01:06:40'),(2705,178,'Brazos','2011-06-01 01:06:40'),(2706,178,'Burleson','2011-06-01 01:06:40'),(2707,178,'Lee','2011-06-01 01:06:40'),(2708,178,'Victoria','2011-06-01 01:06:40'),(2709,178,'Refugio','2011-06-01 01:06:40'),(2710,178,'De Witt','2011-06-01 01:06:40'),(2711,178,'Jackson','2011-06-01 01:06:40'),(2712,178,'Goliad','2011-06-01 01:06:40'),(2713,178,'Lavaca','2011-06-01 01:06:40'),(2714,178,'Calhoun','2011-06-01 01:06:40'),(2715,178,'La Salle','2011-06-01 01:06:40'),(2716,178,'Bexar','2011-06-01 01:06:40'),(2717,178,'Bandera','2011-06-01 01:06:40'),(2718,178,'Kendall','2011-06-01 01:06:40'),(2719,178,'Frio','2011-06-01 01:06:40'),(2720,178,'McMullen','2011-06-01 01:06:40'),(2721,178,'Atascosa','2011-06-01 01:06:40'),(2722,178,'Medina','2011-06-01 01:06:40'),(2723,178,'Kerr','2011-06-01 01:06:40'),(2724,178,'Live Oak','2011-06-01 01:06:40'),(2725,178,'Webb','2011-06-01 01:06:40'),(2726,178,'Zapata','2011-06-01 01:06:40'),(2727,178,'Comal','2011-06-01 01:06:40'),(2728,178,'Bee','2011-06-01 01:06:40'),(2729,178,'Guadalupe','2011-06-01 01:06:40'),(2730,178,'Karnes','2011-06-01 01:06:40'),(2731,178,'Wilson','2011-06-01 01:06:40'),(2732,178,'Gonzales','2011-06-01 01:06:40'),(2733,178,'Nueces','2011-06-01 01:06:40'),(2734,178,'Jim Wells','2011-06-01 01:06:40'),(2735,178,'San Patricio','2011-06-01 01:06:40'),(2736,178,'Kenedy','2011-06-01 01:06:40'),(2737,178,'Duval','2011-06-01 01:06:40'),(2738,178,'Brooks','2011-06-01 01:06:40'),(2739,178,'Aransas','2011-06-01 01:06:40'),(2740,178,'Jim Hogg','2011-06-01 01:06:40'),(2741,178,'Kleberg','2011-06-01 01:06:40'),(2742,178,'Hidalgo','2011-06-01 01:06:40'),(2743,178,'Cameron','2011-06-01 01:06:40'),(2744,178,'Starr','2011-06-01 01:06:40'),(2745,178,'Willacy','2011-06-01 01:06:40'),(2746,178,'Bastrop','2011-06-01 01:06:40'),(2747,178,'Burnet','2011-06-01 01:06:40'),(2748,178,'Blanco','2011-06-01 01:06:40'),(2749,178,'Hays','2011-06-01 01:06:40'),(2750,178,'Caldwell','2011-06-01 01:06:40'),(2751,178,'Gillespie','2011-06-01 01:06:40'),(2752,178,'Uvalde','2011-06-01 01:06:40'),(2753,178,'Dimmit','2011-06-01 01:06:40'),(2754,178,'Zavala','2011-06-01 01:06:40'),(2755,178,'Kinney','2011-06-01 01:06:40'),(2756,178,'Real','2011-06-01 01:06:40'),(2757,178,'Val Verde','2011-06-01 01:06:40'),(2758,178,'Terrell','2011-06-01 01:06:40'),(2759,178,'Maverick','2011-06-01 01:06:40'),(2760,178,'Fayette','2011-06-01 01:06:40'),(2761,178,'Oldham','2011-06-01 01:06:40'),(2762,178,'Gray','2011-06-01 01:06:40'),(2763,178,'Wheeler','2011-06-01 01:06:40'),(2764,178,'Lipscomb','2011-06-01 01:06:40'),(2765,178,'Hutchinson','2011-06-01 01:06:40'),(2766,178,'Parmer','2011-06-01 01:06:40'),(2767,178,'Potter','2011-06-01 01:06:40'),(2768,178,'Moore','2011-06-01 01:06:40'),(2769,178,'Hemphill','2011-06-01 01:06:40'),(2770,178,'Randall','2011-06-01 01:06:40'),(2771,178,'Hartley','2011-06-01 01:06:40'),(2772,178,'Armstrong','2011-06-01 01:06:40'),(2773,178,'Hale','2011-06-01 01:06:40'),(2774,178,'Dallam','2011-06-01 01:06:40'),(2775,178,'Deaf Smith','2011-06-01 01:06:40'),(2776,178,'Castro','2011-06-01 01:06:40'),(2777,178,'Lamb','2011-06-01 01:06:40'),(2778,178,'Ochiltree','2011-06-01 01:06:40'),(2779,178,'Carson','2011-06-01 01:06:40'),(2780,178,'Hansford','2011-06-01 01:06:40'),(2781,178,'Swisher','2011-06-01 01:06:40'),(2782,178,'Roberts','2011-06-01 01:06:40'),(2783,178,'Collingsworth','2011-06-01 01:06:40'),(2784,178,'Sherman','2011-06-01 01:06:40'),(2785,178,'Childress','2011-06-01 01:06:40'),(2786,178,'Dickens','2011-06-01 01:06:40'),(2787,178,'Floyd','2011-06-01 01:06:40'),(2788,178,'Cottle','2011-06-01 01:06:40'),(2789,178,'Hardeman','2011-06-01 01:06:40'),(2790,178,'Donley','2011-06-01 01:06:40'),(2791,178,'Foard','2011-06-01 01:06:40'),(2792,178,'Hall','2011-06-01 01:06:40'),(2793,178,'Motley','2011-06-01 01:06:40'),(2794,178,'King','2011-06-01 01:06:40'),(2795,178,'Briscoe','2011-06-01 01:06:40'),(2796,178,'Hockley','2011-06-01 01:06:40'),(2797,178,'Cochran','2011-06-01 01:06:40'),(2798,178,'Terry','2011-06-01 01:06:40'),(2799,178,'Bailey','2011-06-01 01:06:40'),(2800,178,'Crosby','2011-06-01 01:06:40'),(2801,178,'Yoakum','2011-06-01 01:06:40'),(2802,178,'Lubbock','2011-06-01 01:06:40'),(2803,178,'Garza','2011-06-01 01:06:40'),(2804,178,'Dawson','2011-06-01 01:06:40'),(2805,178,'Gaines','2011-06-01 01:06:40'),(2806,178,'Lynn','2011-06-01 01:06:40'),(2807,178,'Jones','2011-06-01 01:06:40'),(2808,178,'Stonewall','2011-06-01 01:06:40'),(2809,178,'Nolan','2011-06-01 01:06:40'),(2810,178,'Taylor','2011-06-01 01:06:40'),(2811,178,'Howard','2011-06-01 01:06:40'),(2812,178,'Mitchell','2011-06-01 01:06:40'),(2813,178,'Scurry','2011-06-01 01:06:40'),(2814,178,'Kent','2011-06-01 01:06:40'),(2815,178,'Fisher','2011-06-01 01:06:40'),(2816,178,'Midland','2011-06-01 01:06:40'),(2817,178,'Andrews','2011-06-01 01:06:40'),(2818,178,'Reeves','2011-06-01 01:06:40'),(2819,178,'Ward','2011-06-01 01:06:40'),(2820,178,'Pecos','2011-06-01 01:06:40'),(2821,178,'Crane','2011-06-01 01:06:40'),(2822,178,'Jeff Davis','2011-06-01 01:06:40'),(2823,178,'Borden','2011-06-01 01:06:40'),(2824,178,'Glasscock','2011-06-01 01:06:40'),(2825,178,'Ector','2011-06-01 01:06:40'),(2826,178,'Winkler','2011-06-01 01:06:40'),(2827,178,'Martin','2011-06-01 01:06:40'),(2828,178,'Upton','2011-06-01 01:06:40'),(2829,178,'Loving','2011-06-01 01:06:40'),(2830,178,'El Paso','2011-06-01 01:06:40'),(2831,178,'Brewster','2011-06-01 01:06:40'),(2832,178,'Hudspeth','2011-06-01 01:06:40'),(2833,178,'Presidio','2011-06-01 01:06:40'),(2834,178,'Culberson','2011-06-01 01:06:40'),(2835,134,'Jefferson','2011-06-01 01:06:40'),(2836,134,'Arapahoe','2011-06-01 01:06:40'),(2837,134,'Adams','2011-06-01 01:06:40'),(2838,134,'Boulder','2011-06-01 01:06:40'),(2839,134,'Elbert','2011-06-01 01:06:40'),(2840,134,'Douglas','2011-06-01 01:06:40'),(2841,134,'Denver','2011-06-01 01:06:40'),(2842,134,'El Paso','2011-06-01 01:06:40'),(2843,134,'Park','2011-06-01 01:06:40'),(2844,134,'Gilpin','2011-06-01 01:06:40'),(2845,134,'Eagle','2011-06-01 01:06:40'),(2846,134,'Summit','2011-06-01 01:06:40'),(2847,134,'Routt','2011-06-01 01:06:40'),(2848,134,'Lake','2011-06-01 01:06:40'),(2849,134,'Jackson','2011-06-01 01:06:40'),(2850,134,'Clear Creek','2011-06-01 01:06:40'),(2851,134,'Grand','2011-06-01 01:06:40'),(2852,134,'Weld','2011-06-01 01:06:40'),(2853,134,'Larimer','2011-06-01 01:06:40'),(2854,134,'Morgan','2011-06-01 01:06:40'),(2855,134,'Washington','2011-06-01 01:06:40'),(2856,134,'Phillips','2011-06-01 01:06:40'),(2857,134,'Logan','2011-06-01 01:06:40'),(2858,134,'Yuma','2011-06-01 01:06:40'),(2859,134,'Sedgwick','2011-06-01 01:06:40'),(2860,134,'Cheyenne','2011-06-01 01:06:40'),(2861,134,'Lincoln','2011-06-01 01:06:40'),(2862,134,'Kit Carson','2011-06-01 01:06:40'),(2863,134,'Teller','2011-06-01 01:06:40'),(2864,134,'Mohave','2011-06-01 01:06:40'),(2865,134,'Pueblo','2011-06-01 01:06:40'),(2866,134,'Las Animas','2011-06-01 01:06:40'),(2867,134,'Kiowa','2011-06-01 01:06:40'),(2868,134,'Baca','2011-06-01 01:06:40'),(2869,134,'Otero','2011-06-01 01:06:40'),(2870,134,'Crowley','2011-06-01 01:06:40'),(2871,134,'Bent','2011-06-01 01:06:40'),(2872,134,'Huerfano','2011-06-01 01:06:40'),(2873,134,'Prowers','2011-06-01 01:06:40'),(2874,134,'Alamosa','2011-06-01 01:06:40'),(2875,134,'Conejos','2011-06-01 01:06:40'),(2876,134,'Archuleta','2011-06-01 01:06:40'),(2877,134,'La Plata','2011-06-01 01:06:40'),(2878,134,'Costilla','2011-06-01 01:06:40'),(2879,134,'Saguache','2011-06-01 01:06:40'),(2880,134,'Mineral','2011-06-01 01:06:40'),(2881,134,'Rio Grande','2011-06-01 01:06:40'),(2882,134,'Chaffee','2011-06-01 01:06:40'),(2883,134,'Gunnison','2011-06-01 01:06:40'),(2884,134,'Fremont','2011-06-01 01:06:40'),(2885,134,'Montrose','2011-06-01 01:06:40'),(2886,134,'Hinsdale','2011-06-01 01:06:40'),(2887,134,'Custer','2011-06-01 01:06:40'),(2888,134,'Dolores','2011-06-01 01:06:40'),(2889,134,'Montezuma','2011-06-01 01:06:40'),(2890,134,'San Miguel','2011-06-01 01:06:40'),(2891,134,'Delta','2011-06-01 01:06:40'),(2892,134,'Ouray','2011-06-01 01:06:40'),(2893,134,'San Juan','2011-06-01 01:06:40'),(2894,134,'Mesa','2011-06-01 01:06:40'),(2895,134,'Garfield','2011-06-01 01:06:40'),(2896,134,'Moffat','2011-06-01 01:06:40'),(2897,134,'Pitkin','2011-06-01 01:06:40'),(2898,134,'Rio Blanco','2011-06-01 01:06:40'),(2899,186,'Laramie','2011-06-01 01:06:40'),(2900,186,'Albany','2011-06-01 01:06:40'),(2901,186,'Park','2011-06-01 01:06:40'),(2902,186,'Platte','2011-06-01 01:06:40'),(2903,186,'Goshen','2011-06-01 01:06:40'),(2904,186,'Niobrara','2011-06-01 01:06:40'),(2905,186,'Converse','2011-06-01 01:06:40'),(2906,186,'Carbon','2011-06-01 01:06:40'),(2907,186,'Fremont','2011-06-01 01:06:40'),(2908,186,'Sweetwater','2011-06-01 01:06:40'),(2909,186,'Washakie','2011-06-01 01:06:40'),(2910,186,'Big Horn','2011-06-01 01:06:40'),(2911,186,'Hot Springs','2011-06-01 01:06:40'),(2912,186,'Natrona','2011-06-01 01:06:40'),(2913,186,'Johnson','2011-06-01 01:06:40'),(2914,186,'Weston','2011-06-01 01:06:40'),(2915,186,'Crook','2011-06-01 01:06:40'),(2916,186,'Campbell','2011-06-01 01:06:40'),(2917,186,'Sheridan','2011-06-01 01:06:40'),(2918,186,'Sublette','2011-06-01 01:06:40'),(2919,186,'Uinta','2011-06-01 01:06:40'),(2920,186,'Teton','2011-06-01 01:06:40'),(2921,186,'Lincoln','2011-06-01 01:06:40'),(2922,143,'Bannock','2011-06-01 01:06:40'),(2923,143,'Bingham','2011-06-01 01:06:40'),(2924,143,'Power','2011-06-01 01:06:40'),(2925,143,'Butte','2011-06-01 01:06:40'),(2926,143,'Caribou','2011-06-01 01:06:40'),(2927,143,'Bear Lake','2011-06-01 01:06:40'),(2928,143,'Custer','2011-06-01 01:06:40'),(2929,143,'Franklin','2011-06-01 01:06:40'),(2930,143,'Lemhi','2011-06-01 01:06:40'),(2931,143,'Oneida','2011-06-01 01:06:40'),(2932,143,'Twin Falls','2011-06-01 01:06:40'),(2933,143,'Cassia','2011-06-01 01:06:40'),(2934,143,'Blaine','2011-06-01 01:06:40'),(2935,143,'Gooding','2011-06-01 01:06:40'),(2936,143,'Camas','2011-06-01 01:06:40'),(2937,143,'Lincoln','2011-06-01 01:06:40'),(2938,143,'Jerome','2011-06-01 01:06:40'),(2939,143,'Minidoka','2011-06-01 01:06:40'),(2940,143,'Bonneville','2011-06-01 01:06:40'),(2941,143,'Fremont','2011-06-01 01:06:40'),(2942,143,'Teton','2011-06-01 01:06:40'),(2943,143,'Clark','2011-06-01 01:06:40'),(2944,143,'Jefferson','2011-06-01 01:06:40'),(2945,143,'Madison','2011-06-01 01:06:40'),(2946,143,'Nez Perce','2011-06-01 01:06:40'),(2947,143,'Clearwater','2011-06-01 01:06:40'),(2948,143,'Idaho','2011-06-01 01:06:40'),(2949,143,'Lewis','2011-06-01 01:06:40'),(2950,143,'Latah','2011-06-01 01:06:40'),(2951,143,'Elmore','2011-06-01 01:06:40'),(2952,143,'Boise','2011-06-01 01:06:40'),(2953,143,'Owyhee','2011-06-01 01:06:40'),(2954,143,'Canyon','2011-06-01 01:06:40'),(2955,143,'Washington','2011-06-01 01:06:40'),(2956,143,'Valley','2011-06-01 01:06:40'),(2957,143,'Adams','2011-06-01 01:06:40'),(2958,143,'Ada','2011-06-01 01:06:40'),(2959,143,'Gem','2011-06-01 01:06:40'),(2960,143,'Payette','2011-06-01 01:06:40'),(2961,143,'Kootenai','2011-06-01 01:06:40'),(2962,143,'Shoshone','2011-06-01 01:06:40'),(2963,143,'Bonner','2011-06-01 01:06:40'),(2964,143,'Boundary','2011-06-01 01:06:40'),(2965,143,'Benewah','2011-06-01 01:06:40'),(2966,179,'Duchesne','2011-06-01 01:06:40'),(2967,179,'Utah','2011-06-01 01:06:40'),(2968,179,'Salt Lake','2011-06-01 01:06:40'),(2969,179,'Uintah','2011-06-01 01:06:40'),(2970,179,'Davis','2011-06-01 01:06:40'),(2971,179,'Summit','2011-06-01 01:06:40'),(2972,179,'Morgan','2011-06-01 01:06:40'),(2973,179,'Tooele','2011-06-01 01:06:40'),(2974,179,'Daggett','2011-06-01 01:06:40'),(2975,179,'Rich','2011-06-01 01:06:40'),(2976,179,'Wasatch','2011-06-01 01:06:40'),(2977,179,'Weber','2011-06-01 01:06:40'),(2978,179,'Box Elder','2011-06-01 01:06:40'),(2979,179,'Cache','2011-06-01 01:06:40'),(2980,179,'Carbon','2011-06-01 01:06:40'),(2981,179,'San Juan','2011-06-01 01:06:40'),(2982,179,'Emery','2011-06-01 01:06:40'),(2983,179,'Grand','2011-06-01 01:06:40'),(2984,179,'Sevier','2011-06-01 01:06:40'),(2985,179,'Sanpete','2011-06-01 01:06:40'),(2986,179,'Millard','2011-06-01 01:06:40'),(2987,179,'Juab','2011-06-01 01:06:40'),(2988,179,'Kane','2011-06-01 01:06:40'),(2989,179,'Beaver','2011-06-01 01:06:40'),(2990,179,'Iron','2011-06-01 01:06:40'),(2991,179,'Wayne','2011-06-01 01:06:40'),(2992,179,'Washington','2011-06-01 01:06:40'),(2993,179,'Piute','2011-06-01 01:06:40'),(2994,131,'Maricopa','2011-06-01 01:06:40'),(2995,131,'Pinal','2011-06-01 01:06:40'),(2996,131,'Gila','2011-06-01 01:06:40'),(2997,131,'Pima','2011-06-01 01:06:40'),(2998,131,'Yavapai','2011-06-01 01:06:40'),(2999,131,'La Paz','2011-06-01 01:06:40'),(3000,131,'Yuma','2011-06-01 01:06:40'),(3001,131,'Mohave','2011-06-01 01:06:40'),(3002,131,'Graham','2011-06-01 01:06:40'),(3003,131,'Greenlee','2011-06-01 01:06:40'),(3004,131,'Cochise','2011-06-01 01:06:40'),(3005,131,'Santa Cruz','2011-06-01 01:06:40'),(3006,131,'Navajo','2011-06-01 01:06:40'),(3007,131,'Apache','2011-06-01 01:06:40'),(3008,131,'Coconino','2011-06-01 01:06:40'),(3009,163,'Sandoval','2011-06-01 01:06:40'),(3010,163,'Valencia','2011-06-01 01:06:40'),(3011,163,'Cibola','2011-06-01 01:06:40'),(3012,163,'Bernalillo','2011-06-01 01:06:40'),(3013,163,'Torrance','2011-06-01 01:06:40'),(3014,163,'Santa Fe','2011-06-01 01:06:40'),(3015,163,'Socorro','2011-06-01 01:06:40'),(3016,163,'Rio Arriba','2011-06-01 01:06:40'),(3017,163,'San Juan','2011-06-01 01:06:40'),(3018,163,'McKinley','2011-06-01 01:06:40'),(3019,163,'Taos','2011-06-01 01:06:40'),(3020,163,'San Miguel','2011-06-01 01:06:40'),(3021,163,'Los Alamos','2011-06-01 01:06:40'),(3022,163,'Colfax','2011-06-01 01:06:40'),(3023,163,'Guadalupe','2011-06-01 01:06:40'),(3024,163,'Mora','2011-06-01 01:06:40'),(3025,163,'Harding','2011-06-01 01:06:40'),(3026,163,'Catron','2011-06-01 01:06:40'),(3027,163,'Sierra','2011-06-01 01:06:40'),(3028,163,'Dona Ana','2011-06-01 01:06:40'),(3029,163,'Hidalgo','2011-06-01 01:06:40'),(3030,163,'Grant','2011-06-01 01:06:40'),(3031,163,'Luna','2011-06-01 01:06:40'),(3032,163,'Curry','2011-06-01 01:06:40'),(3033,163,'Roosevelt','2011-06-01 01:06:40'),(3034,163,'Lea','2011-06-01 01:06:40'),(3035,163,'De Baca','2011-06-01 01:06:40'),(3036,163,'Quay','2011-06-01 01:06:40'),(3037,163,'Chaves','2011-06-01 01:06:40'),(3038,163,'Eddy','2011-06-01 01:06:40'),(3039,163,'Lincoln','2011-06-01 01:06:40'),(3040,163,'Otero','2011-06-01 01:06:40'),(3041,163,'Union','2011-06-01 01:06:40'),(3042,160,'Clark','2011-06-01 01:06:40'),(3043,160,'Lincoln','2011-06-01 01:06:40'),(3044,160,'Nye','2011-06-01 01:06:40'),(3045,160,'Esmeralda','2011-06-01 01:06:40'),(3046,160,'White Pine','2011-06-01 01:06:40'),(3047,160,'Lander','2011-06-01 01:06:40'),(3048,160,'Eureka','2011-06-01 01:06:40'),(3049,160,'Washoe','2011-06-01 01:06:40'),(3050,160,'Lyon','2011-06-01 01:06:40'),(3051,160,'Humboldt','2011-06-01 01:06:40'),(3052,160,'Churchill','2011-06-01 01:06:40'),(3053,160,'Douglas','2011-06-01 01:06:40'),(3054,160,'Mineral','2011-06-01 01:06:40'),(3055,160,'Pershing','2011-06-01 01:06:40'),(3056,160,'Storey','2011-06-01 01:06:40'),(3057,160,'Carson City','2011-06-01 01:06:40'),(3058,160,'Elko','2011-06-01 01:06:40'),(3059,133,'Los Angeles','2011-06-01 01:06:40'),(3060,133,'Orange','2011-06-01 01:06:40'),(3061,133,'Ventura','2011-06-01 01:06:40'),(3062,133,'San Bernardino','2011-06-01 01:06:40'),(3063,133,'Riverside','2011-06-01 01:06:40'),(3064,133,'San Diego','2011-06-01 01:06:40'),(3065,133,'Imperial','2011-06-01 01:06:40'),(3066,133,'Inyo','2011-06-01 01:06:40'),(3067,133,'Santa Barbara','2011-06-01 01:06:40'),(3068,133,'Tulare','2011-06-01 01:06:40'),(3069,133,'Kings','2011-06-01 01:06:40'),(3070,133,'Kern','2011-06-01 01:06:40'),(3071,133,'Fresno','2011-06-01 01:06:40'),(3072,133,'San Luis Obispo','2011-06-01 01:06:40'),(3073,133,'Monterey','2011-06-01 01:06:40'),(3074,133,'Mono','2011-06-01 01:06:40'),(3075,133,'Madera','2011-06-01 01:06:40'),(3076,133,'Merced','2011-06-01 01:06:40'),(3077,133,'Mariposa','2011-06-01 01:06:40'),(3078,133,'San Mateo','2011-06-01 01:06:40'),(3079,133,'Santa Clara','2011-06-01 01:06:40'),(3080,133,'San Francisco','2011-06-01 01:06:40'),(3081,133,'Sacramento','2011-06-01 01:06:40'),(3082,133,'Alameda','2011-06-01 01:06:40'),(3083,133,'Napa','2011-06-01 01:06:40'),
   (3084,133,'Contra Costa','2011-06-01 01:06:40'),(3085,133,'Solano','2011-06-01 01:06:40'),(3086,133,'Marin','2011-06-01 01:06:40'),(3087,133,'Sonoma','2011-06-01 01:06:40'),(3088,133,'Santa Cruz','2011-06-01 01:06:40'),(3089,133,'San Benito','2011-06-01 01:06:40'),(3090,133,'San Joaquin','2011-06-01 01:06:40'),(3091,133,'Calaveras','2011-06-01 01:06:40'),(3092,133,'Tuolumne','2011-06-01 01:06:40'),(3093,133,'Stanislaus','2011-06-01 01:06:40'),(3094,133,'Mendocino','2011-06-01 01:06:40'),(3095,133,'Lake','2011-06-01 01:06:40'),(3096,133,'Humboldt','2011-06-01 01:06:40'),(3097,133,'Trinity','2011-06-01 01:06:40'),(3098,133,'Del Norte','2011-06-01 01:06:40'),(3099,133,'Siskiyou','2011-06-01 01:06:40'),(3100,133,'Amador','2011-06-01 01:06:40'),(3101,133,'Placer','2011-06-01 01:06:40'),(3102,133,'Yolo','2011-06-01 01:06:40'),(3103,133,'El Dorado','2011-06-01 01:06:40'),(3104,133,'Alpine','2011-06-01 01:06:40'),(3105,133,'Sutter','2011-06-01 01:06:40'),(3106,133,'Yuba','2011-06-01 01:06:40'),(3107,133,'Nevada','2011-06-01 01:06:40'),(3108,133,'Sierra','2011-06-01 01:06:40'),(3109,133,'Colusa','2011-06-01 01:06:40'),(3110,133,'Glenn','2011-06-01 01:06:40'),(3111,133,'Butte','2011-06-01 01:06:40'),(3112,133,'Plumas','2011-06-01 01:06:40'),(3113,133,'Shasta','2011-06-01 01:06:40'),(3114,133,'Modoc','2011-06-01 01:06:40'),(3115,133,'Lassen','2011-06-01 01:06:40'),(3116,133,'Tehama','2011-06-01 01:06:40'),(3117,142,'Honolulu','2011-06-01 01:06:40'),(3118,142,'Kauai','2011-06-01 01:06:40'),(3119,142,'Hawaii','2011-06-01 01:06:40'),(3120,142,'Maui','2011-06-01 01:06:40'),(3121,130,'American Samoa','2011-06-01 01:06:40'),(3122,141,'Guam','2011-06-01 01:06:40'),(3123,171,'Palau','2011-06-01 01:06:40'),(3124,138,'Federated States of Micro','2011-06-01 01:06:40'),(3125,167,'Northern Mariana Islands','2011-06-01 01:06:40'),(3126,151,'Marshall Islands','2011-06-01 01:06:40'),(3127,170,'Wasco','2011-06-01 01:06:40'),(3128,170,'Marion','2011-06-01 01:06:40'),(3129,170,'Clackamas','2011-06-01 01:06:40'),(3130,170,'Washington','2011-06-01 01:06:40'),(3131,170,'Multnomah','2011-06-01 01:06:40'),(3132,170,'Hood River','2011-06-01 01:06:40'),(3133,170,'Columbia','2011-06-01 01:06:40'),(3134,170,'Sherman','2011-06-01 01:06:40'),(3135,170,'Yamhill','2011-06-01 01:06:40'),(3136,170,'Clatsop','2011-06-01 01:06:40'),(3137,170,'Tillamook','2011-06-01 01:06:40'),(3138,170,'Polk','2011-06-01 01:06:40'),(3139,170,'Linn','2011-06-01 01:06:40'),(3140,170,'Benton','2011-06-01 01:06:40'),(3141,170,'Lincoln','2011-06-01 01:06:40'),(3142,170,'Lane','2011-06-01 01:06:40'),(3143,170,'Curry','2011-06-01 01:06:40'),(3144,170,'Coos','2011-06-01 01:06:40'),(3145,170,'Douglas','2011-06-01 01:06:40'),(3146,170,'Klamath','2011-06-01 01:06:40'),(3147,170,'Josephine','2011-06-01 01:06:40'),(3148,170,'Jackson','2011-06-01 01:06:40'),(3149,170,'Lake','2011-06-01 01:06:40'),(3150,170,'Deschutes','2011-06-01 01:06:40'),(3151,170,'Harney','2011-06-01 01:06:40'),(3152,170,'Jefferson','2011-06-01 01:06:40'),(3153,170,'Wheeler','2011-06-01 01:06:40'),(3154,170,'Crook','2011-06-01 01:06:40'),(3155,170,'Umatilla','2011-06-01 01:06:40'),(3156,170,'Gilliam','2011-06-01 01:06:40'),(3157,170,'Baker','2011-06-01 01:06:40'),(3158,170,'Grant','2011-06-01 01:06:40'),(3159,170,'Morrow','2011-06-01 01:06:40'),(3160,170,'Union','2011-06-01 01:06:40'),(3161,170,'Wallowa','2011-06-01 01:06:40'),(3162,170,'Malheur','2011-06-01 01:06:40'),(3163,183,'King','2011-06-01 01:06:40'),(3164,183,'Snohomish','2011-06-01 01:06:40'),(3165,183,'Kitsap','2011-06-01 01:06:40'),(3166,183,'Whatcom','2011-06-01 01:06:40'),(3167,183,'Skagit','2011-06-01 01:06:40'),(3168,183,'San Juan','2011-06-01 01:06:40'),(3169,183,'Island','2011-06-01 01:06:40'),(3170,183,'Pierce','2011-06-01 01:06:40'),(3171,183,'Clallam','2011-06-01 01:06:40'),(3172,183,'Jefferson','2011-06-01 01:06:40'),(3173,183,'Lewis','2011-06-01 01:06:40'),(3174,183,'Thurston','2011-06-01 01:06:40'),(3175,183,'Grays Harbor','2011-06-01 01:06:40'),(3176,183,'Mason','2011-06-01 01:06:40'),(3177,183,'Pacific','2011-06-01 01:06:40'),(3178,183,'Cowlitz','2011-06-01 01:06:40'),(3179,183,'Clark','2011-06-01 01:06:40'),(3180,183,'Klickitat','2011-06-01 01:06:40'),(3181,183,'Skamania','2011-06-01 01:06:40'),(3182,183,'Wahkiakum','2011-06-01 01:06:40'),(3183,183,'Chelan','2011-06-01 01:06:40'),(3184,183,'Douglas','2011-06-01 01:06:40'),(3185,183,'Okanogan','2011-06-01 01:06:40'),(3186,183,'Grant','2011-06-01 01:06:40'),(3187,183,'Yakima','2011-06-01 01:06:40'),(3188,183,'Kittitas','2011-06-01 01:06:40'),(3189,183,'Spokane','2011-06-01 01:06:40'),(3190,183,'Lincoln','2011-06-01 01:06:40'),(3191,183,'Stevens','2011-06-01 01:06:40'),(3192,183,'Whitman','2011-06-01 01:06:40'),(3193,183,'Adams','2011-06-01 01:06:40'),(3194,183,'Ferry','2011-06-01 01:06:40'),(3195,183,'Pend Oreille','2011-06-01 01:06:40'),(3196,183,'Franklin','2011-06-01 01:06:40'),(3197,183,'Benton','2011-06-01 01:06:40'),(3198,183,'Walla Walla','2011-06-01 01:06:40'),(3199,183,'Columbia','2011-06-01 01:06:40'),(3200,183,'Garfield','2011-06-01 01:06:40'),(3201,183,'Asotin','2011-06-01 01:06:40'),(3202,128,'Anchorage','2011-06-01 01:06:40'),(3203,128,'Bethel','2011-06-01 01:06:40'),(3204,128,'Aleutians West','2011-06-01 01:06:40'),(3205,128,'Lake And Peninsula','2011-06-01 01:06:40'),(3206,128,'Kodiak Island','2011-06-01 01:06:40'),(3207,128,'Aleutians East','2011-06-01 01:06:40'),(3208,128,'Wade Hampton','2011-06-01 01:06:40'),(3209,128,'Dillingham','2011-06-01 01:06:40'),(3210,128,'Kenai Peninsula','2011-06-01 01:06:40'),(3211,128,'Yukon Koyukuk','2011-06-01 01:06:40'),(3212,128,'Valdez Cordova','2011-06-01 01:06:40'),(3213,128,'Matanuska Susitna','2011-06-01 01:06:40'),(3214,128,'Bristol Bay','2011-06-01 01:06:40'),(3215,128,'Nome','2011-06-01 01:06:40'),(3216,128,'Yakutat','2011-06-01 01:06:40'),(3217,128,'Fairbanks North Star','2011-06-01 01:06:40'),(3218,128,'Denali','2011-06-01 01:06:40'),(3219,128,'North Slope','2011-06-01 01:06:40'),(3220,128,'Northwest Arctic','2011-06-01 01:06:40'),(3221,128,'Southeast Fairbanks','2011-06-01 01:06:40'),(3222,128,'Juneau','2011-06-01 01:06:40'),(3223,128,'Skagway Hoonah Angoon','2011-06-01 01:06:40'),(3224,128,'Haines','2011-06-01 01:06:40'),(3225,128,'Wrangell Petersburg','2011-06-01 01:06:40'),(3226,128,'Sitka','2011-06-01 01:06:40'),(3227,128,'Ketchikan Gateway','2011-06-01 01:06:40'),(3228,128,'Prince Wales Ketchikan','2011-06-01 01:06:40'),(3229,4,'Coconino County','2011-09-13 23:01:20'),(3230,131,'Coconino County','2011-09-13 23:01:20'),(3232,209,'Carleton','2011-09-20 06:54:42'),(3233,4,'Yavapai County','2011-09-20 19:41:27'),(3234,131,'Yavapai County','2011-09-20 19:41:27'),(3236,4,'Maricopa County','2011-09-22 20:06:14'),(3237,131,'Maricopa County','2011-09-22 20:06:14'),(3239,33,'Pershing County','2011-09-22 21:00:37'),(3240,160,'Pershing County','2011-09-22 21:00:37'),(3242,51,'Collingsworth County','2011-09-22 22:56:40'),(3243,178,'Collingsworth County','2011-09-22 22:56:40'),(3245,4,'Mohave County','2011-09-22 23:03:03'),(3246,131,'Mohave County','2011-09-22 23:03:03'),(3248,33,'Humboldt County','2011-09-27 22:41:42'),(3249,160,'Humboldt County','2011-09-27 22:41:42'),(3251,33,'Lyon County','2011-09-29 22:03:12'),(3252,160,'Lyon County','2011-09-29 22:03:12'),(3254,33,'Washoe County','2011-10-04 19:47:23'),(3255,160,'Washoe County','2011-10-04 19:47:23'),(3257,33,'Nye County','2011-10-04 19:52:14'),(3258,160,'Nye County','2011-10-04 19:52:14'),(3260,33,'Eureka County','2011-10-04 20:11:47'),(3261,160,'Eureka County','2011-10-04 20:11:47'),(3263,33,'Lander County','2011-10-04 21:03:57'),(3264,160,'Lander County','2011-10-04 21:03:57'),(3266,33,'White Pine County','2011-10-04 21:07:31'),(3267,160,'White Pine County','2011-10-04 21:07:31'),(3269,33,'Douglas County','2011-10-04 21:16:07'),(3270,160,'Douglas County','2011-10-04 21:16:07'),(3272,33,'Clark County','2011-10-04 21:27:29'),(3273,160,'Clark County','2011-10-04 21:27:29'),(3275,4,'Cochise County','2011-10-06 18:37:45'),(3276,131,'Cochise County','2011-10-06 18:37:45'),(3278,4,'Navajo County','2011-10-08 22:39:35'),(3279,131,'Navajo County','2011-10-08 22:39:35'),(3281,50,'Travis','2011-10-09 19:50:56'),(3282,177,'Travis','2011-10-09 19:50:56'),(3284,33,'Churchill County','2011-10-13 22:09:02'),(3285,160,'Churchill County','2011-10-13 22:09:02'),(3287,33,'Storey County','2011-10-13 22:31:08'),(3288,160,'Storey County','2011-10-13 22:31:08'),(3290,43,'Owyhee','2011-10-14 22:22:56'),(3291,170,'Owyhee','2011-10-14 22:22:56'),(3293,6,'Mendocino County','2011-10-23 06:09:03'),(3294,133,'Mendocino County','2011-10-23 06:09:03'),(3296,6,'Inyo County','2011-10-23 06:15:39'),(3297,133,'Inyo County','2011-10-23 06:15:39'),(3299,6,'Mono County','2011-10-23 07:01:13'),(3300,133,'Mono County','2011-10-23 07:01:13'),(3302,4,'Pima County','2011-10-29 18:02:10'),(3303,131,'Pima County','2011-10-29 18:02:10'),(3305,424,'Matale','2011-10-31 20:19:50'),(3306,52,'Garfield County','2011-11-05 22:25:24'),(3307,179,'Garfield County','2011-11-05 22:25:24'),(3309,447,'Starogard Gdaski','2011-11-16 18:08:01'),(3310,449,'Zwickau','2011-11-16 18:30:16'),(3311,4,'Pinal County','2011-11-18 18:37:50'),(3312,131,'Pinal County','2011-11-18 18:37:50'),(3314,4,'Graham County','2011-11-20 02:21:02'),(3315,131,'Graham County','2011-11-20 02:21:02'),(3317,52,'San Juan County','2011-11-22 22:05:55'),(3318,179,'San Juan County','2011-11-22 22:05:55'),(3320,197,'San Felipe de JesÃºs','2011-11-23 18:05:58'),(3321,211,'San Felipe de JesÃºs','2011-11-23 18:05:58'),(3323,197,'YÃ©cora','2011-11-23 18:08:45'),(3324,211,'YÃ©cora','2011-11-23 18:08:45'),(3326,197,'Cucurpe','2011-11-25 18:13:39'),(3327,211,'Cucurpe','2011-11-25 18:13:39'),(3329,197,'Ãlamos','2011-12-09 17:49:29'),(3330,211,'Alamos','2011-12-09 17:49:29'),(3331,231,'Aguascalientes','2016-05-17 02:26:11'),(3332,231,'Asientos','2016-05-17 02:26:11'),(3333,231,'Calvillo','2016-05-17 02:26:11'),(3334,231,'CosÃ­o','2016-05-17 02:26:11'),(3335,231,'JesÃºs MarÃ­a','2016-05-17 02:26:11'),(3336,231,'PabellÃ³n de Arteaga','2016-05-17 02:26:11'),(3337,231,'RincÃ³n de Romos','2016-05-17 02:26:11'),(3338,231,'San JosÃ© de Gracia','2016-05-17 02:26:11'),(3339,231,'TepezalÃ¡','2016-05-17 02:26:11'),(3340,231,'El Llano','2016-05-17 02:26:11'),(3341,231,'San Francisco de los Romo','2016-05-17 02:26:12'),(3342,198,'Ensenada','2016-05-17 02:26:12'),(3343,198,'Mexicali','2016-05-17 02:26:12'),(3344,198,'Tecate','2016-05-17 02:26:12'),(3345,198,'Tijuana','2016-05-17 02:26:12'),(3346,198,'Playas de Rosarito','2016-05-17 02:26:12'),(3347,253,'ComondÃº','2016-05-17 02:26:12'),(3348,253,'MulegÃ©','2016-05-17 02:26:12'),(3349,253,'La Paz','2016-05-17 02:26:12'),(3350,253,'Los Cabos','2016-05-17 02:26:12'),(3351,253,'Loreto','2016-05-17 02:26:12'),(3352,263,'CalkinÃ­','2016-05-17 02:26:12'),(3353,263,'Campeche','2016-05-17 02:26:12'),(3354,263,'Carmen','2016-05-17 02:26:13'),(3355,263,'ChampotÃ³n','2016-05-17 02:26:13'),(3356,263,'HecelchakÃ¡n','2016-05-17 02:26:13'),(3357,263,'HopelchÃ©n','2016-05-17 02:26:13'),(3358,263,'Palizada','2016-05-17 02:26:13'),(3359,263,'Tenabo','2016-05-17 02:26:13'),(3360,263,'EscÃ¡rcega','2016-05-17 02:26:13'),(3361,263,'Calakmul','2016-05-17 02:26:13'),(3362,263,'Candelaria','2016-05-17 02:26:13'),(3363,273,'Acacoyagua','2016-05-17 02:26:13'),(3364,273,'Acala','2016-05-17 02:26:13'),(3365,273,'Acapetahua','2016-05-17 02:26:13'),(3366,273,'Altamirano','2016-05-17 02:26:14'),(3367,273,'AmatÃ¡n','2016-05-17 02:26:14'),(3368,273,'Amatenango de la Frontera','2016-05-17 02:26:14'),(3369,273,'Amatenango del Valle','2016-05-17 02:26:14'),(3370,273,'Angel Albino Corzo','2016-05-17 02:26:14'),(3371,273,'Arriaga','2016-05-17 02:26:14'),(3372,273,'Bejucal de Ocampo','2016-05-17 02:26:14'),(3373,273,'Bella Vista','2016-05-17 02:26:14'),(3374,273,'BerriozÃ¡bal','2016-05-17 02:26:14'),(3375,273,'Bochil','2016-05-17 02:26:14'),(3376,273,'El Bosque','2016-05-17 02:26:14'),(3377,273,'CacahoatÃ¡n','2016-05-17 02:26:14'),(3378,273,'CatazajÃ¡','2016-05-17 02:26:14'),(3379,273,'Cintalapa','2016-05-17 02:26:15'),(3380,273,'Coapilla','2016-05-17 02:26:15'),(3381,273,'ComitÃ¡n de DomÃ­nguez','2016-05-17 02:26:15'),(3382,273,'La Concordia','2016-05-17 02:26:15'),(3383,273,'CopainalÃ¡','2016-05-17 02:26:15'),(3384,273,'ChalchihuitÃ¡n','2016-05-17 02:26:15'),(3385,273,'Chamula','2016-05-17 02:26:15'),(3386,273,'Chanal','2016-05-17 02:26:15'),(3387,273,'Chapultenango','2016-05-17 02:26:15'),(3388,273,'ChenalhÃ³','2016-05-17 02:26:15'),(3389,273,'Chiapa de Corzo','2016-05-17 02:26:15'),(3390,273,'Chiapilla','2016-05-17 02:26:15'),(3391,273,'ChicoasÃ©n','2016-05-17 02:26:16'),(3392,273,'Chicomuselo','2016-05-17 02:26:16'),(3393,273,'ChilÃ³n','2016-05-17 02:26:16'),(3394,273,'Escuintla','2016-05-17 02:26:16'),(3395,273,'Francisco LeÃ³n','2016-05-17 02:26:16'),(3396,273,'Frontera Comalapa','2016-05-17 02:26:16'),(3397,273,'Frontera Hidalgo','2016-05-17 02:26:16'),(3398,273,'La Grandeza','2016-05-17 02:26:16'),(3399,273,'HuehuetÃ¡n','2016-05-17 02:26:16'),(3400,273,'HuixtÃ¡n','2016-05-17 02:26:16'),(3401,273,'HuitiupÃ¡n','2016-05-17 02:26:16'),(3402,273,'Huixtla','2016-05-17 02:26:16'),(3403,273,'La Independencia','2016-05-17 02:26:17'),(3404,273,'IxhuatÃ¡n','2016-05-17 02:26:17'),(3405,273,'IxtacomitÃ¡n','2016-05-17 02:26:17'),(3406,273,'Ixtapa','2016-05-17 02:26:17'),(3407,273,'Ixtapangajoya','2016-05-17 02:26:17'),(3408,273,'Jiquipilas','2016-05-17 02:26:17'),(3409,273,'Jitotol','2016-05-17 02:26:17'),(3410,273,'JuÃ¡rez','2016-05-17 02:26:17'),(3411,273,'LarrÃ¡inzar','2016-05-17 02:26:17'),(3412,273,'La Libertad','2016-05-17 02:26:17'),(3413,273,'Mapastepec','2016-05-17 02:26:17'),(3414,273,'Las Margaritas','2016-05-17 02:26:17'),(3415,273,'Mazapa de Madero','2016-05-17 02:26:17'),(3416,273,'MazatÃ¡n','2016-05-17 02:26:18'),(3417,273,'Metapa','2016-05-17 02:26:18'),(3418,273,'Mitontic','2016-05-17 02:26:18'),(3419,273,'Motozintla','2016-05-17 02:26:18'),(3420,273,'NicolÃ¡s RuÃ­z','2016-05-17 02:26:18'),(3421,273,'Ocosingo','2016-05-17 02:26:18'),(3422,273,'Ocotepec','2016-05-17 02:26:18'),(3423,273,'Ocozocoautla de Espinosa','2016-05-17 02:26:18'),(3424,273,'OstuacÃ¡n','2016-05-17 02:26:18'),(3425,273,'Osumacinta','2016-05-17 02:26:18'),(3426,273,'Oxchuc','2016-05-17 02:26:18'),(3427,273,'Palenque','2016-05-17 02:26:18'),(3428,273,'PantelhÃ³','2016-05-17 02:26:19'),(3429,273,'Pantepec','2016-05-17 02:26:19'),(3430,273,'Pichucalco','2016-05-17 02:26:19'),(3431,273,'Pijijiapan','2016-05-17 02:26:19'),(3432,273,'El Porvenir','2016-05-17 02:26:19'),(3433,273,'Villa ComaltitlÃ¡n','2016-05-17 02:26:19'),(3434,273,'Pueblo Nuevo SolistahuacÃ¡n','2016-05-17 02:26:19'),(3435,273,'RayÃ³n','2016-05-17 02:26:19'),(3436,273,'Reforma','2016-05-17 02:26:19'),(3437,273,'Las Rosas','2016-05-17 02:26:19'),(3438,273,'Sabanilla','2016-05-17 02:26:19'),(3439,273,'Salto de Agua','2016-05-17 02:26:19'),(3440,273,'San CristÃ³bal de las Casas','2016-05-17 02:26:19'),(3441,273,'San Fernando','2016-05-17 02:26:20'),(3442,273,'Siltepec','2016-05-17 02:26:20'),(3443,273,'Simojovel','2016-05-17 02:26:20'),(3444,273,'SitalÃ¡','2016-05-17 02:26:20'),(3445,273,'Socoltenango','2016-05-17 02:26:20'),(3446,273,'Solosuchiapa','2016-05-17 02:26:20'),(3447,273,'SoyalÃ³','2016-05-17 02:26:20'),(3448,273,'Suchiapa','2016-05-17 02:26:20'),(3449,273,'Suchiate','2016-05-17 02:26:20'),(3450,273,'Sunuapa','2016-05-17 02:26:20'),(3451,273,'Tapachula','2016-05-17 02:26:20'),(3452,273,'Tapalapa','2016-05-17 02:26:20'),(3453,273,'Tapilula','2016-05-17 02:26:21'),(3454,273,'TecpatÃ¡n','2016-05-17 02:26:21'),(3455,273,'Tenejapa','2016-05-17 02:26:21'),(3456,273,'Teopisca','2016-05-17 02:26:21'),(3457,273,'Tila','2016-05-17 02:26:21'),(3458,273,'TonalÃ¡','2016-05-17 02:26:21'),(3459,273,'Totolapa','2016-05-17 02:26:21'),(3460,273,'La Trinitaria','2016-05-17 02:26:21'),(3461,273,'TumbalÃ¡','2016-05-17 02:26:21'),(3462,273,'Tuxtla GutiÃ©rrez','2016-05-17 02:26:21'),(3463,273,'Tuxtla Chico','2016-05-17 02:26:21'),(3464,273,'TuzantÃ¡n','2016-05-17 02:26:21'),(3465,273,'Tzimol','2016-05-17 02:26:21'),(3466,273,'UniÃ³n JuÃ¡rez','2016-05-17 02:26:22'),(3467,273,'Venustiano Carranza','2016-05-17 02:26:22'),(3468,273,'Villa Corzo','2016-05-17 02:26:22'),(3469,273,'Villaflores','2016-05-17 02:26:22'),(3470,273,'YajalÃ³n','2016-05-17 02:26:22'),(3471,273,'San Lucas','2016-05-17 02:26:22'),(3472,273,'ZinacantÃ¡n','2016-05-17 02:26:22'),(3473,273,'San Juan Cancuc','2016-05-17 02:26:22'),(3474,273,'Aldama','2016-05-17 02:26:22'),(3475,273,'BenemÃ©rito de las AmÃ©ricas','2016-05-17 02:26:22'),(3476,273,'Maravilla Tenejapa','2016-05-17 02:26:22'),(3477,273,'MarquÃ©s de Comillas','2016-05-17 02:26:22'),(3478,273,'Montecristo de Guerrero','2016-05-17 02:26:23'),(3479,273,'San AndrÃ©s Duraznal','2016-05-17 02:26:23'),(3480,273,'Santiago el Pinar','2016-05-17 02:26:23'),(3481,273,'Belisario DomÃ­nguez','2016-05-17 02:26:23'),(3482,273,'Emiliano Zapata','2016-05-17 02:26:23'),(3483,273,'El Parral','2016-05-17 02:26:23'),(3484,273,'Mezcalapa','2016-05-17 02:26:23'),(3485,206,'Ahumada','2016-05-17 02:26:23'),(3486,206,'Aldama','2016-05-17 02:26:23'),(3487,206,'Allende','2016-05-17 02:26:23'),(3488,206,'Aquiles SerdÃ¡n','2016-05-17 02:26:23'),(3489,206,'AscensiÃ³n','2016-05-17 02:26:23'),(3490,206,'Bachiniva','2016-05-17 02:26:23'),(3491,206,'Balleza','2016-05-17 02:26:24'),(3492,206,'Batopilas','2016-05-17 02:26:24'),(3493,206,'Bocoyna','2016-05-17 02:26:24'),(3494,206,'Buenaventura','2016-05-17 02:26:24'),(3495,206,'Camargo','2016-05-17 02:26:24'),(3496,206,'Carichi','2016-05-17 02:26:24'),(3497,206,'Casas Grandes','2016-05-17 02:26:24'),(3498,206,'Coronado','2016-05-17 02:26:24'),(3499,206,'Coyame del Sotol','2016-05-17 02:26:24'),(3500,206,'La Cruz','2016-05-17 02:26:24'),(3501,206,'CuauhtÃ©moc','2016-05-17 02:26:24'),(3502,206,'Cusihuiriachi','2016-05-17 02:26:24'),(3503,206,'Chihuahua','2016-05-17 02:26:25'),(3504,206,'ChÃ­nipas','2016-05-17 02:26:25'),(3505,206,'Delicias','2016-05-17 02:26:25'),(3506,206,'Dr. Belisario DomÃ­nguez','2016-05-17 02:26:25'),(3507,206,'Galeana','2016-05-17 02:26:25'),(3508,206,'Santa Isabel','2016-05-17 02:26:25'),(3509,206,'GÃ³mez FarÃ­as','2016-05-17 02:26:25'),(3510,206,'Gran Morelos','2016-05-17 02:26:25'),(3511,206,'Guachochi','2016-05-17 02:26:25'),(3512,206,'Guadalupe','2016-05-17 02:26:25'),(3513,206,'Guadalupe y Calvo','2016-05-17 02:26:25'),(3514,206,'Guazapares','2016-05-17 02:26:25'),(3515,206,'Guerrero','2016-05-17 02:26:25'),(3516,206,'Hidalgo del Parral','2016-05-17 02:26:26'),(3517,206,'HuejotitÃ¡n','2016-05-17 02:26:26'),(3518,206,'Ignacio Zaragoza','2016-05-17 02:26:26'),(3519,206,'Janos','2016-05-17 02:26:26'),(3520,206,'JimÃ©nez','2016-05-17 02:26:26'),(3521,206,'JuÃ¡rez','2016-05-17 02:26:26'),(3522,206,'Julimes','2016-05-17 02:26:26'),(3523,206,'LÃ³pez','2016-05-17 02:26:26'),(3524,206,'Madera','2016-05-17 02:26:26'),(3525,206,'Maguarichi','2016-05-17 02:26:26'),(3526,206,'Manuel Benavides','2016-05-17 02:26:26'),(3527,206,'Matachi','2016-05-17 02:26:26'),(3528,206,'Matamoros','2016-05-17 02:26:27'),(3529,206,'Meoqui','2016-05-17 02:26:27'),(3530,206,'Morelos','2016-05-17 02:26:27'),(3531,206,'Moris','2016-05-17 02:26:27'),(3532,206,'Namiquipa','2016-05-17 02:26:27'),(3533,206,'Nonoava','2016-05-17 02:26:27'),(3534,206,'Nuevo Casas Grandes','2016-05-17 02:26:27'),(3535,206,'Ocampo','2016-05-17 02:26:27'),(3536,206,'Ojinaga','2016-05-17 02:26:27'),(3537,206,'PrÃ¡xedis G. Guerrero','2016-05-17 02:26:27'),(3538,206,'Riva Palacio','2016-05-17 02:26:27'),(3539,206,'Rosales','2016-05-17 02:26:27'),(3540,206,'Rosario','2016-05-17 02:26:27'),(3541,206,'San Francisco de Borja','2016-05-17 02:26:28'),(3542,206,'San Francisco de Conchos','2016-05-17 02:26:28'),(3543,206,'San Francisco del Oro','2016-05-17 02:26:28'),(3544,206,'Santa BÃ¡rbara','2016-05-17 02:26:28'),(3545,206,'Satevo','2016-05-17 02:26:28'),(3546,206,'Saucillo','2016-05-17 02:26:28'),(3547,206,'TemÃ³sachi','2016-05-17 02:26:28'),(3548,206,'El Tule','2016-05-17 02:26:28'),(3549,206,'Urique','2016-05-17 02:26:28'),(3550,206,'Uruachi','2016-05-17 02:26:28'),(3551,206,'Valle de Zaragoza','2016-05-17 02:26:28'),(3552,205,'Abasolo','2016-05-17 02:26:28'),(3553,205,'AcuÃ±a','2016-05-17 02:26:29'),(3554,205,'Allende','2016-05-17 02:26:29'),(3555,205,'Arteaga','2016-05-17 02:26:29'),(3556,205,'Candela','2016-05-17 02:26:29'),(3557,205,'CastaÃ±os','2016-05-17 02:26:29'),(3558,205,'CuatrociÃ©negas','2016-05-17 02:26:29'),(3559,205,'Escobedo','2016-05-17 02:26:29'),(3560,205,'Francisco I. Madero','2016-05-17 02:26:29'),(3561,205,'Frontera','2016-05-17 02:26:29'),(3562,205,'General Cepeda','2016-05-17 02:26:29'),(3563,205,'Guerrero','2016-05-17 02:26:29'),(3564,205,'Hidalgo','2016-05-17 02:26:29'),(3565,205,'JimÃ©nez','2016-05-17 02:26:30'),(3566,205,'JuÃ¡rez','2016-05-17 02:26:30'),(3567,205,'Lamadrid','2016-05-17 02:26:30'),(3568,205,'Matamoros','2016-05-17 02:26:30'),(3569,205,'Monclova','2016-05-17 02:26:30'),(3570,205,'Morelos','2016-05-17 02:26:30'),(3571,205,'MÃºzquiz','2016-05-17 02:26:30'),(3572,205,'Nadadores','2016-05-17 02:26:30'),(3573,205,'Nava','2016-05-17 02:26:30'),(3574,205,'Ocampo','2016-05-17 02:26:30'),(3575,205,'Parras','2016-05-17 02:26:30'),(3576,205,'Piedras Negras','2016-05-17 02:26:30'),(3577,205,'Progreso','2016-05-17 02:26:31'),(3578,205,'Ramos Arizpe','2016-05-17 02:26:31'),(3579,205,'Sabinas','2016-05-17 02:26:31'),(3580,205,'Sacramento','2016-05-17 02:26:31'),(3581,205,'Saltillo','2016-05-17 02:26:31'),(3582,205,'San Buenaventura','2016-05-17 02:26:31'),(3583,205,'San Juan de Sabinas','2016-05-17 02:26:31'),(3584,205,'San Pedro de las Colonias','2016-05-17 02:26:31'),(3585,205,'Sierra Mojada','2016-05-17 02:26:31'),(3586,205,'TorreÃ³n','2016-05-17 02:26:31'),(3587,205,'Viesca','2016-05-17 02:26:31'),(3588,205,'Villa UniÃ³n','2016-05-17 02:26:31'),(3589,205,'Zaragoza','2016-05-17 02:26:31'),(3590,278,'ArmerÃ­a','2016-05-17 02:26:32'),(3591,278,'Colima','2016-05-17 02:26:32'),(3592,278,'Comala','2016-05-17 02:26:32'),(3593,278,'CoquimatlÃ¡n','2016-05-17 02:26:32'),(3594,278,'CuauhtÃ©moc','2016-05-17 02:26:32'),(3595,278,'IxtlahuacÃ¡n','2016-05-17 02:26:32'),(3596,278,'Manzanillo','2016-05-17 02:26:32'),(3597,278,'MinatitlÃ¡n','2016-05-17 02:26:32'),(3598,278,'TecomÃ¡n','2016-05-17 02:26:32'),(3599,278,'Villa de Ãlvarez','2016-05-17 02:26:32'),(3600,290,'CanatlÃ¡n','2016-05-17 02:26:32'),(3601,290,'Canelas','2016-05-17 02:26:32'),(3602,290,'Coneto de Comonfort','2016-05-17 02:26:32'),(3603,290,'CuencamÃ©','2016-05-17 02:26:33'),(3604,290,'Durango','2016-05-17 02:26:33'),(3605,290,'General SimÃ³n BolÃ­var','2016-05-17 02:26:33'),(3606,290,'GÃ³mez Palacio','2016-05-17 02:26:33'),(3607,290,'Guadalupe Victoria','2016-05-17 02:26:33'),(3608,290,'GuanacevÃ­','2016-05-17 02:26:33'),(3609,290,'Hidalgo','2016-05-17 02:26:33'),(3610,290,'IndÃ©','2016-05-17 02:26:33'),(3611,290,'Lerdo','2016-05-17 02:26:33'),(3612,290,'MapimÃ­','2016-05-17 02:26:33'),(3613,290,'Mezquital','2016-05-17 02:26:33'),(3614,290,'Nazas','2016-05-17 02:26:33'),(3615,290,'Nombre de Dios','2016-05-17 02:26:34'),(3616,290,'Ocampo','2016-05-17 02:26:34'),(3617,290,'El Oro','2016-05-17 02:26:34'),(3618,290,'OtÃ¡ez','2016-05-17 02:26:34'),(3619,290,'PÃ¡nuco de Coronado','2016-05-17 02:26:34'),(3620,290,'PeÃ±Ã³n Blanco','2016-05-17 02:26:34'),(3621,290,'Poanas','2016-05-17 02:26:34'),(3622,290,'Pueblo Nuevo','2016-05-17 02:26:34'),(3623,290,'Rodeo','2016-05-17 02:26:34'),(3624,290,'San Bernardo','2016-05-17 02:26:34'),(3625,290,'San Dimas','2016-05-17 02:26:34'),(3626,290,'San Juan de Guadalupe','2016-05-17 02:26:34'),(3627,290,'San Juan del RÃ­o','2016-05-17 02:26:35'),(3628,290,'San Luis del Cordero','2016-05-17 02:26:35'),(3629,290,'San Pedro del Gallo','2016-05-17 02:26:35'),(3630,290,'Santa Clara','2016-05-17 02:26:35'),(3631,290,'Santiago Papasquiaro','2016-05-17 02:26:35'),(3632,290,'SÃºchil','2016-05-17 02:26:35'),(3633,290,'Tamazula','2016-05-17 02:26:35'),(3634,290,'Tepehuanes','2016-05-17 02:26:35'),(3635,290,'Tlahualilo','2016-05-17 02:26:35'),(3636,290,'Topia','2016-05-17 02:26:35'),(3637,290,'Vicente Guerrero','2016-05-17 02:26:35'),(3638,290,'Nuevo Ideal','2016-05-17 02:26:35'),(3639,297,'Abasolo','2016-05-17 02:26:35'),(3640,297,'AcÃ¡mbaro','2016-05-17 02:26:36'),(3641,297,'Allende','2016-05-17 02:26:36'),(3642,297,'Apaseo el Alto','2016-05-17 02:26:36'),(3643,297,'Apaseo el Grande','2016-05-17 02:26:36'),(3644,297,'Atarjea','2016-05-17 02:26:36'),(3645,297,'Celaya','2016-05-17 02:26:36'),(3646,297,'Manuel Doblado','2016-05-17 02:26:36'),(3647,297,'Comonfort','2016-05-17 02:26:36'),(3648,297,'Coroneo','2016-05-17 02:26:36'),(3649,297,'Cortazar','2016-05-17 02:26:36'),(3650,297,'CuerÃ¡maro','2016-05-17 02:26:36'),(3651,297,'Doctor Mora','2016-05-17 02:26:36'),(3652,297,'Dolores Hidalgo','2016-05-17 02:26:37'),(3653,297,'Guanajuato','2016-05-17 02:26:37'),(3654,297,'HuanÃ­maro','2016-05-17 02:26:37'),(3655,297,'Irapuato','2016-05-17 02:26:37'),(3656,297,'Jaral del Progreso','2016-05-17 02:26:37'),(3657,297,'JerÃ©cuaro','2016-05-17 02:26:37'),(3658,297,'LeÃ³n','2016-05-17 02:26:37'),(3659,297,'MoroleÃ³n','2016-05-17 02:26:37'),(3660,297,'Ocampo','2016-05-17 02:26:37'),(3661,297,'PÃ©njamo','2016-05-17 02:26:37'),(3662,297,'Pueblo Nuevo','2016-05-17 02:26:37'),(3663,297,'PurÃ­sima del RincÃ³n','2016-05-17 02:26:37'),(3664,297,'Romita','2016-05-17 02:26:37'),(3665,297,'Salamanca','2016-05-17 02:26:38'),(3666,297,'Salvatierra','2016-05-17 02:26:38'),(3667,297,'San Diego de la UniÃ³n','2016-05-17 02:26:38'),(3668,297,'San Felipe','2016-05-17 02:26:38'),(3669,297,'San Francisco del RincÃ³n','2016-05-17 02:26:38'),(3670,297,'San JosÃ© Iturbide','2016-05-17 02:26:38'),(3671,297,'San Luis de la Paz','2016-05-17 02:26:38'),(3672,297,'Santa Catarina','2016-05-17 02:26:38'),(3673,297,'Santa Cruz de Juventino Rosas','2016-05-17 02:26:38'),(3674,297,'Santiago MaravatÃ­o','2016-05-17 02:26:38'),(3675,297,'Silao','2016-05-17 02:26:38'),(3676,297,'Tarandacuao','2016-05-17 02:26:38'),(3677,297,'Tarimoro','2016-05-17 02:26:39'),(3678,297,'Tierra Blanca','2016-05-17 02:26:39'),(3679,297,'Uriangato','2016-05-17 02:26:39'),(3680,297,'Valle de Santiago','2016-05-17 02:26:39'),(3681,297,'Victoria','2016-05-17 02:26:39'),(3682,297,'VillagrÃ¡n','2016-05-17 02:26:39'),(3683,297,'XichÃº','2016-05-17 02:26:39'),(3684,297,'Yuriria','2016-05-17 02:26:39'),(3685,300,'Acapulco de JuÃ¡rez','2016-05-17 02:26:39'),(3686,300,'Acatepec','2016-05-17 02:26:39'),(3687,300,'AjuchitlÃ¡n del Progreso','2016-05-17 02:26:39'),(3688,300,'Ahuacuotzingo','2016-05-17 02:26:39'),(3689,300,'Alcozauca de Guerrero','2016-05-17 02:26:39'),(3690,300,'Alpoyeca','2016-05-17 02:26:40'),(3691,300,'Apaxtla','2016-05-17 02:26:40'),(3692,300,'Arcelia','2016-05-17 02:26:40'),(3693,300,'Atenango del RÃ­o','2016-05-17 02:26:40'),(3694,300,'Atlamajalcingo del Monte','2016-05-17 02:26:40'),(3695,300,'Atlixtac','2016-05-17 02:26:40'),(3696,300,'Atoyac de Ãlvarez','2016-05-17 02:26:40'),(3697,300,'Ayutla de los Libres','2016-05-17 02:26:40'),(3698,300,'Azoyu','2016-05-17 02:26:40'),(3699,300,'Benito JuÃ¡rez','2016-05-17 02:26:40'),(3700,300,'Buenavista de CuÃ©llar','2016-05-17 02:26:40'),(3701,300,'Chilapa de Ãlvarez','2016-05-17 02:26:40'),(3702,300,'Chilpancingo de los Bravo','2016-05-17 02:26:40'),(3703,300,'Coahuayutla de JosÃ© MarÃ­a Izazaga','2016-05-17 02:26:41'),(3704,300,'Cocula','2016-05-17 02:26:41'),(3705,300,'Copala','2016-05-17 02:26:41'),(3706,300,'Copalillo','2016-05-17 02:26:41'),(3707,300,'Copanatoyac','2016-05-17 02:26:41'),(3708,300,'Coyuca de BenÃ­tez','2016-05-17 02:26:41'),(3709,300,'Coyuca de CatalÃ¡n','2016-05-17 02:26:41'),(3710,300,'Cuajinicuilapa','2016-05-17 02:26:41'),(3711,300,'Cualac','2016-05-17 02:26:41'),(3712,300,'Cuautepec','2016-05-17 02:26:41'),(3713,300,'Cuetzala del Progreso','2016-05-17 02:26:41'),(3714,300,'Cutzamala de PinzÃ³n','2016-05-17 02:26:41'),(3715,300,'Eduardo Neri','2016-05-17 02:26:41'),(3716,300,'Florencio Villarreal','2016-05-17 02:26:42'),(3717,300,'General Canuto A. Neri','2016-05-17 02:26:42'),(3718,300,'General Heliodoro Castillo','2016-05-17 02:26:42'),(3719,300,'HuamuxtitlÃ¡n','2016-05-17 02:26:42'),(3720,300,'Huitzuco de los Figueroa','2016-05-17 02:26:42'),(3721,300,'Iguala de la Independencia','2016-05-17 02:26:42'),(3722,300,'Igualapa','2016-05-17 02:26:42'),(3723,300,'Ixcateopan de CuauhtÃ©moc','2016-05-17 02:26:42'),(3724,300,'Zihuatanejo de Azueta','2016-05-17 02:26:42'),(3725,300,'Juan R. Escudero','2016-05-17 02:26:42'),(3726,300,'La UniÃ³n de Isidoro Montes de Oca','2016-05-17 02:26:42'),(3727,300,'Leonardo Bravo','2016-05-17 02:26:42'),(3728,300,'Malinaltepec','2016-05-17 02:26:43'),(3729,300,'MÃ¡rtir de CuilapÃ¡n','2016-05-17 02:26:43'),(3730,300,'Metlatonoc','2016-05-17 02:26:43'),(3731,300,'MochitlÃ¡n','2016-05-17 02:26:43'),(3732,300,'OlinalÃ¡','2016-05-17 02:26:43'),(3733,300,'Ometepec','2016-05-17 02:26:43'),(3734,300,'Pedro Ascencio Alquisiras','2016-05-17 02:26:43'),(3735,300,'PetatlÃ¡n','2016-05-17 02:26:43'),(3736,300,'Pilcaya','2016-05-17 02:26:43'),(3737,300,'Pungarabato','2016-05-17 02:26:43'),(3738,300,'Quechultenango','2016-05-17 02:26:43'),(3739,300,'San Luis AcatlÃ¡n','2016-05-17 02:26:43'),(3740,300,'San Marcos','2016-05-17 02:26:44'),(3741,300,'San Miguel Totolapan','2016-05-17 02:26:44'),(3742,300,'Taxco de AlarcÃ³n','2016-05-17 02:26:44'),(3743,300,'Tecoanapa','2016-05-17 02:26:44'),(3744,300,'TecpÃ¡n de Galeana','2016-05-17 02:26:44'),(3745,300,'Teloloapan','2016-05-17 02:26:44'),(3746,300,'Tepecoacuilco de Trujano','2016-05-17 02:26:44'),(3747,300,'Tetipac','2016-05-17 02:26:44'),(3748,300,'Tixtla de Guerrero','2016-05-17 02:26:44'),(3749,300,'Tlacoachistlahuaca','2016-05-17 02:26:44'),(3750,300,'Tlacoapa','2016-05-17 02:26:44'),(3751,300,'Tlalchapa','2016-05-17 02:26:44'),(3752,300,'Tlalixtaquilla de Maldonado','2016-05-17 02:26:44'),(3753,300,'Tlapa de Comonfort','2016-05-17 02:26:45'),(3754,300,'Tlapehuala','2016-05-17 02:26:45'),(3755,300,'Xalpatlahuac','2016-05-17 02:26:45'),(3756,300,'Xochihuehuetlan','2016-05-17 02:26:45'),(3757,300,'Xochistlahuaca','2016-05-17 02:26:45'),(3758,300,'ZapotitlÃ¡n Tablas','2016-05-17 02:26:45'),(3759,300,'ZirÃ¡ndaro','2016-05-17 02:26:45'),(3760,300,'Zitlala','2016-05-17 02:26:45'),(3761,300,'Marquelia','2016-05-17 02:26:45'),(3762,300,'Cochoapa el Grande','2016-05-17 02:26:45'),(3763,300,'JosÃ© JoaquÃ­n de Herrera','2016-05-17 02:26:45'),(3764,300,'JuchitÃ¡n','2016-05-17 02:26:45'),(3765,300,'Iliatenco','2016-05-17 02:26:45'),(3766,215,'AcatlÃ¡n','2016-05-17 02:26:46'),(3767,215,'AcaxochitlÃ¡n','2016-05-17 02:26:46'),(3768,215,'Actopan','2016-05-17 02:26:46'),(3769,215,'Agua Blanca de Iturbide','2016-05-17 02:26:46'),(3770,215,'Ajacuba','2016-05-17 02:26:46'),(3771,215,'Alfajayucan','2016-05-17 02:26:46'),(3772,215,'Almoloya','2016-05-17 02:26:46'),(3773,215,'Apan','2016-05-17 02:26:46'),(3774,215,'El Arenal','2016-05-17 02:26:46'),(3775,215,'AtitalaquÃ­a','2016-05-17 02:26:46'),(3776,215,'Atlapexco','2016-05-17 02:26:46'),(3777,215,'Atotonilco de Tula','2016-05-17 02:26:46'),(3778,215,'Atotonilco El Grande','2016-05-17 02:26:46'),(3779,215,'Calnali','2016-05-17 02:26:47'),(3780,215,'Cardonal','2016-05-17 02:26:47'),(3781,215,'Chapantongo','2016-05-17 02:26:47'),(3782,215,'ChapulhuacÃ¡n','2016-05-17 02:26:47'),(3783,215,'Chilcuautla','2016-05-17 02:26:47'),(3784,215,'Cuautepec de Hinojosa','2016-05-17 02:26:47'),(3785,215,'EloxochitlÃ¡n','2016-05-17 02:26:47'),(3786,215,'Emiliano Zapata','2016-05-17 02:26:47'),(3787,215,'Epazoyucan','2016-05-17 02:26:47'),(3788,215,'Francisco I. Madero','2016-05-17 02:26:47'),(3789,215,'Huasca de Ocampo','2016-05-17 02:26:47'),(3790,215,'Huautla','2016-05-17 02:26:47'),(3791,215,'Huazalingo','2016-05-17 02:26:48'),(3792,215,'Huehuetla','2016-05-17 02:26:48'),(3793,215,'Huejutla de Reyes','2016-05-17 02:26:48'),(3794,215,'Huichapan','2016-05-17 02:26:48'),(3795,215,'Ixmiquilpan','2016-05-17 02:26:48'),(3796,215,'Jacala de Ledezma','2016-05-17 02:26:48'),(3797,215,'Jaltocan','2016-05-17 02:26:48'),(3798,215,'JuÃ¡rez','2016-05-17 02:26:48'),(3799,215,'Lolotla','2016-05-17 02:26:48'),(3800,215,'Metepec','2016-05-17 02:26:48'),(3801,215,'MetztitlÃ¡n','2016-05-17 02:26:48'),(3802,215,'Mineral de la Reforma','2016-05-17 02:26:48'),(3803,215,'Mineral del Chico','2016-05-17 02:26:48'),(3804,215,'Mineral del Monte','2016-05-17 02:26:49'),(3805,215,'La MisiÃ³n','2016-05-17 02:26:49'),(3806,215,'Mixquiahuala de JuÃ¡rez','2016-05-17 02:26:49'),(3807,215,'Molango de Escamilla','2016-05-17 02:26:49'),(3808,215,'NicolÃ¡s Flores','2016-05-17 02:26:49'),(3809,215,'Nopala de VillagrÃ¡n','2016-05-17 02:26:49'),(3810,215,'OmitlÃ¡n de JuÃ¡rez','2016-05-17 02:26:49'),(3811,215,'Pisaflores','2016-05-17 02:26:49'),(3812,215,'Pacula','2016-05-17 02:26:49'),(3813,215,'Pachuca de Soto','2016-05-17 02:26:49'),(3814,215,'Progreso de ObregÃ³n','2016-05-17 02:26:49'),(3815,215,'San AgustÃ­n MetzquititlÃ¡n','2016-05-17 02:26:49'),(3816,215,'San AgustÃ­n Tlaxiaca','2016-05-17 02:26:49'),(3817,215,'San Bartolo Tutotepec','2016-05-17 02:26:50'),(3818,215,'San Felipe OrizatlÃ¡n','2016-05-17 02:26:50'),(3819,215,'San Salvador','2016-05-17 02:26:50'),(3820,215,'Santiago de Anaya','2016-05-17 02:26:50'),(3821,215,'Singuilucan','2016-05-17 02:26:50'),(3822,215,'Tasquillo','2016-05-17 02:26:50'),(3823,215,'Tecozautla','2016-05-17 02:26:50'),(3824,215,'Tenango de Doria','2016-05-17 02:26:50'),(3825,215,'Tepeapulco','2016-05-17 02:26:50'),(3826,215,'TepehuacÃ¡n de Guerrero','2016-05-17 02:26:50'),(3827,215,'Tepeji del Rio de Ocampo','2016-05-17 02:26:50'),(3828,215,'TepetitlÃ¡n','2016-05-17 02:26:50'),(3829,215,'Tetepango','2016-05-17 02:26:51'),(3830,215,'Tezontepec de Aldama','2016-05-17 02:26:51'),(3831,215,'Tianguistengo','2016-05-17 02:26:51'),(3832,215,'Tizayuca','2016-05-17 02:26:51'),(3833,215,'Tlahuelilpan','2016-05-17 02:26:51'),(3834,215,'Tlahuiltepa','2016-05-17 02:26:51'),(3835,215,'Tlanalapa','2016-05-17 02:26:51'),(3836,215,'Tlanchinol','2016-05-17 02:26:51'),(3837,215,'Tlaxcoapan','2016-05-17 02:26:51'),(3838,215,'Tolcayuca','2016-05-17 02:26:51'),(3839,215,'Tula de Allende','2016-05-17 02:26:51'),(3840,215,'Tulancingo de Bravo','2016-05-17 02:26:51'),(3841,215,'Tulantepec de Lugo Guerrero','2016-05-17 02:26:51'),(3842,215,'Villa de Tezontepec','2016-05-17 02:26:52'),(3843,215,'Xochiatipan','2016-05-17 02:26:52'),(3844,215,'XochicoatlÃ¡n','2016-05-17 02:26:52'),(3845,215,'Yahualica','2016-05-17 02:26:52'),(3846,215,'Zacualtipan de Ãngeles','2016-05-17 02:26:52'),(3847,215,'ZapotlÃ¡n de JuÃ¡rez','2016-05-17 02:26:52'),(3848,215,'Zempoala','2016-05-17 02:26:52'),(3849,215,'Zimapan','2016-05-17 02:26:52'),(3850,306,'Acatic','2016-05-17 02:26:52'),(3851,306,'AcatlÃ¡n de JuÃ¡rez','2016-05-17 02:26:52'),(3852,306,'Ahualulco de Mercado','2016-05-17 02:26:52'),(3853,306,'Amacueca','2016-05-17 02:26:52'),(3854,306,'AmatitÃ¡n','2016-05-17 02:26:52'),(3855,306,'Ameca','2016-05-17 02:26:53'),(3856,306,'Arandas','2016-05-17 02:26:53'),(3857,306,'Atemajac de Brizuela','2016-05-17 02:26:53'),(3858,306,'Atengo','2016-05-17 02:26:53'),(3859,306,'Atenguillo','2016-05-17 02:26:53'),(3860,306,'Atotonilco El Alto','2016-05-17 02:26:53'),(3861,306,'Atoyac','2016-05-17 02:26:53'),(3862,306,'AutlÃ¡n de Navarro','2016-05-17 02:26:53'),(3863,306,'AyotlÃ¡n','2016-05-17 02:26:53'),(3864,306,'Ayutla','2016-05-17 02:26:53'),(3865,306,'BolaÃ±os','2016-05-17 02:26:53'),(3866,306,'Cabo Corrientes','2016-05-17 02:26:53'),(3867,306,'CaÃ±adas de ObregÃ³n','2016-05-17 02:26:53'),(3868,306,'Casimiro Castillo','2016-05-17 02:26:54'),(3869,306,'Chapala','2016-05-17 02:26:54'),(3870,306,'ChimaltitÃ¡n','2016-05-17 02:26:54'),(3871,306,'ChiquilistlÃ¡n','2016-05-17 02:26:54'),(3872,306,'CihuatlÃ¡n','2016-05-17 02:26:54'),(3873,306,'Cocula','2016-05-17 02:26:54'),(3874,306,'ColotlÃ¡n','2016-05-17 02:26:54'),(3875,306,'ConcepciÃ³n de Buenos Aires','2016-05-17 02:26:54'),(3876,306,'CuautitlÃ¡n de GarcÃ­a BarragÃ¡n','2016-05-17 02:26:54'),(3877,306,'Cuautla','2016-05-17 02:26:54'),(3878,306,'CuquÃ­o','2016-05-17 02:26:54'),(3879,306,'Degollado','2016-05-17 02:26:54'),(3880,306,'Ejutla','2016-05-17 02:26:55'),(3881,306,'El Arenal','2016-05-17 02:26:55'),(3882,306,'El Grullo','2016-05-17 02:26:55'),(3883,306,'El LimÃ³n','2016-05-17 02:26:55'),(3884,306,'El Salto','2016-05-17 02:26:55'),(3885,306,'EncarnaciÃ³n de DÃ­az','2016-05-17 02:26:55'),(3886,306,'EtzatlÃ¡n','2016-05-17 02:26:55'),(3887,306,'GÃ³mez FarÃ­as','2016-05-17 02:26:55'),(3888,306,'Guachinango','2016-05-17 02:26:55'),(3889,306,'Guadalajara','2016-05-17 02:26:55'),(3890,306,'Hostotipaquillo','2016-05-17 02:26:55'),(3891,306,'HuejÃºcar','2016-05-17 02:26:55'),(3892,306,'Huejuquilla El Alto','2016-05-17 02:26:55'),(3893,306,'IxtlahuacÃ¡n de los Membrillos','2016-05-17 02:26:56'),(3894,306,'Ixtlahuacan del RÃ­o','2016-05-17 02:26:56'),(3895,306,'JalostotitlÃ¡n','2016-05-17 02:26:56'),(3896,306,'Jamay','2016-05-17 02:26:56'),(3897,306,'JesÃºs MarÃ­a','2016-05-17 02:26:56'),(3898,306,'JilotlÃ¡n de los Dolores','2016-05-17 02:26:56'),(3899,306,'Jocotepec','2016-05-17 02:26:56'),(3900,306,'JuanacatlÃ¡n','2016-05-17 02:26:56'),(3901,306,'JuchitlÃ¡n','2016-05-17 02:26:56'),(3902,306,'La Barca','2016-05-17 02:26:56'),(3903,306,'La Huerta','2016-05-17 02:26:56'),(3904,306,'La Manzanilla de La Paz','2016-05-17 02:26:56'),(3905,306,'Lagos de Moreno','2016-05-17 02:26:57'),(3906,306,'Magdalena','2016-05-17 02:26:57'),(3907,306,'Mascota','2016-05-17 02:26:57'),(3908,306,'Mazamitla','2016-05-17 02:26:57'),(3909,306,'Mexticacan','2016-05-17 02:26:57'),(3910,306,'Mezquitic','2016-05-17 02:26:57'),(3911,306,'MixtlÃ¡n','2016-05-17 02:26:57'),(3912,306,'OcotlÃ¡n','2016-05-17 02:26:57'),(3913,306,'Ojuelos de Jalisco','2016-05-17 02:26:57'),(3914,306,'PÃ­huamo','2016-05-17 02:26:57'),(3915,306,'PoncitlÃ¡n','2016-05-17 02:26:57'),(3916,306,'Puerto Vallarta','2016-05-17 02:26:57'),(3917,306,'Quitupan','2016-05-17 02:26:57'),(3918,306,'San Cristobal de la Barranca','2016-05-17 02:26:58'),(3919,306,'San Diego de AlejandrÃ­a','2016-05-17 02:26:58'),(3920,306,'San Gabriel','2016-05-17 02:26:58'),(3921,306,'San Juan de los Lagos','2016-05-17 02:26:58'),(3922,306,'San Juanito de Escobedo','2016-05-17 02:26:58'),(3923,306,'San JuliÃ¡n','2016-05-17 02:26:58'),(3924,306,'San Marcos','2016-05-17 02:26:58'),(3925,306,'San MartÃ­n de BolaÃ±os','2016-05-17 02:26:58'),(3926,306,'San MartÃ­n de Hidalgo','2016-05-17 02:26:58'),(3927,306,'San Miguel El Alto','2016-05-17 02:26:58'),(3928,306,'San SebastiÃ¡n del Oeste','2016-05-17 02:26:58'),(3929,306,'Santa MarÃ­a del Oro','2016-05-17 02:26:58'),(3930,306,'Santa MarÃ­a de los Angeles','2016-05-17 02:26:59'),(3931,306,'Sayula','2016-05-17 02:26:59'),(3932,306,'Tala','2016-05-17 02:26:59'),(3933,306,'Talpa de Allende','2016-05-17 02:26:59'),(3934,306,'Tamazula de Gordiano','2016-05-17 02:26:59'),(3935,306,'Tapalpa','2016-05-17 02:26:59'),(3936,306,'TecalitlÃ¡n','2016-05-17 02:26:59'),(3937,306,'Techaluta de Montenegro','2016-05-17 02:26:59'),(3938,306,'TecolotlÃ¡n','2016-05-17 02:26:59'),(3939,306,'TenamaxtlÃ¡n','2016-05-17 02:26:59'),(3940,306,'Teocaltiche','2016-05-17 02:26:59'),(3941,306,'TeocuitatlÃ¡n de Corona','2016-05-17 02:26:59'),(3942,306,'TepatitlÃ¡n de Morelos','2016-05-17 02:27:00'),(3943,306,'Tequila','2016-05-17 02:27:00'),(3944,306,'TeuchitlÃ¡n','2016-05-17 02:27:00'),(3945,306,'Tizapan El Alto','2016-05-17 02:27:00'),(3946,306,'Tlajomulco de ZuÃ±iga','2016-05-17 02:27:00'),(3947,306,'Tlaquepaque','2016-05-17 02:27:00'),(3948,306,'TolimÃ¡n','2016-05-17 02:27:00'),(3949,306,'TomatlÃ¡n','2016-05-17 02:27:00'),(3950,306,'TonalÃ¡','2016-05-17 02:27:00'),(3951,306,'Tonaya','2016-05-17 02:27:00'),(3952,306,'Tonila','2016-05-17 02:27:00'),(3953,306,'Totatiche','2016-05-17 02:27:00'),(3954,306,'TototlÃ¡n','2016-05-17 02:27:00'),(3955,306,'Tuxcacuesco','2016-05-17 02:27:01'),(3956,306,'Tuxcueca','2016-05-17 02:27:01'),(3957,306,'Tuxpan','2016-05-17 02:27:01'),(3958,306,'UniÃ³n de San Antonio','2016-05-17 02:27:01'),(3959,306,'UniÃ³n de Tula','2016-05-17 02:27:01'),(3960,306,'Valle de Guadalupe','2016-05-17 02:27:01'),(3961,306,'Valle de JuÃ¡rez','2016-05-17 02:27:01'),(3962,306,'Villa Corona','2016-05-17 02:27:01'),(3963,306,'Villa Guerrero','2016-05-17 02:27:01'),(3964,306,'Villa Hidalgo','2016-05-17 02:27:01'),(3965,306,'Villa PurificaciÃ³n','2016-05-17 02:27:01'),(3966,306,'Yahualica de GonzÃ¡lez Gallo','2016-05-17 02:27:01'),(3967,306,'Zacoalco de Torres','2016-05-17 02:27:02'),(3968,306,'Zapopan','2016-05-17 02:27:02'),(3969,306,'Zapotiltic','2016-05-17 02:27:02'),(3970,306,'ZapotitlÃ¡n de Vadillo','2016-05-17 02:27:02'),(3971,306,'ZapotlÃ¡n del Rey','2016-05-17 02:27:02'),(3972,306,'ZapotlÃ¡n el Grande','2016-05-17 02:27:02'),(3973,306,'Zapotlanejo','2016-05-17 02:27:02'),(3974,306,'San Ignacio Cerro Gordo','2016-05-17 02:27:02'),(3975,335,'Amacuzac','2016-05-17 02:40:49'),(3976,335,'Atlatlahucan','2016-05-17 02:40:49'),(3977,335,'Axochiapan','2016-05-17 02:40:49'),(3978,335,'Ciudad Ayala','2016-05-17 02:40:49'),(3979,335,'CoatlÃ¡n del RÃ­o','2016-05-17 02:40:49'),(3980,335,'Cuautla','2016-05-17 02:40:49'),(3981,335,'Cuernavaca','2016-05-17 02:40:49'),(3982,335,'Emiliano Zapata','2016-05-17 02:40:49'),(3983,335,'Huitzilac','2016-05-17 02:40:49'),(3984,335,'Jantetelco','2016-05-17 02:40:49'),(3985,335,'Jiutepec','2016-05-17 02:40:50'),(3986,335,'Jojutla','2016-05-17 02:40:50'),(3987,335,'Jonacatepec','2016-05-17 02:40:50'),(3988,335,'Mazatepec','2016-05-17 02:40:50'),(3989,335,'Miacatlan','2016-05-17 02:40:50'),(3990,335,'Ocuituco','2016-05-17 02:40:50'),(3991,335,'Puente de Ixtla','2016-05-17 02:40:50'),(3992,335,'Temixco','2016-05-17 02:40:50'),(3993,335,'Temoac','2016-05-17 02:40:50'),(3994,335,'Tepalcingo','2016-05-17 02:40:50'),(3995,335,'TepoztlÃ¡n','2016-05-17 02:40:50'),(3996,335,'Tetecala','2016-05-17 02:40:50'),(3997,335,'Tetela del VolcÃ¡n','2016-05-17 02:40:50'),(3998,335,'Tlalnepantla','2016-05-17 02:40:51'),(3999,335,'TlaltizapÃ¡n','2016-05-17 02:40:51'),(4000,335,'Tlaquiltenango','2016-05-17 02:40:51'),(4001,335,'Tlayacapan','2016-05-17 02:40:51'),(4002,335,'Totolapan','2016-05-17 02:40:51'),(4003,335,'Xochitepec','2016-05-17 02:40:51'),(4004,335,'Yautepec','2016-05-17 02:40:51'),(4005,335,'Yecapixtla','2016-05-17 02:40:51'),(4006,335,'Zacatepec de Hidalgo','2016-05-17 02:40:51'),(4007,335,'Zacualpan de Amilpas','2016-05-17 02:40:51'),(4008,337,'Acaponeta','2016-05-17 02:40:51'),(4009,337,'AhuacatlÃ¡n','2016-05-17 02:40:51'),(4010,337,'AmatlÃ¡n de CaÃ±as','2016-05-17 02:40:52'),(4011,337,'BahÃ­a de Banderas','2016-05-17 02:40:52'),(4012,337,'Compostela','2016-05-17 02:40:52'),(4013,337,'El Nayar','2016-05-17 02:40:52'),(4014,337,'Huajicori','2016-05-17 02:40:52'),(4015,337,'IxtlÃ¡n del RÃ­o','2016-05-17 02:40:52'),(4016,337,'Jala','2016-05-17 02:40:52'),(4017,337,'La Yesca','2016-05-17 02:40:52'),(4018,337,'Rosamorada','2016-05-17 02:40:52'),(4019,337,'Ruiz','2016-05-17 02:40:52'),(4020,337,'San Blas','2016-05-17 02:40:52'),(4021,337,'San Pedro Lagunillas','2016-05-17 02:40:52'),(4022,337,'Santa MarÃ­a del Oro','2016-05-17 02:40:52'),(4023,337,'Santiago Ixcuintla','2016-05-17 02:40:53'),(4024,337,'Tecuala','2016-05-17 02:40:53'),(4025,337,'Tepic','2016-05-17 02:40:53'),(4026,337,'Tuxpan','2016-05-17 02:40:53'),(4027,337,'Xalisco','2016-05-17 02:40:53'),(4028,340,'Abejones','2016-05-17 02:40:53'),(4029,340,'AcatlÃ¡n de PÃ©rez Figueroa','2016-05-17 02:40:53'),(4030,340,'Animas Trujano, Oaxaca','2016-05-17 02:40:53'),(4031,340,'AsunciÃ³n Cacalotepec','2016-05-17 02:40:53'),(4032,340,'AsunciÃ³n Cuyotepeji','2016-05-17 02:40:53'),(4033,340,'AsunciÃ³n Ixtaltepec','2016-05-17 02:40:53'),(4034,340,'AsunciÃ³n NochixtlÃ¡n','2016-05-17 02:40:53'),(4035,340,'AsunciÃ³n OcotlÃ¡n','2016-05-17 02:40:54'),(4036,340,'AsunciÃ³n Tlacolulita','2016-05-17 02:40:54'),(4037,340,'Ayoquezco de Aldama','2016-05-17 02:40:54'),
   (4038,340,'Ayotzintepec','2016-05-17 02:40:54'),(4039,340,'CalihualÃ¡','2016-05-17 02:40:54'),(4040,340,'Candelaria Loxicha','2016-05-17 02:40:54'),(4041,340,'Capulalpam de MÃ©ndez','2016-05-17 02:40:54'),(4042,340,'Chahuites','2016-05-17 02:40:54'),(4043,340,'Chalcatongo de Hidalgo','2016-05-17 02:40:54'),(4044,340,'Chilapa de Diaz','2016-05-17 02:40:54'),(4045,340,'ChiquihuitlÃ¡n de Benito JuÃ¡rez','2016-05-17 02:40:54'),(4046,340,'CiÃ©nega de ZimatlÃ¡n','2016-05-17 02:40:54'),(4047,340,'Ciudad Ixtepec','2016-05-17 02:40:54'),(4048,340,'Coatecas Altas','2016-05-17 02:40:55'),(4049,340,'CoicoyÃ¡n de las Flores','2016-05-17 02:40:55'),(4050,340,'ConcepciÃ³n Buenavista','2016-05-17 02:40:55'),(4051,340,'ConcepciÃ³n PÃ¡palo','2016-05-17 02:40:55'),(4052,340,'Constancia del Rosario','2016-05-17 02:40:55'),(4053,340,'Cosolapa','2016-05-17 02:40:55'),(4054,340,'Cosoltepec','2016-05-17 02:40:55'),(4055,340,'Cuilapan de Guerrero','2016-05-17 02:40:55'),(4056,340,'Ejutla de Crespo','2016-05-17 02:40:55'),(4057,340,'EloxochitlÃ¡n de Flores MagÃ³n','2016-05-17 02:40:55'),(4058,340,'El Barrio de La Soledad','2016-05-17 02:40:55'),(4059,340,'El Espinal','2016-05-17 02:40:55'),(4060,340,'Evangelista Analco','2016-05-17 02:40:56'),(4061,340,'Fresnillo de Trujano','2016-05-17 02:40:56'),(4062,340,'Guadalupe de RamÃ­rez','2016-05-17 02:40:56'),(4063,340,'Guadalupe Etla','2016-05-17 02:40:56'),(4064,340,'Guelatao de JuÃ¡rez','2016-05-17 02:40:56'),(4065,340,'Guevea de Humboldt','2016-05-17 02:40:56'),(4066,340,'Huajuapan de LeÃ³n','2016-05-17 02:40:56'),(4067,340,'Huautepec','2016-05-17 02:40:56'),(4068,340,'Huautla de JimÃ©nez','2016-05-17 02:40:56'),(4069,340,'Ixpantepec Nieves','2016-05-17 02:40:56'),(4070,340,'IxtlÃ¡n de JuÃ¡rez','2016-05-17 02:40:56'),(4071,340,'JuchitÃ¡n de Zaragoza','2016-05-17 02:40:56'),(4072,340,'La CompaÃ±ia','2016-05-17 02:40:56'),(4073,340,'La Pe','2016-05-17 02:40:57'),(4074,340,'La Reforma','2016-05-17 02:40:57'),(4075,340,'La Trinidad Vista Hermosa','2016-05-17 02:40:57'),(4076,340,'Loma Bonita','2016-05-17 02:40:57'),(4077,340,'Magdalena Apasco','2016-05-17 02:40:57'),(4078,340,'Magdalena Jaltepec','2016-05-17 02:40:57'),(4079,340,'Magdalena Mixtepec','2016-05-17 02:40:57'),(4080,340,'Magdalena OcotlÃ¡n','2016-05-17 02:40:57'),(4081,340,'Magdalena PeÃ±asco','2016-05-17 02:40:57'),(4082,340,'Magdalena Teitipac','2016-05-17 02:40:57'),(4083,340,'Magdalena TequisistlÃ¡n','2016-05-17 02:40:57'),(4084,340,'Magdalena Tlacotepec','2016-05-17 02:40:57'),(4085,340,'Magdalena ZahuatlÃ¡n','2016-05-17 02:40:58'),(4086,340,'Mariscala de JuÃ¡rez','2016-05-17 02:40:58'),(4087,340,'MÃ¡rtires de Tacubaya','2016-05-17 02:40:58'),(4088,340,'MatÃ­as Romero','2016-05-17 02:40:58'),(4089,340,'MazatlÃ¡n Villa de Flores','2016-05-17 02:40:58'),(4090,340,'Mesones Hidalgo','2016-05-17 02:40:58'),(4091,340,'MiahuatlÃ¡n de Porfirio DÃ­az','2016-05-17 02:40:58'),(4092,340,'MixistlÃ¡n de la Reforma','2016-05-17 02:40:58'),(4093,340,'Monjas','2016-05-17 02:40:58'),(4094,340,'Natividad','2016-05-17 02:40:58'),(4095,340,'Nazareno Etla','2016-05-17 02:40:58'),(4096,340,'Nejapa de Madero','2016-05-17 02:40:58'),(4097,340,'Nuevo Zoquiapam','2016-05-17 02:40:58'),(4098,340,'Oaxaca de JuÃ¡rez','2016-05-17 02:40:59'),(4099,340,'OcotlÃ¡n de Morelos','2016-05-17 02:40:59'),(4100,340,'Pinotepa de Don Luis','2016-05-17 02:40:59'),(4101,340,'Pinotepa Nacional','2016-05-17 02:40:59'),(4102,340,'Pluma Hidalgo','2016-05-17 02:40:59'),(4103,340,'Putla Villa de Guerrero','2016-05-17 02:40:59'),(4104,340,'Reforma de Pineda','2016-05-17 02:40:59'),(4105,340,'Reyes Etla','2016-05-17 02:40:59'),(4106,340,'Rojas de CuauhtÃ©moc','2016-05-17 02:40:59'),(4107,340,'Salina Cruz','2016-05-17 02:40:59'),(4108,340,'San AgustÃ­n Amatengo','2016-05-17 02:40:59'),(4109,340,'San AgustÃ­n Atenango','2016-05-17 02:40:59'),(4110,340,'San AgustÃ­n Chayuco','2016-05-17 02:41:00'),(4111,340,'San AgustÃ­n de las Juntas','2016-05-17 02:41:00'),(4112,340,'San AgustÃ­n Etla','2016-05-17 02:41:00'),(4113,340,'San AgustÃ­n Loxicha','2016-05-17 02:41:00'),(4114,340,'San AgustÃ­n Tlacotepec','2016-05-17 02:41:00'),(4115,340,'San AgustÃ­n Yatareni','2016-05-17 02:41:00'),(4116,340,'San AndrÃ©s Cabecera Nueva','2016-05-17 02:41:00'),(4117,340,'San AndrÃ©s Dinicuiti','2016-05-17 02:41:00'),(4118,340,'San AndrÃ©s Huaxpaltepec','2016-05-17 02:41:00'),(4119,340,'San AndrÃ©s Huayapam','2016-05-17 02:41:00'),(4120,340,'San AndrÃ©s Ixtlahuaca','2016-05-17 02:41:00'),(4121,340,'San AndrÃ©s Lagunas','2016-05-17 02:41:00'),(4122,340,'San AndrÃ©s NuxiÃ±o','2016-05-17 02:41:01'),(4123,340,'San AndrÃ©s PaxtlÃ¡n','2016-05-17 02:41:01'),(4124,340,'San AndrÃ©s Sinaxtla','2016-05-17 02:41:01'),(4125,340,'San AndrÃ©s Solaga','2016-05-17 02:41:01'),(4126,340,'San AndrÃ©s Teotilalpam','2016-05-17 02:41:01'),(4127,340,'San AndrÃ©s Tepetlapa','2016-05-17 02:41:01'),(4128,340,'San AndrÃ©s YaÃ¡','2016-05-17 02:41:01'),(4129,340,'San AndrÃ©s Zabache','2016-05-17 02:41:01'),(4130,340,'San AndrÃ©s Zautla','2016-05-17 02:41:01'),(4131,340,'San Antonino Castillo Velasco','2016-05-17 02:41:01'),(4132,340,'San Antonino El Alto','2016-05-17 02:41:01'),(4133,340,'San Antonino Monte Verde','2016-05-17 02:41:01'),(4134,340,'San Antonio Acutla','2016-05-17 02:41:01'),(4135,340,'San Antonio de la Cal','2016-05-17 02:41:02'),(4136,340,'San Antonio Huitepec','2016-05-17 02:41:02'),(4137,340,'San Antonio Nanahuatipam','2016-05-17 02:41:02'),(4138,340,'San Antonio Sinicahua','2016-05-17 02:41:02'),(4139,340,'San Antonio Tepetlapa','2016-05-17 02:41:02'),(4140,340,'San Baltazar Chichicapam','2016-05-17 02:41:02'),(4141,340,'San Baltazar Loxicha','2016-05-17 02:41:02'),(4142,340,'San Baltazar Yatzachi el Bajo','2016-05-17 02:41:02'),(4143,340,'San Bartolo Coyotepec','2016-05-17 02:41:02'),(4144,340,'San BartolomÃ© Ayautla','2016-05-17 02:41:02'),(4145,340,'San BartolomÃ© Loxicha','2016-05-17 02:41:02'),(4146,340,'San BartolomÃ© Quialana','2016-05-17 02:41:02'),(4147,340,'San BartolomÃ© YucuaÃ±e','2016-05-17 02:41:03'),(4148,340,'San BartolomÃ© Zoogocho','2016-05-17 02:41:03'),(4149,340,'San Bartolo Soyaltepec','2016-05-17 02:41:03'),(4150,340,'San Bartolo Yautepec','2016-05-17 02:41:03'),(4151,340,'San Bernardo Mixtepec','2016-05-17 02:41:03'),(4152,340,'San Blas Atempa','2016-05-17 02:41:03'),(4153,340,'San Carlos Yautepec','2016-05-17 02:41:03'),(4154,340,'San CristÃ³bal AmatlÃ¡n','2016-05-17 02:41:03'),(4155,340,'San CristÃ³bal Amoltepec','2016-05-17 02:41:03'),(4156,340,'San CristÃ³bal Lachirioag','2016-05-17 02:41:03'),(4157,340,'San CristÃ³bal Suchixtlahuaca','2016-05-17 02:41:03'),(4158,340,'San Dionisio del Mar','2016-05-17 02:41:03'),(4159,340,'San Dionisio Ocotepec','2016-05-17 02:41:03'),(4160,340,'San Dionisio OcotlÃ¡n','2016-05-17 02:41:04'),(4161,340,'San Esteban Atatlahuca','2016-05-17 02:41:04'),(4162,340,'San Felipe Jalapa de DÃ­az','2016-05-17 02:41:04'),(4163,340,'San Felipe Tejalapam','2016-05-17 02:41:04'),(4164,340,'San Felipe Usila','2016-05-17 02:41:04'),(4165,340,'San Francisco CahuacÃºa','2016-05-17 02:41:04'),(4166,340,'San Francisco Cajonos','2016-05-17 02:41:04'),(4167,340,'San Francisco Chapulapa','2016-05-17 02:41:04'),(4168,340,'San Francisco ChindÃºa','2016-05-17 02:41:04'),(4169,340,'San Francisco del Mar','2016-05-17 02:41:04'),(4170,340,'San Francisco HuehuetlÃ¡n','2016-05-17 02:41:04'),(4171,340,'San Francisco IxhuatÃ¡n','2016-05-17 02:41:04'),(4172,340,'San Francisco Jaltepetongo','2016-05-17 02:41:05'),(4173,340,'San Francisco LachigolÃ³','2016-05-17 02:41:05'),(4174,340,'San Francisco Logueche','2016-05-17 02:41:05'),(4175,340,'San Francisco NuxaÃ±o','2016-05-17 02:41:05'),(4176,340,'San Francisco Ozolotepec','2016-05-17 02:41:05'),(4177,340,'San Francisco SolÃ¡','2016-05-17 02:41:05'),(4178,340,'San Francisco Telixtlahuaca','2016-05-17 02:41:05'),(4179,340,'San Francisco Teopan','2016-05-17 02:41:05'),(4180,340,'San Francisco Tlapancingo','2016-05-17 02:41:05'),(4181,340,'San Gabriel Mixtepec','2016-05-17 02:41:05'),(4182,340,'San Ildefonso AmatlÃ¡n','2016-05-17 02:41:05'),(4183,340,'San Ildefonso SolÃ¡','2016-05-17 02:41:05'),(4184,340,'San Ildefonso Villa Alta','2016-05-17 02:41:05'),(4185,340,'San Jacinto Amilpas','2016-05-17 02:41:06'),(4186,340,'San Jacinto Tlacotepec','2016-05-17 02:41:06'),(4187,340,'San JerÃ³nimo CoatlÃ¡n','2016-05-17 02:41:06'),(4188,340,'San JerÃ³nimo Silacayoapilla','2016-05-17 02:41:06'),(4189,340,'San JerÃ³nimo Sosola','2016-05-17 02:41:06'),(4190,340,'San JerÃ³nimo Taviche','2016-05-17 02:41:06'),(4191,340,'San JerÃ³nimo Tecoatl','2016-05-17 02:41:06'),(4192,340,'San JerÃ³nimo Tlacochahuaya','2016-05-17 02:41:06'),(4193,340,'San Jorge Nuchita','2016-05-17 02:41:06'),(4194,340,'San JosÃ© Ayuquila','2016-05-17 02:41:06'),(4195,340,'San JosÃ© Chinantequilla (Oaxaca)','2016-05-17 02:41:06'),(4196,340,'San JosÃ© Chiltepec','2016-05-17 02:41:06'),(4197,340,'San JosÃ© del PeÃ±asco','2016-05-17 02:41:07'),(4198,340,'San JosÃ© del Progreso','2016-05-17 02:41:07'),(4199,340,'San JosÃ© Estancia Grande','2016-05-17 02:41:07'),(4200,340,'San JosÃ© Independencia','2016-05-17 02:41:07'),(4201,340,'San JosÃ© Lachiguiri','2016-05-17 02:41:07'),(4202,340,'San JosÃ© Tenango','2016-05-17 02:41:07'),(4203,340,'San Juan Achiutla','2016-05-17 02:41:07'),(4204,340,'San Juan Atepec','2016-05-17 02:41:07'),(4205,340,'San Juan Bautista Atatlahuca','2016-05-17 02:41:07'),(4206,340,'San Juan Bautista Coixtlahuaca','2016-05-17 02:41:07'),(4207,340,'San Juan Bautista Cuicatlan','2016-05-17 02:41:07'),(4208,340,'San Juan Bautista Guelache','2016-05-17 02:41:07'),(4209,340,'San Juan Bautista JayacatlÃ¡n','2016-05-17 02:41:07'),(4210,340,'San Juan Bautista lo de Soto','2016-05-17 02:41:08'),(4211,340,'San Juan Bautista Suchitepec','2016-05-17 02:41:08'),(4212,340,'San Juan Bautista Tlachichilco','2016-05-17 02:41:08'),(4213,340,'San Juan Bautista Tlacoatzintepec','2016-05-17 02:41:08'),(4214,340,'San Juan Bautista Tuxtepec','2016-05-17 02:41:08'),(4215,340,'San Juan Bautista Valle Nacional','2016-05-17 02:41:08'),(4216,340,'San Juan Cacahuatepec','2016-05-17 02:41:08'),(4217,340,'San Juan ChicomezÃºchil','2016-05-17 02:41:08'),(4218,340,'San Juan Chilateca','2016-05-17 02:41:08'),(4219,340,'San Juan Cieneguilla','2016-05-17 02:41:08'),(4220,340,'San Juan Coatzospam','2016-05-17 02:41:08'),(4221,340,'San Juan Colorado','2016-05-17 02:41:08'),(4222,340,'San Juan Comaltepec','2016-05-17 02:41:08'),(4223,340,'San Juan CotzocÃ³n','2016-05-17 02:41:09'),(4224,340,'San Juan del Estado','2016-05-17 02:41:09'),(4225,340,'San Juan de los Cues','2016-05-17 02:41:09'),(4226,340,'San Juan del RÃ­o','2016-05-17 02:41:09'),(4227,340,'San Juan Diuxi','2016-05-17 02:41:09'),(4228,340,'San Juan GuelavÃ­a','2016-05-17 02:41:09'),(4229,340,'San Juan Guichicovi','2016-05-17 02:41:09'),(4230,340,'San Juan Ihualtepec','2016-05-17 02:41:09'),(4231,340,'San Juan Juquila Mixes','2016-05-17 02:41:09'),(4232,340,'San Juan Juquila Vijanos','2016-05-17 02:41:09'),(4233,340,'San Juan Lachao','2016-05-17 02:41:09'),(4234,340,'San Juan Lachigalla','2016-05-17 02:41:09'),(4235,340,'San Juan Lajarcia','2016-05-17 02:41:09'),(4236,340,'San Juan Lalana','2016-05-17 02:41:10'),(4237,340,'San Juan MazatlÃ¡n','2016-05-17 02:41:10'),(4238,340,'San Juan Mixtepec, Mixteca','2016-05-17 02:41:10'),(4239,340,'San Juan Mixtepec, MiahuatlÃ¡n','2016-05-17 02:41:10'),(4240,340,'San Juan Ã‘umÃ­','2016-05-17 02:41:10'),(4241,340,'San Juan Ozolotepec','2016-05-17 02:41:10'),(4242,340,'San Juan Petlapa','2016-05-17 02:41:10'),(4243,340,'San Juan Quiahije','2016-05-17 02:41:10'),(4244,340,'San Juan Quiotepec','2016-05-17 02:41:10'),(4245,340,'San Juan Sayultepec','2016-05-17 02:41:10'),(4246,340,'San Juan TabaÃ¡','2016-05-17 02:41:10'),(4247,340,'San Juan Tamazola','2016-05-17 02:41:10'),(4248,340,'San Juan Teita','2016-05-17 02:41:11'),(4249,340,'San Juan Teitipac','2016-05-17 02:41:11'),(4250,340,'San Juan Tepeuxila','2016-05-17 02:41:11'),(4251,340,'San Juan Teposcolula','2016-05-17 02:41:11'),(4252,340,'San Juan YaeÃ©','2016-05-17 02:41:11'),(4253,340,'San Juan Yatzona','2016-05-17 02:41:11'),(4254,340,'San Juan Yucuita','2016-05-17 02:41:11'),(4255,340,'San Lorenzo','2016-05-17 02:41:11'),(4256,340,'San Lorenzo Albarradas','2016-05-17 02:41:11'),(4257,340,'San Lorenzo Cacaotepec','2016-05-17 02:41:11'),(4258,340,'San Lorenzo Cuaunecuiltitla','2016-05-17 02:41:11'),(4259,340,'San Lorenzo Texmelucan','2016-05-17 02:41:11'),(4260,340,'San Lorenzo Victoria','2016-05-17 02:41:11'),(4261,340,'San Lucas CamotlÃ¡n','2016-05-17 02:41:12'),(4262,340,'San Lucas OjitlÃ¡n','2016-05-17 02:41:12'),(4263,340,'San Lucas QuiavinÃ­','2016-05-17 02:41:12'),(4264,340,'San Lucas Zoquiapam','2016-05-17 02:41:12'),(4265,340,'San Luis AmatlÃ¡n','2016-05-17 02:41:12'),(4266,340,'San Marcial Ozolotepec','2016-05-17 02:41:12'),(4267,340,'San Marcos Arteaga','2016-05-17 02:41:12'),(4268,340,'San MartÃ­n de los Cansecos','2016-05-17 02:41:12'),(4269,340,'San MartÃ­n Huamelulpam','2016-05-17 02:41:12'),(4270,340,'San MartÃ­n Itunyoso','2016-05-17 02:41:12'),(4271,340,'San MartÃ­n LachilÃ¡','2016-05-17 02:41:12'),(4272,340,'San MartÃ­n Peras','2016-05-17 02:41:12'),(4273,340,'San MartÃ­n Tilcajete','2016-05-17 02:41:13'),(4274,340,'San MartÃ­n Toxpalan','2016-05-17 02:41:13'),(4275,340,'San MartÃ­n Zacatepec','2016-05-17 02:41:13'),(4276,340,'San Mateo Cajonos','2016-05-17 02:41:13'),(4277,340,'San Mateo del Mar','2016-05-17 02:41:13'),(4278,340,'San Mateo Etlatongo','2016-05-17 02:41:13'),(4279,340,'San Mateo Nejapam','2016-05-17 02:41:13'),(4280,340,'San Mateo PeÃ±asco','2016-05-17 02:41:13'),(4281,340,'San Mateo PiÃ±as','2016-05-17 02:41:13'),(4282,340,'San Mateo RÃ­o Hondo','2016-05-17 02:41:13'),(4283,340,'San Mateo Sindihui','2016-05-17 02:41:13'),(4284,340,'San Mateo Tlapiltepec','2016-05-17 02:41:14'),(4285,340,'San Mateo YoloxochitlÃ¡n','2016-05-17 02:41:14'),(4286,340,'San Melchor Betaza','2016-05-17 02:41:14'),(4287,340,'San Miguel Achiutla','2016-05-17 02:41:14'),(4288,340,'San Miguel AhuehuetitlÃ¡n','2016-05-17 02:41:14'),(4289,340,'San Miguel AloÃ¡pam','2016-05-17 02:41:14'),(4290,340,'San Miguel AmatitlÃ¡n','2016-05-17 02:41:14'),(4291,340,'San Miguel AmatlÃ¡n','2016-05-17 02:41:14'),(4292,340,'San Miguel Chicahua','2016-05-17 02:41:14'),(4293,340,'San Miguel Chimalapa','2016-05-17 02:41:14'),(4294,340,'San Miguel CoatlÃ¡n','2016-05-17 02:41:14'),(4295,340,'San Miguel del Puerto','2016-05-17 02:41:14'),(4296,340,'San Miguel del RÃ­o','2016-05-17 02:41:14'),(4297,340,'San Miguel Ejutla','2016-05-17 02:41:15'),(4298,340,'San Miguel El Grande','2016-05-17 02:41:15'),(4299,340,'San Miguel Huautla','2016-05-17 02:41:15'),(4300,340,'San Miguel Mixtepec','2016-05-17 02:41:15'),(4301,340,'San Miguel Panixtlahuaca','2016-05-17 02:41:15'),(4302,340,'San Miguel Peras','2016-05-17 02:41:15'),(4303,340,'San Miguel Piedras','2016-05-17 02:41:15'),(4304,340,'San Miguel Quetzaltepec','2016-05-17 02:41:15'),(4305,340,'San Miguel Santa Flor','2016-05-17 02:41:15'),(4306,340,'San Miguel Soyaltepec','2016-05-17 02:41:15'),(4307,340,'San Miguel Suchixtepec','2016-05-17 02:41:15'),(4308,340,'San Miguel TecomatlÃ¡n','2016-05-17 02:41:15'),(4309,340,'San Miguel Tenango','2016-05-17 02:41:16'),(4310,340,'San Miguel Tequixtepec','2016-05-17 02:41:16'),(4311,340,'San Miguel Tilquiapam','2016-05-17 02:41:16'),(4312,340,'San Miguel Tlacamama','2016-05-17 02:41:16'),(4313,340,'San Miguel Tlacotepec','2016-05-17 02:41:16'),(4314,340,'San Miguel Tulancingo','2016-05-17 02:41:16'),(4315,340,'San Miguel Yotao','2016-05-17 02:41:16'),(4316,340,'San NicolÃ¡s','2016-05-17 02:41:16'),(4317,340,'San NicolÃ¡s Hidalgo','2016-05-17 02:41:16'),(4318,340,'San Pablo CoatlÃ¡n','2016-05-17 02:41:16'),(4319,340,'San Pablo Cuatro Venados','2016-05-17 02:41:16'),(4320,340,'San Pablo Etla','2016-05-17 02:41:16'),(4321,340,'San Pablo Huitzo','2016-05-17 02:41:16'),(4322,340,'San Pablo Huixtepec','2016-05-17 02:41:17'),(4323,340,'San Pablo Macuiltianguis','2016-05-17 02:41:17'),(4324,340,'San Pablo Tijaltepec','2016-05-17 02:41:17'),(4325,340,'San Pablo Villa de Mitla','2016-05-17 02:41:17'),(4326,340,'San Pablo Yaganiza','2016-05-17 02:41:17'),(4327,340,'San Pedro Amuzgos','2016-05-17 02:41:17'),(4328,340,'San Pedro ApÃ³stol','2016-05-17 02:41:17'),(4329,340,'San Pedro Atoyac','2016-05-17 02:41:17'),(4330,340,'San Pedro Cajonos','2016-05-17 02:41:17'),(4331,340,'San Pedro Comitancillo','2016-05-17 02:41:17'),(4332,340,'San Pedro Coxcaltepec CÃ¡ntaros','2016-05-17 02:41:17'),(4333,340,'San Pedro El Alto','2016-05-17 02:41:17'),(4334,340,'San Pedro Huamelula','2016-05-17 02:41:18'),(4335,340,'San Pedro Huilotepec','2016-05-17 02:41:18'),(4336,340,'San Pedro IxcatlÃ¡n','2016-05-17 02:41:18'),(4337,340,'San Pedro Ixtlahuaca','2016-05-17 02:41:18'),(4338,340,'San Pedro Jaltepetongo','2016-05-17 02:41:18'),(4339,340,'San Pedro Jicayan','2016-05-17 02:41:18'),(4340,340,'San Pedro Jocotipac','2016-05-17 02:41:18'),(4341,340,'San Pedro Juchatengo','2016-05-17 02:41:18'),(4342,340,'San Pedro MÃ¡rtir','2016-05-17 02:41:18'),(4343,340,'San Pedro MÃ¡rtir Quiechapa','2016-05-17 02:41:18'),(4344,340,'San Pedro MÃ¡rtir Yucuxaco','2016-05-17 02:41:18'),(4345,340,'San Pedro Mixtepec, Juquila','2016-05-17 02:41:18'),(4346,340,'San Pedro Mixtepec, MiahuatlÃ¡n','2016-05-17 02:41:18'),(4347,340,'San Pedro Molinos','2016-05-17 02:41:19'),(4348,340,'San Pedro Nopala','2016-05-17 02:41:19'),(4349,340,'San Pedro Ocopetatillo','2016-05-17 02:41:19'),(4350,340,'San Pedro Ocotepec','2016-05-17 02:41:19'),(4351,340,'San Pedro Pochutla','2016-05-17 02:41:19'),(4352,340,'San Pedro Quiatoni','2016-05-17 02:41:19'),(4353,340,'San Pedro Sochiapam','2016-05-17 02:41:19'),(4354,340,'San Pedro Tapanatepec','2016-05-17 02:41:19'),(4355,340,'San Pedro Taviche','2016-05-17 02:41:19'),(4356,340,'San Pedro Teozacoalco','2016-05-17 02:41:19'),(4357,340,'San Pedro Teutila','2016-05-17 02:41:19'),(4358,340,'San Pedro Tidaa','2016-05-17 02:41:19'),(4359,340,'San Pedro Topiltepec','2016-05-17 02:41:20'),(4360,340,'San Pedro Totolapa','2016-05-17 02:41:20'),(4361,340,'San Pedro Yaneri','2016-05-17 02:41:20'),(4362,340,'San Pedro YÃ³lox','2016-05-17 02:41:20'),(4363,340,'San Pedro y San Pablo Ayutla','2016-05-17 02:41:20'),(4364,340,'San Pedro y San Pablo Teposcolula','2016-05-17 02:41:20'),(4365,340,'San Pedro y San Pablo Tequixtepec','2016-05-17 02:41:20'),(4366,340,'San Pedro Yucunama','2016-05-17 02:41:20'),(4367,340,'San Raymundo Jalpan','2016-05-17 02:41:20'),(4368,340,'San SebastiÃ¡n Abasolo','2016-05-17 02:41:20'),(4369,340,'San SebastiÃ¡n CoatlÃ¡n','2016-05-17 02:41:20'),(4370,340,'San SebastiÃ¡n Ixcapa','2016-05-17 02:41:20'),(4371,340,'San SebastiÃ¡n Nicananduta','2016-05-17 02:41:20'),(4372,340,'San SebastiÃ¡n RÃ­o Hondo','2016-05-17 02:41:21'),(4373,340,'San SebastiÃ¡n Tecomaxtlahuaca','2016-05-17 02:41:21'),(4374,340,'San SebastiÃ¡n Teitipac','2016-05-17 02:41:21'),(4375,340,'San SebastiÃ¡n Tutla','2016-05-17 02:41:21'),(4376,340,'San SimÃ³n Almolongas','2016-05-17 02:41:21'),(4377,340,'San SimÃ³n Zahuatlan','2016-05-17 02:41:21'),(4378,340,'Santa Ana','2016-05-17 02:41:21'),(4379,340,'Santa Ana Ateixtlahuaca','2016-05-17 02:41:21'),(4380,340,'Santa Ana CuauhtÃ©moc','2016-05-17 02:41:21'),(4381,340,'Santa Ana del Valle','2016-05-17 02:41:21'),(4382,340,'Santa Ana Tavela','2016-05-17 02:41:21'),(4383,340,'Santa Ana Tlapacoyan','2016-05-17 02:41:21'),(4384,340,'Santa Ana Yareni','2016-05-17 02:41:21'),(4385,340,'Santa Ana Zegache','2016-05-17 02:41:22'),(4386,340,'Santa Catalina Quieri','2016-05-17 02:41:22'),(4387,340,'Santa Catarina Cuixtla','2016-05-17 02:41:22'),(4388,340,'Santa Catarina Ixtepeji','2016-05-17 02:41:22'),(4389,340,'Santa Catarina Juquila','2016-05-17 02:41:22'),(4390,340,'Santa Catarina Lachatao','2016-05-17 02:41:22'),(4391,340,'Santa Catarina Loxicha','2016-05-17 02:41:22'),(4392,340,'Santa Catarina MechoacÃ¡n','2016-05-17 02:41:22'),(4393,340,'Santa Catarina Minas','2016-05-17 02:41:22'),(4394,340,'Santa Catarina QuianÃ©','2016-05-17 02:41:22'),(4395,340,'Santa Catarina Quioquitani','2016-05-17 02:41:22'),(4396,340,'Santa Catarina Tayata','2016-05-17 02:41:22'),(4397,340,'Santa Catarina Ticua','2016-05-17 02:41:22'),(4398,340,'Santa Catarina YosonotÃº','2016-05-17 02:41:23'),(4399,340,'Santa Catarina Zapoquila','2016-05-17 02:41:23'),(4400,340,'Santa Cruz Acatepec','2016-05-17 02:41:23'),(4401,340,'Santa Cruz Amilpas','2016-05-17 02:41:23'),(4402,340,'Santa Cruz de Bravo','2016-05-17 02:41:23'),(4403,340,'Santa Cruz Itundujia','2016-05-17 02:41:23'),(4404,340,'Santa Cruz Mixtepec','2016-05-17 02:41:23'),(4405,340,'Santa Cruz Nundaco','2016-05-17 02:41:23'),(4406,340,'Santa Cruz Papalutla','2016-05-17 02:41:23'),(4407,340,'Santa Cruz Tacache de Mina','2016-05-17 02:41:23'),(4408,340,'Santa Cruz Tacahua','2016-05-17 02:41:23'),(4409,340,'Santa Cruz Tayata','2016-05-17 02:41:23'),(4410,340,'Santa Cruz Xitla','2016-05-17 02:41:24'),(4411,340,'Santa Cruz XoxocotlÃ¡n','2016-05-17 02:41:24'),(4412,340,'Santa Cruz Zenzontepec','2016-05-17 02:41:24'),(4413,340,'Santa Gertrudis','2016-05-17 02:41:24'),(4414,340,'Santa InÃ©s del Monte','2016-05-17 02:41:24'),(4415,340,'Santa InÃ©s de Zaragoza','2016-05-17 02:41:24'),(4416,340,'Santa InÃ©s Yatzeche','2016-05-17 02:41:24'),(4417,340,'Santa LucÃ­a del Camino','2016-05-17 02:41:24'),(4418,340,'Santa LucÃ­a MiahuatlÃ¡n','2016-05-17 02:41:24'),(4419,340,'Santa LucÃ­a Monteverde','2016-05-17 02:41:24'),(4420,340,'Santa LucÃ­a OcotlÃ¡n','2016-05-17 02:41:24'),(4421,340,'Santa Magdalena JicotlÃ¡n','2016-05-17 02:41:24'),(4422,340,'Santa MarÃ­a Alotepec','2016-05-17 02:41:24'),(4423,340,'Santa MarÃ­a Apazco','2016-05-17 02:41:25'),(4424,340,'Santa MarÃ­a Atzompa','2016-05-17 02:41:25'),(4425,340,'Santa MarÃ­a CamotlÃ¡n','2016-05-17 02:41:25'),(4426,340,'Santa MarÃ­a Chachoapam','2016-05-17 02:41:25'),(4427,340,'Santa MarÃ­a Chilchotla','2016-05-17 02:41:25'),(4428,340,'Santa MarÃ­a Chimalapa','2016-05-17 02:41:25'),(4429,340,'Santa MarÃ­a Colotepec','2016-05-17 02:41:25'),(4430,340,'Santa MarÃ­a Cortijo','2016-05-17 02:41:25'),(4431,340,'Santa MarÃ­a Coyotepec','2016-05-17 02:41:25'),(4432,340,'Santa MarÃ­a del Rosario','2016-05-17 02:41:25'),(4433,340,'Santa MarÃ­a del Tule','2016-05-17 02:41:25'),(4434,340,'Santa MarÃ­a Ecatepec','2016-05-17 02:41:26'),(4435,340,'Santa MarÃ­a GuelacÃ©','2016-05-17 02:41:26'),(4436,340,'Santa MarÃ­a Guienagati','2016-05-17 02:41:26'),(4437,340,'Santa MarÃ­a Huatulco','2016-05-17 02:41:26'),(4438,340,'Santa MarÃ­a HuazolotitlÃ¡n','2016-05-17 02:41:26'),(4439,340,'Santa MarÃ­a Ipalapa','2016-05-17 02:41:26'),(4440,340,'Santa MarÃ­a IxcatlÃ¡n','2016-05-17 02:41:26'),(4441,340,'Santa MarÃ­a Jacatepec','2016-05-17 02:41:26'),(4442,340,'Santa MarÃ­a Jalapa del MarquÃ©s','2016-05-17 02:41:26'),(4443,340,'Santa MarÃ­a Jaltianguis','2016-05-17 02:41:26'),(4444,340,'Santa MarÃ­a la AsunciÃ³n','2016-05-17 02:41:26'),(4445,340,'Santa MarÃ­a LachixÃ­o','2016-05-17 02:41:26'),(4446,340,'Santa MarÃ­a Mixtequilla','2016-05-17 02:41:26'),(4447,340,'Santa MarÃ­a Nativitas','2016-05-17 02:41:27'),(4448,340,'Santa MarÃ­a Nduayaco','2016-05-17 02:41:27'),(4449,340,'Santa MarÃ­a Ozolotepec','2016-05-17 02:41:27'),(4450,340,'Santa MarÃ­a PÃ¡palo','2016-05-17 02:41:27'),(4451,340,'Santa MarÃ­a PeÃ±oles','2016-05-17 02:41:27'),(4452,340,'Santa MarÃ­a Petapa','2016-05-17 02:41:27'),(4453,340,'Santa MarÃ­a Quiegolani','2016-05-17 02:41:27'),(4454,340,'Santa MarÃ­a Sola','2016-05-17 02:41:27'),(4455,340,'Santa MarÃ­a Tataltepec','2016-05-17 02:41:27'),(4456,340,'Santa MarÃ­a Tecomavaca','2016-05-17 02:41:27'),(4457,340,'Santa MarÃ­a Temaxcalapa','2016-05-17 02:41:27'),(4458,340,'Santa MarÃ­a Temaxcaltepec','2016-05-17 02:41:27'),(4459,340,'Santa MarÃ­a Teopoxco','2016-05-17 02:41:27'),(4460,340,'Santa MarÃ­a Tepantlali','2016-05-17 02:41:28'),(4461,340,'Santa MarÃ­a TexcatitlÃ¡n','2016-05-17 02:41:28'),(4462,340,'Santa MarÃ­a Tlahuitoltepec','2016-05-17 02:41:28'),(4463,340,'Santa MarÃ­a Tlalixtac','2016-05-17 02:41:28'),(4464,340,'Santa MarÃ­a Tonameca','2016-05-17 02:41:28'),(4465,340,'Santa MarÃ­a Totolapilla','2016-05-17 02:41:28'),(4466,340,'Santa MarÃ­a Xadani','2016-05-17 02:41:28'),(4467,340,'Santa MarÃ­a Yalina','2016-05-17 02:41:28'),(4468,340,'Santa MarÃ­a YavesÃ­a','2016-05-17 02:41:28'),(4469,340,'Santa MarÃ­a Yolotepec','2016-05-17 02:41:28'),(4470,340,'Santa MarÃ­a YosoyÃºa','2016-05-17 02:41:28'),(4471,340,'Santa MarÃ­a Yucuhiti','2016-05-17 02:41:28'),(4472,340,'Santa MarÃ­a Zacatepec','2016-05-17 02:41:29'),(4473,340,'Santa MarÃ­a Zaniza','2016-05-17 02:41:29'),(4474,340,'Santa MarÃ­a ZoquitlÃ¡n','2016-05-17 02:41:29'),(4475,340,'Santiago Amoltepec','2016-05-17 02:41:29'),(4476,340,'Santiago Apoala','2016-05-17 02:41:29'),(4477,340,'Santiago ApÃ³stol','2016-05-17 02:41:29'),(4478,340,'Santiago Astata','2016-05-17 02:41:29'),(4479,340,'Santiago AtitlÃ¡n','2016-05-17 02:41:29'),(4480,340,'Santiago Ayuquililla','2016-05-17 02:41:29'),(4481,340,'Santiago Cacaloxtepec','2016-05-17 02:41:29'),(4482,340,'Santiago CamotlÃ¡n','2016-05-17 02:41:29'),(4483,340,'Santiago Chazumba','2016-05-17 02:41:29'),(4484,340,'Santiago Choapam','2016-05-17 02:41:30'),(4485,340,'Santiago Comaltepec','2016-05-17 02:41:30'),(4486,340,'Santiago del RÃ­o','2016-05-17 02:41:30'),(4487,340,'Santiago HuajolotitlÃ¡n','2016-05-17 02:41:30'),(4488,340,'Santiago Huauclilla','2016-05-17 02:41:30'),(4489,340,'Santiago IhuitlÃ¡n Plumas','2016-05-17 02:41:30'),(4490,340,'Santiago Ixcuintepec','2016-05-17 02:41:30'),(4491,340,'Santiago Ixtayutla','2016-05-17 02:41:30'),(4492,340,'Santiago Jamiltepec','2016-05-17 02:41:30'),(4493,340,'Santiago Jocotepec','2016-05-17 02:41:30'),(4494,340,'Santiago Juxtlahuaca','2016-05-17 02:41:30'),(4495,340,'Santiago Lachiguiri','2016-05-17 02:41:30'),(4496,340,'Santiago Lalopa','2016-05-17 02:41:30'),(4497,340,'Santiago Laollaga','2016-05-17 02:41:31'),(4498,340,'Santiago Laxopa','2016-05-17 02:41:31'),(4499,340,'Santiago Llano Grande','2016-05-17 02:41:31'),(4500,340,'Santiago MatatlÃ¡n','2016-05-17 02:41:31'),(4501,340,'Santiago Miltepec','2016-05-17 02:41:31'),(4502,340,'Santiago Minas','2016-05-17 02:41:31'),(4503,340,'Santiago Nacaltepec','2016-05-17 02:41:31'),(4504,340,'Santiago Nejapilla','2016-05-17 02:41:31'),(4505,340,'Santiago Niltepec','2016-05-17 02:41:31'),(4506,340,'Santiago Nundiche','2016-05-17 02:41:31'),(4507,340,'Santiago NuyoÃ³','2016-05-17 02:41:31'),(4508,340,'Santiago Suchilquitongo','2016-05-17 02:41:31'),(4509,340,'Santiago Tamazola','2016-05-17 02:41:31'),(4510,340,'Santiago Tapextla','2016-05-17 02:41:32'),(4511,340,'Santiago Tenango','2016-05-17 02:41:32'),(4512,340,'Santiago Tepetlapa','2016-05-17 02:41:32'),(4513,340,'Santiago Tetepec','2016-05-17 02:41:32'),(4514,340,'Santiago Texcalcingo','2016-05-17 02:41:32'),(4515,340,'Santiago TextitlÃ¡n','2016-05-17 02:41:32'),(4516,340,'Santiago Tilantongo','2016-05-17 02:41:32'),(4517,340,'Santiago Tillo','2016-05-17 02:41:32'),(4518,340,'Santiago Tlazoyaltepec','2016-05-17 02:41:32'),(4519,340,'Santiago Xanica','2016-05-17 02:41:32'),(4520,340,'Santiago XiacuÃ­','2016-05-17 02:41:32'),(4521,340,'Santiago Yaitepec','2016-05-17 02:41:32'),(4522,340,'Santiago Yaveo','2016-05-17 02:41:33'),(4523,340,'Santiago YolomÃ©catl','2016-05-17 02:41:33'),(4524,340,'Santiago YosondÃºa','2016-05-17 02:41:33'),(4525,340,'Santiago Yucuyachi','2016-05-17 02:41:33'),(4526,340,'Santiago Zacatepec','2016-05-17 02:41:33'),(4527,340,'Santiago Zoochila','2016-05-17 02:41:33'),(4528,340,'Santo Domingo Albarradas','2016-05-17 02:41:33'),(4529,340,'Santo Domingo Armenta','2016-05-17 02:41:33'),(4530,340,'Santo Domingo ChihuitÃ¡n','2016-05-17 02:41:33'),(4531,340,'Santo Domingo de Morelos','2016-05-17 02:41:33'),(4532,340,'Santo Domingo Ingenio','2016-05-17 02:41:33'),(4533,340,'Santo Domingo IxcatlÃ¡n','2016-05-17 02:41:33'),(4534,340,'Santo Domingo NuxaÃ¡','2016-05-17 02:41:34'),(4535,340,'Santo Domingo Ozolotepec','2016-05-17 02:41:34'),(4536,340,'Santo Domingo Petapa','2016-05-17 02:41:34'),(4537,340,'Santo Domingo Roayaga','2016-05-17 02:41:34'),(4538,340,'Santo Domingo Tehuantepec','2016-05-17 02:41:34'),(4539,340,'Santo Domingo Teojomulco','2016-05-17 02:41:34'),(4540,340,'Santo Domingo Tepuxtepec','2016-05-17 02:41:34'),(4541,340,'Santo Domingo Tlatayapam','2016-05-17 02:41:34'),(4542,340,'Santo Domingo Tomaltepec','2016-05-17 02:41:34'),(4543,340,'Santo Domingo TonalÃ¡','2016-05-17 02:41:34'),(4544,340,'Santo Domingo Tonaltepec','2016-05-17 02:41:34'),(4545,340,'Santo Domingo XagacÃ­a','2016-05-17 02:41:34'),(4546,340,'Santo Domingo YanhuitlÃ¡n','2016-05-17 02:41:34'),(4547,340,'Santo Domingo Yodohino','2016-05-17 02:41:35'),(4548,340,'Santo Domingo Zanatepec','2016-05-17 02:41:35'),(4549,340,'Santos Reyes Nopala','2016-05-17 02:41:35'),(4550,340,'Santos Reyes PÃ¡palo','2016-05-17 02:41:35'),(4551,340,'Santos Reyes Tepejillo','2016-05-17 02:41:35'),(4552,340,'Santos Reyes YucunÃ¡','2016-05-17 02:41:35'),(4553,340,'Santo TomÃ¡s Jalieza','2016-05-17 02:41:35'),(4554,340,'Santo TomÃ¡s Mazaltepec','2016-05-17 02:41:35'),(4555,340,'Santo TomÃ¡s Ocotepec','2016-05-17 02:41:35'),(4556,340,'Santo TomÃ¡s Tamazulapan','2016-05-17 02:41:35'),(4557,340,'San Vicente CoatlÃ¡n','2016-05-17 02:41:35'),(4558,340,'San Vicente LachixÃ­o','2016-05-17 02:41:35'),(4559,340,'San Vicente NuÃ±Ãº','2016-05-17 02:41:36'),(4560,340,'Silacayoapam','2016-05-17 02:41:36'),(4561,340,'Sitio de Xitlapehua','2016-05-17 02:41:36'),(4562,340,'Soledad Etla','2016-05-17 02:41:36'),(4563,340,'Tamazulapam del EspÃ­ritu Santo','2016-05-17 02:41:36'),(4564,340,'Tamazulapam del Progreso','2016-05-17 02:41:36'),(4565,340,'Tanetze de Zaragoza','2016-05-17 02:41:36'),(4566,340,'Taniche','2016-05-17 02:41:36'),(4567,340,'Tataltepec de ValdÃ©s','2016-05-17 02:41:36'),(4568,340,'Teococuilco de Marcos PÃ©rez','2016-05-17 02:41:36'),(4569,340,'TeotitlÃ¡n de Flores MagÃ³n','2016-05-17 02:41:36'),(4570,340,'TeotitlÃ¡n del Valle','2016-05-17 02:41:36'),(4571,340,'Teotongo','2016-05-17 02:41:36'),(4572,340,'Tepelmeme Villa de Morelos','2016-05-17 02:41:37'),(4573,340,'TezoatlÃ¡n de Segura y Luna','2016-05-17 02:41:37'),(4574,340,'Tlacolula de Matamoros','2016-05-17 02:41:37'),(4575,340,'Tlacotepec Plumas','2016-05-17 02:41:37'),(4576,340,'Tlalixtac de Cabrera','2016-05-17 02:41:37'),(4577,340,'Tlaxiaco','2016-05-17 02:41:37'),(4578,340,'Totontepec Villa de Morelos','2016-05-17 02:41:37'),(4579,340,'Trinidad Zaachila','2016-05-17 02:41:37'),(4580,340,'UniÃ³n Hidalgo','2016-05-17 02:41:37'),(4581,340,'Valerio Trujano','2016-05-17 02:41:37'),(4582,340,'Villa de Etla','2016-05-17 02:41:37'),(4583,340,'Villa de Tututepec de Melchor Ocampo','2016-05-17 02:41:37'),(4584,340,'Villa de Zaachila','2016-05-17 02:41:38'),(4585,340,'Cuyamecalco Villa de Zaragoza','2016-05-17 02:41:38'),(4586,340,'Villa DÃ­az Ordaz','2016-05-17 02:41:38'),(4587,340,'Villa Hidalgo','2016-05-17 02:41:38'),(4588,340,'Villa Sola de Vega','2016-05-17 02:41:39'),(4589,340,'Villa Talea de Castro','2016-05-17 02:41:39'),(4590,340,'Villa Tejupam de la UniÃ³n','2016-05-17 02:41:39'),(4591,340,'Yaxe Magdalena','2016-05-17 02:41:39'),(4592,340,'Magdalena Yodocono de Porfirio DÃ­az','2016-05-17 02:41:39'),(4593,340,'Yogana','2016-05-17 02:41:40'),(4594,340,'Yutanduchi de Guerrero','2016-05-17 02:41:40'),(4595,340,'ZapotitlÃ¡n del RÃ­o','2016-05-17 02:41:40'),(4596,340,'ZapotitlÃ¡n Lagunas','2016-05-17 02:41:40'),(4597,340,'ZapotitlÃ¡n Palmas','2016-05-17 02:41:40'),(4598,340,'ZimatlÃ¡n de Alvarez','2016-05-17 02:41:41'),(4599,353,'Acajete','2016-05-17 02:41:41'),(4600,353,'Acateno','2016-05-17 02:41:41'),(4601,353,'AcatlÃ¡n de Osorio','2016-05-17 02:41:41'),(4602,353,'Acatzingo','2016-05-17 02:41:41'),(4603,353,'Acteopan','2016-05-17 02:41:42'),(4604,353,'AhuacatlÃ¡n','2016-05-17 02:41:42'),(4605,353,'AhuatlÃ¡n','2016-05-17 02:41:42'),(4606,353,'Ahuazotepec','2016-05-17 02:41:42'),(4607,353,'Ahuehuetitla','2016-05-17 02:41:42'),(4608,353,'Ajalpan','2016-05-17 02:41:42'),(4609,353,'Albino Zertuche','2016-05-17 02:41:42'),(4610,353,'Aljojuca','2016-05-17 02:41:42'),(4611,353,'Altepexi','2016-05-17 02:41:42'),(4612,353,'Amixtlan','2016-05-17 02:41:42'),(4613,353,'Amozoc','2016-05-17 02:41:43'),(4614,353,'Aquixtla','2016-05-17 02:41:43'),(4615,353,'Atempan','2016-05-17 02:41:43'),(4616,353,'Atexcal','2016-05-17 02:41:43'),(4617,353,'Atlequizayan','2016-05-17 02:41:43'),(4618,353,'Atlixco','2016-05-17 02:41:43'),(4619,353,'Atoyatempan','2016-05-17 02:41:43'),(4620,353,'Atzala','2016-05-17 02:41:43'),(4621,353,'AtzitzihuacÃ¡n','2016-05-17 02:41:43'),(4622,353,'Atzitzintla','2016-05-17 02:41:43'),(4623,353,'Axutla','2016-05-17 02:41:43'),(4624,353,'Ayotoxco de Guerrero','2016-05-17 02:41:43'),(4625,353,'Calpan','2016-05-17 02:41:43'),(4626,353,'Caltepec','2016-05-17 02:41:44'),(4627,353,'Camocuautla','2016-05-17 02:41:44'),(4628,353,'CaÃ±ada Morelos','2016-05-17 02:41:44'),(4629,353,'CaxhuacÃ¡n','2016-05-17 02:41:44'),(4630,353,'Chalchicomula de Sesma','2016-05-17 02:41:44'),(4631,353,'Chapulco','2016-05-17 02:41:44'),(4632,353,'Chiautla','2016-05-17 02:41:44'),(4633,353,'Chiautzingo','2016-05-17 02:41:44'),(4634,353,'Chichiquila','2016-05-17 02:41:44'),(4635,353,'Chiconcuautla','2016-05-17 02:41:44'),(4636,353,'Chietla','2016-05-17 02:41:44'),(4637,353,'ChigmecatitlÃ¡n','2016-05-17 02:41:44'),(4638,353,'Chignahuapan','2016-05-17 02:41:44'),(4639,353,'Chignautla','2016-05-17 02:41:45'),(4640,353,'Chila','2016-05-17 02:41:45'),(4641,353,'Chila de la Sal','2016-05-17 02:41:45'),(4642,353,'Chilchotla','2016-05-17 02:41:45'),(4643,353,'Chinantla','2016-05-17 02:41:45'),(4644,353,'Coatepec','2016-05-17 02:41:45'),(4645,353,'Coatzingo','2016-05-17 02:41:45'),(4646,353,'Cohetzala','2016-05-17 02:41:45'),(4647,353,'CohuecÃ¡n','2016-05-17 02:41:45'),(4648,353,'Coronango','2016-05-17 02:41:45'),(4649,353,'CoxcatlÃ¡n','2016-05-17 02:41:45'),(4650,353,'Coyomeapan','2016-05-17 02:41:45'),(4651,353,'Coyotepec','2016-05-17 02:41:46'),(4652,353,'Cuapiaxtla de Madero','2016-05-17 02:41:46'),(4653,353,'Cuautempan','2016-05-17 02:41:46'),(4654,353,'Cuautinchan','2016-05-17 02:41:46'),(4655,353,'Cuautlancingo','2016-05-17 02:41:46'),(4656,353,'Cuayuca de Andrade','2016-05-17 02:41:46'),(4657,353,'CuetzalÃ¡n del Progreso','2016-05-17 02:41:46'),(4658,353,'Cuyoaco','2016-05-17 02:41:46'),(4659,353,'Domingo Arenas','2016-05-17 02:41:46'),(4660,353,'EloxochitlÃ¡n','2016-05-17 02:41:46'),(4661,353,'EpatlÃ¡n','2016-05-17 02:41:46'),(4662,353,'Esperanza','2016-05-17 02:41:46'),(4663,353,'Francisco Z. Mena','2016-05-17 02:41:47'),(4664,353,'General Felipe Ãngeles','2016-05-17 02:41:47'),(4665,353,'Guadalupe','2016-05-17 02:41:47'),(4666,353,'Guadalupe Victoria','2016-05-17 02:41:47'),(4667,353,'Hermenegildo Galeana','2016-05-17 02:41:47'),(4668,353,'Honey','2016-05-17 02:41:47'),(4669,353,'Huaquechula','2016-05-17 02:41:47'),(4670,353,'Huatlatlauca','2016-05-17 02:41:47'),(4671,353,'Huauchinango','2016-05-17 02:41:47'),(4672,353,'Huehuetla','2016-05-17 02:41:47'),(4673,353,'HuehuetlÃ¡n El Chico','2016-05-17 02:41:47'),(4674,353,'HuehuetlÃ¡n El Grande','2016-05-17 02:41:47'),(4675,353,'Huejotzingo','2016-05-17 02:41:47'),(4676,353,'Hueyapan','2016-05-17 02:41:48'),(4677,353,'Hueytamalco','2016-05-17 02:41:48'),(4678,353,'Hueytlalpan','2016-05-17 02:41:48'),(4679,353,'Huitzilan de SerdÃ¡n','2016-05-17 02:41:48'),(4680,353,'Huitziltepec','2016-05-17 02:41:48'),(4681,353,'Ixcamilpa','2016-05-17 02:41:48'),(4682,353,'Ixcaquixtla','2016-05-17 02:41:48'),(4683,353,'IxtacamaxtitlÃ¡n','2016-05-17 02:41:48'),(4684,353,'Ixtepec','2016-05-17 02:41:48'),(4685,353,'IzÃºcar de Matamoros','2016-05-17 02:41:48'),(4686,353,'Jalpan','2016-05-17 02:41:48'),(4687,353,'Jolalpan','2016-05-17 02:41:48'),(4688,353,'Jopala','2016-05-17 02:41:48'),(4689,353,'Juan C. Bonilla','2016-05-17 02:41:49'),(4690,353,'Juan Galindo','2016-05-17 02:41:49'),(4691,353,'Juan N. MÃ©ndez','2016-05-17 02:41:49'),(4692,353,'Lafragua','2016-05-17 02:41:49'),(4693,353,'Libres','2016-05-17 02:41:49'),(4694,353,'La Magdalena Tlatlauquitepec','2016-05-17 02:41:49'),(4695,353,'Los Reyes de JuÃ¡rez','2016-05-17 02:41:49'),(4696,353,'Mazapiltepec de JuÃ¡rez','2016-05-17 02:41:49'),(4697,353,'Mixtla','2016-05-17 02:41:49'),(4698,353,'Molcaxac','2016-05-17 02:41:49'),(4699,353,'Naupan','2016-05-17 02:41:49'),(4700,353,'Nauzontla','2016-05-17 02:41:49'),(4701,353,'NealticÃ¡n','2016-05-17 02:41:50'),(4702,353,'NicolÃ¡s Bravo','2016-05-17 02:41:50'),(4703,353,'Nopalucan','2016-05-17 02:41:50'),(4704,353,'Ocotepec','2016-05-17 02:41:50'),(4705,353,'Ocoyucan','2016-05-17 02:41:50'),(4706,353,'Olintla','2016-05-17 02:41:50'),(4707,353,'Oriental','2016-05-17 02:41:50'),(4708,353,'PahuatlÃ¡n','2016-05-17 02:41:50'),(4709,353,'Palmar de Bravo','2016-05-17 02:41:50'),(4710,353,'Pantepec','2016-05-17 02:41:50'),(4711,353,'Petlalcingo','2016-05-17 02:41:50'),(4712,353,'Piaxtla','2016-05-17 02:41:50'),(4713,353,'Puebla','2016-05-17 02:41:50'),(4714,353,'Quecholac','2016-05-17 02:41:51'),(4715,353,'QuimixtlÃ¡n','2016-05-17 02:41:51'),(4716,353,'Rafael Lara Grajales','2016-05-17 02:41:51'),(4717,353,'San AndrÃ©s Cholula','2016-05-17 02:41:51'),(4718,353,'San Antonio CaÃ±ada','2016-05-17 02:41:51'),(4719,353,'San Diego La Mesa Tochimiltzingo','2016-05-17 02:41:51'),(4720,353,'San Felipe Teotlalcingo','2016-05-17 02:41:51'),(4721,353,'San Felipe TepatlÃ¡n','2016-05-17 02:41:51'),(4722,353,'San Gabriel Chilac','2016-05-17 02:41:51'),(4723,353,'San Gregorio Atzompa','2016-05-17 02:41:51'),(4724,353,'San JerÃ³nimo Tecuanipan','2016-05-17 02:41:51'),(4725,353,'San JerÃ³nimo XayacatlÃ¡n','2016-05-17 02:41:51'),(4726,353,'San JosÃ© Chiapa','2016-05-17 02:41:51'),(4727,353,'San JosÃ© MiahuatlÃ¡n','2016-05-17 02:41:52'),(4728,353,'San Juan Atenco','2016-05-17 02:41:52'),(4729,353,'San Juan Atzompa','2016-05-17 02:41:52'),(4730,353,'San MartÃ­n Texmelucan','2016-05-17 02:41:52'),(4731,353,'San MartÃ­n Totoltepec','2016-05-17 02:41:52'),(4732,353,'San MatÃ­as Tlalancaleca','2016-05-17 02:41:52'),(4733,353,'San Miguel IxitlÃ¡n','2016-05-17 02:41:52'),(4734,353,'San Miguel Xoxtla','2016-05-17 02:41:52'),(4735,353,'San NicolÃ¡s de Buenos Aires','2016-05-17 02:41:52'),(4736,353,'San NicolÃ¡s de los Ranchos','2016-05-17 02:41:52'),(4737,353,'San Pablo Anicano','2016-05-17 02:41:52'),(4738,353,'San Pedro Cholula','2016-05-17 02:41:52'),(4739,353,'San Pedro Yeloixtlahuacan','2016-05-17 02:41:52'),(4740,353,'San Salvador El Seco','2016-05-17 02:41:53'),(4741,353,'San Salvador El Verde','2016-05-17 02:41:53'),(4742,353,'San Salvador Huixcolotla','2016-05-17 02:41:53'),(4743,353,'San SebastiÃ¡n Tlacotepec','2016-05-17 02:41:53'),(4744,353,'Santa Catarina Tlaltempan','2016-05-17 02:41:53'),(4745,353,'Santa InÃ©s Ahuatempan','2016-05-17 02:41:53'),(4746,353,'Santa Isabel Cholula','2016-05-17 02:41:53'),(4747,353,'Santiago MiahuatlÃ¡n','2016-05-17 02:41:53'),(4748,353,'Santo TomÃ¡s HueyotlipÃ¡n','2016-05-17 02:41:53'),(4749,353,'Soltepec','2016-05-17 02:41:53'),(4750,353,'Tecali','2016-05-17 02:41:53'),(4751,353,'Tecamachalco','2016-05-17 02:41:53'),(4752,353,'TecomatlÃ¡n','2016-05-17 02:41:54'),(4753,353,'TehuacÃ¡n','2016-05-17 02:41:54'),(4754,353,'Tehuitzingo','2016-05-17 02:41:54'),(4755,353,'Tenampulco','2016-05-17 02:41:54'),(4756,353,'TeopantlÃ¡n','2016-05-17 02:41:54'),(4757,353,'Teotlalco','2016-05-17 02:41:54'),(4758,353,'Tepanco de LÃ³pez','2016-05-17 02:41:54'),(4759,353,'Tepango de RodrÃ­guez','2016-05-17 02:41:54'),(4760,353,'Tepatlaxco de Hidalgo','2016-05-17 02:41:54'),(4761,353,'Tepeaca','2016-05-17 02:41:54'),(4762,353,'Tepemaxalco','2016-05-17 02:41:54'),(4763,353,'Tepeojuma','2016-05-17 02:41:54'),(4764,353,'Tepetzintla','2016-05-17 02:41:54'),(4765,353,'Tepexco','2016-05-17 02:41:55'),(4766,353,'Tepexi de RodrÃ­guez','2016-05-17 02:41:55'),(4767,353,'Tepeyahualco','2016-05-17 02:41:55'),(4768,353,'Tepeyahualco de CuauhtÃ©moc','2016-05-17 02:41:55'),(4769,353,'Tetela de Ocampo','2016-05-17 02:41:55'),(4770,353,'Teteles de Ãvila Castillo','2016-05-17 02:41:55'),(4771,353,'TezuitlÃ¡n','2016-05-17 02:41:55'),(4772,353,'Tianguismanalco','2016-05-17 02:41:55'),(4773,353,'Tilapa','2016-05-17 02:41:55'),(4774,353,'Tlachichuca','2016-05-17 02:41:55'),(4775,353,'Tlacotepec de Benito JuÃ¡rez','2016-05-17 02:41:55'),(4776,353,'Tlacuilotepec','2016-05-17 02:41:55'),(4777,353,'Tlahuapan','2016-05-17 02:41:56'),(4778,353,'Tlaltenango','2016-05-17 02:41:56'),(4779,353,'Tlanepantla','2016-05-17 02:41:56'),(4780,353,'Tlaola','2016-05-17 02:41:56'),(4781,353,'Tlapacoya','2016-05-17 02:41:56'),(4782,353,'Tlapanala','2016-05-17 02:41:56'),(4783,353,'Tlatlauquitepec','2016-05-17 02:41:56'),(4784,353,'Tlaxco','2016-05-17 02:41:56'),(4785,353,'Tochimilco','2016-05-17 02:41:56'),(4786,353,'Tochtepec','2016-05-17 02:41:56'),(4787,353,'Totoltepec de Guerrero','2016-05-17 02:41:56'),(4788,353,'Tulcingo','2016-05-17 02:41:56'),(4789,353,'TuzamapÃ¡n de Galeana','2016-05-17 02:41:57'),(4790,353,'Tzicatlacoyan','2016-05-17 02:41:57'),(4791,353,'Venustiano Carranza','2016-05-17 02:41:57'),(4792,353,'Vicente Guerrero','2016-05-17 02:41:57'),(4793,353,'XayacatlÃ¡n de Bravo','2016-05-17 02:41:57'),(4794,353,'Xicotepec','2016-05-17 02:41:57'),(4795,353,'XicotlÃ¡n','2016-05-17 02:41:57'),(4796,353,'Xiutetelco','2016-05-17 02:41:57'),(4797,353,'Xochiapulco','2016-05-17 02:41:57'),(4798,353,'Xochiltepec','2016-05-17 02:41:57'),(4799,353,'XochitlÃ¡n de Vicente SuÃ¡rez','2016-05-17 02:41:57'),(4800,353,'XochitlÃ¡n Todos Santos','2016-05-17 02:41:57'),(4801,353,'Xonotla','2016-05-17 02:41:58'),(4802,353,'Yaonahuac','2016-05-17 02:41:58'),(4803,353,'Yehualtepec','2016-05-17 02:41:58'),(4804,353,'Zacapala','2016-05-17 02:41:58'),(4805,353,'Zacapoaxtla','2016-05-17 02:41:58'),(4806,353,'ZacatlÃ¡n','2016-05-17 02:41:58'),(4807,353,'ZapotitlÃ¡n','2016-05-17 02:41:58'),(4808,353,'ZapotitlÃ¡n de MÃ©ndez','2016-05-17 02:41:58'),(4809,353,'Zaragoza','2016-05-17 02:41:58'),(4810,353,'Zautla','2016-05-17 02:41:58'),(4811,353,'Zihuateutla','2016-05-17 02:41:58'),(4812,353,'Zinacatepec','2016-05-17 02:41:58'),(4813,353,'Zongozotla','2016-05-17 02:41:58'),(4814,353,'Zoquiapan','2016-05-17 02:41:59'),(4815,353,'ZoquitlÃ¡n','2016-05-17 02:41:59'),(4816,193,'Cozumel','2016-05-17 02:41:59'),(4817,193,'Felipe Carrillo Puerto','2016-05-17 02:41:59'),(4818,193,'Isla Mujeres','2016-05-17 02:41:59'),(4819,193,'OthÃ³n P. Blanco','2016-05-17 02:41:59'),(4820,193,'Benito JuÃ¡rez','2016-05-17 02:41:59'),(4821,193,'JosÃ© MarÃ­a Morelos','2016-05-17 02:41:59'),(4822,193,'LÃ¡zaro CÃ¡rdenas','2016-05-17 02:41:59'),(4823,193,'Solidaridad','2016-05-17 02:41:59'),(4824,193,'Tulum','2016-05-17 02:41:59'),(4825,193,'Bacalar','2016-05-17 02:41:59'),(4826,200,'Ahome','2016-05-17 02:41:59'),(4827,200,'Angostura','2016-05-17 02:42:00'),(4828,200,'Badiraguato','2016-05-17 02:42:00'),(4829,200,'Concordia','2016-05-17 02:42:00'),(4830,200,'CosalÃ¡','2016-05-17 02:42:00'),(4831,200,'CuliacÃ¡n','2016-05-17 02:42:00'),(4832,200,'Choix','2016-05-17 02:42:00'),(4833,200,'Elota','2016-05-17 02:42:00'),(4834,200,'Escuinapa','2016-05-17 02:42:00'),(4835,200,'El Fuerte','2016-05-17 02:42:00'),(4836,200,'Guasave','2016-05-17 02:42:00'),(4837,200,'MazatlÃ¡n','2016-05-17 02:42:00'),(4838,200,'Mocorito','2016-05-17 02:42:01'),(4839,200,'Rosario','2016-05-17 02:42:01'),(4840,200,'Salvador Alvarado','2016-05-17 02:42:01'),(4841,200,'San Ignacio','2016-05-17 02:42:01'),(4842,200,'Sinaloa','2016-05-17 02:42:01'),(4843,200,'Navolato','2016-05-17 02:42:01'),(4844,197,'Aconchi','2016-05-17 02:42:01'),(4845,197,'Agua Prieta','2016-05-17 02:42:01'),(4846,197,'Altar','2016-05-17 02:42:01'),(4847,197,'Arivechi','2016-05-17 02:42:01'),(4848,197,'Arizpe','2016-05-17 02:42:01'),(4849,197,'Atil','2016-05-17 02:42:01'),(4850,197,'BacadÃ©huachi','2016-05-17 02:42:02'),(4851,197,'Bacanora','2016-05-17 02:42:02'),(4852,197,'Bacerac','2016-05-17 02:42:02'),
   (4853,197,'Bacoachi','2016-05-17 02:42:02'),(4854,197,'BÃ¡cum','2016-05-17 02:42:02'),(4855,197,'BanÃ¡michi','2016-05-17 02:42:02'),(4856,197,'BaviÃ¡cora','2016-05-17 02:42:02'),(4857,197,'Bavispe','2016-05-17 02:42:02'),(4858,197,'Benito JuÃ¡rez','2016-05-17 02:42:02'),(4859,197,'BenjamÃ­n Hill','2016-05-17 02:42:02'),(4860,197,'Caborca','2016-05-17 02:42:02'),(4861,197,'Cajeme','2016-05-17 02:42:02'),(4862,197,'Cananea','2016-05-17 02:42:03'),(4863,197,'CarbÃ³','2016-05-17 02:42:03'),(4864,197,'Cumpas','2016-05-17 02:42:03'),(4865,197,'Divisaderos','2016-05-17 02:42:03'),(4866,197,'Empalme','2016-05-17 02:42:03'),(4867,197,'Etchojoa','2016-05-17 02:42:03'),(4868,197,'Fronteras','2016-05-17 02:42:03'),(4869,197,'Granados','2016-05-17 02:42:03'),(4870,197,'Guaymas','2016-05-17 02:42:03'),(4871,197,'Hermosillo','2016-05-17 02:42:03'),(4872,197,'Huachinera','2016-05-17 02:42:03'),(4873,197,'HuÃ¡sabas','2016-05-17 02:42:03'),(4874,197,'Huatabampo','2016-05-17 02:42:03'),(4875,197,'HuÃ©pac','2016-05-17 02:42:04'),(4876,197,'Imuris','2016-05-17 02:42:04'),(4877,197,'La Colorada','2016-05-17 02:42:04'),(4878,197,'Magdalena de Kino','2016-05-17 02:42:04'),(4879,197,'MazatÃ¡n','2016-05-17 02:42:04'),(4880,197,'Moctezuma','2016-05-17 02:42:04'),(4881,197,'Naco','2016-05-17 02:42:04'),(4882,197,'NÃ¡cori Chico','2016-05-17 02:42:04'),(4883,197,'Nacozari de GarcÃ­a','2016-05-17 02:42:04'),(4884,197,'Navojoa','2016-05-17 02:42:04'),(4885,197,'Nogales','2016-05-17 02:42:04'),(4886,197,'Onavas','2016-05-17 02:42:04'),(4887,197,'Opodepe','2016-05-17 02:42:05'),(4888,197,'Oquitoa','2016-05-17 02:42:05'),(4889,197,'Pitiquito','2016-05-17 02:42:05'),(4890,197,'Puerto PeÃ±asco','2016-05-17 02:42:05'),(4891,197,'Plutarco ElÃ­as Calles','2016-05-17 02:42:05'),(4892,197,'Quiriego','2016-05-17 02:42:05'),(4893,197,'RayÃ³n','2016-05-17 02:42:05'),(4894,197,'Rosario de Tesopaco','2016-05-17 02:42:05'),(4895,197,'Sahuaripa','2016-05-17 02:42:05'),(4896,197,'San Ignacio RÃ­o Muerto','2016-05-17 02:42:05'),(4897,197,'San Javier','2016-05-17 02:42:05'),(4898,197,'San Luis RÃ­o Colorado','2016-05-17 02:42:05'),(4899,197,'San Miguel de Horcasitas','2016-05-17 02:42:05'),(4900,197,'San Pedro de la Cueva','2016-05-17 02:42:06'),(4901,197,'Santa Ana','2016-05-17 02:42:06'),(4902,197,'Santa Cruz','2016-05-17 02:42:06'),(4903,197,'SÃ¡ric','2016-05-17 02:42:06'),(4904,197,'Soyopa','2016-05-17 02:42:06'),(4905,197,'Suaqui Grande','2016-05-17 02:42:06'),(4906,197,'Tepache','2016-05-17 02:42:06'),(4907,197,'Trincheras','2016-05-17 02:42:06'),(4908,197,'Tubutama','2016-05-17 02:42:06'),(4909,197,'Ures','2016-05-17 02:42:06'),(4910,197,'Villa Hidalgo','2016-05-17 02:42:06'),(4911,197,'Villa Pesqueira','2016-05-17 02:42:06'),(4912,380,'BalancÃ¡n','2016-05-17 02:42:06'),(4913,380,'CÃ¡rdenas','2016-05-17 02:42:07'),(4914,380,'Centla','2016-05-17 02:42:07'),(4915,380,'Centro','2016-05-17 02:42:07'),(4916,380,'Comalcalco','2016-05-17 02:42:07'),(4917,380,'CunduacÃ¡n','2016-05-17 02:42:07'),(4918,380,'Emiliano Zapata','2016-05-17 02:42:07'),(4919,380,'Huimanguillo','2016-05-17 02:42:07'),(4920,380,'Jalapa','2016-05-17 02:42:07'),(4921,380,'Jalpa de MÃ©ndez','2016-05-17 02:42:07'),(4922,380,'Jonuta','2016-05-17 02:42:07'),(4923,380,'Macuspana','2016-05-17 02:42:07'),(4924,380,'Nacajuca','2016-05-17 02:42:07'),(4925,380,'ParaÃ­so','2016-05-17 02:42:08'),(4926,380,'Tacotalpa','2016-05-17 02:42:08'),(4927,380,'Teapa','2016-05-17 02:42:08'),(4928,380,'Tenosique','2016-05-17 02:42:08'),(4929,214,'Abasolo','2016-05-17 02:42:08'),(4930,214,'Aldama','2016-05-17 02:42:08'),(4931,214,'Altamira','2016-05-17 02:42:08'),(4932,214,'Antiguo Morelos','2016-05-17 02:42:08'),(4933,214,'Burgos','2016-05-17 02:42:08'),(4934,214,'Bustamante','2016-05-17 02:42:08'),(4935,214,'Camargo','2016-05-17 02:42:08'),(4936,214,'Casas','2016-05-17 02:42:08'),(4937,214,'Ciudad Madero','2016-05-17 02:42:08'),(4938,214,'Cruillas','2016-05-17 02:42:09'),(4939,214,'GÃ³mez FarÃ­as','2016-05-17 02:42:09'),(4940,214,'GonzÃ¡lez','2016-05-17 02:42:09'),(4941,214,'GÃ¼Ã©mez','2016-05-17 02:42:09'),(4942,214,'Guerrero','2016-05-17 02:42:09'),(4943,214,'Gustavo DÃ­az Ordaz','2016-05-17 02:42:09'),(4944,214,'Hidalgo','2016-05-17 02:42:09'),(4945,214,'Juamave','2016-05-17 02:42:09'),(4946,214,'JimÃ©nez','2016-05-17 02:42:09'),(4947,214,'Llera','2016-05-17 02:42:09'),(4948,214,'Mainero','2016-05-17 02:42:09'),(4949,214,'El Mante','2016-05-17 02:42:09'),(4950,214,'Matamoros','2016-05-17 02:42:10'),(4951,214,'MÃ©ndez','2016-05-17 02:42:10'),(4952,214,'Mier','2016-05-17 02:42:10'),(4953,214,'Miguel AlemÃ¡n','2016-05-17 02:42:10'),(4954,214,'Miquihuana','2016-05-17 02:42:10'),(4955,214,'Nuevo Laredo','2016-05-17 02:42:10'),(4956,214,'Nuevo Morelos','2016-05-17 02:42:10'),(4957,214,'Ocampo','2016-05-17 02:42:10'),(4958,214,'Padilla','2016-05-17 02:42:10'),(4959,214,'Palmillas','2016-05-17 02:42:10'),(4960,214,'Reynosa','2016-05-17 02:42:10'),(4961,214,'RÃ­o Bravo','2016-05-17 02:42:10'),(4962,214,'San Carlos','2016-05-17 02:42:10'),(4963,214,'San Fernando','2016-05-17 02:42:11'),(4964,214,'San NicolÃ¡s','2016-05-17 02:42:11'),(4965,214,'Soto la Marina','2016-05-17 02:42:11'),(4966,214,'Tampico','2016-05-17 02:42:11'),(4967,214,'Tula','2016-05-17 02:42:11'),(4968,214,'Valle Hermoso','2016-05-17 02:42:11'),(4969,214,'Victoria','2016-05-17 02:42:11'),(4970,214,'VillagrÃ¡n','2016-05-17 02:42:11'),(4971,214,'XicotÃ©ncatl','2016-05-17 02:42:11'),(4972,386,'Acuamanala de Miguel Hidalgo','2016-05-17 02:42:11'),(4973,386,'Altzayanca','2016-05-17 02:42:11'),(4974,386,'Amaxac de Guerrero','2016-05-17 02:42:11'),(4975,386,'ApetatitlÃ¡n de Antonio Carvajal','2016-05-17 02:42:12'),(4976,386,'Apizaco','2016-05-17 02:42:12'),(4977,386,'Atlangatepec','2016-05-17 02:42:12'),(4978,386,'Benito JuÃ¡rez','2016-05-17 02:42:12'),(4979,386,'Calpulalpan','2016-05-17 02:42:12'),(4980,386,'Chiautempan','2016-05-17 02:42:12'),(4981,386,'Contla de Juan Cuamatzi','2016-05-17 02:42:12'),(4982,386,'Cuapiaxtla','2016-05-17 02:42:12'),(4983,386,'Cuaxomulco','2016-05-17 02:42:12'),(4984,386,'El Carmen Tequexquitla','2016-05-17 02:42:12'),(4985,386,'Emiliano Zapata','2016-05-17 02:42:12'),(4986,386,'EspaÃ±ita','2016-05-17 02:42:12'),(4987,386,'Huamantla','2016-05-17 02:42:12'),(4988,386,'Hueyotlipan','2016-05-17 02:42:13'),(4989,386,'Ixtacuixtla de Mariano Matamoros','2016-05-17 02:42:13'),(4990,386,'Ixtenco','2016-05-17 02:42:13'),(4991,386,'La Magdalena Tlaltelulco','2016-05-17 02:42:13'),(4992,386,'LÃ¡zaro CÃ¡rdenas','2016-05-17 02:42:13'),(4993,386,'Mazatecochco de JosÃ© MarÃ­a Morelos','2016-05-17 02:42:13'),(4994,386,'MuÃ±oz de Domingo Arenas','2016-05-17 02:42:13'),(4995,386,'Nanacamilpa de Mariano Arista','2016-05-17 02:42:13'),(4996,386,'Nativitas','2016-05-17 02:42:13'),(4997,386,'Panotla','2016-05-17 02:42:13'),(4998,386,'Papalotla de Xicohtencatl','2016-05-17 02:42:13'),(4999,386,'Sanctorum de LÃ¡zaro CÃ¡rdenas','2016-05-17 02:42:13'),(5000,386,'San DamiÃ¡n Texoloc','2016-05-17 02:42:14'),(5001,386,'San Francisco Tetlanohcan','2016-05-17 02:42:14'),(5002,386,'San JerÃ³nimo Zacualpan','2016-05-17 02:42:14'),(5003,386,'San JosÃ© Teacalco','2016-05-17 02:42:14'),(5004,386,'San Juan Huactzinco','2016-05-17 02:42:14'),(5005,386,'San Lorenzo Axocomanitla','2016-05-17 02:42:14'),(5006,386,'San Lucas Tecopilco','2016-05-17 02:42:14'),(5007,386,'San Pablo del Monte','2016-05-17 02:42:14'),(5008,386,'Santa Ana Nopalucan','2016-05-17 02:42:14'),(5009,386,'Santa Apolonia Teacalco','2016-05-17 02:42:14'),(5010,386,'Santa Catarina Ayometla','2016-05-17 02:42:14'),(5011,386,'Santa Cruz Quilehtla','2016-05-17 02:42:14'),(5012,386,'Santa Cruz Tlaxcala','2016-05-17 02:42:14'),(5013,386,'Santa Isabel Xiloxoxtla','2016-05-17 02:42:15'),(5014,386,'Tenancingo','2016-05-17 02:42:15'),(5015,386,'Teolocholco','2016-05-17 02:42:15'),(5016,386,'Tepetitla de Lardizabal','2016-05-17 02:42:15'),(5017,386,'Tepeyanco','2016-05-17 02:42:15'),(5018,386,'Terrenate','2016-05-17 02:42:15'),(5019,386,'Tetla de la Solidaridad','2016-05-17 02:42:15'),(5020,386,'Tetlatlahuca','2016-05-17 02:42:15'),(5021,386,'Tlaxcala','2016-05-17 02:42:15'),(5022,386,'Tlaxco','2016-05-17 02:42:15'),(5023,386,'TocatlÃ¡n','2016-05-17 02:42:15'),(5024,386,'Totolac','2016-05-17 02:42:15'),(5025,386,'Tzompantepec','2016-05-17 02:42:16'),(5026,386,'Xaloztoc','2016-05-17 02:42:16'),(5027,386,'Xaltocan','2016-05-17 02:42:16'),(5028,386,'Xicohtzinco','2016-05-17 02:42:16'),(5029,386,'Yauhquemecan','2016-05-17 02:42:16'),(5030,386,'Zacatelco','2016-05-17 02:42:16'),(5031,386,'Zitlaltepec de Trinidad SÃ¡nchez Santos','2016-05-17 02:42:16'),(5032,397,'Acajete','2016-05-17 02:42:16'),(5033,397,'AcatlÃ¡n','2016-05-17 02:42:16'),(5034,397,'Acayucan','2016-05-17 02:42:16'),(5035,397,'Actopan','2016-05-17 02:42:16'),(5036,397,'Acula','2016-05-17 02:42:16'),(5037,397,'Acultzingo','2016-05-17 02:42:16'),(5038,397,'Agua Dulce','2016-05-17 02:42:17'),(5039,397,'Alpatlahuac','2016-05-17 02:42:17'),(5040,397,'Alto Lucero de GutiÃ©rrez Barrios','2016-05-17 02:42:17'),(5041,397,'Altotonga','2016-05-17 02:42:17'),(5042,397,'Alvarado','2016-05-17 02:42:17'),(5043,397,'AmatitlÃ¡n','2016-05-17 02:42:17'),(5044,397,'AmatlÃ¡n de los Reyes','2016-05-17 02:42:17'),(5045,397,'Ãngel R. Cabada','2016-05-17 02:42:17'),(5046,397,'Apazapan','2016-05-17 02:42:17'),(5047,397,'Aquila','2016-05-17 02:42:17'),(5048,397,'Astacinga','2016-05-17 02:42:17'),(5049,397,'Atlahuilco','2016-05-17 02:42:17'),(5050,397,'Atoyac','2016-05-17 02:42:17'),(5051,397,'Atzacan','2016-05-17 02:42:18'),(5052,397,'AtzalÃ¡n','2016-05-17 02:42:18'),(5053,397,'Ayahualulco','2016-05-17 02:42:18'),(5054,397,'Banderilla','2016-05-17 02:42:18'),(5055,397,'Benito JuÃ¡rez','2016-05-17 02:42:18'),(5056,397,'Boca del RÃ­o','2016-05-17 02:42:18'),(5057,397,'Calcahualco','2016-05-17 02:42:18'),(5058,397,'CamarÃ³n de Tejeda','2016-05-17 02:42:18'),(5059,397,'Camerino Z. Mendoza','2016-05-17 02:42:18'),(5060,397,'Carlos A. Carrillo','2016-05-17 02:42:18'),(5061,397,'Carrillo Puerto','2016-05-17 02:42:18'),(5062,397,'Castillo de Teayo','2016-05-17 02:42:18'),(5063,397,'Catemaco','2016-05-17 02:42:18'),(5064,397,'Cazones','2016-05-17 02:42:19'),(5065,397,'Cerro Azul','2016-05-17 02:42:19'),(5066,397,'Chacaltianguis','2016-05-17 02:42:19'),(5067,397,'Chalma','2016-05-17 02:42:19'),(5068,397,'Chiconamel','2016-05-17 02:42:19'),(5069,397,'Chiconquiaco','2016-05-17 02:42:19'),(5070,397,'Chicontepec','2016-05-17 02:42:19'),(5071,397,'Chinameca','2016-05-17 02:42:19'),(5072,397,'Chinampa de Gorostiza','2016-05-17 02:42:19'),(5073,397,'Chocaman','2016-05-17 02:42:19'),(5074,397,'Chontla','2016-05-17 02:42:19'),(5075,397,'Chumatlan','2016-05-17 02:42:19'),(5076,397,'Citlaltepetl','2016-05-17 02:42:20'),(5077,397,'Coacoatzintla','2016-05-17 02:42:20'),(5078,397,'Coahuitlan','2016-05-17 02:42:20'),(5079,397,'Coatepec','2016-05-17 02:42:20'),(5080,397,'Coatzacoalcos','2016-05-17 02:42:20'),(5081,397,'Coatzintla','2016-05-17 02:42:20'),(5082,397,'Coetzala','2016-05-17 02:42:20'),(5083,397,'Colipa','2016-05-17 02:42:20'),(5084,397,'Comapa','2016-05-17 02:42:20'),(5085,397,'CÃ³rdoba','2016-05-17 02:42:20'),(5086,397,'Cosamaloapan de Carpio','2016-05-17 02:42:20'),(5087,397,'CosautlÃ¡n de Carvajal','2016-05-17 02:42:20'),(5088,397,'Coscomatepec','2016-05-17 02:42:20'),(5089,397,'Cosoleacaque','2016-05-17 02:42:21'),(5090,397,'Cotaxtla','2016-05-17 02:42:21'),(5091,397,'Coxquihui','2016-05-17 02:42:21'),(5092,397,'Coyutla','2016-05-17 02:42:21'),(5093,397,'Cuichapa','2016-05-17 02:42:21'),(5094,397,'CuitlÃ¡huac','2016-05-17 02:42:21'),(5095,397,'El Higo','2016-05-17 02:42:21'),(5096,397,'Emiliano Zapata','2016-05-17 02:42:21'),(5097,397,'Espinal','2016-05-17 02:42:21'),(5098,397,'Filomeno Mata','2016-05-17 02:42:21'),(5099,397,'FortÃ­n','2016-05-17 02:42:21'),(5100,397,'GutiÃ©rrez Zamora','2016-05-17 02:42:21'),(5101,397,'HidalgotitlÃ¡n','2016-05-17 02:42:22'),(5102,397,'Huatusco','2016-05-17 02:42:22'),(5103,397,'Huayacocotla','2016-05-17 02:42:22'),(5104,397,'Hueyapan de Ocampo','2016-05-17 02:42:22'),(5105,397,'Huiloapan','2016-05-17 02:42:22'),(5106,397,'Ignacio de la Llave','2016-05-17 02:42:22'),(5107,397,'IlamatlÃ¡n','2016-05-17 02:42:22'),(5108,397,'Isla','2016-05-17 02:42:22'),(5109,397,'Ixcatepec','2016-05-17 02:42:22'),(5110,397,'IxhuacÃ¡n de los Reyes','2016-05-17 02:42:22'),(5111,397,'Ixhuatlancillo','2016-05-17 02:42:22'),(5112,397,'IxhuatlÃ¡n del CafÃ©','2016-05-17 02:42:22'),(5113,397,'IxhuatlÃ¡n del Sureste','2016-05-17 02:42:22'),(5114,397,'IxhuatlÃ¡n de Madero','2016-05-17 02:42:23'),(5115,397,'Ixmatlahuacan','2016-05-17 02:42:23'),(5116,397,'IxtaczoquitlÃ¡n','2016-05-17 02:42:23'),(5117,397,'Jalacingo','2016-05-17 02:42:23'),(5118,397,'Jalcomulco','2016-05-17 02:42:23'),(5119,397,'Jaltipan','2016-05-17 02:42:23'),(5120,397,'Jamapa','2016-05-17 02:42:23'),(5121,397,'JesÃºs Carranza','2016-05-17 02:42:23'),(5122,397,'Jilotepec','2016-05-17 02:42:23'),(5123,397,'JosÃ© Azueta','2016-05-17 02:42:23'),(5124,397,'Juan RodrÃ­guez Clara','2016-05-17 02:42:23'),(5125,397,'Juchique de Ferrer','2016-05-17 02:42:23'),(5126,397,'Landero y Coss','2016-05-17 02:42:23'),(5127,397,'La Antigua','2016-05-17 02:42:24'),(5128,397,'La Perla','2016-05-17 02:42:24'),(5129,397,'Las Choapas','2016-05-17 02:42:24'),(5130,397,'Las Minas','2016-05-17 02:42:24'),(5131,397,'Las Vigas de RamÃ­rez','2016-05-17 02:42:24'),(5132,397,'Lerdo de Tejada','2016-05-17 02:42:24'),(5133,397,'Los Reyes','2016-05-17 02:42:24'),(5134,397,'Magdalena','2016-05-17 02:42:24'),(5135,397,'Maltrata','2016-05-17 02:42:24'),(5136,397,'Manlio Fabio Altamirano','2016-05-17 02:42:24'),(5137,397,'Mariano Escobedo','2016-05-17 02:42:24'),(5138,397,'MartÃ­nez de la Torre','2016-05-17 02:42:24'),(5139,397,'MecatlÃ¡n','2016-05-17 02:42:25'),(5140,397,'Mecayapan','2016-05-17 02:42:25'),(5141,397,'MedellÃ­n','2016-05-17 02:42:25'),(5142,397,'MiahuatlÃ¡n','2016-05-17 02:42:25'),(5143,397,'MinatitlÃ¡n','2016-05-17 02:42:25'),(5144,397,'Misantla','2016-05-17 02:42:25'),(5145,397,'Mixtla de Altamirano','2016-05-17 02:42:25'),(5146,397,'MoloacÃ¡n','2016-05-17 02:42:25'),(5147,397,'Nanchital','2016-05-17 02:42:25'),(5148,397,'Naolinco','2016-05-17 02:42:25'),(5149,397,'Naranjal','2016-05-17 02:42:25'),(5150,397,'Naranjos AmatlÃ¡n','2016-05-17 02:42:25'),(5151,397,'Nautla','2016-05-17 02:42:25'),(5152,397,'Nogales','2016-05-17 02:42:26'),(5153,397,'Oluta','2016-05-17 02:42:26'),(5154,397,'Omealca','2016-05-17 02:42:26'),(5155,397,'Orizaba','2016-05-17 02:42:26'),(5156,397,'OtatitlÃ¡n','2016-05-17 02:42:26'),(5157,397,'Oteapan','2016-05-17 02:42:26'),(5158,397,'Ozuluama de MascareÃ±as','2016-05-17 02:42:26'),(5159,397,'Pajapan','2016-05-17 02:42:26'),(5160,397,'PÃ¡nuco','2016-05-17 02:42:26'),(5161,397,'Papantla','2016-05-17 02:42:26'),(5162,397,'Paso de Ovejas','2016-05-17 02:42:26'),(5163,397,'Paso del Macho','2016-05-17 02:42:26'),(5164,397,'Perote','2016-05-17 02:42:27'),(5165,397,'PlatÃ³n SÃ¡nchez','2016-05-17 02:42:27'),(5166,397,'Playa Vicente','2016-05-17 02:42:27'),(5167,397,'Poza Rica de Hidalgo','2016-05-17 02:42:27'),(5168,397,'Pueblo Viejo','2016-05-17 02:42:27'),(5169,397,'Puente Nacional','2016-05-17 02:42:27'),(5170,397,'Rafael Delgado','2016-05-17 02:42:27'),(5171,397,'Rafael Lucio','2016-05-17 02:42:27'),(5172,397,'RÃ­o Blanco','2016-05-17 02:42:27'),(5173,397,'Saltabarranca','2016-05-17 02:42:27'),(5174,397,'San AndrÃ©s Tenejapan','2016-05-17 02:42:27'),(5175,397,'San AndrÃ©s Tuxtla','2016-05-17 02:42:27'),(5176,397,'San Juan Evangelista','2016-05-17 02:42:27'),(5177,397,'Santiago Tuxtla','2016-05-17 02:42:28'),(5178,397,'Sayula de AlemÃ¡n','2016-05-17 02:42:28'),(5179,397,'Sochiapa','2016-05-17 02:42:28'),(5180,397,'Soconusco','2016-05-17 02:42:28'),(5181,397,'Soledad Atzompa','2016-05-17 02:42:28'),(5182,397,'Soledad de Doblado','2016-05-17 02:42:28'),(5183,397,'Soteapan','2016-05-17 02:42:28'),(5184,397,'TamalÃ­n','2016-05-17 02:42:28'),(5185,397,'Tamiahua','2016-05-17 02:42:28'),(5186,397,'Tampico Alto','2016-05-17 02:42:28'),(5187,397,'Tancoco','2016-05-17 02:42:28'),(5188,397,'Tantima','2016-05-17 02:42:29'),(5189,397,'Tantoyuca','2016-05-17 02:42:29'),(5190,397,'Tatahuicapan de JuÃ¡rez','2016-05-17 02:42:29'),(5191,397,'Tatatila','2016-05-17 02:42:29'),(5192,397,'Tecolutla','2016-05-17 02:42:29'),(5193,397,'Tehuipango','2016-05-17 02:42:29'),(5194,397,'Temapache','2016-05-17 02:42:29'),(5195,397,'Tempoal','2016-05-17 02:42:29'),(5196,397,'Tenampa','2016-05-17 02:42:29'),(5197,397,'TenochtitlÃ¡n','2016-05-17 02:42:29'),(5198,397,'Teocelo','2016-05-17 02:42:29'),(5199,397,'Tepatlaxco','2016-05-17 02:42:29'),(5200,397,'TepetlÃ¡n','2016-05-17 02:42:29'),(5201,397,'Tepetzintla','2016-05-17 02:42:30'),(5202,397,'Tequila','2016-05-17 02:42:30'),(5203,397,'Texcatepec','2016-05-17 02:42:30'),(5204,397,'TexhuacÃ¡n','2016-05-17 02:42:30'),(5205,397,'Texistepec','2016-05-17 02:42:30'),(5206,397,'Tezonapa','2016-05-17 02:42:30'),(5207,397,'Tierra Blanca','2016-05-17 02:42:30'),(5208,397,'TihuatlÃ¡n','2016-05-17 02:42:30'),(5209,397,'Tlachichilco','2016-05-17 02:42:30'),(5210,397,'Tlacojalpan','2016-05-17 02:42:30'),(5211,397,'Tlacolulan','2016-05-17 02:42:30'),(5212,397,'Tlacotalpan','2016-05-17 02:42:30'),(5213,397,'Tlacotepec de MejÃ­a','2016-05-17 02:42:30'),(5214,397,'Tlalixcoyan','2016-05-17 02:42:31'),(5215,397,'Tlalnelhuayocan','2016-05-17 02:42:31'),(5216,397,'Tlaltetela','2016-05-17 02:42:31'),(5217,397,'Tlapacoyan','2016-05-17 02:42:31'),(5218,397,'Tlaquilpa','2016-05-17 02:42:31'),(5219,397,'Tlilapan','2016-05-17 02:42:31'),(5220,397,'TomatlÃ¡n','2016-05-17 02:42:31'),(5221,397,'Tonayan','2016-05-17 02:42:31'),(5222,397,'Totutla','2016-05-17 02:42:31'),(5223,397,'Tres Valles','2016-05-17 02:42:31'),(5224,397,'Tuxpam','2016-05-17 02:42:31'),(5225,397,'Tuxtilla','2016-05-17 02:42:31'),(5226,397,'Ãšrsulo GalvÃ¡n','2016-05-17 02:42:32'),(5227,397,'Uxpanapa','2016-05-17 02:42:32'),(5228,397,'Vega de Alatorre','2016-05-17 02:42:32'),(5229,397,'Veracruz','2016-05-17 02:42:32'),(5230,397,'Villa Aldama','2016-05-17 02:42:32'),(5231,397,'Xalapa','2016-05-17 02:42:32'),(5232,397,'Xico','2016-05-17 02:42:32'),(5233,397,'Xoxocotla','2016-05-17 02:42:32'),(5234,397,'Yanga','2016-05-17 02:42:32'),(5235,397,'Yecuatla','2016-05-17 02:42:32'),(5236,397,'Zacualpan','2016-05-17 02:42:32'),(5237,397,'Zaragoza','2016-05-17 02:42:32'),(5238,397,'Zentla','2016-05-17 02:42:33'),(5239,397,'Zongolica','2016-05-17 02:42:33'),(5240,397,'ZontecomatlÃ¡n de LÃ³pez y Fuentes','2016-05-17 02:42:33'),(5241,397,'Zozocolco de Hidalgo','2016-05-17 02:42:33'),(5242,397,'San Rafael','2016-05-17 02:42:33'),(5243,397,'Santiago Sochiapan','2016-05-17 02:42:33'),(5244,207,'Apozol','2016-05-17 02:42:33'),(5245,207,'Apulco','2016-05-17 02:42:33'),(5246,207,'Atolinga','2016-05-17 02:42:33'),(5247,207,'Florencia de Benito JuÃ¡rez','2016-05-17 02:42:33'),(5248,207,'Calera de VÃ­ctor Rosales','2016-05-17 02:42:33'),(5249,207,'CaÃ±itas de Felipe Pescador','2016-05-17 02:42:33'),(5250,207,'ConcepciÃ³n del Oro','2016-05-17 02:42:34'),(5251,207,'CuauhtÃ©moc','2016-05-17 02:42:34'),(5252,207,'Chalchihuites','2016-05-17 02:42:34'),(5253,207,'El Plateado de JoaquÃ­n Amaro','2016-05-17 02:42:34'),(5254,207,'El Salvador','2016-05-17 02:42:34'),(5255,207,'Fresnillo','2016-05-17 02:42:34'),(5256,207,'Genaro Codina','2016-05-17 02:42:34'),(5257,207,'General Enrique Estrada','2016-05-17 02:42:34'),(5258,207,'General Francisco R MurguÃ­a','2016-05-17 02:42:34'),(5259,207,'General PÃ¡nfilo Natera','2016-05-17 02:42:34'),(5260,207,'Guadalupe','2016-05-17 02:42:34'),(5261,207,'Huanusco','2016-05-17 02:42:34'),(5262,207,'Jalpa','2016-05-17 02:42:35'),(5263,207,'Jerez de GarcÃ­a Salinas','2016-05-17 02:42:35'),(5264,207,'JimÃ©nez del Teul','2016-05-17 02:42:35'),(5265,207,'Juan Aldama','2016-05-17 02:42:35'),(5266,207,'Juchipila','2016-05-17 02:42:35'),(5267,207,'Loreto','2016-05-17 02:42:35'),(5268,207,'Luis Moya','2016-05-17 02:42:35'),(5269,207,'Mazapil','2016-05-17 02:42:35'),(5270,207,'Melchor Ocampo','2016-05-17 02:42:35'),(5271,207,'Mezquital del Oro','2016-05-17 02:42:35'),(5272,207,'Miguel Auza','2016-05-17 02:42:35'),(5273,207,'Momax','2016-05-17 02:42:35'),(5274,207,'Monte Escobedo','2016-05-17 02:42:36'),(5275,207,'Morelos','2016-05-17 02:42:36'),(5276,207,'Moyahua de Estrada','2016-05-17 02:42:36'),(5277,207,'NochistlÃ¡n de MejÃ­a','2016-05-17 02:42:36'),(5278,207,'Noria de Ãngeles','2016-05-17 02:42:36'),(5279,207,'Ojocaliente','2016-05-17 02:42:36'),(5280,207,'PÃ¡nuco','2016-05-17 02:42:36'),(5281,207,'Pinos','2016-05-17 02:42:36'),(5282,207,'RÃ­o Grande','2016-05-17 02:42:36'),(5283,207,'Santa MarÃ­a de la Paz','2016-05-17 02:42:36'),(5289,207,'SusticacÃ¡n','2016-05-17 03:27:43'),(5316,207,'Sombrerete','2016-05-17 03:30:04'),(5317,207,'Tabasco','2016-05-17 03:30:09'),(5318,207,'TepechitlÃ¡n','2016-05-17 03:30:09'),(5319,207,'Tepetongo','2016-05-17 03:30:09'),(5320,207,'TeÃºl de GonzÃ¡lez Ortega','2016-05-17 03:30:09'),(5321,207,'Tlaltenango de SÃ¡nchez RomÃ¡n','2016-05-17 03:30:09'),(5322,207,'Valparaiso','2016-05-17 03:30:09'),(5323,207,'Trinidad GarcÃ­a de la Cadena','2016-05-17 03:30:10'),(5325,207,'Vetagrande','2016-05-17 03:30:27'),(5326,207,'Villa de Cos','2016-05-17 03:30:32'),(5327,207,'Villa GarcÃ­a','2016-05-17 03:30:33'),(5328,207,'Villa GonzÃ¡lez Ortega','2016-05-17 03:30:33'),(5329,207,'Villa Hidalgo','2016-05-17 03:30:33'),(5330,207,'Villanueva','2016-05-17 03:30:33'),(5331,207,'Zacatecas','2016-05-17 03:30:33'),(5332,328,'Acambay','2016-05-17 03:57:54'),(5333,328,'Acolman','2016-05-17 03:57:54'),(5334,328,'Aculco','2016-05-17 03:57:54'),(5335,328,'Almoloya de Alquisiras','2016-05-17 03:57:55'),(5336,328,'Almoloya de JuÃ¡rez','2016-05-17 03:57:55'),(5337,328,'Almoloya del RÃ­o','2016-05-17 03:57:55'),(5338,328,'Amanalco','2016-05-17 03:57:55'),(5339,328,'Amatepec','2016-05-17 03:57:55'),(5340,328,'Amecameca','2016-05-17 03:57:55'),(5341,328,'Apaxco','2016-05-17 03:57:55'),(5342,328,'Atenco','2016-05-17 03:57:56'),(5343,328,'AtizapÃ¡n','2016-05-17 03:57:56'),(5344,328,'AtizapÃ¡n de Zaragoza','2016-05-17 03:57:56'),(5345,328,'Atlacomulco','2016-05-17 03:57:56'),(5346,328,'Atlautla','2016-05-17 03:57:56'),(5347,328,'Axapusco','2016-05-17 03:57:56'),(5348,328,'Ayapango','2016-05-17 03:57:56'),(5349,328,'Calimaya','2016-05-17 03:57:56'),(5350,328,'Capulhuac','2016-05-17 03:57:57'),(5351,328,'Coacalco de BerriozÃ¡bal','2016-05-17 03:57:57'),(5352,328,'Coatepec Harinas','2016-05-17 03:57:57'),(5353,328,'CocotitlÃ¡n','2016-05-17 03:57:57'),(5354,328,'Coyotepec','2016-05-17 03:57:57'),(5355,328,'CuautitlÃ¡n','2016-05-17 03:57:57'),(5356,328,'Chalco','2016-05-17 03:57:57'),(5357,328,'Chapa de Mota','2016-05-17 03:57:57'),(5358,328,'Chapultepec','2016-05-17 03:57:58'),(5359,328,'Chiautla','2016-05-17 03:57:58'),(5360,328,'Chicoloapan','2016-05-17 03:57:58'),(5361,328,'Chiconcuac','2016-05-17 03:57:58'),(5362,328,'ChimalhuacÃ¡n','2016-05-17 03:57:58'),(5363,328,'Donato Guerra','2016-05-17 03:57:58'),(5364,328,'Ecatepec de Morelos','2016-05-17 03:57:58'),(5365,328,'Ecatzingo','2016-05-17 03:57:59'),(5366,328,'Huehuetoca','2016-05-17 03:57:59'),(5367,328,'Hueypoxtla','2016-05-17 03:57:59'),(5368,328,'Huixquilucan','2016-05-17 03:57:59'),(5369,328,'Isidro Fabela','2016-05-17 03:57:59'),(5370,328,'Ixtapaluca','2016-05-17 03:57:59'),(5371,328,'Ixtapan de la Sal','2016-05-17 03:57:59'),(5372,328,'Ixtapan del Oro','2016-05-17 03:57:59'),(5373,328,'Ixtlahuaca','2016-05-17 03:58:00'),(5374,328,'Xalatlaco','2016-05-17 03:58:00'),(5375,328,'Jaltenco','2016-05-17 03:58:00'),(5376,328,'Jilotepec','2016-05-17 03:58:00'),(5377,328,'Jilotzingo','2016-05-17 03:58:00'),(5378,328,'Jiquipilco','2016-05-17 03:58:00'),(5379,328,'JocotitlÃ¡n','2016-05-17 03:58:00'),(5380,328,'Joquicingo','2016-05-17 03:58:00'),(5381,328,'Juchitepec','2016-05-17 03:58:01'),(5382,328,'Lerma','2016-05-17 03:58:01'),(5383,328,'Malinalco','2016-05-17 03:58:01'),(5384,328,'Melchor Ocampo','2016-05-17 03:58:01'),(5385,328,'Metepec','2016-05-17 03:58:01'),(5386,328,'Mexicaltzingo','2016-05-17 03:58:01'),(5387,328,'Morelos','2016-05-17 03:58:01'),(5388,328,'Naucalpan','2016-05-17 03:58:01'),(5389,328,'NezahualcÃ³yotl','2016-05-17 03:58:02'),(5390,328,'Nextlalpan','2016-05-17 03:58:02'),(5391,328,'NicolÃ¡s Romero','2016-05-17 03:58:02'),(5392,328,'Nopaltepec','2016-05-17 03:58:02'),(5393,328,'Ocoyoacac','2016-05-17 03:58:02'),(5394,328,'OcuilÃ¡n','2016-05-17 03:58:02'),(5395,328,'El Oro','2016-05-17 03:58:02'),(5396,328,'Otumba','2016-05-17 03:58:02'),(5397,328,'Otzoloapan','2016-05-17 03:58:03'),(5398,328,'Otzolotepec','2016-05-17 03:58:03'),(5399,328,'Ozumba','2016-05-17 03:58:03'),(5400,328,'Papalotla','2016-05-17 03:58:03'),(5401,328,'La Paz','2016-05-17 03:58:03'),(5402,328,'PolotitlÃ¡n','2016-05-17 03:58:03'),(5403,328,'RayÃ³n','2016-05-17 03:58:03'),(5404,328,'San Antonio la Isla','2016-05-17 03:58:04'),(5405,328,'San Felipe del Progreso','2016-05-17 03:58:04'),(5406,328,'San MartÃ­n de las PirÃ¡mides','2016-05-17 03:58:04'),(5407,328,'San Mateo Atenco','2016-05-17 03:58:04'),(5408,328,'San SimÃ³n de Guerrero','2016-05-17 03:58:04'),(5409,328,'Santo TomÃ¡s','2016-05-17 03:58:04'),(5410,328,'Soyaniquilpan de JuÃ¡rez','2016-05-17 03:58:04'),(5411,328,'Sultepec','2016-05-17 03:58:04'),(5412,328,'TecÃ¡mac','2016-05-17 03:58:05'),(5413,328,'Tejupilco','2016-05-17 03:58:05'),(5414,328,'Temamatla','2016-05-17 03:58:05'),(5415,328,'Temascalapa','2016-05-17 03:58:05'),(5416,328,'Temascalcingo','2016-05-17 03:58:05'),(5417,328,'Temascaltepec','2016-05-17 03:58:05'),(5418,328,'Temoaya','2016-05-17 03:58:05'),(5419,328,'Tenancingo','2016-05-17 03:58:05'),(5420,328,'Tenango del Aire','2016-05-17 03:58:06'),(5421,328,'Tenango del Valle','2016-05-17 03:58:06'),(5422,328,'Teoloyucan','2016-05-17 03:58:06'),(5423,328,'TeotihuacÃ¡n','2016-05-17 03:58:06'),(5424,328,'Tepetlaoxtoc','2016-05-17 03:58:06'),(5425,328,'Tepetlixpa','2016-05-17 03:58:06'),(5426,328,'TepotzotlÃ¡n','2016-05-17 03:58:06'),(5427,328,'Tequixquiac','2016-05-17 03:58:07'),(5428,328,'TexcaltitlÃ¡n','2016-05-17 03:58:07'),(5429,328,'Texcalyacac','2016-05-17 03:58:07'),(5430,328,'Texcoco','2016-05-17 03:58:07'),(5431,328,'Tezoyuca','2016-05-17 03:58:07'),(5432,328,'Tianguistenco','2016-05-17 03:58:07'),(5433,328,'Timilpan','2016-05-17 03:58:07'),(5434,328,'Tlalmanalco','2016-05-17 03:58:07'),(5435,328,'Tlalnepantla de Baz','2016-05-17 03:58:08'),(5436,328,'Tlatlaya','2016-05-17 03:58:08'),(5437,328,'Toluca','2016-05-17 03:58:08'),(5438,328,'Tonatico','2016-05-17 03:58:08'),(5439,328,'Tultepec','2016-05-17 03:58:08'),(5440,328,'TultitlÃ¡n','2016-05-17 03:58:08'),(5441,328,'Valle de Bravo','2016-05-17 03:58:08'),(5442,328,'Villa de Allende','2016-05-17 03:58:09'),(5443,328,'Villa del CarbÃ³n','2016-05-17 03:58:09'),(5444,328,'Villa Guerrero','2016-05-17 03:58:09'),(5445,328,'Villa Victoria','2016-05-17 03:58:09'),(5446,328,'XonacatlÃ¡n','2016-05-17 03:58:09'),(5447,328,'Zacazonapan','2016-05-17 03:58:09'),(5448,328,'Zacualpan, State of Mexico','2016-05-17 03:58:09'),(5449,328,'Zinacantepec','2016-05-17 03:58:09'),(5450,328,'ZumpahuacÃ¡n','2016-05-17 03:58:10'),(5451,328,'Zumpango','2016-05-17 03:58:10'),(5452,328,'CuautitlÃ¡n Izcalli','2016-05-17 03:58:10'),(5453,328,'Valle de Chalco Solidaridad','2016-05-17 03:58:10'),(5454,328,'Luvianos','2016-05-17 03:58:10'),(5455,328,'San JosÃ© del RincÃ³n','2016-05-17 03:58:10'),(5456,328,'Tonanitla','2016-05-17 03:58:10'),(5457,329,'Acuitzio','2016-05-17 03:58:11'),(5458,329,'Aguililla','2016-05-17 03:58:11'),(5459,329,'Ãlvaro ObregÃ³n','2016-05-17 03:58:11'),(5460,329,'Angamacutiro','2016-05-17 03:58:11'),(5461,329,'Angangueo','2016-05-17 03:58:11'),(5462,329,'ApatzingÃ¡n','2016-05-17 03:58:11'),(5463,329,'Aporo','2016-05-17 03:58:11'),(5464,329,'Aquila','2016-05-17 03:58:12'),(5465,329,'Ario','2016-05-17 03:58:12'),(5466,329,'Arteaga','2016-05-17 03:58:12'),(5467,329,'BriseÃ±as','2016-05-17 03:58:12'),(5468,329,'Buenavista','2016-05-17 03:58:12'),(5469,329,'Caracuaro','2016-05-17 03:58:12'),(5470,329,'Charapan','2016-05-17 03:58:12'),(5471,329,'Charo','2016-05-17 03:58:12'),(5472,329,'Chavinda','2016-05-17 03:58:13'),(5473,329,'CherÃ¡n','2016-05-17 03:58:13'),(5474,329,'Chilchota','2016-05-17 03:58:13'),(5475,329,'Chinicuila','2016-05-17 03:58:13'),(5476,329,'ChucÃ¡ndiro','2016-05-17 03:58:13'),(5477,329,'Churintzio','2016-05-17 03:58:13'),(5478,329,'Churumuco','2016-05-17 03:58:13'),(5479,329,'Coahuayana','2016-05-17 03:58:13'),(5480,329,'CoalcomÃ¡n de VÃ¡zquez Pallares','2016-05-17 03:58:14'),(5481,329,'Coeneo','2016-05-17 03:58:14'),(5482,329,'CojumatlÃ¡n de RÃ©gules','2016-05-17 03:58:14'),(5483,329,'Contepec','2016-05-17 03:58:14'),(5484,329,'CopÃ¡ndaro','2016-05-17 03:58:14'),(5485,329,'Cotija','2016-05-17 03:58:14'),(5486,329,'Cuitzeo','2016-05-17 03:58:14'),(5487,329,'Ecuandureo','2016-05-17 03:58:14'),(5488,329,'EpitÃ¡cio Huerta','2016-05-17 03:58:15'),(5489,329,'Erongaricuaro','2016-05-17 03:58:15'),(5490,329,'Gabriel Zamora','2016-05-17 03:58:15'),(5491,329,'Hidalgo','2016-05-17 03:58:15'),(5492,329,'La Huacana','2016-05-17 03:58:15'),(5493,329,'Huandacareo','2016-05-17 03:58:15'),(5494,329,'Huaniqueo','2016-05-17 03:58:15'),(5495,329,'Huetamo','2016-05-17 03:58:15'),(5496,329,'Huiramba','2016-05-17 03:58:16'),(5497,329,'Indaparapeo','2016-05-17 03:58:16'),(5498,329,'Irimbo','2016-05-17 03:58:16'),(5499,329,'IxtlÃ¡n','2016-05-17 03:58:16'),(5500,329,'Jacona','2016-05-17 03:58:16'),(5501,329,'JimÃ©nez','2016-05-17 03:58:16'),(5502,329,'Jiquilpan','2016-05-17 03:58:16'),(5503,329,'JosÃ© Sixto Verduzco','2016-05-17 03:58:16'),(5504,329,'JuÃ¡rez','2016-05-17 03:58:17'),(5505,329,'Jungapeo','2016-05-17 03:58:17'),(5506,329,'Lagunillas','2016-05-17 03:58:17'),(5507,329,'La Piedad','2016-05-17 03:58:17'),(5508,329,'LÃ¡zaro CÃ¡rdenas','2016-05-17 03:58:17'),(5509,329,'Los Reyes','2016-05-17 03:58:17'),(5510,329,'Madero','2016-05-17 03:58:17'),(5511,329,'MaravatÃ­o','2016-05-17 03:58:17'),(5512,329,'Marcos','2016-05-17 03:58:18'),(5513,329,'Morelia','2016-05-17 03:58:18'),(5514,329,'Morelos','2016-05-17 03:58:18'),(5515,329,'MÃºgica','2016-05-17 03:58:18'),(5516,329,'NahuatzÃ©n','2016-05-17 03:58:18'),(5517,329,'NocupÃ©taro','2016-05-17 03:58:18'),(5518,329,'Nuevo Parangaricutiro','2016-05-17 03:58:18'),(5519,329,'Nuevo Urecho','2016-05-17 03:58:18'),(5520,329,'NumarÃ¡n','2016-05-17 03:58:18'),(5521,329,'Ocampo','2016-05-17 03:58:19'),(5522,329,'PajacuarÃ¡n','2016-05-17 03:58:19'),(5523,329,'Panindicuaro','2016-05-17 03:58:19'),(5524,329,'Paracho','2016-05-17 03:58:19'),(5525,329,'ParÃ¡cuaro','2016-05-17 03:58:19'),(5526,329,'PÃ¡tzcuaro','2016-05-17 03:58:19'),(5527,329,'Penjamillo','2016-05-17 03:58:19'),(5528,329,'PeribÃ¡n','2016-05-17 03:58:19'),(5529,329,'PurÃ©pero','2016-05-17 03:58:20'),(5530,329,'PuruÃ¡ndiro','2016-05-17 03:58:20'),(5531,329,'QuerÃ©ndaro','2016-05-17 03:58:20'),(5532,329,'Quiroga','2016-05-17 03:58:20'),(5533,329,'Sahuayo','2016-05-17 03:58:20'),(5534,329,'Salvador Escalante','2016-05-17 03:58:20'),(5535,329,'San Lucas','2016-05-17 03:58:20'),(5536,329,'Santa Ana Maya','2016-05-17 03:58:20'),(5537,329,'SenguÃ­o','2016-05-17 03:58:21'),(5538,329,'Susupuato','2016-05-17 03:58:21'),(5539,329,'TacÃ¡mbaro','2016-05-17 03:58:21'),(5540,329,'TancÃ­taro','2016-05-17 03:58:21'),(5541,329,'Tangamandapio','2016-05-17 03:58:21'),(5542,329,'TangancÃ­cuaro','2016-05-17 03:58:21'),(5543,329,'Tanhuato','2016-05-17 03:58:21'),(5544,329,'Taretan','2016-05-17 03:58:21'),(5545,329,'TarÃ­mbaro','2016-05-17 03:58:22'),(5546,329,'Tepalcatepec','2016-05-17 03:58:22'),(5547,329,'Tingambato','2016-05-17 03:58:22'),(5548,329,'TingÃ¼indÃ­n','2016-05-17 03:58:22'),(5549,329,'Tiquicheo de Nicolas Romero','2016-05-17 03:58:22'),(5550,329,'Tlalpujahua','2016-05-17 03:58:22'),(5551,329,'Tlazazalca','2016-05-17 03:58:22'),(5552,329,'Tocumbo','2016-05-17 03:58:22'),(5553,329,'TumbiscatÃ­o','2016-05-17 03:58:23'),(5554,329,'Turicato','2016-05-17 03:58:23'),(5555,329,'Tuxpan','2016-05-17 03:58:23'),(5556,329,'Tuzantla','2016-05-17 03:58:23'),(5557,329,'Tzintzuntzan','2016-05-17 03:58:23'),(5558,329,'TzitzÃ­o','2016-05-17 03:58:23'),(5559,329,'Uruapan','2016-05-17 03:58:23'),(5560,329,'Venustiano Carranza','2016-05-17 03:58:23'),(5561,329,'Villamar','2016-05-17 03:58:23'),(5562,329,'Vista Hermosa','2016-05-17 03:58:24'),(5563,329,'YurÃ©cuaro','2016-05-17 03:58:24'),(5564,329,'ZacapÃº','2016-05-17 03:58:24'),(5565,329,'Zamora','2016-05-17 03:58:24'),(5566,329,'ZinÃ¡paro','2016-05-17 03:58:24'),(5567,329,'ZinapÃ©cuaro','2016-05-17 03:58:24'),(5568,329,'Ziracuaretiro','2016-05-17 03:58:24'),(5569,329,'ZitÃ¡cuaro','2016-05-17 03:58:24'),(5570,208,'Abasolo','2016-05-17 03:58:25'),(5571,208,'Agualeguas','2016-05-17 03:58:25'),(5572,208,'Allende','2016-05-17 03:58:25'),(5573,208,'AnÃ¡huac','2016-05-17 03:58:25'),(5574,208,'Apodaca','2016-05-17 03:58:25'),(5575,208,'Aramberri','2016-05-17 03:58:25'),(5576,208,'Bustamante','2016-05-17 03:58:25'),(5577,208,'Cadereyta JimÃ©nez','2016-05-17 03:58:25'),(5578,208,'El Carmen','2016-05-17 03:58:25'),(5579,208,'Cerralvo','2016-05-17 03:58:26'),(5580,208,'China','2016-05-17 03:58:26'),(5581,208,'CiÃ©nega de Flores','2016-05-17 03:58:26'),(5582,208,'Doctor Arroyo','2016-05-17 03:58:26'),(5583,208,'Doctor Coss','2016-05-17 03:58:26'),(5584,208,'Doctor GonzÃ¡lez','2016-05-17 03:58:26'),(5585,208,'Galeana','2016-05-17 03:58:26'),(5586,208,'GarcÃ­a','2016-05-17 03:58:26'),(5587,208,'General Bravo','2016-05-17 03:58:27'),(5588,208,'General Escobedo','2016-05-17 03:58:27'),(5589,208,'General TerÃ¡n','2016-05-17 03:58:27'),(5590,208,'General TreviÃ±o','2016-05-17 03:58:27'),(5591,208,'General Zaragoza','2016-05-17 03:58:27'),(5592,208,'General Zuazua','2016-05-17 03:58:27'),(5593,208,'Guadalupe','2016-05-17 03:58:27'),(5594,208,'Hidalgo','2016-05-17 03:58:28'),(5595,208,'Higueras','2016-05-17 03:58:28'),(5596,208,'Hualahuises','2016-05-17 03:58:28'),(5597,208,'Iturbide','2016-05-17 03:58:28'),(5598,208,'JuÃ¡rez','2016-05-17 03:58:28'),(5599,208,'Lampazos de Naranjo','2016-05-17 03:58:28'),(5600,208,'Linares','2016-05-17 03:58:28'),(5601,208,'Los Aldama','2016-05-17 03:58:28'),(5602,208,'Los Herrera','2016-05-17 03:58:28'),(5603,208,'Los Ramones','2016-05-17 03:58:29'),(5604,208,'MarÃ­n','2016-05-17 03:58:29'),(5605,208,'Melchor Ocampo','2016-05-17 03:58:29'),(5606,208,'Mier y Noriega','2016-05-17 03:58:29'),(5607,208,'Mina','2016-05-17 03:58:29'),(5608,208,'Montemorelos','2016-05-17 03:58:29'),(5609,208,'Monterrey','2016-05-17 03:58:29'),(5610,208,'ParÃ¡s','2016-05-17 03:58:29'),(5611,208,'PesquerÃ­a','2016-05-17 03:58:30'),(5612,208,'Rayones','2016-05-17 03:58:30'),(5613,208,'Sabinas Hidalgo','2016-05-17 03:58:30'),(5614,208,'Salinas Victoria','2016-05-17 03:58:30'),(5615,208,'San NicolÃ¡s de los Garza','2016-05-17 03:58:30'),(5616,208,'San Pedro Garza GarcÃ­a','2016-05-17 03:58:30'),(5617,208,'Santa Catarina','2016-05-17 03:58:30'),(5618,208,'Santiago','2016-05-17 03:58:30'),(5619,208,'Vallecillo','2016-05-17 03:58:30'),(5620,208,'Villaldama','2016-05-17 03:58:31'),(5621,357,'Amealco de Bonfil','2016-05-17 03:58:31'),(5622,357,'Pinal de Amoles','2016-05-17 03:58:31'),(5623,357,'Arroyo Seco','2016-05-17 03:58:31'),(5624,357,'Cadereyta de Montes','2016-05-17 03:58:31'),(5625,357,'ColÃ³n','2016-05-17 03:58:31'),(5626,357,'Corregidora','2016-05-17 03:58:31'),(5627,357,'Ezequiel Montes','2016-05-17 03:58:31'),(5628,357,'Huimilpan','2016-05-17 03:58:32'),(5629,357,'Jalpan de Serra','2016-05-17 03:58:32'),(5630,357,'Landa de Matamoros','2016-05-17 03:58:32'),(5631,357,'El MarquÃ©s','2016-05-17 03:58:32'),(5632,357,'Pedro Escobedo','2016-05-17 03:58:32'),(5633,357,'PeÃ±amiller','2016-05-17 03:58:32'),(5634,357,'QuerÃ©taro','2016-05-17 03:58:32'),(5635,357,'San JoaquÃ­n','2016-05-17 03:58:32'),(5636,357,'San Juan del RÃ­o','2016-05-17 03:58:32'),(5637,357,'Tequisquiapan','2016-05-17 03:58:33'),(5638,357,'TolimÃ¡n','2016-05-17 03:58:33'),(5639,199,'Ahualulco','2016-05-17 03:58:33'),(5640,199,'Alaquines','2016-05-17 03:58:33'),(5641,199,'AquismÃ³n','2016-05-17 03:58:33'),(5642,199,'Armadillo de los Infante','2016-05-17 03:58:33'),(5643,199,'Axtla de Terrazas','2016-05-17 03:58:33'),(5644,199,'CÃ¡rdenas','2016-05-17 03:58:33'),(5645,199,'Catorce','2016-05-17 03:58:34'),(5646,199,'Cedral','2016-05-17 03:58:34'),(5647,199,'Cerritos','2016-05-17 03:58:34'),(5648,199,'Cerro de San Pedro','2016-05-17 03:58:34'),(5649,199,'Charcas','2016-05-17 03:58:34'),(5650,199,'Ciudad del MaÃ­z','2016-05-17 03:58:34'),(5651,199,'Ciudad FernÃ¡ndez','2016-05-17 03:58:34'),(5652,199,'Ciudad Valles','2016-05-17 03:58:34'),(5653,199,'CoxcatlÃ¡n','2016-05-17 03:58:35'),(5654,199,'Ebano','2016-05-17 03:58:35'),(5655,199,'El Naranjo','2016-05-17 03:58:35'),(5656,199,'Guadalcazar','2016-05-17 03:58:35'),(5657,199,'HuehuetlÃ¡n','2016-05-17 03:58:35'),(5658,199,'Lagunillas','2016-05-17 03:58:35'),(5659,199,'Matehuala','2016-05-17 03:58:35'),(5660,199,'Matlapa','2016-05-17 03:58:35'),(5661,199,'Mexquitic de Carmona','2016-05-17 03:58:36'),(5662,199,'Moctezuma','2016-05-17 03:58:36'),(5663,199,'RayÃ³n','2016-05-17 03:58:36'),(5664,199,'Rioverde','2016-05-17 03:58:36'),(5665,199,'Salinas','2016-05-17 03:58:36'),(5666,199,'San Antonio','2016-05-17 03:58:36'),(5667,199,'San Ciro de Acosta','2016-05-17 03:58:36'),(5668,199,'San Luis PotosÃ­','2016-05-17 03:58:36'),(5669,199,'San MartÃ­n Chalchicuautla','2016-05-17 03:58:37'),(5670,199,'San NicolÃ¡s Tolentino','2016-05-17 03:58:37'),(5671,199,'Santa Catarina','2016-05-17 03:58:37'),(5672,199,'Santa MarÃ­a del RÃ­o','2016-05-17 03:58:37'),(5673,199,'Santo Domingo','2016-05-17 03:58:37'),(5674,199,'San Vicente Tancuayalab','2016-05-17 03:58:37'),(5675,199,'Soledad de Graciano SÃ¡nchez','2016-05-17 03:58:37'),(5676,199,'Tamasopo','2016-05-17 03:58:37'),(5677,199,'Tamazunchale','2016-05-17 03:58:37'),(5678,199,'Tampacan','2016-05-17 03:58:38'),(5679,199,'TampamolÃ³n Corona','2016-05-17 03:58:38'),(5680,199,'TamuÃ­n','2016-05-17 03:58:38'),(5681,199,'Tancanhuitz de Santos','2016-05-17 03:58:38'),(5682,199,'TanlajÃ¡s','2016-05-17 03:58:38'),(5683,199,'TanquiÃ¡n de Escobedo','2016-05-17 03:58:38'),(5684,199,'Tierra Nueva','2016-05-17 03:58:38'),(5685,199,'Vanegas','2016-05-17 03:58:38'),(5686,199,'Venado','2016-05-17 03:58:39'),(5687,199,'Villa de Arista','2016-05-17 03:58:39'),(5688,199,'Villa de Arriaga','2016-05-17 03:58:39'),(5689,199,'Villa de Guadalupe','2016-05-17 03:58:39'),(5690,199,'Villa de La Paz','2016-05-17 03:58:39'),(5691,199,'Villa de Ramos','2016-05-17 03:58:39'),(5692,199,'Villa de Reyes','2016-05-17 03:58:39'),(5693,199,'Villa de Hidalgo','2016-05-17 03:58:39'),(5694,199,'Villa JuÃ¡rez','2016-05-17 03:58:39'),(5695,199,'Xilitla','2016-05-17 03:58:40'),(5696,199,'Zaragoza','2016-05-17 03:58:40'),(5697,400,'AbalÃ¡','2016-05-17 03:58:40'),(5698,400,'Acanceh','2016-05-17 03:58:40'),(5699,400,'Akil','2016-05-17 03:58:40'),(5700,400,'Baca','2016-05-17 03:58:40'),(5701,400,'BokobÃ¡','2016-05-17 03:58:40'),(5702,400,'Buctzotz','2016-05-17 03:58:40'),(5703,400,'CacalchÃ©n','2016-05-17 03:58:41'),(5704,400,'Calotmul','2016-05-17 03:58:41'),(5705,400,'Cansahcab','2016-05-17 03:58:41'),(5706,400,'Cantamayec','2016-05-17 03:58:41'),(5707,400,'CelestÃºn','2016-05-17 03:58:41'),(5708,400,'Cenotillo','2016-05-17 03:58:41'),(5709,400,'Conkal','2016-05-17 03:58:41'),(5710,400,'Cuncunul','2016-05-17 03:58:41'),(5711,400,'CuzamÃ¡','2016-05-17 03:58:42'),(5712,400,'ChacsinkÃ­n','2016-05-17 03:58:42'),(5713,400,'Chankom','2016-05-17 03:58:42'),(5714,400,'Chapab','2016-05-17 03:58:42'),(5715,400,'Chemax','2016-05-17 03:58:42'),(5716,400,'Chicxulub Pueblo','2016-05-17 03:58:42'),(5717,400,'ChichimilÃ¡','2016-05-17 03:58:42'),(5718,400,'Chikindzonot','2016-05-17 03:58:42'),(5719,400,'ChocholÃ¡','2016-05-17 03:58:43'),(5720,400,'Chumayel','2016-05-17 03:58:43'),(5721,400,'Dzan','2016-05-17 03:58:43'),(5722,400,'Dzemul','2016-05-17 03:58:43'),(5723,400,'DzidzantÃºn','2016-05-17 03:58:43'),(5724,400,'Dzilam de Bravo','2016-05-17 03:58:43'),(5725,400,'Dzilam GonzÃ¡lez','2016-05-17 03:58:43'),(5726,400,'DzitÃ¡s','2016-05-17 03:58:43'),(5727,400,'Dzoncauich','2016-05-17 03:58:44'),(5728,400,'Espita','2016-05-17 03:58:44'),(5729,400,'HalachÃ³','2016-05-17 03:58:44'),(5730,400,'HocabÃ¡','2016-05-17 03:58:44'),(5731,400,'HoctÃºn','2016-05-17 03:58:44'),(5732,400,'HomÃºn','2016-05-17 03:58:44'),(5733,400,'HuhÃ­','2016-05-17 03:58:44'),(5734,400,'HunucmÃ¡','2016-05-17 03:58:44'),(5735,400,'Ixil','2016-05-17 03:58:44'),(5736,400,'Izamal','2016-05-17 03:58:45'),(5737,400,'KanasÃ­n','2016-05-17 03:58:45'),(5738,400,'Kantunil','2016-05-17 03:58:45'),(5739,400,'Kaua','2016-05-17 03:58:45'),(5740,400,'Kinchil','2016-05-17 03:58:45'),(5741,400,'KopomÃ¡','2016-05-17 03:58:45'),(5742,400,'Mama','2016-05-17 03:58:45'),(5743,400,'ManÃ­','2016-05-17 03:58:45'),(5744,400,'MaxcanÃº','2016-05-17 03:58:46'),(5745,400,'MayapÃ¡n','2016-05-17 03:58:46'),(5746,400,'MÃ©rida','2016-05-17 03:58:46'),(5747,400,'MocochÃ¡','2016-05-17 03:58:46'),(5748,400,'Motul','2016-05-17 03:58:46'),(5749,400,'Muna','2016-05-17 03:58:46'),(5750,400,'Muxupip','2016-05-17 03:58:46'),(5751,400,'OpichÃ©n','2016-05-17 03:58:46'),(5752,400,'Oxkutzcab','2016-05-17 03:58:47'),(5753,400,'PanabÃ¡','2016-05-17 03:58:47'),(5754,400,'Peto','2016-05-17 03:58:47'),(5755,400,'Progreso','2016-05-17 03:58:47'),(5756,400,'Quintana Roo','2016-05-17 03:58:47'),(5757,400,'RÃ­o Lagartos','2016-05-17 03:58:47'),(5758,400,'Sacalum','2016-05-17 03:58:47'),(5759,400,'Samahil','2016-05-17 03:58:47'),(5760,400,'Sanahcat','2016-05-17 03:58:48'),(5761,400,'San Felipe','2016-05-17 03:58:48'),(5762,400,'Santa Elena','2016-05-17 03:58:48'),(5763,400,'SeyÃ©','2016-05-17 03:58:48'),(5764,400,'SinanchÃ©','2016-05-17 03:58:48'),(5765,400,'Sotuta','2016-05-17 03:58:48'),(5766,400,'SucilÃ¡','2016-05-17 03:58:48'),(5767,400,'Sudzal','2016-05-17 03:58:48'),(5768,400,'Suma','2016-05-17 03:58:49'),(5769,400,'TahdziÃº','2016-05-17 03:58:49'),(5770,400,'Tahmek','2016-05-17 03:58:49'),(5771,400,'Teabo','2016-05-17 03:58:49'),(5772,400,'Tecoh','2016-05-17 03:58:49'),(5773,400,'Tekal de Venegas','2016-05-17 03:58:49'),(5774,400,'TekantÃ³','2016-05-17 03:58:49'),(5775,400,'Tekax','2016-05-17 03:58:49'),(5776,400,'Tekit','2016-05-17 03:58:50'),(5777,400,'Tekom','2016-05-17 03:58:50'),(5778,400,'Telchac Pueblo','2016-05-17 03:58:50'),(5779,400,'Telchac Puerto','2016-05-17 03:58:50'),(5780,400,'Temax','2016-05-17 03:58:50'),(5781,400,'TemozÃ³n','2016-05-17 03:58:50'),(5782,400,'TepakÃ¡n','2016-05-17 03:58:50'),(5783,400,'Tetiz','2016-05-17 03:58:51'),(5784,400,'Teya','2016-05-17 03:58:51'),(5785,400,'Ticul','2016-05-17 03:58:51'),(5786,400,'Timucuy','2016-05-17 03:58:51'),(5787,400,'TinÃºm','2016-05-17 03:58:51'),(5788,400,'Tixcacalcupul','2016-05-17 03:58:51'),(5789,400,'Tixkokob','2016-05-17 03:58:51'),(5790,400,'TixmÃ©huac','2016-05-17 03:58:51'),(5791,400,'TixpÃ©hual','2016-05-17 03:58:51'),(5792,400,'TizimÃ­n','2016-05-17 03:58:52'),(5793,400,'TunkÃ¡s','2016-05-17 03:58:52'),(5794,400,'Tzucacab','2016-05-17 03:58:52'),(5795,400,'Uayma','2016-05-17 03:58:52'),(5796,400,'UcÃº','2016-05-17 03:58:52'),(5797,400,'UmÃ¡n','2016-05-17 03:58:52'),(5798,400,'Valladolid','2016-05-17 03:58:52'),(5799,400,'Xocchel','2016-05-17 03:58:53'),(5800,400,'YaxcabÃ¡','2016-05-17 03:58:53'),(5801,400,'Yaxkukul','2016-05-17 03:58:53'),(5802,400,'YobaÃ­n','2016-05-17 03:58:53'),(5803,140,'Miller','2017-01-26 02:49:59');
