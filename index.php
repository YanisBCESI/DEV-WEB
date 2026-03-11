<?php

require __DIR__ . "/vendor/autoload.php";
#ini_set("display_errors", 1);
#ini_set("display_startup_errors", 1);
#error_reporting(E_ALL);

use App\Controllers\FileDepotController;

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

$controller = new FileDepotController($twig);

switch($uri){
    case '/':
        $controller->welcomePage();
        break;
    case 'depot':
        $controller->depotPage();
        break;
    case 'send_file':
        $controller->sendFile();
}