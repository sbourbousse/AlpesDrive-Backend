<?php
require 'Database.php'; // Classe Database
require 'Vente.php'; // Classe PointRelais
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
    $response->setDb(false, "La connexion à la base de données a échouée :  ". $e->getMessage());
}

if (isset($_GET["id"])) {
    $tab = [];
    $reqVenteInfo = "select venteId, prix, quantite, dateAjout, dateLimiteVente, prodPrenom, prodNom, uniteLibelle, varieteLibelle, produitLibelle, produitImage, categorieLibelle 
    from vente 
        inner join producteur on vente.prodId=producteur.prodId
        inner join variete on vente.varieteId = variete.varieteId 
        inner join produit on variete.produitId=produit.produitId 
        inner join categorie on produit.categorieId=categorie.categorieId 
        inner join unite on produit.uniteId=unite.uniteId 
    where venteId=".$_GET['id'];
    $sth = $dbh->prepare($reqVenteInfo);
    $sth->execute();
    $resVenteInfo = $sth->fetch();

    // print_r($resVenteInfo);
    if($resVenteInfo != null) {

        // liste des points relais propsant la vente
        $reqListePointRelais = "SELECT point_relais.pointRelaisId, pointRelaisAdresse, pointRelaisVille, pointRelaisCodePostal, entrepriseLibelle, point_relais_type.*
        from point_relais
                 inner join proposer on proposer.pointRelaisId=point_relais.pointRelaisId
                 inner join producteur on proposer.prodId=producteur.prodId
                 inner join vente on producteur.prodId=vente.prodId
                 inner join entreprise on point_relais.entrepriseId=entreprise.entrepriseId
                 inner join point_relais_type on point_relais.pointRelaisTypeId=point_relais.pointRelaisTypeId
        where point_relais_type.pointRelaisTypeId=point_relais.pointRelaisTypeId and vente.venteId=".$_GET["id"];

        $sth = $dbh->prepare($reqListePointRelais);
        $sth->execute();
        $resListePointRelais = $sth->fetchAll();
        $tab = $resVenteInfo;
        $tab["pointRelais"] = $resListePointRelais;
        $response->setData($tab);
    }
}

$response->printResponseJSON();