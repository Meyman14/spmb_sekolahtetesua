<?php

/**
 * Petugas bertugas hari ini berdasarkan includes/jadwal_petugas.php
 * Menggunakan waktu dari database MySQL untuk akurasi
 *
 * @return array{hari: string, tanggal: string, jam: string, petugas: string[]}
 */
function infoPetugasHariIni(): array
{
    global $conn;
    
    $hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // Ambil waktu dari database (single source of truth)
    $stmt = mysqli_prepare($conn, "SELECT NOW() as waktu_now");
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    
    $waktu_now = $row['waktu_now'] ?? date('Y-m-d H:i:s');
    $timestamp = strtotime($waktu_now);
    
    // Parse komponen waktu
    $hari_index = (int)date('w', $timestamp);
    $tanggal_angka = (int)date('d', $timestamp);
    $bulan_angka = (int)date('m', $timestamp);
    $tahun = (int)date('Y', $timestamp);
    $jam_str = date('H:i', $timestamp);

    $w = $hari_index;
    $jadwal = require __DIR__ . '/jadwal_petugas.php';
    $petugas = $jadwal[$w] ?? [];

    require_once __DIR__ . '/helpers_date.php';
    return [
        'hari' => $hariIndo[$hari_index],
        'tanggal' => format_date_indonesia(date('Y-m-d', $timestamp)),
        'jam' => $jam_str . ' WIB',
        'petugas' => is_array($petugas) ? $petugas : [],
    ];
}
