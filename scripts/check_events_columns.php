<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    try {
        // Controlla le colonne della tabella events
        $query = "SHOW COLUMNS FROM events";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll();
        
        echo "Colonne della tabella 'events':\n";
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
        
        // Controlla anche teams
        echo "\nColonne della tabella 'teams':\n";
        $query = "SHOW COLUMNS FROM teams";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll();
        
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
        
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage() . "\n";
    }
} else {
    echo "Connessione al database fallita\n";
}
?>