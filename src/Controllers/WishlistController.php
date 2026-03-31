<?php

namespace App\Controllers;

use App\Models\WishlistModel;

class WishlistController extends Controller{
    protected $wishlist_model = null;

    public function __construct($templateEngine){
        $this->wishlist_model = new WishlistModel();
        $this->templateEngine = $templateEngine;
    }

    private function getLoggedStudentId(): ?int{
        return isset($_SESSION["student"]["id"]) ? (int) $_SESSION["student"]["id"] : null;
    }

    private function requireLoggedStudent(): int{
        $studentId = $this->getLoggedStudentId();

        if ($studentId === null) {
            header("Location: ?uri=connect");
            exit;
        }

        return $studentId;
    }

    private function getRedirectTarget(string $default = "?uri=wishlist"): string{
        $redirect = $_POST["redirect_to"] ?? $default;

        if (!is_string($redirect) || $redirect === "") {
            return $default;
        }

        if (str_starts_with($redirect, "?") || str_starts_with($redirect, "/")) {
            return $redirect;
        }

        return $default;
    }

    public function wishlistPage(): void{
        $studentId = $this->requireLoggedStudent();
        $wishlist = $this->wishlist_model->getWishlistByStudentId($studentId);

        echo $this->templateEngine->render("wishlist.html.twig", [
            "wishlist" => $wishlist,
        ]);
    }

    public function addOffer(): void{
        $studentId = $this->requireLoggedStudent();
        $offerId = isset($_POST["offre_id"]) ? (int) $_POST["offre_id"] : 0;

        if ($offerId > 0) {
            $this->wishlist_model->addOfferToWishlist($studentId, $offerId);
        }

        header("Location: " . $this->getRedirectTarget("?uri=offres"));
        exit;
    }

    public function removeOffer(): void{
        $studentId = $this->requireLoggedStudent();
        $offerId = isset($_POST["offre_id"]) ? (int) $_POST["offre_id"] : 0;

        if ($offerId > 0) {
            $this->wishlist_model->removeOfferFromWishlist($studentId, $offerId);
        }

        header("Location: " . $this->getRedirectTarget("?uri=wishlist"));
        exit;
    }
}
