<?php
/*
 * /views/demographics.php
 * Halaman Demografi (Langkah 4).
 * REVISI: File ini sekarang "bodoh" (dumb view).
 * Logika POST telah dihapus karena ditangani oleh /index.php.
 *
 * Variabel $pdo, $page_title, $role, $session_id tersedia dari index.php
 */

// Tentukan partial view yang akan dimuat
$is_eksternal = ($role === 'eksternal');
$partial_view = $is_eksternal
    ? __DIR__ . '/demographics_eksternal.php'
    : __DIR__ . '/demographics_teknisi_manajer.php';

// Data untuk ditampilkan di form (jika user kembali)
// Kita gunakan session sementara untuk menyimpan ini jika validasi gagal
$saved_data = $_SESSION['temp_demographics'] ?? [];

// Pengaturan Progress Bar
$total_steps = 7; 
$current_step_number = 4;
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
                
                <h3 class="card-title mb-4">Informasi Demografis</h3>
                <p>Mohon lengkapi data berikut. Informasi ini hanya akan digunakan untuk analisis data secara agregat.</p>

                <form action="index.php" method="POST" id="demographics-form">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <?php
                    // Muat formulir yang sesuai (Teknisi/Manajer atau Eksternal)
                    if (file_exists($partial_view)) {
                        include $partial_view;
                    } else {
                        echo '<div class="alert alert-danger">Error: Gagal memuat formulir demografis.</div>';
                    }
                    ?>
                    
                    <hr class="my-4">

                    <div class="d-flex justify-content-end">
                        <button type="submit" name="action" value="submit_demographics" id="next-btn" class="btn btn-primary btn-lg" disabled>
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

        // Validasi form demografi
        var $form = $('#demographics-form');
        var $nextBtn = $('#next-btn');

        function validateDemographics() {
            var allValid = true;
            
            // Cari semua input 'required' di dalam form
            $form.find(':input[required]').each(function() {
                var $input = $(this);
                
                if ($input.is(':radio')) {
                    // Cek radio group
                    var name = $input.attr('name');
                    if ($('input[name="' + name + '"]:checked').length === 0) {
                        allValid = false;
                    }
                } else {
                    // Cek input text/select
                    if (!$input.val() || $input.val().trim() === '') {
                        allValid = false;
                    }
                }
            });
            
            $nextBtn.prop('disabled', !allValid);
        }

        // Cek saat pertama kali load
        validateDemographics();

        // Cek setiap kali user mengubah input
        $form.on('input change', ':input[required]', validateDemographics);
    });
</script>