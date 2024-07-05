-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 05 juil. 2024 à 12:16
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
(2, 'alphonsebovine@gmail.com', '95fHE2rj+686fnfxs+SAq4Te3Vm4cSF1uxpvNO2MraesEsFR', 2, NULL, 'Bovine', 'Alphonse', '0455874567', '2014-06-10', 'Avenue Saint-Jean, 1000 Bruxelles', 1),
(5, 'henry.zelensky@gmail.com', 'o5vz7736NbSEIGzmQfxzI6xZBuES8+rfmI4UXY7jzwUISkgK', 2, NULL, 'Zelenski', 'Henry', '014789652', '1990-03-02', 'Rue de la paix', 1),
(11, 'alain.bovaris@gmail.com', '+e9/G98yC5kGlUovZU+5EeyNQv9HYqxc7W/sfFEz4c3N5oCu', 3, NULL, 'Bovaris', 'Alain', '0478965214', '2024-06-24', 'Rue des Epinards', 1),
(13, 'olivier.pullman@gmail.com', 'CLrbuhm/rgoRsFgb97o6dJZJv/a9r7Xz/PYAwXWlImzHDdCx', 3, NULL, 'Pullman', 'Olivier', '04588962547', '1995-03-03', 'Rue de la Station', 1),
(14, 'kevin.podosky@gmail.com', 'a8deHseEynj8Thqc1kWtWt7mVDZRxTHq1ITMLVquYxgl878D', 3, NULL, 'Podosky', 'Kevin', '023458741', '1991-03-01', 'Boulevard Régional', 1),
(17, 'caleb.sommier@gmail.com', 'ycbz27nOXTr2LBLOhp2U1kpBv39AMW/V65RVde1R4irbz49c', 2, NULL, 'Sommier', 'Caleb', '0456789521', '1990-06-20', 'Rue du cinquantanaire', 1);

--
-- Déchargement des données de la table `dossier_patient`
--

INSERT INTO `dossier_patient` (`id`, `description`, `ref_patient`) VALUES
(3, 'Patient atteint de retard de croissance', 11),
(5, NULL, 13),
(6, 'Patient généralement en baisse de tension', 14);

--
-- Déchargement des données de la table `consultation`
--

INSERT INTO `consultation` (`id`, `motif`, `rapport`, `prescription`, `ref_medecin`, `ref_dossier`, `ref_rdv`) VALUES
(2, 'Fracture du tibia', 'Grosse fracture et nécessite opération chez un professionnel', 'Antibiotique', 2, 6, 8),
(4, 'Accident de voiture', 'Fractures gaves', 'Antibiotique et pansement', 5, 6, 10),
(12, 'Accident de voiture', '', '', 2, 3, 13),
(13, 'Elongation', 'Pas très grave ', 'Calmant et antibiotique', 5, 3, 0);

--
-- Déchargement des données de la table `statut_rendezvous`
--

INSERT INTO `statut_rendezvous` (`id`, `nom`, `description`) VALUES
(1, 'En attente', 'Le rendez-vous est en attente de réalisation de la consultation associée'),
(2, 'Honoré', 'Le rendez-vous de consultation a bien été honoré'),
(3, 'Annulé', 'Le rendez-vous de consultation n\'a pas été honoré');

--
-- Déchargement des données de la table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `description`, `date`, `duree`, `ref_consultation`, `ref_statut`) VALUES
(8, 'Fracture', '2024-07-14 00:00:00', 35, 2, 1),
(10, 'Accident grave', '2024-07-20 00:00:00', 60, 4, 2),
(13, '', '2024-07-18 00:00:00', 50, 12, 1);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
