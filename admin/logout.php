<?php
/*
 * /admin/logout.php
 * (Fase 4, File 19)
 * Menghancurkan session admin dan redirect ke halaman login.
 */

// 1. Load file konfigurasi untuk inisialisasi session
require_once __DIR__ . '/../config.php';
// Load helper
require_once __DIR__ . '/../includes/functions.php';

// 2. Hapus data admin dari session
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);

// 3. (Opsional tapi disarankan) Hancurkan session jika hanya admin yang login
// Jika Anda ingin session kuesioner (responden) tetap ada,
// jangan panggil session_destroy(). Cukup unset() di atas.
// Tapi karena admin dan responden adalah alur terpisah,
// menghancurkan session admin itu aman.
session_destroy();

// 4. Redirect ke halaman login
redirect('admin/login.php');