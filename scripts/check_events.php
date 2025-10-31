<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "=== STRUTTURA TABELLA EVENTS ===\n";
    $stmt = $db->query("DESCRIBE events");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    echo "\n=== SAMPLE EVENTS DATA ===\n";
    $stmt = $db->query("SELECT * FROM events LIMIT 2");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($events) {
        echo "Campi disponibili: " . implode(', ', array_keys($events[0])) . "\n\n";
        foreach ($events as $event) {
            echo "ID: " . $event['id'] . "\n";
            echo "  - " . print_r($event, true) . "\n";
        }
    } else {
        echo "Nessun evento trovato\n";
    }
    
} catch(PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
?>