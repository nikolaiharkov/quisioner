-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 11, 2025 at 01:09 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `psv`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` char(64) COLLATE utf8mb4_general_ci NOT NULL,
  `salt` char(32) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password_hash`, `salt`, `created_at`) VALUES
(1, 'admin@example.com', 'd4e3a02bdd9bb4b3caf9623fdf98da07493dc1de43c5892bddf84dc32f4b907c', 'cdf18cfa171653fce54b23263ed9142a', '2025-11-11 20:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` bigint NOT NULL,
  `session_id` bigint NOT NULL,
  `question_id` int NOT NULL,
  `value` tinyint NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL,
  `section_id` tinyint NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `text` text COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `target_role` enum('semua','teknisi','manajer','eksternal') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'semua',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `section_id`, `code`, `text`, `sort_order`, `target_role`, `is_active`) VALUES
(1, 1, 'T1', 'Technical officers di organisasi kami mampu menggunakan dan menyesuaikan teknologi AI untuk menyelesaikan kebutuhan atau masalah spesifik dalam layanan publik.', 10, 'teknisi', 1),
(2, 1, 'T2', 'Technical officers memiliki pemahaman teknis yang cukup dan keterampilan praktis untuk mengoperasikan dan mengembangkan layanan yang didukung AI.', 20, 'teknisi', 1),
(3, 1, 'T3', 'Technical officers mampu mengintegrasikan sistem AI dengan sistem digital pemerintah yang sudah ada, sambil tetap memastikan keamanan dan perlindungan data.', 30, 'teknisi', 1),
(4, 1, 'T4', 'Technical officers mampu menilai dan mengurangi potensi bias atau kesalahan dalam sistem AI agar hasilnya tetap adil dan dapat dipertanggungjawabkan.', 40, 'teknisi', 1),
(5, 1, 'T5', 'Technical officers dapat menjelaskan hasil atau keluaran AI dengan bahasa yang mudah dipahami oleh pegawai lain yang tidak memiliki latar belakang teknis.', 50, 'teknisi', 1),
(6, 1, 'M1', 'Pimpinan di organisasi kami mampu mengidentifikasi permasalahan penting dan mengambil keputusan strategis untuk menentukan kapan dan bagaimana AI digunakan guna meningkatkan kualitas layanan publik.', 110, 'manajer', 1),
(7, 1, 'M2', 'Pimpinan mampu mengkoordinasikan kerja sama antara pegawai yang menangani sistem, pegawai operasional, dan pihak eksternal untuk mengembangkan solusi berbasis AI.', 120, 'manajer', 1),
(8, 1, 'M3', 'Pimpinan menunjukkan kepemimpinan digital, termasuk dalam mengalokasikan sumber daya (waktu, anggaran, pelatihan) untuk mendukung penerapan AI secara berkelanjutan.', 130, 'manajer', 1),
(9, 1, 'M4', 'Pimpinan mampu mengantisipasi kebutuhan layanan di masa mendatang dan mendorong penggunaan AI secara proaktif untuk meningkatkan kualitas dan kecepatan layanan publik.', 140, 'manajer', 1),
(10, 1, 'M5', 'Pimpinan menjamin penerapan AI sesuai aturan, etika, keamanan data, dan perlindungan masyarakat, termasuk dalam pencegahan penyalahgunaan teknologi.', 150, 'manajer', 1),
(11, 2, 'ODC1', 'Pegawai dari berbagai unit dan fungsi bekerja sama secara lintas bagian dalam melaksanakan inisiatif transformasi dan inovasi digital.', 10, 'semua', 1),
(12, 2, 'ODC2', 'Pegawai di berbagai tingkatan mendukung dan beradaptasi dengan perubahan yang dibawa oleh teknologi digital sebagai bagian dari budaya kerja organisasi.', 20, 'semua', 1),
(13, 2, 'ODC3', 'Inovasi digital telah menjadi proses yang berkelanjutan dan terstruktur, bukan hanya kegiatan sementara atau proyek sesaat.', 30, 'semua', 1),
(14, 2, 'ODC4', 'Organisasi secara terbuka menyampaikan arah transformasi digital dan melibatkan pegawai dalam memberikan masukan terhadap keputusan terkait.', 40, 'semua', 1),
(15, 2, 'ODC5', 'Organisasi mendorong percobaan dan pembelajaran, serta menciptakan suasana aman secara psikologis untuk mencoba ide-ide digital baru tanpa rasa takut disalahkan jika terjadi kesalahan.', 50, 'semua', 1),
(16, 2, 'ODC6', 'Dalam mengambil keputusan terkait inisiatif digital, organisasi lebih mengandalkan data dan analisis, bukan semata mata pada intuisi atau hierarki jabatan.', 60, 'semua', 1),
(17, 3, 'EXPL1', 'Organisasi kami secara rutin meningkatkan layanan yang sudah ada berdasarkan umpan balik dan pembelajaran dari pengalaman sebelumnya.', 10, 'semua', 1),
(18, 3, 'EXPL2', 'Pegawai mendapatkan dukungan dan pelatihan untuk menggunakan AI atau analitik data dalam mengoptimalkan proses layanan.', 20, 'semua', 1),
(19, 3, 'EXPL3', 'Sistem digital yang sudah ada digunakan untuk meningkatkan kejelasan, akuntabilitas, dan konsistensi dalam layanan publik.', 30, 'semua', 1),
(20, 3, 'EXPL4', 'Kami menggunakan otomatisasi untuk membantu layanan rutin agar lebih cepat dan mengurangi kesalahan.', 40, 'semua', 1),
(21, 3, 'EXPR1', 'Organisasi kami mendorong percobaan terhadap ide layanan digital baru, meskipun hasil akhirnya belum pasti.', 110, 'semua', 1),
(22, 3, 'EXPR2', 'Kami mencoba pendekatan atau model layanan baru yang berbeda dari cara kerja sebelumnya.', 120, 'semua', 1),
(23, 3, 'EXPR3', 'Organisasi kami menjajaki penggunaan teknologi AI baru untuk memenuhi kebutuhan layanan yang belum terpenuhi.', 130, 'semua', 1),
(24, 3, 'EXPR4', 'Kami bekerja sama dengan warga, mitra, atau pihak luar dalam merancang dan menguji layanan digital baru.', 140, 'semua', 1),
(25, 5, 'CA1', 'Sistem layanan digital pemerintah mudah saya akses dan gunakan kapan saja, di mana saja.', 10, 'eksternal', 1),
(26, 5, 'CA2', 'Sistem dapat digunakan di berbagai perangkat dan kondisi jaringan.', 20, 'eksternal', 1),
(27, 5, 'CA3', 'Saya tidak mengalami hambatan teknis dalam mengakses sistem ini.', 30, 'eksternal', 1),
(28, 5, 'PR1', 'Saya melihat adanya perbaikan sistem yang menanggapi masukan warga.', 110, 'eksternal', 1),
(29, 5, 'PR2', 'Saya mudah memberikan saran atau melaporkan masalah melalui saluran yang jelas.', 120, 'eksternal', 1),
(30, 5, 'PR3', 'Masukan dari warga benar-benar dipertimbangkan dalam perbaikan layanan.', 130, 'eksternal', 1),
(31, 5, 'TT1', 'Sistem memberi tahu dengan jelas jika suatu proses dijalankan oleh AI.', 210, 'eksternal', 1),
(32, 5, 'TT2', 'Saya mengetahui bagaimana data pribadi saya digunakan dan dilindungi.', 220, 'eksternal', 1),
(33, 5, 'TT3', 'Saya percaya keputusan layanan digital pemerintah dibuat secara transparan untuk kepentingan publik.', 230, 'eksternal', 1),
(34, 5, 'IIE1', 'Sistem dapat digunakan oleh semua orang, termasuk yang kemampuan digitalnya terbatas.', 310, 'eksternal', 1),
(35, 5, 'IIE2', 'Sistem memperlakukan seluruh pengguna secara setara.', 320, 'eksternal', 1),
(36, 5, 'IIE3', 'Layanan digital ini membantu memperluas akses publik dan mengurangi kesenjangan layanan.', 330, 'eksternal', 1),
(37, 5, 'PVO1', 'Sistem digital membantu saya menyelesaikan urusan lebih cepat dan mudah.', 410, 'eksternal', 1),
(38, 5, 'PVO2', 'Informasi yang diberikan sistem akurat dan sesuai dengan kebutuhan saya.', 420, 'eksternal', 1),
(39, 5, 'PVO3', 'Saya merasa lebih puas terhadap layanan pemerintah setelah menggunakan sistem ini.', 430, 'eksternal', 1),
(40, 5, 'PVO4', 'Keberadaan sistem ini meningkatkan kepercayaan saya pada institusi pemerintah.', 440, 'eksternal', 1);

-- --------------------------------------------------------

--
-- Table structure for table `respondents`
--

CREATE TABLE `respondents` (
  `id` int NOT NULL,
  `full_name` varchar(190) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('teknisi','manajer','eksternal') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `gender` enum('L','P','Lainnya') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `age_group` enum('20-29','30-39','40-49','50+') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pendidikan` enum('Diploma','S1','S2','S3','Lainnya') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jabatan` enum('Staf/Pelaksana','Kepala Seksi','Kepala Subdit','Kepala Bidang','Kepala Kantor','Direktur atau Setara') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lama_bekerja` enum('<3','3-7','8-15','>15') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pengalaman_ai` enum('Belum pernah','Pernah menggunakan','Terlibat proyek AI','Mengelola/mengembangkan') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `eksternal_kategori` enum('Importir/Eksportir (Perusahaan/Pribadi)','PPJK/Freight Forwarder','Operator TPS/PLB/Kawasan Berikat','Perusahaan logistik/e-commerce') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `response_sessions`
--

CREATE TABLE `response_sessions` (
  `id` bigint NOT NULL,
  `respondent_id` int NOT NULL,
  `status` enum('in_progress','completed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'in_progress',
  `current_section` tinyint DEFAULT NULL,
  `started_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` tinyint NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `code`, `name`, `sort_order`) VALUES
(1, 'S1', 'Human AI Competency', 10),
(2, 'S2', 'Organizational Digital Culture', 20),
(3, 'S3', 'Organizational Service Innovation', 30),
(4, 'S4', '(Opsional/TBD)', 40),
(5, 'S5', 'Citizen-Centric Public Service Value', 50);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_answer` (`session_id`,`question_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `respondents`
--
ALTER TABLE `respondents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role` (`role`);

--
-- Indexes for table `response_sessions`
--
ALTER TABLE `response_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `respondent_id` (`respondent_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `respondents`
--
ALTER TABLE `respondents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `response_sessions`
--
ALTER TABLE `response_sessions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `response_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `response_sessions`
--
ALTER TABLE `response_sessions`
  ADD CONSTRAINT `response_sessions_ibfk_1` FOREIGN KEY (`respondent_id`) REFERENCES `respondents` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
