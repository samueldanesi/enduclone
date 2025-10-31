<?php
// Configurazione di base
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Avvia sessione
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurazioni base
define('BASE_URL', 'https://biglietteria.piramedia.it');
define('SITE_NAME', 'SportEvents');

// Database
class Database {
    private $host = 'localhost';
    private $db_name = 'biglietteria_piramedia_it';
    private $username = 'biglietteria_piramedia_it';
    private $password = 'Nl2FjP5TMGtsCF8f';
    private $conn = null;

    public function getConnection() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            return null;
        }
    }
}

// Test connessione
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Errore connessione database");
}

// Router semplice
$request = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request, PHP_URL_PATH);

// Homepage semplice
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportEvents - Gestione Eventi Sportivi</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
        }
        h1 { 
            color: #333; 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .status { 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            color: #155724; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 20px 0; 
        }
        .btn { 
            display: inline-block; 
            padding: 12px 24px; 
            background: #667eea; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 10px; 
        }
        .btn:hover { 
            background: #5a6fd8; 
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-top: 30px; 
        }
        .card { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏆 SportEvents</h1>
        <div class="status">
            <strong>✅ Applicazione Online!</strong><br>
            Database connesso e funzionante.<br>
            Deployment completato con successo su Piramedia.
        </div>
        
        <div class="grid">
            <div class="card">
                <h3>👥 Gestione Utenti</h3>
                <p>Sistema completo di registrazione e login</p>
                <a href="/login" class="btn">Accedi</a>
            </div>
            <div class="card">
                <h3>🏃 Eventi Sportivi</h3>
                <p>Crea e gestisci eventi sportivi</p>
                <a href="/events" class="btn">Eventi</a>
            </div>
            <div class="card">
                <h3>🛒 Shop</h3>
                <p>Vendita prodotti e merchandise</p>
                <a href="/shop" class="btn">Shop</a>
            </div>
            <div class="card">
                <h3>👥 Community</h3>
                <p>Forum e discussioni</p>
                <a href="/community" class="btn">Community</a>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 40px; color: #666;">
            <p><strong>Deployment Info:</strong></p>
            <p>Server: biglietteria.piramedia.it | Database: Connected | Status: Live</p>
        </div>
    </div>
</body>
</html>