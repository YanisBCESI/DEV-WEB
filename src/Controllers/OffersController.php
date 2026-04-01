<?php

namespace App\Controllers;

use App\Models\OffersModel;
use App\Models\WishlistModel;

class OffersController extends Controller{
    protected OffersModel $offer_model;

    public function __construct($templateEngine){
        $this->offer_model = new OffersModel();
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

    private function getOfferListMessage(?string $status): ?array{
        return match ($status) {
            "created" => [
                "type" => "success",
                "text" => "L'offre a ete creee avec succes.",
            ],
            "updated" => [
                "type" => "success",
                "text" => "L'offre a ete mise a jour.",
            ],
            "deleted" => [
                "type" => "success",
                "text" => "L'offre a ete supprimee.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "L'offre demandee est introuvable.",
            ],
            "apply_unavailable" => [
                "type" => "error",
                "text" => "La gestion des candidatures sera branchee dans l'etape suivante.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'operation sur l'offre a echoue.",
            ],
            default => null,
        };
    }

    private function getOfferFormMessage(?string $status): ?array{
        return match ($status) {
            "invalid_data" => [
                "type" => "error",
                "text" => "Merci de remplir correctement les informations obligatoires de l'offre.",
            ],
            "not_found" => [
                "type" => "error",
                "text" => "L'offre demandee est introuvable.",
            ],
            "error" => [
                "type" => "error",
                "text" => "L'enregistrement de l'offre a echoue.",
            ],
            default => null,
        };
    }

    private function getOfferFormData(): array{
        return [
            "entreprise_id" => (int) ($_POST["entreprise_id"] ?? 0),
            "titre" => trim($_POST["titre"] ?? ""),
            "type_contrat" => trim($_POST["type_contrat"] ?? ""),
            "secteur" => trim($_POST["secteur"] ?? ""),
            "localisation" => trim($_POST["localisation"] ?? ""),
            "description_offre" => trim($_POST["description_offre"] ?? ""),
            "competences" => trim($_POST["competences"] ?? ""),
            "remuneration" => trim($_POST["remuneration"] ?? ""),
            "date_debut" => trim($_POST["date_debut"] ?? ""),
            "date_fin" => trim($_POST["date_fin"] ?? ""),
            "statut" => trim($_POST["statut"] ?? ""),
            "nb_places" => (int) ($_POST["nb_places"] ?? 0),
        ];
    }

    private function validateOfferData(array $data): string{
        $allowedTypes = ["stage", "alternance", "emploi"];
        $allowedStatuses = ["ouverte", "fermee", "archivee"];

        if (
            (int) ($data["entreprise_id"] ?? 0) <= 0
            || trim((string) ($data["titre"] ?? "")) === ""
            || !in_array((string) ($data["type_contrat"] ?? ""), $allowedTypes, true)
            || trim((string) ($data["secteur"] ?? "")) === ""
            || trim((string) ($data["localisation"] ?? "")) === ""
            || trim((string) ($data["description_offre"] ?? "")) === ""
            || trim((string) ($data["competences"] ?? "")) === ""
            || !in_array((string) ($data["statut"] ?? ""), $allowedStatuses, true)
            || (int) ($data["nb_places"] ?? 0) <= 0
        ) {
            return "invalid_data";
        }

        return "valid";
    }

    public function offersPage(): void{
        $search = trim($_GET["search"] ?? "");
        $contractType = trim($_GET["type_contrat"] ?? "");
        $offres = $this->offer_model->getOffers($search, $contractType !== "" ? $contractType : null);
        $wishlistOfferIds = [];

        if (isset($_SESSION["student"]["id"])) {
            $wishlistModel = new WishlistModel();
            $wishlistOfferIds = $wishlistModel->getWishlistOfferIdsByStudentId((int) $_SESSION["student"]["id"]);
        }

        $parPage = 9;
        $page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

        $total = count($offres);
        $totalPages = max(1, (int) ceil($total / $parPage));

        if ($page < 1) {
            $page = 1;
        }

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $parPage;
        $offresPage = array_slice($offres, $offset, $parPage);

        echo $this->templateEngine->render("offres.html.twig", [
            "offres" => $offresPage,
            "page" => $page,
            "totalPages" => $totalPages,
            "search" => $search,
            "selected_contract_type" => $contractType,
            "wishlist_offer_ids" => $wishlistOfferIds,
            "offer_stats" => $this->offer_model->getOfferStats(),
            "offer_list_message" => $this->getOfferListMessage($_GET["offer_status"] ?? null),
        ]);
    }

    public function showOffer(): void{
        if (!isset($_GET["id_offre"])) {
            $this->offersPage();
            return;
        }

        $offerId = (int) $_GET["id_offre"];
        $offre = $this->offer_model->getOfferById($offerId, true);

        if ($offre === null) {
            header("Location: ?uri=offres&offer_status=not_found");
            exit;
        }

        echo $this->templateEngine->render("poste_offre.html.twig", [
            "offre" => $offre,
            "offer_message" => $this->getOfferListMessage($_GET["offer_status"] ?? null),
        ]);
    }

    public function createOfferPage(): void{
        $this->getLoggedManager();

        echo $this->templateEngine->render("offre_form.html.twig", [
            "offer" => null,
            "companies" => $this->offer_model->getCompaniesForSelect(),
            "offer_form_message" => $this->getOfferFormMessage($_GET["offer_form_status"] ?? null),
            "page_title" => "Creer une offre",
            "submit_label" => "Publier l'offre",
            "form_action" => "?uri=offre_store",
        ]);
    }

    public function storeOffer(): void{
        $this->getLoggedManager();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=offres");
            exit;
        }

        $data = $this->getOfferFormData();
        $validation = $this->validateOfferData($data);

        if ($validation !== "valid") {
            header("Location: ?uri=creer_offre&offer_form_status=" . $validation);
            exit;
        }

        try {
            $created = $this->offer_model->createOffer($data);

            header("Location: ?uri=offres&offer_status=" . ($created ? "created" : "error"));
            exit;
        } catch (\PDOException $exception) {
            header("Location: ?uri=creer_offre&offer_form_status=error");
            exit;
        }
    }

    public function editOfferPage(): void{
        $this->getLoggedManager();
        $offerId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($offerId <= 0) {
            header("Location: ?uri=offres&offer_status=not_found");
            exit;
        }

        $offer = $this->offer_model->getOfferById($offerId, false);

        if ($offer === null) {
            header("Location: ?uri=offres&offer_status=not_found");
            exit;
        }

        echo $this->templateEngine->render("offre_form.html.twig", [
            "offer" => $offer,
            "companies" => $this->offer_model->getCompaniesForSelect(),
            "offer_form_message" => $this->getOfferFormMessage($_GET["offer_form_status"] ?? null),
            "page_title" => "Modifier une offre",
            "submit_label" => "Enregistrer les modifications",
            "form_action" => "?uri=offre_update&id=" . $offerId,
        ]);
    }

    public function updateOffer(): void{
        $this->getLoggedManager();
        $offerId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $offerId <= 0) {
            header("Location: ?uri=offres&offer_status=not_found");
            exit;
        }

        $data = $this->getOfferFormData();
        $validation = $this->validateOfferData($data);

        if ($validation !== "valid") {
            header("Location: ?uri=offre_edit&id=" . $offerId . "&offer_form_status=" . $validation);
            exit;
        }

        try {
            $updated = $this->offer_model->updateOffer($offerId, $data);

            header("Location: ?uri=offres&id_offre=" . $offerId . "&offer_status=" . ($updated ? "updated" : "error"));
            exit;
        } catch (\PDOException $exception) {
            header("Location: ?uri=offre_edit&id=" . $offerId . "&offer_form_status=error");
            exit;
        }
    }

    public function deleteOffer(): void{
        $this->getLoggedManager();
        $offerId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

        if ($_SERVER["REQUEST_METHOD"] !== "POST" || $offerId <= 0) {
            header("Location: ?uri=offres&offer_status=not_found");
            exit;
        }

        try {
            $deleted = $this->offer_model->deleteOffer($offerId);

            header("Location: ?uri=offres&offer_status=" . ($deleted ? "deleted" : "error"));
            exit;
        } catch (\PDOException $exception) {
            header("Location: ?uri=offres&offer_status=error");
            exit;
        }
    }



    public function postulerPage(){
        if(isset($_GET["id_offre"])){
            $data = $this->offer_model->getDataFormed();
            $id = $data[0];
            $etudiant = $data[1];
            $offre = $this->offer_model->getOfferById((int)$_GET["id_offre"]);
            $statut = "en attente";
            $date_candidature = date("d-m-y");
            $data = [$id, $etudiant, $statut, $offre, $date_candidature];
            echo $this->templateEngine->render("postuler.html.twig", ["data" => $data]);
        }else{
            $this->offersPage();
        }
    }
}
