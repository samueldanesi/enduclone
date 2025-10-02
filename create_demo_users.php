<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Password hash per "password123"
$password_hash = password_hash('password123', PASSWORD_DEFAULT);

// Inserisce utenti demo
$users = [
    [
        'nome' => 'Organizzatore',
        'cognome' => 'Demo',
        'email' => 'organizer@example.com',
        'ruolo' => 'organizzatore'
    ],
    [
        'nome' => 'Partecipante', 
        'cognome' => 'Demo',
        'email' => 'participant@example.com',
        'ruolo' => 'atleta'
    ],
    [
        'nome' => 'Admin',
        'cognome' => 'Demo', 
        'email' => 'admin@sportevents.com',
        'ruolo' => 'admin'
    ]
];

foreach ($users as $user) {
    $query = "INSERT INTO users (nome, cognome, email, password, ruolo, attivo) 
              VALUES (:nome, :cognome, :email, :password, :ruolo, 1)
              ON DUPLICATE KEY UPDATE password = :password";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nome', $user['nome']);
    $stmt->bindParam(':cognome', $user['cognome']);
    $stmt->bindParam(':email', $user['email']);
    $stmt->bindParam(':password', $password_hash);
    $stmt->bindParam(':ruolo', $user['ruolo']);
    
    if ($stmt->execute()) {
        echo "✅ Utente creato: " . $user['email'] . " (" . $user['ruolo'] . ")\n";
    } else {
        echo "❌ Errore nel creare: " . $user['email'] . "\n";
    }
}

echo "\n🔑 Password per tutti gli account: password123\n";
?>