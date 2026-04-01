<?php

namespace App\Models;

class OffersModel extends Model {

    public function __construct($info = null){
        if(is_null($info)){
            $this->data=[];
            $this->dbh= new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
    }
    }

    public function getAllOffers(){
        $sql = "SELECT
                    offres.id_offre,
                    offres.entreprise_id,
                    offres.titre,
                    offres.type_contrat,
                    offres.secteur,
                    offres.localisation,
                    offres.description_offre,
                    offres.competences,
                    offres.remuneration,
                    offres.date_debut,
                    offres.date_fin,
                    offres.statut,
                    offres.nb_places,
                    offres.created_at,
                    offres.updated_at,
                    offres.nb_vues,
                    entreprises.nom_entreprise
                FROM offres
                INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
                ORDER BY offres.created_at DESC";

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getOfferById($id){
        $sql = "SELECT
                    offres.*,
                    entreprises.nom_entreprise
                FROM offres
                INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
                WHERE offres.id_offre = :id";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getDataFormed(){
        $sql = $pdo->query("SELECT Max(id) FROM candidatures as id");
        $id = $sql->fetch(\PDO::FETCH_ASSOC)["id"] + 1;
        $etudiant = $currentStudent;
        $data = [$id, $etudiant];
    }
}
