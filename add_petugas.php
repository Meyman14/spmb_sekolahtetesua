<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';

requireAdmin();

$toAdd = [
    ['merlina', 'spmbtts2026', 'Merlina'],
    ['niatasri', 'spmbtts2026', 'Niatasri'],
];

$added = [];
$skipped = [];

$check = mysqli_prepare($conn, 'SELECT 1 FROM users WHERE username = ? LIMIT 1');
$ins = mysqli_prepare($conn, 'INSERT INTO users (username, password, nama, level) VALUES (?, ?, ?, "petugas")');

foreach ($toAdd as [$username, $password, $nama]) {
    mysqli_stmt_bind_param($check, 's', $username);
    mysqli_stmt_execute($check);
    $exists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
    if ($exists) {
        $skipped[] = $username;
        continue;
    }

    mysqli_stmt_bind_param($ins, 'sss', $username, $password, $nama);
    if (mysqli_stmt_execute($ins)) {
        $added[] = $username;
    } else {
        $skipped[] = $username;
    }
}

mysqli_stmt_close($check);
mysqli_stmt_close($ins);

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Tambah Petugas</title>
<link rel="stylesheet" href="assets/css/app-pages.css" />
</head>
<body style="padding:20px">
    <h3>Tambah akun petugas (satu kali)</h3>
    <?php if (!empty($added)): ?>
        <div style="padding:10px;background:#e6ffed;border:1px solid #a6f0b9;margin-bottom:10px">Berhasil ditambahkan: <?php echo implode(', ', $added); ?></div>
    <?php endif; ?>
    <?php if (!empty($skipped)): ?>
        <div style="padding:10px;background:#fff0f0;border:1px solid #f2a6a6">Dilewati/ gagal: <?php echo implode(', ', $skipped); ?></div>
    <?php endif; ?>
    <p>Username dan password default: <strong>spmbtts2026</strong></p>
    <p><a href="daftar_hadir.php">Kembali ke Daftar Hadir</a></p>
    <?php
    // Hapus file ini untuk keamanan setelah menambahkan akun
    $self = __FILE__;
    if (is_writable($self)) {
        @unlink($self);
        echo '<p style="color:gray;font-size:0.9em">Skrip ini telah dihapus otomatis.</p>';
    } else {
        echo '<p style="color:gray;font-size:0.9em">Skrip tidak dapat dihapus otomatis (periksa permission), silakan hapus secara manual.</p>';
    }
    ?>
</body>
</html>
