<?php
namespace App\Controllers;

use App\Models\OffersModel;

class OffersController extends Controller{
    public function __construct($templateEngine){
        $this->Offer_model = new OffersModel();
        $this->templateEngine = $templateEngine;
    }
    public function offersPage(){
       echo  $this->templateEngine->render('offres.html.twig');
    }
    public function createOfferPage(){
        echo $this->templateEngine->render("deposer_offre.html");
    }

      

}