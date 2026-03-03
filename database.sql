-- Database: db_parkit_ukk

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','petugas','owner') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data for users (Password: 123456 -> md5)
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('admin', MD5('admin123'), 'Administrator Utama', 'admin'),
('petugas', MD5('petugas123'), 'Petugas Parkir 1', 'petugas'),
('owner', MD5('owner123'), 'Pemilik Bisnis', 'owner');

-- --------------------------------------------------------

-- Table structure for table `jenis_kendaraan` (stores tarif as well)
CREATE TABLE `jenis_kendaraan` (
  `id_jenis` int(11) NOT NULL AUTO_INCREMENT,
  `nama_jenis` varchar(50) NOT NULL,
  `tarif_per_jam` int(11) NOT NULL,
  PRIMARY KEY (`id_jenis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `jenis_kendaraan` (`nama_jenis`, `tarif_per_jam`) VALUES
('Motor', 2000),
('Mobil', 5000),
('Truk/Bus', 10000);

-- --------------------------------------------------------

-- Table structure for table `area_parkir`
CREATE TABLE `area_parkir` (
  `id_area` int(11) NOT NULL AUTO_INCREMENT,
  `nama_area` varchar(50) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `terisi` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_area`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `area_parkir` (`nama_area`, `kapasitas`, `terisi`) VALUES
('Lantai 1 (Motor)', 100, 0),
('Lantai 2 (Mobil)', 50, 0);

-- --------------------------------------------------------

-- Table structure for table `transaksi`
CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(20) NOT NULL,
  `plat_nomor` varchar(15) NOT NULL,
  `id_jenis` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `jam_masuk` datetime NOT NULL,
  `jam_keluar` datetime DEFAULT NULL,
  `biaya` int(11) DEFAULT 0,
  `status` enum('masuk','keluar') NOT NULL DEFAULT 'masuk',
  `id_petugas` int(11) NOT NULL,
  PRIMARY KEY (`id_transaksi`),
  KEY `id_jenis` (`id_jenis`),
  KEY `id_area` (`id_area`),
  KEY `id_petugas` (`id_petugas`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `log_aktivitas`
CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `aktivitas` text NOT NULL,
  `waktu` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Constraints
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_jenis` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_kendaraan` (`id_jenis`),
  ADD CONSTRAINT `fk_transaksi_area` FOREIGN KEY (`id_area`) REFERENCES `area_parkir` (`id_area`),
  ADD CONSTRAINT `fk_transaksi_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `users` (`id_user`);

ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

COMMIT;
