<?php

require 'Database.php'; // Classe Database
require 'Article.php'; // Classe Article
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
    $response->setDb(false, "La connexion à la base de données a échouée :  " . $e->getMessage());
}
//Récuperer les données
$json = file_get_contents("php://input");
$data = json_decode($json);

//Liste des champs normalement envoyés
$venteId = $data->{'idVente'};
$clientId = $data->{'idClient'};


if(!somethingMissing($venteId, $clientId)) {
    $article = new Article($venteId, $clientId, $dbh);
    if($article->removeFromDatabase($dbh)) {
        $response->setDelete(true,"L'article à été supprimé'");

    } else {
        $response->setDelete(false,"La suppression à échoué");
    }
} else {
    $response->setDelete(false,"Il manque un champs pour supprimer l'article du panier");
}

$response->printResponseJSON();