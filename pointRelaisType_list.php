<?php
require 'Database.php'; // Classe Database
require 'PointRelais.php'; // Classe PointRelais
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

$req = "select pointRelaisTypeId, pointRelaisTypeLibelle from point_relais_type";
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

