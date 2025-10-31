<?php
require_once 'config/database.php';
require_once 'app/models/CollectiveRegistration.php';

$file_path = '/Users/dane/endu clone/test_participants.csv';

// Crea connessione database
$database = new Database();
$pdo = $database->getConnection();
$collective = new CollectiveRegistration($pdo);

// Simula la struttura $_FILES
$file_array = [
    'name' => 'test_participants.csv',
    'tmp_name' => $file_path
];

echo "=== DEBUG VALIDAZIONE ===\n\n";

try {
    // Usa reflection per processare il file
    $reflection = new ReflectionClass($collective);
    $processMethod = $reflection->getMethod('processExcelFile');
    $processMethod->setAccessible(true);
    
    $participants = $processMethod->invoke($collective, $file_array);
    
    echo "Partecipanti prima della validazione:\n";
    foreach ($participants as $p) {
        echo "Riga {$p['row_number']}: ";
        echo "Nome='" . ($p['nome'] ?? 'NULL') . "' ";
        echo "Cognome='" . ($p['cognome'] ?? 'NULL') . "' ";
        echo "empty(nome)=" . (empty($p['nome']) ? 'TRUE' : 'FALSE') . " ";
        echo "empty(cognome)=" . (empty($p['cognome']) ? 'TRUE' : 'FALSE') . "\n";
    }
    echo "\n";
    
    // Ora testa la validazione
    $validateMethod = $reflection->getMethod('validateAndCleanParticipants');
    $validateMethod->setAccessible(true);
    
    $result = $validateMethod->invoke($collective, $participants);
    
    echo "Risultato validazione:\n";
    print_r($result);
    
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}