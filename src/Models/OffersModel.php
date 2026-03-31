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
}
