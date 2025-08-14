-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 11 août 2025 à 14:58
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `restoetudiantdb`
--

-- --------------------------------------------------------

--
-- Structure de la table `commande`
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
-- Structure de la table `commande_formule`
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
-- Structure de la table `formule`
--

DROP TABLE IF EXISTS `formule`;
CREATE TABLE IF NOT EXISTS `formule` (
                                         `id` int NOT NULL AUTO_INCREMENT,
                                         `titre` varchar(100) NOT NULL,
                                         `description` text,
                                         `prix` decimal(6,2) NOT NULL,
                                         `cuisine` varchar(100) DEFAULT NULL,
                                         `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                                         `disponible` tinyint(1) NOT NULL DEFAULT '1',
                                         `image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
                                         PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `formule`
--

INSERT INTO `formule` (`id`, `titre`, `description`, `prix`, `cuisine`, `date_ajout`, `disponible`, `image`) VALUES
                                                                                                                 (1, 'Poulet Braisé', 'Servi avec alloco et attiéké. Semoule de manioc accompagnée de poisson frit. Ragoût de poulet ivoirien cuit à l’étouffée.', 8.50, 'Cuisine Ivoirienne', '2025-07-27 17:02:32', 1, 'poulet_braise.jpg'),
                                                                                                                 (2, 'Couscous Royal', 'Semoule, légumes et viande. Poulet citron et olives. Tajine marocain aux olives et citron confit.', 9.00, 'Cuisine Marocaine', '2025-07-27 17:02:32', 1, 'couscous_royal.jpeg'),
                                                                                                                 (20, 'Couscous Tunisien', 'Plat le plus emblématique de la Tunisie, décliné en de nombreuses versions, notamment au poisson, à l\'agneau ou aux légumes. ', 8.00, 'Cuisine Tunisienne', '2025-07-27 17:02:32', 1, 'Couscous_Tunisien.jpeg'),
                                                                                                                 (4, 'Yassa Poulet', 'Poulet mariné aux oignons et riz. Riz sénégalais au poisson et légumes.', 8.00, 'Cuisine Sénégalaise', '2025-07-27 17:02:32', 1, 'yassa.jpeg'),
                                                                                                                 (5, 'Chorba', 'Soupe traditionnelle algérienne parfumée au céleri...                  Semoule accompagnée de légumes et viandes variées.', 7.00, 'Cuisine Algérienne', '2025-07-27 17:02:32', 1, 'chorba.jpg'),
                                                                                                                 (6, 'Ndolé', 'Feuilles amères et viande. Plat camerounais à base de poulet, plantain et légumes.', 9.00, 'Cuisine Camerounnaise', '2025-07-27 17:02:32', 1, 'ndole.jpeg');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `motdepasse`, `universite`, `annee_academique`, `carte_scolaire`, `role`, `date_creation`) VALUES
                                                                                                                                                          (7, 'Eric', 'Beda', 'ericbeda@gmail.com', '$2y$10$FSl89bhbYqj/VQ2sbTi.g.IuLP9BGBKfW99ugHWalAW7BeO5aLp6e', 'Montreal', '2025', '23456789', 'Etudiant', '2025-07-15 21:39:28'),
                                                                                                                                                          (5, 'Sehboub', 'Mourad', 'mouradmaths17@gmail.com', '$2y$10$fuq3rqPuTziLisgYQAOx.eFSSoSBeCg7HYpERysoF04jqefjFw9xq', 'Montreal', '2002', '123456', 'Restaurateur', '2025-07-14 01:39:51');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
