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

    public function AccountInfoSent(){
        $this->account_model->retrieveData();
        $this->account_model->sendToDataBase();
        echo("Compte créé avec succès");
    }

    public function getData(){
        return $this->account_model->getData();
    }

    public function userConnexionPage(){
        echo $this->templateEngine->render('connecter_User.html.twig', [
            "login_error" => isset($_GET["error"]) && $_GET["error"] === "1",
        ]);
    }

    public function loginStudent(){
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ?uri=connect");
            exit;
        }

        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $student = $this->account_model->authenticateStudent($email, $password);

        if (!$student) {
            header("Location: ?uri=connect&error=1");
            exit;
        }

        $_SESSION["student"] = $student;

        header("Location: ?uri=offres");
        exit;
    }

    public function logoutStudent(){
        unset($_SESSION["student"]);
        header("Location: /");
        exit;
    }
}
