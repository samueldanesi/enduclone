<?php
require_once 'config/database.php';
require_once 'app/models/CollectiveRegistration.php';

$file_path = '/Users/dane/endu clone/test_participants.csv';

if (!file_exists($file_path)) {
    die("File non trovato: $file_path\n");
}

echo "=== DEBUG CSV PROCESSING ===\n\n";

// Leggi il file manualmente per vedere cosa contiene
$content = file_get_contents($file_path);
echo "Contenuto del file:\n";
echo $content . "\n";
echo "======================\n\n";

// Crea connessione database
$database = new Database();
$pdo = $database->getConnection();

// Ora testa il parsing
$collective = new CollectiveRegistration($pdo);

try {
    // Simula la struttura $_FILES
    $file_array = [
        'name' => 'test_participants.csv',
        'tmp_name' => $file_path
    ];
    
    // Usa reflection per accedere al metodo privato
    $reflection = new ReflectionClass($collective);
    $method = $reflection->getMethod('processExcelFile');
    $method->setAccessible(true);
    
    $result = $method->invoke($collective, $file_array);
    
    echo "Risultato del parsing:\n";
    print_r($result);
    
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}