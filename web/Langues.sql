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

CREATE TABLE IF NOT EXISTS `Langues` (
  `langue_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `langue_langue` varchar(255) DEFAULT NULL,
  `langue_niveau` varchar(255) DEFAULT NULL,
  
  
  PRIMARY KEY (`langue_id`),
  
  KEY `langue_langue` (`langue_langue`),
  KEY `langue_niveau` (`langue_niveau`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36831 ;

--
-- Dumping data for table ``
--

INSERT INTO `Langues` (`id`, `langue`, `niveau`) VALUES
(1,  'Francais', 'connansance de base'),
(2,  'Francais', 'bonne connaissance'),
(3,  'Francais', 'courant'),
(4,  'Francais', 'langue maternelle'),

(5,  'Anglais', 'connansance de base'),
(6,  'Anglais', 'bonne connaissance'),
(7,  'Anglais', 'courant'),
(8,  'Anglais', 'langue maternelle'),

(9,  'Espagnol', 'connansance de base'),
(10,  'Espagnol', 'bonne connaissance'),
(11,  'Espagnol', 'courant'),
(12,  'Espagnol', 'langue maternelle'),

(13,  'Allemand', 'connansance de base'),
(14,  'Allemand', 'bonne connaissance'),
(15,  'Allemand', 'courant'),
(16,  'Allemand', 'langue maternelle');