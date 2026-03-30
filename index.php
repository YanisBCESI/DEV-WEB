<?php

require __DIR__ . "/vendor/autoload.php";
#ini_set("display_errors", 1);
#ini_set("display_startup_errors", 1);
#error_reporting(E_ALL);

use App\Controllers\HomepageController;
use App\Controllers\OffersController;
use App\Controllers\FileDepotController;
use App\Controllers\AccountController;
use App\Controllers\LegalController;
use App\Controllers\ConseilsController;


$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader, [
    "debug" => true
]);

if(isset($_GET["uri"])){
    $uri = $_GET['uri'];
}
else{
    $uri = "/";
}

$HomepageController = new HomepageController($twig);
$OffersController = new OffersController($twig);
$FileDepotController = new FileDepotController($twig);
$AccountController = new AccountController($twig);
$LegalController = new LegalController($twig);
$ConseilsController = new ConseilsController($twig);

switch($uri){
    case '/':
        $HomepageController->welcomePage();
        break;
    case 'offres':
        $OffersController->showOffer();
        break;
    case 'creer_offre':
        $OffersController->createOfferPage();
        break;
    case 'deposer_fichier':
        $FileDepotController->filedepotPage();
        break;
    case 'inscription_user':
        $AccountController->userInscriptionPage();
        break;
    case 'mentions_legales':
        $LegalController->legalNoticePage();
        break;
    case 'account_created':
        $AccountController->AccountInfoSent();
        break;
    case 'connect':
        $AccountController->userConnexionPage();
        break;
    case "conseils":
        $ConseilsController->conseilsPage();
        break;
}
