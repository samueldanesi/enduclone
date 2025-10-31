<?php
/**
 * File di configurazione per Render
 */

return [
    'app' => [
        'name' => 'SportEvents',
        'url' => $_ENV['RENDER_EXTERNAL_URL'] ?? 'https://your-app.onrender.com',
        'timezone' => 'Europe/Rome'
    ],
    
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'database' => $_ENV['DB_NAME'] ?? 'eventi_sportivi_db',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASS'] ?? '',
        'charset' => 'utf8mb4'
    ],
    
    'session' => [
        'name' => 'sportevents_session',
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '',
        'secure' => true, // HTTPS in produzione
        'httponly' => true,
        'samesite' => 'Lax'
    ],
    
    'uploads' => [
        'max_size' => 5242880,
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'gpx'],
        'path' => __DIR__ . '/../uploads/'
    ],
    
    'security' => [
        'password_min_length' => 8,
        'session_regenerate_id' => true,
        'csrf_protection' => true
    ]
];