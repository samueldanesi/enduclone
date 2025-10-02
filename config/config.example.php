<?php
/**
 * File di configurazione di esempio per SportEvents
 * Copia questo file come config.php e modifica i valori
 */

return [
    'app' => [
        'name' => 'SportEvents',
        'url' => 'http://localhost:8001',
        'timezone' => 'Europe/Rome'
    ],
    
    'database' => [
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'eventi_sportivi_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    
    'session' => [
        'name' => 'sportevents_session',
        'lifetime' => 86400, // 24 ore
        'path' => '/',
        'domain' => '',
        'secure' => false, // Cambia a true in produzione con HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ],
    
    'uploads' => [
        'max_size' => 5242880, // 5MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'gpx'],
        'path' => __DIR__ . '/../uploads/'
    ],
    
    'mail' => [
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 587,
        'smtp_username' => 'your-email@example.com',
        'smtp_password' => 'your-password',
        'from_email' => 'noreply@sportevents.com',
        'from_name' => 'SportEvents'
    ],
    
    'security' => [
        'password_min_length' => 8,
        'session_regenerate_id' => true,
        'csrf_protection' => true
    ]
];