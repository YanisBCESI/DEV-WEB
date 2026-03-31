-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 31 mars 2026 à 09:03
-- Version du serveur : 8.0.45-0ubuntu0.24.04.1
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `stage4all`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `Titre` varchar(100) NOT NULL,
  `Contenu` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` int NOT NULL,
  `etudiant_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `statut` enum('envoyee','vue','retenue','refusee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'envoyee',
  `comaire` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_candidature` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comptes`
--

CREATE TABLE `comptes` (
  `id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comptes`
--

INSERT INTO `comptes` (`id`, `email`, `mot_de_passe`, `role_id`, `actif`, `created_at`) VALUES
(1, 'contact@websolutions.fr', 'hash_mdp1', 2, 1, '2026-03-30 08:44:50'),
(2, 'rh@data-insights.fr', 'hash_mdp2', 2, 1, '2026-03-30 08:44:50'),
(3, 'jobs@cybersecure.fr', 'hash_mdp3', 2, 1, '2026-03-30 08:44:50'),
(4, 'hr@cloudfactory.io', 'hash_mdp4', 2, 1, '2026-03-30 08:44:50');

-- --------------------------------------------------------

--
-- Structure de la table `conseils`
--

CREATE TABLE `conseils` (
  `id` int NOT NULL,
  `Titre` varchar(100) NOT NULL,
  `Contenu` text,
  `Tags` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entreprises`
--

CREATE TABLE `entreprises` (
  `id` int NOT NULL,
  `compte_id` int NOT NULL,
  `nom_entreprise` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_entreprise` enum('TPE','PME','ETI','GE') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secteur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `siret` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `site_web` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moyenne_note` decimal(3,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entreprises`
--

INSERT INTO `entreprises` (`id`, `compte_id`, `nom_entreprise`, `type_entreprise`, `secteur`, `siret`, `adresse`, `ville`, `code_postal`, `description`, `site_web`, `moyenne_note`, `created_at`) VALUES
(1, 1, 'Web Solutions', 'PME', 'Informatique / Web', '12345678900011', '10 rue du Web', 'Paris', '75010', 'Agence spécialisée dans le développement d\'applications web.', 'https://www.websolutions.fr', NULL, '2026-03-30 08:44:50'),
(2, 2, 'Data Insights', 'ETI', 'Data / BI', '12345678900022', '20 avenue des Données', 'Issy-les-Moulineaux', '92130', 'Société de conseil en data analytics et business intelligence.', 'https://www.data-insights.fr', NULL, '2026-03-30 08:44:50'),
(3, 3, 'CyberSecure', 'PME', 'Cybersécurité', '12345678900033', '5 boulevard de la Sécurité', 'Toulouse', '31000', 'Entreprise spécialisée dans la sécurité des systèmes d\'information.', 'https://www.cybersecure.fr', NULL, '2026-03-30 08:44:50'),
(4, 4, 'CloudFactory', 'GE', 'Cloud / SaaS', '12345678900044', '15 rue du Cloud', 'Paris', '75009', 'Fournisseur de solutions cloud et SaaS pour entreprises.', 'https://www.cloudfactory.io', NULL, '2026-03-30 08:44:50');

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int NOT NULL,
  `compte_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilote_id` int DEFAULT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `genre` enum('femme','homme','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mdp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `compte_id`, `pilote_id`, `nom`, `prenom`, `genre`, `created_at`, `mdp`, `email`) VALUES
(13, 'etudiant', NULL, 'gougou', 'gaga', 'homme', '2026-03-26 09:48:28', '$2y$10$9sLHlU7nDdMKLpuN.Dru0.RaKlkLzpRxK/cYyumbWC2DAhc/ixYie', 'abcd@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `evaluations_entreprises`
--

CREATE TABLE `evaluations_entreprises` (
  `id` int NOT NULL,
  `etudiant_id` int NOT NULL,
  `entreprise_id` int NOT NULL,
  `note` tinyint NOT NULL,
  `comaire` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `logs_actions`
--

CREATE TABLE `logs_actions` (
  `id` int NOT NULL,
  `compte_id` int DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `offres`
--

CREATE TABLE `offres` (
  `id_offre` int NOT NULL,
  `entreprise_id` int NOT NULL,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_contrat` enum('stage','alternance','emploi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secteur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `localisation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_offre` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `competences` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remuneration` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('ouverte','fermee','archivee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ouverte',
  `nb_places` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nb_vues` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `offres`
--

INSERT INTO `offres` (`id_offre`, `entreprise_id`, `titre`, `type_contrat`, `secteur`, `localisation`, `description_offre`, `competences`, `remuneration`, `date_debut`, `date_fin`, `statut`, `nb_places`, `created_at`, `updated_at`, `nb_vues`) VALUES
(1, 1, 'Stagiaire Développeur Web PHP', 'stage', 'Informatique', 'Paris (75)', 'Participation au développement et à la maintenance d\'un site web interne en PHP et MySQL.', 'PHP, MySQL, HTML, CSS, notions de JavaScript, Git, bonnes pratiques de développement.', '900 €/mois', '2026-04-01', '2026-09-30', 'ouverte', 2, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(2, 1, 'Stagiaire Data & Business Intelligence', 'stage', 'Data / BI', 'Issy-les-Moulineaux (92)', 'Développement de pipelines ETL, modélisation de données et création de tableaux de bord décisionnels.', 'Python, SQL, Power BI ou équivalent, notions de cloud, Git, esprit d\'analyse.', '1 100 €/mois', '2026-05-01', '2026-10-31', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(3, 1, 'Alternant Administrateur Systèmes & Réseaux', 'alternance', 'Informatique / Réseaux', 'Paris (75)', 'Administration des serveurs Linux/Windows, supervision et support aux utilisateurs.', 'Linux, Windows Server, TCP/IP, VLAN, scripting Bash ou PowerShell, sens du service.', 'Selon grille alternance', '2026-09-01', '2027-08-31', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(4, 2, 'Stagiaire DevOps Junior', 'stage', 'DevOps', 'Lyon (69)', 'Mise en place et amélioration de la chaîne CI/CD, automatisation et supervision des environnements.', 'Docker, GitLab CI ou Jenkins, Linux, bases en cloud (AWS/Azure/GCP), scripting.', '1 000 €/mois', '2026-04-15', '2026-10-15', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(5, 2, 'Développeur Web Junior', 'emploi', 'Informatique', 'Paris (75)', 'Développement de nouvelles fonctionnalités sur une application web à fort trafic.', 'JavaScript, framework front (Vue/React/Angular), API REST, bonnes pratiques de tests.', '32 000 €/an', '2026-06-01', NULL, 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(6, 2, 'Alternant Développeur Full‑Stack', 'alternance', 'Informatique', 'Marseille (13)', 'Participation au développement full‑stack d\'applications internes.', 'Node.js ou PHP, framework front, SQL, Git, méthodologie agile, curiosité technique.', 'Selon rythme et convention', '2026-09-01', '2027-08-31', 'ouverte', 2, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(7, 3, 'Stagiaire Cybersecurité', 'stage', 'Cybersécurité', 'Toulouse (31)', 'Contribution aux audits de sécurité, à la gestion des vulnérabilités et à la sensibilisation des équipes.', 'Connaissances réseaux, Linux, outils de scan, notions d\'OWASP, rigueur et confidentialité.', '1 050 €/mois', '2026-05-15', '2026-11-15', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(8, 3, 'Stagiaire Data Analyst Junior', 'stage', 'Data / Analytics', 'Paris (75)', 'Analyse de données marketing et opérationnelles, production de tableaux de bord.', 'SQL, Excel/Google Sheets, un outil de dataviz (Power BI, Tableau), esprit de synthèse.', '1 000 €/mois', '2026-04-01', '2026-09-30', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(9, 3, 'Alternant Data Engineer', 'alternance', 'Data / Engineering', 'Nantes (44)', 'Mise en place de flux de données, optimisation des performances et industrialisation.', 'Python, SQL, ETL, Git, notions de cloud, bonnes pratiques de code.', 'Selon convention', '2026-10-01', '2027-09-30', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(10, 3, 'Stagiaire QA / Testeur Logiciel', 'stage', 'Qualité Logicielle', 'Lille (59)', 'Rédaction et exécution de plans de tests, automatisation de scénarios de non‑régression.', 'Méthodes de tests, outils type Selenium/Cypress, rigueur, bonne communication écrite.', '900 €/mois', '2026-06-01', '2026-11-30', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(11, 4, 'Alternant Administrateur Cloud', 'alternance', 'Cloud', 'Paris (75)', 'Participation à la gestion et à l\'automatisation des infrastructures cloud.', 'AWS ou Azure ou GCP, Linux, scripting, notions de sécurité, Git.', 'Selon politique entreprise', '2026-09-01', '2027-08-31', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(12, 4, 'Stagiaire Support Applicatif', 'stage', 'Support / Production', 'Rennes (35)', 'Support de niveau 2 sur des applications métiers, analyse d\'incidents et rédaction de documentation.', 'Bases SQL, compréhension des architectures web, pédagogie, bon relationnel.', '850 €/mois', '2026-04-15', '2026-09-15', 'ouverte', 2, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(13, 4, 'Développeur Backend Junior', 'emploi', 'Informatique', 'Lyon (69)', 'Conception et développement de services backend robustes et scalables.', 'Java ou C#, bases de données relationnelles, API REST, Git, intégration continue.', '35 000 €/an', '2026-07-01', NULL, 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(14, 2, 'Stagiaire Product Owner IT', 'stage', 'Produit / IT', 'Paris (75)', 'Aide à la rédaction des user stories, priorisation du backlog et suivi des développements.', 'Compréhension fonctionnelle, bases agiles (Scrum/Kanban), bonnes capacités de communication.', '1 000 €/mois', '2026-05-01', '2026-10-31', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(15, 1, 'Alternant Technicien Réseaux', 'alternance', 'Réseaux', 'Nice (06)', 'Déploiement et maintenance d\'équipements réseau chez les clients (switch, routeur, Wi‑Fi).', 'Bases solides en TCP/IP, routage, VLAN, outils de supervision, permis B apprécié.', 'Selon convention', '2026-09-01', '2027-08-31', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(16, 4, 'Stagiaire UX/UI Designer', 'stage', 'Design / UX', 'Paris (75)', 'Conception de maquettes et de prototypes pour des interfaces web et mobiles.', 'Figma ou équivalent, notions HTML/CSS, sens de l\'ergonomie et du détail.', '900 €/mois', '2026-04-01', '2026-09-30', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0);

-- --------------------------------------------------------

--
-- Structure de la table `pilotes`
--

CREATE TABLE `pilotes` (
  `id` int NOT NULL,
  `compte_id` int NOT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `genre` enum('femme','homme','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`) VALUES
(1, 'admin'),
(2, 'entreprise'),
(3, 'etudiant'),
(4, 'pilote');

-- --------------------------------------------------------

--
-- Structure de la table `sessions_utilisateurs`
--

CREATE TABLE `sessions_utilisateurs` (
  `id` int NOT NULL,
  `compte_id` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `etudiant_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_candidature` (`etudiant_id`,`offre_id`),
  ADD KEY `idx_candidatures_offre` (`offre_id`),
  ADD KEY `idx_candidatures_etudiant` (`etudiant_id`),
  ADD KEY `idx_candidatures_statut` (`statut`);

--
-- Index pour la table `comptes`
--
ALTER TABLE `comptes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Index pour la table `conseils`
--
ALTER TABLE `conseils`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `compte_id` (`compte_id`),
  ADD UNIQUE KEY `siret` (`siret`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pilote_id` (`pilote_id`),
  ADD KEY `compte_id` (`compte_id`);

--
-- Index pour la table `evaluations_entreprises`
--
ALTER TABLE `evaluations_entreprises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_evaluation` (`etudiant_id`,`entreprise_id`),
  ADD KEY `entreprise_id` (`entreprise_id`);

--
-- Index pour la table `logs_actions`
--
ALTER TABLE `logs_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compte_id` (`compte_id`);

--
-- Index pour la table `offres`
--
ALTER TABLE `offres`
  ADD PRIMARY KEY (`id_offre`),
  ADD KEY `idx_offres_entreprise` (`entreprise_id`),
  ADD KEY `idx_offres_type` (`type_contrat`),
  ADD KEY `idx_offres_statut` (`statut`);

--
-- Index pour la table `pilotes`
--
ALTER TABLE `pilotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `compte_id` (`compte_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `sessions_utilisateurs`
--
ALTER TABLE `sessions_utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `compte_id` (`compte_id`);

--
-- Index pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`etudiant_id`,`offre_id`),
  ADD KEY `offre_id` (`offre_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `comptes`
--
ALTER TABLE `comptes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `conseils`
--
ALTER TABLE `conseils`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `entreprises`
--
ALTER TABLE `entreprises`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `evaluations_entreprises`
--
ALTER TABLE `evaluations_entreprises`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `logs_actions`
--
ALTER TABLE `logs_actions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `offres`
--
ALTER TABLE `offres`
  MODIFY `id_offre` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `pilotes`
--
ALTER TABLE `pilotes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `sessions_utilisateurs`
--
ALTER TABLE `sessions_utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id_offre`) ON DELETE CASCADE;

--
-- Contraintes pour la table `comptes`
--
ALTER TABLE `comptes`
  ADD CONSTRAINT `comptes_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Contraintes pour la table `entreprises`
--
ALTER TABLE `entreprises`
  ADD CONSTRAINT `entreprises_ibfk_1` FOREIGN KEY (`compte_id`) REFERENCES `comptes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `etudiants_ibfk_2` FOREIGN KEY (`pilote_id`) REFERENCES `pilotes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `etudiants_ibfk_3` FOREIGN KEY (`compte_id`) REFERENCES `roles` (`nom`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `evaluations_entreprises`
--
ALTER TABLE `evaluations_entreprises`
  ADD CONSTRAINT `evaluations_entreprises_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_entreprises_ibfk_2` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `logs_actions`
--
ALTER TABLE `logs_actions`
  ADD CONSTRAINT `logs_actions_ibfk_1` FOREIGN KEY (`compte_id`) REFERENCES `comptes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `offres`
--
ALTER TABLE `offres`
  ADD CONSTRAINT `offres_ibfk_1` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `pilotes`
--
ALTER TABLE `pilotes`
  ADD CONSTRAINT `pilotes_ibfk_1` FOREIGN KEY (`compte_id`) REFERENCES `comptes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sessions_utilisateurs`
--
ALTER TABLE `sessions_utilisateurs`
  ADD CONSTRAINT `sessions_utilisateurs_ibfk_1` FOREIGN KEY (`compte_id`) REFERENCES `comptes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id_offre`) ON DELETE CASCADE;

--
-- Requetes utiles centralisees pour les tests de la wishlist
-- Ces requetes remplacent les anciens fichiers SQL temporaires supprimes du projet.
--
-- Compte etudiant de test utilise pendant les essais wishlist :
-- email : wishlist.test@stage4all.local
-- mot de passe : WishlistTest123!
--
-- Requetes utilisees sur la base reelle :
--
-- INSERT INTO comptes (email, mot_de_passe, role_id, actif)
-- VALUES ('wishlist.test@stage4all.local', '$2y$10$dHdHlI/tTnSRLX9JyDdX8uy2lmJ25iivBn5OhXNNRwdsAl9VuEeZu', 3, 1);
--
-- SET @compte_id = LAST_INSERT_ID();
--
-- INSERT INTO etudiants (compte_id, nom, prenom, genre)
-- VALUES (@compte_id, 'Wishlist', 'Test', 'autre');
--
-- SELECT comptes.id, comptes.email, comptes.role_id, comptes.actif, etudiants.id AS etudiant_id
-- FROM comptes
-- INNER JOIN etudiants ON etudiants.compte_id = comptes.id
-- WHERE comptes.email = 'wishlist.test@stage4all.local';
--
-- SELECT wishlist.id, wishlist.etudiant_id, wishlist.offre_id, offres.titre, entreprises.nom_entreprise
-- FROM wishlist
-- INNER JOIN offres ON offres.id = wishlist.offre_id
-- INNER JOIN entreprises ON entreprises.id = offres.entreprise_id
-- WHERE wishlist.etudiant_id = 14
-- ORDER BY wishlist.date_ajout DESC;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
