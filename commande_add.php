<?php
require 'Database.php'; // Classe Database
require 'Response.php'; // Classe Response
require 'Article.php'; // Classe Article
include 'function.php';

include_once "headers.php";

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
$idClient = $data->{'idClient'};

//récupérer l'id du panier du client

//update le panier



//Si l'utilisateur envoie toute les données nécéssaires
if (!somethingMissing($idClient) && !$e) {
    $reqRecupPanierId = "SELECT panier.panierId 
                        FROM panier 
                            inner join article on panier.panierId=article.panierId
                        WHERE clientId=".$idClient."
                            and panierRegle=false";
    $sth = $dbh->prepare($reqRecupPanierId);
    $sth->execute();
    $resRecupPanierId = $sth->fetch();
    //echo $reqRecupPanierId;
    if ($resRecupPanierId) {
        $currentDate = date("Y-m-d h:i:s");
        $reqUpdatePanier = "UPDATE panier SET panierRegle=true, panierDateRegle=\"".$currentDate."\" WHERE panierId=".$resRecupPanierId["panierId"];
        $sth = $dbh->prepare($reqUpdatePanier);

        if ($sth->execute()) {
            $response->setUpdate(true, "La commande à bien été passé");
            //TODO sendCommandeConfirmMail($idClient)

        } else {
            $response->setUpdate(false, "Une erreur est survenu lors du passage de la commande");
        }
    } else {
        $response->setUpdate(false, "Nous n'avons pas pu trouver le panier du client");
    }
} else {
    $response->setUpdate(false, "Un des champs n'a pas été envoyé");
}

//http_response_code($responseCode);
$response->printResponseJSON();
