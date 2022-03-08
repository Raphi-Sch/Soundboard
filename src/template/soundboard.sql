-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mar. 08 mars 2022 à 15:56
-- Version du serveur : 10.3.29-MariaDB-0+deb10u1
-- Version de PHP : 7.3.19-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `soundboard`
--

-- --------------------------------------------------------

--
-- Structure de la table `active`
--

CREATE TABLE IF NOT EXISTS `active` (
  `reference` smallint(6) NOT NULL AUTO_INCREMENT,
  `volume` float NOT NULL DEFAULT 1,
  `speed` float NOT NULL DEFAULT 1,
  `audio` int(11) DEFAULT NULL,
  `page` int(11) DEFAULT NULL,
  `shortkey` int(11) DEFAULT NULL,
  PRIMARY KEY (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `audio`
--

CREATE TABLE IF NOT EXISTS `audio` (
  `reference` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `file` text NOT NULL,
  PRIMARY KEY (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `sequencer`
--

CREATE TABLE IF NOT EXISTS `sequencer` (
  `reference` int(11) NOT NULL AUTO_INCREMENT,
  `header` tinyint(1) NOT NULL,
  `audio` int(11) DEFAULT NULL,
  `next` int(11) DEFAULT NULL,
  PRIMARY KEY (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
