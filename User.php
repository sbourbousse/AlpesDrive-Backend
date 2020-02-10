<?php
require_once 'Database.php';

class User {
    private $id;
    private $email;
    private $motDePasse;
    private $verifie;
    private $cleMail;
    private $dateInscription;

    public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 1:
                self::__construct1( $argv[0] );
                break;
            case 2:
                self::__construct2( $argv[0], $argv[1]);
        }
    }

    function __construct1($id) {
        $this->id = $id;
    }

    function __construct2($email, $password) {
        $this->email = $email;
        $this->motDePasse = $password;
    }

    function auth($db) {

        //Verifier si l'utilisateur existe
        $req = "SELECT utilisateurVerifie FROM utilisateur WHERE utilisateurMail=\"".$this->email."\" and utilisateurMotDePasse=\"".md5($this->motDePasse)."\"";
        $sth = $db->prepare($req);
        $sth->execute();
        //Lit le premier résultat
        $result = $sth->fetch();

        if($result) {
            if ($result["utilisateurVerifie"] == true) {
                $statusCode = 1;  //L'utilisateur existe et son mail est vérifié

            } else {
                $statusCode = 2; //L'utilisateur existe mais son mail n'est pas vérifié
            }
        }
        else {
            $statusCode = 3; //L'utilisateur n'existe pas
        }


        return $statusCode;
    }

    public function isValidFields() {
        $valid = true;

        if(!(filter_var($this->email, FILTER_VALIDATE_EMAIL)))
            $valid = false;
        if(strlen($this->motDePasse)<6)
            $valid = false;

        return $valid;
    }

    public function generate() {
        $this->id = time() - 999999;
        $this->verifie = 0;
        $this->cleMail = rand(1 , 2147483647);
        $this->dateInscription = date("Y-m-d");
    }

    public function emailExists($db) {
        $req = "SELECT utilisateurMail FROM utilisateur WHERE utilisateurMail=\"".$this->email."\"";
        $sth = $db->prepare($req);
        $sth->execute();
        //Lit le premier résultat
        $result = $sth->fetch();

        if($result)
            return true;
        else
            return false;
    }

    public function isValidMailKey($db) {
        $req = "SELECT utilisateurVerifie FROM utilisateur WHERE utilisateurMail=\"".$this->email."\"";
        $sth = $db->prepare($req);
        $sth->execute();
        //Lit le premier résultat
        $result = $sth->fetch();

        if($result["utilisateurVerifie"] == 1)
            return true;
        else
            return false;
    }

    public function addToDatabase($db) {
        $req = "INSERT INTO utilisateur (utilisateurId, utilisateurMail, utilisateurMotDePasse, utilisateurVerifie, utilisateurcleMail, utilisateurDateInscription) VALUES ( ".$this->id.",\"".$this->email."\",\"".md5($this->motDePasse)."\",".$this->verifie.",".$this->cleMail.",\"".$this->dateInscription."\")";
        $sth = $db->prepare($req);
        //echo $req;
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }

    }

    public  function removeFromDatabase($db) {
        $req = "DELETE FROM utilisateur where utilisateurId=".$this->id;
        $sth = $db->prepare($req);
        //echo $req;
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function getId() {
        return $this->id;
    }

    public function getCleMail() {
        return $this->cleMail;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setMailVerified($db) {
        $req = "UPDATE utilisateur set utilisateurVerifie=1 where utilisateurId=".$this->id;
        $sth = $db->prepare($req);
        //echo $req;
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function checkVerification($db, $keyToVerify) {
        $req = "SELECT utilisateurCleMail FROM utilisateur WHERE utilisateurId=".$this->id;
        $sth = $db->prepare($req);
        $sth->execute();
        $result = $sth->fetch();

        /*echo 'Ma requete ---->'.$req;
        echo 'Resultat renvoyé --->'.$result["utilisateurCleMail"];
        echo 'Cryptage du résultat renvoyé --->'.md5($result["utilisateurCleMail"]);
        echo 'Ma clé du lien à comparer --->'.$keyToVerify; */


        if (md5($result["utilisateurCleMail"]) == $keyToVerify) {
            return true;
            echo 'bon';
        } else {
            return false;
        }
    }
}