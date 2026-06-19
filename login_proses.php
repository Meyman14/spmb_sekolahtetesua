<?php
session_start();
require_once __DIR__ . '/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header('Location: index.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE username = ? LIMIT 1');
if ($stmt === false) {
    $mysqlErr = mysqli_error($conn);
    error_log("[SPMB] mysqli_prepare failed: $mysqlErr");
    $_SESSION['login_error'] = 'Kesalahan database: ' . $mysqlErr;
    header('Location: index.php');
    exit;
}
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['login_error'] = 'Username tidak ditemukan.';
    header('Location: index.php');
    exit;
}

if ($password !== $data['password']) {
    $_SESSION['login_error'] = 'Password salah.';
    header('Location: index.php');
    exit;
}

$_SESSION['username'] = $data['username'];
$_SESSION['level'] = $data['level'] ?? 'petugas';
$_SESSION['nama'] = $data['nama'] ?? $data['username'];
unset($_SESSION['absensi_hadir_tanggal']);

$level = $_SESSION['level'];

if ($level === 'petugas') {
    require_once 'includes/ensure_absensi_schema.php';
    require_once 'includes/absensi.php';
    ensureAbsensiSchema($conn);

    if (petugasSudahHadirHariIni($conn, $username)) {
        header('Location: dashboard.php');
    } else {
        header('Location: absen.php');
    }
    exit;
}

header('Location: dashboard.php');
exit;
