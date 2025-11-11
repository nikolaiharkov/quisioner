# Proyek Kuesioner Public Service Value (PSV)

Aplikasi web ini adalah sistem kuesioner **multi-langkah (wizard)** yang dibuat menggunakan **PHP natif**, **MySQL**, dan **Bootstrap 5**. Aplikasi dirancang untuk mengumpulkan data dari tiga jenis responden (DJBC Pelaksana, DJBC Manager, dan Eksternal) dan dilengkapi panel admin untuk mengelola pertanyaan serta mengekspor hasil.

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

Wizard kini juga mendukung **navigasi 'Kembali'** di setiap langkah untuk memperbaiki input sebelumnya.

**Alur standar untuk responden (Internal: 9 Langkah / Eksternal: 7 Langkah):**

1.  **Selamat Datang** (`views/welcome.php`): Halaman pengantar, perkenalan peneliti, dan tujuan penelitian. (Menampilkan Logo).
2.  **Info Responden** (`views/welcome_part2.php`): Penjelasan detail mengenai sasaran responden (Pelaksana, Manajer, Eksternal).
3.  **Nama Lengkap** (`views/fullname.php`): Mengumpulkan identitas responden.
4.  **Pemilihan Peran** (`views/role.php`): Langkah krusial untuk menentukan alur. Responden memilih antara:
    * **DJBC Pelaksana** (Internal)
    * **DJBC Manager** (Internal)
    * **Eksternal** (Pemangku Kepentingan)
5.  **Demografi** (`views/demographics.php`): Memuat formulir demografis **berdasarkan peran** (`demographics_teknisi_manajer.php` atau `demographics_eksternal.php`).
    * *Fitur Dinamis:* Pilihan "Jabatan" untuk **DJBC Pelaksana** otomatis dikunci sebagai "Staf/Pelaksana". **DJBC Manager** mendapatkan daftar 5 pilihan jabatan level manajerial.
6.  **Instruksi** (`views/instructions.php`): Penjelasan skala **Likert 1–7**.
7.  **Pengisian Kuesioner** (`views/section.php`): Halaman dinamis yang memuat pertanyaan dari database (`questions`) berdasarkan:
    * **Section (Bagian):** Kuesioner dibagi ke beberapa bagian.
    * **Peran (Role):** Pertanyaan ditampilkan selektif.
    * **Fitur Dinamis (Judul S1):** Responden **Pelaksana** melihat judul "Technical AI Competency (TAC)", sementara **Manager** melihat "Managerial AI Competency (MAC)".
    * **Fitur Dinamis (Alur S3):** Untuk responden internal, Bagian 3 (OSI) **dipisah menjadi dua langkah terpisah**:
        * Langkah 1: **Exploitative Service Innovation (EXPL)**
        * Langkah 2: **Exploratory Service Innovation (EXPR)**
8.  **Selesai** (`views/done.php`): Halaman terima kasih dan sesi pengguna dibersihkan.

---

### 2. Panel Admin (`/admin/`)
Area admin terproteksi untuk mengelola kuesioner dan data. (Tidak ada perubahan fungsional)

- **Login Admin** (`admin/login.php`)
- **Dashboard** (`admin/index.php`)
- **Manajemen Pertanyaan** (`admin/questions.php`)
- **Ekspor Data** (via `admin/index.php`)
- **Seeder Admin** (`admin/seed_admin.php`)

---

### 3. Struktur Database (`migration.sql`)
Skema database dirancang untuk mendukung seluruh fungsionalitas aplikasi.

- **admins**: Menyimpan data login admin.
- **sections**: Mendefinisikan bagian-bagian kuesioner (`S1`, `S2`, dll.).
- **questions**: Menyimpan teks pertanyaan, `code`, `section_id`, dan `target_role`.
- **respondents**: Menyimpan data demografis tiap responden.
- **response_sessions**: Melacak setiap sesi pengisian kuesioner.
- **answers**: Menyimpan setiap jawaban (skala 1–7).

> **PENTING (Perubahan Pasca-Migrasi):**
> File `migration.sql` di repositori ini **BELUM DIPERBARUI** untuk mencerminkan perubahan pada form demografi. Setelah menjalankan `migration.sql`, Anda **WAJIB** menjalankan kueri `ALTER TABLE` berikut agar form demografi Manajer berfungsi dan tidak error:
> ```sql
> ALTER TABLE respondents MODIFY COLUMN jabatan 
> ENUM(
>     'Staf/Pelaksana', 
>     'Kepala Seksi', 
>     'Kepala Subdit', 
>     'Kepala Bidang', 
>     'Kepala Kantor', 
>     'Direktur atau Setara'
> );
> ```
> *Kueri ini memperbarui definisi `ENUM` pada kolom `jabatan` agar sesuai dengan 5 opsi baru untuk DJBC Manager.*

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
3. **(WAJIB)** Jalankan kueri SQL berikut pada database Anda untuk memperbarui kolom `jabatan` agar sesuai dengan logika aplikasi terbaru:
   ```sql
   ALTER TABLE respondents MODIFY COLUMN jabatan 
   ENUM(
       'Staf/Pelaksana', 'Kepala Seksi', 'Kepala Subdit', 
       'Kepala Bidang', 'Kepala Kantor', 'Direktur atau Setara'
   );
````

### 2\) Konfigurasi

1.  Salin seluruh file proyek ke server web Anda (misal: ke folder `quisioner-full`).
2.  Buka `config.php`, lalu sesuaikan kredensial database dan URL dasar:

<!-- end list -->

```php
// config.php (contoh)
define('DB_HOST', 'localhost');
define('DB_NAME', 'psv');
define('DB_USER', 'root');
define('DB_PASS', '');

// URL root proyek (tanpa trailing slash)
define('BASE_URL', 'http://localhost/quisioner-full');
```

### 3\) Buat Admin Pertama (Seeder)

1.  Buka di browser: `http://localhost/quisioner-full/admin/seed_admin.php`.
2.  Skrip akan membuat admin default **(email: `admin@example.com`, password: `admin123`)**.
3.  **PENTING:** Segera **hapus** file `admin/seed_admin.php` setelah berhasil dijalankan.

### 4\) Selesai

  * Akses aplikasi: `BASE_URL` (misal: `http://localhost/quisioner/`).
  * Akses panel admin: `BASE_URL/admin/login.php`.

-----

## Catatan Tambahan

  * **Routing Wizard** ditangani oleh `index.php` menggunakan `$_SESSION['wizard_step']` dan kini mencakup 9 langkah untuk internal.
  * **Navigasi Mundur** kini didukung melalui `action="go_back"`.
  * **Pertanyaan Dinamis** ditarik dari tabel `questions` dan difilter berdasarkan `section`, `target_role`, dan `code` (untuk S3).
  * **Ekspor CSV** tersedia di dashboard admin.
  * **Keamanan**: Pastikan `display_errors` dimatikan di produksi.

```
```