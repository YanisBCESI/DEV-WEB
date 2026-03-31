<?php

namespace App\Controllers;

use App\Models\AdminModel;

class AdminController extends Controller{
    protected $admin_model = null;

    public function __construct($templateEngine){
        $this->admin_model = new AdminModel();
        $this->templateEngine = $templateEngine;
    }

    private function requireLoggedAdmin(): array{
        if (!isset($_SESSION["admin"]["id"])) {
            header("Location: ?uri=connect");
            exit;
        }

        return $_SESSION["admin"];
    }

    private function getPilotFormMessage(?string $status): ?array{
        return match ($status) {
            "created" => [
                "type" => "success",
                "text" => "Le compte pilote a ete cree avec succes.",
            ],
            "invalid_data" => [
                "type" => "error",
                "text" => "Merci de remplir correctement tous les champs obligatoires.",
            ],
            "email_exists" => [
                "type" => "error",
                "text" => "Cette adresse e-mail est deja utilisee.",
            ],
            "error" => [
                "type" => "error",
                "text" => "La creation du compte pilote a echoue.",
            ],
            default => null,
        };
    }

    public function pilotsPage(): void{
        $this->requireLoggedAdmin();
        $pilotMessage = $this->getPilotFormMessage($_GET["pilot_status"] ?? null);

        echo $this->templateEngine->render("admin_pilots.html.twig", [
            "pilots" => $this->admin_model->getAllPilots(),
            "pilot_form_message" => $pilotMessage,
        ]);
    }

    public function createPilotAccount(): void{
        $this->requireLoggedAdmin();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=admin_pilots");
            exit;
        }

        $nom = trim($_POST["nom"] ?? "");
        $prenom = trim($_POST["prenom"] ?? "");
        $genre = trim($_POST["genre"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $telephone = trim($_POST["telephone"] ?? "");
        $password = $_POST["password"] ?? "";
        $passwordConfirm = $_POST["password_confirm"] ?? "";
        $allowedGenres = ["femme", "homme", "autre"];

        if (
            $nom === ""
            || $prenom === ""
            || !in_array($genre, $allowedGenres, true)
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $password === ""
            || strlen($password) < 6
            || $password !== $passwordConfirm
        ) {
            header("Location: ?uri=admin_pilots&pilot_status=invalid_data#pilot-create-form");
            exit;
        }

        $status = $this->admin_model->createPilotAccount([
            "nom" => $nom,
            "prenom" => $prenom,
            "genre" => $genre,
            "email" => $email,
            "telephone" => $telephone,
            "password" => $password,
        ]);

        header("Location: ?uri=admin_pilots&pilot_status=" . $status . "#pilot-create-form");
        exit;
    }
}
