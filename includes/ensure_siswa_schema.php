<?php

/**
 * Menambah kolom tabel siswa jika belum ada (upgrade dari struktur lama).
 */
function ensureSiswaSchema(mysqli $conn): void
{
    $tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'siswa'");
    if (!$tableCheck || mysqli_num_rows($tableCheck) === 0) {
        mysqli_query($conn, "CREATE TABLE siswa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_lengkap VARCHAR(150) NOT NULL,
            jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
            agama ENUM('Kristen', 'Katolik', 'Islam', 'Hindu', 'Buddha', 'Konghucu') NOT NULL,
            tempat_lahir VARCHAR(80) DEFAULT NULL,
            tanggal_lahir DATE DEFAULT NULL,
            alamat TEXT,
            status ENUM('draft', 'final') NOT NULL DEFAULT 'draft',
            tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    $columns = [
        'petugas_username' => 'VARCHAR(50) DEFAULT NULL',
        'petugas_nama' => 'VARCHAR(100) DEFAULT NULL',
        'tanggal_pendaftaran' => 'DATE DEFAULT NULL',
        'tempat_lahir' => 'VARCHAR(80) DEFAULT NULL',
        'tanggal_lahir' => 'DATE DEFAULT NULL',
        'alamat' => 'TEXT',
        'nisn' => 'VARCHAR(20) DEFAULT NULL',
        'pernah_tk' => "ENUM('Ya','Tidak') DEFAULT NULL",
        'nama_tk' => 'VARCHAR(150) DEFAULT NULL',
        'nik' => 'VARCHAR(20) DEFAULT NULL',
        'no_kk' => 'VARCHAR(20) DEFAULT NULL',
        'berkebutuhan_khusus' => "VARCHAR(80) DEFAULT 'Tidak'",
        'dusun' => 'VARCHAR(80) DEFAULT NULL',
        'kelurahan' => 'VARCHAR(80) DEFAULT NULL',
        'kecamatan' => 'VARCHAR(80) DEFAULT NULL',
        'kode_pos' => 'VARCHAR(10) DEFAULT NULL',
        'moda_transportasi' => 'VARCHAR(80) DEFAULT NULL',
        'anak_keberapa' => 'TINYINT DEFAULT NULL',
        'nama_ayah' => 'VARCHAR(100) DEFAULT NULL',
        'nik_ayah' => 'VARCHAR(20) DEFAULT NULL',
        'tahun_lahir_ayah' => 'SMALLINT DEFAULT NULL',
        'pendidikan_ayah' => 'VARCHAR(50) DEFAULT NULL',
        'pekerjaan_ayah' => 'VARCHAR(80) DEFAULT NULL',
        'nama_ibu' => 'VARCHAR(100) DEFAULT NULL',
        'nik_ibu' => 'VARCHAR(20) DEFAULT NULL',
        'tahun_lahir_ibu' => 'SMALLINT DEFAULT NULL',
        'pendidikan_ibu' => 'VARCHAR(50) DEFAULT NULL',
        'pekerjaan_ibu' => 'VARCHAR(80) DEFAULT NULL',
        'nama_wali' => 'VARCHAR(100) DEFAULT NULL',
        'nik_wali' => 'VARCHAR(20) DEFAULT NULL',
        'tahun_lahir_wali' => 'SMALLINT DEFAULT NULL',
        'pendidikan_wali' => 'VARCHAR(50) DEFAULT NULL',
        'pekerjaan_wali' => 'VARCHAR(80) DEFAULT NULL',
        'no_hp' => 'VARCHAR(20) DEFAULT NULL',
        'email' => 'VARCHAR(100) DEFAULT NULL',
        'tinggi_badan' => 'DECIMAL(5,2) DEFAULT NULL',
        'berat_badan' => 'DECIMAL(5,2) DEFAULT NULL',
        'lingkar_kepala' => 'DECIMAL(5,2) DEFAULT NULL',
        'jarak_sekolah' => 'DECIMAL(6,2) DEFAULT NULL',
        'waktu_tempuh' => 'VARCHAR(50) DEFAULT NULL',
        'jumlah_saudara' => 'TINYINT DEFAULT NULL',
        'hobi' => 'VARCHAR(150) DEFAULT NULL',
        'cita_cita' => 'VARCHAR(150) DEFAULT NULL',
        'file_kk' => 'VARCHAR(255) DEFAULT NULL',
        'file_akte' => 'VARCHAR(255) DEFAULT NULL',
        'file_ijazah' => 'VARCHAR(255) DEFAULT NULL',
        'file_kks' => 'VARCHAR(255) DEFAULT NULL',
        'file_foto' => 'VARCHAR(255) DEFAULT NULL',
        'file_surat_orangtua' => 'VARCHAR(255) DEFAULT NULL',
    ];

    foreach ($columns as $name => $definition) {
        $safeName = mysqli_real_escape_string($conn, $name);
        $result = mysqli_query($conn, "SHOW COLUMNS FROM siswa LIKE '$safeName'");
        if ($result && mysqli_num_rows($result) === 0) {
            mysqli_query($conn, "ALTER TABLE siswa ADD COLUMN `$name` $definition");
        }
    }

    // Perlebar nama_lengkap jika masih VARCHAR(100)
    mysqli_query($conn, 'ALTER TABLE siswa MODIFY COLUMN nama_lengkap VARCHAR(150) NOT NULL');

    // Status: draft, final (menunggu verifikasi), terdaftar (lolos verifikasi)
    mysqli_query($conn, "ALTER TABLE siswa MODIFY COLUMN status ENUM('draft','final','terdaftar') NOT NULL DEFAULT 'draft'");

    // Perluas pilihan agama sesuai formulir
    mysqli_query($conn, "ALTER TABLE siswa MODIFY COLUMN agama ENUM('Kristen','Katolik','Islam','Hindu','Buddha','Konghucu') NULL DEFAULT NULL");

    require_once __DIR__ . '/dokumen.php';
    foreach (daftarDokumenSiswa() as $info) {
        $col = $info['verif_col'];
        $safeCol = mysqli_real_escape_string($conn, $col);
        $result = mysqli_query($conn, "SHOW COLUMNS FROM siswa LIKE '$safeCol'");
        if ($result && mysqli_num_rows($result) === 0) {
            mysqli_query($conn, "ALTER TABLE siswa ADD COLUMN `$col` ENUM('belum','verifikasi','batal') NOT NULL DEFAULT 'belum'");
        }
    }
}
