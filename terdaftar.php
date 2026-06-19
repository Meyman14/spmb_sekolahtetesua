<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';

ensureSiswaSchema($conn);

$page_title = 'Terdaftar';
$active_menu = 'terdaftar';

$query = mysqli_query($conn, "SELECT id, nisn, nama_lengkap, jenis_kelamin, agama, tanggal_daftar, petugas_nama, petugas_username 
    FROM siswa WHERE status = 'terdaftar' ORDER BY nama_lengkap ASC");

$totalTerdaftar = mysqli_num_rows($query);

$formSuccess = $_SESSION['form_success'] ?? '';
unset($_SESSION['form_success']);

$formError = $_SESSION['form_error'] ?? '';
unset($_SESSION['form_error']);

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <?php
    $page_heading = 'Data Terdaftar';
    $page_subtitle = 'Murid yang telah diverifikasi admin — status <span class="badge bg-light text-success">Terdaftar</span>';
    $page_header_theme = 'green';
    $page_header_icon = 'bi-people-fill';
    require 'includes/page_header.php';
    ?>

    <?php if ($formSuccess !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $formSuccess; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($formError !== ''): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $formError; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card content-card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Tabel Siswa Terdaftar</span>
            <div class="d-flex align-items-center gap-2">
                <?php if (isAdmin()): ?>
                <a href="export_excel.php" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Unduh Excel
                </a>
                <?php endif; ?>
                <span class="badge bg-secondary"><?php echo $totalTerdaftar; ?> data</span>
            </div>
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
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($query) > 0):
                            while ($row = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nisn'] ?? '-'); ?></td>
                            <td>
                                <a href="terdaftar_detail.php?id=<?php echo (int) $row['id']; ?>" class="fw-semibold text-decoration-none">
                                    <?php echo htmlspecialchars($row['nama_lengkap']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                            <td><?php echo htmlspecialchars($row['agama']); ?></td>
                            <td><?php echo $row['tanggal_daftar'] ? date('d/m/Y', strtotime($row['tanggal_daftar'])) : '-'; ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars(namaPetugasPendaftar($row)); ?></span></td>
                            <td>
                                <a href="terdaftar_detail.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <?php if (isAdmin()): ?>
                                <a href="terdaftar_hapus.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data siswa ini? Data yang dihapus tidak bisa dikembalikan.');">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada siswa terdaftar.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
