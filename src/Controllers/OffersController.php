<?php
namespace App\Controllers;

use App\Models\OffersModel;

class OffersController extends Controller {

    public function __construct($templateEngine){
        $this->Offer_model = new OffersModel();
        $this->templateEngine = $templateEngine;
    }

    public function getoffers(){

        // 1. RÃƒÂ©cupÃƒÂ©rer les donnÃƒÂ©es
        $offres = $this->Offer_model->getAllOffers();

        // 2. Envoyer ÃƒÂ  la vue
        echo $this->templateEngine->render('offres.html.twig', [
            'offres' => $offres
        ]);
    }

    public function offersPage(){
        echo $this->templateEngine->render("offres.html.twig");
    }
}
