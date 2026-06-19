<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';

requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo 'Invalid id';
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT username, foto_kehadiran, foto_absen FROM daftar_hadir WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
    http_response_code(404);
    echo 'Record not found';
    exit;
}

$foto = $row['foto_kehadiran'] ?: $row['foto_absen'];
if (!$foto) {
    http_response_code(404);
    echo 'No photo available for this record';
    exit;
}

// Resolve to filesystem path
$full = '';
// If it's an absolute URL, parse path
if (strpos($foto, 'http://') === 0 || strpos($foto, 'https://') === 0) {
    $path = parse_url($foto, PHP_URL_PATH);
    if ($path) {
        $full = __DIR__ . $path;
    }
} else {
    // treat as relative to project root
    $full = __DIR__ . '/' . ltrim($foto, '/\\');
}

if (!is_file($full)) {
    http_response_code(404);
    echo 'File not found on disk: ' . htmlspecialchars($full);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $full) ?: 'application/octet-stream';
finfo_close($finfo);

$basename = basename($full);
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $basename . '"');
header('Content-Length: ' . filesize($full));
readfile($full);
exit;

?>
