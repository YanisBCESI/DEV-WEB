<?php 
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
$user = "debian-sys-maint";
$pass = "LYK8WN3Oup7UxkZW";
try{
    $dbh = new PDO('mysql:host=localhost;dbname=workshop', $user, $pass);
} catch(PDOException $e){
    echo("Erreur de connexion");
}

try{
    $sth = $dbh->query("SELECT pseudo FROM utilisateurs");
    $sth=$sth->fetchAll(\PDO::FETCH_ASSOC);
    foreach($sth as $slt){
        var_dump($slt);
    }
} catch(PDOException $e){
    echo("Erreur d'accès a la base)");
}
$sth = null;

$pseudo = "Gandalf';--";
$mdp = "Maia";

try{
    $sth = $dbh->query("SELECT * FROM utilisateurs WHERE pseudo = '".$pseudo."' AND motDePasse = '".$mdp."'");
    $sth = $sth->fetch();
    var_dump($sth);
} catch(PDOException $e){
    echo("Erreur d'accès a la base");
}