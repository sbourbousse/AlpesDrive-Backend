<?php
require 'Database.php'; // Classe Database
require 'Response.php'; // Classe Response
require 'Vente.php'; // Classe Vente

include 'function.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Retry-After: 10");


//Création d'une réponse
$response = new Response;
$responseCode = 400;

//Connexion à la base de données
try {
    $dbh = new Database;
    $response->setDb(true, "La connexion à la base de données a réussie");
} catch (PDOException $e) {
    $response->setDb(false, "La connexion à la base de données a échouée :  ". $e->getMessage());
}


//Récuperer les données
$json = file_get_contents("php://input");
$data = json_decode($json);

//Liste des champs normalement envoyés
$prix = $data->{'prix'};
$quantite = $data->{'quantite'};
$dateLimiteVente = $data->{'dateLimiteVente'};
$prodId = $data->{'prodId'};
$varieteId = $data->{'varieteId'};



//Si l'utilisateur envoie toute les données nécéssaires
if(!somethingMissing($prix, $quantite, $dateLimiteVente, $prodId, $varieteId) && !$e) {


    $vente = new Vente($prix, $quantite, $dateLimiteVente, $prodId, $varieteId);
    $vente->generate();

    if($vente->addToDatabase($dbh)) {
        $response->setNew(true, "Ajout de la vente réussi");
        $responseCode= 201;
    } else {
        $response->setNew(false, "Ajout de la vente échoué");
        $responseCode= 500;
    }

} else {
    $response->setNew(false, "Un ou plusieurs champs n'ont pas été recu");
    $responseCode= 400;
}

http_response_code($responseCode);
$response->printResponseJSON();
