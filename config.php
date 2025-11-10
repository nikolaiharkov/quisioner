<?php
/*
 * /config.php
 * Konfigurasi utama aplikasi, koneksi database, dan inisialisasi session.
 */

// 1. Pengaturan Database (Sesuaikan dengan environment Anda)
define('DB_HOST', '127.0.0.1');      // atau 'localhost'
define('DB_NAME', 'psv'); // Nama database Anda
define('DB_USER', 'root');             // Username database Anda
define('DB_PASS', '');                 // Password database Anda
define('DB_CHARSET', 'utf8mb4');

// 2. Pengaturan Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set ke 0 di produksi

// 3. Pengaturan Session
if (session_status() === PHP_SESSION_NONE) {
    // Konfigurasi cookie session untuk keamanan
    session_set_cookie_params([
        'lifetime' => 86400, // 1 hari
        'path' => '/',
        'domain' => '', // Sesuaikan jika perlu
        'secure' => isset($_SERVER['HTTPS']), // True jika HTTPS
        'httponly' => true, // Wajib
        'samesite' => 'Lax' // Wajib
    ]);
    session_start();
}

// 4. Base URL (Opsional, tapi membantu)
// (Pastikan tidak ada trailing slash '/')
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/quisioner');