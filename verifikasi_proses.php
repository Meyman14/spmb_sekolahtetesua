<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';
require_once 'includes/dokumen.php';

requireAdmin();
ensureSiswaSchema($conn);

$siswaId = (int) ($_POST['siswa_id'] ?? 0);
$verifPost = $_POST['verif'] ?? [];

if ($siswaId <= 0) {
    header('Location: status_final.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id = ? AND status = 'final' LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $siswaId);
mysqli_stmt_execute($stmt);
$siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$siswa) {
    $_SESSION['form_error'] = 'Data siswa tidak ditemukan atau sudah diverifikasi.';
    header('Location: status_final.php');
    exit;
}

$dokumenList = daftarDokumenSiswa();
$anyBatal = false;
$allUploadedVerified = true;
$hasUploaded = false;

foreach ($dokumenList as $field => $info) {
    $path = $siswa[$field] ?? '';
    if ($path === '') {
        continue;
    }

    $hasUploaded = true;
    $keputusan = $verifPost[$field] ?? '';

    if ($keputusan !== 'verifikasi' && $keputusan !== 'batal') {
        $_SESSION['form_error'] = 'Pilih Verifikasi atau Batal untuk setiap dokumen yang diunggah.';
        header('Location: verifikasi_detail.php?id=' . $siswaId);
        exit;
    }

    $verifCol = $info['verif_col'];
    $keputusanEsc = mysqli_real_escape_string($conn, $keputusan);
    mysqli_query($conn, "UPDATE siswa SET `$verifCol` = '$keputusanEsc' WHERE id = $siswaId");

    if ($keputusan === 'batal') {
        $anyBatal = true;
    }
    if ($keputusan !== 'verifikasi') {
        $allUploadedVerified = false;
    }
}

if (!$hasUploaded) {
    $_SESSION['form_error'] = 'Tidak ada dokumen yang dapat diverifikasi.';
    header('Location: verifikasi_detail.php?id=' . $siswaId);
    exit;
}

if ($anyBatal) {
    mysqli_query($conn, "UPDATE siswa SET status = 'draft' WHERE id = $siswaId");
    $_SESSION['form_success'] = 'Verifikasi disimpan. Ada dokumen ditolak — status siswa dikembalikan ke Draft.';
    header('Location: status_draft.php');
    exit;
}

if ($allUploadedVerified) {
    mysqli_query($conn, "UPDATE siswa SET status = 'terdaftar' WHERE id = $siswaId");
    $_SESSION['form_success'] = 'Semua dokumen terverifikasi. Siswa berhasil masuk menu Terdaftar.';
    header('Location: terdaftar.php');
    exit;
}

$_SESSION['form_error'] = 'Verifikasi belum lengkap.';
header('Location: verifikasi_detail.php?id=' . $siswaId);
exit;
