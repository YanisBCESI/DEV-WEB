CREATE DATABASE IF NOT EXISTS stage4all
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE stage4all;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    genre ENUM('ms', 'mr', 'other') NOT NULL,
    statut ENUM('student', 'teacher') NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    cv_nom_original VARCHAR(255) NOT NULL,
    cv_nom_stocke VARCHAR(255) NOT NULL UNIQUE,
    cv_chemin VARCHAR(255) NOT NULL,
    cv_taille INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_entreprise VARCHAR(255) NOT NULL,
    type_entreprise ENUM('TPE', 'PME', 'ETI', 'GE') NOT NULL,
    secteur VARCHAR(255) NOT NULL,
    siret VARCHAR(14) NOT NULL UNIQUE,
    adresse VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
