<?php
namespace App\Controllers;

use App\Models\FileDepotModel;

class MainController extends Controller{
    public function __construct($templateEngine){
        $this->Depot_model = new FileDepotModel();
        $this->templateEngine = $templateEngine;
    }
    public function welcomePage(){
        echo $this->templateEngine->render("index.html");
    }
    public function connectionPage(){
        echo $this->templateEngine->render("page_connexion.html.twig");
    }
    public function inscriptionEntreprise(){
        echo $this->templateEngine->render("inscrire_entreprise.html.twig");
    }
    public function sendFile(){
        var_dump($_FILES);
        if (!isset($_FILES['userfile']['tmp_name'])){
            header("Location: /");
            exit();
        }
        $file = $_FILES['userfile']['tmp_name'];
        $this->Depot_model = new FileDepotModel($file);
        $this->Depot_model->depot();
        header('Location: index.php?uri=depot&success=1');
    }
    public function offersPage(){
        echo $this->templateEngine->render("offres.html.twig");
    }
}
