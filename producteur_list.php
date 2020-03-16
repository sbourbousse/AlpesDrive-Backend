<?php
require 'Database.php'; // Classe Database
require 'Producteur.php'; // Classe PointRelais
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

if (isset($_GET["id"]) && isset($_GET["userType"]) && $_GET["userType"] == "point_relais"){
    $req = "SELECT producteur.prodId, prodPrenom, prodNom, prodTel, prodAdresse, prodVille, prodCodePostal, entrepriseLibelle 
    FROM producteur inner join entreprise on producteur.entrepriseId=entreprise.entrepriseId inner join proposer on producteur.prodId=proposer.prodId 
    WHERE proposer.pointRelaisId=".$_GET["id"];
} else {
    $req = "SELECT producteur.prodId, prodPrenom, prodNom, prodTel, prodAdresse, prodVille, prodCodePostal, entrepriseLibelle 
    FROM producteur inner join entreprise on producteur.entrepriseId=entreprise.entrepriseId";
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

