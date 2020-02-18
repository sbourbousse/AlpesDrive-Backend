<?php
require 'Database.php'; // Classe Database
require 'Vente.php'; // Classe PointRelais
require 'Response.php';

include 'function.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); //TODO Modifier
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//Création d'une réponse
$response = new Response;

//Connexion à la base de données
try {
    $dbh = new Database;
    $response->setDb(true, "La connexion à la base de données a réussie");
} catch (PDOException $e) {
    $response->setDb(false, "La connexion à la base de données a échouée :  ". $e->getMessage());
}

if (isset($_GET["id"]) && isset($_GET["userType"]) && $_GET["userType"] == "producteur"){
    $req = "SELECT venteId, prix, quantite, dateAjout, dateLimiteVente, valide, prodId, varieteId from vente where prodId=".$_GET["id"];
} else {
    $req = "SELECT venteId, prix, quantite, dateAjout, dateLimiteVente, valide, prodId, varieteId from vente";
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

