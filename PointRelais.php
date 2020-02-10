<?php
require_once 'Database.php';
//require_once 'Entreprise.php';

class PointRelais {
    private $id;
    private $firstname;
    private $lastname;
    private $phone;
    private $address;
    private $city;
    private $postCode;
    private $type;
    private $entrepriseId;
    private $userId;
    private $deleted;

    public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 1:
                self::__construct1( $argv[0] );
                break;
            case 9:
                self::__construct2( $argv[0], $argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6], $argv[7], $argv[8] );
        }
    }

    function __construct1($id) {
        $this->id = $id;

    }

    function __construct2($firstname, $lastname, $phone, $address, $city, $postCode, $type, $entrepriseId, $userId ) {
        $this->id = time() - 888888;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->phone = $phone;
        $this->address = $address;
        $this->city = $city;
        $this->postCode = $postCode;
        $this->type = $type;
        $this->entrepriseId = $entrepriseId;
        $this->userId = $userId;
        $this->deleted = 0;
    }

    function addToDatabase($db) {
        $req = "INSERT INTO point_relais (pointRelaisId, pointRelaisPrenomGerant, pointRelaisNomGerant, pointRelaisTel, pointRelaisAdresse, pointRelaisVille, pointRelaisCodePostal, entrepriseId, utilisateurId, supprime) VALUES (".$this->id.",\"".$this->firstname."\",\"".$this->lastname."\",\"".$this->phone."\",\"".$this->address."\",\"".$this->city."\",\"".$this->postCode."\",\"".$this->entrepriseId."\",".$this->userId.",0)";

        $sth = $db->prepare($req);
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function removeFromDatabase($db) {
        $req = "DELETE FROM point_relais where pointRelaisId=".$this->id;

        $sth = $db->prepare($req);
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function typeExists($db) {
        $req = "SELECT pointRelaisTypeId from point_relais_type where pointRelaisTypeId = ".$this->type;
        //echo $req;
        $sth = $db->prepare($req);
        $sth->execute();
        $result = $sth->fetch();

        if($result)
            return true;
        else
            return false;
    }

    function phoneExists($db) {
        $req = "SELECT pointRelaisTel FROM point_relais where pointRelaisTel=\"".$this->phone."\"";
        $sth = $db->prepare($req);
        $sth->execute();
        $result = $sth->fetch();

        if($result)
            return true;
        else
            return false;
    }

    function isValidFields() {
        $valid = true;

        //TODO empecher chiffre dans ville nom et prenom
        if(strlen($this->firstname) < 3 || strlen($this->fisrtname) > 64 ||
            strlen($this->lastname) <3 || strlen($this->lastname) > 64 ||
            !preg_match("/^[0-0]{1}[6-7]{1}[0-9]{8}$/", $this->phone) ||
            strlen($this->address) <3 || strlen($this->address) > 128 ||
            strlen($this->postCode) != 5 || !preg_match("/[0-9]/",$this->postCode) ||
            strlen($this->city) < 3 || strlen($this->city) > 128)
            $valid = false;

        /*
        if(strlen($this->firstname) <3 || strlen($this->firstname) > 64)
            echo 'erreur de prenom';
        if (strlen($this->lastname) <3 || strlen($this->lastname) > 64)
            echo 'erreur de nom';
        if (!preg_match("/^[0-0]{1}[6-7]{1}[0-9]{8}$/", $this->phone))
            echo 'erreur de telephone';
        if (strlen($this->address) <3 || strlen($this->address) > 128)
            echo 'erreur dadresse';
        if (strlen($this->postCode) != 5 || !preg_match("/[0-9]/",$this->postCode))
            echo 'erreur de code postale';
        if (strlen($this->city) < 3 || strlen($this->city) > 128)
            echo 'erreur de ville';
        */


        return $valid;
    }

}