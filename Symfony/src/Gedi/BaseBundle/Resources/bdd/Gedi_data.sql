-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 23 Février 2017 à 16:03
-- Version du serveur: 5.6.33-0ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `Gedi`
--

--
-- Contenu de la table `Document`
--

INSERT INTO `Document` (`id_document`, `nom`, `type_doc`, `date_creation`, `date_modification`, `tag`, `resume`, `path`, `nb_download`, `id_projet_fk_document`, `id_utilisateur_fk_document`) VALUES
(18, '3097-mur-en-bois-wallfizz', 'jpg', '2017-02-22 19:48:20', '2017-02-23 10:05:32', 'wallpaper', 'Mon fond decran', '9/Projet 1/5dbf782101e8409fd4fa74f4da7d5af1.jpeg', 0, 7, 9),
(19, 'Coldplay-a-head-full-of-dreams-1', 'jpg', '2017-02-22 19:49:11', '2017-02-22 19:49:11', 'coldplay', 'Pochette album', '10/Projet 2/334a7858e8c9e2ec968abc58e43c105e.jpeg', 0, 8, 10),
(21, 'Cars_back_to_the_future_time_t_2560x1600_wallpaper', 'jpg', '2017-02-23 09:56:42', '2017-02-23 10:26:05', 'film', 'film préféré', '11/Projet 3/7782e11f2277a0cc728507ff92aeb1ae.jpg', 0, 9, 11),
(25, 'Interstellar2', 'png', '2017-02-23 10:42:28', '2017-02-23 14:59:55', NULL, NULL, '9/Projet 1/1ce7a6d5dc183f137d287977ca2c8a6c.png', 1, 7, 9);

--
-- Contenu de la table `Groupe`
--

INSERT INTO `Groupe` (`id_groupe`, `nom`, `date_creation`, `date_modification`, `id_utilisateur_fk_groupe`) VALUES
(5, 'Groupe 1', '2017-02-22 19:39:37', '2017-02-22 19:39:37', 9),
(6, 'Groupe 2', '2017-02-22 19:40:02', '2017-02-22 19:40:02', 10),
(7, 'Groupe 3', '2017-02-22 19:40:13', '2017-02-22 19:40:13', 11);

--
-- Contenu de la table `Projet`
--

INSERT INTO `Projet` (`id_projet`, `nom`, `path`, `date_creation`, `date_modification`, `id_utilisateur_fk_projet`, `parent_id`) VALUES
(7, 'Projet 1', '9/Projet 1', '2017-02-22 19:45:25', '2017-02-22 19:45:25', 9, NULL),
(8, 'Projet 2', '10/Projet 2', '2017-02-22 19:46:34', '2017-02-22 19:46:34', 10, NULL),
(9, 'Projet 3', '11/Projet 3', '2017-02-22 19:46:51', '2017-02-22 19:46:51', 11, NULL);

--
-- Contenu de la table `Utilisateur`
--

INSERT INTO `Utilisateur` (`id_utilisateur`, `login`, `mot_de_passe`, `nom`, `prenom`, `salt`, `role`, `actif`, `date_creation`, `date_modification`) VALUES
(4, 'anthony@email.fr', '$2y$13$LrssCwYymE2Uir9DeFKZzOXiZ.yqFI87lyGz5C2cHs1sXQ7BO/8wS', 'Gumb', 'Anthony', 'd11d599b5362baaa96bf91b', 'ROLE_ADMIN', 1, '2017-02-02 12:17:33', '2017-02-02 12:17:46'),
(9, 'yasmine@email.fr', '$2y$13$makM5qC1dq69..CD8GEcIepy20VbCnSizjtl9mSJ7HpBB7gqGXUh.', 'Amar', 'Yasmine', '793d07f8f7653d5cc860d46', 'ROLE_USER', 1, '2017-02-22 19:36:40', '2017-02-22 19:37:50'),
(10, 'audrey@email.fr', '$2y$13$LJ0vxZHQDHAIYv/w64jWnuK69.OVwekhtzDIROn7txldgkMtxzFNW', 'Mommeja', 'Audrey', '6c5316fe3c8b396ebd0bd6e', 'ROLE_USER', 1, '2017-02-22 19:38:41', '2017-02-22 19:38:41'),
(11, 'sofiane@email.fr', '$2y$13$R2FvY9RLAe07H3lfDX/UceLd4YfDcodnDJ/dQPLJI33.uwasTWhsu', 'Abidi', 'Sofiane', '5260f5db56cd53958e35e5b', 'ROLE_USER', 1, '2017-02-22 19:39:06', '2017-02-22 19:39:06');

--
-- Contenu de la table `Utilisateur_Groupe`
--

INSERT INTO `Utilisateur_Groupe` (`id_utilisateur_ug`, `id_groupe_ug`) VALUES
(9, 5),
(10, 6),
(11, 7);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;