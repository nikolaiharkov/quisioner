/*
 * /assets/js/admin.js
 * Logika untuk panel admin (login.html dan admin.html).
 * Menggunakan jQuery dan mengandalkan app.js untuk helpers.
 */

// Pastikan namespace global PSV ada
window.PSV = window.PSV || {};

(function($, PSV) {
    "use strict";

    // Pastikan helpers dari app.js ada
    if (!PSV.storage || !PSV.helpers || !PSV.SEED_QUESTIONS) {
        console.error("PSV.storage atau PSV.helpers tidak ditemukan. Pastikan app.js dan seed.js dimuat sebelum admin.js.");
        return;
    }

    PSV.admin = {

        // Menyimpan instance modal Bootstrap
        crudModal: null,

        /**
         * Inisialisasi utama untuk logika admin.
         * Memeriksa halaman mana yang aktif (login atau dashboard).
         */
        init: function() {
            const path = window.location.pathname;

            if (path.endsWith("login.html")) {
                this.initLogin();
            } else if (path.endsWith("admin.html")) {
                this.initDashboard();
            }
        },

        /**
         * Memuat partials header dan footer admin.
         * Path-nya relatif dari folder /admin/
         */
        loadPartials: function() {
            // Path relatif dari /admin/
            $("#header-admin").load("../partials/header.html");
            $("#footer-admin").load("../partials/footer.html");
        },

        // --- 1. Logika Login & Otentikasi ---

        /**
         * Inisialisasi halaman login.html.
         */
        initLogin: function() {
            this.loadPartials();
            $("#login-form").on("submit", this.handleLogin.bind(this));
        },

        /**
         * Menangani submit form login.
         */
        handleLogin: async function(e) {
            e.preventDefault();
            const $btn = $("#login-btn");
            const $spinner = $btn.find(".spinner-border");
            const $alert = $("#login-alert");
            const password = $("#password").val();

            $btn.prop("disabled", true);
            $spinner.removeClass("d-none");
            $alert.addClass("d-none");

            if (!password) {
                $alert.text("Password tidak boleh kosong.").removeClass("d-none");
                $btn.prop("disabled", false);
                $spinner.addClass("d-none");
                return;
            }

            try {
                const adminData = PSV.storage.get("psv_admin");
                if (!adminData || !adminData.passwordHash) {
                    $alert.text("Data admin tidak terkonfigurasi. Coba muat halaman utama.").removeClass("d-none");
                    return;
                }

                const inputHash = await PSV.helpers.sha256Hex(password);
                
                if (inputHash === adminData.passwordHash) {
                    // Berhasil Login
                    adminData.lastLoginAt = PSV.helpers.getISOTime();
                    PSV.storage.set("psv_admin", adminData);
                    
                    // TODO: Ganti path ini jika nanti pindah ke root PHP
                    window.location.href = "admin.html";
                } else {
                    // Gagal Login
                    $alert.text("Password salah.").removeClass("d-none");
                    $btn.prop("disabled", false);
                    $spinner.addClass("d-none");
                }
            } catch (error) {
                console.error("Login error:", error);
                $alert.text("Terjadi kesalahan sistem saat login.").removeClass("d-none");
                $btn.prop("disabled", false);
                $spinner.addClass("d-none");
            }
        },

        /**
         * Memeriksa apakah admin sudah login (session simulasi).
         * @returns {boolean} True jika sudah login.
         */
        checkAuth: function() {
            const adminData = PSV.storage.get("psv_admin");
            // "Session" kita valid jika lastLoginAt diset (tidak null)
            return adminData && adminData.lastLoginAt !== null;
        },

        /**
         * Menangani logout.
         */
        handleLogout: function() {
            const adminData = PSV.storage.get("psv_admin");
            if (adminData) {
                adminData.lastLoginAt = null;
                PSV.storage.set("psv_admin", adminData);
            }
            // TODO: Ganti path ini jika nanti pindah ke root PHP
            window.location.href = "login.html";
        },

        // --- 2. Logika Dashboard ---

        /**
         * Inisialisasi halaman admin.html (Dasbor).
         */
        initDashboard: function() {
            // Cek otentikasi dulu
            if (!this.checkAuth()) {
                // TODO: Ganti path ini jika nanti pindah ke root PHP
                window.location.href = "login.html";
                return;
            }

            this.loadPartials();

            // Inisialisasi Modal
            this.crudModal = new bootstrap.Modal(document.getElementById('modal-crud-question'));

            // Bind Event
            this.bindDashboardEvents();
            
            // Muat Konten Awal
            this.loadDashboardStats();
            this.loadRespondenTable("semua");
            this.loadAllCrudTables();
        },

        /**
         * Mendaftarkan semua event listener untuk dasbor.
         */
        bindDashboardEvents: function() {
            // Navigasi Sidebar (SPA-style)
            $("#admin-sidebar").on("click", ".nav-link", this.handleAdminNav.bind(this));

            // Logout
            $("#logout-btn").on("click", this.handleLogout.bind(this));

            // Filter Tabel Responden
            $("#filter-posisi").on("change", (e) => {
                this.loadRespondenTable($(e.currentTarget).val());
            });

            // Logika Download CSV
            $("#csv-posisi").on("change", this.populateCsvSections.bind(this));
            $("#csv-download-btn").on("click", this.handleCsvDownload.bind(this));

            // Logika CRUD
            $("#reset-seed-btn").on("click", this.resetToSeed.bind(this));
            $("#content-crud").on("click", ".crud-add-btn", this.openCrudModal.bind(this));
            $("#content-crud").on("click", ".crud-edit-btn", this.openCrudModal.bind(this));
            $("#content-crud").on("click", ".crud-delete-btn", this.deleteQuestion.bind(this));
            $("#crud-save-btn").on("click", this.saveQuestion.bind(this));
        },

        /**
         * Menangani navigasi sidebar untuk menampilkan section yang relevan.
         */
        handleAdminNav: function(e) {
            e.preventDefault();
            const $link = $(e.currentTarget);
            const targetId = $link.attr("href"); // e.g., "#content-ringkasan"

            if (targetId && targetId.startsWith("#content-")) {
                // Sembunyikan semua section
                $(".admin-section").hide();
                // Tampilkan section yang dituju
                $(targetId).show();

                // Atur status 'active'
                $("#admin-sidebar .nav-link").removeClass("active");
                $link.addClass("active");
            }
        },

        /**
         * Memuat dan menampilkan statistik ringkasan.
         */
        loadDashboardStats: function() {
            const responses = PSV.storage.get("psv_responses") || [];
            
            let counts = {
                total: responses.length,
                teknisi: 0,
                manajer: 0,
                eksternal: 0
            };

            responses.forEach(resp => {
                if (resp.posisi === "teknisi") counts.teknisi++;
                else if (resp.posisi === "manajer") counts.manajer++;
                else if (resp.posisi === "eksternal") counts.eksternal++;
            });

            $("#stats-total").text(counts.total);
            $("#stats-teknisi").text(counts.teknisi);
            $("#stats-manajer").text(counts.manajer);
            $("#stats-eksternal").text(counts.eksternal);
        },

        /**
         * Memuat data ke tabel responden, dengan filter opsional.
         * @param {string} filterPosisi - "semua", "teknisi", "manajer", "eksternal".
         */
        loadRespondenTable: function(filterPosisi = "semua") {
            const $tbody = $("#responden-table-body");
            const responses = PSV.storage.get("psv_responses") || [];
            
            $tbody.empty();

            const filteredResponses = responses.filter(resp => {
                return filterPosisi === "semua" || resp.posisi === filterPosisi;
            });

            if (filteredResponses.length === 0) {
                $tbody.html('<tr><td colspan="5" class="text-center">Tidak ada data responden ditemukan.</td></tr>');
                return;
            }

            filteredResponses.forEach(resp => {
                // Hitung total jawaban
                let totalAnswers = 0;
                if (resp.answers) {
                    Object.keys(resp.answers).forEach(key => {
                        totalAnswers += resp.answers[key].length;
                    });
                }
                
                const submittedDate = new Date(resp.submittedAt).toLocaleString('id-ID');

                const rowHtml = `
                    <tr data-posisi="${resp.posisi}">
                        <td><code>${resp.id}</code></td>
                        <td>${submittedDate}</td>
                        <td>${resp.email}</td>
                        <td class="text-capitalize">${resp.posisi}</td>
                        <td>${totalAnswers}</td>
                    </tr>
                `;
                $tbody.append(rowHtml);
            });
        },

        // --- 3. Logika Download CSV ---

        /**
         * Mengisi dropdown section berdasarkan posisi yang dipilih.
         */
        populateCsvSections: function() {
            const $sectionSelect = $("#csv-section");
            const posisi = $("#csv-posisi").val();
            
            $sectionSelect.empty().prop("disabled", false);
            $("#csv-download-btn").prop("disabled", true);

            let sections = [];
            if (posisi === "teknisi" || posisi === "manajer") {
                sections = ["section1", "section2", "section3", "section4"];
            } else if (posisi === "eksternal") {
                sections = ["section5"];
            }

            if (sections.length === 0) {
                 $sectionSelect.append('<option value="" disabled selected>-- Pilih Posisi Dulu --</option>');
                 return;
            }
            
            $sectionSelect.append('<option value="" disabled selected>-- Pilih Section --</option>');
            sections.forEach(s => {
                $sectionSelect.append(`<option value="${s}">${s.replace('section', 'Section ')}</option>`);
            });

            $sectionSelect.off("change").on("change", () => {
                 $("#csv-download-btn").prop("disabled", false);
            });
        },

        /**
         * Menangani klik tombol download CSV.
         */
        handleCsvDownload: function() {
            const posisi = $("#csv-posisi").val();
            const sectionKey = $("#csv-section").val();

            if (!posisi || !sectionKey) {
                alert("Silakan pilih Posisi dan Section terlebih dahulu.");
                return;
            }
            
            const responses = PSV.storage.get("psv_responses") || [];
            
            // 1. Filter respons berdasarkan posisi
            const filteredResponses = responses.filter(r => r.posisi === posisi);

            // 2. Siapkan header CSV
            let csvContent = "email,posisi,section,qid,score,submittedAt\n";

            // 3. Iterasi respons dan build CSV string
            filteredResponses.forEach(resp => {
                const email = resp.email;
                const submittedAt = resp.submittedAt;
                
                // Ambil jawaban hanya untuk section yang diminta
                const answers = resp.answers[sectionKey];
                
                if (answers && answers.length > 0) {
                    answers.forEach(ans => {
                        // Pastikan data bersih (tanpa koma atau newline)
                        const cleanEmail = `"${email.replace(/"/g, '""')}"`;
                        
                        csvContent += `${cleanEmail},${posisi},${sectionKey},${ans.qid},${ans.value},${submittedAt}\n`;
                    });
                }
            });

            // 4. Buat nama file
            const filename = `psv-${posisi}-section-${sectionKey.replace('section', '')}.csv`;

            // 5. Trigger download
            this.downloadBlob(csvContent, filename, 'text/csv;charset=utf-8;');
        },

        /**
         * Helper untuk men-trigger download file (Blob).
         */
        downloadBlob: function(content, filename, contentType) {
            const blob = new Blob([content], { type: contentType });
            const url = URL.createObjectURL(blob);

            const $a = $("<a>")
                .attr("href", url)
                .attr("download", filename)
                .hide()
                .appendTo("body");
                
            $a[0].click();
            
            $a.remove();
            URL.revokeObjectURL(url);
        },

        // --- 4. Logika CRUD Kuisioner ---

        /**
         * Memuat semua tabel pertanyaan di tab "Kelola Kuisioner".
         */
        loadAllCrudTables: function() {
            this.renderCrudTable("section1", "teknisi");
            this.renderCrudTable("section1", "manajer");
            this.renderCrudTable("section2", null);
            this.renderCrudTable("section3", null);
            this.renderCrudTable("section4", null);
            this.renderCrudTable("section5", null);
        },

        /**
         * Merender satu tabel CRUD untuk section/variant tertentu.
         * @param {string} sectionKey - e.g., "section1"
         * @param {string|null} variant - e.g., "teknisi", "manajer", atau null
         */
        renderCrudTable: function(sectionKey, variant) {
            let containerId = `#crud-table-s${sectionKey.match(/\d+/)[0]}`;
            if (variant) {
                containerId += `-${variant}`;
            }

            const $container = $(containerId);
            $container.empty();
            const questions = this.getQuestionsList(sectionKey, variant);

            if (!questions) {
                $container.html('<p class="text-danger">Gagal memuat data pertanyaan.</p>');
                return;
            }
            
            if (questions.length === 0) {
                $container.html('<p class="text-muted">Belum ada pertanyaan.</p>');
                return;
            }

            const $table = $('<table class="table table-sm table-hover"></table>');
            $table.append('<thead><tr><th>ID</th><th>Teks Pertanyaan</th><th class="text-end">Aksi</th></tr></thead>');
            const $tbody = $('<tbody></tbody>');
            
            questions.forEach(q => {
                const $row = $(`
                    <tr>
                        <td><code>${q.id}</code></td>
                        <td>${q.text}</td>
                        <td class="text-end">
                            <button class="btn btn-warning btn-sm crud-edit-btn" 
                                    data-id="${q.id}" 
                                    data-text="${$("<div/>").text(q.text).html()}" 
                                    data-section="${sectionKey}" 
                                    data-variant="${variant || ''}">
                                Edit
                            </button>
                            <button class="btn btn-danger btn-sm crud-delete-btn" 
                                    data-id="${q.id}" 
                                    data-section="${sectionKey}" 
                                    data-variant="${variant || ''}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                `);
                $tbody.append($row);
            });

            $table.append($tbody);
            $container.append($table);
        },

        /**
         * Helper untuk mengambil array pertanyaan dari data storage.
         */
        getQuestionsList: function(sectionKey, variant) {
            const allQuestions = PSV.storage.get("psv_questions");
            if (!allQuestions) return [];
            
            if (variant) {
                return allQuestions[sectionKey] ? allQuestions[sectionKey][variant] || [] : [];
            } else {
                return allQuestions[sectionKey] || [];
            }
        },

        /**
         * Membuka modal CRUD untuk 'add' atau 'edit'.
         */
        openCrudModal: function(e) {
            const $btn = $(e.currentTarget);
            const data = $btn.data();
            const $form = $("#crud-form");
            $form.trigger("reset");

            $("#crud-q-id").val(data.id || "");
            $("#crud-q-text").val(data.text || "");
            $("#crud-q-section").val(data.section);
            $("#crud-q-variant").val(data.variant === 'null' ? '' : data.variant);

            if (data.id) {
                // Mode Edit
                $("#modal-crud-title").text("Edit Pertanyaan");
            } else {
                // Mode Add
                $("#modal-crud-title").text("Tambah Pertanyaan Baru");
            }
            
            this.crudModal.show();
        },

        /**
         * Menyimpan perubahan dari modal (add/edit).
         */
        saveQuestion: function() {
            const id = $("#crud-q-id").val();
            const text = $("#crud-q-text").val().trim();
            const sectionKey = $("#crud-q-section").val();
            const variant = $("#crud-q-variant").val() || null; // "" -> null

            if (!text || !sectionKey) {
                alert("Teks pertanyaan tidak boleh kosong.");
                return;
            }

            const allQuestions = PSV.storage.get("psv_questions");
            let qList;
            
            if (variant) {
                qList = allQuestions[sectionKey][variant];
            } else {
                qList = allQuestions[sectionKey];
            }

            if (id) {
                // Mode Edit
                const q = qList.find(item => item.id === id);
                if (q) {
                    q.text = text;
                }
            } else {
                // Mode Add
                const newId = this.generateQuestionId(qList, sectionKey, variant);
                qList.push({ id: newId, text: text });
            }

            // Simpan kembali ke localStorage
            PSV.storage.set("psv_questions", allQuestions);
            
            // Tutup modal dan refresh tabel
            this.crudModal.hide();
            this.renderCrudTable(sectionKey, variant);
        },

        /**
         * Menghapus pertanyaan.
         */
        deleteQuestion: function(e) {
            const $btn = $(e.currentTarget);
            const data = $btn.data();
            const qid = data.id;
            const sectionKey = data.section;
            const variant = data.variant || null;

            if (!confirm(`Apakah Anda yakin ingin menghapus pertanyaan dengan ID: ${qid}?`)) {
                return;
            }

            const allQuestions = PSV.storage.get("psv_questions");
            
            if (variant) {
                let qList = allQuestions[sectionKey][variant];
                const index = qList.findIndex(item => item.id === qid);
                if (index > -1) {
                    qList.splice(index, 1);
                }
            } else {
                let qList = allQuestions[sectionKey];
                const index = qList.findIndex(item => item.id === qid);
                if (index > -1) {
                    qList.splice(index, 1);
                }
            }

            PSV.storage.set("psv_questions", allQuestions);
            this.renderCrudTable(sectionKey, variant);
        },

        /**
         * Mengembalikan data pertanyaan ke seed default.
         */
        resetToSeed: function() {
            if (!confirm("YAKIN? Ini akan menghapus semua perubahan pada daftar pertanyaan dan mengembalikan ke data awal (seed).")) {
                return;
            }
            
            PSV.storage.set("psv_questions", PSV.SEED_QUESTIONS);
            this.loadAllCrudTables();
            alert("Data kuisioner telah di-reset ke seed default.");
        },

        /**
         * Helper untuk membuat ID pertanyaan baru (e.g., S1T-4).
         */
        generateQuestionId: function(qList, sectionKey, variant) {
            let prefix = `S${sectionKey.match(/\d+/)[0]}`;
            if (variant === 'teknisi') prefix += 'T';
            else if (variant === 'manajer') prefix += 'M';
            else if (variant === 'eksternal') prefix += 'E'; // (Meskipun S5 pakai 'E')
            
            if (sectionKey === 'section5') prefix = 'S5'; // Override untuk S5

            let maxNum = 0;
            qList.forEach(q => {
                try {
                    const num = parseInt(q.id.split('-')[1], 10);
                    if (num > maxNum) maxNum = num;
                } catch(e) {}
            });
            
            return `${prefix}-${maxNum + 1}`;
        }
    };

    // --- Inisialisasi Admin saat DOM Ready ---
    $(document).ready(function() {
        PSV.admin.init();
    });

})(jQuery, window.PSV);