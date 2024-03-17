<?php

class Database
{

    private $host = "localhost";
    private $db = "shop_online";
    private $user = "root";
    private $pass = "root";
    private $port = "8888"; // Ajout du port

    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            // Modification de l'URL pour inclure le port
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db, $this->user, $this->pass);
            $this->conn->exec("set names utf8");

            // Affiche un message si la connexion est réussie
            echo "Connexion à la base de données réussie !";
        } catch (PDOException $exception) {
            echo "Database connection failed : " . $exception->getMessage();
        }
        return $this->conn;
    }
}

?>
