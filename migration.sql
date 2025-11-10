-- --------------------------------------------------------
-- Skema Database Kuesioner Public Service Value
-- REVISI FINAL: Memasukkan tabel 'respondents' 7-poin
-- DAN seed data untuk 'questions' S1, S2, S3, S5.
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `psv_kuesioner_db` (Ganti jika perlu)
--

-- --------------------------------------------------------

--
-- Struktur tabel untuk `admins`
--
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `password_hash` char(64) NOT NULL,
  `salt` char(32) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `sections`
--
DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` tinyint(4) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Seed data untuk tabel `sections`
--
INSERT INTO `sections` (`id`, `code`, `name`, `sort_order`) VALUES
(1, 'S1', 'Human AI Competency', 10),
(2, 'S2', 'Organizational Digital Culture', 20),
(3, 'S3', 'Organizational Service Innovation', 30),
(4, 'S4', '(Opsional/TBD)', 40),
(5, 'S5', 'Citizen-Centric Public Service Value', 50);

-- --------------------------------------------------------

--
-- Struktur tabel untuk `questions`
--
DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` tinyint(4) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `text` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `target_role` enum('semua','teknisi','manajer','eksternal') NOT NULL DEFAULT 'semua',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Seed data untuk tabel `questions` (SESUAI PERMINTAAN)
--
INSERT INTO `questions` (`section_id`, `code`, `text`, `sort_order`, `target_role`, `is_active`) VALUES
-- S1: Technical AI Competency (TAC)
(1, 'T1', 'Technical officers di organisasi kami mampu menggunakan dan menyesuaikan teknologi AI untuk menyelesaikan kebutuhan atau masalah spesifik dalam layanan publik.', 10, 'teknisi', 1),
(1, 'T2', 'Technical officers memiliki pemahaman teknis yang cukup dan keterampilan praktis untuk mengoperasikan dan mengembangkan layanan yang didukung AI.', 20, 'teknisi', 1),
(1, 'T3', 'Technical officers mampu mengintegrasikan sistem AI dengan sistem digital pemerintah yang sudah ada, sambil tetap memastikan keamanan dan perlindungan data.', 30, 'teknisi', 1),
(1, 'T4', 'Technical officers mampu menilai dan mengurangi potensi bias atau kesalahan dalam sistem AI agar hasilnya tetap adil dan dapat dipertanggungjawabkan.', 40, 'teknisi', 1),
(1, 'T5', 'Technical officers dapat menjelaskan hasil atau keluaran AI dengan bahasa yang mudah dipahami oleh pegawai lain yang tidak memiliki latar belakang teknis.', 50, 'teknisi', 1),
-- S1: Managerial AI Competency (MAC)
(1, 'M1', 'Pimpinan di organisasi kami mampu mengidentifikasi permasalahan penting dan mengambil keputusan strategis untuk menentukan kapan dan bagaimana AI digunakan guna meningkatkan kualitas layanan publik.', 110, 'manajer', 1),
(1, 'M2', 'Pimpinan mampu mengkoordinasikan kerja sama antara pegawai yang menangani sistem, pegawai operasional, dan pihak eksternal untuk mengembangkan solusi berbasis AI.', 120, 'manajer', 1),
(1, 'M3', 'Pimpinan menunjukkan kepemimpinan digital, termasuk dalam mengalokasikan sumber daya (waktu, anggaran, pelatihan) untuk mendukung penerapan AI secara berkelanjutan.', 130, 'manajer', 1),
(1, 'M4', 'Pimpinan mampu mengantisipasi kebutuhan layanan di masa mendatang dan mendorong penggunaan AI secara proaktif untuk meningkatkan kualitas dan kecepatan layanan publik.', 140, 'manajer', 1),
(1, 'M5', 'Pimpinan menjamin penerapan AI sesuai aturan, etika, keamanan data, dan perlindungan masyarakat, termasuk dalam pencegahan penyalahgunaan teknologi.', 150, 'manajer', 1),

-- S2: Organizational Digital Culture (ODC) (Target 'semua' = Teknisi + Manajer)
(2, 'ODC1', 'Pegawai dari berbagai unit dan fungsi bekerja sama secara lintas bagian dalam melaksanakan inisiatif transformasi dan inovasi digital.', 10, 'semua', 1),
(2, 'ODC2', 'Pegawai di berbagai tingkatan mendukung dan beradaptasi dengan perubahan yang dibawa oleh teknologi digital sebagai bagian dari budaya kerja organisasi.', 20, 'semua', 1),
(2, 'ODC3', 'Inovasi digital telah menjadi proses yang berkelanjutan dan terstruktur, bukan hanya kegiatan sementara atau proyek sesaat.', 30, 'semua', 1),
(2, 'ODC4', 'Organisasi secara terbuka menyampaikan arah transformasi digital dan melibatkan pegawai dalam memberikan masukan terhadap keputusan terkait.', 40, 'semua', 1),
(2, 'ODC5', 'Organisasi mendorong percobaan dan pembelajaran, serta menciptakan suasana aman secara psikologis untuk mencoba ide-ide digital baru tanpa rasa takut disalahkan jika terjadi kesalahan.', 50, 'semua', 1),
(2, 'ODC6', 'Dalam mengambil keputusan terkait inisiatif digital, organisasi lebih mengandalkan data dan analisis, bukan semata mata pada intuisi atau hierarki jabatan.', 60, 'semua', 1),

-- S3: Organizational Service Innovation (OSI) (Target 'semua' = Teknisi + Manajer)
(3, 'EXPL1', 'Organisasi kami secara rutin meningkatkan layanan yang sudah ada berdasarkan umpan balik dan pembelajaran dari pengalaman sebelumnya.', 10, 'semua', 1),
(3, 'EXPL2', 'Pegawai mendapatkan dukungan dan pelatihan untuk menggunakan AI atau analitik data dalam mengoptimalkan proses layanan.', 20, 'semua', 1),
(3, 'EXPL3', 'Sistem digital yang sudah ada digunakan untuk meningkatkan kejelasan, akuntabilitas, dan konsistensi dalam layanan publik.', 30, 'semua', 1),
(3, 'EXPL4', 'Kami menggunakan otomatisasi untuk membantu layanan rutin agar lebih cepat dan mengurangi kesalahan.', 40, 'semua', 1),
(3, 'EXPR1', 'Organisasi kami mendorong percobaan terhadap ide layanan digital baru, meskipun hasil akhirnya belum pasti.', 110, 'semua', 1),
(3, 'EXPR2', 'Kami mencoba pendekatan atau model layanan baru yang berbeda dari cara kerja sebelumnya.', 120, 'semua', 1),
(3, 'EXPR3', 'Organisasi kami menjajaki penggunaan teknologi AI baru untuk memenuhi kebutuhan layanan yang belum terpenuhi.', 130, 'semua', 1),
(3, 'EXPR4', 'Kami bekerja sama dengan warga, mitra, atau pihak luar dalam merancang dan menguji layanan digital baru.', 140, 'semua', 1),

-- S5: Citizen Centric Public Service Value (CCPSV) (Target 'eksternal')
(5, 'CA1', 'Sistem layanan digital pemerintah mudah saya akses dan gunakan kapan saja, di mana saja.', 10, 'eksternal', 1),
(5, 'CA2', 'Sistem dapat digunakan di berbagai perangkat dan kondisi jaringan.', 20, 'eksternal', 1),
(5, 'CA3', 'Saya tidak mengalami hambatan teknis dalam mengakses sistem ini.', 30, 'eksternal', 1),
(5, 'PR1', 'Saya melihat adanya perbaikan sistem yang menanggapi masukan warga.', 110, 'eksternal', 1),
(5, 'PR2', 'Saya mudah memberikan saran atau melaporkan masalah melalui saluran yang jelas.', 120, 'eksternal', 1),
(5, 'PR3', 'Masukan dari warga benar-benar dipertimbangkan dalam perbaikan layanan.', 130, 'eksternal', 1),
(5, 'TT1', 'Sistem memberi tahu dengan jelas jika suatu proses dijalankan oleh AI.', 210, 'eksternal', 1),
(5, 'TT2', 'Saya mengetahui bagaimana data pribadi saya digunakan dan dilindungi.', 220, 'eksternal', 1),
(5, 'TT3', 'Saya percaya keputusan layanan digital pemerintah dibuat secara transparan untuk kepentingan publik.', 230, 'eksternal', 1),
(5, 'IIE1', 'Sistem dapat digunakan oleh semua orang, termasuk yang kemampuan digitalnya terbatas.', 310, 'eksternal', 1),
(5, 'IIE2', 'Sistem memperlakukan seluruh pengguna secara setara.', 320, 'eksternal', 1),
(5, 'IIE3', 'Layanan digital ini membantu memperluas akses publik dan mengurangi kesenjangan layanan.', 330, 'eksternal', 1),
(5, 'PVO1', 'Sistem digital membantu saya menyelesaikan urusan lebih cepat dan mudah.', 410, 'eksternal', 1),
(5, 'PVO2', 'Informasi yang diberikan sistem akurat dan sesuai dengan kebutuhan saya.', 420, 'eksternal', 1),
(5, 'PVO3', 'Saya merasa lebih puas terhadap layanan pemerintah setelah menggunakan sistem ini.', 430, 'eksternal', 1),
(5, 'PVO4', 'Keberadaan sistem ini meningkatkan kepercayaan saya pada institusi pemerintah.', 440, 'eksternal', 1);

-- --------------------------------------------------------

--
-- Struktur tabel untuk `respondents` (Versi 7-poin)
--
DROP TABLE IF EXISTS `respondents`;
CREATE TABLE `respondents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(190) NOT NULL,
  `role` enum('teknisi','manajer','eksternal') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  
  -- Demografi Internal (CV1-CV7)
  `gender` enum('L','P','Lainnya') DEFAULT NULL,
  `age_group` enum('20-29','30-39','40-49','50+') DEFAULT NULL,
  `pendidikan` enum('Diploma','S1','S2','S3','Lainnya') DEFAULT NULL,
  `jabatan` enum('Staf/Pelaksana','Kasi/Supervisor','Kasubdit/Manajer','Kabid/Senior Manager','Direktur/Setara') DEFAULT NULL,
  `lama_bekerja` enum('<3','3-7','8-15','>15') DEFAULT NULL,
  `unit` varchar(150) DEFAULT NULL,
  `pengalaman_ai` enum('Belum pernah','Pernah menggunakan','Terlibat proyek AI','Mengelola/mengembangkan') DEFAULT NULL,

  -- Demografi Eksternal
  `eksternal_kategori` enum('Importir/Eksportir (Perusahaan/Pribadi)','PPJK/Freight Forwarder','Operator TPS/PLB/Kawasan Berikat','Perusahaan logistik/e-commerce') DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `response_sessions`
--
DROP TABLE IF EXISTS `response_sessions`;
CREATE TABLE `response_sessions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `respondent_id` int(11) NOT NULL,
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `current_section` tinyint(4) DEFAULT NULL,
  `started_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `respondent_id` (`respondent_id`),
  CONSTRAINT `response_sessions_ibfk_1` FOREIGN KEY (`respondent_id`) REFERENCES `respondents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `answers`
--
DROP TABLE IF EXISTS `answers`;
CREATE TABLE `answers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session_id` bigint(20) NOT NULL,
  `question_id` int(11) NOT NULL,
  `value` tinyint(4) NOT NULL CHECK (`value` between 1 and 7),
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_answer` (`session_id`,`question_id`),
  KEY `session_id` (`session_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `response_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- AUTO_INCREMENT untuk tabel
--
ALTER TABLE `admins` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `questions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;
ALTER TABLE `respondents` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `response_sessions` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `answers` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

SET FOREIGN_KEY_CHECKS = 1;