<?php
require 'Database.php'; // Classe Database
require 'Vente.php'; // Classe PointRelais
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

if (isset($_GET["id"])) {
    $req = "SELECT venteId, prix, quantite, dateAjout, dateLimiteVente, valide, prodId, varieteLibelle, produitLibelle, produitImage, categorieLibelle, uniteLettre 
    from vente inner join variete on vente.varieteId=variete.varieteId inner join produit on variete.produitId=produit.produitId
        inner join categorie on produit.categorieId=categorie.categorieId inner join unite on produit.uniteId=unite.uniteId 
    where prodId=".$_GET["id"]." 
    order by dateAjout";
} else {
    $req = "SELECT venteId, prix, quantite, dateAjout, dateLimiteVente, valide, prodId, varieteLibelle, produitLibelle, produitImage, categorieLibelle, uniteLettre 
from vente inner join variete on vente.varieteId=variete.varieteId inner join produit on variete.produitId=produit.produitId
    inner join categorie on produit.categorieId=categorie.categorieId inner join unite on produit.uniteId=unite.uniteId 
    order by dateAjout";
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

