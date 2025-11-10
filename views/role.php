<?php
/*
 * /views/role.php
 * Halaman pemilihan Peran/Role (Langkah 3).
 * Variabel $pdo dan $page_title tersedia dari index.php
 */

// Perkiraan total langkah
$total_steps = 7; 
$current_step_number = 3;

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
                            Internal - Teknisi / Fungsional / Pelaksana
                            <div class="form-text mt-1 ps-4">Pegawai yang bertugas di level operasional atau teknis terkait sistem dan layanan digital.</div>
                        </label>
                        <label class="list-group-item list-group-item-action fs-5 p-3">
                            <input class="form-check-input me-3" 
                                   type="radio" 
                                   name="role" 
                                   value="manajer" 
                                   id="role-manajer" 
                                   required>
                            Internal - Manajer / Pengambil Keputusan
                            <div class="form-text mt-1 ps-4">Pegawai yang berperan dalam perencanaan, pengelolaan, dan pengawasan layanan digital (misal: Eselon IV ke atas, Fungsional Ahli Muda ke atas).</div>
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

                    <div class="d-flex justify-content-end">
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

        // Panggil validasi sederhana untuk radio button
        // (Fungsi PSV_validateStep ada di footer.php)
        // Kita panggil di sini untuk inisialisasi
        
        // Kita gunakan fungsi kustom sederhana di sini karena PSV_validateStep 
        // di-desain untuk halaman section.
        function validateRole() {
            var $checked = $('input[name="role"]:checked');
            $nextBtn.prop('disabled', $checked.length === 0);
        }

        // Cek saat pertama kali load (jika user klik 'back' - browser)
        validateRole();

        // Cek setiap kali user mengganti pilihan
        $('input[name="role"]').on('change', validateRole);
    });
</script>