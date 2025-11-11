<?php
// FILE: views/instructions.php
/*
 * REVISI: Total steps menjadi 9
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

                <hr class="my-4">

                <form action="index.php" method="POST">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="action" value="go_back" class="btn btn-outline-secondary btn-lg">
                            Kembali
                        </button>
                        <button type="submit" name="action" value="start_questions" class="btn btn-primary btn-lg">
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
    });
</script>