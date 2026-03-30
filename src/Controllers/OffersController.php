<?php
namespace App\Controllers;

use App\Models\OffersModel;

class OffersController extends Controller {

    public function __construct($templateEngine){
        $this->Offer_model = new OffersModel();
        $this->templateEngine = $templateEngine;
    }


    public function offersPage(){
    // 1. Récupérer toutes les offres
    $offres = $this->Offer_model->getAllOffers();

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
        'totalPages' => $totalPages
    ]);
    }
}
