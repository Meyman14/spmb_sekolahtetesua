<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/helpers_date.php';

requireAdmin();

$username = $_GET['username'] ?? '';
if ($username === '') {
    echo "Usage: ?username=nerius\n";
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, username, nama_petugas, tanggal, waktu_masuk, metode_verifikasi, foto_kehadiran, foto_absen FROM daftar_hadir WHERE username = ? ORDER BY waktu_masuk DESC LIMIT 10");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

echo "<h3>Debug kehadiran untuk user: " . htmlspecialchars($username) . "</h3>\n";
echo "<table border=1 cellpadding=6 cellspacing=0>\n";
echo "<tr><th>id</th><th>tanggal</th><th>waktu_masuk</th><th>metode</th><th>foto_absen</th><th>exists?</th></tr>\n";
while ($row = mysqli_fetch_assoc($res)) {
    $fotoK = $row['foto_kehadiran'] ?: '';
    $foto = $fotoK !== '' ? $fotoK : ($row['foto_absen'] ?: '');
    $exists = 'n/a';
    if ($foto !== '') {
        $fullA = __DIR__ . '/' . $foto; // if foto is relative
        $existsA = file_exists($fullA);
        $exists = $existsA ? 'yes' : 'no';

        // if foto is a URL, try resolving path component
        if (!$existsA && (strpos($foto, 'http://') === 0 || strpos($foto, 'https://') === 0)) {
            $path = parse_url($foto, PHP_URL_PATH) ?: '';
            // try mapping web path to filesystem under project root
            $fullB = __DIR__ . $path;
            if (file_exists($fullB)) {
                $exists = 'yes (via URL path)';
                $fullA = $fullB;
            } else {
                $exists = 'MISSING: ' . htmlspecialchars($fullB);
            }
        } elseif (!$existsA) {
            $exists = 'MISSING: ' . htmlspecialchars($fullA);
        }
    }
    echo "<tr>\n";
    echo "<td>" . (int)$row['id'] . "</td>\n";
    echo "<td>" . htmlspecialchars(format_date_indonesia($row['tanggal'])) . "</td>\n";
    echo "<td>" . htmlspecialchars($row['waktu_masuk']) . "</td>\n";
    echo "<td>" . htmlspecialchars($row['metode_verifikasi']) . "</td>\n";
    echo "<td>" . ($foto ? '<a href="' . htmlspecialchars($foto) . '" target="_blank">' . htmlspecialchars($foto) . '</a>' : '') . "</td>\n";
    echo "<td>" . $exists . "</td>\n";
    echo "</tr>\n";
}
mysqli_stmt_close($stmt);
echo "</table>\n";

echo "<p>Jika foto kehadiran ada tetapi file tidak ditemukan, periksa permissions dan path file (harus ada di ". __DIR__ . "/uploads/absensi/ atau /uploads/kehadiran/ ).</p>\n";

?>
