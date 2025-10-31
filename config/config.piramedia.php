<?php
/**
 * Configurazioni per deployment su Piramedia
 */

// URL di produzione
define('BASE_URL', 'https://biglietteria.piramedia.it');

define('SITE_NAME', 'SportEvents');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB per produzione

// Configurazioni email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@biglietteria.piramedia.it');
define('FROM_NAME', 'SportEvents');

// Configurazioni PayPal (produzione)
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_CLIENT_SECRET', '');
define('PAYPAL_MODE', 'live'); // live per produzione

// Configurazioni database Piramedia
define('DB_HOST', 'localhost');
define('DB_NAME', 'biglietteria_piramedia_it');
define('DB_USER', 'biglietteria_piramedia_it');
define('DB_PASSWORD', 'NI2FjP5TMGtsCF3f');

// Sessioni
session_start();

// Timezone
date_default_timezone_set('Europe/Rome');

// Autoload delle classi
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/',
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