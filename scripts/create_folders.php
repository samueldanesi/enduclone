<?php
/**
 * Crea automaticamente la struttura delle cartelle uploads
 */

echo "<h1>Creazione Cartelle Upload</h1>";

$uploadDirs = [
    'uploads',
    'uploads/certificates',
    'uploads/events',
    'uploads/receipts', 
    'uploads/cards',
    'uploads/collective_registrations',
    'uploads/community',
    'uploads/gpx',
    'uploads/products'
];

foreach ($uploadDirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "<p style='color: green;'>✓ Creata cartella: {$dir}</p>";
        } else {
            echo "<p style='color: red;'>✗ Impossibile creare: {$dir}</p>";
        }
    } else {
        echo "<p style='color: blue;'>→ Già esistente: {$dir}</p>";
    }
    
    // Verifica permessi
    if (is_dir($fullPath)) {
        chmod($fullPath, 0755);
        $writable = is_writable($fullPath);
        $status = $writable ? '✓ Scrivibile' : '✗ NON scrivibile';
        $color = $writable ? 'green' : 'red';
        echo "<p style='color: {$color}; margin-left: 20px;'>{$status}</p>";
    }
}

// Crea file .htaccess per sicurezza uploads
$htaccessContent = "Options -Indexes\n";
$htaccessContent .= "# Prevent direct access to uploads\n";
$htaccessContent .= "<Files \"*.php\">\n";
$htaccessContent .= "    Order allow,deny\n";
$htaccessContent .= "    Deny from all\n";
$htaccessContent .= "</Files>\n";

file_put_contents(__DIR__ . '/uploads/.htaccess', $htaccessContent);
echo "<p style='color: green;'>✓ File .htaccess di sicurezza creato in uploads/</p>";

echo "<h2>Struttura cartelle completata!</h2>";
echo "<p><a href='/test_config.php'>Esegui Test Configurazione</a></p>";
?>