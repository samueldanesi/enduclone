<?php
require_once 'config/database.php';

$db = new Database();
$pdo = $db->getConnection();

if ($pdo) {
    // Controlla se esiste già
    $check = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $check->execute(['organizer@example.com']);
    $exists = $check->fetch()['count'] > 0;
    
    if (!$exists) {
        $stmt = $pdo->prepare("INSERT INTO users (nome, cognome, email, password, ruolo, attivo, data_registrazione) VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $result = $stmt->execute(['Organizer', 'Test', 'organizer@example.com', $password, 'organizzatore']);
        echo "Utente creato: " . ($result ? 'SI' : 'NO') . "\n";
    } else {
        echo "Utente già esistente\n";
    }
    
    // Verifica
    $verify = $pdo->prepare("SELECT user_id, email, ruolo FROM users WHERE email = ?");
    $verify->execute(['organizer@example.com']);
    $user = $verify->fetch();
    echo "ID: {$user['user_id']}, Email: {$user['email']}, Ruolo: {$user['ruolo']}\n";
} else {
    echo "Errore connessione database\n";
}
?>