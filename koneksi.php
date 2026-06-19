<?php
/**
 * Koneksi database SPMB — dengan penanganan error jika MySQL belum jalan di XAMPP
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_spmb';

$conn = null;

mysqli_report(MYSQLI_REPORT_OFF);

$socket = @mysqli_connect(
    $host,
    $user,
    $pass,
    $db,
    ini_get('mysqli.default_port') ?: 3306
);

if ($socket instanceof mysqli) {
    $conn = $socket;
    mysqli_set_charset($conn, 'utf8mb4');
} else {
    // Coba tanpa nama database (MySQL jalan tapi db_spmb belum ada)
    $socketTanpaDb = @mysqli_connect($host, $user, $pass);
    if ($socketTanpaDb instanceof mysqli) {
        @mysqli_query($socketTanpaDb, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        mysqli_close($socketTanpaDb);
        $conn = @mysqli_connect($host, $user, $pass, $db);
        if ($conn instanceof mysqli) {
            mysqli_set_charset($conn, 'utf8mb4');
        }
    }
}

if (!$conn instanceof mysqli) {
    require_once __DIR__ . '/includes/mysql_error_page.php';
    $err = mysqli_connect_error();
    $code = mysqli_connect_errno();
    $detail = "Error #$code: $err (host=$host, db=$db)";
    tampilkanHalamanMySqlTidakJalan($detail);
    exit;
}
