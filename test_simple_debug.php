<?php
require_once 'config/database.php';
require_once 'app/models/CollectiveRegistration.php';

$file_path = '/Users/dane/endu clone/test_simple.csv';

// Crea connessione database
$database = new Database();
$pdo = $database->getConnection();
$collective = new CollectiveRegistration($pdo);

echo "=== TEST FILE SEMPLICE ===\n\n";
echo "Contenuto:\n";
echo file_get_contents($file_path) . "\n";
echo "========================\n\n";

try {
    // Simula la struttura $_FILES
    $file_array = [
        'name' => 'test_simple.csv',
        'tmp_name' => $file_path
    ];
    
    // Usa reflection per processare il file
    $reflection = new ReflectionClass($collective);
    $processMethod = $reflection->getMethod('processExcelFile');
    $processMethod->setAccessible(true);
    
    $participants = $processMethod->invoke($collective, $file_array);
    
    echo "Partecipanti processati:\n";
    foreach ($participants as $p) {
        echo "- {$p['nome']} {$p['cognome']}\n";
    }
    echo "\n";
    
    // Test validazione
    $validateMethod = $reflection->getMethod('validateAndCleanParticipants');
    $validateMethod->setAccessible(true);
    
    $result = $validateMethod->invoke($collective, $participants);
    
    if (is_array($result) && !isset($result['errors'])) {
        echo "✅ SUCCESSO! Tutti i partecipanti sono validi:\n";
        foreach ($result as $p) {
            echo "- {$p['nome']} {$p['cognome']}\n";
        }
    } else {
        echo "❌ ERRORI:\n";
        print_r($result);
    }
    
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}