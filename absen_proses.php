<?php
/**
 * Absensi manual melalui tombol di halaman absen.php.
 */
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';
require_once 'includes/absensi.php';

requirePetugasOrAdmin();
ensureAbsensiSchema($conn);

$username = $_SESSION['username'] ?? '';
$nama = $_SESSION['nama'] ?? $username;

$result = catatAbsensiHadir($conn, $username, $nama, 'manual');
$_SESSION['absen_pesan'] = $result['pesan'];
$_SESSION['absen_pesan_tipe'] = $result['ok'] ? 'success' : 'danger';
header('Location: absen.php');
exit;
