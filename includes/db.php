<?php
/*
 * /includes/db.php
 * Mengelola koneksi PDO ke database.
 * File ini harus di-include setelah config.php
 */

// Opsi koneksi PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Melempar exception jika error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Hasil default sebagai array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                // Gunakan prepared statements asli
];

// Data Source Name (DSN)
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    // Buat instance PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // Tangani error koneksi
    // Di aplikasi nyata, ini harus dicatat (log) dan menampilkan halaman error
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Variabel $pdo sekarang tersedia untuk file yang meng-include db.php