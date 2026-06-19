-- Tabel absensi harian petugas SPMB
USE db_spmb;

CREATE TABLE IF NOT EXISTS absensi_petugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('hadir') NOT NULL DEFAULT 'hadir',
    waktu_masuk DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_absensi_harian (username, tanggal),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
