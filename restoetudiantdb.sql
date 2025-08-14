-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 14, 2025 at 03:57 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restoetudiantdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
                                          `id` int NOT NULL AUTO_INCREMENT,
                                          `utilisateur_id` int NOT NULL,
                                          `date_commande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                                          `statut` enum('en cours','validée','livrée','annulée') DEFAULT 'en cours',
                                          PRIMARY KEY (`id`),
                                          KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commande_formule`
--

DROP TABLE IF EXISTS `commande_formule`;
CREATE TABLE IF NOT EXISTS `commande_formule` (
                                                  `commande_id` int NOT NULL,
                                                  `formule_id` int NOT NULL,
                                                  `quantite` int DEFAULT '1',
                                                  PRIMARY KEY (`commande_id`,`formule_id`),
                                                  KEY `formule_id` (`formule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formule`
--

DROP TABLE IF EXISTS `formule`;
CREATE TABLE IF NOT EXISTS `formule` (
                                         `id` int NOT NULL AUTO_INCREMENT,
                                         `titre` varchar(100) NOT NULL,
                                         `description` text,
                                         `prix` decimal(6,2) NOT NULL,
                                         `cuisine` varchar(100) DEFAULT NULL,
                                         `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                                         PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
                                             `id` int NOT NULL AUTO_INCREMENT,
                                             `nom` varchar(100) NOT NULL,
                                             `prenom` varchar(100) NOT NULL,
                                             `email` varchar(150) NOT NULL,
                                             `motdepasse` varchar(255) NOT NULL,
                                             `universite` varchar(150) DEFAULT NULL,
                                             `annee_academique` varchar(20) DEFAULT NULL,
                                             `carte_scolaire` varchar(100) DEFAULT NULL,
                                             `role` enum('Etudiant','Restaurateur') NOT NULL DEFAULT 'Etudiant',
                                             `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                                             PRIMARY KEY (`id`),
                                             UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `motdepasse`, `universite`, `annee_academique`, `carte_scolaire`, `role`, `date_creation`) VALUES
                                                                                                                                                          (6, 'Beda', 'Eric', 'eric@gmail.com', '$2y$10$IJ/bdsL9h3gjYwsTRm3SrOzAuX.DG37RMu.KsVCoAw7pUMlGF7HNC', 'Montreal', '2024', '123456789', 'Etudiant', '2025-07-14 01:58:31'),
                                                                                                                                                          (5, 'Sehboub', 'Mourad', 'mouradmaths17@gmail.com', '$2y$10$fuq3rqPuTziLisgYQAOx.eFSSoSBeCg7HYpERysoF04jqefjFw9xq', 'Montreal', '2002', '123456', 'Restaurateur', '2025-07-14 01:39:51');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



-- a rajouter
CREATE TABLE commandes (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           user_id INT NOT NULL,
                           date_commande DATETIME NOT NULL,
                           statut VARCHAR(50) DEFAULT 'en attente',

                           FOREIGN KEY (user_id) REFERENCES utilisateur(id)
);

CREATE TABLE commande_items (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                commande_id INT NOT NULL,
                                formule_id INT NOT NULL,
                                quantite INT NOT NULL,
                                prix DECIMAL(10, 2) NOT NULL,

                                FOREIGN KEY (commande_id) REFERENCES commandes(id),
                                FOREIGN KEY (formule_id) REFERENCES formule(id)
);
