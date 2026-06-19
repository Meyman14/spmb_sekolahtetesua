-- HAPUS SEMUA DATA SISWA (uji coba) — TIDAK BISA DIBATALKAN
-- Tidak menghapus akun login (tabel users tetap ada)
-- Jalankan di phpMyAdmin: pilih database db_spmb → tab SQL → tempel → Go

USE db_spmb;

-- Kosongkan tabel siswa (draft, final, terdaftar)
TRUNCATE TABLE siswa;

-- Selesai. Jika ada file di folder uploads/, hapus manual lewat File Explorer:
-- C:\xampp\htdocs\spmb_sekolah\uploads\  (hapus isi file, biarkan folder & .htaccess)
