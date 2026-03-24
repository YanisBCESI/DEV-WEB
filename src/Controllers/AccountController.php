<?php

namespace App\Controllers;

use App\Models\AccountModel;

class AccountController extends Controller{
    public function __construct($templateEngine){
        $this->account_model = new AccountModel();
        $this->templateEngine = $templateEngine;
    }

    public function userInscriptionPage(){
        echo $this->templateEngine->render("inscrire_User.html.twig");
    }

    public function isPostMethod(){
        if($_SERVER['REQUEST_METHOD'] !== "POST"){
            return false;
        }
    }

    public function getData(){
        
    }
}