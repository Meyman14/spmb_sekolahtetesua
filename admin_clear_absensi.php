<?php
/** Halaman admin: kosongkan data absensi dan opsi hapus session agar semua diminta absen ulang */
require_once 'includes/auth.php';
require_once 'koneksi.php';

requireAdmin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clearSessions = isset($_POST['clear_sessions']) && $_POST['clear_sessions'] === '1';

    // backup table dengan timestamp
    $backupName = 'daftar_hadir_backup_' . date('Ymd_His');
    $createSql = "CREATE TABLE `" . mysqli_real_escape_string($conn, $backupName) . "` AS SELECT * FROM daftar_hadir";
    $backupOk = mysqli_query($conn, $createSql);
    $backupMsg = $backupOk ? "Backup dibuat: {$backupName}" : 'Gagal membuat backup: ' . mysqli_error($conn);

    // Hapus semua data setelah backup
    mysqli_query($conn, "DELETE FROM daftar_hadir");
    $affected = mysqli_affected_rows($conn);

    $sessResult = '';
    if ($clearSessions) {
        $path = ini_get('session.save_path') ?: sys_get_temp_dir();
        $sessFiles = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'sess_*');
        $deleted = 0;
        if (is_array($sessFiles)) {
            foreach ($sessFiles as $f) {
                if (is_file($f)) {
                    @unlink($f);
                    $deleted++;
                }
            }
        }
        $sessResult = "; deleted {$deleted} session files from {$path}";
    }

    $message = "Sukses mengosongkan data absensi (hapus rows: {$affected}). " . $backupMsg . $sessResult;
}

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>
<main class="main-content app-page">
    <div class="container p-4">
        <h1 class="h4">Hapus Semua Data Absensi</h1>
        <p class="text-muted">Gunakan fungsi ini untuk mengosongkan tabel <code>daftar_hadir</code>. Pastikan melakukan backup sebelum menjalankan.</p>

        <?php if ($message !== ''): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post" onsubmit="return confirm('Yakin kosongkan semua data absensi? Tindakan ini tidak bisa dibatalkan.');">
            <div class="mb-3 form-check">
                <input type="checkbox" name="clear_sessions" value="1" id="clearSessions" class="form-check-input">
                <label for="clearSessions" class="form-check-label">Hapus file session (paksa semua user diminta absen ulang)</label>
            </div>
            <button class="btn btn-danger">Kosongkan Data Absensi</button>
            <a href="daftar_hadir.php" class="btn btn-secondary ms-2">Kembali</a>
        </form>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
