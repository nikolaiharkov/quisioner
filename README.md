````markdown
# Proyek Kuesioner Public Service Value (PSV)

Aplikasi web ini adalah sistem kuesioner **multi-langkah (wizard)** yang dibuat menggunakan **PHP natif**, **MySQL**, dan **Bootstrap 5**. Aplikasi dirancang untuk mengumpulkan data dari tiga jenis responden (Teknisi, Manajer, dan Eksternal) dan dilengkapi panel admin untuk mengelola pertanyaan serta mengekspor hasil.

---

## Daftar Isi
- [Fitur Utama](#fitur-utama)
  - [1. Alur Kuesioner (Wizard)](#1-alur-kuesioner-wizard)
  - [2. Panel Admin (/admin/)](#2-panel-admin-admin)
  - [3. Struktur Database (migration.sql)](#3-struktur-database-migrationsql)
  - [4. Keamanan](#4-keamanan)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Cara Instalasi](#cara-instalasi)
- [Catatan Tambahan](#catatan-tambahan)

---

## Fitur Utama

### 1. Alur Kuesioner (Wizard)
Aplikasi memandu pengguna melalui serangkaian langkah yang terdefinisi dengan baik. Logika utama dikendalikan oleh `index.php` yang berfungsi sebagai **controller/router** utama, menggunakan `$_SESSION['wizard_step']` untuk melacak progres pengguna.

**Alur standar untuk responden:**

1. **Selamat Datang** (`views/welcome.php`): Halaman pengantar.
2. **Nama Lengkap** (`views/fullname.php`): Mengumpulkan identitas responden.
3. **Pemilihan Peran** (`views/role.php`): Langkah krusial untuk menentukan alur. Responden memilih antara:
   - **Teknisi (Internal)**
   - **Manajer (Internal)**
   - **Eksternal (Pemangku Kepentingan)**
4. **Demografi** (`views/demographics.php`): Memuat formulir demografis **berdasarkan peran** yang dipilih pada langkah 3 (`demographics_teknisi_manajer.php` atau `demographics_eksternal.php`).
5. **Instruksi** (`views/instructions.php`): Penjelasan skala **Likert 1–7**.
6. **Pengisian Kuesioner** (`views/section.php`): Halaman dinamis yang memuat pertanyaan dari database (`questions`) berdasarkan:
   - **Section (Bagian):** Kuesioner dibagi ke beberapa bagian (misal: `S1`, `S2`, `S3`, `S5`).
   - **Peran (Role):** Pertanyaan ditampilkan selektif sesuai `target_role` di database (misal: *teknisi* hanya melihat pertanyaan `T1–T5`, *manajer* hanya `M1–M5`, dan *eksternal* hanya `S5`).
7. **Selesai** (`views/done.php`): Halaman terima kasih dan sesi pengguna dibersihkan.

---

### 2. Panel Admin (`/admin/`)
Area admin terproteksi untuk mengelola kuesioner dan data.

- **Login Admin** (`admin/login.php`): Halaman login aman yang memverifikasi akun admin di tabel `admins`.
- **Dashboard** (`admin/index.php`):
  - Statistik ringkasan (total responden, jumlah per peran).
  - Daftar semua responden dengan status (**Completed** / **In Progress**).
  - Fitur filter dan pencarian responden.
- **Manajemen Pertanyaan** (`admin/questions.php`):
  - Fitur **CRUD penuh** (Create, Read, Update, Delete) untuk semua pertanyaan.
  - Admin dapat menambah, mengedit, menonaktifkan pertanyaan, serta mengatur **section** dan **target role**.
- **Ekspor Data** (via `admin/index.php`):
  - Ekspor data jawaban **CSV**.
  - Ekspor dapat difilter berdasarkan **Peran (Role)** dan **Section (Bagian)** untuk memudahkan analisis.
- **Seeder Admin** (`admin/seed_admin.php`): Skrip **sekali pakai** untuk membuat akun admin pertama. **Hapus file ini setelah instalasi**.

---

### 3. Struktur Database (`migration.sql`)
Skema database dirancang untuk mendukung seluruh fungsionalitas aplikasi:

- **admins**: Menyimpan data login admin.
- **sections**: Mendefinisikan bagian-bagian kuesioner (`S1`, `S2`, dll.).
- **questions**: Menyimpan teks pertanyaan, `code`, `section_id`, dan `target_role`.
- **respondents**: Menyimpan data demografis tiap responden.
- **response_sessions**: Melacak setiap sesi pengisian kuesioner.
- **answers**: Menyimpan setiap jawaban (skala 1–7) yang tertaut ke `session_id` dan `question_id`.

> Import `migration.sql` akan membuat semua tabel dan mengisi seed untuk `sections` dan `questions`.

---

### 4. Keamanan
Aplikasi menerapkan praktik keamanan dasar yang baik:

- **Otentikasi**: Panel admin dilindungi oleh login dan session (`includes/auth.php`).
- **Proteksi SQL Injection**: Seluruh kueri menggunakan **PDO** dengan **prepared statements**.
- **Proteksi XSS**: Fungsi `esc_html()` membersihkan output sebelum ditampilkan.
- **Proteksi CSRF**: Setiap formulir (kuesioner & login admin) dilindungi **token CSRF unik per sesi**.
- **Hashing Password**: Password admin disimpan menggunakan `sha256` dengan **salt unik per pengguna** (`functions.php`).

---

## Teknologi yang Digunakan
- **Backend**: PHP 7/8 (Natif, prosedural)
- **Database**: MySQL
- **Frontend**: HTML5, Bootstrap 5.3
- **JavaScript**: jQuery 3.7

---

## Cara Instalasi

### 1) Database
1. Buat database baru di MySQL (misal: `psv`).
2. Import file `migration.sql` ke database tersebut.

### 2) Konfigurasi
1. Salin seluruh file proyek ke server web Anda (misal: ke folder `quisioner-full`).
2. Buka `config.php`, lalu sesuaikan kredensial database dan URL dasar:

```php
// config.php (contoh)
define('DB_HOST', 'localhost');
define('DB_NAME', 'psv');
define('DB_USER', 'root');
define('DB_PASS', '');

// URL root proyek (tanpa trailing slash)
define('BASE_URL', 'http://localhost/quisioner-full');
````

### 3) Buat Admin Pertama (Seeder)

1. Buka di browser: `http://localhost/quisioner-full/admin/seed_admin.php`.
2. Skrip akan membuat admin default **(email: `admin@example.com`, password: `admin123`)**.
3. **PENTING:** Segera **hapus** file `admin/seed_admin.php` setelah berhasil dijalankan.

### 4) Selesai

* Akses aplikasi: `BASE_URL` (misal: `http://localhost/quisioner/`).
* Akses panel admin: `BASE_URL/admin/login.php`.

---

## Catatan Tambahan

* **Routing Wizard** ditangani oleh `index.php` menggunakan `$_SESSION['wizard_step']`.
* **Pertanyaan Dinamis** ditarik dari tabel `questions` dan difilter berdasarkan `section` serta `target_role`.
* **Ekspor CSV** tersedia di dashboard admin dan dapat difilter **Role**/**Section**.
* **Keamanan**: Pastikan `display_errors` dimatikan di produksi dan gunakan HTTPS bila memungkinkan.

```
```
