<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';
require_once 'includes/dokumen.php';

requirePetugas();
ensureSiswaSchema($conn);

$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id = ? AND status IN ('draft','final') LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$siswa) {
    header('Location: status_draft.php');
    exit;
}

$page_title = 'Detail Siswa';
$active_menu = $siswa['status'] === 'draft' ? 'draft' : 'final';
$dokumenList = daftarDokumenSiswa();
$backUrl = $siswa['status'] === 'draft' ? 'status_draft.php' : 'status_final.php';
$statusLabel = $siswa['status'] === 'draft' ? 'Draft' : 'Menunggu Verifikasi';

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="<?php echo $backUrl; ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <?php if ($siswa['status'] === 'draft'): ?>
        <a href="input_murid.php?edit=<?php echo (int) $siswa['id']; ?>" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> Lengkapi / Edit
        </a>
        <?php endif; ?>
        <a href="print_murid_pdf.php?id=<?php echo (int) $siswa['id']; ?>" target="_blank" class="btn btn-sm btn-primary">
            <i class="bi bi-printer"></i> Cetak PDF
        </a>
    </div>

    <div class="page-header-banner page-header-orange mb-3 d-flex align-items-center gap-3">
        <i class="bi bi-person-vcard fs-3"></i>
        <div>
            <h1 class="h5 mb-0"><?php echo htmlspecialchars($siswa['nama_lengkap']); ?></h1>
            <p class="mb-0 small opacity-90">Status: <?php echo htmlspecialchars($statusLabel); ?></p>
        </div>
    </div>

    <?php require __DIR__ . '/includes/tampilan_identitas_siswa.php'; ?>
</main>

<?php require_once 'includes/footer.php'; ?>
