<?php
namespace App\Controllers;

use App\Models\OffersModel;

class OffersController extends Controller {

    public function __construct($templateEngine){
        $this->Offer_model = new OffersModel();
        $this->templateEngine = $templateEngine;
    }

    public function offersPage(){

        // 1. Récupérer les données
        $offres = $this->Offer_model->getAllOffers();

        // 2. Envoyer à la vue
        echo $this->templateEngine->render('offres.html.twig', [
            'offres' => $offres
        ]);
    }

    public function createOfferPage(){
        echo $this->templateEngine->render("deposer_offre.html.twig");
    }
}