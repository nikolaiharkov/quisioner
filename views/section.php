<?php
/*
 * /views/section.php
 * Halaman Pengisian Kuesioner (Langkah 6).
 * Dinamis memuat pertanyaan berdasarkan section dan role.
 * Variabel $pdo, $page_title, $respondent_id, $role, $session_id tersedia dari index.php
 */

// 1. Ambil data state dari session
$current_section_id = (int)($_SESSION['current_section_id'] ?? 0);
// $role sudah tersedia
// $session_id sudah tersedia

if ($current_section_id === 0 || !$role || !$session_id) {
    // Sesuatu salah, kembalikan ke awal
    redirect('index.php');
}

// 2. Ambil data Section saat ini
$stmt_sect = $pdo->prepare("SELECT * FROM sections WHERE id = ?");
$stmt_sect->execute([$current_section_id]);
$section = $stmt_sect->fetch();

if (!$section) {
    // Section tidak ditemukan
    throw new Exception("Data kuesioner (section {$current_section_id}) tidak ditemukan.");
}

// 3. Tentukan filter role untuk query pertanyaan
$role_filter_sql = "";
if ($role === 'teknisi') {
    $role_filter_sql = " AND target_role IN ('semua', 'teknisi') ";
} elseif ($role === 'manajer') {
    $role_filter_sql = " AND target_role IN ('semua', 'manajer') ";
} elseif ($role === 'eksternal') {
    // Eksternal HANYA melihat yang targetnya 'eksternal' (S5)
    $role_filter_sql = " AND target_role = 'eksternal' ";
}

// 4. Ambil Pertanyaan untuk section dan role ini
$stmt_q = $pdo->prepare("
    SELECT * FROM questions 
    WHERE section_id = ? 
      AND is_active = 1
      {$role_filter_sql}
    ORDER BY sort_order, code, id
");
$stmt_q->execute([$current_section_id]);
$questions = $stmt_q->fetchAll();

if (empty($questions)) {
    // Jika tidak ada pertanyaan (misal: S4 opsional tidak diisi),
    // kita bisa redirect ke 'done' atau 'next section'
    // Untuk kesederhanaan, kita anggap ini sebagai error,
    // kecuali jika admin memang sengaja menonaktifkan S4.
    
    // Solusi sederhana: jika S4 kosong, anggap selesai.
    if ($current_section_id == 4) {
        // Tandai Selesai
        $_SESSION['wizard_step'] = 'done';
        $stmt_done = $pdo->prepare("UPDATE response_sessions SET status = 'completed', completed_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt_done->execute([$session_id]);
        redirect('index.php');
    }
    
    throw new Exception("Tidak ada pertanyaan aktif yang ditemukan untuk S{$current_section_id} dengan peran '{$role}'.");
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

// 6. Pengaturan Progress Bar
$total_steps = 7; 
$current_step_number = 6;
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
                
                <h3 class="card-title mb-2">Bagian <?php echo esc_html($section['id']); ?>: <?php echo esc_html($section['name']); ?></h3>
                <p class="text-muted mb-4">Mohon berikan penilaian Anda untuk setiap pernyataan berikut.</p>
                
                <form action="index.php" method="POST" id="section-form">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="question-list">
                        <?php foreach ($questions as $q): ?>
                            <?php 
                            // Ini adalah ID unik untuk grup radio
                            $group_name = 'q_group_' . $q['id']; 
                            // Ini adalah nama input untuk POST data (array)
                            $input_name = 'q[' . $q['id'] . ']'; 
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
                                                   required>
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

                    <div class="d-flex justify-content-end">
                        <button type"submit" name="action" value="submit_section" id="next-btn" class="btn btn-primary btn-lg" disabled>
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

        // Panggil validasi (dari functions.php)
        // 1. Cek saat halaman dimuat
        PSV_validateStep();

        // 2. Cek setiap kali radio button diganti
        $('#section-form').on('change', 'input[type="radio"]', function() {
            PSV_validateStep();
        });
    });
</script>