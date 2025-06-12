-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 12 Juin 2025 à 11:27
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `acteur`
--

INSERT INTO `acteur` (`id`, `nom`, `prenom`, `sexe`, `numero`, `photo`, `domicile`, `categorie`, `mot_de_passe`, `cv`, `email`, `date_ajout`, `statut`) VALUES
(2, 'evina', 'romaric', 'F', '688822788', '20250611_020450_6848c822cbf3c.jpeg', 'Nkoabang', 'securite', '$2y$10$OKpED7w.kL0TgfjcnYlEru98nf.gBHHrMRvsEZmsa7bLbUTWKMxFC', '20250611_020450_6848c822ccf8a.pdf', 'ze@gmail.com', '2025-06-11', 'valider'),
(5, 'evina', 'romaric', 'F', '682434125', '20250611_131055_6849643fcffa1.jpeg', 'Nlongkak', 'decorateur', '$2y$10$v82U3qvdsGBLoDHprz3LkeiPcs.YIQHcTxgxIGPBhGgirafxFPqlO', '20250611_131055_6849643fd0d6a.pdf', 'test@gmail.com', '2025-06-11', 'valider');

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
  `cv` varchar(255) NOT NULL,
  `numero` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `admin`
--

INSERT INTO `admin` (`id`, `nom`, `prenom`, `email`, `photo`, `date_ajout`, `mot_de_passe`, `cv`, `numero`) VALUES
(1, 'dieubeni', 'test', 'dieubeni@gmail.com', '20250612_024254_684a228e5705d.jpeg', '2025-06-12', '$2y$10$cyNN8kT.mnRwzLiRmVgAnend1Q3f7erchwPfz3xFyoT922Mswtnd6', '20250612_024254_684a228e5a23c.docx', 691809314);

-- --------------------------------------------------------

--
-- Structure de la table `apercus`
--

CREATE TABLE IF NOT EXISTS `apercus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `photo` varchar(255) DEFAULT NULL,
  `id_acteur` int(11) DEFAULT NULL,
  `date_ajout` date NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `apercus`
--

INSERT INTO `apercus` (`id`, `photo`, `id_acteur`, `date_ajout`, `nom`, `description`) VALUES
(1, '20250611_202523_d3df1bd6f3022e2c3b547f4662a654c9.jpeg', 5, '2025-06-11', 'test image', 'sfsfdsfds'),
(2, '20250611_213414_b3e244c57c86cf8cab1684708dcf5462.jpeg', 5, '2025-06-11', 'images1', '<div class="container">\r\n  <?php foreach ($aperÃ§us as $aperÃ§u): ?>\r\n    <div class="card">\r\n      <!-- Nom et photo de profil avant l''image -->\r\n      <div class="profile-header">\r\n        <img src="../images/<?= htmlspecialchars($acteur[''photo'']) ?>" alt="Profil" class="profile-pic" />\r\n        <div class="photo-title"><?= htmlspecialchars($aperÃ§u[''nom'']) ?></div>\r\n      </div>\r\n      \r\n      <!-- Image principale -->\r\n      <img src="../images/<?= htmlspecialchars($aperÃ§u[''photo'']) ?>" alt="<?= htmlspecialchars($aperÃ§u[''nom'']) ?>" class="main-image" />\r\n      \r\n      <div class="card-content">\r\n        <p class="description"><?= htmlspecialchars($aperÃ§u[''description'']) ?></p>\r\n        <p class="date">AjoutÃ© le: <?= htmlspecialchars($aperÃ§u[''date_ajout'']) ?></p>\r\n      </div>\r\n    </div>\r\n  <?php endforeach; ?>\r\n</div>');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `contrat`
--

INSERT INTO `contrat` (`id`, `id_evenement`, `id_client`, `id_acteur`) VALUES
(1, 1, 1, 2),
(2, 1, 1, 2),
(3, 1, 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `etoile`
--

CREATE TABLE IF NOT EXISTS `etoile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acteur` int(11) DEFAULT NULL,
  `id_evenement` int(11) DEFAULT NULL,
  `nombre` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

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
  `lieu` varchar(255) NOT NULL,
  `place` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `evenement`
--

INSERT INTO `evenement` (`id`, `nom`, `description`, `categorie`, `date_debut`, `date_fin`, `lieu`, `place`) VALUES
(1, 'test', 'test evenement', 'anniversaire', '2025-06-10 00:00:00', '2025-06-12 00:00:00', '', '255'),
(2, 'evina', 'test d''un evenement', 'anniversaire', '2025-06-14 14:04:00', '2025-06-15 14:04:00', '', 'yaounde'),
(3, 'test', 'nbghghgh', 'bapteme', '2025-06-03 14:06:00', '2025-06-05 14:06:00', '', 'yaounde');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `notification`
--

INSERT INTO `notification` (`id`, `id_acteur`, `message`, `date_envoye`) VALUES
(2, 1, 'test de survie', '0000-00-00'),
(3, 7, 'Vous avez Ã©tÃ© acceptÃ© en tant qu''acteur !!!', '2025-06-12'),
(4, 5, 'Vous avez Ã©tÃ© acceptÃ© en tant qu''acteur !!!', '2025-06-12');

-- --------------------------------------------------------

--
-- Structure de la table `notification_admin`
--

CREATE TABLE IF NOT EXISTS `notification_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `date_ajout` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `notification_admin`
--

INSERT INTO `notification_admin` (`id`, `message`, `date_ajout`) VALUES
(2, 'Nouveaux collaborateur du nom de noura edima de la catÃ©gorie decorateur !!! Allez dans la liste des acteurs pour valider son insertion.', '2025-06-12');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `photo_evenement`
--

INSERT INTO `photo_evenement` (`id`, `nom`, `photo`, `description`) VALUES
(1, 'test image', '20250610_145802_ia.png', '0000-00-00'),
(2, 'test image', '20250610_150047_ia.png', 'test'),
(3, 'test image', '20250610_173734_poivrons.jpeg', 'kjfkjhfkjdfh'),
(4, 'test image', '20250612_030357_riz blanc.jpeg', 'dsfsff');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
