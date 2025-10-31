<?php
require_once 'config/database.php';
require_once 'app/models/CollectiveRegistration.php';

// Crea connessione database
$database = new Database();
$pdo = $database->getConnection();
$collective = new CollectiveRegistration($pdo);

echo "=== TEMPLATE GENERATO ===\n\n";

try {
    // Usa reflection per accedere al metodo 
    $reflection = new ReflectionClass($collective);
    $method = $reflection->getMethod('generateCsvTemplate');
    $method->setAccessible(true);
    
    $template_data = $method->invoke($collective);
    
    echo "Headers: " . implode(',', $template_data['headers']) . "\n";
    echo "Sample data:\n";
    foreach ($template_data['sample_data'] as $row) {
        echo implode(',', $row) . "\n";
    }
    
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}