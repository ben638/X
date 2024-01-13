<?php
    if (basename(getcwd()) == "api")
    {
        require "../lib/constantes.inc.php";
    }
    else {
        require "./lib/constantes.inc.php";
    }
    
    function dbConnect()
    {
        static $dbc = null;
        if ($dbc == null) {
            try {
                $dbc = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME, DBUSER, DBPWD, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_PERSISTENT => true
                ));
            }
            catch (PDOException $e) {
                echo 'Erreur : ' . $e->getMessage() . '<br />';
                echo 'N° : ' . $e->getCode();
                die('Could not connect to MySQL');
            }
        }
        return $dbc;
    }
?>