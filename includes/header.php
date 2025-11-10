<?php
/*
 * /includes/header.php
 * Header HTML, include Bootstrap 5 + jQuery 3.7 CDN
 *
 * PERBAIKAN: Memperbaiki fungsi window.PSV_validateStep()
 * agar mencari radio button :checked di dalam div grup,
 * bukan berdasarkan 'name' yang salah.
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
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script>
        (function($) {
            "use strict";

            /**
             * Fungsi untuk men-disable tombol 'Next' jika radio group belum terisi.
             * Dipanggil di views/section.php
             */
            window.PSV_validateStep = function() {
                var $radioGroups = $('[data-group-name]');
                var $nextButton = $('#next-btn');
                
                if ($radioGroups.length === 0 || $nextButton.length === 0) {
                    return;
                }

                var allAnswered = true;
                
                $radioGroups.each(function() {
                    // 'this' adalah div.likert-scale
                    
                    // --- INI ADALAH PERBAIKAN ---
                    // Kita cari input radio :checked DI DALAM div ini.
                    // Kita tidak lagi peduli dengan atribut 'name'.
                    if ($(this).find('input[type="radio"]:checked').length === 0) {
                        allAnswered = false;
                        return false; // Hentikan loop .each() lebih awal
                    }
                    // --- AKHIR PERBAIKAN ---
                });

                $nextButton.prop('disabled', !allAnswered);
            };

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