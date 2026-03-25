<?php

namespace App\Models;

class OffersModel extends Model{
    /*Implémenter la gestion SGBD des offres*/

    public function __construct($info = null){
        if(is_null($info)){
            $this->data=[];
            $this->dbh= new \PDO("mysql:host=localhost;dbname = stage4all", self::ADMIN, self::PASS);
        }else{
            $this->data = $info;
            $this->dbh = new \PDO("mysql:host=localhost;dbname = stage4all", $this->data["email"], $this->data["password"]);
        }
    }

    
    

}