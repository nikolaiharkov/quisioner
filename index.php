<?php
// FILE: index.php
/*
 * /index.php
 * Controller Utama (Router) untuk Wizard Kuesioner.
 *
 * REVISI 4: Memecah Section 3 (EXPL dan EXPR) menjadi dua langkah
 * - Menambah $_SESSION['current_section_part'] ('part_1', 'part_2')
 * - Memperbarui total_steps menjadi 9
 *
 * REVISI 5 (USER): Menambahkan validasi 'consent' server-side
 *
 * REVISI 6 (USER): Menambahkan 'phone_number' pada submit_fullname dan submit_role
 */

// 1. Load file konfigurasi, database, dan fungsi
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// 2. Tentukan langkah (step) saat ini
$current_step = $_SESSION['wizard_step'] ?? 'welcome';

// Jika user datang ke 'welcome' dan masih punya session lama, hapus.
if ($current_step === 'welcome' && isset($_SESSION['respondent_id'])) {
    session_unset();
    session_destroy();
    session_start(); // Mulai session baru
    $current_step = 'welcome';
}

// 3. Tentukan ID Responden & Sesi dari Session (jika sudah ada)
$respondent_id = $_SESSION['respondent_id'] ?? null;
$session_id = $_SESSION['session_id'] ?? null;
$role = $_SESSION['role'] ?? null;


// 4. Proses data POST (jika ada form yang disubmit)
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
            throw new Exception("Sesi tidak valid. Silakan muat ulang halaman.");
        }

        $action = $_POST['action'] ?? '';

        switch ($action) {
            
            case 'start_wizard':
                $_SESSION['wizard_step'] = 'welcome_part2';
                break;

            case 'submit_welcome_part2':
                $_SESSION['wizard_step'] = 'fullname';
                break;
            
            // --- LOGIKA TOMBOL KEMBALI ---
            case 'go_back':
                $current_step_on_back = $_SESSION['wizard_step'] ?? 'welcome';
                
                switch ($current_step_on_back) {
                    case 'welcome_part2':
                        $_SESSION['wizard_step'] = 'welcome';
                        break;
                    case 'fullname':
                        $_SESSION['wizard_step'] = 'welcome_part2';
                        break;
                    case 'role':
                        $_SESSION['wizard_step'] = 'fullname';
                        break;
                    case 'demographics':
                        $_SESSION['wizard_step'] = 'role';
                        break;
                    case 'instructions':
                        $_SESSION['wizard_step'] = 'demographics';
                        break;
                    case 'section':
                        $current_section_id = (int)($_SESSION['current_section_id'] ?? 0);
                        $current_part = $_SESSION['current_section_part'] ?? null;
                        $role = $_SESSION['role'] ?? null;
                        
                        $prev_section_id = null;
                        $prev_part = null;
                        $_SESSION['wizard_step'] = 'section'; // Default

                        if ($role === 'teknisi' || $role === 'manajer') {
                            if ($current_section_id == 1) {
                                $_SESSION['wizard_step'] = 'instructions';
                            
                            } elseif ($current_section_id == 2) {
                                $prev_section_id = 1; $prev_part = null;
                            
                            } elseif ($current_section_id == 3) {
                                if ($current_part == 'part_1') { // Dari S3-P1 kembali ke S2
                                    $prev_section_id = 2; $prev_part = null;
                                } elseif ($current_part == 'part_2') { // Dari S3-P2 kembali ke S3-P1
                                    $prev_section_id = 3; $prev_part = 'part_1';
                                }
                            
                            } elseif ($current_section_id == 4) { // Dari S4 kembali ke S3-P2
                                $prev_section_id = 3; $prev_part = 'part_2';
                            }
                        
                        } elseif ($role === 'eksternal') {
                            if ($current_section_id == 5) {
                                $_SESSION['wizard_step'] = 'instructions';
                            }
                        }

                        if ($prev_section_id) {
                            $_SESSION['current_section_id'] = $prev_section_id;
                            $_SESSION['current_section_part'] = $prev_part;
                        }
                        
                        break;
                    default:
                        $_SESSION['wizard_step'] = $current_step_on_back;
                        break;
                }
                break;
            // --- AKHIR LOGIKA TOMBOL KEMBALI ---

            case 'submit_fullname':
                $full_name = trim($_POST['full_name'] ?? '');
                $phone_number = trim($_POST['phone_number'] ?? ''); // <-- BARU

                if (empty($full_name) || empty($phone_number)) { // <-- DIPERBARUI
                    throw new Exception("Nama Lengkap dan Nomor Telepon wajib diisi.");
                }
                
                $_SESSION['temp_full_name'] = $full_name;
                $_SESSION['temp_phone_number'] = $phone_number; // <-- BARU
                $_SESSION['wizard_step'] = 'role';
                break;

            case 'submit_role':
                $role = $_POST['role'] ?? '';
                if (empty($role)) {
                    throw new Exception("Peran (Role) wajib dipilih.");
                }
                
                $full_name = $_SESSION['temp_full_name'] ?? 'Responden Anonim';
                $phone_number = $_SESSION['temp_phone_number'] ?? null; // <-- BARU

                $stmt_resp = $pdo->prepare("
                    INSERT INTO respondents (full_name, phone_number, role) 
                    VALUES (?, ?, ?)
                "); // <-- DIPERBARUI
                $stmt_resp->execute([$full_name, $phone_number, $role]); // <-- DIPERBARUI
                $respondent_id = $pdo->lastInsertId();

                $stmt_sess = $pdo->prepare("INSERT INTO response_sessions (respondent_id) VALUES (?)");
                $stmt_sess->execute([$respondent_id]);
                $session_id = $pdo->lastInsertId();

                $_SESSION['respondent_id'] = $respondent_id;
                $_SESSION['session_id'] = $session_id;
                $_SESSION['role'] = $role;
                $_SESSION['full_name'] = $full_name; // Simpan nama lengkap
                unset($_SESSION['temp_full_name']);
                unset($_SESSION['temp_phone_number']); // <-- BARU

                $_SESSION['wizard_step'] = 'demographics';
                break;

            case 'submit_demographics':
                $role = $_SESSION['role']; 
                
                $_SESSION['temp_demographics'] = $_POST;

                if ($role === 'teknisi' || $role === 'manajer') {
                    
                    $gender = $_POST['gender'] ?? null;
                    $age_group = $_POST['age_group'] ?? null;
                    $pendidikan = $_POST['pendidikan'] ?? null;
                    $jabatan = $_POST['jabatan'] ?? null;
                    $lama_bekerja = $_POST['lama_bekerja'] ?? null;
                    $unit = trim($_POST['unit'] ?? '');
                    $pengalaman_ai = $_POST['pengalaman_ai'] ?? null;

                    if (empty($gender) || empty($age_group) || empty($pendidikan) || empty($jabatan) || empty($lama_bekerja) || empty($unit) || empty($pengalaman_ai)) {
                        throw new Exception("Semua data demografis (7 poin) wajib diisi.");
                    }

                    $stmt = $pdo->prepare("
                        UPDATE respondents SET 
                            gender = ?, 
                            age_group = ?, 
                            pendidikan = ?, 
                            jabatan = ?, 
                            lama_bekerja = ?, 
                            unit = ?, 
                            pengalaman_ai = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $gender, $age_group, $pendidikan, 
                        $jabatan, $lama_bekerja, $unit, $pengalaman_ai, 
                        $respondent_id
                    ]);

                } elseif ($role === 'eksternal') {
                    $kategori = $_POST['eksternal_kategori'] ?? null;
                    
                    if (empty($kategori)) {
                        throw new Exception("Kategori eksternal wajib dipilih.");
                    }

                    $stmt = $pdo->prepare("UPDATE respondents SET eksternal_kategori = ? WHERE id = ?");
                    $stmt->execute([$kategori, $respondent_id]);
                }
                
                unset($_SESSION['temp_demographics']); 
                $_SESSION['wizard_step'] = 'instructions';
                break;

            case 'start_questions':
                // === VALIDASI PERSETUJUAN (SERVER-SIDE) ===
                $consent = $_POST['consent'] ?? '';
                if ($consent !== 'yes') {
                    throw new Exception("Anda harus menyetujui persyaratan partisipasi untuk melanjutkan.");
                }
                // === AKHIR VALIDASI ===

                $role = $_SESSION['role'];
                $next_section = 1; 
                
                if ($role === 'teknisi' || $role === 'manajer') {
                    $next_section = 1; 
                } elseif ($role === 'eksternal') {
                    $next_section = 5; 
                }
                
                $_SESSION['current_section_id'] = $next_section;
                $_SESSION['current_section_part'] = null; // Mulai tanpa bagian
                $_SESSION['wizard_step'] = 'section'; 
                break;

            case 'submit_section':
                $current_section_id = (int)($_SESSION['current_section_id'] ?? 1);
                $current_part = $_SESSION['current_section_part'] ?? null;
                $answers = $_POST['q'] ?? []; // Format: q[question_id] = value

                if (empty($answers)) {
                    // Izinkan submit kosong HANYA jika S4 (opsional)
                    if ($current_section_id != 4) {
                         throw new Exception("Tidak ada jawaban yang diterima.");
                    }
                }

                $stmt_ans = $pdo->prepare("
                    INSERT INTO answers (session_id, question_id, value) 
                    VALUES (:session_id, :question_id, :value)
                    ON DUPLICATE KEY UPDATE value = VALUES(value)
                ");

                foreach ($answers as $question_id => $value) {
                    $stmt_ans->execute([
                        'session_id' => $session_id,
                        'question_id' => (int)$question_id,
                        'value' => (int)$value
                    ]);
                }
                
                $role = $_SESSION['role'];
                $next_section_id = null;
                $next_part = null;

                if ($role === 'teknisi' || $role === 'manajer') {
                    if ($current_section_id === 1) {
                        $next_section_id = 2; $next_part = null;
                    } elseif ($current_section_id === 2) {
                        $next_section_id = 3; $next_part = 'part_1'; // Mulai S3, Part 1 (EXPL)
                    } elseif ($current_section_id === 3) {
                        if ($current_part === 'part_1') {
                            $next_section_id = 3; $next_part = 'part_2'; // Lanjut ke S3, Part 2 (EXPR)
                        } else { // Selesai S3, Part 2
                            $next_section_id = 4; $next_part = null; // Lanjut ke S4
                        }
                    } elseif ($current_section_id === 4) {
                         $next_section_id = null; // Selesai setelah S4
                    }
                } 
                
                if ($current_section_id === 5) { // Eksternal
                    $next_section_id = null; // Selesai
                }
                
                if ($next_section_id) {
                    // Cek apakah section berikutnya punya pertanyaan aktif (skip S4 jika kosong)
                    
                    // Filter S4
                    if ($next_section_id == 4) {
                        $role_filter_sql = ($role === 'eksternal') ? " AND target_role = 'eksternal' " : " AND target_role IN ('semua', '{$role}') ";
                        $stmt_check = $pdo->prepare("
                            SELECT 1 FROM questions 
                            WHERE section_id = 4 AND is_active = 1 {$role_filter_sql} LIMIT 1
                        ");
                        $stmt_check->execute();
                        
                        if (!$stmt_check->fetch()) {
                            // Tidak ada pertanyaan di S4, anggap selesai
                            $next_section_id = null; 
                        }
                    }
                }
                
                if ($next_section_id) {
                    $_SESSION['current_section_id'] = $next_section_id;
                    $_SESSION['current_section_part'] = $next_part;
                    $_SESSION['wizard_step'] = 'section';
                } else {
                    $_SESSION['wizard_step'] = 'done';
                    
                    $stmt_done = $pdo->prepare("UPDATE response_sessions SET status = 'completed', completed_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt_done->execute([$session_id]);
                }
                break;
        }

        redirect('index.php');
    }

} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// 6. Tentukan file view yang akan dimuat
// REVISI: Tambahkan welcome_part2 ke view yang valid
if ($current_step === 'welcome_part2') {
    $view_file = __DIR__ . "/views/welcome_part2.php";
} else {
    // Gunakan filter untuk keamanan nama file
    $allowed_steps = ['welcome', 'fullname', 'role', 'demographics', 'instructions', 'section', 'done'];
    $safe_step = in_array($current_step, $allowed_steps) ? $current_step : 'welcome';
    $view_file = __DIR__ . "/views/{$safe_step}.php";
}


// 7. Muat view
if (file_exists($view_file)) {
    $page_title = "Kuesioner PSV - " . ucfirst($current_step);
    
    require_once __DIR__ . '/includes/header.php';
    
    if (isset($error_message)) {
        echo '<div classrow justify-content-center"><div class="col-md-10 col-lg-8">';
        echo '<div class="alert alert-danger" role="alert">' . esc_html($error_message) . '</div>';
        echo '</div></div>';
    }
    
    require_once $view_file;
    
    require_once __DIR__ . '/includes/footer.php';

} else {
    http_response_code(404);
    echo "<h1>404 - Halaman Tidak Ditemukan</h1>";
    echo "<p>View untuk langkah '{$current_step}' tidak ditemukan.</p>";
}
?>