<?php

namespace App\Models;

class WishlistModel extends Model{
    protected $dbh = null;

    public function __construct(){
        $this->dbh = new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getWishlistByStudentId(int $studentId): array{
        $stmt = $this->dbh->prepare(
            "SELECT
                wishlist.offre_id,
                wishlist.date_ajout,
                offres.id AS id_offre,
                offres.titre,
                offres.type_contrat,
                offres.secteur,
                offres.localisation,
                offres.description AS description,
                offres.remuneration,
                offres.date_debut,
                entreprises.nom_entreprise
            FROM wishlist
            INNER JOIN offres ON wishlist.offre_id = offres.id
            INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
            WHERE wishlist.etudiant_id = :student_id
            ORDER BY wishlist.date_ajout DESC"
        );
        $stmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getWishlistOfferIdsByStudentId(int $studentId): array{
        $stmt = $this->dbh->prepare("SELECT offre_id FROM wishlist WHERE etudiant_id = :student_id");
        $stmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
        $stmt->execute();

        return array_map("intval", $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public function addOfferToWishlist(int $studentId, int $offerId): void{
        $stmt = $this->dbh->prepare(
            "INSERT INTO wishlist (etudiant_id, offre_id)
             VALUES (:student_id, :offer_id)
             ON DUPLICATE KEY UPDATE date_ajout = CURRENT_TIMESTAMP"
        );
        $stmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
        $stmt->bindValue(":offer_id", $offerId, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function removeOfferFromWishlist(int $studentId, int $offerId): void{
        $stmt = $this->dbh->prepare("DELETE FROM wishlist WHERE etudiant_id = :student_id AND offre_id = :offer_id");
        $stmt->bindValue(":student_id", $studentId, \PDO::PARAM_INT);
        $stmt->bindValue(":offer_id", $offerId, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
