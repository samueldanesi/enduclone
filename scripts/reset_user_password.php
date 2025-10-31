<?php
// Usage: php scripts/reset_user_password.php email@example.com [new_password]
require_once __DIR__ . '/../config/database.php';

function columnExists(PDO $pdo, string $table, string $column): bool {
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE :col");
        $stmt->bindValue(':col', $column);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Parse args
[$script, $email, $newPassword] = array_pad($argv, 3, null);
if (!$email) {
    fwrite(STDERR, "Errore: specifica l'email.\nEsempio: php scripts/reset_user_password.php participant@example.com password123\n");
    exit(1);
}
$newPassword = $newPassword ?: 'password123';

$db = new Database();
$pdo = $db->getConnection();

if (!$pdo) {
    fwrite(STDERR, "Errore connessione database.\n");
    exit(2);
}

// Detect schema variant
$idCol = columnExists($pdo, 'users', 'user_id') ? 'user_id' : 'id';

// Ensure user exists
$check = $pdo->prepare("SELECT `{$idCol}` AS id, email FROM users WHERE email = :email");
$check->bindValue(':email', $email);
$check->execute();
$user = $check->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    fwrite(STDERR, "Utente non trovato: {$email}\n");
    exit(3);
}

$hash = password_hash($newPassword, PASSWORD_DEFAULT);
$upd = $pdo->prepare("UPDATE users SET password = :pwd WHERE email = :email");
$upd->bindValue(':pwd', $hash);
$upd->bindValue(':email', $email);
$ok = $upd->execute();

if ($ok) {
    echo "✅ Password aggiornata per {$email}.\n";
    echo "ℹ️  Nuova password: {$newPassword}\n";
} else {
    echo "❌ Errore aggiornamento password per {$email}.\n";
}
?>
