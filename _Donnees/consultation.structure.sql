-- -----------------------
-- Début de la transaction
-- -----------------------
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- -------------------------
-- Base de données : consultation
-- -------------------------
DROP DATABASE IF EXISTS consultation;
CREATE DATABASE consultation;
USE consultation;

-- ---------------------------------
-- Utilisateur  : u_consultation
-- Mot de passe : YY836O3eew!Vy0OQ
-- ---------------------------------
DROP USER IF EXISTS 'u_consultation'@'localhost';
CREATE USER 'u_consultation'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'YY836O3eew!Vy0OQ';
GRANT USAGE ON *.* TO 'u_consultation'@'localhost';
ALTER USER 'u_consultation'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON `consultation`.* TO 'u_consultation'@'localhost';
ALTER USER 'u_consultation'@'localhost';

-- --------------------------------------------------------

--
-- Structure de la table `consultation`
--

CREATE TABLE `consultation` (
  `id` int NOT NULL,
  `motif` varchar(255) NOT NULL,
  `rapport` varchar(255) DEFAULT NULL,
  `prescription` varchar(255) DEFAULT NULL,
  `ref_medecin` int NOT NULL,
  `ref_dossier` int NOT NULL,
  `ref_rdv` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossier_patient`
--

CREATE TABLE `dossier_patient` (
  `id` int NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ref_patient` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `hopital`
--

CREATE TABLE `hopital` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL,
  `duree` int NOT NULL,
  `ref_consultation` int NOT NULL,
  `ref_statut` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role_utilisateur`
--

CREATE TABLE `role_utilisateur` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `statut_rendezvous`
--

CREATE TABLE `statut_rendezvous` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ref_role` int NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `date_naissance` date NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ref_hopital` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `consultation`
--
ALTER TABLE `consultation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_ibfk_1` (`ref_medecin`),
  ADD KEY `consultation_ibfk_2` (`ref_dossier`);

--
-- Index pour la table `dossier_patient`
--
ALTER TABLE `dossier_patient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dossier_ibfk_1` (`ref_patient`);

--
-- Index pour la table `hopital`
--
ALTER TABLE `hopital`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rendez_vous_ibfk_1` (`ref_consultation`),
  ADD KEY `rendez_vous_ibfk_2` (`ref_statut`);

--
-- Index pour la table `role_utilisateur`
--
ALTER TABLE `role_utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `statut_rendezvous`
--
ALTER TABLE `statut_rendezvous`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `utilisateur_ibfk_2` (`ref_hopital`) USING BTREE,
  ADD KEY `utilisateur_ibfk_1` (`ref_role`) USING BTREE;

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `consultation`
--
ALTER TABLE `consultation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dossier_patient`
--
ALTER TABLE `dossier_patient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `hopital`
--
ALTER TABLE `hopital`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `role_utilisateur`
--
ALTER TABLE `role_utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `statut_rendezvous`
--
ALTER TABLE `statut_rendezvous`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `consultation`
--
ALTER TABLE `consultation`
  ADD CONSTRAINT `consultation_ibfk_1` FOREIGN KEY (`ref_medecin`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultation_ibfk_2` FOREIGN KEY (`ref_dossier`) REFERENCES `dossier_patient` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dossier_patient`
--
ALTER TABLE `dossier_patient`
  ADD CONSTRAINT `dossier_ibfk_1` FOREIGN KEY (`ref_patient`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`ref_consultation`) REFERENCES `consultation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rendez_vous_ibfk_2` FOREIGN KEY (`ref_statut`) REFERENCES `statut_rendezvous` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `hopital_ibfk_2` FOREIGN KEY (`ref_hopital`) REFERENCES `hopital` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_ibfk_1` FOREIGN KEY (`ref_role`) REFERENCES `role_utilisateur` (`id`) ON DELETE CASCADE;
COMMIT;
