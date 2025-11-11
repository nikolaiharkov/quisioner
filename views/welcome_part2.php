<?php
// FILE: views/welcome_part2.php
/*
 * REVISI: Total steps menjadi 9
 */

// Hitung total langkah
$total_steps = 9; 
$current_step_number = 2;
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
                
                <h3 class="card-title mb-4">Sasaran Responden Kuesioner</h3>
                
                <p>Kuesioner ini ditujukan kepada pegawai Direktorat Jenderal Bea dan Cukai (DJBC) serta pemangku kepentingan eksternal yang menggunakan atau berinteraksi dengan layanan digital DJBC (termasuk CEISA 4.0).</p>
                <p>Responden terdiri dari:</p>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        <strong>Pelaksana / Fungsional setara (Pegawai DJBC – Level Operasional):</strong>
                        <p class="small text-muted mb-0">Pegawai Negeri Sipil pada Direktorat Jenderal Bea dan Cukai dengan masa kerja minimum 2 (dua) tahun, diutamakan yang bertugas pada Direktorat Informasi Kepabeanan dan Cukai serta unit Dukungan Teknis terkait sistem dan layanan digital di seluruh satuan kerja DJBC.</p>
                    </li>
                    <li class="list-group-item">
                        <strong>Manajer / Pengambil Keputusan (Pegawai DJBC – Level Strategis):</strong>
                        <p class="small text-muted mb-0">Pegawai yang berperan dalam perencanaan, pengelolaan, dan pengawasan sistem layanan digital pintar Bea Cukai. Kelompok manajerial mencakup Pejabat Struktural (Eselon IV hingga Eselon I) dan Pejabat Fungsional Ahli Muda hingga Ahli Utama yang terlibat dalam perencanaan, pengelolaan, dan pengawasan layanan digital DJBC.</p>
                    </li>
                    <li class="list-group-item">
                        <strong>Pemangku Kepentingan Eksternal:</strong>
                        <p class="small text-muted mb-0">PPJK, importir, eksportir, pengelola gudang/depo/logistik, serta pengguna layanan digital lain yang terhubung dengan sistem DJBC.</p>
                    </li>
                </ul>

                <h4 class="h5 mt-4">MANFAAT PENELITIAN:</h4>
                <p>Partisipasi Bapak/Ibu diharapkan dapat memberikan kontribusi ilmiah dan rekomendasi kebijakan untuk memperkuat kesiapan organisasi publik, khususnya DJBC, dalam menghadapi transformasi digital dan integrasi kecerdasan buatan di sektor kepabeanan dan cukai Indonesia.</p>

                <hr class="my-4">

                <form action="index.php" method="POST">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="action" value="go_back" class="btn btn-outline-secondary btn-lg">
                            Kembali
                        </button>
                        <button type="submit" name="action" value="submit_welcome_part2" class="btn btn-primary btn-lg">
                            Mulai Kuesioner
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // Panggil auto-scroll
    $(document).ready(function() {
        PSV_scrollTop();
    });
</script>