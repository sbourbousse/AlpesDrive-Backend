<?php
require_once 'Database.php';

class Entreprise {
    private $id;
    private $name;
    private $iBAN;

    public function __construct() {
        $argv = func_get_args();
        switch( func_num_args() ) {
            case 2:
                self::__construct1( $argv[0], $argv[1] );
                break;
            case 3:
                self::__construct2( $argv[0], $argv[1], $argv[2] );
        }
    }


    function __construct1($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    function __construct2($id, $name, $iBAN) {
        $this->id = $id;
        $this->name = $name;
        $this->iBAN = $iBAN;
    }

    function idExists($db) {
        $req = "SELECT entrepriseId FROM entreprise WHERE entrepriseId=\"".$this->id."\"";
        $sth = $db->prepare($req);
        $sth->execute();
        //Lit le premier résultat
        $result = $sth->fetch();

        if($result)
            return true;
        else
            return false;
    }

    function isValidFields() {
        $valid = true;

        if(strlen($this->id) != 14 || preg_match("/[^0-9]/", $this->id))
            $valid = false;
        if(strlen($this->name) < 3)
            $valid = false;
        if($this->iBAN != null && (strlen($this->iBAN) > 34|| strlen($this->iBAN) < 27))
            $valid = false;

        return $valid;
    }

    public function addToDatabase($db) {
        if ($this->iBAN == null)
            $req = "INSERT INTO entreprise (entrepriseId, entrepriseLibelle) VALUES ( \"".$this->id."\",\"".$this->name."\")";
        else
            $req = "INSERT INTO entreprise (entrepriseId, entrepriseLibelle, entrepriseIBAN) VALUES ( \"".$this->id."\",\"".$this->name."\",\"".$this->iBAN."\")";

        $sth = $db->prepare($req);
        //echo $req;
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }

    }

    public function removeFromDatabase($db) {
        $req = "DELETE FROM entreprise where entrepriseId=\"".$this->id."\"";
        $sth = $db->prepare($req);
        //echo $req;
        if ($sth->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

?>