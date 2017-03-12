-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 05, 2014 at 05:02 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `clinic`
--
CREATE DATABASE IF NOT EXISTS `clinic` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `clinic`;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subject` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `create_date` int(11) NOT NULL,
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_doctor_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `comment_type` smallint(6) NOT NULL DEFAULT '1' COMMENT 'for future use, 1 is default',
  `create_date` int(11) NOT NULL,
  `last_edit_time` int(11) NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `patient_doctor_id` (`patient_doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `drugs`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `drugs` (
  `drug_id` int(11) NOT NULL AUTO_INCREMENT,
  `drug_name_en` varchar(50) DEFAULT NULL,
  `drug_name_fa` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,0) NOT NULL,
  `num` int(11) NOT NULL DEFAULT '0',
  `memo` text,
  PRIMARY KEY (`drug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `drug_patient`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `drug_patient` (
  `drug_patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `drug_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `user_id_assign` int(11) NOT NULL,
  `assign_date` int(11) NOT NULL,
  `no_of_item` int(11) DEFAULT 1 NOT NULL,
  `total_cost` decimal(10,0) NOT NULL,
  `user_id_discharge` int(11) DEFAULT NULL,
  `discharge_date`int(11) DEFAULT NULL,
  `memo` text,
  PRIMARY KEY (`drug_patient_id`),
  KEY `drug_id` (`drug_id`),
  KEY `patient_id` (`patient_id`),
  KEY `user_id_assign` (`user_id_assign`),
  KEY `user_id_discharge` (`user_id_discharge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `groups` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL,
  `description` text NOT NULL,
  `roles` bigint(20) unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `name`, `description`, `roles`) VALUES
(1, 'Administrator', '', 1),
(2, 'Guest', '', 2),
(3, 'Doctor', '', 4),
(4, 'X-Ray Agent', '', 8),
(5, 'Laboratory Agent', '', 16),
(6, 'Pharmacy Agent', '', 32),
(7, 'Receptionist', '', 64),
(8, 'Patient', '', 128);
-- --------------------------------------------------------

--
-- Table structure for table `lab`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `lab` (
  `test_id` int(11) NOT NULL AUTO_INCREMENT,
  `test_name_en` varchar(50) DEFAULT NULL,
  `test_name_fa` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,0) NOT NULL,
  `memo` text,
  PRIMARY KEY (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lab_files`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `lab_files` (
  `lab_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `lab_patient_id` int(11) NOT NULL,
  `upload_date` int(11) NOT NULL,
  `path` text NOT NULL,
  `memo` text,
  PRIMARY KEY (`lab_file_id`),
  KEY `lab_patient_id` (`lab_patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lab_patient`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `lab_patient` (
  `lab_patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `user_id_assign` int(11) NOT NULL,
  `assign_date` int(11) NOT NULL,
  `no_of_item` int(11) DEFAULT 1 NOT NULL,
  `total_cost` decimal(10,0) NOT NULL,
  `user_id_discharge` int(11) DEFAULT NULL,
  `discharge_date` int(11) DEFAULT NULL,
  `memo` text,
  PRIMARY KEY (`lab_patient_id`),
  KEY `test_id` (`test_id`),
  KEY `patient_id` (`patient_id`),
  KEY `user_id_assign` (`user_id_assign`),
  KEY `user_id_discharge` (`user_id_discharge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `logins` (
  `login_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `time` timestamp NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`login_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `patients` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(40) NOT NULL,
  `last_name` varchar(40) DEFAULT NULL,
  `fname` varchar(40) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `social_id` varchar(12) DEFAULT NULL,
  `id_type` enum('','Tazkara','Passport','Driver License','Bank ID Card') DEFAULT NULL,
  `birth_date`int(11) NULL DEFAULT NULL,
  `create_date` int(11) NOT NULL,
  `picture` text,
  `memo` text,
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patient_doctor`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `patient_doctor` (
  `patient_doctor_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `visit_date` int(11) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`patient_doctor_id`),
  KEY `patient_id` (`patient_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `purchased_drugs`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `purchased_drugs` (
  `purchased_drug_id` int(11) NOT NULL AUTO_INCREMENT,
  `drug_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `purchase_date` int(11) NOT NULL,
  `purchase_price` decimal(10,0) NOT NULL,
  `no_of_item` int(11) DEFAULT 1 NOT NULL,
  `total_cost` decimal(10,0) NOT NULL,
  `memo` text,
  PRIMARY KEY (`purchased_drug_id`),
  KEY `drug_id` (`drug_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `returned_drugs`
--
-- Creation: Jan 05, 2014 at 03:18 PM
--

CREATE TABLE IF NOT EXISTS `returned_drugs` (
  `returned_drug_id` int(11) NOT NULL AUTO_INCREMENT,
  `drug_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `return_date` int(11) NOT NULL,
  `no_of_item` int(11) DEFAULT 1 NOT NULL,
  `total_cost` decimal(10,0) NOT NULL,
  `memo` text,
  PRIMARY KEY (`returned_drug_id`),
  KEY `drug_id` (`drug_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `userdata`
--
-- Creation: Jan 05, 2014 at 03:57 PM
--

CREATE TABLE IF NOT EXISTS `userdata` (
  `userdata_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `first_name` varchar(40) DEFAULT NULL,
  `last_name` varchar(40) DEFAULT NULL,
  `fname` varchar(40) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `position` varchar(40) NOT NULL,
  `social_id` varchar(12) NOT NULL,
  `id_type` enum('','Tazkara','Passport','Driver License','Bank ID Card') DEFAULT 'Tazkara',
  `birth_date`int(11) NULL DEFAULT NULL,
  `create_date` int(11) NOT NULL,
  `picture` text,
  `memo` text,
  PRIMARY KEY (`userdata_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(60) NOT NULL,
  `password_last_set` datetime NOT NULL,
  `password_never_expires` tinyint(1) NOT NULL DEFAULT '0',
  `remember_me` varchar(40) NOT NULL,
  `activation_code` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `forgot_code` varchar(40) NOT NULL,
  `forgot_generated` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime NOT NULL,
  `last_login_ip` int(10) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `user_group` (
  `assoc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`assoc_id`),
  KEY `user_id` (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `xrays`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `xrays` (
  `xray_id` int(11) NOT NULL AUTO_INCREMENT,
  `xray_name_en` varchar(50) DEFAULT NULL,
  `xray_name_fa` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,0) NOT NULL,
  `memo` text,
  PRIMARY KEY (`xray_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `xray_files`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `xray_files` (
  `xray_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `xray_patient_id` int(11) NOT NULL,
  `upload_date` int(11) NOT NULL,
  `path` text NOT NULL,
  `memo` text,
  PRIMARY KEY (`xray_file_id`),
  KEY `xray_patient_id` (`xray_patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `xray_patient`
--
-- Creation: Dec 07, 2013 at 03:42 PM
--

CREATE TABLE IF NOT EXISTS `xray_patient` (
  `xray_patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `xray_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `user_id_assign` int(11) NOT NULL,
  `assign_date` int(11) NOT NULL,
  `no_of_item` int(11) DEFAULT 1 NOT NULL,
  `total_cost` decimal(10,0) NOT NULL,
  `user_id_discharge` int(11) DEFAULT NULL,
  `discharge_date` int(11) DEFAULT NULL,
  `memo` text,
  PRIMARY KEY (`xray_patient_id`),
  KEY `xray_id` (`xray_id`),
  KEY `patient_id` (`patient_id`),
  KEY `user_id_assign` (`user_id_assign`),
  KEY `user_id_discharge` (`user_id_discharge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
