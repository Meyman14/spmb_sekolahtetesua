-- Hapus semua data siswa berstatus draft (uji coba baru)
USE db_spmb;
DELETE FROM siswa WHERE status = 'draft';
