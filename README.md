# Stage 4 All

Stage 4 All est une plateforme web développée en PHP dans le cadre d’un projet de développement web.  
Le site a pour objectif de mettre en relation plusieurs profils autour des offres de stage, d’alternance et d’emploi :

- les **étudiants**
- les **professeurs**
- les **entreprises**

Le projet permet :
- l’inscription de différents types d’utilisateurs
- le dépôt de CV en **PDF uniquement**
- l’enregistrement des données dans une base **MySQL**
- la publication d’offres par les entreprises
- l’affichage des offres disponibles sur le site

---

## Objectifs du projet

L’objectif principal est de proposer une plateforme simple permettant :

- aux **étudiants** de créer un compte et déposer leur CV
- aux **professeurs** de créer un compte
- aux **entreprises** de créer un compte et publier des offres
- de centraliser les données dans une base de données relationnelle
- de structurer le projet selon une logique proche du **MVC**


 Technologies utilisées

- **PHP**
- **MySQL**
- **HTML5**
- **CSS3**
- **WSL / Ubuntu**
- **Git / GitHub**
- **Composer**
- **Twig** selon l’évolution du projet



 Fonctionnalités mises en place

### 1. Gestion des comptes
Le projet permet l’inscription de trois types de comptes :

- Étudiant
- Professeur
- Entreprise

Les informations communes sont enregistrées dans une table `comptes`, avec :
- l’email
- le mot de passe
- le rôle du compte

Les informations spécifiques sont ensuite enregistrées dans des tables dédiées :
- `etudiants`
- `professeurs`
- `entreprises`

### 2. Dépôt de CV
Lors de l’inscription d’un étudiant :

- seul le format **PDF** est accepté
- la taille maximale autorisée est de **2 Mo**
- le fichier est renommé automatiquement
- le fichier est stocké dans un dossier dédié
- les métadonnées du CV sont enregistrées en base de données

### 3. Publication d’offres
Les entreprises peuvent publier des offres contenant :

- un titre
- un type de contrat
- un secteur
- une localisation
- une description
- les compétences recherchées
- une rémunération éventuelle
- une date de début éventuelle

Les offres sont enregistrées dans la table `offres`.

### 4. Affichage des offres
Une page dédiée permet d’afficher les offres publiées avec les informations principales de l’entreprise.

---

## Structure de la base de données

La base de données utilisée est `stage4all`.

Elle contient les tables principales suivantes :

- `comptes`
- `etudiants`
- `professeurs`
- `entreprises`
- `offres`

### Logique générale

- `comptes` contient les informations de connexion et le rôle
- `etudiants`, `professeurs` et `entreprises` contiennent les informations spécifiques
- `offres` est liée aux entreprises

---

## Arborescence du projet

Exemple d’organisation du projet :

```bash
Projet_WEB/
├── assets/
├── src/
│   ├── Controllers/
│   └── Models/
├── templates/
├── vendor/
├── config.php
├── database.sql
├── index.php
├── depot.php
├── pagination.php
├── inscrire_User.html
├── inscrire_Entreprise.html
├── deposer_offre.html
├── offres.php
├── traitement_inscription_user.php
├── traitement_inscription_entreprise.php
├── traitement_offre.php
├── style.css
├── style_Connection.css
├── style_depot.css
├── style_offre.css
└── README.md
