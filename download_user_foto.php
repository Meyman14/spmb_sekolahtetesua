<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';

requireAdmin();

$username = $_GET['username'] ?? '';
if ($username === '') {
    http_response_code(400);
    echo 'Missing username';
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT foto_petugas FROM users WHERE username = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row || empty($row['foto_petugas'])) {
    http_response_code(404);
    echo 'Foto user tidak ditemukan';
    exit;
}

$foto = $row['foto_petugas'];

// Resolve to filesystem path
$full = '';
if (strpos($foto, 'http://') === 0 || strpos($foto, 'https://') === 0) {
    $path = parse_url($foto, PHP_URL_PATH);
    if ($path) {
        $full = __DIR__ . $path;
    }
} else {
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
