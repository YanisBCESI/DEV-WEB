<?php

namespace App\Controllers;

use App\Models\EnterpriseManagementModel;

class EnterpriseManagementController extends Controller{
    protected $enterprise_management_model = null;

    public function __construct($templateEngine){
        $this->enterprise_management_model = new EnterpriseManagementModel();
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

    private function getOptionalManager(): ?array{
        if (isset($_SESSION["admin"]["id"])) {
            return $_SESSION["admin"];
        }

        if (isset($_SESSION["pilot"]["id"])) {
            return $_SESSION["pilot"];
        }

        return null;
    }

    private function getListMessage(?string $status): ?array{
        return match ($status) {
            "created" => [
                "type" => "success",
                "text" => "L'entreprise a ete creee avec succes.",
            ],
            "updated" => [
                "type" => "success",
                "text" => "L'entreprise a ete mise a jour.",
            ],
            "deleted" => [
                "type" => "success",
                "text" => "L'entreprise a ete supprimee.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "L'entreprise demandee est introuvable.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'operation sur l'entreprise a echoue.",
            ],
            default => null,
        };
    }

    private function getFormMessage(?string $status): ?array{
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
            "siret_exists" => [
                "type" => "error",
                "text" => "Ce numero de SIRET existe deja.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "L'entreprise demandee est introuvable.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'enregistrement de l'entreprise a echoue.",
            ],
            default => null,
        };
    }

    private function getEvaluationMessage(?string $status): ?array{
        return match ($status) {
            "rated" => [
                "type" => "success",
                "text" => "Votre evaluation a bien ete enregistree.",
            ],
            "invalid_note" => [
                "type" => "error",
                "text" => "La note doit etre comprise entre 1 et 5.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "L'entreprise demandee est introuvable.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'evaluation de l'entreprise a echoue.",
            ],
            default => null,
        };
    }

    private function getCompanyFormData(): array{
        return [
            "nom_entreprise" => trim($_POST["nom_entreprise"] ?? ""),
            "type_entreprise" => trim($_POST["type_entreprise"] ?? ""),
            "secteur" => trim($_POST["secteur"] ?? ""),
            "siret" => trim($_POST["siret"] ?? ""),
            "adresse" => trim($_POST["adresse"] ?? ""),
            "ville" => trim($_POST["ville"] ?? ""),
            "code_postal" => trim($_POST["code_postal"] ?? ""),
            "description" => trim($_POST["description"] ?? ""),
            "site_web" => trim($_POST["site_web"] ?? ""),
            "email" => trim($_POST["email"] ?? ""),
            "password" => $_POST["password"] ?? "",
            "password_confirm" => $_POST["password_confirm"] ?? "",
        ];
    }

    public function companiesPage(): void{
        $search = trim($_GET["search"] ?? "");
        $manager = $this->getOptionalManager();

        echo $this->templateEngine->render("entreprises.html.twig", [
            "companies" => $this->enterprise_management_model->getCompanies($search),
            "search" => $search,
            "manager" => $manager,
            "company_list_message" => $this->getListMessage($_GET["company_status"] ?? null),
        ]);
    }

    public function companyDetailPage(): void{
        $companyId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($companyId <= 0) {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        $company = $this->enterprise_management_model->getCompanyById($companyId);

        if ($company === null) {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        $manager = $this->getOptionalManager();

        echo $this->templateEngine->render("entreprise_detail.html.twig", [
            "company" => $company,
            "manager" => $manager,
            "evaluations" => $this->enterprise_management_model->getManagementEvaluationsByCompanyId($companyId),
            "existing_evaluation" => $manager ? $this->enterprise_management_model->getManagementEvaluationForCompany($companyId, $manager) : null,
            "company_status_message" => $this->getListMessage($_GET["company_status"] ?? null),
            "evaluation_message" => $this->getEvaluationMessage($_GET["evaluation_status"] ?? null),
        ]);
    }

    public function createCompanyPage(): void{
        $manager = $this->getLoggedManager();

        echo $this->templateEngine->render("entreprise_form.html.twig", [
            "manager" => $manager,
            "company" => null,
            "company_form_message" => $this->getFormMessage($_GET["company_form_status"] ?? null),
            "form_action" => "?uri=entreprise_store",
            "page_title" => "Creer une entreprise",
            "submit_label" => "Creer l'entreprise",
        ]);
    }

    public function storeCompany(): void{
        $this->getLoggedManager();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=entreprises");
            exit;
        }

        $status = $this->enterprise_management_model->createCompanyAccount($this->getCompanyFormData());

        if ($status === "created") {
            header("Location: ?uri=entreprises&company_status=created");
            exit;
        }

        header("Location: ?uri=entreprise_create&company_form_status=" . $status);
        exit;
    }

    public function editCompanyPage(): void{
        $manager = $this->getLoggedManager();
        $companyId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($companyId <= 0) {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        $company = $this->enterprise_management_model->getCompanyById($companyId);

        if ($company === null) {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        echo $this->templateEngine->render("entreprise_form.html.twig", [
            "manager" => $manager,
            "company" => $company,
            "company_form_message" => $this->getFormMessage($_GET["company_form_status"] ?? null),
            "form_action" => "?uri=entreprise_update&id=" . $companyId,
            "page_title" => "Modifier une entreprise",
            "submit_label" => "Enregistrer les modifications",
        ]);
    }

    public function updateCompany(): void{
        $this->getLoggedManager();
        $companyId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $companyId <= 0) {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        $status = $this->enterprise_management_model->updateCompanyAccount($companyId, $this->getCompanyFormData());

        if ($status === "updated") {
            header("Location: ?uri=entreprise&id=" . $companyId . "&company_status=updated");
            exit;
        }

        if ($status === "not_found") {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        header("Location: ?uri=entreprise_edit&id=" . $companyId . "&company_form_status=" . $status);
        exit;
    }

    public function deleteCompany(): void{
        $this->getLoggedManager();
        $companyId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $companyId <= 0) {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        $status = $this->enterprise_management_model->deleteCompanyAccount($companyId);

        header("Location: ?uri=entreprises&company_status=" . $status);
        exit;
    }

    public function rateCompany(): void{
        $manager = $this->getLoggedManager();
        $companyId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $companyId <= 0) {
            header("Location: ?uri=entreprises&company_status=not_found");
            exit;
        }

        $note = isset($_POST["note"]) ? (int) $_POST["note"] : 0;
        $commentaire = trim($_POST["commentaire"] ?? "");
        $status = $this->enterprise_management_model->saveManagementEvaluation($companyId, $manager, $note, $commentaire);

        header("Location: ?uri=entreprise&id=" . $companyId . "&evaluation_status=" . $status);
        exit;
    }
}
