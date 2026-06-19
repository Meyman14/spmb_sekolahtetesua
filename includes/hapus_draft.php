<?php

require_once __DIR__ . '/dokumen.php';

/**
 * Hapus permanen siswa berstatus draft beserta berkas unggahannya.
 *
 * @return array{ok: bool, pesan: string}
 */
function hapusSiswaDraftPermanen(mysqli $conn, int $siswaId): array
{
    if ($siswaId <= 0) {
        return ['ok' => false, 'pesan' => 'ID tidak valid.'];
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id = ? AND status = 'draft' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $siswaId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$row) {
        return ['ok' => false, 'pesan' => 'Data draft tidak ditemukan atau sudah tidak berstatus draft.'];
    }

    $baseDir = dirname(__DIR__);
    foreach (array_keys(daftarDokumenSiswa()) as $field) {
        $path = $row[$field] ?? '';
        if ($path === '') {
            continue;
        }
        $fullPath = $baseDir . '/' . ltrim(str_replace(['\\', '..'], ['/', ''], $path), '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    $del = mysqli_prepare($conn, "DELETE FROM siswa WHERE id = ? AND status = 'draft' LIMIT 1");
    mysqli_stmt_bind_param($del, 'i', $siswaId);
    mysqli_stmt_execute($del);
    $terhapus = mysqli_stmt_affected_rows($del) > 0;
    mysqli_stmt_close($del);

    if (!$terhapus) {
        return ['ok' => false, 'pesan' => 'Gagal menghapus dari database.'];
    }

    // Bersihkan session edit jika sedang mengedit baris yang dihapus
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['edit_siswa_id']) && (int) $_SESSION['edit_siswa_id'] === $siswaId) {
        require_once __DIR__ . '/form_pendaftaran.php';
        formSessionClear();
    }

    $nama = $row['nama_lengkap'] ?? 'Siswa';

    return ['ok' => true, 'pesan' => 'Data draft "' . $nama . '" berhasil dihapus permanen dari database.'];
}
