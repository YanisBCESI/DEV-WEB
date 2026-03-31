<?php

namespace App\Controllers;

use App\Models\ConseilsModel;

class ConseilsController extends Controller{
    public function __construct($templateEngine){
        $this->conseils_model = new ConseilsModel();
        $this->templateEngine = $templateEngine;
    }

    public function getData(){
        return $this->conseils_model->getFromDataBase();
    }

    public function conseilsPage(){
        $conseils=$this->getData();
        if (isset($_GET["conseil_id"])){
            echo $this->templateEngine->render("poste_conseil.html.twig", ["conseil"=>$conseils[$_GET["conseil_id"]-1]]);
        }
        else {
            echo $this->templateEngine->render("conseil.html.twig", ["conseils" => $this->getData()]);
        }
    }
}