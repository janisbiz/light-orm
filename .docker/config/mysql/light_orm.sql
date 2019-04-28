-- MySQL dump 10.13  Distrib 5.7.25, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: light_orm
-- ------------------------------------------------------
-- Server version	5.7.25-28

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
-- Current Database: `light_orm`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `light_orm` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `light_orm`;

--
-- Table structure for table `test_table_one`
--

DROP TABLE IF EXISTS `test_table_one`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_table_one` (
  `id` int(11) unsigned NOT NULL,
  `int_col_not_null` int(11) NOT NULL,
  `varchar_col_not_null` varchar(255) NOT NULL,
  `float_col_not_null` float NOT NULL,
  `int_col_null` int(11) DEFAULT NULL,
  `varchar_col_null` varchar(255) DEFAULT NULL,
  `float_col_null` float DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_table_one`
--

LOCK TABLES `test_table_one` WRITE;
/*!40000 ALTER TABLE `test_table_one` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_table_one` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_table_one_two`
--

DROP TABLE IF EXISTS `test_table_one_two`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_table_one_two` (
  `test_table_one_id` int(11) unsigned NOT NULL,
  `test_table_two_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`test_table_one_id`,`test_table_two_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_table_one_two`
--

LOCK TABLES `test_table_one_two` WRITE;
/*!40000 ALTER TABLE `test_table_one_two` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_table_one_two` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_table_two`
--

DROP TABLE IF EXISTS `test_table_two`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_table_two` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_table_two`
--

LOCK TABLES `test_table_two` WRITE;
/*!40000 ALTER TABLE `test_table_two` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_table_two` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-04-28 20:01:20
