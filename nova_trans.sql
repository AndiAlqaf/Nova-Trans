-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2025 at 09:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nova_trans`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `teks_lengkap` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `deskripsi`, `teks_lengkap`, `gambar`, `tanggal`) VALUES
(1, 'Nova Trans Siap Antar Anda dengan Aman dan Nyaman', 'Nova Trans hadir sebagai solusi transportasi darat yang aman, nyaman, dan terjangkau untuk semua kalangan.', 'Nova Trans adalah layanan transportasi darat yang berkomitmen memberikan kenyamanan dan efisiensi bagi para penumpang. Dengan armada bus yang modern dan fasilitas lengkap, Nova Trans menghadirkan solusi perjalanan yang aman, cepat, dan terjangkau.\r\n\r\nKami menyediakan layanan kelas ekonomi yang tetap mengutamakan kenyamanan. Setiap penumpang akan menikmati tempat duduk yang ergonomis, AC yang sejuk, serta layanan sopir profesional yang berpengalaman. Rute perjalanan kami mencakup berbagai kota besar dan destinasi wisata, menjadikan Nova Trans sebagai pilihan tepat untuk perjalanan pribadi maupun rombongan.\r\n\r\nNova Trans siap menjadi mitra perjalanan anda, kapan pun dan di mana pun.', 'millenium_limited.png', '2025-06-28 14:18:13'),
(2, 'Nova Trans Siap Antar Anda dengan Aman dan Nyaman', 'Nova Trans hadir sebagai solusi transportasi darat yang aman, nyaman, dan terjangkau untuk semua kalangan.', 'Perjalanan jauh kini bukan lagi masalah. Nova Trans, perusahaan otobus terpercaya, menyediakan layanan transportasi yang mengutamakan kenyamanan dan keselamatan penumpang. Dengan dukungan armada terbaru serta kru yang profesional, Nova Trans menjadi pilihan utama bagi masyarakat yang ingin bepergian dengan tenang dan tepat waktu.\r\n\r\nTidak hanya itu, Nova Trans juga menawarkan harga tiket yang bersahabat serta berbagai fasilitas seperti reclining seat, AC, dan hiburan selama perjalanan. Jadwal keberangkatan yang fleksibel dan rute yang luas semakin mempermudah mobilitas pelanggan.\r\n\r\nNova Trans, sahabat perjalanan anda ke mana saja dan kapan saja.', 'apm nag.png', '2025-06-28 14:41:38'),
(3, 'Rasakan Kenyamanan Premium Bersama Nova Trans Executive', 'Nova Trans Executive hadir dengan kenyamanan ekstra dan fasilitas premium untuk setiap perjalanan Anda.', 'Kini perjalanan jauh terasa seperti di rumah sendiri. Nova Trans Executive menawarkan layanan transportasi kelas atas yang mengutamakan kenyamanan, keamanan, dan kualitas. Bus ini dirancang khusus untuk penumpang yang menginginkan perjalanan nyaman dengan sentuhan premium.\r\n\r\nDengan AC yang sejuk, toilet bersih, serta Wi-Fi gratis yang memungkinkan Anda tetap terhubung selama perjalanan, Nova Trans Executive menjadi pilihan ideal untuk perjalanan bisnis maupun liburan. Tak hanya itu, setiap penumpang juga mendapatkan snack gratis sebagai teman perjalanan.', 'Millenium Big Class.png', '2025-06-28 14:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL,
  `nama_pemesan` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tanggal_pemesanan` date DEFAULT NULL,
  `status` enum('Tersedia','Sudah Diplih','Temporary','Pending','Paid','Cancelled') DEFAULT NULL,
  `total_harga` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_bus` int(11) NOT NULL,
  `id_pemesan` int(11) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `kursi` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id_booking`, `nama_pemesan`, `no_telepon`, `email`, `tanggal_pemesanan`, `status`, `total_harga`, `created_at`, `updated_at`, `id_bus`, `id_pemesan`, `metode_pembayaran`, `tanggal_pembayaran`, `kursi`) VALUES
(2, 'andi alqaf', '082194392833', 'aalqafwiryawan1010@gmail.com', '2025-06-28', 'Paid', 185000.00, '2025-06-28 14:48:48', '2025-06-28 06:49:07', 1, NULL, 'COD', '2025-06-28 14:48:51', '1A');

-- --------------------------------------------------------

--
-- Table structure for table `data_bus`
--

CREATE TABLE `data_bus` (
  `id_bus` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `kota_asal` varchar(100) NOT NULL,
  `terminal_asal` varchar(100) DEFAULT NULL,
  `kota_tujuan` varchar(100) NOT NULL,
  `terminal_tujuan` varchar(100) DEFAULT NULL,
  `waktu_berangkat` datetime NOT NULL,
  `estimasi_waktu` time DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `harga_tiket` decimal(10,2) NOT NULL,
  `status` enum('Tersedia','Tidak Tersedia','Maintenance') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_bus`
--

INSERT INTO `data_bus` (`id_bus`, `id_kendaraan`, `nama_kelas`, `kota_asal`, `terminal_asal`, `kota_tujuan`, `terminal_tujuan`, `waktu_berangkat`, `estimasi_waktu`, `tanggal`, `fasilitas`, `harga_tiket`, `status`) VALUES
(1, 1, 'Ekonomi', 'Makassar', 'Terminal Daya', 'Sorowako', 'Terminal Sorowako', '2025-06-14 08:00:00', '03:30:00', '2025-06-16', 'AC, Toilet', 180000.00, 'Tersedia'),
(2, 2, 'Executive', 'Makassar', 'Terminal Daya', 'Palopo', 'Terminal Palopo', '2025-06-14 09:00:00', '08:00:00', '2025-06-16', 'AC, Toilet, WiFi, Snack', 135000.00, 'Tersedia'),
(3, 3, 'Ekonomi', 'Makassar', 'Terminal Daya', 'Pare-pare', 'Terminal Lumpue', '2025-06-17 09:00:00', '00:00:00', '2025-06-17', 'AC, Toilet', 100000.00, 'Tersedia'),
(7, 4, 'Executive', 'Makassar', 'Terminal Daya', 'Toraja', 'Terminal Toraja', '2025-06-20 08:30:00', '00:00:00', '2025-06-20', 'AC, Toilet, WiFi, Snack', 140000.00, 'Tersedia'),
(8, 5, 'Ekonomi', 'Makassar', 'Terminal Daya', 'Sidrap', 'Terminal Sidrap', '2025-06-22 03:00:00', '00:00:00', '2025-06-22', 'AC', 120000.00, 'Tersedia'),
(9, 6, 'Executive', 'Makassar', 'Terminal Daya', 'Bone', 'Terminal Bone', '2025-06-30 07:00:00', '00:00:00', '0000-00-00', 'AC, Toilet, WiFi, Snack', 150000.00, 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `data_pemesan`
--

CREATE TABLE `data_pemesan` (
  `id_pemesan` int(11) NOT NULL,
  `id_booking` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nomor_telepon` varchar(25) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_rute` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `waktu_keberangkatan` datetime NOT NULL,
  `waktu_kedatangan` datetime NOT NULL,
  `status` enum('terjadwal','berangkat','tiba','dibatalkan') DEFAULT 'terjadwal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_rute`, `id_kendaraan`, `waktu_keberangkatan`, `waktu_kedatangan`, `status`) VALUES
(2, 1, 1, '2025-05-22 15:16:47', '2025-05-22 15:16:47', 'terjadwal');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int(11) NOT NULL,
  `nomor_polisi` varchar(20) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `status` enum('tersedia','perawatan','digunakan') DEFAULT 'tersedia',
  `tanggal_perawatan_terakhir` date DEFAULT NULL,
  `tanggal_perawatan_berikutnya` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `nomor_polisi`, `jenis`, `kapasitas`, `status`, `tanggal_perawatan_terakhir`, `tanggal_perawatan_berikutnya`) VALUES
(1, 'DD 1233 BK', 'Bus Besar', 30, 'tersedia', '2025-06-20', '2025-07-12'),
(2, 'DD 1208 BN', 'Bus Besar', 30, 'tersedia', '2025-06-05', '2025-06-28'),
(3, 'DD 1010 BK', 'Bus Sedang', 20, 'tersedia', '2025-06-10', '2025-07-10'),
(4, 'DD 1210 AN', 'Bus Besar', 35, 'tersedia', '2025-06-20', '2025-07-20'),
(5, 'DD 2801 BN', 'Bus Sedang', 25, 'tersedia', '2025-06-22', '2025-07-22'),
(6, 'DD 1212 AN', 'Bus Besar', 40, 'tersedia', '2025-06-28', '2025-07-28');

-- --------------------------------------------------------

--
-- Table structure for table `kontak`
--

CREATE TABLE `kontak` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subjek` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `dikirim_pada` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontak`
--

INSERT INTO `kontak` (`id`, `nama_lengkap`, `email`, `subjek`, `pesan`, `dikirim_pada`) VALUES
(5, 'Andi Al Qaf', 'aalqafwiryawan1010@gmail.com', 'Pelayanan', 'Pelayanan Nova Trans sangat ramah', '2025-06-28 14:43:44');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pemesanan` int(11) NOT NULL,
  `id_penumpang` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `id_rute` int(25) DEFAULT NULL,
  `tanggal_pemesanan` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_harga` decimal(10,2) NOT NULL,
  `status_pembayaran` enum('menunggu','lunas','dibatalkan') DEFAULT 'menunggu',
  `status` enum('dikonfirmasi','dibatalkan','selesai') DEFAULT 'dikonfirmasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rute`
--

CREATE TABLE `rute` (
  `id_rute` int(11) NOT NULL,
  `asal` varchar(100) NOT NULL,
  `tujuan` varchar(100) NOT NULL,
  `estimasi_waktu` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rute`
--

INSERT INTO `rute` (`id_rute`, `asal`, `tujuan`, `estimasi_waktu`) VALUES
(1, 'Makassar', 'Toraja', '21:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `testimoni`
--

CREATE TABLE `testimoni` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_pengguna` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nomor_telepon` varchar(25) DEFAULT NULL,
  `nama_pengguna` varchar(255) NOT NULL,
  `login_terakhir` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_pengguna`, `email`, `password`, `role`, `created_at`, `nomor_telepon`, `nama_pengguna`, `login_terakhir`) VALUES
(1, 'andialqaf0810@gmail.com', '$2y$10$GLldssV.R50TzmlrFgE9/e.SFO66dX.aRUmdBSHHsYkVi9eixgo/y', 'admin', '2025-05-16 08:17:06', '082194392250', 'alqaf', '2025-05-20 12:33:48'),
(2, 'aalqafwiryawan1010@gmail.com', '$2y$10$VnYqsiHT0/5BVPUI0tF/UeAJqa.0JLsABhrBbzkyKato0PEsVqIQa', 'user', '2025-05-16 08:29:02', '082194399288', 'wiryawan', '2025-05-20 12:33:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `id_pemesan` (`id_pemesan`),
  ADD KEY `id_bus` (`id_bus`);

--
-- Indexes for table `data_bus`
--
ALTER TABLE `data_bus`
  ADD PRIMARY KEY (`id_bus`),
  ADD KEY `FK_bus_kendaraan` (`id_kendaraan`);

--
-- Indexes for table `data_pemesan`
--
ALTER TABLE `data_pemesan`
  ADD PRIMARY KEY (`id_pemesan`),
  ADD KEY `id_booking` (`id_booking`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_rute` (`id_rute`),
  ADD KEY `id_kendaraan` (`id_kendaraan`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD UNIQUE KEY `nomor_polisi` (`nomor_polisi`);

--
-- Indexes for table `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD UNIQUE KEY `id_penumpang` (`id_penumpang`),
  ADD UNIQUE KEY `id_jadwal` (`id_jadwal`),
  ADD KEY `id_rute` (`id_rute`);

--
-- Indexes for table `rute`
--
ALTER TABLE `rute`
  ADD PRIMARY KEY (`id_rute`);

--
-- Indexes for table `testimoni`
--
ALTER TABLE `testimoni`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_bus`
--
ALTER TABLE `data_bus`
  MODIFY `id_bus` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `data_pemesan`
--
ALTER TABLE `data_pemesan`
  MODIFY `id_pemesan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rute`
--
ALTER TABLE `rute`
  MODIFY `id_rute` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `testimoni`
--
ALTER TABLE `testimoni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_pemesan`) REFERENCES `data_pemesan` (`id_pemesan`) ON DELETE SET NULL;

--
-- Constraints for table `data_bus`
--
ALTER TABLE `data_bus`
  ADD CONSTRAINT `FK_bus_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`);

--
-- Constraints for table `data_pemesan`
--
ALTER TABLE `data_pemesan`
  ADD CONSTRAINT `data_pemesan_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`);

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_rute`) REFERENCES `rute` (`id_rute`),
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
