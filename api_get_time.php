<?php
/**
 * API untuk mendapatkan waktu SISTEM sebenarnya dari database server
 * Ini adalah single source of truth untuk semua klien
 * Waktu dari MySQL menggunakan timezone sistem yang sebenarnya
 */

require_once 'koneksi.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Ambil waktu sistem sebenarnya dari database
// NOW() mengembalikan waktu lokal system
// UNIX_TIMESTAMP() mengembalikan UTC timestamp
$stmt = mysqli_prepare($conn, "SELECT NOW() as waktu_lokal, UNIX_TIMESTAMP() as unix_timestamp");
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
    echo json_encode(['error' => 'Failed to fetch time from database', 'success' => false]);
    exit;
}

// Waktu lokal dari database (format: YYYY-MM-DD HH:MM:SS)
$waktu_lokal = $row['waktu_lokal'];
$unix_timestamp = (int)$row['unix_timestamp'];

// Parse waktu lokal menjadi komponen
$parts = explode(' ', $waktu_lokal);
$date_parts = explode('-', $parts[0]);
$time_parts = explode(':', $parts[1]);

$tahun = (int)$date_parts[0];
$bulan = (int)$date_parts[1];
$tanggal = (int)$date_parts[2];
$jam = (int)$time_parts[0];
$menit = (int)$time_parts[1];
$detik = (int)$time_parts[2];

// Nama hari dan bulan
$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 
              'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Hitung hari dalam minggu
$timestamp_lokal = strtotime($waktu_lokal);
$hari_index = (int)date('w', $timestamp_lokal);
$hari = $hariIndo[$hari_index];
$bulan_nama = $bulanIndo[$bulan - 1];

// Format output
$tanggal = date('Y-m-d', $timestamp_lokal);
require_once __DIR__ . '/includes/helpers_date.php';
$tanggal_format = format_date_indonesia($tanggal);
$jam_format = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':' . 
              str_pad($menit, 2, '0', STR_PAD_LEFT) . ':' . 
              str_pad($detik, 2, '0', STR_PAD_LEFT);

// Validasi format
if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $jam_format)) {
    $jam_format = sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
}

echo json_encode([
    'hari' => $hari,
     'tanggal' => $tanggal_format,
    'jam' => $jam_format,
    'waktu_lengkap' => $waktu_lokal,
    'unix_timestamp' => $unix_timestamp,
    'success' => true
]);

