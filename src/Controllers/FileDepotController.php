<?php

namespace App\Controllers;

use App\Models\FileDepotModel;

class FileDepotController extends Controller{
    public function __construct($templateEngine){
        $this->FileDepot_model = new FileDepotModel();
        $this->templateEngine = $templateEngine;
    }
    public function filedepotPage(){
        echo $this->templateEngine->render("formulaire_depot_fichier.html.twig");
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
}