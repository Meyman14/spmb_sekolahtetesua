<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';
require_once 'includes/helpers_date.php';

requireAdmin();
ensureAbsensiSchema($conn);

$page_title = 'Daftar Hadir Petugas';
$active_menu = 'daftar_hadir';

// Filter tanggal (default: hari ini)
$rawTanggal = $_GET['tanggal'] ?? date('Y-m-d');
require_once 'includes/helpers_date.php';
$filterTanggal = parse_date_indonesia($rawTanggal) ?? date('Y-m-d');
$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

 $ts = strtotime($filterTanggal);
if ($ts === false) {
    $ts = time();
    $filterTanggal = date('Y-m-d');
}
 $tglTeks = format_date_indonesia(date('Y-m-d', $ts));

// Query daftar hadir
$stmt = mysqli_prepare($conn, "
    SELECT id, username, nama_petugas, tanggal, waktu_masuk, metode_verifikasi 
    FROM daftar_hadir 
    WHERE tanggal = ? 
    ORDER BY waktu_masuk ASC
");
mysqli_stmt_bind_param($stmt, 's', $filterTanggal);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$daftarHadir = [];
while ($row = mysqli_fetch_assoc($result)) {
    $daftarHadir[] = $row;
}
mysqli_stmt_close($stmt);

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page">
    <header class="page-header page-header-blue">
        <div class="page-header-content">
            <div class="page-header-icon"><i class="bi bi-calendar-check"></i></div>
            <div>
                <h1 class="page-title">Daftar Hadir Petugas</h1>
                <p class="page-subtitle">Data kehadiran petugas harian</p>
            </div>
        </div>
    </header>

    <div class="container-fluid p-4">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Filter Tanggal</h5>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <label for="filterTanggal" class="form-label">Pilih Tanggal</label>
                        <input type="text" id="filterTanggal" name="tanggal" class="form-control" value="<?php echo htmlspecialchars(format_date_indonesia($filterTanggal)); ?>" placeholder="07 Juni 2026" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="daftar_hadir.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($tglTeks); ?>
                    <span class="badge bg-info text-dark ms-2"><?php echo count($daftarHadir); ?> Petugas Hadir</span>
                    <div class="float-end">
                        <a href="export_daftar_hadir.php?tanggal=<?php echo urlencode($filterTanggal); ?>" class="btn btn-sm btn-outline-success">Unduh CSV</a>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.print();">Cetak</button>
                    </div>
                </h5>
            </div>

            <div class="card-body">
                <?php if (empty($daftarHadir)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Belum ada petugas yang absen pada tanggal ini.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Nama Petugas</th>
                                <th>Waktu Masuk</th>
                                <th>Metode Verifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daftarHadir as $idx => $row): ?>
                            <tr>
                                <td><?php echo $idx + 1; ?></td>
                                <td>
                                    <code><?php echo htmlspecialchars($row['username']); ?></code>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nama_petugas']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <?php echo date('H:i:s', strtotime($row['waktu_masuk'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $metodeBadge = $row['metode_verifikasi'] === 'wajah' ? 'bg-primary' : 'bg-secondary';
                                    $metodeLabel = ucfirst($row['metode_verifikasi']);
                                    ?>
                                    <span class="badge <?php echo $metodeBadge; ?>">
                                        <?php echo htmlspecialchars($metodeLabel); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="kehadiran_detail.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-1">Total Hadir</h6>
                                <h3 class="text-primary mb-0"><?php echo count($daftarHadir); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-1">Foto Absen</h6>
                                <h3 class="text-info mb-0">
                                    <?php 
                                    echo count(array_filter($daftarHadir, fn($r) => $r['metode_verifikasi'] === 'foto' || $r['metode_verifikasi'] === 'wajah'));
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <h6 class="text-muted mb-1">Absensi Manual</h6>
                                <h3 class="text-secondary mb-0">
                                    <?php 
                                    echo count(array_filter($daftarHadir, fn($r) => $r['metode_verifikasi'] === 'manual'));
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
