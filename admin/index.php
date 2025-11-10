<?php
/*
 * /admin/index.php
 * (Fase 4, File 20)
 * Halaman utama admin: Dashboard, Tabel Responden, dan Form Export CSV.
 */

// 1. Load file konfigurasi, database, dan fungsi
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// 2. Wajibkan admin untuk login
require_admin_login();

// 3. Logika Penanganan Ekspor CSV (jika form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'export_csv') {
    
    // Validasi CSRF
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Sesi tidak valid. Silakan coba lagi.");
    }

    $role = $_POST['export_role'] ?? '';
    $section_id = (int)($_POST['export_section'] ?? 0);

    if (empty($role) || $section_id === 0) {
        die("Role dan Section wajib dipilih untuk ekspor.");
    }

    // --- Mulai proses CSV ---

    // 1. Ambil info section dan pertanyaan
    $stmt_q = $pdo->prepare("
        SELECT id, code, text FROM questions 
        WHERE section_id = ? AND is_active = 1 
        ORDER BY sort_order, code
    ");
    $stmt_q->execute([$section_id]);
    $questions = $stmt_q->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        die("Tidak ada pertanyaan aktif ditemukan untuk section ini.");
    }

    // 2. Ambil responden berdasarkan role
    $stmt_r = $pdo->prepare("
        SELECT r.id, r.full_name, s.id as session_id 
        FROM respondents r
        JOIN response_sessions s ON r.id = s.respondent_id
        WHERE r.role = ? AND s.status = 'completed'
    ");
    $stmt_r->execute([$role]);
    $respondents = $stmt_r->fetchAll(PDO::FETCH_ASSOC);

    // 3. Ambil semua jawaban (lebih efisien daripada query di dalam loop)
    $session_ids = array_column($respondents, 'session_id');
    $answers = [];
    if (!empty($session_ids)) {
        $in_placeholders = implode(',', array_fill(0, count($session_ids), '?'));
        $stmt_a = $pdo->prepare("
            SELECT session_id, question_id, value FROM answers 
            WHERE session_id IN ($in_placeholders)
        ");
        $stmt_a->execute($session_ids);
        while ($row = $stmt_a->fetch(PDO::FETCH_ASSOC)) {
            $answers[$row['session_id']][$row['question_id']] = $row['value'];
        }
    }
    
    // 4. Set Header CSV
    $filename = "export_psv_${role}_s${section_id}_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // 5. Buat Header Row
    $header = ['respondent_id', 'full_name', 'session_id'];
    foreach ($questions as $q) {
        $header[] = $q['code'] ?? ('q_' . $q['id']); // Gunakan code (T1, ODC1, dll)
    }
    fputcsv($output, $header);

    // 6. Buat Data Rows
    foreach ($respondents as $resp) {
        $row = [
            $resp['id'],
            $resp['full_name'],
            $resp['session_id']
        ];
        
        $respondent_answers = $answers[$resp['session_id']] ?? [];
        
        foreach ($questions as $q) {
            $row[] = $respondent_answers[$q['id']] ?? null; // Isi 'null' jika tidak dijawab
        }
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit; // Hentikan eksekusi skrip setelah CSV dibuat
}
// --- Akhir Logika Ekspor CSV ---


// 4. Logika Pengambilan Data untuk Tampilan Dashboard (GET Request)

// A. Statistik Dashboard
$stats = [
    'total' => 0,
    'teknisi' => 0,
    'manajer' => 0,
    'eksternal' => 0
];
$stmt_stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN role = 'teknisi' THEN 1 ELSE 0 END) as teknisi,
        SUM(CASE WHEN role = 'manajer' THEN 1 ELSE 0 END) as manajer,
        SUM(CASE WHEN role = 'eksternal' THEN 1 ELSE 0 END) as eksternal
    FROM respondents
");
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);


// B. Filter Tabel Responden
$filter_role = $_GET['filter_role'] ?? 'all';
$filter_search = $_GET['filter_search'] ?? '';

$sql_resp = "
    SELECT r.id, r.full_name, r.role, r.created_at, s.status, s.completed_at 
    FROM respondents r
    LEFT JOIN response_sessions s ON r.id = s.respondent_id
";
$params = [];
$where_clauses = [];

if ($filter_role !== 'all' && in_array($filter_role, ['teknisi', 'manajer', 'eksternal'])) {
    $where_clauses[] = "r.role = ?";
    $params[] = $filter_role;
}
if (!empty($filter_search)) {
    $where_clauses[] = "r.full_name LIKE ?";
    $params[] = '%' . $filter_search . '%';
}

if (!empty($where_clauses)) {
    $sql_resp .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql_resp .= " ORDER BY r.created_at DESC";

$stmt_resp_list = $pdo->prepare($sql_resp);
$stmt_resp_list->execute($params);
$respondents_list = $stmt_resp_list->fetchAll();


// C. Data Section (untuk form ekspor)
$sections = $pdo->query("SELECT id, code, name FROM sections ORDER BY sort_order")->fetchAll();


// Set judul halaman
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php'; 
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 border-bottom">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Admin Panel</span>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Dashboard & Responden</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="questions.php">Kelola Pertanyaan</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                Login sebagai: <?php echo esc_html($_SESSION['admin_email']); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<section id="dashboard" class="mb-5">
    <h2>Ringkasan Dashboard</h2>
    <div class="row">
        <div class="col-md-3">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['total']; ?></h5>
                    <p class="card-text">Total Responden</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['teknisi']; ?></h5>
                    <p class="card-text">Teknisi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['manajer']; ?></h5>
                    <p class="card-text">Manajer</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['eksternal']; ?></h5>
                    <p class="card-text">Eksternal</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="respondents" class="mb-5">
    <h2>Data Responden</h2>

    <form action="index.php" method="GET" class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="filter_role" class="form-label">Filter Peran</label>
            <select name="filter_role" id="filter_role" class="form-select">
                <option value="all" <?php echo ($filter_role === 'all') ? 'selected' : ''; ?>>Semua Peran</option>
                <option value="teknisi" <?php echo ($filter_role === 'teknisi') ? 'selected' : ''; ?>>Teknisi</option>
                <option value="manajer" <?php echo ($filter_role === 'manajer') ? 'selected' : ''; ?>>Manajer</option>
                <option value="eksternal" <?php echo ($filter_role === 'eksternal') ? 'selected' : ''; ?>>Eksternal</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="filter_search" class="form-label">Cari Nama</label>
            <input type="search" name="filter_search" id="filter_search" class="form-control" value="<?php echo esc_html($filter_search); ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Peran (Role)</th>
                    <th>Status</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($respondents_list)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada data responden ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($respondents_list as $resp): ?>
                        <tr>
                            <td><?php echo esc_html($resp['id']); ?></td>
                            <td><?php echo esc_html($resp['full_name']); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo esc_html($resp['role']); ?></span></td>
                            <td>
                                <?php if ($resp['status'] === 'completed'): ?>
                                    <span class="badge text-bg-success">Completed</span>
                                <?php else: ?>
                                    <span class="badge text-bg-warning">In Progress</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($resp['created_at']); ?></td>
                            <td><?php echo esc_html($resp['completed_at'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section id="export" class="mb-5">
    <h2>Ekspor Data CSV</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Unduh Data Jawaban</h5>
            <p class="card-text">Pilih peran dan section untuk mengunduh data jawaban mentah dalam format CSV.</p>
            
            <form action="index.php" method="POST">
                <?php csrf_input(); ?>
                <input type="hidden" name="action" value="export_csv">
                
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="export_role" class="form-label">1. Pilih Peran</label>
                        <select name="export_role" id="export_role" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Peran --</option>
                            <option value="teknisi">Teknisi</option>
                            <option value="manajer">Manajer</option>
                            <option value="eksternal">Eksternal</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="export_section" class="form-label">2. Pilih Section</label>
                        <select name="export_section" id="export_section" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Section --</option>
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?php echo esc_html($sec['id']); ?>">
                                    (<?php echo esc_html($sec['code']); ?>) <?php echo esc_html($sec['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">Download CSV</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


<?php
require_once __DIR__ . '/../includes/footer.php'; 
?>