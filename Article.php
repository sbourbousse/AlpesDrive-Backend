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

    public function __construct($quantite, $clientId, $venteId, $pointRelaisId){
        $this->quantite = $quantite;
        $this->clientId = $clientId;
        $this->venteId = $venteId;
        $this->pointRelaisId = $pointRelaisId;
        $this->panierDateAjout = date('Y-m-d h:i:s', time());
    }

    function setPanierClientId($db) {
        //Chercher panier actuel du client
        $req = "SELECT panier.panierId from panier inner join article on panier.panierId=article.panierId where panierRegle=0 and clientId=".$this->clientId;
        $sth = $db->prepare($req);
        $sth->execute();
        //Lit le premier résultat
        $result = $sth->fetch();
        //Si un panier existe
        if($result) {
            //retourner l'id du panier
            $this->panierId = $result['panierId'];
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
}