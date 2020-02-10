<?php
require 'Database.php'; // Classe Database
require 'User.php'; // Classe User

//Connexion à la base de données
try {
    $dbh = new Database;
} catch (PDOException $e) {
    echo 'Erreur de connexion à la base de données';
}

if (isset($_GET["id"]) && isset($_GET["verif"])) {

    $user = new User(strval($_GET["id"]));
    if($user->checkVerification($dbh, $_GET['verif'])) {

        if ($user->setMailVerified($dbh)) {
            header("location:http://sylvain-bourbousse.fr/index.php");
        } else {
            echo 'L\'activation à échoué';
        }
    } else {
        echo 'Le lien de véfication n\'est pas valide';
    }
}