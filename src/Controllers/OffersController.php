<?php
namespace App\Controllers;

use App\Models\OffersModel;
use App\Models\WishlistModel;

class OffersController extends Controller {

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
        $offres=$this->Offer_model->getAllOffers();
        if(isset($_GET["id_offre"])){
            echo $this->templateEngine->render("poste_offre.html.twig", ["offre"=>$offres[$_GET["id_offre"]-1]]);
        }
        else{
            $this->offersPage();
        }
    }
}
