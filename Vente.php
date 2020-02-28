<?php
require_once 'Database.php';

class Vente {
    private $id;
    private $prix;
    private $quantite;
    private $dateAjout;
    private $dateLimiteVente;
    private $valide;
    private $prodId;
    private $varieteId;

    public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 5:
                self::__construct1( $argv[0], $argv[1], $argv[2], $argv[3], $argv[4] );
        }
    }

    function __construct1($prix, $quantite, $dateLimiteVente, $prodId, $varieteId) {
        $this->prix = $prix;
        $this->quantite = $quantite;
        $this->dateLimiteVente = $dateLimiteVente;
        $this->prodId = $prodId;
        $this->varieteId = $varieteId;
    }

    public function isValidFields() {

    }

    public function generate() {
        $this->id = time() - 999999;
        $this->dateAjout = date('Y-m-d h:i:s', time());
        $this->valide = 0;
    }

    public function addToDatabase($db) {
        $req = "INSERT INTO vente (venteId, prix, quantite, dateAjout, dateLimiteVente, valide, prodId, varieteId) 
VALUES ( ".$this->id.", ".$this->prix.", ".$this->quantite.", \"".$this->dateAjout."\", \"".$this->dateLimiteVente."\", ".$this->valide.", 
 ".$this->prodId.", ".$this->varieteId.")";
        $sth = $db->prepare($req);
        //echo $req;
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }
    }
}