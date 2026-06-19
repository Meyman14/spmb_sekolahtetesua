-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 08:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spmb`
--

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `tanggal_pendaftaran` date DEFAULT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `agama` enum('Kristen','Katolik','Islam','Hindu','Buddha','Konghucu') DEFAULT NULL,
  `status` enum('draft','final','terdaftar') NOT NULL DEFAULT 'draft',
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `nisn` varchar(20) DEFAULT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `no_kk` varchar(20) DEFAULT NULL,
  `berkebutuhan_khusus` varchar(80) DEFAULT 'Tidak',
  `dusun` varchar(80) DEFAULT NULL,
  `kelurahan` varchar(80) DEFAULT NULL,
  `kecamatan` varchar(80) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `moda_transportasi` varchar(80) DEFAULT NULL,
  `anak_keberapa` tinyint(4) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nik_ayah` varchar(20) DEFAULT NULL,
  `tahun_lahir_ayah` smallint(6) DEFAULT NULL,
  `pendidikan_ayah` varchar(50) DEFAULT NULL,
  `pekerjaan_ayah` varchar(80) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `nik_ibu` varchar(20) DEFAULT NULL,
  `tahun_lahir_ibu` smallint(6) DEFAULT NULL,
  `pendidikan_ibu` varchar(50) DEFAULT NULL,
  `pekerjaan_ibu` varchar(80) DEFAULT NULL,
  `nama_wali` varchar(100) DEFAULT NULL,
  `nik_wali` varchar(20) DEFAULT NULL,
  `tahun_lahir_wali` smallint(6) DEFAULT NULL,
  `pendidikan_wali` varchar(50) DEFAULT NULL,
  `pekerjaan_wali` varchar(80) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `lingkar_kepala` decimal(5,2) DEFAULT NULL,
  `jarak_sekolah` decimal(6,2) DEFAULT NULL,
  `waktu_tempuh` varchar(50) DEFAULT NULL,
  `jumlah_saudara` tinyint(4) DEFAULT NULL,
  `file_kk` varchar(255) DEFAULT NULL,
  `file_akte` varchar(255) DEFAULT NULL,
  `file_ijazah` varchar(255) DEFAULT NULL,
  `file_kks` varchar(255) DEFAULT NULL,
  `file_foto` varchar(255) DEFAULT NULL,
  `file_surat_orangtua` varchar(255) DEFAULT NULL,
  `tempat_lahir` varchar(80) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `verif_file_kk` enum('belum','verifikasi','batal') NOT NULL DEFAULT 'belum',
  `verif_file_akte` enum('belum','verifikasi','batal') NOT NULL DEFAULT 'belum',
  `verif_file_ijazah` enum('belum','verifikasi','batal') NOT NULL DEFAULT 'belum',
  `verif_file_kks` enum('belum','verifikasi','batal') NOT NULL DEFAULT 'belum',
  `verif_file_foto` enum('belum','verifikasi','batal') NOT NULL DEFAULT 'belum',
  `verif_file_surat_orangtua` enum('belum','verifikasi','batal') NOT NULL DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `tanggal_pendaftaran`, `nama_lengkap`, `jenis_kelamin`, `agama`, `status`, `tanggal_daftar`, `nisn`, `nik`, `no_kk`, `berkebutuhan_khusus`, `dusun`, `kelurahan`, `kecamatan`, `kode_pos`, `moda_transportasi`, `anak_keberapa`, `nama_ayah`, `nik_ayah`, `tahun_lahir_ayah`, `pendidikan_ayah`, `pekerjaan_ayah`, `nama_ibu`, `nik_ibu`, `tahun_lahir_ibu`, `pendidikan_ibu`, `pekerjaan_ibu`, `nama_wali`, `nik_wali`, `tahun_lahir_wali`, `pendidikan_wali`, `pekerjaan_wali`, `no_hp`, `email`, `tinggi_badan`, `berat_badan`, `lingkar_kepala`, `jarak_sekolah`, `waktu_tempuh`, `jumlah_saudara`, `file_kk`, `file_akte`, `file_ijazah`, `file_kks`, `file_foto`, `file_surat_orangtua`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `verif_file_kk`, `verif_file_akte`, `verif_file_ijazah`, `verif_file_kks`, `verif_file_foto`, `verif_file_surat_orangtua`) VALUES
(1, NULL, 'asasadsadsada', 'Laki-laki', 'Kristen', 'draft', '2026-05-29 17:42:50', '', '', '', 'Tidak', '', '', '', '', '', 0, 'sdssdasadsd', '', 0, '', '', 'sdasdadada', '', 0, '', '', '', '', 0, '', '', '', '', 0.00, 0.00, 0.00, 0.00, '', 0, '', '', '', '', '', '', '', '2003-12-12', '', 'belum', 'belum', 'belum', 'belum', 'belum', 'belum');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','petugas') NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `nama`) VALUES
(1, 'admin', 'admin123', 'admin', 'Admin SPMB'),
(38, 'maulidanur', 'spmbtts2026', 'petugas', 'Maulidanur'),
(39, 'saribadi', 'spmbtts2026', 'petugas', 'Saribadi'),
(40, 'nerius', 'spmbtts2026', 'petugas', 'Nerius'),
(41, 'kasih', 'spmbtts2026', 'petugas', 'Kasih'),
(42, 'anurni', 'spmbtts2026', 'petugas', 'Anurni'),
(43, 'iffan', 'spmbtts2026', 'petugas', 'Iffan'),
(44, 'agustina', 'spmbtts2026', 'petugas', 'Agustina'),
(45, 'alimmarwin', 'spmbtts2026', 'petugas', 'Alimmarwin'),
(46, 'ervinta', 'spmbtts2026', 'petugas', 'Ervinta'),
(47, 'faebuadodo', 'spmbtts2026', 'petugas', 'Faebuadodo'),
(48, 'menifati', 'spmbtts2026', 'petugas', 'Menifati'),
(49, 'yunidar', 'spmbtts2026', 'petugas', 'Yunidar'),
(50, 'lidia', 'spmbtts2026', 'petugas', 'Lidia'),
(51, 'meylestari', 'spmbtts2026', 'petugas', 'Meylestari'),
(52, 'nirani', 'spmbtts2026', 'petugas', 'Nirani'),
(53, 'niska', 'spmbtts2026', 'petugas', 'Niska'),
(54, 'theresia', 'spmbtts2026', 'petugas', 'Theresia'),
(55, 'yanania', 'spmbtts2026', 'petugas', 'Yanania');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
