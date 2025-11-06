/*
 * /assets/js/seed.js
 * (REVISI: Ditambahkan data lorem ipsum untuk demo yang lebih penuh)
 *
 * File ini berisi data awal (seed) untuk kuesioner.
 * Data ini akan digunakan oleh app.js dan admin.js untuk inisialisasi
 * localStorage (psv_questions) jika belum ada.
 */

// Pastikan namespace global PSV ada
window.PSV = window.PSV || {};

(function() {

    // Skala Likert 1-7 yang akan digunakan di seluruh aplikasi
    const LIKERT_SCALE_7 = [
        { value: 1, label: "Sangat Tidak Setuju" },
        { value: 2, label: "Tidak Setuju" },
        { value: 3, label: "Agak Tidak Setuju" },
        { value: 4, label: "Netral" },
        { value: 5, label: "Agak Setuju" },
        { value: 6, label: "Setuju" },
        { value: 7, label: "Sangat Setuju" }
    ];

    // Data awal (seed) untuk pertanyaan kuesioner
    // Struktur ini WAJIB diikuti
    const SEED_QUESTIONS = {
        "section1": {
            "teknisi": [
                { "id": "S1T-1", "text": "Prosedur pemeliharaan peralatan sudah terdokumentasi dengan baik." },
                { "id": "S1T-2", "text": "Waktu henti (downtime) peralatan dapat diminimalkan." },
                { "id": "S1T-3", "text": "Ketersediaan suku cadang mendukung keandalan layanan." },
                { "id": "S1T-4", "text": "Lorem ipsum dolor sit amet, consectetur adipiscing elit." },
                { "id": "S1T-5", "text": "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip." },
                { "id": "S1T-6", "text": "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore." }
            ],
            "manajer": [
                { "id": "S1M-1", "text": "Target kinerja layanan publik dikomunikasikan dengan jelas." },
                { "id": "S1M-2", "text": "Alokasi anggaran mendukung prioritas strategis." },
                { "id": "S1M-3", "text": "Koordinasi lintas divisi berjalan efektif." },
                { "id": "S1M-4", "text": "Lorem ipsum dolor sit amet, consectetur adipiscing elit." },
                { "id": "S1M-5", "text": "Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia." }
            ]
        },
        "section2": [
            { "id": "S2-1", "text": "Proses kerja harian berjalan efisien." },
            { "id": "S2-2", "text": "Sistem pelaporan gangguan mudah digunakan." },
            { "id": "S2-3", "text": "Tindak lanjut perbaikan dilakukan tepat waktu." },
            { "id": "S2-4", "text": "Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." },
            { "id": "S2-5", "text": "Egestas pretium aenean pharetra magna ac placerat vestibulum." },
            { "id": "S2-6", "text": "Nunc sed id semper risus in hendrerit gravida rutrum." },
            { "id": "S2-7", "text": "Commodo odio aenean sed adipiscing diam donec adipiscing tristique." }
        ],
        "section3": [
            { "id": "S3-1", "text": "Komunikasi internal tim berlangsung terbuka." },
            { "id": "S3-2", "text": "Pelatihan/peningkatan kemampuan tersedia memadai." },
            { "id": "S3-3", "text": "Perangkat kerja memenuhi kebutuhan tugas." },
            { "id": "S3-4", "text": "Quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo." }
        ],
        "section4": [
            { "id": "S4-1", "text": "Kebijakan mutu dipahami oleh seluruh staf." },
            { "id": "S4-2", "text": "Audit internal membantu peningkatan layanan." },
            { "id": "S4-3", "text": "Umpan balik digunakan untuk perbaikan berkelanjutan." },
            { "id": "S4-4", "text": "Volutpat lacus laoreet non curabitur gravida arcu ac." },
            { "id": "S4-5", "text": "Turpis egestas maecenas pharetra convallis posuere morbi leo." }
        ],
        "section5": [
            // Section 5 hanya untuk Eksternal
            { "id": "S5-1", "text": "Informasi layanan publik mudah diakses." },
            { "id": "S5-2", "text": "Respons terhadap pengaduan cepat." },
            { "id": "S5-3", "text": "Kualitas layanan sesuai harapan." },
            { "id": "S5-4", "text": "Ac feugiat sed lectus vestibulum mattis ullamcorper velit." },
            { "id": "S5-5", "text": "Neque vitae tempus quam pellentesque nec nam aliquam sem." },
            { "id": "S5-6", "text": "Consectetur adipiscing elit pellentesque habitant morbi tristique." }
        ]
    };

    // Ekspor data ke namespace global
    window.PSV.SEED_QUESTIONS = SEED_QUESTIONS;
    window.PSV.LIKERT_SCALE_7 = LIKERT_SCALE_7;

    console.log("PSV Seed data loaded (Versi Revisi dengan Lorem Ipsum).");

})();