<?php
// Test rapido per verificare se i modelli funzionano senza errori
require_once 'config/database.php';
require_once 'app/models/Team.php';
require_once 'app/models/CollectiveRegistration.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "✅ Connessione database OK\n";
    
    // Test Team model
    $team = new Team($conn);
    try {
        $teams = $team->getAllActive();
        echo "✅ Team::getAllActive() OK - " . count($teams) . " team trovati\n";
    } catch (Exception $e) {
        echo "❌ Team::getAllActive() Error: " . $e->getMessage() . "\n";
    }
    
    // Test CollectiveRegistration model
    $cr = new CollectiveRegistration($conn);
    try {
        $recent = $cr->getRecentByTeam(1, 5);
        echo "✅ CollectiveRegistration::getRecentByTeam() OK - " . count($recent) . " registrazioni trovate\n";
    } catch (Exception $e) {
        echo "❌ CollectiveRegistration::getRecentByTeam() Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n🔍 Test completato.\n";
} else {
    echo "❌ Connessione database fallita\n";
}
?>