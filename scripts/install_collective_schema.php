<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Installazione schema iscrizioni collettive...\n";
    
    $sql = file_get_contents('database/team_collective_registrations_fixed.sql');
    
    // Dividi in query separate (il file contiene multiple CREATE TABLE)
    $queries = explode(';', $sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $conn->exec($query);
        }
    }
    
    echo "✅ Schema installato con successo!\n";
    echo "✅ Tabelle create:\n";
    echo "   - team_collective_registrations\n";
    echo "   - team_collective_participants\n";
    echo "   - collective_discount_rules\n";
    echo "   - team_collective_stats (view)\n";
    
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
}
?>
