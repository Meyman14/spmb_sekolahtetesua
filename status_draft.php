<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';

$page_title = 'Status Draft';
$active_menu = 'draft';

$query = mysqli_query($conn, "SELECT id, nisn, nama_lengkap, jenis_kelamin, agama, tanggal_daftar 
    FROM siswa WHERE status = 'draft' ORDER BY tanggal_daftar DESC");

$formSuccess = $_SESSION['form_success'] ?? '';
$formError = $_SESSION['form_error'] ?? '';
unset($_SESSION['form_success'], $_SESSION['form_error']);

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <?php
    $page_heading = 'Status Draft';
    $page_subtitle = 'Data pendaftaran yang belum lengkap atau perlu diperbaiki';
    $page_header_theme = 'orange';
    $page_header_icon = 'bi-pencil-square';
    require 'includes/page_header.php';
    ?>

    <?php if ($formSuccess !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($formSuccess); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($formError !== ''): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($formError); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card content-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Daftar Draft</span>
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
                            <th>Status</th>
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
                            <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                            <td><?php echo htmlspecialchars($row['agama']); ?></td>
                            <td><?php echo $row['tanggal_daftar'] ? date('d/m/Y', strtotime($row['tanggal_daftar'])) : '-'; ?></td>
                            <td><span class="badge badge-draft">Draft</span></td>
                            <td class="text-nowrap">
                                <?php if (isPetugas()): ?>
                                <a href="input_murid.php?edit=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <?php endif; ?>
                                <a href="siswa_detail.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                <form method="post" action="draft_hapus.php" class="d-inline"
                                      onsubmit="return confirm('Hapus permanen data draft ini? Data dan berkas akan dihapus dari database dan tidak dapat dikembalikan.');">
                                    <input type="hidden" name="siswa_id" value="<?php echo (int) $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus permanen">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada data berstatus Draft.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
