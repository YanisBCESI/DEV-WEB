<?php

namespace App\Controllers;

class LegalController extends Controller{
    public function __construct($templateEngine){
        $this->templateEngine = $templateEngine;
    }

    public function legalNoticePage(){
        echo $this->templateEngine->render("mentions_legales.html.twig");
    }

    public function cookiesPage(){
        echo $this->templateEngine->render("cookies.html.twig");
    }

    public function helpPage(){
        echo $this->templateEngine->render("besoin_aide.html.twig");
    }
}
