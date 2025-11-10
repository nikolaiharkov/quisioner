<?php
/*
 * /views/done.php
 * Halaman Selesai / Terima Kasih (Langkah 7 - Terakhir).
 * Variabel $pdo, $page_title, $respondent_id, $role, $session_id tersedia dari index.php
 */

// Perkiraan total langkah
$total_steps = 7; 
$current_step_number = 7;

// Ambil ID Sesi untuk referensi
$session_id_display = $_SESSION['session_id'] ?? null;
$full_name_display = $_SESSION['full_name'] ?? 'Responden'; // Ambil nama jika disimpan, jika tidak, default

// Bersihkan session wizard untuk memulai yang baru nanti.
// Kita bisa lakukan ini di sini, atau saat user klik link 'kembali'
// Untuk sekarang, kita biarkan session-nya, dan bersihkan jika user kembali ke index.php
// (Logic untuk membersihkan session bisa ditambahkan di index.php jika $current_step == 'welcome')

// --- Logika Pembersihan Session ---
// Setelah halaman 'done' ditampilkan, data wizard tidak lagi diperlukan
// Menghapus ini memastikan jika user me-refresh atau kembali ke index.php,
// mereka akan memulai dari awal.
unset(
    $_SESSION['wizard_step'], 
    $_SESSION['respondent_id'], 
    $_SESSION['session_id'], 
    $_SESSION['role'], 
    $_SESSION['current_section_id'],
    $_SESSION['temp_full_name']
);
// ---------------------------------

?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        
        <div class="progress mb-3" role="progressbar" aria-label="Progres Kuesioner" aria-valuenow="<?php echo $current_step_number; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total_steps; ?>">
            <div class="progress-bar bg-success" style="width: 100%">
                Selesai (100%)
            </div>
        </div>

        <div class="card shadow-sm wizard-card">
            <div class="card-body p-4 p-md-5 text-center">
                
                <h1 class="text-success mb-3">Terima Kasih!</h1>
                
                <p class="fs-5">Partisipasi Anda telah berhasil direkam.</p>
                <p>Terima kasih telah meluangkan waktu untuk mengisi kuesioner ini. Masukan Anda sangat berharga untuk perbaikan layanan kami di masa mendatang.</p>
                
                <?php if ($session_id_display): ?>
                <p class="text-muted small mt-4">
                    ID Sesi Anda: <code><?php echo esc_html($session_id_display); ?></code>
                </p>
                <?php endif; ?>

                <hr class="my-4">

                <a href="index.php" class="btn btn-outline-primary">
                    Kembali ke Halaman Awal
                </a>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Panggil auto-scroll
        PSV_scrollTop();
    });
</script>