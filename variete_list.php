<?php
require 'Database.php'; // Classe Database
require 'Variete.php'; // Classe PointRelais
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

if (isset ($_GET["proId"])) {
    $req = "SELECT varieteId, varieteLibelle FROM variete WHERE produitId=".$_GET["proId"];
} else {
    $req = "SELECT varieteId, varieteLibelle FROM variete";
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

