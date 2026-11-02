-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 10, 2026 at 05:32 AM
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
-- Database: `db_undangan`
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
