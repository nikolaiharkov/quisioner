<?php
// FILE: views/instructions.php
/*
 * REVISI: Total steps menjadi 9
 * REVISI 2: Menambahkan blok persetujuan (consent)
 * dan JavaScript validasi client-side.
 */

// Perkiraan total langkah
$total_steps = 9; 
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
                
                <h3 class="card-title mb-4">Petunjuk Pengisian</h3>
                
                <p>Berikutnya Anda akan disajikan serangkaian pernyataan. Mohon berikan penilaian Anda terhadap setiap pernyataan menggunakan <strong>skala 1 sampai 7</strong> berikut:</p>
                
                <div class="table-responsive my-4">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                                <th>6</th>
                                <th>7</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sangat Tidak Setuju</td>
                                <td>Tidak Setuju</td>
                                <td>Agak Tidak Setuju</td>
                                <td>Netral</td>
                                <td>Agak Setuju</td>
                                <td>Setuju</td>
                                <td>Sangat Setuju</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p>Pilih jawaban yang paling mewakili pandangan dan pengalaman Anda. Tidak ada jawaban yang benar atau salah.</p>
                <p>Pastikan tidak ada pernyataan yang terlewat. Tombol "Berikutnya" hanya akan aktif setelah semua pernyataan di halaman tersebut terjawab.</p>

                <form action="index.php" method="POST" id="instructions-form">
                    <?php csrf_input(); // Helper untuk CSRF token ?>

                    <div class="card bg-light my-4 border-warning">
                        <div class="card-body">
                            <p class="fw-bold fs-5">Persetujuan Partisipasi Sukarela <span class="text-danger">*</span></p>
                            <blockquote class="blockquote small">
                                "Saya bersedia secara sukarela mengisi kuesioner ini serta memberikan kewenangan kepada peneliti untuk menggunakan, mengolah, dan mempublikasikan data penelitian dengan tetap menjaga kerahasiaan identitas saya."
                            </blockquote>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="consent" id="consent-yes" value="yes" required>
                                <label class="form-check-label" for="consent-yes">
                                    Ya, saya setuju
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="consent" id="consent-no" value="no" required>
                                <label class="form-check-label" for="consent-no">
                                    Tidak
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="action" value="go_back" class="btn btn-outline-secondary btn-lg" formnovalidate>
                            Kembali
                        </button>
                        <button type="submit" name="action" value="start_questions" id="next-btn" class="btn btn-primary btn-lg" disabled>
                            Mulai Mengisi
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

        // --- SCRIPT BARU UNTUK VALIDASI PERSETUJUAN ---
        var $consentRadios = $('input[name="consent"]');
        var $nextBtn = $('#next-btn');

        function validateConsent() {
            // Aktifkan tombol 'Next' HANYA jika 'Ya' (value="yes") dicentang
            if ($('input[name="consent"][value="yes"]').is(':checked')) {
                $nextBtn.prop('disabled', false);
            } else {
                $nextBtn.prop('disabled', true);
            }
        }

        // Cek saat halaman dimuat (jika browser mengingat pilihan)
        validateConsent();

        // Cek setiap kali pilihan radio diubah
        $consentRadios.on('change', validateConsent);
        // --- AKHIR SCRIPT BARU ---
    });
</script>