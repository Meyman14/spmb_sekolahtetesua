<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';

ensureSiswaSchema($conn);

$page_title = isAdmin() ? 'Verifikasi Dokumen' : 'Menunggu Verifikasi';
$active_menu = isAdmin() ? 'verifikasi' : 'final';

$kolomPetugas = isAdmin() ? ', petugas_username, petugas_nama' : '';
$query = mysqli_query($conn, "SELECT id, nisn, nama_lengkap, jenis_kelamin, agama, tanggal_daftar $kolomPetugas
    FROM siswa WHERE status = 'final' ORDER BY tanggal_daftar DESC");

$formSuccess = $_SESSION['form_success'] ?? '';
unset($_SESSION['form_success']);

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <?php
    $page_heading = isAdmin() ? 'Verifikasi Dokumen' : 'Menunggu Verifikasi';
    $page_subtitle = isAdmin()
        ? 'Verifikasi berkas calon murid dari petugas pendaftar'
        : 'Data Final Anda sedang menunggu verifikasi admin';
    $page_header_theme = isAdmin() ? 'blue' : 'purple';
    $page_header_icon = isAdmin() ? 'bi-shield-check' : 'bi-hourglass-split';
    require 'includes/page_header.php';
    ?>

    <?php if ($formSuccess !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo htmlspecialchars($formSuccess); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card content-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Menunggu Verifikasi</span>
            <span class="badge bg-secondary"><?php echo mysqli_num_rows($query); ?> data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-spmb table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>Agama</th>
                            <th>Tanggal Daftar</th>
                            <?php if (isAdmin()): ?><th>Petugas Pendaftar</th><?php endif; ?>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($query) > 0):
                            while ($row = mysqli_fetch_assoc($query)):
                                $detailUrl = isAdmin()
                                    ? 'verifikasi_detail.php?id=' . (int) $row['id']
                                    : 'siswa_detail.php?id=' . (int) $row['id'];
                                $btnLabel = isAdmin() ? 'Verifikasi' : 'Lihat';
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nisn'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                            <td><?php echo htmlspecialchars($row['agama']); ?></td>
                            <td><?php echo $row['tanggal_daftar'] ? date('d/m/Y', strtotime($row['tanggal_daftar'])) : '-'; ?></td>
                            <?php if (isAdmin()): ?>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <i class="bi bi-person-badge"></i>
                                    <?php echo htmlspecialchars(namaPetugasPendaftar($row)); ?>
                                </span>
                            </td>
                            <?php endif; ?>
                            <td>
                                <a href="<?php echo $detailUrl; ?>" class="btn btn-sm <?php echo isAdmin() ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="bi bi-<?php echo isAdmin() ? 'shield-check' : 'eye'; ?>"></i> <?php echo $btnLabel; ?>
                                </a>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="<?php echo isAdmin() ? 8 : 7; ?>" class="text-center text-muted py-4">Tidak ada data menunggu verifikasi.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
