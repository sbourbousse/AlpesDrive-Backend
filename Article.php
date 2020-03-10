<?php


class Article
{
    private $quantite;
    private $panierDateAjout;
    private $panierDateRecuperer;
    private $clientId;
    private $venteId;
    private $pointRelaisId;
    private $panierId;

    public function __construct(){

        $argv = func_get_args();
        switch( func_num_args() ) {
            case 3:
                self::__construct1( $argv[0], $argv[1], $argv[2]);
                break;
            case 4:
                self::__construct2( $argv[0], $argv[1], $argv[2], $argv[3]);
        }

    }

    public function __construct1($id, $clientId, $db){
        $this->venteId = $id;
        $this->clientId = $clientId;
        $this->panierId = $this->getPanierId($db);
    }

    public function __construct2($quantite, $clientId, $venteId, $pointRelaisId){
        $this->quantite = $quantite;
        $this->clientId = $clientId;
        $this->venteId = $venteId;
        $this->pointRelaisId = $pointRelaisId;
        $this->panierDateAjout = date('Y-m-d h:i:s', time());
    }

    function setPanierClientId($db) {
        //Chercher panier actuel du client

        //Si un panier existe
        if($this->getPanierId($db)) {
            //retourner l'id du panier
            $this->panierId = $this->getPanierId($db);
            return true;
        } else { //Si aucun panier existe

            $idPanier = time() - 11111;
            //Creer un nouveau panier
            $req = "INSERT INTO panier (panierId, panierRegle, panierDateRegle) values (".$idPanier.",0,NULL)";
            $sth = $db->prepare($req);
            if ($sth->execute()) {
                $this->panierId = $idPanier;
                return true;
            } else {
                return false;
            }
        }
    }

    function alreadyTaken($db) {
        //vérifier que la vente est présente dans la table article
        $req = "SELECT venteId from article where venteId=".$this->venteId;
        $sth = $db->prepare($req);
        $sth->execute();
        $res = $sth->fetch();
        if ($res)  return true;
        else return false;
    }


    function addToCartInDatabase($db) {
        $req = "INSERT INTO article (quantite, panierDateAjout, panierDateRecuperer, clientId, venteId, pointRelaisId, panierId) 
            values (".$this->quantite.", \"".$this->panierDateAjout."\", NULL, ".$this->clientId.", ".$this->venteId.", ".$this->pointRelaisId.",".$this->panierId.")";
        $sth = $db->prepare($req);
        if ($sth->execute())  return true;
        else return false;
    }

    function removeFromDatabase($db) {
        //Verifier que l'article que l'on veut supprimer est le dernier article du panier
        if($this->isSeulDuPanier($db))
            $seulArticle = true;
        else
            $seulArticle = false;

        //Supprimer l'article de la base de données
        $req = "DELETE FROM article where venteId=".$this->venteId;
        $sth = $db->prepare($req);
        //echo $req;
        $articleIsRemoved = $sth->execute();

        //Supprimer le panier après le dernier article (contrainte d'intégrité référentielle
        if($seulArticle)
            $this->removePanierFromDatabase($db);

        if ($articleIsRemoved) return true;
        else return false;
    }



    function getPanierId($db) {
        //retourner l'id du panier du client ou null si il n'existe pas
        $req = "SELECT panier.panierId from panier inner join article on panier.panierId=article.panierId where panierRegle=0 and clientId=".$this->clientId;
        $sth = $db->prepare($req);
        $sth->execute();

        //echo $req;
        //Lit le premier résultat
        $result = $sth->fetch();
        if($result['panierId'])
            return $result['panierId'];
        else
            return null;
    }

    function isSeulDuPanier($db) {
        $req = "SELECT count(*) as nbArticle FROM article WHERE panierId=".$this->panierId;
        //echo $req;

        $sth = $db->prepare($req);
        $sth->execute();
        //Lit le premier résultat
        $result = $sth->fetch();

        if($result["nbArticle"]<=1)
            return true;
        else return false;

    }

    function removePanierFromDatabase($db) {
        $req = "DELETE FROM panier WHERE panierId=".$this->panierId;

        $sth = $db->prepare($req);
        if($sth->execute()) {
            return true;
        } else {
            return false;
        }
    }
}