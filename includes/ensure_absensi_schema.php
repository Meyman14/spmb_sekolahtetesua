<?php

/**
 * Schema absensi petugas + data wajah referensi di users
 */
function ensureAbsensiSchema(mysqli $conn): void
{
    // Tabel kehadiran harian (sesuai permintaan: daftar_hadir)
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS daftar_hadir (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        nama_petugas VARCHAR(100) NOT NULL,
        tanggal DATE NOT NULL,
        status ENUM('hadir') NOT NULL DEFAULT 'hadir',
        waktu_masuk DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        metode_verifikasi VARCHAR(20) DEFAULT 'wajah',
        UNIQUE KEY uq_hadir_harian (username, tanggal),
        INDEX idx_tanggal (tanggal)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Migrasi data lama absensi_petugas → daftar_hadir (sekali)
    $cekLama = mysqli_query($conn, "SHOW TABLES LIKE 'absensi_petugas'");
    if ($cekLama && mysqli_num_rows($cekLama) > 0) {
        mysqli_query($conn, "INSERT IGNORE INTO daftar_hadir (username, nama_petugas, tanggal, status, waktu_masuk, metode_verifikasi)
            SELECT username, nama_petugas, tanggal, status, waktu_masuk, 'manual' FROM absensi_petugas");
    }

    // Hapus kolom foto referensi / descriptor pada users jika ada (menghapus fitur foto referensi)
    $resDrop = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'foto_wajah'");
    if ($resDrop && mysqli_num_rows($resDrop) > 0) {
        mysqli_query($conn, "ALTER TABLE users DROP COLUMN foto_wajah");
    }
    $resDrop2 = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'face_descriptor'");
    if ($resDrop2 && mysqli_num_rows($resDrop2) > 0) {
        mysqli_query($conn, "ALTER TABLE users DROP COLUMN face_descriptor");
    }

    // Pastikan kolom foto_petugas ada di tabel users untuk menyimpan foto terakhir saat absen
    $resPet = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'foto_petugas'");
    if ($resPet && mysqli_num_rows($resPet) === 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN `foto_petugas` VARCHAR(255) DEFAULT NULL");
    }

    // Pastikan kolom foto_absen ada di tabel daftar_hadir (backward compat)
    $res2 = mysqli_query($conn, "SHOW COLUMNS FROM daftar_hadir LIKE 'foto_absen'");
    if ($res2 && mysqli_num_rows($res2) === 0) {
        mysqli_query($conn, "ALTER TABLE daftar_hadir ADD COLUMN `foto_absen` VARCHAR(255) DEFAULT NULL");
    }

    // Pastikan kolom foto_kehadiran ada di tabel daftar_hadir (capture saat absen)
    $res3 = mysqli_query($conn, "SHOW COLUMNS FROM daftar_hadir LIKE 'foto_kehadiran'");
    if ($res3 && mysqli_num_rows($res3) === 0) {
        mysqli_query($conn, "ALTER TABLE daftar_hadir ADD COLUMN `foto_kehadiran` VARCHAR(255) DEFAULT NULL");
    }
}
