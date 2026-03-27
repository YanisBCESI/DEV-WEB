<?php

namespace App\Models;

class HomepageModel extends Model{
    protected $dbh = null;

    public function __construct(){
        $this->dbh = new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getLatestOffers(int $limit = 10): array{
        $sql = "SELECT
                    offres.id,
                    offres.titre,
                    offres.description,
                    offres.localisation,
                    offres.type_contrat,
                    entreprises.nom_entreprise
                FROM offres
                INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
                ORDER BY offres.created_at DESC
                LIMIT :offer_limit";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(":offer_limit", $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
