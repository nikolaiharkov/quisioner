<?php
// FILE: views/section.php
/*
 * /views/section.php
 * Halaman Pengisian Kuesioner.
 * REVISI 3: Memecah S3 menjadi Part 1 (EXPL) dan Part 2 (EXPR)
 * - Logika judul dinamis
 * - Logika query SQL dinamis
 * - Logika progress bar dinamis
 */

// 1. Ambil data state dari session
$current_section_id = (int)($_SESSION['current_section_id'] ?? 0);
$current_part = $_SESSION['current_section_part'] ?? null;
$role = $_SESSION['role'] ?? null;
$session_id = $_SESSION['session_id'] ?? null;

if ($current_section_id === 0 || !$role || !$session_id) {
    redirect('index.php');
}

// 2. Ambil data Section saat ini
$stmt_sect = $pdo->prepare("SELECT * FROM sections WHERE id = ?");
$stmt_sect->execute([$current_section_id]);
$section = $stmt_sect->fetch();

if (!$section) {
    throw new Exception("Data kuesioner (section {$current_section_id}) tidak ditemukan.");
}

// --- AWAL REVISI: Logika Judul & Filter SQL Dinamis ---
$display_section_id = esc_html($section['id']);
$display_section_name = esc_html($section['name']);
$sql_part_filter = ""; // Filter SQL tambahan

if ($current_section_id == 1) { // Judul S1
    if ($role === 'teknisi') {
        $display_section_name = 'Technical AI Competency (TAC)';
    } elseif ($role === 'manajer') {
        $display_section_name = 'Managerial AI Competency (MAC)';
    }
} elseif ($current_section_id == 3) { // Judul dan Filter S3
    if ($current_part == 'part_1') {
        $display_section_name = 'Exploitative Service Innovation (EXPL)';
        $sql_part_filter = " AND code LIKE 'EXPL%' ";
    } elseif ($current_part == 'part_2') {
        $display_section_name = 'Exploratory Service Innovation (EXPR)';
        $sql_part_filter = " AND code LIKE 'EXPR%' ";
    } else {
        // Sesuatu salah, S3 harus punya 'part'
        throw new Exception("Kesalahan alur: Bagian 3 tidak memiliki sub-bagian.");
    }
}
// --- AKHIR REVISI ---


// 3. Tentukan filter role untuk query pertanyaan
$role_filter_sql = "";
if ($role === 'teknisi') {
    $role_filter_sql = " AND target_role IN ('semua', 'teknisi') ";
} elseif ($role === 'manajer') {
    $role_filter_sql = " AND target_role IN ('semua', 'manajer') ";
} elseif ($role === 'eksternal') {
    $role_filter_sql = " AND target_role = 'eksternal' ";
}

// 4. Ambil Pertanyaan untuk section dan role ini
$stmt_q = $pdo->prepare("
    SELECT * FROM questions 
    WHERE section_id = ? 
      AND is_active = 1
      {$role_filter_sql}
      {$sql_part_filter}
    ORDER BY sort_order, code, id
");
$stmt_q->execute([$current_section_id]);
$questions = $stmt_q->fetchAll();

// 4.1 Ambil jawaban sebelumnya (jika user klik 'Kembali')
$stmt_ans = $pdo->prepare("SELECT question_id, value FROM answers WHERE session_id = ?");
$stmt_ans->execute([$session_id]);
$previous_answers = $stmt_ans->fetchAll(PDO::FETCH_KEY_PAIR);


if (empty($questions) && $current_section_id != 4) {
    // S4 boleh kosong (opsional), section lain tidak.
    throw new Exception("Tidak ada pertanyaan aktif yang ditemukan untuk S{$current_section_id} ({$current_part}) dengan peran '{$role}'.");
}

// 5. Data untuk Skala Likert (Sesuai Master Prompt)
$likert_scale = [
    1 => "Sangat Tidak Setuju",
    2 => "Tidak Setuju",
    3 => "Agak Tidak Setuju",
    4 => "Netral",
    5 => "Agak Setuju",
    6 => "Setuju",
    7 => "Sangat Setuju"
];

// 6. Pengaturan Progress Bar (REVISI: Total 9)
$total_steps = 9; 

// Logika dinamis untuk nomor langkah saat ini
$current_step_number = 7; // Default (S1, S5)
if ($role != 'eksternal') {
    if ($current_section_id == 2) { $current_step_number = 8; }
    if ($current_section_id == 3 && $current_part == 'part_1') { $current_step_number = 9; }
    if ($current_section_id == 3 && $current_part == 'part_2') { $current_step_number = 9; }
    if ($current_section_id == 4) { $current_step_number = 9; }
}
?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        
        <div class="progress mb-3" role="progressbar" aria-label="Progres Kuesioner" aria-valuenow="<?php echo $current_step_number; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total_steps; ?>">
            <div class="progress-bar" style="width: <?php echo ($current_step_number / $total_steps) * 100; ?>%">
                Langkah <?php echo $current_step_number; ?> dari <?php echo $total_steps; ?>
            </div>
        </div>

        <div class="card shadow-sm wizard-card">
            <div class="card-body p-4 p-md-5">
                
                <h3 class="card-title mb-2">Bagian <?php echo $display_section_id; ?>: <?php echo $display_section_name; ?></h3>
                <p class="text-muted mb-4">Mohon berikan penilaian Anda untuk setiap pernyataan berikut.</p>
                
                <form action="index.php" method="POST" id="section-form">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="question-list">
                        <?php if (empty($questions) && $current_section_id == 4): ?>
                            <div class="alert alert-info">
                                Bagian ini opsional dan saat ini tidak memiliki pertanyaan. Silakan klik "Berikutnya" untuk melanjutkan.
                            </div>
                        <?php endif; ?>

                        <?php foreach ($questions as $q): ?>
                            <?php 
                            $group_name = 'q_group_' . $q['id']; 
                            $input_name = 'q[' . $q['id'] . ']';
                            $prev_value = $previous_answers[$q['id']] ?? null;
                            ?>
                            <div class="likert-scale" data-group-name="<?php echo $group_name; ?>" role="radiogroup" aria-labelledby="label-<?php echo $q['id']; ?>">
                                <div class="likert-text" id="label-<?php echo $q['id']; ?>">
                                    <?php if ($q['code']): ?>
                                        <span class="badge text-bg-light border me-2"><?php echo esc_html($q['code']); ?></span>
                                    <?php endif; ?>
                                    <?php echo esc_html($q['text']); ?>
                                </div>
                                <div class="likert-radio-group">
                                    <?php foreach ($likert_scale as $value => $label): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="<?php echo $input_name; ?>" 
                                                   id="q-<?php echo $q['id']; ?>-<?php echo $value; ?>" 
                                                   value="<?php echo $value; ?>" 
                                                   required
                                                   <?php echo ($prev_value == $value) ? 'checked' : ''; ?>
                                                   >
                                            <label class="form-check-label" for="q-<?php echo $q['id']; ?>-<?php echo $value; ?>">
                                                <span class="badge text-bg-secondary me-2"><?php echo $value; ?></span>
                                                <?php echo esc_html($label); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <button type="submit" name="action" value="go_back" class="btn btn-outline-secondary btn-lg" formnovalidate>
                            Kembali
                        </button>
                        <button type="submit" name="action" value="submit_section" id="next-btn" class="btn btn-primary btn-lg" disabled>
                            Berikutnya
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Panggil auto-scroll
        PSV_scrollTop();

        // 1. Cek saat halaman dimuat
        PSV_validateStep();

        // 2. Cek setiap kali radio button diganti
        $('#section-form').on('change', 'input[type="radio"]', function() {
            PSV_validateStep();
        });

        // REVISI: Jika S4 kosong, tombol Next harus aktif
        <?php if (empty($questions) && $current_section_id == 4): ?>
            $('#next-btn').prop('disabled', false);
        <?php endif; ?>
    });
</script>