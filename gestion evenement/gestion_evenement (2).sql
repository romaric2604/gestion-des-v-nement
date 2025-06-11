-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 11 Juin 2025 à 09:38
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `gestion_evenement`
--

-- --------------------------------------------------------

--
-- Structure de la table `acteur`
--

CREATE TABLE IF NOT EXISTS `acteur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `domicile` varchar(255) DEFAULT NULL,
  `categorie` enum('traiteur','immobilier','dj','decorateur','securite','entretien','chauffeur','hotesse','photographe') DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `cv` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_ajout` date NOT NULL,
  `statut` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `acteur`
--

INSERT INTO `acteur` (`id`, `nom`, `prenom`, `sexe`, `numero`, `photo`, `domicile`, `categorie`, `mot_de_passe`, `cv`, `email`, `date_ajout`, `statut`) VALUES
(2, 'evina', 'romaric', 'F', '691809314', '20250611_020450_6848c822cbf3c.jpeg', 'Nkoabang', 'securite', '$2y$10$OKpED7w.kL0TgfjcnYlEru98nf.gBHHrMRvsEZmsa7bLbUTWKMxFC', '20250611_020450_6848c822ccf8a.pdf', 'ze@gmail.com', '2025-06-11', '');

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `date_ajout` date NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `apercus`
--

CREATE TABLE IF NOT EXISTS `apercus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo` varchar(255) DEFAULT NULL,
  `id_acteur` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE IF NOT EXISTS `avis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `avis` varchar(255) NOT NULL,
  `types` varchar(255) NOT NULL,
  `date_ajout` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `avis`
--

INSERT INTO `avis` (`id`, `id_user`, `avis`, `types`, `date_ajout`) VALUES
(2, 1, 'sfsdfsdfds', 'client', '2025-06-11');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sexe` enum('M','F') DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `domicile` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_ajout` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `client`
--

INSERT INTO `client` (`id`, `nom`, `prenom`, `age`, `sexe`, `photo`, `domicile`, `numero`, `email`, `mot_de_passe`, `date_ajout`) VALUES
(1, 'evina', 'romaric', 17, '', '20250610_163913_68484391a6067.jpeg', 'Nkoabang', '691809314', 'evinazedieubeniromaric@gmail.com', '$2y$10$x1Bzhi7WlYh3o4d0ImIOY.uJ4z1yAodsHN6xDgNVu2P5BGmwh23ye', '2025-06-10');

-- --------------------------------------------------------

--
-- Structure de la table `contrat`
--

CREATE TABLE IF NOT EXISTS `contrat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_evenement` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_acteur` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `contrat`
--

INSERT INTO `contrat` (`id`, `id_evenement`, `id_client`, `id_acteur`) VALUES
(1, 1, 1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `etoile`
--

CREATE TABLE IF NOT EXISTS `etoile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acteur` int(11) DEFAULT NULL,
  `id_evenement` int(11) DEFAULT NULL,
  `nombre` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE IF NOT EXISTS `evenement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `description` text,
  `categorie` enum('mariage','anniversaire','funeral','bapteme','autre') DEFAULT NULL,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `evenement`
--

INSERT INTO `evenement` (`id`, `nom`, `description`, `categorie`, `date_debut`, `date_fin`, `place`) VALUES
(1, 'test', 'test evenement', 'anniversaire', '2025-06-10 00:00:00', '2025-06-12 00:00:00', '255');

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acteur` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_envoye` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `notification`
--

INSERT INTO `notification` (`id`, `id_acteur`, `message`, `date_envoye`) VALUES
(2, 1, 'test de survie', '0000-00-00');

-- --------------------------------------------------------

--
-- Structure de la table `photo_evenement`
--

CREATE TABLE IF NOT EXISTS `photo_evenement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `photo_evenement`
--

INSERT INTO `photo_evenement` (`id`, `nom`, `photo`, `description`) VALUES
(1, 'test image', '20250610_145802_ia.png', '0000-00-00'),
(2, 'test image', '20250610_150047_ia.png', 'test'),
(3, 'test image', '20250610_173734_poivrons.jpeg', 'kjfkjhfkjdfh');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
