<?php
// FILE: includes/header.php
/*
 * /includes/header.php
 * Header HTML, include Bootstrap 5 + jQuery 3.7 CDN
 *
 * PERBAIKAN: Memperbaiki fungsi window.PSV_validateStep()
 * agar mencari radio button :checked di dalam div grup,
 * bukan berdasarkan 'name' yang salah.
 *
 * REVISI (USER): Menambahkan highlight visual (border merah)
 * pada pertanyaan yang belum terjawab.
 *
 * REVISI 2 (USER): Menambahkan TEKS peringatan merah
 * di bawah pertanyaan yang belum terjawab.
 */

// Jika config.php belum di-include (misal di halaman error)
if (!defined('DB_HOST')) {
    // Panggil config.php dari root
    require_once __DIR__ . '/../config.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Kuesioner Public Service Value'; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <style>
        /* CSS minimal untuk layout wizard */
        body { background-color: #f8f9fa; }
        .wizard-card { max-width: 800px; }

        /* Gaya Likert Vertikal (Sesuai Master Prompt) */
        .likert-scale {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #eee;
            background-color: #fff;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
            /* --- CSS REVISI (USER) --- */
            /* Transisi agar perubahan border mulus */
            border: 2px solid transparent;
            transition: border-color 0.3s ease, background-color 0.3s ease;
            /* --- AKHIR CSS REVISI --- */
        }
        .likert-scale:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .likert-text {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }
        .likert-radio-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding-left: 0.5rem;
        }
        .likert-radio-group .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-left: 0;
            padding: 0.375rem 0.5rem;
            border-radius: 0.25rem;
        }
        .likert-radio-group .form-check:hover {
            background-color: #f8f9fa;
        }
        .likert-radio-group .form-check-input {
            margin-left: 0;
            float: none;
            cursor: pointer;
        }
        .likert-radio-group .form-check-label {
            padding-top: 0;
            line-height: 1.5;
            cursor: pointer;
        }
        .likert-radio-group .badge {
            flex-shrink: 0;
            width: 1.5rem; /* Lebar badge angka */
            height: 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* --- CSS REVISI 1 (USER): Gaya untuk peringatan --- */
        .likert-scale.unanswered-warning {
            border-color: #dc3545; /* Merah (Bootstrap 'danger') */
            background-color: #fff8f8;
        }
        /* --- AKHIR CSS REVISI 1 --- */

        /* --- CSS REVISI 2 (USER): Gaya untuk teks peringatan --- */
        .likert-error-message {
            display: none; /* Sembunyi by default */
            font-size: 0.875rem;
            font-weight: 500;
            color: #dc3545; /* Merah (Bootstrap 'danger') */
            margin-top: 0.75rem;
            padding-left: 0.5rem;
        }
        /* --- AKHIR CSS REVISI 2 --- */

    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script>
        (function($) {
            "use strict";

            /**
             * Fungsi untuk men-disable tombol 'Next' jika radio group belum terisi.
             * Dipanggil di views/section.php
             *
             * --- REVISI 2 (USER) ---
             * Logika diubah:
             * 1. Loop TIDAK berhenti lebih awal.
             * 2. Tambahkan/Hapus class 'unanswered-warning' pada setiap grup.
             * 3. Tampilkan/Sembunyikan '.likert-error-message' di dalam grup.
             */
            window.PSV_validateStep = function() {
                var $radioGroups = $('[data-group-name]');
                var $nextButton = $('#next-btn');
                
                if ($radioGroups.length === 0 || $nextButton.length === 0) {
                    return;
                }

                var allAnswered = true; // Asumsikan semua terjawab
                
                $radioGroups.each(function() {
                    // 'this' adalah div.likert-scale
                    var $currentGroup = $(this);
                    // Cari elemen error di dalam grup ini
                    var $errorMessage = $currentGroup.find('.likert-error-message');
                    
                    if ($currentGroup.find('input[type="radio"]:checked').length === 0) {
                        // Jika tidak terjawab
                        allAnswered = false; // Set flag menjadi false
                        $currentGroup.addClass('unanswered-warning'); // Tambahkan class peringatan
                        $errorMessage.text('※ Jawaban wajib diisi').show(); // Tampilkan teks error
                    } else {
                        // Jika terjawab
                        $currentGroup.removeClass('unanswered-warning'); // Hapus class peringatan
                        $errorMessage.text('').hide(); // Sembunyikan teks error
                    }
                });

                // Nonaktifkan tombol 'Next' jika 'allAnswered' adalah false
                $nextButton.prop('disabled', !allAnswered);
            };
            /* --- AKHIR REVISI (USER) --- */

            /**
             * Fungsi untuk auto-scroll ke atas (ke wizard card).
             * Dipanggil di setiap halaman view wizard.
             */
            window.PSV_scrollTop = function() {
                // Kita tambahkan penundaan sedikit untuk memastikan DOM siap
                setTimeout(function() {
                    var $target = $(".wizard-card").first();
                    if ($target.length) {
                        $('html, body').animate({
                            scrollTop: $target.offset().top - 20 // 20px padding dari atas
                        }, 400);
                    }
                }, 100); // 100ms delay
            };

        })(jQuery);
    </script>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php">
                Public Service Value
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/index.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container my-4 my-md-5">