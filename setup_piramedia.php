<?php
/**
 * Script di setup database per Piramedia
 * Esegui questo script dopo aver caricato i file sul server
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurazione database Piramedia
$host = 'localhost';
$dbname = 'biglietteria_piramedia_it';
$username = 'biglietteria_piramedia_it';
$password = 'NI2FjP5TMGtsCF3f';

echo "<h1>Setup Database SportEvents su Piramedia</h1>";

try {
    // Connessione al database
    $dsn = "mysql:host={$host};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Connessione al server MySQL riuscita</p>";
    
    // Crea il database se non esiste
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✓ Database '{$dbname}' creato o già esistente</p>";
    
    // Seleziona il database
    $pdo->exec("USE `{$dbname}`");
    
    // Leggi e esegui lo schema SQL
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        
        // Dividi lo script in statements individuali
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignora errori di tabelle già esistenti
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        
        echo "<p style='color: green;'>✓ Schema database importato</p>";
    } else {
        echo "<p style='color: red;'>✗ File schema.sql non trovato</p>";
    }
    
    // Crea utenti demo se la tabella users è vuota
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        // Admin
        $pdo->exec("INSERT INTO users (nome, cognome, email, password, ruolo, attivo) VALUES 
                   ('Admin', 'SportEvents', 'admin@biglietteria.piramedia.it', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin', 1)");
        
        // Organizzatore
        $pdo->exec("INSERT INTO users (nome, cognome, email, password, ruolo, attivo) VALUES 
                   ('Organizzatore', 'Demo', 'organizer@biglietteria.piramedia.it', '" . password_hash('organizer123', PASSWORD_DEFAULT) . "', 'organizzatore', 1)");
        
        // Partecipante
        $pdo->exec("INSERT INTO users (nome, cognome, email, password, ruolo, attivo) VALUES 
                   ('Partecipante', 'Demo', 'participant@biglietteria.piramedia.it', '" . password_hash('participant123', PASSWORD_DEFAULT) . "', 'partecipante', 1)");
        
        echo "<p style='color: green;'>✓ Utenti demo creati</p>";
        echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #0066cc; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>Credenziali Demo:</h3>";
        echo "<p><strong>Admin:</strong> admin@biglietteria.piramedia.it / admin123</p>";
        echo "<p><strong>Organizzatore:</strong> organizer@biglietteria.piramedia.it / organizer123</p>";
        echo "<p><strong>Partecipante:</strong> participant@biglietteria.piramedia.it / participant123</p>";
        echo "</div>";
    }
    
    // Verifica tabelle
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p style='color: green;'>✓ Tabelle create: " . count($tables) . "</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>{$table}</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>Setup completato con successo!</h2>";
    echo "<p><a href='/'>Vai all'applicazione</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Errore: " . $e->getMessage() . "</p>";
    echo "<p>Verifica le credenziali del database.</p>";
}
?>