<?php
// FILE: views/fullname.php
/*
 * REVISI: Total steps menjadi 9
 */

// Perkiraan total langkah
$total_steps = 9; 
$current_step_number = 3;

// Ambil nama sementara jika user kembali (klik 'back')
$temp_full_name = $_SESSION['temp_full_name'] ?? '';
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
                
                <h3 class="card-title mb-4">Identitas Responden</h3>
                
                <form action="index.php" method="POST" id="fullname-form">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label fs-5">Silakan masukkan Nama Lengkap Anda:</label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="full_name" 
                               name="full_name"
                               value="<?php echo esc_html($temp_full_name); ?>"
                               required 
                               autocomplete="name"
                               autofocus>
                        <div class="form-text">Nama Anda akan digunakan untuk keperluan analisis data internal.</div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <button type="submit" name="action" value="go_back" class="btn btn-outline-secondary btn-lg" formnovalidate>
                            Kembali
                        </button>
                        <button type="submit" name="action" value="submit_fullname" id="next-btn" class="btn btn-primary btn-lg" disabled>
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

        // Validasi sederhana untuk form text (sesuai prompt)
        var $input = $('#full_name');
        var $nextBtn = $('#next-btn');

        // Fungsi validasi
        function validateFullName() {
            var name = $input.val().trim();
            // Tombol 'Next' nonaktif sampai terisi
            $nextBtn.prop('disabled', name.length === 0);
        }

        // Cek saat pertama kali load (jika ada data dari session)
        validateFullName();

        // Cek setiap kali user mengetik
        $input.on('input', validateFullName);

        // Pastikan validasi berjalan sebelum submit (opsional, tapi bagus)
        $('#fullname-form').on('submit', function(e) {
            // Jangan cegah submit jika tombol 'go_back' yang diklik
            var $clickedButton = $(document.activeElement);
            if ($clickedButton.attr('name') === 'action' && $clickedButton.val() === 'go_back') {
                return;
            }

            if ($input.val().trim().length === 0) {
                e.preventDefault(); // Hentikan submit jika (entah bagaimana) kosong
                $nextBtn.prop('disabled', true);
            }
        });
    });
</script>