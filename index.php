<?php

require __DIR__ . "/vendor/autoload.php";
#ini_set("display_errors", 1);
#ini_set("display_startup_errors", 1);
#error_reporting(E_ALL);

use App\Controllers\MainController;

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

$controller = new MainController($twig);

switch($uri){
    case '/':
        $controller->welcomePage();
        break;
    case 'offres':
        $controller->offersPage();
        break;
    case 'inscription_entreprise':
        $controller->inscriptionEntreprise();
    case 'depot':
        $controller->depotPage();
        break;
    case 'send_file':
        $controller->sendFile();
}