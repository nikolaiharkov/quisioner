# Prototipe Kuesioner Public Service Value

Ini adalah prototipe *frontend* murni (tanpa backend) untuk situs web kuesioner `publicservicevalue.manhost.my.id`. Proyek ini dirancang untuk mendemokan alur pengguna (wizard) dan alur admin (CRUD) secara lengkap.

Semua data (pertanyaan dan respons) disimpan di `localStorage` browser, dan semua logika admin (termasuk login) disimulasikan menggunakan JavaScript sisi klien.

## 🚀 Fitur Utama

### Alur Pengguna (Kuesioner)
- **Wizard Multi-Langkah:** Pengguna dipandu melalui serangkaian langkah yang jelas (Selamat Datang, Email, Posisi, Demografis, Instruksi, Kuesioner, Review).
- **Alur Dinamis (Branching):** Pilihan posisi ("Teknisi", "Manajer", atau "Eksternal") secara dinamis mengubah alur kuesioner dan pertanyaan yang ditampilkan.
- **Validasi Real-time:** Tombol "Berikutnya" dinonaktifkan hingga semua input yang diperlukan (email, pilihan, jawaban kuesioner) diisi.
- **UI Kuesioner yang Ramah:**
  - Pilihan disajikan secara **vertikal** (atas-ke-bawah).
  - Label teks lengkap (mis: "Sangat Tidak Setuju") digunakan alih-alih angka.
  - Deskripsi pengantar disediakan untuk setiap bagian kuesioner.
- **UX Auto-Scroll:** Halaman secara otomatis menggulir ke atas setiap kali pengguna pindah ke bagian berikutnya, memastikan mereka tidak "tersesat" di bagian bawah.
- **Halaman Review:** Halaman review yang jelas menampilkan jawaban dalam format angka dan teks (mis: `7 (sangat setuju)`).
- **Penyimpanan Lokal:** Setelah submit, respons lengkap disimpan ke `localStorage` dengan *timestamp* dan ID unik.

### Alur Admin (Simulasi)
- **Login Aman (Demo):** Halaman login (`/admin/login.html`) menggunakan **Web Crypto API (SubtleCrypto)** untuk melakukan hash SHA-256 pada kata sandi *sebelum* membandingkannya dengan hash yang disimpan di `localStorage`.
  - Password Demo: `admin123`
- **Dasbor Ringkasan:** Menampilkan jumlah total responden dan rincian per posisi (Teknisi, Manajer, Eksternal).
- **Tabel Data Responden:** Menampilkan semua data yang masuk dengan filter berdasarkan posisi.
- **Ekspor CSV:** Fitur untuk mengunduh data respons yang telah difilter (berdasarkan posisi dan bagian) sebagai file `.csv` yang dibuat di sisi klien.
- **CRUD Kuesioner Penuh:**
  - Admin dapat **Membuat, Membaca, Memperbarui, dan Menghapus** (CRUD) pertanyaan.
  - Dukungan untuk varian pertanyaan (Tab "Teknisi" dan "Manajer" untuk Section 1).
  - Tombol "Reset ke Seed" untuk mengembalikan daftar pertanyaan ke data demo awal.

## 🛠️ Tumpukan Teknologi (Tech Stack)
- **HTML5:** Struktur semantik.
- **Bootstrap 5.3 (CDN):** Untuk komponen UI, layout, dan grid.
- **jQuery 3.7 (CDN):** Untuk manipulasi DOM, event handling, dan memuat *partials*.
- **JavaScript (ES6+):** Menggerakkan semua logika wizard, validasi, dan fungsionalitas admin.
- **Browser `localStorage`:** Digunakan sebagai database sisi klien untuk menyimpan pertanyaan, respons, dan kredensial admin.
- **Web Crypto API (SubtleCrypto):** Digunakan untuk simulasi hashing kata sandi yang aman di sisi klien.

## 📂 Struktur Proyek
Ini adalah bagian yang bermasalah di Markdown. Di HTML, ini akan selalu tampil benar:

```
/
├── index.html          (Halaman wizard kuesioner utama)
├── admin/
│   ├── login.html      (Halaman login admin)
│   └── admin.html      (Dasbor admin)
├── assets/
│   ├── css/
│   │   └── style.css   (Gaya kustom, termasuk layout Likert vertikal)
│   └── js/
│       ├── app.js      (Logika wizard pengguna + helpers)
│       ├── admin.js    (Logika panel admin)
│       └── seed.js     (Data pertanyaan awal/demo)
└── partials/
    ├── header.html     (Header navbar global)
    └── footer.html     (Footer global)
```

## ⚡ Cara Menjalankan

> **Penting:** Proyek ini **TIDAK AKAN** berjalan dengan benar jika Anda hanya membuka `index.html` (via `file://`).

Proyek ini menggunakan `jQuery.load()` untuk memuat *partials* (header/footer), yang memerlukan protokol `http://` atau `https://` (dibatasi oleh kebijakan CORS browser).

**Cara Termudah (Rekomendasi):**
1. Buka folder proyek di **Visual Studio Code**.
2. Instal ekstensi **"Live Server"**.
3. Klik kanan pada `index.html` dan pilih **"Open with Live Server"**.

**Cara Alternatif (Python):**
1. Buka terminal di folder proyek Anda.
2. Jalankan salah satu perintah berikut (tergantung versi Python Anda):

```bash
# Python 3.x
python -m http.server

# Python 2.x
python -m SimpleHTTPServer
```

3. Buka browser Anda dan navigasikan ke `http://localhost:8000`.

## 💾 Skema localStorage

Proyek ini menggunakan 3 kunci utama di `localStorage`:

### 1. `psv_questions`
Menyimpan data *master* untuk semua pertanyaan.

```json
{
  "section1": {
    "teknisi": [ { "id":"S1T-1","text":"..." }, ... ],
    "manajer": [ { "id":"S1M-1","text":"..." }, ... ]
  },
  "section2": [ { "id":"S2-1","text":"..." }, ... ],
  "section3": [ ... ],
  "section4": [ ... ],
  "section5": [ ... ]
}
```

### 2. `psv_responses`
Sebuah array yang berisi setiap respons yang telah disubmit.

```json
[
  {
    "id": "RESP-1699268400000",
    "email": "user@example.com",
    "posisi": "teknisi",
    "demografis": { "umur": 30, ... },
    "answers": {
      "section1": [ {"qid":"S1T-1","value":7}, ... ],
      "section2": [ {"qid":"S2-1","value":5}, ... ]
    },
    "submittedAt": "2025-11-06T10:00:00.000Z"
  }
]
```

### 3. `psv_admin`
Menyimpan hash kata sandi admin dan status login (simulasi).

```json
{
  "passwordHash": "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918",
  "lastLoginAt": "2025-11-06T10:05:00.000Z"
}
```

## 🔮 Rencana ke Depan (TODO untuk Produksi)

Ini adalah prototipe. Untuk beralih ke produksi (misalnya dengan PHP Native + MySQL):

1. **Ganti `localStorage` dengan API:**
   - **`admin.js` (CRUD):** Ganti semua panggilan `PSV.storage.set("psv_questions", ...)` dengan panggilan `fetch()` (AJAX) ke skrip PHP (misal: `api/questions.php?action=update`).
   - **`app.js` (Submit):** Ganti `PSV.storage.set("psv_responses", ...)` dengan satu panggilan `fetch()` ke `api/submit.php` yang mengirim seluruh objek `wizardData` sebagai JSON.

2. **Backend Login:** Ganti simulasi login di `admin.js` dengan panggilan `fetch()` ke `api/login.php` yang mengatur *session cookie* HTTP-Only.

3. **Ekspor CSV:** Pindahkan logika ekspor CSV ke *backend* agar dapat menangani volume data yang besar.
