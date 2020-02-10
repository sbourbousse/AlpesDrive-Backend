<?php
require 'Database.php'; // Classe Database
require 'Response.php'; // Classe Response
require 'User.php'; // Classe User
require 'Client.php'; // Classe Client
require 'Entreprise.php'; // Classe Entreprise

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
$email = $data->{'email'};
$password = $data->{'password'};
//TODO vérifier le nom des champs data->xxxx concorde avec angular
$cliPrenom = $data->{'prenom'};
$cliNom = $data->{'nom'};
$cliTel = $data->{'telephone'};
$cliAdresse = $data->{'adresse'};
$cliVille = $data->{'ville'};
$cliCodePostal = $data->{'codePostal'};



//Si l'utilisateur envoie toute les données nécéssaires
if(!somethingMissing($email,$password,$cliPrenom,$cliNom,$cliTel,$cliAdresse,$cliVille,$cliCodePostal) && !$e) {

    // En cas d'erreur lors d'un l'ajout, il faudra supprimer les données déjà envoyés dans la base de données
    $errorEntreprise = false;
    $errorClient = false;

    // Creer des classes avec les donnée recu (User, Client)
    $user = new User($email,$password);
    //Générer les identifiant et clé de l'utilisateur
    $user->generate();
    $client = new Client($cliPrenom, $cliNom, $cliTel, $cliAdresse, $cliVille, $cliCodePostal, $user->getId());//TODO


    //Si l'email est déja pris
    if ($user->emailExists($dbh)) {
        $response->setNew(false, "Cette email est déja associé a un compte");
        $responseCode = 409;
    } else if (!($user->isValidFields())) {
        $response->setNew(false, "Un ou plusieurs champs non valides");
        $responseCode = 400;
    } else {
        if ($user->addToDatabase($dbh)) { //Ajout de l'utilisateur reussi
            if ($client->phoneExists($dbh)) {
                $response->setNew(false, "Ce numéro de téléphone est déjà pris");
                $responseCode = 409;
                $errorClient = true;
            } else if (!$client->isValidFields()) {
                $response->setNew(false, "Un ou plusieurs champs non valides");
                $responseCode = 400;
                $errorClient = true;
            } else {
                if ($client->addToDatabase($dbh)) { //Ajout du client reussi
                    //TODO envoie de mail
                    if (sendVerifMail($user)) {
                        $response->setNew(true, "Ajout reussi");
                        $responseCode = 201;
                    } else {
                        $response->setNew(false, "L'envoi du mail à échoué");
                        $errorClient = true;
                        $responseCode = 500;
                    }
                } else {
                    $response->setNew(false, "L'ajout du client dans la base de données a échoué");
                    $errorClient = true;
                    $responseCode = 500;
                }
            }
        } else {
            $response->setNew(false, "L'ajout de l'utilisateur dans la base de données a échoué");
            $responseCode = 500;
        }
    }

    if($errorClient){
        $user->removeFromDatabase($dbh);
    }
} else {
    if ($e) {
        $response->setNew(false, "Erreur de connexion à la base de données");
        $responseCode = 500;
    }
    else if (somethingMissing($email,$password,$cliPrenom,$cliNom,$cliTel,$cliAdresse,$cliVille,$cliCodePostal)) {
        $response->setNew(false, "Un ou plusieurs champs n'ont pas été reçus");
        $responseCode = 400;
    }
    else {
        $response->setNew(false, "Erreur inconnu");
        $responseCode = 400;
    }
}

http_response_code($responseCode);
$response->printResponseJSON();
