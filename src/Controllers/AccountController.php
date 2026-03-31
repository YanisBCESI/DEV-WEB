<?php

namespace App\Controllers;

use App\Models\AccountModel;

class AccountController extends Controller{
    protected $account_model = null;

    public function __construct($templateEngine){
        $this->account_model = new AccountModel();
        $this->templateEngine = $templateEngine;
    }

    public function userInscriptionPage(){
        echo $this->templateEngine->render("inscrire_User.html.twig");
    }

    public function AccountInfoSent(){
        $this->account_model->retrieveData();
        $this->account_model->sendToDataBase();
        echo("Compte créé avec succès");
    }

    public function getData(){
        return $this->account_model->getData();
    }

    public function userConnexionPage(){
        echo $this->templateEngine->render('connecter_User.html.twig', [
            "login_error" => isset($_GET["error"]) && $_GET["error"] === "1",
        ]);
    }

    private function requireLoggedStudent(): int{
        if (!isset($_SESSION["student"]["id"])) {
            header("Location: ?uri=connect");
            exit;
        }

        return (int) $_SESSION["student"]["id"];
    }

    private function getUploadMessage(?string $status): ?string{
        return match ($status) {
            "success" => "Document déposé avec succès.",
            "missing" => "Aucun fichier n'a été sélectionné.",
            "invalid_type" => "Seuls les fichiers PDF et DOCX sont autorisés.",
            "too_large" => "Le fichier dépasse la taille maximale de 2 Mo.",
            "upload_failed" => "Le dépôt du fichier a échoué.",
            default => null,
        };
    }

    public function studentProfilePage(){
        $studentId = $this->requireLoggedStudent();
        $profile = $this->account_model->getStudentProfile($studentId);

        echo $this->templateEngine->render("profil_etudiant.html.twig", [
            "student_profile" => $profile,
            "upload_message" => $this->getUploadMessage($_GET["upload"] ?? null),
            "upload_success" => ($_GET["upload"] ?? null) === "success",
        ]);
    }

    public function uploadStudentDocument(){
        $studentId = $this->requireLoggedStudent();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=student_profile");
            exit;
        }

        if (!isset($_FILES["student_document"]) || $_FILES["student_document"]["error"] === UPLOAD_ERR_NO_FILE) {
            header("Location: ?uri=student_profile&upload=missing");
            exit;
        }

        $file = $_FILES["student_document"];
        $maxSize = 2 * 1024 * 1024;
        $extension = strtolower(pathinfo($file["name"] ?? "", PATHINFO_EXTENSION));
        $allowedExtensions = ["pdf", "docx"];
        $mime = mime_content_type($file["tmp_name"]);
        $allowedMimes = [
            "application/pdf",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "application/zip",
            "application/octet-stream",
        ];

        if (!in_array($extension, $allowedExtensions, true) || !in_array($mime, $allowedMimes, true)) {
            header("Location: ?uri=student_profile&upload=invalid_type");
            exit;
        }

        if (($file["size"] ?? 0) > $maxSize) {
            header("Location: ?uri=student_profile&upload=too_large");
            exit;
        }

        $targetDirectory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "student_documents";

        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0777, true) && !is_dir($targetDirectory)) {
            header("Location: ?uri=student_profile&upload=upload_failed");
            exit;
        }

        $targetName = "student_" . $studentId . "_" . bin2hex(random_bytes(8)) . "." . $extension;
        $targetPath = $targetDirectory . DIRECTORY_SEPARATOR . $targetName;

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            header("Location: ?uri=student_profile&upload=upload_failed");
            exit;
        }

        header("Location: ?uri=student_profile&upload=success");
        exit;
    }

    public function loginStudent(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=connect");
            exit;
        }

        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $student = $this->account_model->authenticateStudent($email, $password);

        if (!$student) {
            header("Location: ?uri=connect&error=1");
            exit;
        }

        $_SESSION["student"] = $student;

        header("Location: ?uri=offres");
        exit;
    }

    public function logoutStudent(){
        unset($_SESSION["student"]);
        header("Location: /");
        exit;
    }
}
