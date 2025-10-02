<?php
require 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Inserisci alcuni risultati di test per l'utente ID 1
    $testResults = [
        [
            'user_id' => 1,
            'event_id' => 9,
            'position' => 5,
            'finish_time' => '01:45:30',
            'pace' => '5:15',
            'category' => 'M30-39'
        ],
        [
            'user_id' => 1,
            'event_id' => 10,
            'position' => 3,
            'finish_time' => '00:58:45',
            'pace' => '4:42',
            'category' => 'M30-39'
        ]
    ];
    
    foreach ($testResults as $result) {
        // Controlla se il risultato esiste già
        $check = $conn->prepare("SELECT id FROM event_results WHERE user_id = ? AND event_id = ?");
        $check->execute([$result['user_id'], $result['event_id']]);
        
        if ($check->rowCount() == 0) {
            $insert = $conn->prepare("INSERT INTO event_results (user_id, event_id, position, finish_time, pace, category) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([
                $result['user_id'],
                $result['event_id'], 
                $result['position'],
                $result['finish_time'],
                $result['pace'],
                $result['category']
            ]);
            echo "✅ Risultato inserito per evento {$result['event_id']}\n";
        } else {
            echo "ℹ️  Risultato già esistente per evento {$result['event_id']}\n";
        }
    }
    
    echo "\n🎯 Risultati di test pronti!\n";
    
} catch(PDOException $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
?>