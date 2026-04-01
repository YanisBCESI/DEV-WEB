<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\AdminModel;

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
        echo("Compte cree avec succes");
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
            "success" => "Document depose avec succes.",
            "missing" => "Aucun fichier n'a ete selectionne.",
            "invalid_type" => "Format non autorise : seuls les fichiers PDF et DOCX sont acceptes.",
            "too_large" => "Fichier trop volumineux : la taille maximale autorisee est de 2 Mo.",
            "upload_failed" => "Le depot du fichier a echoue.",
            default => null,
        };
    }

    private function getStudentDocumentsDirectory(): string{
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "student_documents";
    }

    private function formatStudentDocumentLabel(int $studentId, string $storedName): string{
        $pattern = '/^student_' . preg_quote((string) $studentId, '/') . '_(?:[0-9]+_)?(?:[a-f0-9]{16}_)?/i';
        $label = preg_replace($pattern, '', $storedName);

        if (!is_string($label) || $label === '') {
            return $storedName;
        }

        return str_replace('_', ' ', $label);
    }

    private function getStudentDocuments(int $studentId): array{
        $directory = $this->getStudentDocumentsDirectory();

        if (!is_dir($directory)) {
            return [];
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . "student_" . $studentId . "_*");

        if ($files === false) {
            return [];
        }

        usort($files, static function (string $left, string $right): int {
            return filemtime($right) <=> filemtime($left);
        });

        return array_map(function (string $path) use ($studentId): array {
            $storedName = basename($path);

            return [
                "stored_name" => $storedName,
                "label" => $this->formatStudentDocumentLabel($studentId, $storedName),
            ];
        }, $files);
    }

    public function studentProfilePage(){
        $studentId = $this->requireLoggedStudent();
        $profile = $this->account_model->getStudentProfile($studentId);

        echo $this->templateEngine->render("profil_etudiant.html.twig", [
            "student_profile" => $profile,
            "student_documents" => $this->getStudentDocuments($studentId),
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

        if (($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_INI_SIZE || ($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_FORM_SIZE) {
            header("Location: ?uri=student_profile&upload=too_large");
            exit;
        }

        if (($file["error"] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            header("Location: ?uri=student_profile&upload=upload_failed");
            exit;
        }

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

        $targetDirectory = $this->getStudentDocumentsDirectory();

        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0777, true) && !is_dir($targetDirectory)) {
            header("Location: ?uri=student_profile&upload=upload_failed");
            exit;
        }

        $baseName = pathinfo($file["name"] ?? "document", PATHINFO_FILENAME);
        $sanitizedBaseName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $baseName);
        $sanitizedBaseName = trim((string) $sanitizedBaseName, '_');

        if ($sanitizedBaseName === '') {
            $sanitizedBaseName = 'document';
        }

        $targetName = "student_" . $studentId . "_" . time() . "_" . bin2hex(random_bytes(8)) . "_" . $sanitizedBaseName . "." . $extension;
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
        $adminModel = new AdminModel();
        $admin = $adminModel->authenticateAdmin($email, $password);

        if ($admin) {
            unset($_SESSION["student"]);
            unset($_SESSION["pilot"]);
            $_SESSION["admin"] = $admin;
            header("Location: ?uri=admin_students");
            exit;
        }

        $pilot = $adminModel->authenticatePilot($email, $password);

        if ($pilot) {
            unset($_SESSION["student"]);
            unset($_SESSION["admin"]);
            $_SESSION["pilot"] = $pilot;
            header("Location: ?uri=admin_students");
            exit;
        }

        $student = $this->account_model->authenticateStudent($email, $password);

        if (!$student) {
            header("Location: ?uri=connect&error=1");
            exit;
        }

        unset($_SESSION["admin"]);
        unset($_SESSION["pilot"]);
        $_SESSION["student"] = $student;

        header("Location: ?uri=offres");
        exit;
    }

    public function logoutStudent(){
        unset($_SESSION["student"]);
        unset($_SESSION["admin"]);
        unset($_SESSION["pilot"]);
        header("Location: /");
        exit;
    }
}
