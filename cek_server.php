<?php
/**
 * Cek cepat: Apache & MySQL siap sebelum login SPMB
 */
$page_title = 'Cek Server';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Server - SPMB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container" style="max-width: 480px;">
    <h1 class="h4 mb-4 text-center"><i class="bi bi-heart-pulse text-success"></i> Status Server SPMB</h1>

    <?php
    $apacheOk = true;
    $mysqlOk = false;
    $dbOk = false;
    $mysqlPesan = '';

    mysqli_report(MYSQLI_REPORT_OFF);
    $test = @mysqli_connect('localhost', 'root', '', 'db_spmb');
    if ($test instanceof mysqli) {
        $mysqlOk = true;
        $dbOk = true;
        mysqli_close($test);
    } else {
        $mysqlPesan = mysqli_connect_error() ?: 'Tidak dapat terhubung';
        $test2 = @mysqli_connect('localhost', 'root', '');
        if ($test2 instanceof mysqli) {
            $mysqlOk = true;
            mysqli_close($test2);
            $mysqlPesan = 'MySQL jalan, database db_spmb belum ada (akan dibuat otomatis saat login)';
        }
    }
    ?>

    <ul class="list-group mb-4 shadow-sm">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Apache (halaman PHP)
            <span class="badge bg-success">Aktif</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            MySQL (database)
            <?php if ($mysqlOk): ?>
            <span class="badge bg-success">Aktif</span>
            <?php else: ?>
            <span class="badge bg-danger">Mati / belum start</span>
            <?php endif; ?>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            Database db_spmb
            <?php if ($dbOk): ?>
            <span class="badge bg-success">Terhubung</span>
            <?php else: ?>
            <span class="badge bg-warning text-dark">Belum siap</span>
            <?php endif; ?>
        </li>
    </ul>

    <?php if (!$mysqlOk): ?>
    <div class="alert alert-danger">
        <strong>MySQL belum jalan.</strong> Di XAMPP Control Panel, klik <strong>Start</strong> pada MySQL, tunggu hijau, lalu refresh halaman ini.
        <?php if ($mysqlPesan): ?>
        <br><small class="text-muted"><?php echo htmlspecialchars($mysqlPesan); ?></small>
        <?php endif; ?>
    </div>
    <?php elseif ($mysqlOk && $dbOk): ?>
    <div class="alert alert-success">
        Semua siap. Anda bisa <a href="index.php" class="alert-link">login ke SPMB</a>.
    </div>
    <?php endif; ?>

    <div class="d-grid gap-2">
        <a href="cek_server.php" class="btn btn-outline-primary">Refresh Cek</a>
        <a href="index.php" class="btn btn-primary">Ke Halaman Login</a>
    </div>

    <p class="text-center text-muted small mt-4 mb-0">
        Tip: Setiap buka laptop, start <strong>MySQL</strong> dulu di XAMPP, baru buka browser.
    </p>
</div>
</body>
</html>
