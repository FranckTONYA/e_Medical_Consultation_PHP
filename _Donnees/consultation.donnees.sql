-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 24 juin 2024 à 17:15
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
-- Base de données : `consultation`
--

--
-- Déchargement des données de la table `hopital`
--

INSERT INTO `hopital` (`id`, `nom`, `adresse`, `description`) VALUES
(1, 'Centre Hospitalier du Centre', 'Boulevard de la République 70, 7090 Braine-Le-Comte', NULL);

--
-- Déchargement des données de la table `role_utilisateur`
--

INSERT INTO `role_utilisateur` (`id`, `nom`, `description`) VALUES
(1, 'Administrateur', 'Administre tout le système et possède tous les droits'),
(2, 'Medecin', NULL),
(3, 'Patient', NULL),
(4, 'Visiteur', NULL);

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `email`, `password`, `ref_role`, `token`, `nom`, `prenom`, `telephone`, `date_naissance`, `adresse`, `ref_hopital`) VALUES
(1, 'francktonya7@gmail.com', 'iJGWhQrjPPdxq33TxSEuAvteOz2NObtKBni36+YuGAGEqAu1', 1, NULL, 'Tonya', 'Frank', '0466257894', '2004-06-01', 'Rue du printemps 45, 7090 Braine-Le-Comte', 1),
(2, 'medecin@gmail.com', 'GGhxrP2pkTwiKq1z0Kl8L1rUdpSe8UCagxP1775+GNwNta1J', 2, NULL, 'Bovine', 'Alphonse', '0455874567', '2014-06-10', 'Avenue Saint-Jean, 1000 Bruxelles', 1),
(5, 'henry.zelensky@gmail.com', 'owZuHEaoEqJk78DJzkIhtNrAz7vQzRoFh3YA14QmPVwyEybx', 2, NULL, 'Zelenski', 'Henry', '014789652', '1990-03-02', 'Rue de la paix', 1),
(11, 'alain.bovaris@gmail.com', 'or+VNvxrIkGbBOwA+E8yHeokye2mrd3dSighgx41wrqDcnA3', 3, NULL, 'Bovaris', 'Alain', '0478965214', '2024-06-24', 'Rue des Epinards', 1),
(12, 'christophe.matuidi@gmail.com', '8VG8jCOuMqfZAKZYrNHRjFzC18zqrkJiKBgl9VOIDNnXSlRy', 3, NULL, 'Matuidi', 'Christophe', '0425879654', '2024-06-24', 'Avenue Saint Louis', 1),
(13, 'olivier.pullman@gmail.com', 'brIv3rETrjopIgOACRkMnWdtLj23+3/BWNDE6f+420/cR9qp', 3, NULL, 'Pullman', 'Olivier', '04588962547', '1995-03-03', 'Rue de la Station', 1),
(14, 'kevin.podosky@gmail.com', 'TXH9ZrA4KYpTVu58fPHV3ZnG9pL+VacgJW2IWrArWaV4c/dJ', 3, NULL, 'Podosky', 'Kevin', '023458741', '1991-03-01', 'Boulevard Régional', 1),
(15, 'grgr@gmi.vol', 'AkmEMR6bmQNGidxFaMe3MycJvIBHIUdoq4d433JOguVF0g9R', 3, NULL, 'rgr', 'grg', '057889654', '2024-06-24', 'ferfe', 1);

--
-- Déchargement des données de la table `dossier_patient`
--

INSERT INTO `dossier_patient` (`id`, `description`, `ref_utilisateur`) VALUES
(3, 'Patient atteint de retard de croissance', 11),
(4, 'Le patient a un faible taux de calcium', 12),
(5, NULL, 13),
(6, NULL, 14),
(7, NULL, 15);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
