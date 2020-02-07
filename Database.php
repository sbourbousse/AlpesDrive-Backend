<?php
class Database extends PDO{
    private $host;
    private $database;
    private $user;
    private $password;

    function __construct() {
        $this->host = "mysql-supergap.alwaysdata.net";
        $this->database = "supergap_alpesdrive";
        $this->user = "supergap";
        $this->password = "Tiy9mcn!";

        $dsn = 'mysql:dbname='.$this->database.';host='.$this->host;
        parent::__construct($dsn, $this->user, $this->password);
    }
}
?>