-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 02, 2014 at 11:25 PM
-- Server version: 5.5.37-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Langues_tests`
--

-- --------------------------------------------------------

--
-- Table structure for table `Langues`
--

CREATE TABLE IF NOT EXISTS `Hobbies` (
  `hobbie_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `hobbie` varchar(255) DEFAULT NULL,
  
  
  
  PRIMARY KEY (`hobbie_id`),
  KEY `hobbie` (`hobbie`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36831 ;

--
-- Dumping data for table ``
--

INSERT INTO `Hobbies` (`id`, `hobbie`) VALUES
(1,  'sport '),
(2,  'lecture'),
(3,  'Musique'),
(4,  'Danse'),
(5,  'Photographie'),
(6,  'Yoga'),
(7, 'aviation');

