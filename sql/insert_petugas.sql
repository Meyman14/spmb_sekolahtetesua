-- Akun petugas SPMB — password semua: spmbtts2026
-- Username = nama (huruf kecil), kolom nama = nama lengkap untuk tampilan
USE db_spmb;

DELETE FROM users WHERE level = 'petugas';

INSERT INTO users (username, password, nama, level) VALUES
('maulidanur', 'spmbtts2026', 'Maulidanur', 'petugas'),
('saribadi', 'spmbtts2026', 'Saribadi', 'petugas'),
('nerius', 'spmbtts2026', 'Nerius', 'petugas'),
('kasih', 'spmbtts2026', 'Kasih', 'petugas'),
('anurni', 'spmbtts2026', 'Anurni', 'petugas'),
('iffan', 'spmbtts2026', 'Iffan', 'petugas'),
('agustina', 'spmbtts2026', 'Agustina', 'petugas'),
('alimmarwin', 'spmbtts2026', 'Alimmarwin', 'petugas'),
('ervinta', 'spmbtts2026', 'Ervinta', 'petugas'),
('faebuadodo', 'spmbtts2026', 'Faebuadodo', 'petugas'),
('menifati', 'spmbtts2026', 'Menifati', 'petugas'),
('yunidar', 'spmbtts2026', 'Yunidar', 'petugas'),
('lidia', 'spmbtts2026', 'Lidia', 'petugas'),
('meylestari', 'spmbtts2026', 'Meylestari', 'petugas'),
('nirani', 'spmbtts2026', 'Nirani', 'petugas'),
('niska', 'spmbtts2026', 'Niska', 'petugas'),
('theresia', 'spmbtts2026', 'Theresia', 'petugas'),
('yanania', 'spmbtts2026', 'Yanania', 'petugas'),
('merlina', 'spmbtts2026', 'Merlina', 'petugas'),
('niatasri', 'spmbtts2026', 'Niatasri', 'petugas');
