<?php

/**
 * Previously contained face-reference and descriptor helpers.
 * Feature removed: keep only helper to save attendance capture.
 */

/** Simpan foto absen (capture saat absen) ke uploads/kehadiran/ dan kembalikan rel path */
function simpanFotoAbsenDariBase64(string $username, string $dataUrl): ?string
{
    if (!preg_match('#^data:image/(jpeg|jpg|png);base64,#i', $dataUrl, $m)) {
        return null;
    }

    $bin = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
    if ($bin === false || strlen($bin) < 100) {
        return null;
    }

    $ext = strtolower($m[1]) === 'png' ? 'png' : 'jpg';
    $dir = dirname(__DIR__) . '/uploads/kehadiran';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $safeUser = preg_replace('/[^a-z0-9_-]/i', '_', $username);
    $rel = 'uploads/kehadiran/' . $safeUser . '_' . date('Ymd_His') . '.' . $ext;
    $full = dirname(__DIR__) . '/' . $rel;

    if (file_put_contents($full, $bin) === false) {
        return null;
    }

    return $rel;
}

/** Simpan foto absen ke uploads/absensi/ dan kembalikan URL absolut (baru) */
function simpanFotoAbsensiDariBase64(string $username, string $dataUrl): ?string
{
    if (!preg_match('#^data:image/(jpeg|jpg|png);base64,#i', $dataUrl, $m)) {
        return null;
    }

    $bin = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
    if ($bin === false || strlen($bin) < 100) {
        return null;
    }

    $ext = strtolower($m[1]) === 'png' ? 'png' : 'jpg';
    $dir = dirname(__DIR__) . '/uploads/absensi';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $safeUser = preg_replace('/[^a-z0-9_-]/i', '_', $username);
    $rel = 'uploads/absensi/' . $safeUser . '_' . date('Ymd_His') . '.' . $ext;
    $full = dirname(__DIR__) . '/' . $rel;

    if (file_put_contents($full, $bin) === false) {
        return null;
    }

    // Build absolute URL for returned path so stored value is accessible via browser
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // dirname of script path, e.g. /spmb_sekolahtetesua
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    $url = $scheme . '://' . $host . ($basePath !== '' ? $basePath : '') . '/' . $rel;

    return $url;
}

// End of file
