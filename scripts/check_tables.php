<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "=== TABELLE NEL DATABASE ===\n";
    
    $query = "SHOW TABLES";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    echo "\n=== STRUTTURA TABELLA team_messages (se esiste) ===\n";
    
    try {
        $query = "DESCRIBE team_messages";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "{$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
        }
    } catch (Exception $e) {
        echo "Tabella team_messages non esiste\n";
    }
    
    echo "\n=== STRUTTURA TABELLA team_members ===\n";
    
    try {
        $query = "DESCRIBE team_members";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "{$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
        }
    } catch (Exception $e) {
        echo "Tabella team_members non esiste\n";
    }
    
    echo "\n=== STRUTTURA TABELLA user_notifications ===\n";
    
    try {
        $query = "DESCRIBE user_notifications";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "{$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
        }
    } catch (Exception $e) {
        echo "Tabella user_notifications non esiste\n";
    }
    
    echo "\n=== STRUTTURA TABELLA team_join_requests ===\n";
    
    try {
        $query = "DESCRIBE team_join_requests";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "{$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
        }
    } catch (Exception $e) {
        echo "Tabella team_join_requests non esiste\n";
    }
    
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
?>