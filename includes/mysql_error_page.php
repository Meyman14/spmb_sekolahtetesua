<?php
/**
 * Halaman ramah pengguna saat MySQL belum jalan (bukan fatal error PHP)
 */
function tampilkanHalamanMySqlTidakJalan(?string $detailTeknis = null): void
{
    if (headers_sent() === false) {
        header('HTTP/1.1 503 Service Unavailable');
        header('Content-Type: text/html; charset=UTF-8');
    }

    $detailTeknis = $detailTeknis ?? '';
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Belum Aktif - SPMB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .err-card { max-width: 520px; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        .err-header { background: linear-gradient(135deg, #dc2626, #b91c1c); color: #fff; padding: 24px; border-radius: 16px 16px 0 0; text-align: center; }
        .step-num { width: 28px; height: 28px; border-radius: 50%; background: #0d9488; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px; flex-shrink: 0; }
        .step-item { display: flex; align-items: flex-start; margin-bottom: 12px; text-align: left; }
    </style>
</head>
<body>
    <div class="card err-card w-100">
        <div class="err-header">
            <i class="bi bi-database-x fs-1 d-block mb-2"></i>
            <h1 class="h4 mb-1">Database MySQL Belum Berjalan</h1>
            <p class="mb-0 small opacity-90">Aplikasi SPMB tidak dapat terhubung ke server database</p>
        </div>
        <div class="card-body p-4">
            <p class="mb-3">Pesan ini biasanya muncul saat <strong>pertama kali menyalakan laptop</strong> dan MySQL di XAMPP belum di-start, atau Apache sudah jalan tetapi MySQL belum.</p>

            <h2 class="h6 fw-bold text-success mb-3">Langkah perbaikan (urutan ini penting)</h2>
            <div class="step-item">
                <span class="step-num">1</span>
                <span>Buka <strong>XAMPP Control Panel</strong> (bukan hanya folder XAMPP).</span>
            </div>
            <div class="step-item">
                <span class="step-num">2</span>
                <span>Klik <strong>Start</strong> pada baris <strong>MySQL</strong> — tunggu sampai latar hijau / tulisan &quot;Running&quot;.</span>
            </div>
            <div class="step-item">
                <span class="step-num">3</span>
                <span>Klik <strong>Start</strong> pada baris <strong>Apache</strong> (jika belum hijau).</span>
            </div>
            <div class="step-item">
                <span class="step-num">4</span>
                <span>Tunggu <strong>5–10 detik</strong>, lalu klik tombol di bawah untuk coba lagi.</span>
            </div>

            <div class="d-grid gap-2 mt-4">
                <a href="index.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-clockwise me-2"></i>Coba Login Lagi
                </a>
                <a href="cek_server.php" class="btn btn-outline-secondary">
                    <i class="bi bi-heart-pulse me-2"></i>Cek Status Server
                </a>
            </div>

            <?php if ($detailTeknis !== '' && (getenv('APP_DEBUG') === '1' || isset($_GET['debug']))): ?>
            <pre class="small text-muted mt-3 mb-0 border rounded p-2 bg-light"><?php echo htmlspecialchars($detailTeknis); ?></pre>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
    <?php
}
