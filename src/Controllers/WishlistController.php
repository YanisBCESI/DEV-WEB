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

    private function isAjaxRequest(): bool{
        $requestedWith = $_SERVER["HTTP_X_REQUESTED_WITH"] ?? "";
        $accept = $_SERVER["HTTP_ACCEPT"] ?? "";

        return (is_string($requestedWith) && strtolower($requestedWith) === "xmlhttprequest")
            || (is_string($accept) && str_contains(strtolower($accept), "application/json"));
    }

    private function respondJson(array $payload, int $statusCode = 200): void{
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($payload);
        exit;
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
        $studentId = $this->getLoggedStudentId();
        $offerId = isset($_POST["offre_id"]) ? (int) $_POST["offre_id"] : 0;

        if ($studentId === null) {
            if ($this->isAjaxRequest()) {
                $this->respondJson([
                    "success" => false,
                    "redirect" => "?uri=connect",
                    "message" => "Connexion requise.",
                ], 401);
            }

            header("Location: ?uri=connect");
            exit;
        }

        if ($offerId <= 0) {
            if ($this->isAjaxRequest()) {
                $this->respondJson([
                    "success" => false,
                    "message" => "Offre invalide.",
                ], 400);
            }

            header("Location: " . $this->getRedirectTarget("?uri=offres"));
            exit;
        }

        $this->wishlist_model->addOfferToWishlist($studentId, $offerId);

        if ($this->isAjaxRequest()) {
            $this->respondJson([
                "success" => true,
                "action" => "added",
                "offer_id" => $offerId,
            ]);
        }

        header("Location: " . $this->getRedirectTarget("?uri=offres"));
        exit;
    }

    public function removeOffer(): void{
        $studentId = $this->getLoggedStudentId();
        $offerId = isset($_POST["offre_id"]) ? (int) $_POST["offre_id"] : 0;

        if ($studentId === null) {
            if ($this->isAjaxRequest()) {
                $this->respondJson([
                    "success" => false,
                    "redirect" => "?uri=connect",
                    "message" => "Connexion requise.",
                ], 401);
            }

            header("Location: ?uri=connect");
            exit;
        }

        if ($offerId <= 0) {
            if ($this->isAjaxRequest()) {
                $this->respondJson([
                    "success" => false,
                    "message" => "Offre invalide.",
                ], 400);
            }

            header("Location: " . $this->getRedirectTarget("?uri=wishlist"));
            exit;
        }

        $this->wishlist_model->removeOfferFromWishlist($studentId, $offerId);

        if ($this->isAjaxRequest()) {
            $this->respondJson([
                "success" => true,
                "action" => "removed",
                "offer_id" => $offerId,
            ]);
        }

        header("Location: " . $this->getRedirectTarget("?uri=wishlist"));
        exit;
    }
}
