<?php
/*
 * /admin/questions.php
 * (Fase 4, File 21 - Selesai)
 * CRUD Gabungan untuk mengelola Pertanyaan Kuesioner.
 * Menggunakan parameter ?action= (list, add, edit, delete)
 *
 * PERBAIKAN: Menggunakan sintaks switch() { ... } standar
 * untuk menghindari error parsing.
 */

// 1. Load file konfigurasi, database, dan fungsi
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// 2. Wajibkan admin untuk login
require_admin_login();

// 3. Ambil data master (Sections) untuk form
$sections = $pdo->query("SELECT * FROM sections ORDER BY sort_order")->fetchAll();

// 4. Inisialisasi variabel
$action = $_GET['action'] ?? 'list'; // Aksi tampilan (list, add, edit, delete)
$error_message = '';
$success_message = '';

// 5. Logika Penanganan POST (Simpan, Update, Hapus)
// Ini dieksekusi *sebelum* header HTML dimuat
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Validasi CSRF
        if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
            throw new Exception("Sesi tidak valid. Silakan coba lagi.");
        }

        $post_action = $_POST['action'] ?? '';

        // Gunakan switch() { ... } standar
        switch ($post_action) {
            
            // --- SIMPAN PERTANYAAN BARU ---
            case 'save_add':
                $section_id = (int)($_POST['section_id'] ?? 0);
                $code = trim($_POST['code'] ?? '');
                $text = trim($_POST['text'] ?? '');
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                $target_role = $_POST['target_role'] ?? 'semua';
                $is_active = (int)($_POST['is_active'] ?? 1);

                if (empty($text) || $section_id === 0) {
                    throw new Exception("Section dan Teks Pertanyaan wajib diisi.");
                }

                $stmt = $pdo->prepare("
                    INSERT INTO questions (section_id, code, text, sort_order, target_role, is_active)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$section_id, $code, $text, $sort_order, $target_role, $is_active]);
                
                redirect('admin/questions.php?status=added');
                break; // <-- break untuk switch PHP

            // --- UPDATE PERTANYAAN EKSISTING ---
            case 'save_edit':
                $question_id = (int)($_POST['question_id'] ?? 0);
                $section_id = (int)($_POST['section_id'] ?? 0);
                $code = trim($_POST['code'] ?? '');
                $text = trim($_POST['text'] ?? '');
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                $target_role = $_POST['target_role'] ?? 'semua';
                $is_active = (int)($_POST['is_active'] ?? 1);

                if (empty($text) || $section_id === 0 || $question_id === 0) {
                    throw new Exception("ID, Section, dan Teks Pertanyaan wajib diisi.");
                }

                $stmt = $pdo->prepare("
                    UPDATE questions SET 
                        section_id = ?, code = ?, text = ?, 
                        sort_order = ?, target_role = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([$section_id, $code, $text, $sort_order, $target_role, $is_active, $question_id]);
                
                redirect('admin/questions.php?status=updated');
                break; // <-- break untuk switch PHP

            // --- KONFIRMASI HAPUS PERTANYAAN ---
            case 'confirm_delete':
                $question_id = (int)($_POST['question_id'] ?? 0);
                if ($question_id === 0) {
                    throw new Exception("ID Pertanyaan tidak valid.");
                }

                $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
                $stmt->execute([$question_id]);
                
                redirect('admin/questions.php?status=deleted');
                break; // <-- break untuk switch PHP
        }
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
// --- Akhir Logika POST ---


// 6. Logika Tampilan (GET Request)

// Set judul halaman
$page_title = 'Kelola Pertanyaan';
require_once __DIR__ . '/../includes/header.php';

// Data untuk form 'edit' atau 'delete'
$q_data = null;
if (($action === 'edit' || $action === 'delete') && isset($_GET['id'])) {
    $q_id = (int)$_GET['id'];
    $stmt_q = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt_q->execute([$q_id]);
    $q_data = $stmt_q->fetch();
    
    if (!$q_data) {
        // Jika ID tidak ditemukan, kembali ke list
        redirect('admin/questions.php?status=notfound');
    }
}
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
                    <a class="nav-link active" href="questions.php">Kelola Pertanyaan</a>
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

<?php 
// MULAI PERBAIKAN: Menggunakan sintaks switch { ... }
switch ($action) {

// ----------------------------------------------
// --- VIEW: ADD (Tambah) / EDIT (Ubah) ---
// ----------------------------------------------
case 'add':
case 'edit':
    
    $is_edit_mode = ($action === 'edit' && $q_data);
    $form_action_value = $is_edit_mode ? 'save_edit' : 'save_add';
?>
    <h2><?php echo $is_edit_mode ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru'; ?></h2>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="questions.php" method="POST">
                <?php csrf_input(); ?>
                <input type="hidden" name="action" value="<?php echo $form_action_value; ?>">
                
                <?php if ($is_edit_mode): ?>
                    <input type="hidden" name="question_id" value="<?php echo (int)$q_data['id']; ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" id="section_id" class="form-select" required>
                            <option value="" disabled <?php echo !$is_edit_mode ? 'selected' : ''; ?>>-- Pilih Section --</option>
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?php echo (int)$sec['id']; ?>" 
                                    <?php echo ($is_edit_mode && $q_data['section_id'] == $sec['id']) ? 'selected' : ''; ?>>
                                    (<?php echo esc_html($sec['code']); ?>) <?php echo esc_html($sec['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="target_role" class="form-label">Target Peran (Role) <span class="text-danger">*</span></label>
                        <select name="target_role" id="target_role" class="form-select" required>
                            <option value="semua" <?php echo ($is_edit_mode && $q_data['target_role'] == 'semua') ? 'selected' : ''; ?>>Semua (Internal)</option>
                            <option value="teknisi" <?php echo ($is_edit_mode && $q_data['target_role'] == 'teknisi') ? 'selected' : ''; ?>>Hanya Teknisi</option>
                            <option value="manajer" <?php echo ($is_edit_mode && $q_data['target_role'] == 'manajer') ? 'selected' : ''; ?>>Hanya Manajer</option>
                            <option value="eksternal" <?php echo ($is_edit_mode && $q_data['target_role'] == 'eksternal') ? 'selected' : ''; ?>>Hanya Eksternal</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="form-label">Kode (Opsional)</label>
                        <input type="text" name="code" id="code" class="form-control" 
                               value="<?php echo $is_edit_mode ? esc_html($q_data['code']) : ''; ?>"
                               placeholder="Contoh: T1, M1, ODC1, CA1">
                    </div>

                    <div class="col-md-4">
                        <label for="sort_order" class="form-label">Urutan</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" 
                               value="<?php echo $is_edit_mode ? (int)$q_data['sort_order'] : '0'; ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="is_active" class="form-label">Status</label>
                        <select name="is_active" id="is_active" class="form-select">
                            <option value="1" <?php echo ($is_edit_mode && $q_data['is_active'] == 1) ? 'selected' : ''; ?>>Aktif</option>
                            <option value="0" <?php echo ($is_edit_mode && $q_data['is_active'] == 0) ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="text" class="form-label">Teks Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="text" id="text" class="form-control" rows="4" required><?php echo $is_edit_mode ? esc_html($q_data['text']) : ''; ?></textarea>
                    </div>

                    <div class="col-12 text-end border-top pt-3">
                        <a href="questions.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $is_edit_mode ? 'Simpan Perubahan' : 'Simpan Pertanyaan'; ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php 
    break; // <-- break untuk case 'add'/'edit'

// ----------------------------------------------
// --- VIEW: DELETE (Hapus) ---
// ----------------------------------------------
case 'delete':
?>
    <h2>Hapus Pertanyaan</h2>
    
    <?php if (!$q_data): // Pengaman jika data tidak ditemukan ?>
        <div class="alert alert-danger">Pertanyaan tidak ditemukan.</div>
        <a href="questions.php" class="btn btn-primary">Kembali ke Daftar</a>
    <?php else: ?>
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                Konfirmasi Penghapusan
            </div>
            <div class="card-body">
                <p class="fs-5">Anda yakin ingin menghapus pertanyaan berikut secara permanen?</p>
                
                <blockquote class="blockquote bg-light p-3 rounded">
                    <strong>(<?php echo esc_html($q_data['code'] ?? 'N/A'); ?>)</strong>
                    <p class="mb-0"><?php echo esc_html($q_data['text']); ?></p>
                </blockquote>
                
                <p class="text-danger">Tindakan ini tidak dapat diurungkan.</p>

                <form action="questions.php" method="POST" class="mt-4">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="action" value="confirm_delete">
                    <input type="hidden" name="question_id" value="<?php echo (int)$q_data['id']; ?>">
                    
                    <a href="questions.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-danger">Ya, Hapus Sekarang</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php 
    break; // <-- break untuk case 'delete'

// ----------------------------------------------
// --- VIEW: LIST (Daftar) ---
// ----------------------------------------------
default:
?>
    <h2>Daftar Pertanyaan Kuesioner</h2>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'added'): ?>
            <div class="alert alert-success">Pertanyaan baru berhasil ditambahkan.</div>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <div class="alert alert-success">Pertanyaan berhasil diperbarui.</div>
        <?php elseif ($_GET['status'] === 'deleted'): ?>
            <div class="alert alert-success">Pertanyaan berhasil dihapus.</div>
        <?php elseif ($_GET['status'] === 'notfound'): ?>
            <div class="alert alert-warning">Pertanyaan yang Anda cari tidak ditemukan.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="questions.php?action=add" class="btn btn-primary">
            + Tambah Pertanyaan Baru
        </a>
        
        <form action="questions.php" method="GET" class="d-flex" style="width: 300px;">
            <select name="filter_section" class="form-select me-2" onchange="this.form.submit()">
                <option value="all">-- Tampilkan Semua Section --</option>
                <?php foreach ($sections as $sec): ?>
                    <option value="<?php echo (int)$sec['id']; ?>"
                        <?php echo (isset($_GET['filter_section']) && $_GET['filter_section'] == $sec['id']) ? 'selected' : ''; ?>>
                        (<?php echo esc_html($sec['code']); ?>) <?php echo esc_html($sec['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 10%;">Section</th>
                        <th style="width: 10%;">Kode</th>
                        <th>Teks Pertanyaan</th>
                        <th style="width: 10%;">Target Role</th>
                        <th style="width: 5%;">Urutan</th>
                        <th style="width: 5%;">Status</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Logika Query Daftar
                    $sql_list = "
                        SELECT q.*, s.code as section_code 
                        FROM questions q
                        JOIN sections s ON q.section_id = s.id
                    ";
                    $params_list = [];
                    
                    $filter_sec_id = $_GET['filter_section'] ?? 'all';
                    if ($filter_sec_id !== 'all') {
                        $sql_list .= " WHERE q.section_id = ? ";
                        $params_list[] = (int)$filter_sec_id;
                    }
                    
                    $sql_list .= " ORDER BY q.section_id, q.sort_order, q.code";
                    
                    $stmt_list = $pdo->prepare($sql_list);
                    $stmt_list->execute($params_list);
                    $question_list = $stmt_list->fetchAll();
                    ?>

                    <?php if (empty($question_list)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted p-4">
                                Tidak ada pertanyaan ditemukan.
                                <?php if ($filter_sec_id !== 'all'): ?>
                                    <br><a href="questions.php">Hapus filter</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($question_list as $q): ?>
                            <tr>
                                <td><?php echo (int)$q['id']; ?></td>
                                <td><span class="badge text-bg-info"><?php echo esc_html($q['section_code']); ?></span></td>
                                <td><code><?php echo esc_html($q['code']); ?></code></td>
                                <td class="small"><?php echo esc_html($q['text']); ?></td>
                                <td><span class="badge text-bg-secondary"><?php echo esc_html($q['target_role']); ?></span></td>
                                <td><?php echo (int)$q['sort_order']; ?></td>
                                <td>
                                    <?php if ($q['is_active']): ?>
                                        <span class="badge text-bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-warning">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="questions.php?action=edit&id=<?php echo (int)$q['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="questions.php?action=delete&id=<?php echo (int)$q['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php 
    break; // <-- break untuk case 'default'

} // AKHIR PERBAIKAN: Menutup switch { ... }
?>


<?php
// Muat footer
require_once __DIR__ . '/../includes/footer.php'; 
?>