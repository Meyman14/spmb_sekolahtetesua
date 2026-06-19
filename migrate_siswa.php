<?php
/**
 * Jalankan sekali: http://localhost/spmb_sekolah/migrate_siswa.php
 * Setelah berhasil, halaman ini bisa dihapus.
 */
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';

ensureSiswaSchema($conn);

$page_title = 'Migrasi Database';
require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content">
    <div class="page-header">
        <h1>Migrasi Tabel Siswa</h1>
    </div>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i>
        Struktur tabel <strong>siswa</strong> sudah diperbarui. Semua kolom formulir pendaftaran tersedia.
        <hr class="my-2">
        <a href="input_murid.php" class="btn btn-primary btn-sm">Lanjut Input Murid</a>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
