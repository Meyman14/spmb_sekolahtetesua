<?php
// Skrip cepat untuk memeriksa koneksi database dan keberadaan tabel `users`.
// Tempatkan di folder proyek dan buka di browser: http://localhost/spmb_sekolahtetesua/check_db.php

require_once __DIR__ . '/koneksi.php';

header('Content-Type: text/plain; charset=utf-8');

echo "SPMB - Pemeriksaan Database\n";
echo "========================\n";

// Tampilkan nama database yang saat ini dipilih
$dbRes = @mysqli_query($conn, "SELECT DATABASE()");
if ($dbRes) {
    $dbRow = mysqli_fetch_row($dbRes);
    $currentDb = $dbRow[0] ?? '(tidak ada)';
} else {
    $currentDb = '(tidak diketahui)';
    error_log('[SPMB] SELECT DATABASE() failed: ' . mysqli_error($conn));
}
echo "Database saat ini: " . $currentDb . "\n";

// Cek apakah tabel `users` ada
$res = @mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if ($res && mysqli_num_rows($res) > 0) {
    echo "Tabel `users` : ADA\n";
    $countRes = @mysqli_query($conn, "SELECT COUNT(*) FROM users");
    if ($countRes) {
        $countRow = mysqli_fetch_row($countRes);
        $userCount = $countRow[0] ?? 'n/a';
    } else {
        $userCount = 'n/a';
        error_log('[SPMB] SELECT COUNT(*) FROM users failed: ' . mysqli_error($conn));
    }
    echo "Jumlah baris di `users`: " . $userCount . "\n";
} else {
    echo "Tabel `users` : TIDAK DITEMUKAN\n";
    echo "Catatan: Jika database belum diimpor, jalankan perintah impor di bawah.\n";
}

// Informasi koneksi singkat (tanpa menampilkan password)
echo "\nServer MySQL: " . mysqli_get_server_info($conn) . "\n";
echo "Client host: " . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\n";

// Petunjuk impor aman (bawah ini hanya informasi - tidak mengeksekusi apa pun)

echo "\nCara impor (Windows PowerShell) - aman cek lalu impor:\n";
echo "1) Pastikan Anda berada di folder proyek dan punya `db_spmb.sql`.\n";
echo "2) Jalankan (akan meminta password jika ada):\n";
echo "   mysql -u root -p < db_spmb.sql\n";

echo "Jika Anda ingin membuat database jika belum ada terlebih dahulu:\n";
echo "   mysql -u root -p -e \"CREATE DATABASE IF NOT EXISTS db_spmb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\"\n";

echo "Setelah impor, muat ulang halaman ini untuk verifikasi.\n";

?>