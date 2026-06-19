<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';
require_once 'includes/dokumen.php';

requireAdmin();
ensureSiswaSchema($conn);

$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id = ? AND status = 'final' LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$siswa) {
    header('Location: status_final.php');
    exit;
}

$page_title = 'Verifikasi Dokumen';
$active_menu = 'verifikasi';
$dokumenList = daftarDokumenSiswa();

$formError = $_SESSION['form_error'] ?? '';
unset($_SESSION['form_error']);

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <a href="status_final.php" class="btn btn-sm btn-outline-primary mb-2" style="position:relative;z-index:1;">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <?php
    $page_heading = 'Verifikasi Dokumen';
    $page_subtitle = htmlspecialchars($siswa['nama_lengkap']) . ' — menunggu verifikasi';
    $page_header_theme = 'blue';
    $page_header_icon = 'bi-shield-check';
    require 'includes/page_header.php';
    ?>

    <?php if ($formError !== ''): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($formError); ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card content-card">
                <div class="card-header">Identitas Murid</div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted">Petugas Pendaftar</td>
                            <td>
                                <strong><?php echo htmlspecialchars(namaPetugasPendaftar($siswa)); ?></strong>
                                <?php if (!empty($siswa['petugas_username'])): ?>
                                <br><small class="text-muted">@<?php echo htmlspecialchars($siswa['petugas_username']); ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr><td class="text-muted">NISN</td><td><?php echo htmlspecialchars($siswa['nisn'] ?? '-'); ?></td></tr>
                        <tr><td class="text-muted">Pernah TK</td><td><?php echo htmlspecialchars($siswa['pernah_tk'] ?? '-'); ?></td></tr>
                        <?php if (($siswa['pernah_tk'] ?? '') === 'Ya'): ?>
                        <tr><td class="text-muted">Nama TK</td><td><?php echo htmlspecialchars($siswa['nama_tk'] ?? '-'); ?></td></tr>
                        <?php endif; ?>
                        <tr><td class="text-muted">Nama</td><td><?php echo htmlspecialchars($siswa['nama_lengkap']); ?></td></tr>
                        <tr><td class="text-muted">Jenis Kelamin</td><td><?php echo htmlspecialchars($siswa['jenis_kelamin'] ?? '-'); ?></td></tr>
                        <tr><td class="text-muted">Agama</td><td><?php echo htmlspecialchars($siswa['agama'] ?? '-'); ?></td></tr>
                        <tr><td class="text-muted">No. HP</td><td><?php echo htmlspecialchars($siswa['no_hp'] ?? '-'); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card content-card">
                <div class="card-header">Verifikasi Berkas</div>
                <div class="card-body">
                    <form method="post" action="verifikasi_proses.php">
                        <input type="hidden" name="siswa_id" value="<?php echo (int) $siswa['id']; ?>">

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Dokumen</th>
                                        <th>File</th>
                                        <th width="220">Keputusan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dokumenList as $field => $info):
                                        $path = $siswa[$field] ?? '';
                                        $verif = $siswa[$info['verif_col']] ?? 'belum';
                                        $radioName = 'verif[' . $field . ']';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($info['label']); ?></td>
                                        <td>
                                            <?php if ($path !== ''): ?>
                                            <a href="<?php echo htmlspecialchars($path); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                            <?php else: ?>
                                            <span class="text-muted small">Belum diunggah</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($path !== ''): ?>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="<?php echo $radioName; ?>" id="<?php echo $field; ?>_ok" value="verifikasi" <?php echo $verif === 'verifikasi' ? 'checked' : ''; ?> required>
                                                <label class="btn btn-outline-success" for="<?php echo $field; ?>_ok">Verifikasi</label>

                                                <input type="radio" class="btn-check" name="<?php echo $radioName; ?>" id="<?php echo $field; ?>_no" value="batal" <?php echo $verif === 'batal' ? 'checked' : ''; ?>>
                                                <label class="btn btn-outline-danger" for="<?php echo $field; ?>_no">Batal</label>
                                            </div>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info small mt-3 mb-3">
                            Semua dokumen yang diunggah harus dipilih <strong>Verifikasi</strong> agar siswa masuk menu Terdaftar.
                            Jika ada satu dokumen <strong>Batal</strong>, status dikembalikan ke Draft.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Simpan Verifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
