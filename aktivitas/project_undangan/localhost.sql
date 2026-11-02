-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 10, 2026 at 05:28 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_praktikum`
--
CREATE DATABASE IF NOT EXISTS `db_praktikum` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_praktikum`;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int NOT NULL,
  `nim` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `jurusan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `jurusan`) VALUES
(1, '20230001', 'Budi Santoso', 'Teknik Informatika'),
(4, '21122323', 'deded', 'efef'),
(5, '25071104581', 'zaki', 'teknik informatika');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Database: `db_undangan`
--
CREATE DATABASE IF NOT EXISTS `db_undangan` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci;
USE `db_undangan`;

-- --------------------------------------------------------

--
-- Table structure for table `acara`
--

CREATE TABLE `acara` (
  `id` int UNSIGNED NOT NULL,
  `mempelai` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_akad_mulai` time NOT NULL,
  `waktu_akad_selesai` time NOT NULL,
  `waktu_resepsi_mulai` time NOT NULL,
  `waktu_resepsi_selesai` time NOT NULL,
  `map_url` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acara`
--

INSERT INTO `acara` (`id`, `mempelai`, `tanggal`, `waktu`, `lokasi`, `alamat`, `waktu_akad_mulai`, `waktu_akad_selesai`, `waktu_resepsi_mulai`, `waktu_resepsi_selesai`, `map_url`) VALUES
(1, 'fulan dan fulana', '2026-10-12', '08:00:00', 'Hotel Pangeran Pekanbaru', 'Jl. Jend. Sudirman No. 371–373, Cinta Raja, Kec. Sail, Kota Pekanbaru, Riau 28126, Indonesia', '08:00:00', '09:30:00', '11:00:00', '14:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rsvp`
--

CREATE TABLE `rsvp` (
  `id` int UNSIGNED NOT NULL,
  `acara_id` int UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kehadiran` enum('hadir','tidak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `ucapan` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rsvp`
--

INSERT INTO `rsvp` (`id`, `acara_id`, `nama`, `kehadiran`, `jumlah`, `ucapan`, `dibuat_pada`) VALUES
(2, 1, 'zaki', 'hadir', 1, 'we fahrul nikah cuy, aku yang jad wedding organizer', '2026-10-10 03:52:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acara`
--
ALTER TABLE `acara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsvp`
--
ALTER TABLE `rsvp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acara_id` (`acara_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acara`
--
ALTER TABLE `acara`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rsvp`
--
ALTER TABLE `rsvp`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rsvp`
--
ALTER TABLE `rsvp`
  ADD CONSTRAINT `rsvp_ibfk_1` FOREIGN KEY (`acara_id`) REFERENCES `acara` (`id`);
--
-- Database: `db_undangan2`
--
CREATE DATABASE IF NOT EXISTS `db_undangan2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `db_undangan2`;

-- --------------------------------------------------------

--
-- Table structure for table `acara`
--

CREATE TABLE `acara` (
  `id` int UNSIGNED NOT NULL,
  `mempelai` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `waktu_akad_mulai` time NOT NULL,
  `waktu_akad_selesai` time NOT NULL,
  `waktu_resepsi_mulai` time NOT NULL,
  `waktfu_resepsi_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rsvp`
--

CREATE TABLE `rsvp` (
  `id` int NOT NULL,
  `acara_id` int UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kehadiran` enum('hadir','tidak') NOT NULL,
  `jumlah` tinyint UNSIGNED NOT NULL,
  `ucapan` varchar(500) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acara`
--
ALTER TABLE `acara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsvp`
--
ALTER TABLE `rsvp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acara_id` (`acara_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acara`
--
ALTER TABLE `acara`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rsvp`
--
ALTER TABLE `rsvp`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
--
-- Database: `praktikum_dpw`
--
CREATE DATABASE IF NOT EXISTS `praktikum_dpw` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `praktikum_dpw`;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jurusan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `jurusan`) VALUES
(1, '20230001', 'Budi Santoso', 'Teknik Informatika'),
(2, '20230002', 'Siti Aminah', 'Sistem Informasi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Database: `undangan`
--
CREATE DATABASE IF NOT EXISTS `undangan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `undangan`;

-- --------------------------------------------------------

--
-- Table structure for table `acara`
--

CREATE TABLE `acara` (
  `id` int UNSIGNED NOT NULL,
  `mempelai` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `waktu_akad_mulai` time DEFAULT NULL,
  `waktu_akad_selesai` time DEFAULT NULL,
  `waktu_resepsi_mulai` time DEFAULT NULL,
  `waktu_resepsi_selesai` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `acara`
--

INSERT INTO `acara` (`id`, `mempelai`, `tanggal`, `waktu`, `lokasi`, `alamat`, `waktu_akad_mulai`, `waktu_akad_selesai`, `waktu_resepsi_mulai`, `waktu_resepsi_selesai`) VALUES
(1, 'gusion & lesley', '2026-12-20', '10:00:00', 'Hotel Panggeran', 'Jl. Jend. Sudirman No.371-373, Cinta Raja, Sail, Kota Pekanbaru, Riau 28126, Indonesia', '08:00:00', '09:30:00', '11:00:00', '14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `rsvp`
--

CREATE TABLE `rsvp` (
  `id` int UNSIGNED NOT NULL,
  `acara_id` int UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kehadiran` enum('hadir','tidak') NOT NULL,
  `jumlah` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `ucapan` varchar(500) NOT NULL DEFAULT '',
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rsvp`
--

INSERT INTO `rsvp` (`id`, `acara_id`, `nama`, `kehadiran`, `jumlah`, `ucapan`, `dibuat_pada`) VALUES
(3, 1, 'zaki', 'hadir', 1, 'bisalah aku buka wedding organizer', '2026-10-08 10:09:09'),
(4, 1, 'king george', 'hadir', 1, 'i dont even know why im here', '2026-10-08 10:16:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acara`
--
ALTER TABLE `acara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rsvp`
--
ALTER TABLE `rsvp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acara_id` (`acara_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acara`
--
ALTER TABLE `acara`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rsvp`
--
ALTER TABLE `rsvp`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rsvp`
--
ALTER TABLE `rsvp`
  ADD CONSTRAINT `rsvp_ibfk_1` FOREIGN KEY (`acara_id`) REFERENCES `acara` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
