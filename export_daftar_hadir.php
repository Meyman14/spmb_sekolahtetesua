<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';

requireAdmin();
ensureAbsensiSchema($conn);

require_once 'includes/helpers_date.php';
$raw = $_GET['tanggal'] ?? date('Y-m-d');
$tanggal = parse_date_indonesia($raw) ?? date('Y-m-d');

$stmt = mysqli_prepare($conn, 'SELECT username, nama_petugas, tanggal, waktu_masuk, metode_verifikasi, foto_kehadiran FROM daftar_hadir WHERE tanggal = ? ORDER BY waktu_masuk ASC');
mysqli_stmt_bind_param($stmt, 's', $tanggal);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[] = $r;
}
mysqli_stmt_close($stmt);

// filename use Indonesia formatted date without spaces/hyphens
$filename = 'daftar_hadir_' . str_replace(' ', '_', str_replace('-', '', str_replace(' ', '-', format_date_indonesia($tanggal)))) . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Username', 'Nama Petugas', 'Tanggal', 'Waktu Masuk', 'Metode', 'Foto Kehadiran']);
foreach ($rows as $r) {
    fputcsv($out, [$r['username'], $r['nama_petugas'], format_date_indonesia($r['tanggal']), $r['waktu_masuk'], $r['metode_verifikasi'], $r['foto_kehadiran']]);
}
fclose($out);
exit;

?>
