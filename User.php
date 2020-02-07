<?php
require_once 'Database.php';

class User {
    private $id;
    private $email;
    private $motDePasse;
    private $verifie;
    private $cleMail;


    public function __construct($email, $password) {
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

        if($result["utilisateurVerifie"] == true){
            $statusCode = 1;  //L'utilisateur existe et son mail est vérifié
        } else if ($result["utilisateurVerifie"] == false) {
            $statusCode = 2; //L'utilisateur existe mais son mail n'est pas vérifié

        } else {
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
        $req = "INSERT INTO utilisateur (utilisateurId, utilisateurMail, utilisateurMotDePasse, utilisateurVerifie, utilisateurcleMail) VALUES ( ".$this->id.",\"".$this->email."\",\"".md5($this->motDePasse)."\",".$this->verifie.",".$this->cleMail.")";
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
}