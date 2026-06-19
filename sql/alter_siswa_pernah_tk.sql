-- Kolom riwayat TK pada tabel siswa
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS pernah_tk ENUM('Ya','Tidak') DEFAULT NULL AFTER nisn;
ALTER TABLE siswa ADD COLUMN IF NOT EXISTS nama_tk VARCHAR(150) DEFAULT NULL AFTER pernah_tk;
