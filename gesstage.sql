-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 22 avr. 2026 à 12:08
-- Version du serveur : 8.0.45-0ubuntu0.22.04.1
-- Version de PHP : 8.1.2-1ubuntu2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gesstage`
--

-- --------------------------------------------------------

--
-- Structure de la table `activites`
--

CREATE TABLE `activites` (
  `id` bigint UNSIGNED NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `user_nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `bilans`
--

CREATE TABLE `bilans` (
  `id` bigint UNSIGNED NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('en_attente','valide','rejete','brouillon') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `date_soumission` datetime DEFAULT NULL,
  `stagiaire_id` bigint UNSIGNED NOT NULL,
  `tuteur_id` bigint UNSIGNED DEFAULT NULL,
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `note` decimal(5,2) DEFAULT NULL,
  `commentaire_tuteur` text COLLATE utf8mb4_unicode_ci,
  `commentaire_chef` text COLLATE utf8mb4_unicode_ci,
  `fichier_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valide_par` bigint UNSIGNED DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bilans`
--

INSERT INTO `bilans` (`id`, `titre`, `contenu`, `statut`, `date_soumission`, `stagiaire_id`, `tuteur_id`, `service_id`, `date_debut`, `date_fin`, `note`, `commentaire_tuteur`, `commentaire_chef`, `fichier_path`, `valide_par`, `date_validation`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'hdddddddddddddd', 'valide', NULL, 2, 4, NULL, NULL, NULL, '6.00', NULL, NULL, NULL, 10, '2026-04-13 06:56:22', NULL, '2026-04-13 03:56:10', '2026-04-13 03:56:22'),
(2, NULL, 'otiiiiiiiiiii', 'en_attente', NULL, 3, 4, NULL, NULL, NULL, '8.00', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 04:08:49', '2026-04-13 04:08:49');

-- --------------------------------------------------------

--
-- Structure de la table `candidats`
--

CREATE TABLE `candidats` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lettre_motivation_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidats`
--

INSERT INTO `candidats` (`id`, `user_id`, `telephone`, `status`, `bio`, `birth_date`, `address`, `cv_path`, `lettre_motivation_path`, `created_at`, `updated_at`) VALUES
(2, 11, '34567', 'en_attente', NULL, NULL, NULL, NULL, NULL, '2026-04-13 04:56:07', '2026-04-13 04:56:07'),
(3, 13, 'oi', 'en_attente', NULL, NULL, NULL, NULL, NULL, '2026-04-13 08:01:02', '2026-04-13 08:01:02'),
(4, 14, '34tyu', 'en_attente', NULL, NULL, NULL, NULL, NULL, '2026-04-13 08:01:49', '2026-04-13 08:01:49'),
(5, 15, '98765', 'en_attente', NULL, NULL, NULL, NULL, NULL, '2026-04-13 08:20:26', '2026-04-13 08:20:26'),
(6, 16, '0388170635', 'en_attente', NULL, NULL, NULL, NULL, NULL, '2026-04-14 12:15:55', '2026-04-14 12:15:55'),
(7, 18, '09876', 'en_attente', NULL, NULL, NULL, NULL, NULL, '2026-04-16 03:30:18', '2026-04-16 03:30:18');

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

CREATE TABLE `candidatures` (
  `id` bigint UNSIGNED NOT NULL,
  `candidat_id` bigint UNSIGNED NOT NULL,
  `offre_id` bigint UNSIGNED DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `entreprise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stage',
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `motivation` text COLLATE utf8mb4_unicode_ci,
  `cv_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lettre_motivation_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `date_reponse` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id`, `candidat_id`, `offre_id`, `titre`, `description`, `entreprise`, `lieu`, `type`, `statut`, `date_debut`, `date_fin`, `motivation`, `cv_path`, `lettre_motivation_path`, `commentaire`, `date_reponse`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 11, NULL, 'Candidature - testdd', 'Candidature synchronisée le 13/04/2026 à 08:47', NULL, NULL, 'developpement', 'acceptee', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 06:12:39', '2026-04-13 05:47:59', '2026-04-13 06:12:39', NULL),
(2, 14, NULL, 'Candidature - d', 'Candidature générée automatiquement le 13/04/2026 à 11:04', NULL, NULL, 'developpement', 'acceptee', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 08:11:01', '2026-04-13 08:04:22', '2026-04-13 08:11:01', NULL),
(3, 15, NULL, 'Candidature - rr', 'Candidature générée automatiquement le 13/04/2026 à 11:27', NULL, NULL, 'developpement', 'acceptee', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 08:28:11', '2026-04-13 08:27:41', '2026-04-13 08:28:11', NULL),
(4, 16, NULL, 'Candidature - DTS Informatique', 'Candidature générée automatiquement le 14/04/2026 à 15:18', NULL, NULL, 'developpement', 'acceptee', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-14 12:21:54', '2026-04-14 12:18:16', '2026-04-14 12:21:54', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

CREATE TABLE `documents` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fichier_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fichier_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille` decimal(10,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `titre`, `type`, `fichier_path`, `fichier_nom`, `taille`, `description`, `statut`, `commentaire`, `uploaded_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'hf', 'convention', 'documents/3/1774507699_CAP SUR L\' ILE DE LA REUNION.docx', 'CAP SUR L\' ILE DE LA REUNION.docx', '1510.15', NULL, 'en_attente', NULL, NULL, '2026-03-26 03:48:19', '2026-03-26 03:58:02', '2026-03-26 03:58:02'),
(2, 3, 'hf', 'convention', 'documents/3/1774507779_CAP_SUR_L__ILE_DE_LA_REUNION.docx', 'CAP SUR L\' ILE DE LA REUNION.docx', '1510.15', NULL, 'en_attente', NULL, NULL, '2026-03-26 03:49:39', '2026-03-26 03:49:48', '2026-03-26 03:49:48'),
(3, 3, 'ty', 'rapport', 'documents/3/1774508670_CAP_SUR_L__ILE_DE_LA_REUNION.docx', 'CAP SUR L\' ILE DE LA REUNION.docx', '1510.15', NULL, 'en_attente', NULL, NULL, '2026-03-26 04:04:30', '2026-03-26 04:10:36', '2026-03-26 04:10:36'),
(4, 2, 'k', 'attestation', 'documents/2/1774735531_Laravel_rapport.docx', 'Laravel rapport.docx', '9.77', NULL, 'en_attente', NULL, NULL, '2026-03-28 19:05:31', '2026-03-28 19:05:31', NULL),
(5, 11, 'CV - testdd d', 'cv', 'documents/cv/a1thhXk5y8N0C2dXT2eDRtJkdLgtydtdvZEMxZl8.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', NULL, NULL, 'en_attente', NULL, NULL, '2026-04-13 05:07:40', '2026-04-13 05:07:40', NULL),
(6, 11, 'Lettre de motivation - testdd d', 'lettre_motivation', 'documents/lettres/ZwhKs4vDHYW12QRMw3gf8WpO6WAwh8vIko4X0r8v.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', NULL, NULL, 'en_attente', NULL, NULL, '2026-04-13 05:07:41', '2026-04-13 05:07:41', NULL),
(7, 11, 'Diplôme - testdd d', 'diplome', 'documents/diplomes/Lg4LbuM1MiST39Af7YeGPQyIquZQ36Zu317e0AsP.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', NULL, NULL, 'en_attente', NULL, NULL, '2026-04-13 05:07:41', '2026-04-13 05:07:41', NULL),
(8, 14, 'CV - sfgh', 'cv', 'documents/cv/ARh2eNMIej8AsFCGC2gaKbGXdWZGaFiWePql3b6W.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', '436.81', NULL, 'en_attente', NULL, NULL, '2026-04-13 08:04:21', '2026-04-13 08:04:21', NULL),
(9, 14, 'Lettre de motivation - sfgh', 'lettre_motivation', 'documents/lettres/HsdFrw3zx8ViadL3wsxjxc0Em0xlg6N8UHllNRFv.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', '436.81', NULL, 'en_attente', NULL, NULL, '2026-04-13 08:04:22', '2026-04-13 08:04:22', NULL),
(10, 14, 'Diplôme - sfgh', 'diplome', 'documents/diplomes/w02c2ObhGwgw4WetcaqfjZ5LLwh2YguFicjgTo4m.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', '436.81', NULL, 'en_attente', NULL, NULL, '2026-04-13 08:04:22', '2026-04-13 08:04:22', NULL),
(11, 15, 'CV - rr', 'cv', 'documents/cv/kziO03xIBbGj1EWJNdLAXFLCaf6eOaHlnaiyJRZm.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', '436.81', NULL, 'en_attente', NULL, NULL, '2026-04-13 08:27:40', '2026-04-13 08:27:40', NULL),
(12, 15, 'Lettre de motivation - rr', 'lettre_motivation', 'documents/lettres/XjsgYsu0dcs9DCWv9S0EE4ntndoYU4lBF94Kn8ik.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', '436.81', NULL, 'en_attente', NULL, NULL, '2026-04-13 08:27:41', '2026-04-13 08:27:41', NULL),
(13, 15, 'Diplôme - rr', 'diplome', 'documents/diplomes/cncd2BMpdNDrGt6PbYCsZ714stgYrF0ja67rrjWD.pdf', 'rapport_gesstage_2026-04-13T07_16_38.pdf', '436.81', NULL, 'en_attente', NULL, NULL, '2026-04-13 08:27:41', '2026-04-13 08:27:41', NULL),
(14, 16, 'CV - Hermann Sylvano', 'cv', 'documents/cv/eaEuACYBo8RnHOOn7Vq6zgdXSKsuOjJpigg9Iacd.docx', 'Laravel rapport.docx', '9.77', NULL, 'en_attente', NULL, NULL, '2026-04-14 12:18:15', '2026-04-14 12:18:15', NULL),
(15, 16, 'Lettre de motivation - Hermann Sylvano', 'lettre_motivation', 'documents/lettres/YtFJB00ylGenXxZ6NZzam71kG3qotV46m9kRZ69S.docx', 'Laravel rapport.docx', '9.77', NULL, 'en_attente', NULL, NULL, '2026-04-14 12:18:16', '2026-04-14 12:18:16', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint UNSIGNED NOT NULL,
  `stagiaire_id` bigint UNSIGNED DEFAULT NULL,
  `candidat_id` bigint UNSIGNED NOT NULL,
  `stage_id` bigint UNSIGNED DEFAULT NULL,
  `evaluateur_id` bigint UNSIGNED DEFAULT NULL,
  `competences_techniques` int DEFAULT NULL,
  `qualite_travail` int DEFAULT NULL,
  `respect_delais` int DEFAULT NULL,
  `communication` int DEFAULT NULL,
  `autonomie` int DEFAULT NULL,
  `esprit_equipe` int DEFAULT NULL,
  `commentaires` text COLLATE utf8mb4_unicode_ci,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `note` decimal(3,1) DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `evaluateur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evaluateur_nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_evaluation` date DEFAULT NULL,
  `statut` enum('en_attente','publie','archive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `criteria` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `evaluations` (`id`, `stagiaire_id`, `candidat_id`, `stage_id`, `evaluateur_id`, `competences_techniques`, `qualite_travail`, `respect_delais`, `communication`, `autonomie`, `esprit_equipe`, `commentaires`, `titre`, `description`, `note`, `commentaire`, `evaluateur`, `evaluateur_nom`, `date_evaluation`, `statut`, `criteria`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 1, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'g', NULL, '2.8', NULL, NULL, NULL, '2026-03-29', 'publie', '\"[{\\\"nom\\\":\\\"Comp\\\\u00e9tences techniques\\\",\\\"note\\\":\\\"3\\\"},{\\\"nom\\\":\\\"Int\\\\u00e9gration dans l\'\\\\u00e9quipe\\\",\\\"note\\\":\\\"3\\\"},{\\\"nom\\\":\\\"Autonomie\\\",\\\"note\\\":\\\"3\\\"},{\\\"nom\\\":\\\"Qualit\\\\u00e9 du travail\\\",\\\"note\\\":\\\"3\\\"},{\\\"nom\\\":\\\"Respect des d\\\\u00e9lais\\\",\\\"note\\\":\\\"2\\\"}]\"', '2026-03-28 20:39:34', '2026-03-29 02:24:06', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `journaux`
--

CREATE TABLE `journaux` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_journal` date DEFAULT NULL,
  `statut` enum('en_attente','valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `commentaire_tuteur` text COLLATE utf8mb4_unicode_ci,
  `date_validation` timestamp NULL DEFAULT NULL,
  `date_rejet` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2026_03_13_100005_create_taches_table', 1),
(7, '2026_03_13_100006_create_competences_table', 1),
(8, '2026_03_23_075941_create_entreprises_table', 2),
(9, '2026_03_23_075818_create_stages_table', 3),
(10, '2026_03_23_081939_add_birth_date_and_bio_to_users_table', 4),
(14, '2026_03_25_081301_create_pointages_table', 4),
(15, '2026_03_25_082531_create_taches_table', 5),
(16, '2026_03_25_082354_add_missing_columns_to_users_table', 6),
(17, '2026_03_25_083015_create_presences_table', 7),
(18, '2026_03_25_085054_add_deleted_at_to_presences_table', 8),
(19, '2026_03_13_081138_create_documents_table', 9),
(20, '2026_03_23_124756_add_commentaire_to_documents_table', 9),
(21, '2026_03_23_125021_add_deleted_at_to_documents_table', 9),
(22, '2026_03_23_125110_recreate_documents_table', 9),
(23, '2026_03_26_062922_create_candidats_table', 10),
(24, '2026_03_26_063403_add_user_id_to_documents_table', 11),
(25, '2026_03_26_075441_add_terminee_le_to_taches_table', 12),
(26, '2026_03_26_081611_create_evaluations_table', 13),
(27, '2026_03_26_083052_create_journaux_table', 14),
(28, '2026_03_26_090846_add_tuteur_fields_to_users_table', 15),
(29, '2026_03_28_204811_create_candidatures_table', 16),
(30, '2026_03_28_205022_create_offres_table', 17),
(31, '2026_03_28_212950_add_last_login_at_to_users_table', 18),
(32, '2026_03_28_215834_add_entreprise_and_formation_to_users_table', 19),
(33, '2026_03_28_215920_add_entreprise_to_stages_table', 20),
(34, '2026_03_28_220226_add_entreprise_to_stages_table', 21),
(35, '2026_03_29_000241_add_jour_semaine_to_taches_table', 22),
(36, '2026_03_29_003602_create_semaines_validees_table', 23),
(37, '2026_03_29_060633_create_journaux_table', 24),
(38, '2026_03_29_061408_add_user_id_to_presences_table', 25),
(39, '2026_04_09_085332_create_validations_table', 26),
(40, '2026_04_09_090014_create_bilans_table', 27),
(41, '2026_04_09_090153_create_activites_table', 28),
(42, '2026_04_09_090339_create_services_table', 29),
(43, '2026_04_09_090450_add_service_id_to_users_table', 30),
(44, '2026_04_09_094644_create_sanctions_table', 31),
(45, '2026_04_09_103359_add_missing_columns_to_services_table', 32),
(46, '2026_04_09_113259_add_service_id_to_bilans_table', 33),
(47, '2026_04_13_064734_add_missing_columns_to_bilans_table', 34),
(48, '2026_04_13_065530_make_titre_nullable_in_bilans_table', 35),
(49, '2026_04_13_074346_add_dossier_validation_columns_to_users_table', 36),
(50, '2026_04_13_093236_create_validations_table', 37),
(51, '2026_04_13_094938_add_service_id_to_validations_table', 38),
(52, '2026_04_13_095327_add_missing_columns_to_validations_table', 39),
(53, '2026_04_13_100441_add_missing_columns_to_evaluations_table', 40),
(54, '2026_04_13_104105_add_token_acces_to_users_table', 41),
(55, '2026_04_14_072731_add_date_to_taches_table', 42);

-- --------------------------------------------------------

--
-- Structure de la table `offres`
--

CREATE TABLE `offres` (
  `id` bigint UNSIGNED NOT NULL,
  `entreprise_id` bigint UNSIGNED DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stage',
  `duree` int DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `competences` text COLLATE utf8mb4_unicode_ci,
  `conditions` text COLLATE utf8mb4_unicode_ci,
  `gratification` decimal(10,2) DEFAULT NULL,
  `places` int NOT NULL DEFAULT '1',
  `date_limite` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `presences`
--

CREATE TABLE `presences` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `stage_id` bigint UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `heure_depart` time DEFAULT NULL,
  `statut` enum('present','absent','retard','justifie') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `justification` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `heures_travaillees` decimal(5,2) NOT NULL DEFAULT '0.00',
  `est_present` tinyint(1) NOT NULL DEFAULT '1',
  `est_justifie` tinyint(1) NOT NULL DEFAULT '0',
  `motif_absence` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `presences`
--

INSERT INTO `presences` (`id`, `user_id`, `stage_id`, `date`, `heure_arrivee`, `heure_depart`, `statut`, `justification`, `created_at`, `updated_at`, `deleted_at`, `heures_travaillees`, `est_present`, `est_justifie`, `motif_absence`) VALUES
(1, 2, 1, '2026-03-29', '06:17:35', NULL, 'present', NULL, '2026-03-29 03:17:35', '2026-03-29 03:17:35', NULL, '0.00', 1, 0, NULL),
(2, 11, 2, '2026-04-14', '07:00:07', '07:00:26', 'present', NULL, '2026-04-14 04:00:07', '2026-04-14 04:00:26', NULL, '0.00', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sanctions`
--

CREATE TABLE `sanctions` (
  `id` bigint UNSIGNED NOT NULL,
  `stagiaire_id` bigint UNSIGNED NOT NULL,
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('avertissement','suspension','exclusion','retenue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'avertissement',
  `motif` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gravite` enum('faible','moyenne','elevee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moyenne',
  `duree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('actif','termine','en_attente','en_appel') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `cree_par` bigint UNSIGNED DEFAULT NULL,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sanctions`
--

INSERT INTO `sanctions` (`id`, `stagiaire_id`, `service_id`, `type`, `motif`, `gravite`, `duree`, `statut`, `cree_par`, `date_debut`, `date_fin`, `commentaire`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'avertissement', 'kjhcuyyitytyttyututui', 'moyenne', NULL, 'actif', 10, NULL, NULL, NULL, '2026-04-09 07:58:58', '2026-04-09 07:57:55', '2026-04-09 07:58:58'),
(2, 2, NULL, 'avertissement', 'liiiiiiiiiiiiiiii', 'moyenne', NULL, 'actif', 10, NULL, NULL, NULL, NULL, '2026-04-09 08:00:21', '2026-04-09 08:00:21'),
(3, 1, NULL, 'exclusion', 'koiuytd;ojhf', 'elevee', NULL, 'en_appel', 10, NULL, NULL, NULL, NULL, '2026-04-09 08:02:46', '2026-04-09 08:03:00');

-- --------------------------------------------------------

--
-- Structure de la table `semaines_validees`
--

CREATE TABLE `semaines_validees` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `semaine` int NOT NULL,
  `annee` int NOT NULL,
  `validee_le` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `semaines_validees`
--

INSERT INTO `semaines_validees` (`id`, `user_id`, `semaine`, `annee`, `validee_le`, `created_at`, `updated_at`) VALUES
(1, 2, 13, 2026, '2026-03-28 21:49:15', '2026-03-28 21:49:15', '2026-03-28 21:49:15');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` bigint UNSIGNED NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('actif','inactif','en-attente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `tags` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsable_id` bigint UNSIGNED DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `nom`, `code`, `description`, `statut`, `tags`, `responsable_id`, `email`, `telephone`, `adresse`, `logo`, `is_active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'j', NULL, 'k', 'actif', NULL, 7, NULL, NULL, NULL, NULL, 1, '2026-04-09 07:53:38', '2026-04-09 07:35:24', '2026-04-09 07:53:38'),
(2, 'oi', NULL, 'k', 'actif', NULL, 7, NULL, NULL, NULL, NULL, 1, NULL, '2026-04-09 07:53:50', '2026-04-09 07:53:50'),
(3, 'u', NULL, 'k', 'actif', NULL, 7, NULL, NULL, NULL, NULL, 1, NULL, '2026-04-09 07:56:32', '2026-04-09 07:56:32');

-- --------------------------------------------------------

--
-- Structure de la table `stages`
--

CREATE TABLE `stages` (
  `id` bigint UNSIGNED NOT NULL,
  `candidat_id` bigint UNSIGNED NOT NULL,
  `tuteur_id` bigint UNSIGNED DEFAULT NULL,
  `entreprise_id` bigint UNSIGNED DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entreprise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `objectifs` text COLLATE utf8mb4_unicode_ci,
  `competences` text COLLATE utf8mb4_unicode_ci,
  `duree_hebdomadaire` int DEFAULT NULL,
  `gratification` decimal(10,2) DEFAULT NULL,
  `convention_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stages`
--

INSERT INTO `stages` (`id`, `candidat_id`, `tuteur_id`, `entreprise_id`, `titre`, `entreprise`, `description`, `date_debut`, `date_fin`, `statut`, `lieu`, `service`, `objectifs`, `competences`, `duree_hebdomadaire`, `gratification`, `convention_path`, `commentaire`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 4, NULL, 'Stage de rg g', 'usvpa', NULL, '2026-02-26', '2026-05-13', 'en_cours', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-28 19:12:03', '2026-03-28 20:50:17', NULL),
(2, 11, NULL, NULL, 'Stage de testdd d', NULL, NULL, '2026-04-10', '2026-05-10', 'en_cours', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 05:11:19', '2026-04-13 05:11:19', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

CREATE TABLE `taches` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date NOT NULL,
  `priorite` enum('high','medium','low') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `categorie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'tache',
  `echeance` date DEFAULT NULL,
  `jour_semaine` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terminee` tinyint(1) DEFAULT '0',
  `terminee_le` timestamp NULL DEFAULT NULL,
  `date_fin` timestamp NULL DEFAULT NULL,
  `cree_par_tuteur` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `taches`
--

INSERT INTO `taches` (`id`, `user_id`, `titre`, `description`, `date`, `priorite`, `categorie`, `echeance`, `jour_semaine`, `terminee`, `terminee_le`, `date_fin`, `cree_par_tuteur`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 2, 'f', 'f', '2026-04-14', 'medium', 'tache', NULL, NULL, 1, NULL, NULL, 0, '2026-03-28 21:18:22', '2026-03-29 03:50:39', '2026-03-29 03:50:39'),
(6, 2, 'e', 'e', '2026-04-14', 'medium', 'tache', '2026-03-23', NULL, 1, NULL, NULL, 0, '2026-03-28 21:27:23', '2026-03-29 03:50:45', '2026-03-29 03:50:45'),
(7, 2, 'd', 'd', '2026-04-14', 'medium', 'tache', '2026-03-24', NULL, 1, NULL, NULL, 0, '2026-03-28 21:48:27', '2026-03-29 03:50:48', '2026-03-29 03:50:48'),
(8, 2, 'd', 'd', '2026-04-14', 'medium', 'tache', '2026-03-25', NULL, 1, NULL, NULL, 0, '2026-03-28 21:48:37', '2026-03-29 03:50:34', '2026-03-29 03:50:34'),
(9, 2, 'dv', 'dv', '2026-04-14', 'medium', 'tache', '2026-03-26', NULL, 1, NULL, NULL, 0, '2026-03-28 21:48:44', '2026-03-29 03:50:30', '2026-03-29 03:50:30'),
(10, 2, 'dv', 'vd', '2026-04-14', 'medium', 'tache', '2026-03-27', NULL, 1, NULL, NULL, 0, '2026-03-28 21:48:50', '2026-03-29 03:50:27', '2026-03-29 03:50:27'),
(11, 3, 'g', 'g', '2026-04-14', 'medium', 'tache', '2024-12-30', NULL, 0, NULL, NULL, 0, '2026-03-28 22:02:20', '2026-03-28 22:02:20', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'candidat',
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `tuteur_id` bigint UNSIGNED DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `entreprise` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `formation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `dossier_valide` tinyint(1) NOT NULL DEFAULT '0',
  `token_acces` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_validation_dossier` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `departement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poste` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `universite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bureau` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_stagiaires` int NOT NULL DEFAULT '8',
  `experience` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disponibilites` text COLLATE utf8mb4_unicode_ci,
  `expertises` json DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `role`, `service_id`, `tuteur_id`, `phone`, `avatar`, `address`, `entreprise`, `formation`, `is_active`, `dossier_valide`, `token_acces`, `date_validation_dossier`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `birth_date`, `bio`, `departement`, `poste`, `universite`, `bureau`, `max_stagiaires`, `experience`, `linkedin`, `disponibilites`, `expertises`, `last_login_at`) VALUES
(1, 'fgh h', 'fgh', 'h', 't@gmail.com', NULL, '$2y$12$g9U121bNBB6BRgO6kJdoweZ4QJQ4WzbhBVAYLZKr80QFRe850unDq', 'candidat', NULL, 4, '765', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 'actif', NULL, '2026-03-25 05:20:49', '2026-04-09 08:07:00', '2026-04-09 08:07:00', NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, NULL),
(2, 'rgt g', 'rgt', 'g', 'g@gmail.com', NULL, '$2y$12$tGMuYo1NfR/qMZ8oAmz/eOz5F9OPN108nmhjzlGeuAgyrqB8cxVZ6', 'candidat', NULL, 4, '23456', NULL, NULL, 'usvpa', 'dev', 1, 0, NULL, NULL, NULL, NULL, '2026-03-25 05:35:47', '2026-04-14 12:21:33', '2026-04-14 12:21:33', NULL, NULL, 'info', NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-14 05:52:43'),
(3, 'Godin Hermann', 'Godin', 'Hermann', 'Gd@gmail.com', NULL, '$2y$12$UsU0OPaRtRNHxTLIQg.05OYILkIvvfdXEd//eHkSemrnsg5DASN16', 'candidat', NULL, 4, '456789', 'avatars/faK2GmXPeWxkzT7ae6NLDIPsJPGqYbsMU793xPVw.png', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-03-26 03:24:57', '2026-04-14 12:21:11', '2026-04-14 12:21:11', NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, NULL),
(4, 'testfu f', 'testfu', 'f', 'jo@gmail.com', NULL, '$2y$12$3O2G.Lrq4sZkZZQ29MWyQOfQ9197ylbJZL/mflIPLAp0.T5PwTovK', 'tuteur', NULL, NULL, '4567', 'avatars/pn7rVhS1Myt8ZvkzsQj32RnNV10FWVuh2MyRSSRh.png', 'asdfghj', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-03-26 05:22:44', '2026-04-14 05:29:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-14 05:29:22'),
(6, 'gg g', 'gg', 'g', 'gr@gmail.com', NULL, '$2y$12$8N9YVZlN5fbgE9Y2xbOt4OXRY8R9Cr5j2kiLZNu0awSk9pgrz0PDO', 'tuteur', NULL, NULL, '34567', 'avatars/YQXLeoeWGgLjUYLYm7WicxUkx6olUSolvlqDBs8P.png', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-03-28 17:35:14', '2026-03-28 17:35:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, NULL),
(7, 'ert sdfg', 'kkkk', 'sdfg', 're@gmail.com', NULL, '$2y$12$4TpHby2.uH4s7dQQ3AEO/u5DIpdypWKxkZPCojm1HR5bOK8mdQtxq', 'responsable', NULL, NULL, '4567', 'avatars/DXfCPrawO1jMMWgMx3fN8r0DMc8eVtpLyQ59DM6J.png', 'kjhv', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-03-28 17:40:04', '2026-03-28 18:36:11', NULL, '2026-02-27', 'mjhgfd', 'lkjhgf', NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, NULL),
(10, 'hhdd d', 'Randria', 'Nomena', 'chef@gmail.com', NULL, '$2y$12$U2dHzMQO9J9Cg9cmoFTsq.2mrv9JOFa2wyNQPtTYxLdBAigQh2Mn2', 'chef-service', 2, NULL, '9876546', 'avatars/pU3htUxEPT6q8Cl4NOw8AHcmuOZGGrMhcrciCLlT.png', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-04-08 14:23:03', '2026-04-14 05:47:36', NULL, NULL, NULL, NULL, NULL, NULL, '54', 8, NULL, NULL, NULL, NULL, '2026-04-14 05:47:36'),
(11, 'testdd d', 'testdd', 'd', 'candi@gmail.com', NULL, '$2y$12$CO3dIp8UB7g.DAqFfBMsce3ZmQxUR/6mxj18wJPbJo6r8Q6RvG5F6', 'candidat', NULL, NULL, '34567', NULL, 'ttttttttt', NULL, 't', 1, 1, NULL, '2026-04-13 07:59:53', NULL, NULL, '2026-04-13 04:56:07', '2026-04-14 12:22:05', '2026-04-14 12:22:05', NULL, 'kk', NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-14 03:49:16'),
(12, 'ty tt', 'ty', 'tt', 'respo@gmail.com', NULL, '$2y$12$7jX5eW.FgWWSr2dr7pbtLOAmg/YA04IgOgvQTRpr2Nz7RuAnx05xC', 'responsable', NULL, NULL, 'd', 'avatars/fZebzZpkBlDgfCZm3qliMMxIfBnHj8sstBvsC9G2.png', NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-04-13 05:09:08', '2026-04-14 05:47:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-14 05:47:05'),
(13, 'Her d', 'Her', 'd', 'stagi@gmail.com', NULL, '$2y$12$dPkLGTRZLGbWKUzVMOQZYe3iGFq7YxG7s7ORs1sW1ZrR7wcESfATi', 'candidat', NULL, NULL, 'oi', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-04-13 08:01:01', '2026-04-14 12:22:12', '2026-04-14 12:22:12', NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, NULL),
(14, 'sfgh ertyj', 'sfgh', 'ertyj', 'stag@gmail.com', NULL, '$2y$12$1AtF3ZpAM8cnQGhx1gpkGe/JW9yZfpP7s6F5TcGhtnm0lFROtd53.', 'candidat', NULL, NULL, '34tyu', NULL, 'as', NULL, 'd', 1, 1, NULL, '2026-04-13 08:11:14', NULL, NULL, '2026-04-13 08:01:49', '2026-04-14 12:21:38', '2026-04-14 12:21:38', '2026-04-11', NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-13 08:17:38'),
(15, 'rr rr', 'rr', 'rr', 'rest@gmail.com', NULL, '$2y$12$eqj0kFS5K6O6LBCwp1sAOuV2f4YEwZL1xQ4hIn4zR.QOGwy6IuGNG', 'candidat', NULL, NULL, '98765', NULL, 'ert', NULL, NULL, 1, 1, NULL, '2026-04-13 08:28:22', NULL, NULL, '2026-04-13 08:20:25', '2026-04-14 12:21:05', '2026-04-14 12:21:05', '2026-04-23', NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-13 08:28:40'),
(16, 'Hermann Sylvano GODIN', 'Hermann Sylvano', 'GODIN', 'hermanngodin88@gmail.com', NULL, '$2y$12$n6NYggAvbVhfUQKwdEtRK.XcbR3ZZFPGIns.mYYiBR9a8GESpl2J.', 'candidat', NULL, NULL, '0388170635', NULL, 'Antananarivo', NULL, 'DTS Informatique', 1, 0, NULL, NULL, NULL, NULL, '2026-04-14 12:15:55', '2026-04-14 12:21:18', '2026-04-14 12:21:18', '2006-08-08', NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-14 12:16:03'),
(17, 'responsable DIDN', 'responsable', 'DIDN', 'responsable@gmail.com', NULL, '$2y$12$Goh90j/3W5u3UJdaQS.nveONJkdBGDK19t2fGKRJ3U68/4ASMgGjy', 'responsable', NULL, NULL, '03455555555', NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-04-14 12:20:11', '2026-04-14 12:20:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-14 12:20:39'),
(18, 'ty yjmm', 'ty', 'yjmm', 'stagiq@gmail.com', NULL, '$2y$12$UMIGPbq4FZ7m8MilVx5BMuTKLzDshfh5BKmT1ig3E/2jxGmNgpjFi', 'candidat', NULL, NULL, '09876', NULL, 'hhh', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '2026-04-16 03:30:17', '2026-04-20 06:37:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL, '2026-04-20 06:37:08');

-- --------------------------------------------------------

--
-- Structure de la table `validations`
--

CREATE TABLE `validations` (
  `id` bigint UNSIGNED NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('en_attente','approuvee','refusee','en_cours') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `urgent` tinyint(1) NOT NULL DEFAULT '0',
  `valide_par` bigint UNSIGNED DEFAULT NULL,
  `motif_rejet` text COLLATE utf8mb4_unicode_ci,
  `date_reponse` datetime DEFAULT NULL,
  `priorite` enum('basse','moyenne','haute','urgente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'moyenne',
  `echeance` datetime DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'document',
  `icone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `validee_par` bigint UNSIGNED DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activites`
--
ALTER TABLE `activites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activites_user_id_index` (`user_id`),
  ADD KEY `activites_created_at_index` (`created_at`),
  ADD KEY `activites_type_lu_index` (`type`,`lu`);

--
-- Index pour la table `bilans`
--
ALTER TABLE `bilans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bilans_stagiaire_id_foreign` (`stagiaire_id`),
  ADD KEY `bilans_tuteur_id_foreign` (`tuteur_id`),
  ADD KEY `bilans_valide_par_foreign` (`valide_par`),
  ADD KEY `bilans_statut_stagiaire_id_index` (`statut`,`stagiaire_id`),
  ADD KEY `bilans_date_debut_index` (`date_debut`),
  ADD KEY `bilans_date_fin_index` (`date_fin`),
  ADD KEY `bilans_service_id_foreign` (`service_id`);

--
-- Index pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidats_user_id_foreign` (`user_id`);

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidatures_candidat_id_foreign` (`candidat_id`);

--
-- Index pour la table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_user_id_foreign` (`user_id`),
  ADD KEY `documents_uploaded_by_foreign` (`uploaded_by`);

--
-- Index pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluations_stage_id_foreign` (`stage_id`),
  ADD KEY `evaluations_evaluateur_id_foreign` (`evaluateur_id`),
  ADD KEY `evaluations_candidat_id_statut_index` (`candidat_id`,`statut`),
  ADD KEY `evaluations_date_evaluation_index` (`date_evaluation`),
  ADD KEY `evaluations_stagiaire_id_foreign` (`stagiaire_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `journaux`
--
ALTER TABLE `journaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journaux_user_id_index` (`user_id`),
  ADD KEY `journaux_statut_index` (`statut`),
  ADD KEY `journaux_created_at_index` (`created_at`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `offres`
--
ALTER TABLE `offres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `presences`
--
ALTER TABLE `presences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `presences_user_id_stage_id_date_unique` (`user_id`,`stage_id`,`date`),
  ADD KEY `presences_user_id_date_index` (`user_id`,`date`);

--
-- Index pour la table `sanctions`
--
ALTER TABLE `sanctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sanctions_service_id_foreign` (`service_id`),
  ADD KEY `sanctions_cree_par_foreign` (`cree_par`),
  ADD KEY `sanctions_stagiaire_id_statut_index` (`stagiaire_id`,`statut`),
  ADD KEY `sanctions_type_index` (`type`),
  ADD KEY `sanctions_gravite_index` (`gravite`);

--
-- Index pour la table `semaines_validees`
--
ALTER TABLE `semaines_validees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `semaines_validees_user_id_semaine_annee_unique` (`user_id`,`semaine`,`annee`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_code_unique` (`code`),
  ADD KEY `services_nom_index` (`nom`),
  ADD KEY `services_responsable_id_index` (`responsable_id`);

--
-- Index pour la table `stages`
--
ALTER TABLE `stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stages_candidat_id_foreign` (`candidat_id`),
  ADD KEY `stages_tuteur_id_foreign` (`tuteur_id`);

--
-- Index pour la table `taches`
--
ALTER TABLE `taches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_tuteur_id_foreign` (`tuteur_id`),
  ADD KEY `users_service_id_foreign` (`service_id`),
  ADD KEY `users_token_acces_index` (`token_acces`);

--
-- Index pour la table `validations`
--
ALTER TABLE `validations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `validations_validee_par_foreign` (`validee_par`),
  ADD KEY `validations_statut_priorite_index` (`statut`,`priorite`),
  ADD KEY `validations_echeance_index` (`echeance`),
  ADD KEY `validations_user_id_index` (`user_id`),
  ADD KEY `validations_service_id_foreign` (`service_id`),
  ADD KEY `validations_valide_par_foreign` (`valide_par`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activites`
--
ALTER TABLE `activites`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `bilans`
--
ALTER TABLE `bilans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `candidats`
--
ALTER TABLE `candidats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `candidatures`
--
ALTER TABLE `candidatures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `journaux`
--
ALTER TABLE `journaux`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT pour la table `offres`
--
ALTER TABLE `offres`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `presences`
--
ALTER TABLE `presences`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `sanctions`
--
ALTER TABLE `sanctions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `semaines_validees`
--
ALTER TABLE `semaines_validees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `stages`
--
ALTER TABLE `stages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `taches`
--
ALTER TABLE `taches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `validations`
--
ALTER TABLE `validations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activites`
--
ALTER TABLE `activites`
  ADD CONSTRAINT `activites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `bilans`
--
ALTER TABLE `bilans`
  ADD CONSTRAINT `bilans_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bilans_stagiaire_id_foreign` FOREIGN KEY (`stagiaire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bilans_tuteur_id_foreign` FOREIGN KEY (`tuteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bilans_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD CONSTRAINT `candidats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_evaluateur_id_foreign` FOREIGN KEY (`evaluateur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `evaluations_stage_id_foreign` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `evaluations_stagiaire_id_foreign` FOREIGN KEY (`stagiaire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `journaux`
--
ALTER TABLE `journaux`
  ADD CONSTRAINT `journaux_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `presences`
--
ALTER TABLE `presences`
  ADD CONSTRAINT `presences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sanctions`
--
ALTER TABLE `sanctions`
  ADD CONSTRAINT `sanctions_cree_par_foreign` FOREIGN KEY (`cree_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanctions_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sanctions_stagiaire_id_foreign` FOREIGN KEY (`stagiaire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `semaines_validees`
--
ALTER TABLE `semaines_validees`
  ADD CONSTRAINT `semaines_validees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `stages`
--
ALTER TABLE `stages`
  ADD CONSTRAINT `stages_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stages_tuteur_id_foreign` FOREIGN KEY (`tuteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `taches`
--
ALTER TABLE `taches`
  ADD CONSTRAINT `taches_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_tuteur_id_foreign` FOREIGN KEY (`tuteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `validations`
--
ALTER TABLE `validations`
  ADD CONSTRAINT `validations_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `validations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `validations_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `validations_validee_par_foreign` FOREIGN KEY (`validee_par`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
