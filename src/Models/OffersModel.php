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

    /* Insère une nouvelle offre */
    
    public function createOffer(array $data) {
        $sql = "INSERT INTO offres
                    (entreprise_id, titre, type_contrat, secteur, localisation,
                     description_offre, competences, remuneration, date_debut)
                VALUES
                    (:entreprise_id, :titre, :type_contrat, :secteur, :localisation,
                     :description_offre, :competences, :remuneration, :date_debut)";

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':entreprise_id', $data['entreprise_id'], \PDO::PARAM_INT);
        $stmt->bindValue(':titre', $data['titre']);
        $stmt->bindValue(':type_contrat', $data['type_contrat']);
        $stmt->bindValue(':secteur', $data['secteur']);
        $stmt->bindValue(':localisation', $data['localisation']);
        $stmt->bindValue(':description_offre', $data['description_offre']);
        $stmt->bindValue(':competences', $data['competences']);
        $stmt->bindValue(':remuneration', $data['remuneration'] ?: null);
        $stmt->bindValue(':date_debut', $data['date_debut'] ?: null);

        return $stmt->execute();
    }
    public function findEntrepriseIdByEmail(string $emailEntreprise) {
    $sql = "SELECT entreprises.id
            FROM entreprises
            INNER JOIN comptes ON entreprises.compte_id = comptes.id
            WHERE comptes.email = :email
              AND comptes.role_id = '2'
            LIMIT 1";

    $stmt = $this->dbh->prepare($sql);
    $stmt->execute([
        ':email' => $emailEntreprise
    ]);

    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
}
