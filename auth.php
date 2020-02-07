<?php
require 'Database.php'; // Classe Database
require 'Response.php'; // Classe Response
require 'User.php'; // Classe Response

include 'function.php';


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//Création d'un réponse
$response = new Response;

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

$email = $data->{'email'};
$password = $data->{'password'};

//Si l'utilisateur envoie des données
if(!somethingMissing($email,$password) && !$e){


    $user = new User($email,$password);


    if($user->auth($dbh) == 1){
        $response->setAuth(true, "Connexion réussie");
    } else if ($user->auth($dbh) == 2) {
        $response->setAuth(false, "Votre compte n'a pas été vérifié, veuillez consulter votre boite mail");
    } else if ($user->auth($dbh) == 3) {
        $response->setAuth(false, "Email ou mot de passe incorrect");
    }
} else {
    if ($e) {
        $response->setAuth(false, "Erreur de connexion à la base de données");
    }
    else if (somethingMissing($email,$password)) {
        $response->setAuth(false, "Un ou plusieurs champs n'ont pas été reçus");
    }
    else {
        $response->setAuth(false, "Erreur inconnu");
    }
}

//Afficher la réponse
$response->printResponseJSON();


?>