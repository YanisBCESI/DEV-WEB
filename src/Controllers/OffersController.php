<?php
namespace App\Controllers;

use App\Models\OffersModel;
use App\Models\WishlistModel;

class OffersController extends Controller {

    protected  OffersModel $Offer_model;

    public function __construct($templateEngine){
        $this->Offer_model = new OffersModel();
        $this->templateEngine = $templateEngine;
    }


    public function offersPage(){
        // 1. Récupérer toutes les offres
        $offres = $this->Offer_model->getAllOffers();
        $wishlistOfferIds = [];

        if (isset($_SESSION["student"]["id"])) {
            $wishlistModel = new WishlistModel();
            $wishlistOfferIds = $wishlistModel->getWishlistOfferIdsByStudentId((int) $_SESSION["student"]["id"]);
        }

        // 2. Pagination
        $parPage = 9;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $total = count($offres);
        $totalPages = ceil($total / $parPage);

        if ($page < 1) $page = 1;
        if ($page > $totalPages) $page = $totalPages;

        $offset = ($page - 1) * $parPage;

        $offresPage = array_slice($offres, $offset, $parPage);

        // 3. Envoyer à la vue
        echo $this->templateEngine->render('offres.html.twig', [
            'offres' => $offresPage,
            'page' => $page,
            'totalPages' => $totalPages,
            'wishlist_offer_ids' => $wishlistOfferIds,
        ]);
    }

    public function showOffer(){
        if(isset($_GET["id_offre"])){
            $offre = $this->Offer_model->getOfferById((int)$_GET["id_offre"]);
            echo $this->templateEngine->render("poste_offre.html.twig", [
                "offre" => $offre
            ]);
        }
        else{
            $this->offersPage();
        }
    }


    /* Créer une nouvelle offre à partir du formulaire */
    public function createOfferPage() {
        echo $this->templateEngine->render('deposer_offre.html.twig');

        $typesAutorises = ['stage', 'alternance', 'emploi'];

        $emailEntreprise = trim($_POST['email_entreprise'] ?? '');
        $titre         = trim($_POST['titre'] ?? '');
        $typeContrat   = trim($_POST['type_contrat'] ?? '');
        $secteur       = trim($_POST['secteur'] ?? '');
        $localisation  = trim($_POST['localisation'] ?? '');
        $description   = trim($_POST['description'] ?? '');
        $competences   = trim($_POST['competences'] ?? '');
        $remuneration  = trim($_POST['remuneration'] ?? '');
        $dateDebut     = trim($_POST['date_debut'] ?? '');

        if (
            !filter_var($emailEntreprise, FILTER_VALIDATE_EMAIL) ||
            $titre === '' ||
            !in_array($typeContrat, $typesAutorises, true) ||
            $secteur === '' ||
            $localisation === '' ||
            $description === '' ||
            $competences === ''
        ) 

        // 1. Trouver l’entreprise par email
        $entreprise = $this->Offer_model->findEntrepriseIdByEmail($emailEntreprise);

        // 2. Vérifier si l'entreprise existe
        if (!$entreprise || !isset($entreprise['id'])) {
            $_SESSION['error'] = 'Aucune entreprise n\'est associée à cet email.';
            header('Location: ?uri=deposerOffre');
            exit;
        }

        // 2. Insérer l’offre
        $data = [
            'entreprise_id'     => $entreprise['id'],
            'titre'             => $titre,
            'type_contrat'      => $typeContrat,
            'secteur'           => $secteur,
            'localisation'      => $localisation,
            'description_offre' => $description,
            'competences'       => $competences,
            'remuneration'      => $remuneration !== '' ? $remuneration : null,
            'date_debut'        => $dateDebut !== '' ? $dateDebut : null,
        ];

        try {
            if ($this->Offer_model->createOffer($data)) {
                $_SESSION['success'] = 'Offre publiée avec succès.';
                header('Location: ?uri=offres');  // ou ?uri=deposerOffre si tu veux rester sur la page
                exit;
            }
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Erreur lors de l\'enregistrement de l\'offre : ' . $e->getMessage();
            header('Location: ?uri=deposerOffre');
            exit;
        }
    }

}
