-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 11, 2013 at 03:15 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cvm`
--

-- --------------------------------------------------------

--
-- Table structure for table `cvm_download_register`
--

CREATE TABLE IF NOT EXISTS `cvm_download_register` (
  `id_download_register` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fk_request` int(11) unsigned NOT NULL,
  `status` int(1) unsigned NOT NULL COMMENT '1:download error, 2:file saved',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_download_register`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cvm_file_register`
--

CREATE TABLE IF NOT EXISTS `cvm_file_register` (
  `id_file_register` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fk_download_register` int(11) unsigned NOT NULL,
  `gen_date` date NOT NULL,
  `nr_entries` int(11) NOT NULL,
  `fk_file` int(11) NOT NULL,
  PRIMARY KEY (`id_file_register`),
  KEY `fk_file` (`fk_file`),
  KEY `fk_download_register` (`fk_download_register`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cvm_request`
--

CREATE TABLE IF NOT EXISTS `cvm_request` (
  `id_request` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_type` varchar(16) NOT NULL DEFAULT '',
  `method` varchar(64) NOT NULL DEFAULT '',
  `request_date` date NOT NULL,
  `reference_date` date NOT NULL,
  `status` int(11) NOT NULL COMMENT '1:connection error, 2:file doesn''t exist, 3:no more chances,4:success URL retrieved and saved',
  `message` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_request`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70 ;

-- --------------------------------------------------------

--
-- Table structure for table `fundos`
--

CREATE TABLE IF NOT EXISTS `fundos` (
  `id_fundos` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cnpj` varchar(18) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `nome` text COLLATE latin1_general_cs NOT NULL,
  `cnpj_administrador` varchar(18) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `nome_administrador` text COLLATE latin1_general_cs NOT NULL,
  `situacao` varchar(100) COLLATE latin1_general_cs DEFAULT NULL,
  `dt_inicio` date NOT NULL,
  `dt_constituicao` date NOT NULL,
  `classe` varchar(100) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `dt_inicio_classe` date NOT NULL,
  `forma_condominio` varchar(50) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `indicador_desempenho` varchar(50) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `taxa_performance` double DEFAULT NULL,
  `exclusivo` tinyint(1) NOT NULL,
  `cotas` tinyint(1) NOT NULL,
  `tratamento_tributario` tinyint(1) NOT NULL,
  `investidores_qualificados` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_fundos`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `informes_diarios`
--

CREATE TABLE IF NOT EXISTS `informes_diarios` (
  `ID_INFORME` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `CNPJ_FDO` bigint(14) unsigned NOT NULL,
  `DT_COMPTC` date NOT NULL,
  `VL_TOTAL` double NOT NULL,
  `VL_QUOTA` double NOT NULL,
  `PATRIM_LIQ` double NOT NULL,
  `CAPTC_DIA` double NOT NULL,
  `RESG_DIA` double NOT NULL,
  `NR_COTST` int(11) NOT NULL,
  UNIQUE KEY `ID_INFORME` (`ID_INFORME`),
  UNIQUE KEY `daily_entry` (`CNPJ_FDO`,`DT_COMPTC`),
  KEY `DT_COMPTC` (`DT_COMPTC`),
  KEY `CNPJ_FDO` (`CNPJ_FDO`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=691831 ;

-- --------------------------------------------------------

--
-- Table structure for table `system_file`
--

CREATE TABLE IF NOT EXISTS `system_file` (
  `id_file` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `content_type` varchar(32) COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `size` int(11) NOT NULL,
  `content` mediumtext COLLATE latin1_general_cs NOT NULL,
  `checksum` varchar(32) COLLATE latin1_general_cs DEFAULT NULL,
  PRIMARY KEY (`id_file`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs AUTO_INCREMENT=3 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cvm_download_register`
--
ALTER TABLE `cvm_download_register`
  ADD CONSTRAINT `fk_request` FOREIGN KEY (`id_download_register`) REFERENCES `cvm_request` (`id_request`);

--
-- Constraints for table `cvm_file_register`
--
ALTER TABLE `cvm_file_register`
  ADD CONSTRAINT `fk_download_register` FOREIGN KEY (`fk_download_register`) REFERENCES `cvm_download_register` (`id_download_register`),
  ADD CONSTRAINT `fk_file` FOREIGN KEY (`fk_file`) REFERENCES `system_file` (`id_file`);
