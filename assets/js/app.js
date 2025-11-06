/*
 * /assets/js/app.js
 * (REVISI 6: Menambahkan auto-scroll-to-top saat pindah section)
 *
 * Logika inti untuk wizard kuesioner (index.html).
 * Menggunakan jQuery dan mengandalkan seed.js.
 */

// Pastikan namespace global PSV ada
window.PSV = window.PSV || {};

(function($, PSV) {
    "use strict";

    // --- 1. Helper Functions ---

    PSV.storage = {
        /**
         * Mengambil data dari localStorage dan parse JSON.
         * @param {string} key - Kunci localStorage.
         * @returns {any} Data yang sudah di-parse atau null.
         */
        get: function(key) {
            try {
                const data = localStorage.getItem(key);
                return data ? JSON.parse(data) : null;
            } catch (e) {
                console.error(`Error mengambil data dari localStorage [${key}]:`, e);
                return null;
            }
        },

        /**
         * Menyimpan data ke localStorage sebagai string JSON.
         * @param {string} key - Kunci localStorage.
         * @param {any} value - Nilai yang akan disimpan.
         */
        set: function(key, value) {
            try {
                localStorage.setItem(key, JSON.stringify(value));
            } catch (e) {
                console.error(`Error menyimpan data ke localStorage [${key}]:`, e);
            }
        }
    };

    PSV.helpers = {
        /**
         * Menghasilkan hash SHA-256 dari string (asinkron).
         * @param {string} message - String yang akan di-hash.
         * @returns {Promise<string>} Hex string dari hash SHA-256.
         */
        sha256Hex: async function(message) {
            try {
                const msgBuffer = new TextEncoder().encode(message);
                const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
                const hashArray = Array.from(new Uint8Array(hashBuffer));
                return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            } catch (e) {
                console.error("Error saat hashing SHA-256:", e);
                throw e;
            }
        },

        /**
         * Menghasilkan ID submit unik.
         * @returns {string} Format: RESP-<timestamp>
         */
        generateId: function() {
            return `RESP-${Date.now()}`;
        },

        /**
         * Mendapatkan timestamp ISO 8601 saat ini.
         * @returns {string} ISO string timestamp.
         */
        getISOTime: function() {
            return new Date().toISOString();
        }
    };

    PSV.validate = {
        /**
         * Validasi format email sederhana.
         * @param {string} email - Email untuk divalidasi.
         * @returns {boolean} True jika valid.
         */
        email: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        },

        /**
         * Memeriksa validitas semua input 'required' dalam sebuah grup/form.
         * @param {string} selector - Selector jQuery untuk kontainer (mis: "#step-email", "#demografis-teknisi").
         * @returns {boolean} True jika semua input 'required' valid.
         */
        formGroup: function(selector) {
            let isValid = true;
            $(`${selector} [required]`).each(function() {
                const $input = $(this);
                if ($input.is(':radio') || $input.is(':checkbox')) {
                    if ($(`input[name="${$input.attr('name')}"]:checked`).length === 0) {
                        isValid = false;
                    }
                } else {
                    if (!$input.val() || $input.val().trim() === "") {
                        isValid = false;
                    }
                }
            });
            return isValid;
        }
    };

    // --- 2. Wizard Core Logic ---

    PSV.wizard = {
        currentStepIndex: 0,
        wizardData: {
            email: null,
            posisi: null,
            demografis: {},
            answers: {}
        },
        questions: {}, // Dimuat dari localStorage
        flow: [], // Alur langkah-langkah wizard
        totalSteps: 0,
        
        // Deskripsi untuk setiap section
        sectionDescriptions: {
            "section1": "Penilaian ini berfokus pada aspek teknis, strategis, dan manajerial. Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            "section2": "Bagian ini mengukur efisiensi proses kerja harian. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore.",
            "section3": "Fokus pada komunikasi internal, ketersediaan sumber daya, dan pengembangan kapabilitas. Excepteur sint occaecat cupidatat non proident.",
            "section4": "Bagian ini menilai pemahaman kebijakan mutu dan proses perbaikan berkelanjutan. Ut enim ad minim veniam, quis nostrud exercitation.",
            "section5": "Penilaian dari mitra eksternal mengenai aksesibilitas, respons, dan kualitas layanan. Sed ut perspiciatis unde omnis iste natus error."
        },

        /**
         * Inisialisasi utama wizard.
         */
        init: async function() {
            this.loadPartials();
            await this.initializeAppStorage();
            
            this.questions = PSV.storage.get("psv_questions");
            if (!this.questions) {
                console.error("Gagal memuat pertanyaan kuesioner!");
                return;
            }

            // Tetapkan alur awal (sebelum posisi dipilih)
            this.flow = ["welcome", "email", "posisi"];
            this.totalSteps = this.flow.length; // Akan diupdate nanti

            this.bindEvents();
            this.goToStep(0);
        },

        /**
         * Memuat partials header dan footer.
         */
        loadPartials: function() {
            // Cek path untuk memuat partials.
            const isAdminPage = window.location.pathname.includes("/admin/");
            const pathPrefix = isAdminPage ? "../partials/" : "partials/";

            $("#header").load(pathPrefix + "header.html");
            $("#footer").load(pathPrefix + "footer.html");
            
            // Khusus untuk admin (karena ID-nya berbeda)
            $("#header-admin").load(pathPrefix + "header.html");
            $("#footer-admin").load(pathPrefix + "footer.html");
        },

        /**
         * Inisialisasi data di localStorage jika belum ada.
         */
        initializeAppStorage: async function() {
            // 1. Inisialisasi Pertanyaan (dari seed.js)
            if (!PSV.storage.get("psv_questions")) {
                if (PSV.SEED_QUESTIONS) {
                    PSV.storage.set("psv_questions", PSV.SEED_QUESTIONS);
                    console.log("localStorage 'psv_questions' diinisialisasi dari seed.");
                } else {
                    console.error("SEED_QUESTIONS tidak ditemukan! Pastikan seed.js dimuat.");
                }
            }

            // 2. Inisialisasi Respons
            if (!PSV.storage.get("psv_responses")) {
                PSV.storage.set("psv_responses", []);
            }

            // 3. Inisialisasi Admin (termasuk hash password)
            if (!PSV.storage.get("psv_admin")) {
                try {
                    const defaultPass = "admin123";
                    const hash = await PSV.helpers.sha256Hex(defaultPass);
                    const adminData = {
                        passwordHash: hash,
                        lastLoginAt: null
                    };
                    PSV.storage.set("psv_admin", adminData);
                    console.log("localStorage 'psv_admin' diinisialisasi dengan password default.");
                } catch (e) {
                    console.error("Gagal meng-hash password admin untuk inisialisasi.");
                }
            }
        },

        /**
         * Mendaftarkan event listener untuk tombol dan input.
         */
        bindEvents: function() {
            $("#next-btn").on("click", this.handleNext.bind(this));
            $("#prev-btn").on("click", this.handlePrev.bind(this));
            $("#submit-btn").on("click", this.handleSubmit.bind(this));

            // Validasi email saat input
            $("#email").on("input", this.validateStep.bind(this));

            // Tampilkan form demografis yang sesuai saat posisi dipilih
            $('input[name="posisi"]').on("change", function() {
                const posisi = $(this).val();
                PSV.wizard.wizardData.posisi = posisi;
                $('.demografis-form').addClass('d-none');
                $(`#demografis-${posisi}`).removeClass('d-none');
                PSV.wizard.validateStep(); // Validasi ulang untuk tombol next
            });

            // Validasi form demografis saat input
            $('#step-demografis').on('input change', 'input, select', this.validateStep.bind(this));

            // Validasi kuesioner saat radio button dipilih
            $('#kuesioner-content').on('change', 'input[type="radio"]', this.validateStep.bind(this));
        },

        /**
         * Menetapkan alur (flow) wizard berdasarkan pilihan posisi.
         */
        setupFlow: function() {
            const posisi = this.wizardData.posisi;
            let baseFlow = ["welcome", "email", "posisi", "demografis", "instruksi"];
            
            if (posisi === "teknisi" || posisi === "manajer") {
                this.flow = [...baseFlow, "section1", "section2", "section3", "section4", "review", "terima-kasih"];
            } else if (posisi === "eksternal") {
                this.flow = [...baseFlow, "section5", "review", "terima-kasih"];
            } else {
                // Fallback (sebelum posisi dipilih)
                this.flow = ["welcome", "email", "posisi"];
            }
            this.totalSteps = this.flow.length;
        },

        /**
         * Pindah ke langkah wizard tertentu.
         * @param {number} stepIndex - Indeks langkah (dari array this.flow).
         */
        goToStep: function(stepIndex) {
            this.currentStepIndex = stepIndex;
            const stepId = this.flow[stepIndex];

            // Sembunyikan semua step
            $('.wizard-step').addClass('d-none');

            // Perbaikan bug 'd-none'
            if (stepId.startsWith('section')) {
                // Jika ini adalah step kuesioner (section1, section2, etc.)
                // tampilkan wrapper umumnya
                $('#step-kuesioner').removeClass('d-none');
            } else {
                // Tampilkan step biasa (welcome, email, review, etc.)
                $(`#step-${stepId}`).removeClass('d-none');
            }

            // Render konten dinamis jika perlu
            if (stepId.startsWith('section')) {
                this.renderStepContent(stepId);
            } else if (stepId === 'review') {
                this.renderStepContent(stepId);
            }

            this.updateProgressBar();
            this.updateNavButtons();
            this.validateStep(); // Validasi step saat ini untuk tombol next

            // *** INI ADALAH PERUBAHANNYA (AUTO-SCROLL) ***
            // Kita hanya scroll jika step BUKAN 'welcome' (step 0)
            if (stepIndex > 0) {
                // Target scroll adalah 'main container' dari wizard
                const $target = $(".card").first();
                
                if ($target.length) {
                    $('html, body').animate({
                        // Scroll ke 60px DI ATAS kartu wizard (agar ada jarak dari header)
                        scrollTop: $target.offset().top - 60 
                    }, 400); // 400ms durasi scroll
                }
            }
            // *** AKHIR PERUBAHAN ***
        },

        /**
         * Logika saat tombol "Berikutnya" diklik.
         */
        handleNext: function() {
            const currentStepId = this.flow[this.currentStepIndex];

            // Simpan data dari step saat ini (jika perlu)
            if (currentStepId === "email") {
                this.wizardData.email = $("#email").val();
            } else if (currentStepId === "posisi") {
                // Posisi sudah disimpan via 'change' event
                // Setup flow baru berdasarkan posisi
                this.setupFlow();
            } else if (currentStepId === "demografis") {
                this.collectDemografis();
            } else if (currentStepId.startsWith("section")) {
                this.collectAnswers(currentStepId);
            }

            if (this.currentStepIndex < this.totalSteps - 1) {
                this.goToStep(this.currentStepIndex + 1);
            }
        },

        /**
         * Logika saat tombol "Sebelumnya" diklik.
         */
        handlePrev: function() {
            if (this.currentStepIndex > 0) {
                // Jika kembali dari demografis, reset flow
                if (this.flow[this.currentStepIndex] === 'demografis') {
                    this.setupFlow(); // Reset flow ke state awal
                }
                this.goToStep(this.currentStepIndex - 1);
            }
        },

        /**
         * Logika saat tombol "Submit" diklik.
         */
        handleSubmit: function() {
            this.wizardData.id = PSV.helpers.generateId();
            this.wizardData.submittedAt = PSV.helpers.getISOTime();

            // Ambil data respons yang ada
            const responses = PSV.storage.get("psv_responses") || [];
            // Tambahkan respons baru
            responses.push(this.wizardData);
            // Simpan kembali ke localStorage
            PSV.storage.set("psv_responses", responses);

            // Tampilkan ID di halaman terima kasih
            $("#submission-id").text(this.wizardData.id);

            // Pindah ke langkah terakhir (terima kasih)
            this.goToStep(this.flow.indexOf("terima-kasih"));
        },

        /**
         * Mengumpulkan data demografis berdasarkan posisi.
         */
        collectDemografis: function() {
            const posisi = this.wizardData.posisi;
            const $form = $(`#demografis-${posisi}`);
            let data = {};

            if (posisi === "teknisi" || posisi === "manajer") {
                data.umur = $form.find('input[id*="umur"]').val();
                data.jenisKelamin = $form.find('select[id*="jk"]').val();
                data.unitDivisi = $form.find('input[id*="unit"]').val();
                data.lamaBekerja = $form.find('input[id*="lama-bekerja"]').val();
            } else if (posisi === "eksternal") {
                data.organisasi = $form.find('input[id*="organisasi"]').val();
                data.jabatan = $form.find('input[id*="jabatan"]').val();
                data.durasiKerjasama = $form.find('input[id*="durasi-kerjasama"]').val();
            }
            this.wizardData.demografis = data;
        },

        /**
         * Mengumpulkan jawaban dari section kuesioner saat ini.
         * @param {string} sectionKey - Kunci section (mis: "section1", "section2").
         */
        collectAnswers: function(sectionKey) {
            const $container = $(`#kuesioner-${sectionKey}`);
            let answers = [];
            
            $container.find('.likert-scale').each(function() {
                const $q = $(this);
                const qid = $q.data('qid');
                const value = $q.find(`input[name="q-${qid}"]:checked`).val();
                
                if (qid && value) {
                    answers.push({ qid: qid, value: parseInt(value, 10) });
                }
            });
            
            this.wizardData.answers[sectionKey] = answers;
        },

        /**
         * Validasi step saat ini dan atur status tombol Next/Submit.
         */
        validateStep: function() {
            const stepId = this.flow[this.currentStepIndex];
            let isValid = false;

            switch (stepId) {
                case "welcome":
                case "instruksi":
                    isValid = true; // Step statis
                    break;
                case "email":
                    const email = $("#email").val();
                    isValid = PSV.validate.email(email);
                    $("#email").toggleClass('is-invalid', !isValid && email.length > 0);
                    break;
                case "posisi":
                    isValid = (this.wizardData.posisi !== null);
                    break;
                case "demografis":
                    isValid = PSV.validate.formGroup(`#demografis-${this.wizardData.posisi}`);
                    break;
                case "review":
                    isValid = true; // Selalu bisa submit dari review
                    break;
                case "terima-kasih":
                    isValid = false; // Tidak ada 'next'
                    break;
                default:
                    // Asumsi step kuesioner (sectionX)
                    if (stepId.startsWith("section")) {
                        isValid = this.checkQuestionCompletion(stepId);
                    }
                    break;
            }

            $("#next-btn").prop("disabled", !isValid);
        },

        /**
         * Memeriksa apakah semua pertanyaan di section telah dijawab.
         * @param {string} sectionKey - Kunci section (mis: "section1").
         * @returns {boolean} True jika semua terjawab.
         */
        checkQuestionCompletion: function(sectionKey) {
            const $container = $(`#kuesioner-${sectionKey}`);
            const totalQuestions = $container.find('.likert-scale').length;
            const answeredQuestions = $container.find('input[type="radio"]:checked').length;
            return totalQuestions === answeredQuestions;
        },

        /**
         * Render konten dinamis (pertanyaan atau review).
         * @param {string} stepId - ID step (mis: "section1" atau "review").
         */
        renderStepContent: function(stepId) {
            if (stepId === "review") {
                this.renderReview();
            } else if (stepId.startsWith("section")) {
                
                // Tambahkan Judul dan Deskripsi
                const $container = $("#kuesioner-content");
                $container.empty(); // Kosongkan dulu

                const description = this.sectionDescriptions[stepId];
                if (description) {
                    $container.append(
                        `<h4 class="text-capitalize mb-1">${stepId.replace('section', 'Section ')}</h4>` +
                        `<p class="text-muted fst-italic">${description}</p>` +
                        `<hr class="mb-4">`
                    );
                }

                const variant = (stepId === "section1") ? this.wizardData.posisi : null;
                // Render pertanyaan (sekarang di-append ke container)
                this.renderQuestions("#kuesioner-content", stepId, variant);
            }
        },

        /**
         * Render pertanyaan kuesioner ke dalam kontainer.
         * @param {string} containerSelector - Selector jQuery untuk kontainer.
         * @param {string} sectionKey - Kunci section (mis: "section1").
         * @param {string|null} variant - Varian (mis: "teknisi", "manajer") atau null.
         */
        renderQuestions: function(containerSelector, sectionKey, variant = null) {
            const $container = $(containerSelector);
            // $container.empty(); // <-- Dihapus. (Emptying terjadi di renderStepContent)

            let questionsList = [];
            if (variant) {
                questionsList = this.questions[sectionKey][variant] || [];
            } else {
                questionsList = this.questions[sectionKey] || [];
            }

            if (questionsList.length === 0) {
                $container.append("<p>Tidak ada pertanyaan untuk section ini.</p>");
                return;
            }

            // Buat wrapper unik untuk section ini
            const $sectionWrapper = $(`<div id="kuesioner-${sectionKey}"></div>`);
            
            questionsList.forEach(q => {
                const $questionEl = this.createLikertScale(q);
                $sectionWrapper.append($questionEl);
            });

            $container.append($sectionWrapper);
        },

        /**
         * Helper untuk membuat satu baris skala Likert.
         * @param {object} question - Objek pertanyaan {id, text}.
         * @returns {jQuery} Elemen jQuery untuk skala Likert.
         */
        createLikertScale: function(question) {
            const qid = question.id;
            
            const $scale = $(`
                <div class="likert-scale" data-qid="${qid}" role="radiogroup" aria-labelledby="label-${qid}">
                    <div class="likert-text" id="label-${qid}">${question.text}</div>
                    <div class="likert-radio-group"></div>
                </div>
            `);

            const $radioGroup = $scale.find('.likert-radio-group');
            const scaleLabels = PSV.LIKERT_SCALE_7 || [];

            scaleLabels.forEach(opt => {
                const inputId = `q-${qid}-${opt.value}`;
                
                // Label sekarang menggunakan `opt.label` (teks)
                const $radioItem = $(`
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="q-${qid}" id="${inputId}" value="${opt.value}" required>
                        <label class="form-check-label" for="${inputId}">${opt.label}</label>
                    </div>
                `);
                
                // Cek apakah jawaban sudah ada (jika user klik 'prev')
                if (this.wizardData.answers[qid] && this.wizardData.answers[qid] === opt.value) {
                   $radioItem.find('input').prop('checked', true);
                }

                $radioGroup.append($radioItem);
            });

            return $scale;
        },

        /**
         * Render halaman review jawaban.
         */
        renderReview: function() {
            const $container = $("#review-content");
            $container.empty();

            // 1. Demografis
            $container.append("<h5>Data Responden</h5>");
            let demoHtml = `<dl class="row">
                <dt class="col-sm-3">Email</dt><dd class="col-sm-9">${this.wizardData.email}</dd>
                <dt class="col-sm-3">Posisi</dt><dd class="col-sm-9 text-capitalize">${this.wizardData.posisi}</dd>
            </dl>`;
            $container.append(demoHtml);

            // 2. Jawaban (Iterasi melalui jawaban yang tersimpan)
            for (const sectionKey in this.wizardData.answers) {
                const answers = this.wizardData.answers[sectionKey];
                if (answers && answers.length > 0) {
                    $container.append(`<h5>Jawaban ${sectionKey.replace('section', 'Section ')}</h5>`);
                    let answersHtml = '<dl class="row">';
                    
                    answers.forEach(answer => {
                        // Cari teks pertanyaan berdasarkan QID
                        let qText = "Teks pertanyaan tidak ditemukan";
                        let q = null;
                        
                        if (sectionKey === 'section1') {
                            const variant = this.wizardData.posisi; // teknisi/manajer
                            q = this.questions.section1[variant].find(q => q.id === answer.qid);
                        } else if (this.questions[sectionKey]) {
                            q = this.questions[sectionKey].find(q => q.id === answer.qid);
                        }

                        if (q) qText = q.text;

                        // Dapatkan skor numerik
                        const score = answer.value;
                        
                        // Cari label teks yang sesuai
                        let scoreLabel = "";
                        const scaleEntry = (PSV.LIKERT_SCALE_7 || []).find(item => item.value === score);
                        if (scaleEntry) {
                            scoreLabel = scaleEntry.label.toLowerCase();
                        }

                        // Render HTML baru dengan format "Skor (Label Teks)"
                        answersHtml += `
                            <dt class="col-sm-9 small">${qText}</dt>
                            <dd class="col-sm-3 text-end"><b>${score} (${scoreLabel})</b></dd>
                        `;
                    });
                    
                    answersHtml += '</dl>';
                    $container.append(answersHtml);
                }
            }
        },

        /**
         * Update progress bar.
         */
        updateProgressBar: function() {
            // Hitung progres. "terima-kasih" adalah 100%, "review" adalah step sebelumnya.
            let progress = 0;
            const reviewIndex = this.flow.indexOf('review');
            
            if (this.currentStepIndex >= reviewIndex && reviewIndex !== -1) {
                 progress = 100;
            } else if (reviewIndex !== -1) {
                 // Progres berbasis jumlah langkah sebelum review
                progress = Math.round((this.currentStepIndex / reviewIndex) * 100);
            } else {
                // Fallback jika flow belum terdefinisi penuh
                progress = Math.round((this.currentStepIndex / (this.totalSteps - 1)) * 100);
            }
            
            // Jangan biarkan progress 100% sebelum 'terima-kasih'
            if (this.flow[this.currentStepIndex] === 'review') progress = 95;
            if (this.flow[this.currentStepIndex] === 'terima-kasih') progress = 100;


            const $progress = $("#wizard-progress");
            $progress.css("width", `${progress}%`).text(`${progress}%`).attr("aria-valuenow", progress);
        },

        /**
         * Update visibilitas tombol navigasi (Prev, Next, Submit).
         */
        updateNavButtons: function() {
            const stepId = this.flow[this.currentStepIndex];

            // Tombol Prev
            $("#prev-btn").toggle(this.currentStepIndex > 0 && stepId !== "terima-kasih");

            // Tombol Next
            $("#next-btn").toggle(stepId !== "review" && stepId !== "terima-kasih");

            // Tombol Submit
            $("#submit-btn").toggle(stepId === "review");

            // Sembunyikan semua navigasi di halaman "terima kasih"
            if (stepId === "terima-kasih") {
                $("#wizard-nav").hide();
            } else {
                $("#wizard-nav").show();
            }
        }
    };

    // --- 3. DOM Ready ---
    $(document).ready(function() {
        // Perbaikan bug inisialisasi
        // Hanya jalankan wizard jika kita *pasti* berada di halaman wizard.
        // Kita periksa keberadaan #wizard-form.
        if ($("#wizard-form").length > 0) {
            // Ini adalah halaman index.html, jalankan wizard.
            PSV.wizard.init();
        } else {
            // Ini mungkin halaman admin, muat partials admin
            // (admin.js akan menangani logikanya sendiri)
            PSV.wizard.loadPartials();
            
            // Pastikan storage di-init juga di halaman admin
            PSV.wizard.initializeAppStorage();
        }
    });

})(jQuery, window.PSV);