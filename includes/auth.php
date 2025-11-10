<?php
/*
 * /includes/auth.php
 * (Fase 4, File 17)
 * Fungsi helper khusus untuk otentikasi Admin.
 *
 * Asumsikan config.php dan functions.php sudah di-include
 * oleh skrip yang memanggil file ini (misal: /admin/index.php atau /admin/login.php).
 */

// Pastikan session sudah dimulai (dari config.php)
if (session_status() === PHP_SESSION_NONE) {
    // Ini seharusnya tidak terjadi jika config.php di-load dengan benar
    trigger_error("Session not started before loading auth.php", E_USER_WARNING);
    session_start();
}

/**
 * Memeriksa apakah admin sudah login.
 * @return bool
 */
function is_admin_logged_in() {
    // Cek apakah 'admin_id' ada di session dan bukan 0 atau null
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Mewajibkan login admin (untuk halaman admin yang aman).
 * Jika belum login, redirect ke halaman login.
 */
function require_admin_login() {
    if (!is_admin_logged_in()) {
        // Asumsi fungsi redirect() tersedia dari functions.php
        // dan BASE_URL tersedia dari config.php
        redirect('admin/login.php');
    }
}

/**
 * Mengarahkan admin yang sudah login menjauh dari halaman login.
 * Berguna untuk ditaruh di login.php
 */
function redirect_if_admin_logged_in() {
    if (is_admin_logged_in()) {
        // Asumsi fungsi redirect() tersedia dari functions.php
        redirect('admin/index.php'); // Arahkan ke dashboard
    }
}