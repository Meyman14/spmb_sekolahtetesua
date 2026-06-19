<?php
/**
 * Proses absen: menerima descriptor + foto (base64), verifikasi, simpan capture ke uploads/absensi,
 * dan simpan record ke daftar_hadir.foto_kehadiran
 */
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';
require_once 'includes/absensi.php';
require_once 'includes/face_recognition.php';
require_once 'includes/helpers_date.php';

header('Content-Type: application/json; charset=utf-8');

requirePetugasOrAdmin();
ensureAbsensiSchema($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'pesan' => 'Metode tidak diizinkan.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'pesan' => 'Data tidak valid.']);
    exit;
}

$username = $_SESSION['username'] ?? '';
$nama = $_SESSION['nama'] ?? $username;
$liveDescriptor = $input['descriptor'] ?? null;
// Descriptor verification removed — accept photo capture and record attendance directly.
// save captured photo to uploads/absensi
$fotoData = $input['foto'] ?? null;
$fotoPath = null;
if (is_string($fotoData) && $fotoData !== '') {
    $fotoPath = simpanFotoAbsensiDariBase64($username, $fotoData);
}

$hasil = catatAbsensiHadir($conn, $username, $nama, 'foto', $fotoPath);

$response = [
    'ok' => $hasil['ok'],
    'pesan' => $hasil['pesan'],
    'redirect' => $hasil['ok'] ? 'dashboard.php' : null,
];

if ($hasil['ok']) {
    // Fetch the recorded row for confirmation
    $tanggal = date('Y-m-d');
    $q = mysqli_prepare($conn, 'SELECT id, username, nama_petugas, tanggal, waktu_masuk, metode_verifikasi, foto_kehadiran FROM daftar_hadir WHERE username = ? AND tanggal = ? LIMIT 1');
    mysqli_stmt_bind_param($q, 'ss', $username, $tanggal);
    mysqli_stmt_execute($q);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
    mysqli_stmt_close($q);
    if ($r) {
        // format tanggal for client display
        if (isset($r['tanggal'])) {
            $r['tanggal_formatted'] = format_date_indonesia($r['tanggal']);
        }
        $response['record'] = $r;
    } else {
        $response['record_found'] = false;
    }
}

echo json_encode($response);

?>
