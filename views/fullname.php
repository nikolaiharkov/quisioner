<?php
// FILE: views/fullname.php
/*
 * REVISI: Total steps menjadi 9
 * REVISI 2 (USER): Menambahkan input Nomor Telepon
 * REVISI 3 (USER): Menambahkan opsi Anonim
 */

// Perkiraan total langkah
$total_steps = 9; 
$current_step_number = 3;

// Ambil data sementara jika user kembali (klik 'back')
$temp_full_name = $_SESSION['temp_full_name'] ?? '';
$temp_phone_number = $_SESSION['temp_phone_number'] ?? '';

// Cek apakah nama 'Anonim' digunakan
$is_anonim = ($temp_full_name === 'Anonim');
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
                        <label for="full_name" class="form-label fs-5">Nama Lengkap Anda <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="full_name" 
                               name="full_name"
                               value="<?php echo esc_html($temp_full_name); ?>"
                               <?php if ($is_anonim) echo 'readonly'; // <-- BARU ?>
                               required 
                               autocomplete="name"
                               autofocus>
                        <div class="form-text">Nama Anda akan digunakan untuk keperluan analisis data internal.</div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="anonim_check" <?php if ($is_anonim) echo 'checked'; // <-- BARU ?>>
                        <label class="form-check-label" for="anonim_check">
                            Saya ingin mengisi sebagai "Anonim"
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label fs-5">Nomor Telepon (WhatsApp) <span class="text-danger">*</span></label>
                        <input type="tel" 
                               class="form-control form-control-lg" 
                               id="phone_number" 
                               name="phone_number"
                               value="<?php echo esc_html($temp_phone_number); ?>"
                               required 
                               autocomplete="tel"
                               placeholder="Contoh: 08123456789">
                        <div class="form-text">Digunakan untuk menghubungi pemenang undian doorprize e-Wallet.</div>
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

        // Validasi untuk form text
        var $nameInput = $('#full_name');
        var $phoneInput = $('#phone_number');
        var $nextBtn = $('#next-btn');
        var $anonCheck = $('#anonim_check'); // <-- BARU

        // --- AWAL LOGIKA ANONIM (BARU) ---
        $anonCheck.on('change', function() {
            if ($(this).is(':checked')) {
                // Jika dicentang, isi "Anonim" dan kunci
                $nameInput.val('Anonim').prop('readonly', true);
            } else {
                // Jika tidak dicentang, kosongkan dan buka kunci
                $nameInput.val('').prop('readonly', false).focus();
            }
            // Validasi ulang setelah diubah
            validateForm();
        });
        // --- AKHIR LOGIKA ANONIM (BARU) ---


        // Fungsi validasi (diperbarui)
        function validateForm() {
            var name = $nameInput.val().trim();
            var phone = $phoneInput.val().trim();
            // Tombol 'Next' nonaktif sampai kedua field terisi
            $nextBtn.prop('disabled', name.length === 0 || phone.length === 0);
        }

        // Cek saat pertama kali load (jika ada data dari session)
        validateForm();

        // Cek setiap kali user mengetik di salah satu input
        $nameInput.add($phoneInput).on('input', validateForm);

        // Pastikan validasi berjalan sebelum submit (opsional, tapi bagus)
        $('#fullname-form').on('submit', function(e) {
            // Jangan cegah submit jika tombol 'go_back' yang diklik
            var $clickedButton = $(document.activeElement);
            if ($clickedButton.attr('name') === 'action' && $clickedButton.val() === 'go_back') {
                return;
            }

            if ($nameInput.val().trim().length === 0 || $phoneInput.val().trim().length === 0) {
                e.preventDefault(); // Hentikan submit jika (entah bagaimana) kosong
                $nextBtn.prop('disabled', true);
            }
        });
    });
</script>