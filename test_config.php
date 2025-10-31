<?php
/**
 * Test di configurazione per Piramedia
 * Verifica che tutto sia configurato correttamente
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Configurazione SportEvents</h1>";
echo "<p>Data test: " . date('Y-m-d H:i:s') . "</p>";

// Test 1: Inclusione file di configurazione
echo "<h2>1. Test Configurazione</h2>";
try {
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/config/database.php';
    echo "<p style='color: green;'>✓ File di configurazione caricati</p>";
    echo "<p><strong>BASE_URL:</strong> " . (defined('BASE_URL') ? BASE_URL : 'NON DEFINITO') . "</p>";
    echo "<p><strong>ENVIRONMENT:</strong> " . (defined('ENVIRONMENT') ? ENVIRONMENT : 'NON DEFINITO') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Errore configurazione: " . $e->getMessage() . "</p>";
}

// Test 2: Connessione Database
echo "<h2>2. Test Database</h2>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if ($conn) {
        echo "<p style='color: green;'>✓ Connessione database riuscita</p>";
        
        // Test query
        $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p><strong>Utenti nel database:</strong> " . $result['count'] . "</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Impossibile connettersi al database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Errore database: " . $e->getMessage() . "</p>";
}

// Test 3: Cartelle Upload
echo "<h2>3. Test Cartelle Upload</h2>";
$uploadDirs = [
    'uploads',
    'uploads/certificates',
    'uploads/events', 
    'uploads/receipts',
    'uploads/cards',
    'uploads/collective_registrations',
    'uploads/community',
    'uploads/gpx'
];

foreach ($uploadDirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath)) {
        $writable = is_writable($fullPath);
        $color = $writable ? 'green' : 'red';
        $status = $writable ? '✓' : '✗';
        echo "<p style='color: {$color};'>{$status} {$dir} - " . ($writable ? 'Scrivibile' : 'NON scrivibile') . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠ {$dir} - Cartella non esistente</p>";
    }
}

// Test 4: Estensioni PHP
echo "<h2>4. Test Estensioni PHP</h2>";
$requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'fileinfo', 'json', 'mbstring'];

foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $color = $loaded ? 'green' : 'red';
    $status = $loaded ? '✓' : '✗';
    echo "<p style='color: {$color};'>{$status} {$ext}</p>";
}

// Test 5: Configurazioni PHP
echo "<h2>5. Test Configurazioni PHP</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Upload Max Filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Post Max Size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . "s</p>";
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";

// Test 6: File critici
echo "<h2>6. Test File Critici</h2>";
$criticalFiles = [
    'public/index.php',
    'app/controllers/AuthController.php',
    'app/controllers/EventController.php',
    'app/models/User.php',
    'app/models/Event.php',
    '.htaccess',
    'public/.htaccess'
];

foreach ($criticalFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✓' : '✗';
    echo "<p style='color: {$color};'>{$status} {$file}</p>";
}

// Test 7: Test Routing
echo "<h2>7. Test Routing</h2>";
echo "<p><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'NON DEFINITO') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NON DEFINITO') . "</p>";
echo "<p><strong>DOCUMENT_ROOT:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NON DEFINITO') . "</p>";

// Conclusioni
echo "<h2 style='color: #0066cc;'>Riepilogo Test</h2>";
echo "<p>Se tutti i test mostrano ✓ verde, l'applicazione dovrebbe funzionare correttamente.</p>";
echo "<p>Per problemi con ✗ rossi, controlla la documentazione di deployment.</p>";
echo "<p><a href='/' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Vai all'Applicazione</a></p>";
?>