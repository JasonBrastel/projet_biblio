ID Site 

paulygalere@cuck.com

MDP

Azerty123




----------------------------------------------------------------------------------------------------------------


-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 08 déc. 2023 à 10:08
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `biblio`
--

-- --------------------------------------------------------

--
-- Structure de la table `auteurs`
--

DROP TABLE IF EXISTS `auteurs`;
CREATE TABLE IF NOT EXISTS `auteurs` (
  `id_auteur` bigint NOT NULL AUTO_INCREMENT,
  `nom_auteur` varchar(255) NOT NULL,
  PRIMARY KEY (`id_auteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `disponibilités`
--

DROP TABLE IF EXISTS `disponibilités`;
CREATE TABLE IF NOT EXISTS `disponibilités` (
  `id_disponibiite` bigint NOT NULL AUTO_INCREMENT,
  `status_dispo` tinyint(1) NOT NULL,
  `stock_id` bigint NOT NULL,
  PRIMARY KEY (`id_disponibiite`),
  KEY `disponibilités_stock_id_foreign` (`stock_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `genres`
--

DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `id_genre` bigint NOT NULL AUTO_INCREMENT,
  `nom_genre` varchar(255) NOT NULL,
  PRIMARY KEY (`id_genre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

DROP TABLE IF EXISTS `livres`;
CREATE TABLE IF NOT EXISTS `livres` (
  `id_livre` bigint NOT NULL AUTO_INCREMENT,
  `titre_livre` varchar(255) NOT NULL,
  `isbn` text NOT NULL,
  `shortDescription` text NOT NULL,
  `longDescription` text NOT NULL,
  `Image` varchar(255) NOT NULL,
  `nombrePage` smallint NOT NULL,
  `auteur_id` bigint NOT NULL,
  `disponibilite_id` bigint NOT NULL,
  `id_genre` bigint NOT NULL,
  PRIMARY KEY (`id_livre`),
  KEY `livres_disponibilite_id_foreign` (`disponibilite_id`),
  KEY `livres_auteur_id_foreign` (`auteur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livres_genres`
--

DROP TABLE IF EXISTS `livres_genres`;
CREATE TABLE IF NOT EXISTS `livres_genres` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `genre_id` bigint NOT NULL,
  `livre_id` bigint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `genres_livres_genre_id_foreign` (`genre_id`),
  KEY `genres_livres_livre_id_foreign` (`livre_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livre_auteur`
--

DROP TABLE IF EXISTS `livre_auteur`;
CREATE TABLE IF NOT EXISTS `livre_auteur` (
  `id_livre` bigint NOT NULL,
  `id_auteur` bigint NOT NULL,
  PRIMARY KEY (`id_livre`,`id_auteur`),
  KEY `idx_fk_auteur_id` (`id_auteur`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livre_utilisateur`
--

DROP TABLE IF EXISTS `livre_utilisateur`;
CREATE TABLE IF NOT EXISTS `livre_utilisateur` (
  `id_emprunt` bigint NOT NULL AUTO_INCREMENT,
  `id_livre` bigint NOT NULL,
  `id_utilisateur` bigint NOT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour` date NOT NULL,
  PRIMARY KEY (`id_emprunt`),
  KEY `id_livre` (`id_livre`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id_livre` bigint NOT NULL AUTO_INCREMENT,
  `Nombre_livre` smallint NOT NULL,
  PRIMARY KEY (`id_livre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` bigint NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(255) NOT NULL,
  `prenom_utilisateur` varchar(255) NOT NULL,
  `identifiant_utilisateur` bigint NOT NULL,
  `type_utilisateur` varchar(255) NOT NULL,
  `mail_utilisateur` varchar(255) NOT NULL,
  `mdp_utilisateur` varchar(255) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `tel_utilisateur` varchar(30) NOT NULL,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `livre_auteur`
--
ALTER TABLE `livre_auteur`
  ADD CONSTRAINT `livre_auteur_ibfk_1` FOREIGN KEY (`id_livre`) REFERENCES `livres` (`id_livre`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `livre_auteur_ibfk_2` FOREIGN KEY (`id_auteur`) REFERENCES `auteurs` (`id_auteur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `livre_utilisateur`
--
ALTER TABLE `livre_utilisateur`
  ADD CONSTRAINT `livre_utilisateur_ibfk_1` FOREIGN KEY (`id_livre`) REFERENCES `livres` (`id_livre`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `livre_utilisateur_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_livre`) REFERENCES `livres` (`id_livre`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
