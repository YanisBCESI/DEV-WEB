<?php

namespace App\Controllers;

use App\Models\ConseilsModel;

class AccountController extends Controller{
    public function __construct($templateEngine){
        $this->account_model = new AccountModel();
        $this->templateEngine = $templateEngine;
    }

    public function getData(){
        return $this->getFronDataBase()
    }
