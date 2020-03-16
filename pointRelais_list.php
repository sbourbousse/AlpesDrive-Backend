<?php
require 'Database.php'; // Classe Database
require 'PointRelais.php'; // Classe PointRelais
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

if (isset($_GET["id"]) && isset($_GET["userType"]) && $_GET["userType"] == "producteur"){
    $req = "select point_relais.pointRelaisId, pointRelaisAdresse, pointRelaisVille, pointRelaisCodePostal, pointRelaisTypeLibelle, entrepriseLibelle 
            from point_relais inner join point_relais_type on point_relais.pointRelaisTypeId=point_relais_type.pointRelaisTypeId inner join entreprise on  point_relais.entrepriseId=entreprise.entrepriseId
                inner join proposer on proposer.pointRelaisId=point_relais.pointRelaisId 
            where proposer.prodId=".$_GET["id"];
} else if (isset($_GET["id"]) && isset($_GET["userType"]) && $_GET["userType"] == "client") {
    $req = "select point_relais.pointRelaisId, pointRelaisAdresse, pointRelaisVille, pointRelaisCodePostal, pointRelaisTypeLibelle, entrepriseLibelle 
            from point_relais inner join point_relais_type on point_relais.pointRelaisTypeId=point_relais_type.pointRelaisTypeId inner join entreprise on  point_relais.entrepriseId=entreprise.entrepriseId
                inner join choisir on choisir.pointRelaisId=point_relais.pointRelaisId 
            where choisir.clientId=".$_GET["id"];
} else {
    $req = "select pointRelaisId, pointRelaisAdresse, pointRelaisVille, pointRelaisCodePostal, pointRelaisTypeLibelle, entrepriseLibelle from point_relais inner join point_relais_type on point_relais.pointRelaisTypeId=point_relais_type.pointRelaisTypeId inner join entreprise on  point_relais.entrepriseId=entreprise.entrepriseId";
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

