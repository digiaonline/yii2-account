-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: account_test
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `authKey` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lastLoginAt` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_login_history`
--

DROP TABLE IF EXISTS `account_login_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_login_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountId` int(11) NOT NULL DEFAULT '0',
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `numFailedAttempts` int(11) NOT NULL DEFAULT '0',
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_login_history_accountId_account_id` (`accountId`),
  CONSTRAINT `account_login_history_accountId_account_id` FOREIGN KEY (`accountId`) REFERENCES `account` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_login_history`
--

LOCK TABLES `account_login_history` WRITE;
/*!40000 ALTER TABLE `account_login_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_login_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_password_history`
--

DROP TABLE IF EXISTS `account_password_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_password_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountId` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_password_history_accountId_account_id` (`accountId`),
  CONSTRAINT `account_password_history_accountId_account_id` FOREIGN KEY (`accountId`) REFERENCES `account` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_password_history`
--

LOCK TABLES `account_password_history` WRITE;
/*!40000 ALTER TABLE `account_password_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_password_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_provider`
--

DROP TABLE IF EXISTS `account_provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `clientId` varchar(255) NOT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accountId_name_clientId` (`accountId`,`name`,`clientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_provider`
--

LOCK TABLES `account_provider` WRITE;
/*!40000 ALTER TABLE `account_provider` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_provider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_token`
--

DROP TABLE IF EXISTS `account_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountId` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `createdAt` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `accountId_type_token` (`accountId`,`type`,`token`),
  CONSTRAINT `account_token_accountId_account_id` FOREIGN KEY (`accountId`) REFERENCES `account` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_token`
--

LOCK TABLES `account_token` WRITE;
/*!40000 ALTER TABLE `account_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1417026716),('m141113_121701_create_account',1417026718),('m141113_131938_create_account_token',1417026718),('m141113_132146_create_account_login_history',1417026718),('m141113_132156_create_account_password_history',1417026718),('m141119_185128_create_account_provider',1417026718),('m141119_185148_add_foreign_keys',1417026718);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-26 18:33:41
