<?php

namespace App\Controllers;

class LegalController extends Controller{
    public function __construct($templateEngine){
        $this->templateEngine = $templateEngine;
    }

    public function legalNoticePage(){
        echo $this->templateEngine->render("mentions_legales.html.twig");
    }
}
