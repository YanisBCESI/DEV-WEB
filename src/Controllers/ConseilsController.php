<?php

namespace App\Controllers;

use App\Models\ConseilsModel;

class ConseilsController extends Controller{
    public function __construct($templateEngine){
        $this->conseils_model = new ConseilsModel();
        $this->templateEngine = $templateEngine;
    }

    public function getData(){
        return $this->getFronDataBase();
    }

    public function conseilsPage(){
            echo $this->templateEngine->render("conseil.html.twig");
        }
}