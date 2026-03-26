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
        $sql = "SELECT *
                FROM offres
                INNER JOIN entreprises ON offres.entreprise_id = entreprises.id
                ORDER BY offres.created_at DESC";

        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}