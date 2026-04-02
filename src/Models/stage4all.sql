-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 02 avr. 2026 à 21:48
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

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `etudiant_id`, `offre_id`, `statut`, `comaire`, `date_candidature`) VALUES
(1, 15, 2, 'envoyee', 'ehtjerhtjh', '2001-04-25 22:00:00');

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
(4, 'hr@cloudfactory.io', 'hash_mdp4', 2, 1, '2026-03-30 08:44:50'),
(5, 'etudiant.simple@stage4all.local', '$2y$10$38vSfN.7Ws4JmDndc5IIF.3OsOxyc5sgY5vFvWQV3FV51m0qY1AHy', 3, 1, '2026-03-31 12:04:50'),
(6, 'admin@stage4all.local', '$2y$10$FgYCM2CtSf4KXKEF/nSPaeozGVsAA0wO9MnT4jRHbBLBW.KPIZMdS', 1, 1, '2026-03-31 14:16:14'),
(8, 'yanis.barral@viacesi.fr', '$2y$10$DopqEgNLLHOPNVs/xKMAKeVHEpquWnrnYnRAOOv3GwZuD8Ag0Zlcu', 4, 1, '2026-04-01 07:42:32'),
(9, 'web4all@gmail.com', '$2y$10$tanEuhAOHVef3/Zd6iFhuu23.yTUehzSi/xeG2erlK9FYDsQAPamC', 2, 1, '2026-04-01 09:13:20');

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

--
-- Déchargement des données de la table `conseils`
--

INSERT INTO `conseils` (`id`, `Titre`, `Contenu`, `Tags`) VALUES
(1, 'Comment faire un CV?', 'Créer un bon CV\r\n\r\nFaire un CV efficace, c’est surtout présenter clairement tes compétences et ton parcours pour donner envie au recruteur de te rencontrer. Voici une méthode simple 👇\r\n\r\n1. Structure de base\r\n\r\nUn bon CV tient généralement sur 1 page (2 max) et contient :\r\n\r\nInformations personnelles\r\nNom, prénom\r\nEmail (pro)\r\nTéléphone\r\nVille (pas besoin d’adresse complète)\r\nTitre\r\nExemple : Étudiant en informatique / Assistant marketing\r\nAccroche (optionnelle mais recommandée)\r\n2-3 lignes pour résumer ton profil et ton objectif\r\n2. Expériences professionnelles\r\n\r\nListe tes expériences de la plus récente à la plus ancienne :\r\n\r\nPour chaque expérience :\r\n\r\nNom du poste\r\nNom de l’entreprise\r\nDates\r\nMissions principales (avec verbes d’action)\r\n\r\n👉 Exemple :\r\n\r\nGérer les réseaux sociaux\r\nAnalyser les performances\r\nParticiper à des projets d’équipe\r\n3. Formation\r\n\r\nMême logique :\r\n\r\nDiplôme\r\nÉtablissement\r\nDates\r\nMention si utile\r\n4. Compétences\r\n\r\nSépare en catégories :\r\n\r\n💻 Techniques (ex : Excel, Python)\r\n🌍 Langues (avec niveau)\r\n🤝 Soft skills (organisation, travail en équipe)\r\n5. Centres d’intérêt (facultatif)\r\n\r\nAjoute seulement si ça apporte quelque chose :\r\n\r\nSport, projets, engagement, etc.\r\n6. Conseils importants\r\n✔️ Design simple et lisible\r\n✔️ Pas de fautes d’orthographe\r\n✔️ Adapter le CV à chaque offre\r\n✔️ Utiliser des mots-clés du poste\r\n✔️ Être honnête (très important)\r\n7. Outils pour créer un CV\r\n\r\nTu peux utiliser :\r\n\r\nCanva (facile et moderne)\r\nMicrosoft Word (classique)\r\nGoogle Docs (gratuit en ligne)', 'CV'),
(2, 'Réussir son entretien', 'Passer un bon entretien, ce n’est pas juste “répondre correctement” — c’est montrer que tu es la bonne personne, humainement et professionnellement. Voilà une méthode simple et efficace 👇\r\n\r\n🧠 1. Prépare-toi intelligemment\r\n\r\nAvant l’entretien, renseigne-toi sur :\r\n\r\nl’entreprise (activité, valeurs, actualités)\r\nle poste (missions, compétences clés)\r\n\r\n👉 Objectif : adapter tes réponses et montrer ton intérêt réel.\r\n\r\n🗣️ 2. Maîtrise ton discours\r\n\r\nPrépare des réponses aux questions classiques :\r\n\r\n“Parle-moi de toi”\r\n“Tes qualités / défauts”\r\n“Pourquoi ce poste ?”\r\n\r\n💡 Astuce : utilise la méthode STAR\r\n(Situation – Tâche – Action – Résultat) pour raconter tes expériences clairement.\r\n\r\n👀 3. Soigne ton attitude\r\n\r\nPendant l’entretien :\r\n\r\nRegarde ton interlocuteur\r\nSouris naturellement 🙂\r\nParle calmement, sans te précipiter\r\n\r\n👉 Tu es évalué autant sur ton comportement que sur tes compétences.\r\n\r\n🎯 4. Mets-toi en valeur (sans exagérer)\r\nDonne des exemples concrets\r\nParle de tes réussites\r\nExplique ce que tu peux apporter\r\n\r\n⚠️ Évite les réponses vagues comme “je suis motivé” → montre-le avec des faits.\r\n\r\n❓ 5. Pose des questions\r\n\r\nÀ la fin, pose 2–3 questions :\r\n\r\n“Comment se passe une journée type ?”\r\n“Quels sont les objectifs du poste ?”\r\n\r\n👉 Ça montre que tu es impliqué et curieux.\r\n\r\n🚫 6. Évite les erreurs classiques\r\nArriver en retard\r\nCritiquer un ancien employeur\r\nNe pas connaître l’entreprise\r\nParler trop ou pas assez\r\n🧩 7. Petit bonus qui fait la différence\r\n\r\nEnvoie un message après :\r\n\r\n“Merci pour cet échange, il a renforcé mon intérêt pour le poste.”\r\n\r\n👉 Simple, mais très apprécié.', 'Entretient'),
(3, 'Réussir son stage', 'Se comporter correctement en stage, ce n’est pas juste “être poli” — c’est montrer que tu peux déjà agir comme un professionnel. Voilà les clés 👇\r\n\r\n🧠 1. Adopte une attitude pro dès le début\r\nArrive à l’heure (voire un peu en avance)\r\nHabille-toi de façon adaptée\r\nSois respectueux avec tout le monde\r\n\r\n👉 Même en stage, tu es considéré comme un membre de l’équipe.\r\n\r\n👂 2. Observe et écoute beaucoup\r\n\r\nAu début surtout :\r\n\r\nRegarde comment les autres travaillent\r\nÉcoute les consignes attentivement\r\nPrends des notes\r\n\r\n💡 Tu apprends autant en observant qu’en faisant.\r\n\r\n🙋 3. Pose des questions (les bonnes)\r\nSi tu ne comprends pas → demande\r\nMais évite de poser 10 fois la même question\r\n\r\n👉 Montre que tu veux progresser, pas que tu es perdu.\r\n\r\n💪 4. Sois impliqué\r\nPropose ton aide quand tu as fini une tâche\r\nFais les choses sérieusement, même les petites missions\r\nRespecte les délais\r\n\r\n👉 L’objectif : montrer que tu es fiable.\r\n\r\n🗣️ 5. Communique clairement\r\nPréviens si tu as un problème\r\nDemande des retours sur ton travail\r\nInforme de ton avancement\r\n\r\n💡 En entreprise, ne pas communiquer = problème.\r\n\r\n🤝 6. Intègre-toi à l’équipe\r\nDis bonjour, sois agréable\r\nParticipe aux discussions (sans t’imposer)\r\nIntéresse-toi aux autres\r\n\r\n👉 Les compétences humaines comptent énormément.\r\n\r\n🚫 7. Ce qu’il faut éviter\r\nÊtre passif (“on ne m’a rien donné à faire”)\r\nPasser du temps sur ton téléphone\r\nFaire semblant d’avoir compris\r\nSe plaindre constamment\r\n🌟 8. Le petit plus\r\n\r\nÀ la fin :\r\n\r\nDemande un feedback\r\nRemercie ton tuteur\r\n\r\n👉 Ça laisse une excellente impression (et peut aider pour la suite).', 'Stage'),
(4, 'Bien communiquer au travail', 'Bien communiquer en entreprise, ce n’est pas seulement parler — c’est être compris, clair et professionnel dans toutes les situations. Voici les bases essentielles 👇\r\n\r\n🧠 1. Sois clair et structuré\r\n\r\nQuand tu parles ou écris :\r\n\r\nVa droit au but\r\nOrganise tes idées\r\nÉvite les phrases trop longues\r\n\r\n💡 Exemple :\r\n❌ “J’ai un peu avancé mais il y a des trucs…”\r\n✅ “J’ai terminé X, il me reste Y, et j’ai un blocage sur Z.”\r\n\r\n👂 2. Écoute activement\r\nNe coupe pas la parole\r\nReformule si nécessaire\r\nMontre que tu suis (hochement de tête, réponses)\r\n\r\n👉 Une bonne communication, c’est 50% écouter.\r\n\r\n🗣️ 3. Adapte ton ton et ton langage\r\nAvec un manager → plus formel\r\nAvec des collègues → plus naturel\r\nÀ l’écrit → toujours professionnel\r\n\r\n⚠️ Évite le langage trop familier ou les abréviations (sauf contexte informel).\r\n\r\n📩 4. Maîtrise la communication écrite\r\n\r\nEmails / messages :\r\n\r\nObjet clair\r\nMessage court et structuré\r\nFormule de politesse\r\n\r\n💡 Exemple :\r\n\r\nBonjour,\r\nVoici l’avancement du projet…\r\nMerci, bonne journée.\r\n\r\n🤝 5. Ose parler (au bon moment)\r\nPose des questions si besoin\r\nDonne ton avis avec respect\r\nSignale un problème rapidement\r\n\r\n👉 Se taire peut créer des erreurs ou des malentendus.\r\n\r\n⚖️ 6. Gère les désaccords intelligemment\r\nReste calme\r\nParle des faits, pas des personnes\r\nPropose des solutions\r\n\r\n💡 Exemple :\r\n“Je vois ton point, mais on pourrait aussi essayer…”\r\n\r\n🚫 7. Les erreurs à éviter\r\nParler sans écouter\r\nÊtre flou ou vague\r\nEnvoyer des messages impulsifs\r\nIgnorer un problème\r\n🌟 8. Le détail qui change tout\r\n\r\nToujours :\r\n\r\nDire bonjour / merci\r\nReconnaître le travail des autres\r\n\r\n👉 Ça paraît simple, mais ça fait une énorme différence.', 'Stage'),
(5, 'Réussir son rapport', 'Un bon rapport de stage, ce n’est pas juste raconter ce que tu as fait — c’est montrer ce que tu as appris et compris. Voici une méthode simple et efficace 👇\r\n\r\n🧱 1. Structure classique à suivre\r\n\r\nTon rapport doit être bien organisé :\r\n\r\nIntroduction\r\nPrésentation du stage (durée, lieu, objectif)\r\nPourquoi tu as choisi ce stage\r\nPrésentation de l’entreprise\r\nActivité\r\nOrganisation\r\nEnvironnement de travail\r\nTes missions\r\nCe que tu as fait concrètement\r\nLes outils utilisés\r\nLes difficultés rencontrées\r\nApports du stage\r\nCe que tu as appris (technique + humain)\r\nLes compétences développées\r\nConclusion\r\nBilan global\r\nLien avec ton projet professionnel\r\n✍️ 2. Comment bien rédiger\r\nÉcris de façon claire et simple\r\nUtilise des phrases courtes\r\nExplique les termes techniques\r\nParle à la première personne (“j’ai réalisé…”)\r\n\r\n💡 L’objectif : être compris facilement par n’importe qui.\r\n\r\n🎯 3. Mets en valeur ton expérience\r\n\r\nNe te contente pas de décrire :\r\n\r\nAnalyse ce que tu as fait\r\nExplique pourquoi c’était utile\r\nMontre ton évolution\r\n\r\n👉 Exemple :\r\n❌ “J’ai fait un tableau Excel”\r\n✅ “J’ai créé un tableau Excel pour suivre les ventes, ce qui a permis de mieux organiser les données.”\r\n\r\n📊 4. Ajoute des éléments visuels (si possible)\r\nTableaux\r\nGraphiques\r\nCaptures d’écran\r\n\r\n👉 Ça rend ton rapport plus professionnel et vivant.\r\n\r\n🔍 5. Soigne la présentation\r\nPage de garde propre\r\nSommaire\r\nTitres clairs\r\nPas de fautes d’orthographe\r\n\r\n💡 Relis-toi ou fais corriger.\r\n\r\n🚫 6. Les erreurs à éviter\r\nFaire juste une liste de tâches\r\nCopier-coller internet\r\nÊtre trop vague\r\nNégliger la conclusion\r\n🌟 7. Le petit plus\r\n\r\nAjoute une réflexion personnelle :\r\n\r\nCe que tu as aimé / moins aimé\r\nCe que tu ferais différemment\r\n\r\n👉 C’est souvent ce qui fait la différence.', 'Rapport'),
(6, 'Trouver un stage', 'Trouver un stage, ce n’est pas une question de chance — c’est surtout une méthode + de la régularité. Voilà comment t’y prendre efficacement 👇\r\n\r\n🔎 1. Définis ce que tu cherches\r\n\r\nAvant de postuler :\r\n\r\nQuel domaine ?\r\nQuel type de missions ?\r\nQuelle durée ?\r\n\r\n👉 Plus tu es précis, plus ta recherche est efficace.\r\n\r\n🌐 2. Utilise les bons canaux\r\n\r\nCherche sur :\r\n\r\nLinkedIn\r\nIndeed\r\nWelcome to the Jungle\r\nSites d’entreprises directement\r\n\r\n💡 Astuce : active les alertes pour gagner du temps.\r\n\r\n📄 3. Prépare un CV + une lettre efficaces\r\nCV clair, 1 page\r\nMets en avant tes compétences et projets\r\nLettre personnalisée pour chaque entreprise\r\n\r\n👉 Évite les candidatures copiées-collées.\r\n\r\n📩 4. Postule intelligemment\r\nEnvoie plusieurs candidatures (pas une seule)\r\nAdapte ton message à chaque entreprise\r\nRelance après 1 semaine si pas de réponse\r\n\r\n👉 Chercher un stage = un petit “travail” quotidien.\r\n\r\n🤝 5. Utilise ton réseau\r\nFamille, amis, profs\r\nAnciens élèves\r\nContacts sur LinkedIn\r\n\r\n💡 Beaucoup de stages se trouvent grâce au réseau.\r\n\r\n🏢 6. Candidate spontanément\r\n\r\nMême sans offre :\r\n\r\nVa sur les sites d’entreprises\r\nEnvoie CV + message court\r\n\r\n👉 Ça montre ta motivation (et il y a moins de concurrence).\r\n\r\n🗣️ 7. Prépare-toi aux entretiens\r\n\r\nUne fois contacté :\r\n\r\nRenseigne-toi sur l’entreprise\r\nPrépare tes réponses\r\nSois naturel et motivé\r\n🚫 8. Les erreurs à éviter\r\nAttendre la dernière minute\r\nEnvoyer le même CV partout\r\nAbandonner après 3 refus\r\n🌟 9. Le mindset important\r\n\r\n👉 Tu vas sûrement avoir des refus, c’est normal.\r\nChaque candidature te rapproche d’un “oui”.', 'Recherche');

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
(4, 4, 'CloudFactory', 'GE', 'Cloud / SaaS', '12345678900044', '15 rue du Cloud', 'Paris', '75009', 'Fournisseur de solutions cloud et SaaS pour entreprises.', 'https://www.cloudfactory.io', NULL, '2026-03-30 08:44:50'),
(5, 9, 'Web4All', 'PME', 'Informatique', '12345678912345', '1063 Rue du Pied', 'Orléans', '45000', 'C\'est nous !', 'https://www.cesi.fr', 5.00, '2026-04-01 09:13:20');

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int NOT NULL,
  `compte_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pilote_id` int DEFAULT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `genre` enum('femme','homme','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mdp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `compte_id`, `pilote_id`, `nom`, `prenom`, `genre`, `created_at`, `mdp`, `email`) VALUES
(13, 'etudiant', NULL, 'gougou', 'gaga', 'homme', '2026-03-26 09:48:28', '$2y$10$9sLHlU7nDdMKLpuN.Dru0.RaKlkLzpRxK/cYyumbWC2DAhc/ixYie', 'abcd@gmail.com'),
(15, 'etudiant', NULL, 'Etudiant', 'Simple', 'autre', '2026-03-31 12:06:09', '$2y$10$mqdi0l0f4PEAneORaYn3E.8cEW0w/XqNUpoqfizXEDfMk2.YJqt/2', 'etudiant.simple@stage4all.local');

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
-- Structure de la table `evaluations_gestion_entreprises`
--

CREATE TABLE `evaluations_gestion_entreprises` (
  `id` int NOT NULL,
  `entreprise_id` int NOT NULL,
  `manager_role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager_account_id` int NOT NULL,
  `note` tinyint NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evaluations_gestion_entreprises`
--

INSERT INTO `evaluations_gestion_entreprises` (`id`, `entreprise_id`, `manager_role`, `manager_account_id`, `note`, `commentaire`, `created_at`, `updated_at`) VALUES
(1, 5, 'pilote', 8, 5, 'Franchement incroyable', '2026-04-01 09:15:38', '2026-04-01 09:15:38');

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
(2, 1, 'Stagiaire Data & Business Intelligence', 'stage', 'Data / BI', 'Issy-les-Moulineaux (92)', 'Développement de pipelines ETL, modélisation de données et création de tableaux de bord décisionnels.', 'Python, SQL, Power BI ou équivalent, notions de cloud, Git, esprit d\'analyse.', '1 100 €/mois', '2026-05-01', '2026-10-31', 'ouverte', 1, '2026-03-30 08:44:50', '2026-04-02 06:25:29', 19),
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
(16, 4, 'Stagiaire UX/UI Designer', 'stage', 'Design / UX', 'Paris (75)', 'Conception de maquettes et de prototypes pour des interfaces web et mobiles.', 'Figma ou équivalent, notions HTML/CSS, sens de l\'ergonomie et du détail.', '900 €/mois', '2026-04-01', '2026-09-30', 'ouverte', 1, '2026-03-30 08:44:50', '2026-03-30 08:44:50', 0),
(17, 5, 'Stage création de site web', 'stage', 'Informatique', 'Orléans', 'En gros faut faire un site web', 'Toutes', '0 EUR/Mois', '2026-04-01', '2026-04-30', 'ouverte', 1, '2026-04-01 09:14:37', '2026-04-01 14:43:44', 3);

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

--
-- Déchargement des données de la table `pilotes`
--

INSERT INTO `pilotes` (`id`, `compte_id`, `nom`, `prenom`, `genre`, `telephone`, `created_at`) VALUES
(2, 8, 'Yanis', 'BARRAL', 'homme', '0771853979', '2026-04-01 07:42:32');

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
-- Déchargement des données de la table `wishlist`
--

INSERT INTO `wishlist` (`id`, `etudiant_id`, `offre_id`, `date_ajout`) VALUES
(10, 15, 2, '2026-04-02 08:21:26');

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
-- Index pour la table `evaluations_gestion_entreprises`
--
ALTER TABLE `evaluations_gestion_entreprises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_manager_evaluation` (`entreprise_id`,`manager_role`,`manager_account_id`),
  ADD KEY `fk_eval_gestion_compte` (`manager_account_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `comptes`
--
ALTER TABLE `comptes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `conseils`
--
ALTER TABLE `conseils`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `entreprises`
--
ALTER TABLE `entreprises`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `evaluations_entreprises`
--
ALTER TABLE `evaluations_entreprises`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evaluations_gestion_entreprises`
--
ALTER TABLE `evaluations_gestion_entreprises`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `logs_actions`
--
ALTER TABLE `logs_actions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `offres`
--
ALTER TABLE `offres`
  MODIFY `id_offre` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `pilotes`
--
ALTER TABLE `pilotes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Contraintes pour la table `evaluations_gestion_entreprises`
--
ALTER TABLE `evaluations_gestion_entreprises`
  ADD CONSTRAINT `fk_eval_gestion_compte` FOREIGN KEY (`manager_account_id`) REFERENCES `comptes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eval_gestion_entreprise` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
