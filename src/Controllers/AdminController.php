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
        if (isset($_SESSION["pilot"]["id"])) {
            header("Location: ?uri=admin_students&student_status=unauthorized");
            exit;
        }

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
            "deleted" => [
                "type" => "success",
                "text" => "Le compte pilote a ete supprime.",
            ],
            "invalid_data" => [
                "type" => "error",
                "text" => "Merci de remplir correctement tous les champs obligatoires.",
            ],
            "password_mismatch" => [
                "type" => "error",
                "text" => "Les mots de passe ne correspondent pas.",
            ],
            "email_exists" => [
                "type" => "error",
                "text" => "Cette adresse e-mail est deja utilisee.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "Le compte pilote demande est introuvable.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'operation sur le compte pilote a echoue.",
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
        $genre = strtolower(trim($_POST["genre"] ?? ""));
        $email = trim($_POST["email"] ?? "");
        $telephone = trim($_POST["telephone"] ?? "");
        $password = $_POST["password"] ?? "";
        $passwordConfirm = $_POST["password_confirm"] ?? "";
        $allowedGenres = ["femme", "homme", "autre"];

        if ($password !== $passwordConfirm) {
            header("Location: ?uri=admin_pilots&pilot_status=password_mismatch#pilot-create-form");
            exit;
        }

        if (
            $nom === ""
            || $prenom === ""
            || !in_array($genre, $allowedGenres, true)
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $password === ""
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

    public function deletePilotAccount(): void{
        $this->requireLoggedAdmin();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=admin_pilots");
            exit;
        }

        $pilotId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($pilotId <= 0) {
            header("Location: ?uri=admin_pilots&pilot_status=not_found");
            exit;
        }

        $status = $this->admin_model->deletePilotAccount($pilotId);

        header("Location: ?uri=admin_pilots&pilot_status=" . $status);
        exit;
    }
}
