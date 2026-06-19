<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';

requireAdmin();

$selected = $_POST['selected'] ?? [];
if (!is_array($selected) || count($selected) === 0) {
    header('Location: foto_petugas.php');
    exit;
}

$conn->begin_transaction();
try {
    $stmt = mysqli_prepare($conn, 'SELECT foto_petugas FROM users WHERE username = ? LIMIT 1');
    $upd = mysqli_prepare($conn, 'UPDATE users SET foto_petugas = ? WHERE username = ? LIMIT 1');
    foreach ($selected as $username) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        $foto = $row['foto_petugas'] ?? '';
        if ($foto) {
            // resolve path similar to existing download logic
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
                @unlink($full);
            }
        }
        // clear DB field
        $empty = '';
        mysqli_stmt_bind_param($upd, 'ss', $empty, $username);
        mysqli_stmt_execute($upd);
    }
    mysqli_stmt_close($stmt);
    mysqli_stmt_close($upd);
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
}

header('Location: foto_petugas.php?deleted=1');
exit;

?>
