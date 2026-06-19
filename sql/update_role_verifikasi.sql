-- Update untuk sistem peran admin/petugas dan verifikasi dokumen
USE db_spmb;

ALTER TABLE siswa MODIFY COLUMN status ENUM('draft','final','terdaftar') NOT NULL DEFAULT 'draft';

ALTER TABLE siswa ADD COLUMN IF NOT EXISTS verif_file_kk ENUM('belum','verifikasi','batal') NOT NULL DEFAULT 'belum';
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS verif_file_akte ENUM('belum','verifikasi','batal') NOT NULL DEFAULT 'belum';
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS verif_file_ijazah ENUM('belum','verifikasi','batal') NOT NULL DEFAULT 'belum';
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS verif_file_kks ENUM('belum','verifikasi','batal') NOT NULL DEFAULT 'belum';
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS verif_file_foto ENUM('belum','verifikasi','batal') NOT NULL DEFAULT 'belum';
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS verif_file_surat_orangtua ENUM('belum','verifikasi','batal') NOT NULL DEFAULT 'belum';

-- Contoh akun (password plain text sesuai login_proses.php saat ini)
-- INSERT INTO users (username, password, nama, level) VALUES
-- ('petugas1', 'petugas123', 'Petugas SPMB', 'petugas');
