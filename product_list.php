<?php

require 'Database.php'; // Classe Database
require 'PointRelais.php'; // Classe PointRelais
require 'Response.php';

include 'function.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); //TODO Modifier
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//Création d'une réponse
$response = new Response;

//Connexion à la base de données
try {
    $dbh = new Database;
    $response->setDb(true, "La connexion à la base de données a réussie");
} catch (PDOException $e) {
    $response->setDb(false, "La connexion à la base de données a échouée :  " . $e->getMessage());
}

if(isset($_GET['id']) && isset($_GET['userType']) && $_GET['userType'] == "client") {

    $req = "select vente.venteId, prix, vente.quantite, dateAjout, dateLimiteVente, prodPrenom, prodNom, uniteLibelle, varieteLibelle, produitLibelle, produitImage, categorieLibelle, count(*) as nbPointRelaisProposant
            from vente
                inner join producteur on vente.prodId=producteur.prodId
                inner join proposer p on producteur.prodId = p.prodId
                inner join point_relais pr on p.pointRelaisId = pr.pointRelaisId
                inner join choisir c on pr.pointRelaisId = c.pointRelaisId
                inner join variete on vente.varieteId = variete.varieteId
                inner join produit on variete.produitId=produit.produitId
                inner join categorie on produit.categorieId=categorie.categorieId
                inner join unite on produit.uniteId=unite.uniteId
            WHERE c.clientId=".$_GET["id"]."
                and valide=1
                and vente.venteId not in (SELECT venteId
                            FROM article)
            GROUP BY vente.venteId
            ORDER BY dateAjout desc";
    $sth = $dbh->prepare($req);
    if ($sth->execute()) {
        $result = $sth->fetchAll();
        $response->setData($result);
        $responseCode = 202;
    } else {
        $responseCode = 500;
    }
} else {
    $responseCode = 400;
}


$response->printResponseJSON();

