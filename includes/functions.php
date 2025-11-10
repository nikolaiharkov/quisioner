<?php
/*
 * /includes/functions.php
 * Kumpulan fungsi helper (keamanan, navigasi, dll)
 * Harap include config.php sebelum file ini.
 *
 * PERBAIKAN: Menghapus fungsi is_admin_logged_in() dan require_admin_login()
 * karena sudah didefinisikan di auth.php
 */

/**
 * Meng-generate dan menyimpan CSRF token di session.
 * @return string Token CSRF
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Memvalidasi CSRF token dari POST/GET request.
 * @param string $token Token yang diterima dari form
 * @return bool True jika valid, false jika tidak.
 */
function validate_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Helper untuk menampilkan input CSRF token di form.
 */
function csrf_input() {
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generate_csrf_token()) . '">';
}

/**
 * Melakukan redirect ke halaman lain.
 * @param string $url URL tujuan (relatif dari BASE_URL)
 */
function redirect($url) {
    header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
    exit;
}

/**
 * Membersihkan output untuk mencegah XSS.
 * @param string|null $data Data yang akan dibersihkan
 * @return string Data yang aman
 */
function esc_html($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}


/* --- Fungsi Keamanan (Hashing) --- */

/**
 * Membuat hash password dengan salt yang aman.
 * @param string $password Password mentah
 * @return array ['hash' => string, 'salt' => string]
 */
function hash_password_with_salt($password) {
    $salt = bin2hex(random_bytes(16)); // 32 char salt
    $hash = hash('sha256', $password . $salt);
    return [
        'hash' => $hash,
        'salt' => $salt,
    ];
}

/**
 * Memverifikasi password mentah dengan hash dan salt dari database.
 * @param string $password Password mentah dari input login
 * @param string $hash Hash dari database
 * @param string $salt Salt dari database
 * @return bool True jika cocok
 */
function verify_password($password, $hash, $salt) {
    $check_hash = hash('sha256', $password . $salt);
    return hash_equals($hash, $check_hash);
}

// **FUNGSI YANG KONFLIK TELAH DIHAPUS DARI SINI**
// (is_admin_logged_in() dan require_admin_login()
// sekarang hanya ada di auth.php)