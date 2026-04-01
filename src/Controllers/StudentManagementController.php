<?php

namespace App\Controllers;

use App\Models\StudentManagementModel;

class StudentManagementController extends Controller{
    protected $student_management_model = null;

    public function __construct($templateEngine){
        $this->student_management_model = new StudentManagementModel();
        $this->templateEngine = $templateEngine;
    }

    private function getLoggedManager(): array{
        if (isset($_SESSION["admin"]["id"])) {
            return $_SESSION["admin"];
        }

        if (isset($_SESSION["pilot"]["id"])) {
            return $_SESSION["pilot"];
        }

        header("Location: ?uri=connect");
        exit;
    }

    private function requireLoggedAdmin(): array{
        $manager = $this->getLoggedManager();

        if (($manager["role"] ?? null) !== "admin") {
            header("Location: ?uri=admin_students&student_status=unauthorized");
            exit;
        }

        return $manager;
    }

    private function isAdmin(array $manager): bool{
        return ($manager["role"] ?? null) === "admin";
    }

    private function getStudentListMessage(?string $status): ?array{
        return match ($status) {
            "created" => [
                "type" => "success",
                "text" => "Le compte etudiant a ete cree avec succes.",
            ],
            "updated" => [
                "type" => "success",
                "text" => "Le compte etudiant a ete mis a jour.",
            ],
            "deleted" => [
                "type" => "success",
                "text" => "Le compte etudiant a ete supprime.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "Le compte etudiant demande est introuvable.",
            ],
            "unauthorized" => [
                "type" => "error",
                "text" => "Vous n'avez pas les droits pour effectuer cette action.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'operation sur le compte etudiant a echoue.",
            ],
            default => null,
        };
    }

    private function getStudentFormMessage(?string $status): ?array{
        return match ($status) {
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
            "invalid_pilot" => [
                "type" => "error",
                "text" => "Le pilote selectionne est invalide.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "Le compte etudiant demande est introuvable.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'enregistrement du compte etudiant a echoue.",
            ],
            default => null,
        };
    }

    private function getStudentFormData(array $manager): array{
        return [
            "nom" => trim($_POST["nom"] ?? ""),
            "prenom" => trim($_POST["prenom"] ?? ""),
            "genre" => strtolower(trim($_POST["genre"] ?? "")),
            "email" => trim($_POST["email"] ?? ""),
            "pilot_id" => $this->isAdmin($manager) ? ($_POST["pilot_id"] ?? null) : ($manager["pilot_id"] ?? null),
            "password" => $_POST["password"] ?? "",
            "password_confirm" => $_POST["password_confirm"] ?? "",
        ];
    }

    private function deleteStudentDocuments(int $studentId): void{
        $directory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "student_documents";

        if (!is_dir($directory)) {
            return;
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . "student_" . $studentId . "_*");

        if ($files === false) {
            return;
        }

        foreach ($files as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    public function studentsPage(): void{
        $manager = $this->getLoggedManager();
        $search = trim($_GET["search"] ?? "");
        $students = $this->student_management_model->getStudents($manager, $search);

        echo $this->templateEngine->render("admin_students.html.twig", [
            "manager" => $manager,
            "students" => $students,
            "students_count" => count($students),
            "search" => $search,
            "student_list_message" => $this->getStudentListMessage($_GET["student_status"] ?? null),
        ]);
    }

    public function studentCreatePage(): void{
        $manager = $this->getLoggedManager();

        echo $this->templateEngine->render("admin_student_form.html.twig", [
            "manager" => $manager,
            "student" => null,
            "pilots" => $this->student_management_model->getPilotsForSelect(),
            "student_form_message" => $this->getStudentFormMessage($_GET["student_form_status"] ?? null),
            "form_action" => "?uri=admin_student_store",
            "page_title" => "Creer un compte etudiant",
            "submit_label" => "Creer le compte etudiant",
        ]);
    }

    public function storeStudent(): void{
        $manager = $this->getLoggedManager();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=admin_students");
            exit;
        }

        $status = $this->student_management_model->createStudentAccount($this->getStudentFormData($manager), $manager);

        if ($status === "created") {
            header("Location: ?uri=admin_students&student_status=created");
            exit;
        }

        header("Location: ?uri=admin_student_create&student_form_status=" . $status);
        exit;
    }

    public function studentEditPage(): void{
        $manager = $this->getLoggedManager();
        $studentId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($studentId <= 0) {
            header("Location: ?uri=admin_students&student_status=not_found");
            exit;
        }

        $student = $this->student_management_model->getStudentById($studentId, $manager);

        if ($student === null) {
            header("Location: ?uri=admin_students&student_status=not_found");
            exit;
        }

        echo $this->templateEngine->render("admin_student_form.html.twig", [
            "manager" => $manager,
            "student" => $student,
            "pilots" => $this->student_management_model->getPilotsForSelect(),
            "student_form_message" => $this->getStudentFormMessage($_GET["student_form_status"] ?? null),
            "form_action" => "?uri=admin_student_update&id=" . $studentId,
            "page_title" => "Modifier un compte etudiant",
            "submit_label" => "Enregistrer les modifications",
        ]);
    }

    public function updateStudent(): void{
        $manager = $this->getLoggedManager();
        $studentId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $studentId <= 0) {
            header("Location: ?uri=admin_students&student_status=not_found");
            exit;
        }

        $status = $this->student_management_model->updateStudentAccount($studentId, $this->getStudentFormData($manager), $manager);

        if ($status === "updated") {
            header("Location: ?uri=admin_students&student_status=updated");
            exit;
        }

        if ($status === "not_found") {
            header("Location: ?uri=admin_students&student_status=not_found");
            exit;
        }

        header("Location: ?uri=admin_student_edit&id=" . $studentId . "&student_form_status=" . $status);
        exit;
    }

    public function deleteStudent(): void{
        $manager = $this->getLoggedManager();
        $studentId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $studentId <= 0) {
            header("Location: ?uri=admin_students&student_status=not_found");
            exit;
        }

        $status = $this->student_management_model->deleteStudentAccount($studentId, $manager);

        if ($status === "deleted") {
            $this->deleteStudentDocuments($studentId);
        }

        header("Location: ?uri=admin_students&student_status=" . $status);
        exit;
    }
}
