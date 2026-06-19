<?php
/**
 * Proses Hapus Data Siswa (Hanya untuk Admin)
 * File ini menghapus data siswa dari tabel siswa
 * Hanya admin yang bisa mengakses fungsi ini
 */

require_once 'includes/auth.php';
require_once 'includes/role.php';
require_once 'koneksi.php';

// Pastikan hanya admin yang bisa mengakses
if (!isAdmin()) {
    $_SESSION['access_error'] = 'Hanya Admin yang dapat menghapus data.';
    header('Location: terdaftar.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['form_error'] = 'ID siswa tidak valid.';
    header('Location: terdaftar.php');
    exit;
}

// Ambil data siswa untuk info (terutama file_foto jika perlu dihapus)
$stmt = mysqli_prepare($conn, "SELECT id, nama_lengkap, file_foto FROM siswa WHERE id = ? LIMIT 1");
if (!$stmt) {
    $_SESSION['form_error'] = 'Gagal menyiapkan query.';
    header('Location: terdaftar.php');
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$siswa) {
    $_SESSION['form_error'] = 'Data siswa tidak ditemukan.';
    header('Location: terdaftar.php');
    exit;
}

// Hapus file foto jika ada
if (!empty($siswa['file_foto'])) {
    $fotoPath = __DIR__ . '/' . ltrim($siswa['file_foto'], '/\\');
    if (is_file($fotoPath)) {
        @unlink($fotoPath);
    }
}

// Hapus data dari database
$deleteStmt = mysqli_prepare($conn, "DELETE FROM siswa WHERE id = ? LIMIT 1");
if (!$deleteStmt) {
    $_SESSION['form_error'] = 'Gagal menyiapkan query hapus.';
    header('Location: terdaftar.php');
    exit;
}

mysqli_stmt_bind_param($deleteStmt, 'i', $id);
$deleteResult = mysqli_stmt_execute($deleteStmt);
mysqli_stmt_close($deleteStmt);

if ($deleteResult && mysqli_affected_rows($conn) > 0) {
    $_SESSION['form_success'] = 'Data siswa <strong>' . htmlspecialchars($siswa['nama_lengkap']) . '</strong> berhasil dihapus.';
} else {
    $_SESSION['form_error'] = 'Gagal menghapus data siswa.';
}

header('Location: terdaftar.php');
exit;
