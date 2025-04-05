-- MySQL dump 10.13  Distrib 5.6.23, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: cms01
-- ------------------------------------------------------
-- Server version	5.1.73-community

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
-- Table structure for table `component_resource`
--

DROP TABLE IF EXISTS `component_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `component_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `component_name` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valueEn` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `component_resource`
--

LOCK TABLES `component_resource` WRITE;
/*!40000 ALTER TABLE `component_resource` DISABLE KEYS */;
INSERT INTO `component_resource` VALUES (1,15,'Select','a','aa'),(2,15,'Select','b','bb'),(3,7,'Role','admin',''),(4,7,'Role','user',''),(5,7,'Role','dfgf','');
/*!40000 ALTER TABLE `component_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `components`
--

DROP TABLE IF EXISTS `components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `components`
--

LOCK TABLES `components` WRITE;
/*!40000 ALTER TABLE `components` DISABLE KEYS */;
INSERT INTO `components` VALUES (1,'TextField'),(2,'TextArea'),(3,'Position'),(4,'Image'),(5,'Date'),(6,'Password'),(7,'Time'),(8,'SelectBox');
/*!40000 ALTER TABLE `components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_components`
--

DROP TABLE IF EXISTS `module_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `component_id` int(11) DEFAULT NULL,
  `name` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `multilang` int(11) DEFAULT NULL,
  `required` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `module_components_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_components`
--

LOCK TABLES `module_components` WRITE;
/*!40000 ALTER TABLE `module_components` DISABLE KEYS */;
INSERT INTO `module_components` VALUES (14,7,1,'login',0,1),(17,7,6,'pass',0,1),(44,15,8,'Select',1,1),(45,7,8,'Role',0,1);
/*!40000 ALTER TABLE `module_components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_table` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (7,'users','usersdb'),(15,'test','testdb');
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testdb`
--

DROP TABLE IF EXISTS `testdb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testdb` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Select` varchar(255) DEFAULT NULL,
  `SelectEN` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testdb`
--

LOCK TABLES `testdb` WRITE;
/*!40000 ALTER TABLE `testdb` DISABLE KEYS */;
INSERT INTO `testdb` VALUES (1,'2','1');
/*!40000 ALTER TABLE `testdb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usersdb`
--

DROP TABLE IF EXISTS `usersdb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usersdb` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `Role` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usersdb`
--

LOCK TABLES `usersdb` WRITE;
/*!40000 ALTER TABLE `usersdb` DISABLE KEYS */;
INSERT INTO `usersdb` VALUES (2,'admin','$2y$10$VEF04.saurENc5S0fxrnGei/jIxjpa7HkONL1Wb/JQjr6Pbh6A.xO',NULL),(3,'user','$2y$10$hlNKG1Hkr4dB17soMxZEb..CeMFW22SmiAXBP6axh1LOL4lb4bcd6',NULL);
/*!40000 ALTER TABLE `usersdb` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-05 23:50:33
