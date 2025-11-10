<?php
/*
 * /views/welcome.php
 * Halaman selamat datang (Langkah 1).
 * Variabel $pdo dan $page_title tersedia dari index.php
 */

// Hitung total langkah (perkiraan)
$total_steps = 7; // Welcome, Name, Role, Demo, Instruct, Sections (min 1), Done
?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        
        <div class="progress mb-3" role="progressbar" aria-label="Progres Kuesioner" aria-valuenow="1" aria-valuemin="0" aria-valuemax="<?php echo $total_steps; ?>">
            <div class="progress-bar" style="width: 5%">Langkah 1 dari <?php echo $total_steps; ?></div>
        </div>

        <div class="card shadow-sm wizard-card">
            <div class="card-body p-4 p-md-5">
                
                <h3 class="card-title text-center mb-4">Selamat Datang di Kuesioner Public Service Value</h3>
                
                <p>Terima kasih atas kesediaan Anda untuk berpartisipasi dalam kuesioner ini. Partisipasi Anda sangat penting untuk menganalisis dan meningkatkan nilai layanan publik kami, khususnya dalam pemanfaatan teknologi digital dan kecerdasan buatan.</p>
                <p>Pengisian kuesioner ini diperkirakan memakan waktu 10–15 menit. Semua jawaban Anda akan dijaga kerahasiaannya dan hanya digunakan untuk tujuan penelitian akademis.</p>
                
                <hr class="my-4">

                <form action="index.php" method="POST">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="d-grid">
                        <button type="submit" name="action" value="start_wizard" class="btn btn-primary btn-lg">
                            Mulai Kuesioner
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // Panggil auto-scroll (meskipun ini halaman pertama, untuk konsistensi)
    $(document).ready(function() {
        PSV_scrollTop();
    });
</script>