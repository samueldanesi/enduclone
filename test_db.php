<?php
require_once __DIR__ . '/config/database.php';

echo "Test connessione database...\n";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "✅ Connessione riuscita!\n";
        
        // Test query
        $stmt = $conn->query("SELECT COUNT(*) as total FROM events");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Query test riuscita: {$result['total']} eventi trovati\n";
        
    } else {
        echo "❌ Connessione fallita!\n";
    }
} catch (Exception $e) {
    echo "❌ Errore: " . $e->getMessage() . "\n";
}
?>
