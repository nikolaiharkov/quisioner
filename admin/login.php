<?php
/*
 * /admin/login.php
 * (Fase 4, File 18)
 * Halaman login untuk panel admin.
 */

// 1. Load file konfigurasi, database, dan fungsi
// Kita perlu path relatif untuk kembali ke root
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php'; // Muat helper auth

// 2. Jika admin sudah login, redirect ke dashboard
redirect_if_admin_logged_in();

$error_message = '';
$email_input = '';

// 3. Proses data POST (jika form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 4. Validasi CSRF
        if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
            throw new Exception("Sesi tidak valid. Silakan coba lagi.");
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $email_input = $email; // Simpan email untuk diisi kembali di form

        if (empty($email) || empty($password)) {
            throw new Exception("Email dan password wajib diisi.");
        }

        // 5. Cari admin di database
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        // 6. Verifikasi password
        // Gunakan fungsi verify_password (dari functions.php)
        if ($admin && verify_password($password, $admin['password_hash'], $admin['salt'])) {
            
            // 7. Login Berhasil: Regenerasi session ID
            session_regenerate_id(true); // Mencegah session fixation
            
            // Simpan data admin ke session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            
            // 8. Redirect ke dashboard admin
            redirect('admin/index.php');

        } else {
            // Login Gagal
            throw new Exception("Email atau password salah.");
        }

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Set judul halaman
$page_title = 'Admin Login';

// Muat header (path relatif)
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        
        <div class="card shadow-sm wizard-card">
            <div class="card-body p-4 p-md-5">
                
                <h3 class="card-title text-center mb-4">Admin Login</h3>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo esc_html($error_message); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email"
                               value="<?php echo esc_html($email_input); ?>"
                               required 
                               autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password"
                               required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Login
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<?php
// Muat footer (path relatif)
require_once __DIR__ . '/../includes/footer.php';
?>