<?php
/**
 * Database Configuration
 * 
 * Konfigurasi koneksi database untuk aplikasi ChatCare
 */

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'database' => getenv('DB_DATABASE') ?: 'chatcare_db',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
