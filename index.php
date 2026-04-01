<?php

require __DIR__ . "/vendor/autoload.php";
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

session_start();

use App\Controllers\HomepageController;
use App\Controllers\OffersController;
use App\Controllers\FileDepotController;
use App\Controllers\AccountController;
use App\Controllers\AdminController;
use App\Controllers\StudentManagementController;
use App\Controllers\LegalController;
use App\Controllers\ConseilsController;
use App\Controllers\WishlistController;


$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader, [
    "debug" => true
]);

$currentStudent = $_SESSION["student"] ?? null;
$currentAdmin = $_SESSION["admin"] ?? null;
$currentPilot = $_SESSION["pilot"] ?? null;
$currentManager = $currentAdmin ?? $currentPilot;
$twig->addGlobal("current_student", $currentStudent);
$twig->addGlobal("current_admin", $currentAdmin);
$twig->addGlobal("current_pilot", $currentPilot);
$twig->addGlobal("current_manager", $currentManager);
$twig->addGlobal("is_student_logged_in", isset($currentStudent["id"]));
$twig->addGlobal("is_admin_logged_in", isset($currentAdmin["id"]));
$twig->addGlobal("is_pilot_logged_in", isset($currentPilot["id"]));
$twig->addGlobal("is_management_logged_in", isset($currentManager["id"]));
$twig->addGlobal("current_management_role", $currentManager["role"] ?? null);
$twig->addGlobal("wishlist_icon_path", "/assets/images/30571.png");

if(isset($_GET["uri"])){
    $uri = $_GET['uri'];
}
else{
    $uri = "/";
}

switch($uri){
    case '/':
        $HomepageController = new HomepageController($twig);
        $HomepageController->welcomePage();
        break;
    case 'file_sent':
        $FileDepotController = new FileDepotController($twig);
        $FileDepotController->sendFile();
        break;
    case 'offres':
        $OffersController = new OffersController($twig);
        $OffersController->showOffer();
        break;
    case 'creer_offre':
        $OffersController = new OffersController($twig);
        $OffersController->createOfferPage();
        break;
    case 'deposer_fichier':
        $FileDepotController = new FileDepotController($twig);
        $FileDepotController->filedepotPage();
        break;
    case 'inscription_user':
        $AccountController = new AccountController($twig);
        $AccountController->userInscriptionPage();
        break;
    case 'mentions_legales':
        $LegalController = new LegalController($twig);
        $LegalController->legalNoticePage();
        break;
    case 'account_created':
        $AccountController = new AccountController($twig);
        $AccountController->AccountInfoSent();
        break;
    case 'account_connected':
        $AccountController = new AccountController($twig);
        $AccountController->loginStudent();
        break;
    case 'connect':
        $AccountController = new AccountController($twig);
        $AccountController->userConnexionPage();
        break;
    case 'student_profile':
        $AccountController = new AccountController($twig);
        $AccountController->studentProfilePage();
        break;
    case 'management_profile':
        $AccountController = new AccountController($twig);
        $AccountController->managementProfilePage();
        break;
    case 'student_document_upload':
        $AccountController = new AccountController($twig);
        $AccountController->uploadStudentDocument();
        break;
    case 'logout':
        $AccountController = new AccountController($twig);
        $AccountController->logoutStudent();
        break;
    case 'admin_pilots':
        $AdminController = new AdminController($twig);
        $AdminController->pilotsPage();
        break;
    case 'admin_pilot_create':
        $AdminController = new AdminController($twig);
        $AdminController->createPilotAccount();
        break;
    case 'admin_pilot_delete':
        $AdminController = new AdminController($twig);
        $AdminController->deletePilotAccount();
        break;
    case 'admin_students':
        $StudentManagementController = new StudentManagementController($twig);
        $StudentManagementController->studentsPage();
        break;
    case 'admin_student_create':
        $StudentManagementController = new StudentManagementController($twig);
        $StudentManagementController->studentCreatePage();
        break;
    case 'admin_student_store':
        $StudentManagementController = new StudentManagementController($twig);
        $StudentManagementController->storeStudent();
        break;
    case 'admin_student_edit':
        $StudentManagementController = new StudentManagementController($twig);
        $StudentManagementController->studentEditPage();
        break;
    case 'admin_student_update':
        $StudentManagementController = new StudentManagementController($twig);
        $StudentManagementController->updateStudent();
        break;
    case 'admin_student_delete':
        $StudentManagementController = new StudentManagementController($twig);
        $StudentManagementController->deleteStudent();
        break;
    case "conseils":
        $ConseilsController = new ConseilsController($twig);
        $ConseilsController->conseilsPage();
        break;
    case "wishlist":
        $WishlistController = new WishlistController($twig);
        $WishlistController->wishlistPage();
        break;
    case "wishlist_add":
        $WishlistController = new WishlistController($twig);
        $WishlistController->addOffer();
        break;
    case "wishlist_remove":
        $WishlistController = new WishlistController($twig);
        $WishlistController->removeOffer();
        break;
    default:
        http_response_code(404);
        echo "Page introuvable";
        break;
}
