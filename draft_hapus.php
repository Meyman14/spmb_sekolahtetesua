<?php
/**
 * Hapus permanen data siswa draft (POST)
 */
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/hapus_draft.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: status_draft.php');
    exit;
}

$siswaId = (int) ($_POST['siswa_id'] ?? 0);
$hasil = hapusSiswaDraftPermanen($conn, $siswaId);

if ($hasil['ok']) {
    $_SESSION['form_success'] = $hasil['pesan'];
} else {
    $_SESSION['form_error'] = $hasil['pesan'];
}

header('Location: status_draft.php');
exit;
