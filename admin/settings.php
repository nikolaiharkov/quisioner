<?php
/*
 * /admin/settings.php
 * Halaman baru untuk pengaturan admin:
 * 1. Ganti Password
 * 2. Reset Data Kuesioner
 */

// 1. Load file konfigurasi, database, dan fungsi
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// 2. Wajibkan admin untuk login
require_admin_login();

// 3. Inisialisasi variabel pesan
$error_message = '';
$success_message = '';
$admin_id = $_SESSION['admin_id'];

// 4. Logika Penanganan POST (Ganti Password atau Reset Data)
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Validasi CSRF
        if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
            throw new Exception("Sesi tidak valid. Silakan coba lagi.");
        }

        $action = $_POST['action'] ?? '';

        // --- Fitur 1: Ganti Password ---
        if ($action === 'change_password') {
            $current_pass = $_POST['current_password'] ?? '';
            $new_pass = $_POST['new_password'] ?? '';
            $retype_pass = $_POST['retype_password'] ?? '';

            if (empty($current_pass) || empty($new_pass) || empty($retype_pass)) {
                throw new Exception("Semua field password wajib diisi.");
            }
            if ($new_pass !== $retype_pass) {
                throw new Exception("Password baru tidak cocok (re-type salah).");
            }

            // Ambil data admin saat ini
            $stmt_admin = $pdo->prepare("SELECT password_hash, salt FROM admins WHERE id = ?");
            $stmt_admin->execute([$admin_id]);
            $admin = $stmt_admin->fetch();

            if (!$admin) {
                throw new Exception("Admin tidak ditemukan.");
            }

            // Verifikasi password saat ini
            if (verify_password($current_pass, $admin['password_hash'], $admin['salt'])) {
                // Buat hash & salt BARU
                $new_pass_data = hash_password_with_salt($new_pass);
                
                $stmt_update = $pdo->prepare("
                    UPDATE admins SET password_hash = ?, salt = ? WHERE id = ?
                ");
                $stmt_update->execute([
                    $new_pass_data['hash'],
                    $new_pass_data['salt'],
                    $admin_id
                ]);

                $success_message = "Password Anda telah berhasil diperbarui.";

            } else {
                throw new Exception("Password saat ini yang Anda masukkan salah.");
            }
        
        // --- Fitur 2: Reset Data ---
        } elseif ($action === 'reset_data') {
            
            $reset_options = $_POST['reset_data'] ?? [];
            $confirm_reset = $_POST['confirm_reset'] ?? '';

            if ($confirm_reset !== 'YA') {
                throw new Exception("Anda harus mengetik 'YA' di kotak konfirmasi untuk melanjutkan reset.");
            }
            if (empty($reset_options)) {
                throw new Exception("Anda harus memilih setidaknya satu opsi data untuk di-reset.");
            }

            $pdo->beginTransaction();
            $queries_run = 0;

            foreach ($reset_options as $option) {
                if ($option === 'respondents') {
                    // Ini akan cascade ke response_sessions dan answers
                    $pdo->exec("DELETE FROM respondents;");
                    $queries_run++;
                }
                if ($option === 'questions') {
                    // Ini akan cascade ke answers
                    $pdo->exec("DELETE FROM questions;");
                    $queries_run++;
                }
            }

            $pdo->commit();
            $success_message = "Data yang dipilih ($queries_run tabel utama) telah berhasil di-reset.";
        }
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
// --- Akhir Logika POST ---


// Set judul halaman
$page_title = 'Admin Settings';
require_once __DIR__ . '/../includes/header.php'; 
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 border-bottom">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Admin Panel</span>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard & Responden</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="questions.php">Kelola Pertanyaan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="settings.php">Settings</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                Login sebagai: <?php echo esc_html($_SESSION['admin_email']); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<?php if ($error_message): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Error:</strong> <?php echo esc_html($error_message); ?>
    </div>
<?php endif; ?>
<?php if ($success_message): ?>
    <div class="alert alert-success" role="alert">
        <strong>Sukses:</strong> <?php echo esc_html($success_message); ?>
    </div>
<?php endif; ?>

<div class="row g-5">

    <div class="col-md-6">
        <h2>Ganti Password Admin</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="settings.php" method="POST">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="retype_password" class="form-label">Ulangi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="retype_password" id="retype_password" class="form-control" required>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <h2>Reset Data Kuesioner</h2>
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                Zona Berbahaya
            </div>
            <div class="card-body">
                <p>Tindakan ini akan menghapus data secara permanen dan <strong>tidak dapat diurungkan</strong>. Data Admin tidak akan terhapus.</p>
                
                <form action="settings.php" method="POST" id="reset-form">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="action" value="reset_data">

                    <p class="fw-bold">Pilih data yang ingin di-reset:</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="reset_data[]" value="respondents" id="reset-respondents">
                        <label class="form-check-label" for="reset-respondents">
                            Hapus <strong>SEMUA Responden, Sesi, dan Jawaban</strong>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="reset_data[]" value="questions" id="reset-questions">
                        <label class="form-check-label" for="reset-questions">
                            Hapus <strong>SEMUA Pertanyaan Kuesioner</strong>
                        </label>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="confirm_reset" class="form-label text-danger">Untuk konfirmasi, ketik 'YA' (huruf besar):</label>
                        <input type="text" name="confirm_reset" id="confirm_reset" class="form-control" required autocomplete="off">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger" id="reset-btn">Reset Data Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div> <?php
require_once __DIR__ . '/../includes/footer.php'; 
?>