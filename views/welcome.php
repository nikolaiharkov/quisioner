<?php
// FILE: views/welcome.php
/*
 * /views/welcome.php
 * REVISI: Total steps menjadi 9
 */

// Hitung total langkah (perkiraan)
$total_steps = 9; // Welcome, Welcome2, Name, Role, Demo, Instruct, S1, S2, S3, Done
$current_step_number = 1;
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
                
                <div class="text-center mb-3">
                    <img src="https://via.placeholder.com/150" 
                         alt="Logo Kuesioner" 
                         style="width: 100%; max-width: 120px; height: auto;">
                    </div>
                <h3 class="card-title text-center mb-4">Persetujuan Kuesioner Penelitian</h3>
                
                <p><strong>Kepada Yth. Bapak/Ibu Responden,</strong><br>Dengan hormat, Terima kasih atas kesediaan Bapak/Ibu meluangkan waktu untuk berpartisipasi dalam pengisian kuesioner penelitian ini.</p>
                
                <hr>
                
                <p class="mb-2"><strong>PERKENALAN PENELITI:</strong></p>
                <p>Nama saya Dwi Adi Kurniawan, mahasiswa program Magister Public Administration di Huazhong University of Science and Technology (HUST), Tiongkok. Saat ini saya sedang melaksanakan penelitian karya ilmiah dengan judul sebagaimana tercantum di atas.</p>

                <p class="mb-2"><strong>TUJUAN PENELITIAN:</strong></p>
                <p>Penelitian ini bertujuan untuk menganalisis pengaruh Human AI Competency terhadap Citizen Centric Service Value, dengan Organizational Service Innovation sebagai variabel mediasi dan Digital Organisational Culture sebagai variabel moderasi.</p>
                <p>Konteks penelitian berfokus pada penerapan sistem digital berbasis kecerdasan buatan (AI-enabled systems) di lingkungan Direktorat Jenderal Bea dan Cukai (DJBC) guna memperkuat inovasi layanan publik yang adaptif dan berorientasi pada kebutuhan masyarakat.</p>
                <p><em>Catatan: Dalam penelitian ini, istilah AI-enabled systems merujuk pada penerapan sistem digital berbasis kecerdasan buatan di lingkungan DJBC, seperti ekosistem CEISA 4.0 (TASYA CEISA Care, penjaluran dan Intelligent Classification Assistant) yang memanfaatkan data dan otomatisasi cerdas untuk mendukung proses pelayanan dan pengawasan.</em></p>
                
                <hr class="my-4">

                <form action="index.php" method="POST">
                    <?php csrf_input(); // Helper untuk CSRF token ?>
                    
                    <div class="d-grid">
                        <button type="submit" name="action" value="start_wizard" class="btn btn-primary btn-lg">
                            Berikutnya
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