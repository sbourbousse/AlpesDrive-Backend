<?php

require 'Database.php'; // Classe Database
require 'Response.php'; // Classe Response
require 'Article.php'; // Classe Article
include 'function.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); //TODO Modifier
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
    $response->setDb(false, "La connexion à la base de données a échouée :  " . $e->getMessage());
}


//Récuperer les données
$json = file_get_contents("php://input");
$data = json_decode($json);

//Liste des champs normalement envoyés
$quantite = $data->{'quantite'};
$clientId = $data->{'clientId'};
$venteId = $data->{'venteId'};
$pointRelaisId = $data->{'pointRelaisId'};


//Si l'utilisateur envoie toute les données nécéssaires
if (!somethingMissing($quantite, $clientId, $venteId, $pointRelaisId) && !$e) {

    // En cas d'erreur lors d'un l'ajout, il faudra supprimer les données déjà envoyés dans la base de données
    $errorPanier = false;
    $errorArticle = false;

    $article = new Article($quantite, $clientId, $venteId, $pointRelaisId);

    if($article->setPanierClientId($dbh)) {
        if (!($article->alreadyTaken($dbh))) {
            if ($article->addToCartInDatabase($dbh)) {
                $response->setNew(true,"L'article à été ajouté à votre panier");
            } else { //Si une erreur se produit lors de l'ajout dans la base de données
                $response->setNew(false,"Une erreur s'est produite lors de l'ajout de l'article dans le panier");
            }
        } else { //Si le client veut ajouter un article déja dans présent dans le panier d'un client
            //Erreur l'article n'est plus disponible
            $response->setNew(false,"L'article n'est plus disponible");
        }
    } else {
        //Erreur le panier n'a pas pu etre créé
        $response->setNew(false,"Le panier n'a pas pu être créé");
    }
} else {
    //Il manque un champs
    $response->setNew(false,"Un des champs n'a pas été envoyé");
}

if ($errorArticle) {
    //$article->removeArticleFromDatabase($dbh);
}

//http_response_code($responseCode);
$response->printResponseJSON();
