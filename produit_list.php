<?php
require 'Database.php'; // Classe Database
require 'Produit.php'; // Classe PointRelais
require 'Response.php';

include 'function.php';

include_once "headers.php";

//Création d'une réponse
$response = new Response;

//Connexion à la base de données
try {
    $dbh = new Database;
    $response->setDb(true, "La connexion à la base de données a réussie");
} catch (PDOException $e) {
    $response->setDb(false, "La connexion à la base de données a échouée :  ". $e->getMessage());
}

if (isset ($_GET["catId"])) {
    $req = "SELECT produitId, produitLibelle, produitImage, unite.uniteId, uniteLibelle, uniteLettre, uniteQuantiteVente 
            FROM produit inner join unite on produit.uniteId = unite.uniteId 
            WHERE categorieId=".$_GET["catId"];
} else {
    $req = "SELECT produitId, produitLibelle, produitImage, unite.uniteId, uniteLibelle, uniteLettre, uniteQuantiteVente 
            FROM produit inner join unite on produit.uniteId = unite.uniteId";
}
//echo $req;
$sth = $dbh->prepare($req);
if($sth->execute()) {
    $result = $sth->fetchAll();
    $response->setData($result);
    $responseCode = 202;
} else {
    $responseCode = 500;
}


$response->printResponseJSON();

