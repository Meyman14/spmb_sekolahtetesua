<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';

requireAdmin();
require_once 'includes/helpers_date.php';
ensureAbsensiSchema($conn);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: daftar_hadir.php');
    exit;
}

 $stmt = mysqli_prepare($conn, "SELECT d.id, d.username, d.nama_petugas, d.tanggal, d.waktu_masuk, d.metode_verifikasi, d.foto_kehadiran, d.foto_absen
     FROM daftar_hadir d
     WHERE d.id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
    header('Location: daftar_hadir.php');
    exit;
}

$page_title = 'Detail Kehadiran';
$active_menu = 'daftar_hadir';

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <div class="container-fluid p-4">
        <a href="daftar_hadir.php" class="btn btn-sm btn-outline-primary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
        <div class="card">
            <div class="card-header">Detail Kehadiran</div>
                    <div class="card-body text-center">
                        <?php if (!empty($row['foto_kehadiran'])): ?>
                            <img src="<?php echo htmlspecialchars($row['foto_kehadiran']); ?>" alt="foto kehadiran" style="width:160px;height:160px;object-fit:cover;border-radius:8px;" />
                        <?php elseif (!empty($row['foto_absen'])): ?>
                            <img src="<?php echo htmlspecialchars($row['foto_absen']); ?>" alt="foto absen" style="width:160px;height:160px;object-fit:cover;border-radius:8px;" />
                        <?php /* foto referensi dihapus */ ?>
                        <?php else: ?>
                            <div style="width:160px;height:160px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:8px;">No Photo</div>
                        <?php endif; ?>

                <h5 class="mt-3"><?php echo htmlspecialchars($row['nama_petugas']); ?> <small class="text-muted">(@<?php echo htmlspecialchars($row['username']); ?>)</small></h5>
                <p class="text-muted">Tanggal: <?php echo htmlspecialchars(format_date_indonesia($row['tanggal'])); ?></p>
                <p class="text-muted">Waktu Masuk: <?php echo htmlspecialchars($row['waktu_masuk']); ?></p>
                <p class="text-muted">Metode: <?php echo htmlspecialchars($row['metode_verifikasi']); ?></p>
                <?php if (isAdmin() && !empty($row['foto_kehadiran'] ?? '') || isAdmin() && !empty($row['foto_absen'] ?? '')): ?>
                    <p class="mt-2">
                        <a href="download_foto_kehadiran.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download"></i> Unduh Foto
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
