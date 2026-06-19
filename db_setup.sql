-- Jalankan di phpMyAdmin untuk database db_spmb

CREATE DATABASE IF NOT EXISTS db_spmb;

USE db_spmb;



CREATE TABLE IF NOT EXISTS users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(50) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    nama VARCHAR(100) NOT NULL

);



INSERT IGNORE INTO users (username, password, nama) VALUES

('admin', '$2y$10$pwXlZGZQYWOlH2y4q8Hx4u1FvdbX8ii7QPkSdTjaKNqc89xENbtXS', 'Administrator');



CREATE TABLE IF NOT EXISTS siswa (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nama_lengkap VARCHAR(150) NOT NULL,

    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,

    nisn VARCHAR(20) DEFAULT NULL,

    nik VARCHAR(20) DEFAULT NULL,

    no_kk VARCHAR(20) DEFAULT NULL,

    tempat_lahir VARCHAR(80) DEFAULT NULL,

    tanggal_lahir DATE DEFAULT NULL,

    agama ENUM('Kristen', 'Katolik', 'Islam', 'Hindu', 'Buddha', 'Konghucu') NOT NULL,

    berkebutuhan_khusus VARCHAR(80) DEFAULT 'Tidak',

    alamat TEXT,

    dusun VARCHAR(80) DEFAULT NULL,

    kelurahan VARCHAR(80) DEFAULT NULL,

    kecamatan VARCHAR(80) DEFAULT NULL,

    kode_pos VARCHAR(10) DEFAULT NULL,

    moda_transportasi VARCHAR(80) DEFAULT NULL,

    anak_keberapa TINYINT DEFAULT NULL,

    nama_ayah VARCHAR(100) DEFAULT NULL,

    nik_ayah VARCHAR(20) DEFAULT NULL,

    tahun_lahir_ayah SMALLINT DEFAULT NULL,

    pendidikan_ayah VARCHAR(50) DEFAULT NULL,

    pekerjaan_ayah VARCHAR(80) DEFAULT NULL,

    nama_ibu VARCHAR(100) DEFAULT NULL,

    nik_ibu VARCHAR(20) DEFAULT NULL,

    tahun_lahir_ibu SMALLINT DEFAULT NULL,

    pendidikan_ibu VARCHAR(50) DEFAULT NULL,

    pekerjaan_ibu VARCHAR(80) DEFAULT NULL,

    nama_wali VARCHAR(100) DEFAULT NULL,

    nik_wali VARCHAR(20) DEFAULT NULL,

    tahun_lahir_wali SMALLINT DEFAULT NULL,

    pendidikan_wali VARCHAR(50) DEFAULT NULL,

    pekerjaan_wali VARCHAR(80) DEFAULT NULL,

    no_hp VARCHAR(20) DEFAULT NULL,

    email VARCHAR(100) DEFAULT NULL,

    tinggi_badan DECIMAL(5,2) DEFAULT NULL,

    berat_badan DECIMAL(5,2) DEFAULT NULL,

    lingkar_kepala DECIMAL(5,2) DEFAULT NULL,

    jarak_sekolah DECIMAL(6,2) DEFAULT NULL,

    waktu_tempuh VARCHAR(50) DEFAULT NULL,

    jumlah_saudara TINYINT DEFAULT NULL,

    file_kk VARCHAR(255) DEFAULT NULL,

    file_akte VARCHAR(255) DEFAULT NULL,

    file_ijazah VARCHAR(255) DEFAULT NULL,

    file_kks VARCHAR(255) DEFAULT NULL,

    file_foto VARCHAR(255) DEFAULT NULL,

    file_surat_orangtua VARCHAR(255) DEFAULT NULL,

    status ENUM('draft', 'final') NOT NULL DEFAULT 'draft',

    tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


