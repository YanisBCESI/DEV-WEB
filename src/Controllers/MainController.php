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
    public function depotPage(){
        $success = isset($_GET['success']) ? true : false;
        echo $this->templateEngine->render("deposer_offre.html", [
            'success' => $success
        ]);
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
