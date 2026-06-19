-- Schema absensi wajah + daftar_hadir
USE db_spmb;

CREATE TABLE IF NOT EXISTS daftar_hadir (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('hadir') NOT NULL DEFAULT 'hadir',
    waktu_masuk DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    metode_verifikasi VARCHAR(20) DEFAULT 'wajah',
    UNIQUE KEY uq_hadir_harian (username, tanggal),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS foto_wajah VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS face_descriptor LONGTEXT DEFAULT NULL;

-- Catatan: MySQL < 8.0.12 tidak mendukung IF NOT EXISTS pada ADD COLUMN
-- Jalankan manual jika error:
-- ALTER TABLE users ADD COLUMN foto_wajah VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE users ADD COLUMN face_descriptor LONGTEXT DEFAULT NULL;
