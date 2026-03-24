<?php

namespace App\Models;

class AccountModel extends Model{

    CONST ADMIN = "debian-sys-maint";
    CONST PASS = "LYK8WN3Oup7UxkZW";

    public function __construct($info = null){
        if(is_null($info)){
            $this->data=[];
            $this->dbh= new \PDO("mysql:host=localhost;dbname = stage4all", self::ADMIN, self::PASS);
        }else{
            $this->data = $info;
            $this->dbh = new \PDO("mysql:host=localhost;dbname = stage4all", $this->data["email"], $this->data["password"]);
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
        $roles_autorises = ["etudiant"];
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
        $this->data = ["compte_id" => 1, "pilote_id" => NULL, "nom" => $nom, "prenom" => $prenom, "genre" => $genre
        , "mdp" => $password, "email" => $email];
    }

    public function getData(){
        return $this->data;
    }

    public function sendToDataBase($data = null){
        if(isset($data)){
            $this->data = $data;
        }

    }
}