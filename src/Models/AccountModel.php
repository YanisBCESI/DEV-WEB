<?php

namespace App\Models;

class AccountModel extends Model{


//(Mise a jour des templates, du controleur legal et de AccountModel)
    public function __construct($info = null){
        if(is_null($info)){
            $this->data=[];
            $this->dbh= new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
        }else{
            $this->data = $info;
            $this->dbh = new \PDO("mysql:host=localhost;dbname=stage4all", $this->data["email"], $this->data["password"]);
        }
    }

    public function retrieveData(){
        if($_SERVER['REQUEST_METHOD'] !== "POST"){
            http_response_code(405);
            return("Méthode non autorisée");
        }
        $nom = trim($_POST["lastname"] ?? "");
        $prenom = trim($_POST['surname'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $genres_autorises = ["femme", "homme", "autre"];
        $roles_autorises = ["etudiant", "alternant"];
        if($nom === "" or 
           $prenom ==="" or
           !in_array($genre, $genres_autorises, true) or
           !in_array($role, $roles_autorises, true) or
           !filter_var($email, FILTER_VALIDATE_EMAIL) or
           $password === ""){
            return("Formulaire invalide");
        }
        if($password !== $passwordConfirm){
            return("Les mots de passe ne correspondent pas");
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        $this->data = ["compte_id" => "etudiant", "pilote_id" => NULL, "nom" => $nom, "prenom" => $prenom, "genre" => $genre
        , "mdp" => $password, "email" => $email];
    }

    public function getData(){
        return $this->data;
    }

    public function getStudentProfile(int $studentId): array{
        $defaults = [
            "nom" => $_SESSION["student"]["nom"] ?? "Non renseigné",
            "prenom" => $_SESSION["student"]["prenom"] ?? "Non renseigné",
            "email" => $_SESSION["student"]["email"] ?? "Non renseigné",
            "etablissement" => "Non renseigné",
            "tuteur" => "Non renseigné",
        ];

        try {
            $stmt = $this->dbh->prepare(
                "SELECT
                    etudiants.nom,
                    etudiants.prenom,
                    comptes.email,
                    COALESCE(etudiants.promotion, '') AS etablissement,
                    CONCAT(COALESCE(pilotes.prenom, ''), ' ', COALESCE(pilotes.nom, '')) AS tuteur
                FROM etudiants
                INNER JOIN comptes ON comptes.id = etudiants.compte_id
                LEFT JOIN pilotes ON pilotes.id = etudiants.pilote_id
                WHERE etudiants.id = :student_id
                LIMIT 1"
            );
            $stmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
            $stmt->execute();

            $student = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($student) {
                return [
                    "nom" => $student["nom"] ?: $defaults["nom"],
                    "prenom" => $student["prenom"] ?: $defaults["prenom"],
                    "email" => $student["email"] ?: $defaults["email"],
                    "etablissement" => trim((string) ($student["etablissement"] ?? "")) !== "" ? $student["etablissement"] : $defaults["etablissement"],
                    "tuteur" => trim((string) ($student["tuteur"] ?? "")) !== "" ? trim($student["tuteur"]) : $defaults["tuteur"],
                ];
            }
        } catch (\PDOException $exception) {
        }

        $legacyStmt = $this->dbh->prepare(
            "SELECT id, nom, prenom, email, pilote_id
             FROM etudiants
             WHERE id = :student_id
             LIMIT 1"
        );
        $legacyStmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
        $legacyStmt->execute();

        $legacyStudent = $legacyStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$legacyStudent) {
            return $defaults;
        }

        $tuteur = $defaults["tuteur"];

        if (!empty($legacyStudent["pilote_id"])) {
            $piloteStmt = $this->dbh->prepare("SELECT nom, prenom FROM pilotes WHERE id = :pilot_id LIMIT 1");
            $piloteStmt->bindValue(":pilot_id", (int) $legacyStudent["pilote_id"], \PDO::PARAM_INT);
            $piloteStmt->execute();
            $pilote = $piloteStmt->fetch(\PDO::FETCH_ASSOC);

            if ($pilote) {
                $tuteur = trim(($pilote["prenom"] ?? "") . " " . ($pilote["nom"] ?? ""));
            }
        }

        return [
            "nom" => $legacyStudent["nom"] ?: $defaults["nom"],
            "prenom" => $legacyStudent["prenom"] ?: $defaults["prenom"],
            "email" => $legacyStudent["email"] ?: $defaults["email"],
            "etablissement" => $defaults["etablissement"],
            "tuteur" => $tuteur !== "" ? $tuteur : $defaults["tuteur"],
        ];
    }

    public function authenticateStudent(string $email, string $password): ?array{
        $normalizedEmail = trim($email);

        $stmt = $this->dbh->prepare(
            "SELECT
                etudiants.id,
                etudiants.nom,
                etudiants.prenom,
                comptes.email,
                comptes.mot_de_passe,
                comptes.role_id,
                comptes.actif
            FROM etudiants
            INNER JOIN comptes ON comptes.id = etudiants.compte_id
            WHERE comptes.email = :email
            LIMIT 1"
        );
        $stmt->bindValue(":email", $normalizedEmail);
        $stmt->execute();

        $student = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($student) {
            if ((int) ($student["role_id"] ?? 0) !== 3) {
                return null;
            }

            if ((int) ($student["actif"] ?? 0) !== 1) {
                return null;
            }

            if (!password_verify($password, $student["mot_de_passe"] ?? "")) {
                return null;
            }

            return [
                "id" => (int) $student["id"],
                "role" => "etudiant",
                "nom" => $student["nom"],
                "prenom" => $student["prenom"],
                "email" => $student["email"],
            ];
        }

        $legacyStmt = $this->dbh->prepare(
            "SELECT id, nom, prenom, email, mdp
             FROM etudiants
             WHERE compte_id = 'etudiant' AND email = :email
             LIMIT 1"
        );
        $legacyStmt->bindValue(":email", $normalizedEmail);
        $legacyStmt->execute();

        $legacyStudent = $legacyStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$legacyStudent) {
            return null;
        }

        if (!password_verify($password, $legacyStudent["mdp"] ?? "")) {
            return null;
        }

        return [
            "id" => (int) $legacyStudent["id"],
            "role" => "etudiant",
            "nom" => $legacyStudent["nom"],
            "prenom" => $legacyStudent["prenom"],
            "email" => $legacyStudent["email"],
        ];
    }

    public function sendToDataBase($data = null){
        if(isset($data)){
            $this->data = $data;
        }
        $stmt = $this->dbh->prepare("INSERT INTO etudiants (compte_id, pilote_id, nom, prenom, genre, mdp, email) VALUES (:compte_id, :pilote_id, :nom, :prenom, :genre, :mdp, :email);");
        $stmt->bindParam(":compte_id", $this->data["compte_id"]);
        $stmt->bindParam(":pilote_id", $this->data["pilote_id"]);
        $stmt->bindParam(":nom", $this->data["nom"]);
        $stmt->bindParam(":prenom", $this->data["prenom"]);
        $stmt->bindParam(":genre", $this->data["genre"]);
        $stmt->bindParam(":mdp", $this->data["mdp"]);
        $stmt->bindParam(":email", $this->data["email"]);
        
        $stmt->execute();
    }
}
