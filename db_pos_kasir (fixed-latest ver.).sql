-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 02, 2025 at 07:53 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pos_kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int NOT NULL,
  `id_kategori` int NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `satuan_barang` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tgl_input` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `id_kategori`, `nama_barang`, `harga_beli`, `harga_jual`, `satuan_barang`, `tgl_input`, `tgl_update`) VALUES
(1, 3, 'Indomie', '2000.00', '3500.00', 'pcs', '2025-07-01 08:30:14', '2025-07-01 08:30:14'),
(2, 5, 'pulpen', '5000.00', '6000.00', 'pcs', '2025-07-02 00:33:01', '2025-07-02 00:33:01'),
(3, 5, 'buku', '4000.00', '5000.00', 'pcs', '2025-07-02 00:33:41', '2025-07-02 06:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `detailtransaksi`
--

CREATE TABLE `detailtransaksi` (
  `id_detail` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `id_barang` int NOT NULL,
  `harga_saat_transaksi` decimal(15,2) NOT NULL,
  `jumlah` int NOT NULL,
  `sub_total` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detailtransaksi`
--

INSERT INTO `detailtransaksi` (`id_detail`, `id_transaksi`, `id_barang`, `harga_saat_transaksi`, `jumlah`, `sub_total`) VALUES
(1, 1, 3, '5000.00', 10, '50000.00'),
(2, 1, 1, '3500.00', 1, '3500.00'),
(3, 1, 2, '6000.00', 1, '6000.00'),
(4, 2, 1, '3500.00', 2, '7000.00'),
(5, 2, 2, '6000.00', 2, '12000.00');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tgl_input` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `tgl_input`, `tgl_update`) VALUES
(1, 'Keperluan Rumah Tangga', '2025-06-20 12:24:44', '2025-06-20 12:25:47'),
(2, 'Elektronik', '2025-06-20 12:24:53', '2025-06-20 12:24:53'),
(3, 'Makanan', '2025-06-20 12:24:59', '2025-06-20 12:24:59'),
(4, 'Minuman', '2025-06-20 12:25:05', '2025-06-20 12:25:05'),
(5, 'Alat Tulis', '2025-06-20 12:25:23', '2025-06-20 12:25:23');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id_member` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_member` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat_member` text COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_registrasi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id_member`, `username`, `password`, `nama_member`, `alamat_member`, `email`, `telepon`, `tgl_registrasi`) VALUES
(1, NULL, NULL, 'Semaradana', 'Tabanan', 'semara@gmail.com', '082266369017', '2025-07-02 00:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int NOT NULL,
  `id_toko` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','kasir','manajer') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'kasir',
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `id_toko`, `username`, `password`, `nama_lengkap`, `role`, `status_aktif`) VALUES
(1, 1, 'admin01', '$2y$10$0zCZoz7cQG2dah2tP26YH.WDiovZw7TZTQsztDCvNeQHs6NoKoKjG', 'administrator', 'admin', 1),
(2, 1, 'manajer01', '$2y$10$liMd5cQN1GeWQsDJyAMOhu0bfALvTvmCkFKiiS7CyMtZTc0wjes2a', 'Manajer', 'manajer', 1),
(3, 1, 'kasir01', '$2y$10$BSHHcXJJ3b2BvYVX8ZU1mOOlwgOeRh.8BHNThlTVabSZdj6ky5vyO', 'Kasir', 'kasir', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stoktoko`
--

CREATE TABLE `stoktoko` (
  `id_stok` int NOT NULL,
  `id_toko` int NOT NULL,
  `id_barang` int NOT NULL,
  `jumlah_stok` int NOT NULL,
  `tgl_update_stok` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stoktoko`
--

INSERT INTO `stoktoko` (`id_stok`, `id_toko`, `id_barang`, `jumlah_stok`, `tgl_update_stok`) VALUES
(1, 1, 1, 47, '2025-07-02 06:40:12'),
(2, 1, 2, 17, '2025-07-02 06:40:12'),
(3, 1, 3, 20, '2025-07-02 06:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE `toko` (
  `id_toko` int NOT NULL,
  `nama_toko` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat_toko` text COLLATE utf8mb4_general_ci NOT NULL,
  `tlp` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'gambar.jpg',
  `nama_pemilik` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toko`
--

INSERT INTO `toko` (`id_toko`, `nama_toko`, `alamat_toko`, `tlp`, `image`, `nama_pemilik`) VALUES
(1, 'Eternal Store', 'Denpasar', '081234567890', '', 'Semara');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_member` int DEFAULT NULL,
  `id_pengguna` int NOT NULL,
  `id_toko` int NOT NULL,
  `waktu_transaksi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(15,2) NOT NULL,
  `jumlah_bayar` decimal(15,2) DEFAULT NULL,
  `kembalian` decimal(15,2) DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_member`, `id_pengguna`, `id_toko`, `waktu_transaksi`, `total`, `jumlah_bayar`, `kembalian`, `catatan`) VALUES
(1, 1, 3, 1, '2025-07-02 00:35:17', '53550.00', '60000.00', '6450.00', NULL),
(2, 1, 3, 1, '2025-07-02 06:40:12', '17100.00', '20000.00', '2900.00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id_member`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `pengguna_ibfk_1` (`id_toko`);

--
-- Indexes for table `stoktoko`
--
ALTER TABLE `stoktoko`
  ADD PRIMARY KEY (`id_stok`),
  ADD UNIQUE KEY `unique_toko_barang` (`id_toko`,`id_barang`),
  ADD KEY `stoktoko_ibfk_1` (`id_toko`),
  ADD KEY `stoktoko_ibfk_2` (`id_barang`);

--
-- Indexes for table `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`id_toko`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `transaksi_ibfk_1` (`id_member`),
  ADD KEY `transaksi_ibfk_2` (`id_pengguna`),
  ADD KEY `transaksi_ibfk_3` (`id_toko`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id_member` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stoktoko`
--
ALTER TABLE `stoktoko`
  MODIFY `id_stok` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `toko`
--
ALTER TABLE `toko`
  MODIFY `id_toko` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE;

--
-- Constraints for table `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  ADD CONSTRAINT `detailtransaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detailtransaksi_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE;

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON UPDATE CASCADE;

--
-- Constraints for table `stoktoko`
--
ALTER TABLE `stoktoko`
  ADD CONSTRAINT `stoktoko_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stoktoko_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `member` (`id_member`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
