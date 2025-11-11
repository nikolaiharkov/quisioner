<?php
// FILE: views/done.php
/*
 * /views/done.php
 * Halaman Selesai / Terima Kasih (Langkah Terakhir).
 * REVISI: Total steps menjadi 9
 * REVISI 2: Unset 'current_section_part'
 */

// Perkiraan total langkah
$total_steps = 9; 
$current_step_number = 9;

$session_id_display = $_SESSION['session_id'] ?? null;
$full_name_display = $_SESSION['full_name'] ?? 'Responden';

// --- Logika Pembersihan Session ---
unset(
    $_SESSION['wizard_step'], 
    $_SESSION['respondent_id'], 
    $_SESSION['session_id'], 
    $_SESSION['role'], 
    $_SESSION['current_section_id'],
    $_SESSION['current_section_part'], // <-- Tambahan baru
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