<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';

requireAdmin();
ensureAbsensiSchema($conn);

$page_title = 'Status Foto Referensi Petugas';
$active_menu = 'daftar_hadir';

require_once 'includes/head.php';
require_once 'includes/sidebar.php';

$stmt = mysqli_prepare($conn, 'SELECT username, nama, foto_wajah, face_descriptor FROM users ORDER BY username ASC');
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}
mysqli_stmt_close($stmt);
?>

<main class="main-content app-page">
    <header class="page-header page-header-blue">
        <div class="page-header-content">
            <div class="page-header-icon"><i class="bi bi-person-badge"></i></div>
            <div>
                <h1 class="page-title">Status Foto Referensi Petugas</h1>
                <p class="page-subtitle">Preview foto referensi dan status descriptor pada tabel <code>users</code>.</p>
            </div>
        </div>
    </header>

    <div class="container-fluid p-4">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Foto Referensi</th>
                                <th>Descriptor (length)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $i => $u): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><code><?php echo htmlspecialchars($u['username']); ?></code></td>
                                <td><?php echo htmlspecialchars($u['nama']); ?></td>
                                <td>
                                    <?php if (!empty($u['foto_wajah'])): ?>
                                        <img src="<?php echo htmlspecialchars($u['foto_wajah']); ?>" alt="foto" style="height:56px;object-fit:cover;border-radius:4px;" />
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $len = null;
                                    if (!empty($u['face_descriptor'])) {
                                        $decoded = json_decode($u['face_descriptor'], true);
                                        if (is_array($decoded)) $len = count($decoded);
                                    }
                                    echo $len === null ? '<span class="text-muted">0</span>' : (int) $len;
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
