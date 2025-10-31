<?php
/**
 * Configurazione database per SportEvents
 */

class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $conn = null;

    public function __construct() {
        // Detect environment
        $isPiramedia = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'piramedia.it') !== false;
        
        if ($isPiramedia) {
            // Configurazione Piramedia
            $this->host = 'localhost';
            $this->port = '3306';
            $this->db_name = 'biglietteria_piramedia_it';
            $this->username = 'biglietteria_piramedia_it';
            $this->password = 'NI2FjP5TMGtsCF3f';
        } else {
            // Configurazione locale/sviluppo
            $this->host = '127.0.0.1';
            $this->port = '3306';
            $this->db_name = 'eventi_sportivi_db';
            $this->username = 'root';
            $this->password = '';
        }
    }

    public function getConnection() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Errore di connessione database: " . $exception->getMessage());
            return null;
        }
        return $this->conn;
    }
}
?>
