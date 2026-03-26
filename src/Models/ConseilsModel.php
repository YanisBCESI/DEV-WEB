<?php

namespace App\Models;

class ConseilsModel extends Model{

     public function __construct(){
         $this->data=[];
         $this->dbh= new \PDO("mysql:host=localhost;dbname=stage4all", self::ADMIN, self::PASS);
         }
     public function getData(){
             return $this->data;
         }

     public function getFromDataBase(){
        $stmt = $this->dbh->prepare("SELECT * FROM conseils");
        $stmt->execute();
        $resultats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $resultats;
     }
}