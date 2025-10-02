<?php
require 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Controlla se esiste la tabella event_results
    $result = $conn->query("SHOW TABLES LIKE 'event_results'");
    if ($result->rowCount() > 0) {
        echo "✅ Tabella event_results esiste\n";
        
        // Mostra struttura
        $structure = $conn->query("DESCRIBE event_results");
        echo "\n📋 Struttura tabella:\n";
        while ($row = $structure->fetch()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "❌ Tabella event_results NON esiste\n";
        
        // Crea la tabella
        $sql = "CREATE TABLE event_results (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            evento_id INT NOT NULL,
            position_finale INT,
            tempo_finale TIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (evento_id) REFERENCES events(id)
        )";
        
        $conn->exec($sql);
        echo "✅ Tabella event_results creata\n";
    }
    
} catch(PDOException $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
?>