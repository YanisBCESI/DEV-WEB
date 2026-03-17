<?php
namespace App\Controllers;

use App\Models\HomepageModel;

class HomepageController extends Controller{
    public function __construct($templateEngine){
        $this->Homepage_model = new HomepageModel();
        $this->templateEngine = $templateEngine;
    }
    public function welcomePage(){
        echo $this->templateEngine->render("index.html");
    }
}
