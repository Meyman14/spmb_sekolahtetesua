<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';

requireAdmin();

$selected = $_POST['selected'] ?? [];
if (!is_array($selected) || count($selected) === 0) {
    header('Location: foto_petugas.php');
    exit;
}

$files = [];
$stmt = mysqli_prepare($conn, 'SELECT foto_petugas FROM users WHERE username = ? LIMIT 1');
foreach ($selected as $username) {
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $foto = $row['foto_petugas'] ?? '';
    if (!$foto) continue;
    $full = '';
    if (strpos($foto, 'http://') === 0 || strpos($foto, 'https://') === 0) {
        $path = parse_url($foto, PHP_URL_PATH);
        if ($path) {
            $full = __DIR__ . $path;
        }
    } else {
        $full = __DIR__ . '/' . ltrim($foto, '/\\');
    }
    if (is_file($full)) {
        $files[$username] = $full;
    }
}
mysqli_stmt_close($stmt);

if (count($files) === 0) {
    header('Location: foto_petugas.php?nodownload=1');
    exit;
}

$tmp = tempnam(sys_get_temp_dir(), 'fotos_') . '.zip';
$zip = new ZipArchive();
if ($zip->open($tmp, ZipArchive::CREATE) !== true) {
    header('Location: foto_petugas.php?errorzip=1');
    exit;
}

foreach ($files as $username => $path) {
    $basename = basename($path);
    $arcname = $username . '_' . $basename;
    $zip->addFile($path, $arcname);
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="foto_petugas_selected.zip"');
header('Content-Length: ' . filesize($tmp));
readfile($tmp);
@unlink($tmp);
exit;

?>
