<?php
/*
 * /admin/seed_admin.php
 * Skrip untuk membuat admin pertama kali.
 * JALANKAN SEKALI LALU HAPUS FILE INI.
 */

// 1. Load koneksi dan helper
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// 2. Data Admin Default
$admin_email = 'admin@example.com';
$admin_pass  = 'admin123'; // Ganti dengan password yang kuat jika perlu

// 3. Cek apakah admin sudah ada
try {
    $stmt_check = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt_check->execute([$admin_email]);
    
    if ($stmt_check->fetch()) {
        echo "<h1>Admin Seeder</h1>";
        echo "<p style='color:blue;'>Admin dengan email '{$admin_email}' sudah ada di database.</p>";
        echo "<p>Silakan hapus file ini (<code>/admin/seed_admin.php</code>) dari server Anda.</p>";
        exit;
    }

    // 4. Buat admin baru jika belum ada
    $pass_data = hash_password_with_salt($admin_pass);
    
    $stmt_insert = $pdo->prepare("
        INSERT INTO admins (email, password_hash, salt) 
        VALUES (?, ?, ?)
    ");
    
    $stmt_insert->execute([
        $admin_email,
        $pass_data['hash'],
        $pass_data['salt']
    ]);

    echo "<h1>Admin Seeder Berhasil</h1>";
    echo "<p style='color:green;'>Admin berhasil dibuat:</p>";
    echo "<ul>";
    echo "<li>Email: <b>{$admin_email}</b></li>";
    echo "<li>Password: <b>{$admin_pass}</b></li>";
    echo "</ul>";
    echo "<p style='color:red; font-weight:bold;'>PENTING: Harap segera hapus file ini (<code>/admin/seed_admin.php</code>) dari server Anda demi keamanan!</p>";

} catch (PDOException $e) {
    echo "<h1>Error Seeder Admin</h1>";
    echo "<p style='color:red;'>Gagal menjalankan seeder: " . $e->getMessage() . "</p>";
}