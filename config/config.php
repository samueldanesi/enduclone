<?php
/**
 * Configurazioni generali dell'applicazione
 */

// Detect environment
$isPiramedia = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'piramedia.it') !== false;
$isRender = isset($_ENV['RENDER']) || isset($_ENV['RENDER_EXTERNAL_URL']);
$isLocalhost = !$isPiramedia && !$isRender;

// Percorso root applicazione
define('APP_ROOT', dirname(__DIR__));

if ($isPiramedia) {
    define('BASE_URL', 'https://biglietteria.piramedia.it');
    define('ENVIRONMENT', 'production');
} elseif ($isRender) {
    define('BASE_URL', $_ENV['RENDER_EXTERNAL_URL'] ?? 'https://your-app.onrender.com');
    define('ENVIRONMENT', 'staging');
} else {
    // Costruisci BASE_URL dinamicamente in locale per evitare mismatch (localhost vs 127.0.0.1, porte diverse)
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8001'; // include anche la porta se presente
    define('BASE_URL', $scheme . '://' . $host);
    define('ENVIRONMENT', 'development');
}

define('SITE_NAME', 'SportEvents');
define('UPLOAD_PATH', APP_ROOT . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configurazioni email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@sportevents.com');
define('FROM_NAME', 'SportEvents');

// Configurazioni PayPal
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_CLIENT_SECRET', '');
define('PAYPAL_MODE', 'sandbox'); // sandbox o live

// Debug toggle opzionale (?debug=1)
$__debug = (isset($_GET['debug']) && $_GET['debug'] == '1');
define('DEBUG_MODE', $__debug);
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    // Tenta di svuotare l'opcache per evitare codice vecchio in cache
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
}

// Timezone
date_default_timezone_set('Europe/Rome');

// Autoload delle classi
spl_autoload_register(function ($class) {
    $paths = [
        APP_ROOT . '/app/models/',
        APP_ROOT . '/app/controllers/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});
?>
