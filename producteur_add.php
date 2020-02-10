<?php
require 'Database.php'; // Classe Database
require 'Response.php'; // Classe Response
require 'User.php'; // Classe User
require 'Producteur.php'; // Classe Producteur
require 'Entreprise.php'; // Classe Entreprise

include 'function.php';


header("Access-Control-Allow-Origin: *");
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


//Récuperer les données
$json = file_get_contents("php://input");
$data = json_decode($json);

//Liste des champs normalement envoyés
$email = $data->{'email'};
$password = $data->{'password'};
//TODO vérifier le nom des champs data->xxxx concorde avec angular
$prodPrenom = $data->{'prenom'};
$prodNom = $data->{'nom'};
$prodTel = $data->{'telephone'};
$prodAdresse = $data->{'adresse'};
$prodVille = $data->{'ville'};
$prodCodePostal = $data->{'codePostal'};
$entrepriseId = $data->{'entreprise'}->{'id'};
$entrepriseLibelle = $data->{'entreprise'}->{'libelle'};
$entrepriseIBAN = $data->{'entreprise'}->{'iBAN'}; //optionel




//Si l'utilisateur envoie toute les données nécéssaires
if(!somethingMissing($email,$password,$prodPrenom,$prodNom,$prodTel,$prodAdresse,$prodVille,$prodCodePostal,$entrepriseId,$entrepriseLibelle) && !$e) {

    // En cas d'erreur lors d'un l'ajout, il faudra supprimer les données déjà envoyés dans la base de données
    $errorEntreprise = false;
    $errorProducteur = false;

    // Creer des classes avec les donnée recu (User, Entreprise, Producteur)
    $user = new User($email,$password);
    //Générer les identifiant et clé de l'utilisateur
    $user->generate();
    $producteur = new Producteur($prodPrenom, $prodNom, $prodTel, $prodAdresse, $prodVille, $prodCodePostal, $entrepriseId, $user->getId());
    if($entrepriseIBAN == null) {
        $entreprise = new Entreprise($entrepriseId, $entrepriseLibelle);
    } else {
        $entreprise = new Entreprise($entrepriseId, $entrepriseLibelle, $entrepriseIBAN);
    }


    //Si l'email est déja pris
    if ($user->emailExists($dbh)) {
        $response->setNew(false, "Cette email est déja associé a un compte");
    } else if (!($user->isValidFields())) {
        $response->setNew(false, "Un ou plusieurs champs non valides");
    } else {
        if ($user->addToDatabase($dbh)){ //Ajout de l'utilisateur reussi
            if($entreprise->idExists($dbh)) {
                $response->setNew(false, "Cette entreprise est déjè enregistré sur le site");
                $errorEntreprise = true;
            } else if (!($entreprise->isValidFields())) {
                $response->setNew(false, "Un ou plusieurs champs non valides");
                $errorEntreprise = true;
            } else {
                if($entreprise->addToDatabase($dbh)) { //Ajout de l'entreprise reussi
                    if($producteur->phoneExists($dbh)) {
                        $response->setNew(false, "Ce numéro de téléphone est déjà pris");
                        $errorProducteur = true;
                    } else if (!$producteur->isValidFields()) {
                        $response->setNew(false, "Un ou plusieurs champs non valides prod");
                        $errorProducteur = true;
                    } else {
                        if ($producteur->addToDatabase($dbh)) { //Ajout du producteur reussi
                            //TODO envoie de mail
                            if(sendVerifMail($user)){
                                $response->setNew(true, "Ajout reussi");
                            } else {
                                $response->setNew(false, "L'envoi du mail à échoué");
                                $errorProducteur = true;
                            }
                        } else {
                            $response->setNew(false, "L'ajout du producteur dans la base de données a échoué");
                            $errorProducteur = true;
                        }
                    }


                } else {
                    $response->setNew(false, "L'ajout de l'entreprise dans la base de données a échoué");
                    $errorEntreprise = true;
                }

            }
        } else {
            $response->setNew(false, "L'ajout de l'utilisateur dans la base de données a échoué");
        }


    }
    if($errorProducteur){
        $entreprise->removeFromDatabase($dbh);
        $user->removeFromDatabase($dbh);
        //if ($entreprise->removeFromDatabase($dbh))
        //echo 'supprimer entreprise'; //Debogage
        //else
        //echo 'Supression de l\'entreprise a echoue '; //Debogage
        //if ($user->removeFromDatabase($dbh))
        //echo 'supprimer user '; //Debogage
        //else
        //echo 'Supression de l\'utilisateur a echoue '; //Debogage
    }
    if($errorEntreprise) {
        $user->removeFromDatabase($dbh);
        //if ($user->removeFromDatabase($dbh))
        //echo 'supprimer user '; //Debogage
        //else
        //echo 'Supression de l\'utilisateur a echoue '; //Debogage

    }
} else {
    if ($e) {
        $response->setNew(false, "Erreur de connexion à la base de données");
    }
    else if (somethingMissing($email,$password,$prodPrenom,$prodNom,$prodTel,$prodAdresse,$prodVille,$prodCodePostal,$entrepriseId,$entrepriseLibelle)) {
        $response->setNew(false, "Un ou plusieurs champs n'ont pas été reçus");
    }
    else {
        $response->setNew(false, "Erreur inconnu");
    }
}

$response->printResponseJSON();


?>