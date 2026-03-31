<?php
namespace App\Controllers;

use App\Models\HomepageModel;

class HomepageController extends Controller{
    public function __construct($templateEngine){
        $this->Homepage_model = new HomepageModel();
        $this->templateEngine = $templateEngine;
    }
    public function welcomePage(){
        $consentCookieName = "stage4all_cookie_consent";
        $visitCookieName = "stage4all_visite";

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cookie_action"])) {
            $cookieAction = $_POST["cookie_action"];

            if ($cookieAction === "accept") {
                $visitCookieValue = date("Y-m-d H:i:s");

                setcookie($consentCookieName, "accepted", [
                    "expires" => time() + (365 * 24 * 60 * 60),
                    "path" => "/",
                    "httponly" => true,
                    "samesite" => "Lax",
                ]);
                setcookie($visitCookieName, $visitCookieValue, [
                    "expires" => time() + (30 * 24 * 60 * 60),
                    "path" => "/",
                    "httponly" => true,
                    "samesite" => "Lax",
                ]);
            }

            if ($cookieAction === "reject") {
                setcookie($consentCookieName, "rejected", [
                    "expires" => time() + (365 * 24 * 60 * 60),
                    "path" => "/",
                    "httponly" => true,
                    "samesite" => "Lax",
                ]);
                setcookie($visitCookieName, "", [
                    "expires" => time() - 3600,
                    "path" => "/",
                    "httponly" => true,
                    "samesite" => "Lax",
                ]);
            }

            header("Location: /");
            exit;
        }

        $cookieConsent = $_COOKIE[$consentCookieName] ?? null;
        $cookieValue = $_COOKIE[$visitCookieName] ?? null;
        $latestOffers = $this->Homepage_model->getLatestOffers();
        $conseils = $this->Homepage_model->getConseils();
        echo $this->templateEngine->render("accueil.html.twig", [
            "show_cookie_banner" => $cookieConsent === null,
            "cookie_consent" => $cookieConsent,
            "cookie_value" => $cookieValue,
            "latest_offers" => $latestOffers,
            "conseils" => $conseils
        ]);
    }
}
