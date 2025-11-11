<?php
// FILE: views/role.php
/*
 * REVISI: Total steps menjadi 9
 */

// Perkiraan total langkah
$total_steps = 9; 
$current_step_number = 4;

$full_name = $_SESSION['temp_full_name'] ?? 'Responden';
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
                
                <h3 class="card-title mb-2">Pilih Peran Anda</h3>
                <p class="text-muted mb-4">Halo, <?php echo esc_html($full_name); ?>. Silakan pilih peran yang paling sesuai dengan Anda.</p>
                
                <form action="index.php" method="POST" id="role-form">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="list-group" data-group-name="role_group">
                        <label class="list-group-item list-group-item-action fs-5 p-3">
                            <input class="form-check-input me-3" 
                                   type="radio" 
                                   name="role" 
                                   value="teknisi" 
                                   id="role-teknisi" 
                                   required>
                            DJBC Pelaksana
                            <div class="form-text mt-1 ps-4">Pegawai pelaksana/fungsional setara yang bertugas untuk teknis sistem layanan dan digital</div>
                        </label>
                        <label class="list-group-item list-group-item-action fs-5 p-3">
                            <input class="form-check-input me-3" 
                                   type="radio" 
                                   name="role" 
                                   value="manajer" 
                                   id="role-manajer" 
                                   required>
                            DJBC Manager
                            <div class="form-text mt-1 ps-4">Pegawai eselon 4 / fungsional ahli muda keatas</div>
                        </label>
                        <label class="list-group-item list-group-item-action fs-5 p-3">
                            <input class="form-check-input me-3" 
                                   type="radio" 
                                   name="role" 
                                   value="eksternal" 
                                   id="role-eksternal" 
                                   required>
                            Eksternal - Pemangku Kepentingan
                            <div class="form-text mt-1 ps-4">Pengguna layanan dari luar instansi (misal: Importir, Eksportir, PPJK, Perusahaan Logistik, dll).</div>
                        </label>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <button type="submit" name="action" value="go_back" class="btn btn-outline-secondary btn-lg" formnovalidate>
                            Kembali
                        </button>
                        <button type="submit" name="action" value="submit_role" id="next-btn" class="btn btn-primary btn-lg" disabled>
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

        // Ambil tombol 'Next'
        var $nextBtn = $('#next-btn');

        // Ambil data peran yang mungkin tersimpan jika user kembali
        var savedRole = "<?php echo $_SESSION['role'] ?? ''; ?>";

        function validateRole() {
            var $checked = $('input[name="role"]:checked');
            if ($checked.length > 0) {
                $nextBtn.prop('disabled', false);
            } else {
                $nextBtn.prop('disabled', true);
            }
        }
        
        // Jika ada data 'role' di session (dari langkah submit_role sebelumnya),
        // centang radio button yang sesuai
        if (savedRole) {
            $('input[name="role"][value="' + savedRole + '"]').prop('checked', true);
        }

        // Cek saat pertama kali load
        validateRole();

        // Cek setiap kali user mengganti pilihan
        $('input[name="role"]').on('change', validateRole);
    });
</script>