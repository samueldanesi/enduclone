<?php
/**
 * Configurazione database per Piramedia
 */

class Database {
    private $host = 'localhost';
    private $port = '3306';
    private $db_name = 'biglietteria_piramedia_it';
    private $username = 'biglietteria_piramedia_it';
    private $password = 'NI2FjP5TMGtsCF3f';
    private $conn = null;

    public function getConnection() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Log successful connection (remove in production)
            error_log("Database connection successful to: {$this->db_name}");
            
        } catch(PDOException $exception) {
            error_log("Errore di connessione database: " . $exception->getMessage());
            error_log("Host: {$this->host}, DB: {$this->db_name}, User: {$this->username}");
            return null;
        }
        return $this->conn;
    }
}
?>