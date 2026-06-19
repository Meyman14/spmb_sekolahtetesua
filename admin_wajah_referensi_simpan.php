<?php
/** API admin: simpan foto referensi untuk username tertentu
 * Payload JSON: { username: string, descriptor: array(128), foto: dataUrl }
 */
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/face_recognition.php';
require_once 'includes/ensure_absensi_schema.php';

header('Content-Type: application/json; charset=utf-8');

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'pesan' => 'Akses ditolak']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'pesan' => 'Data tidak valid']);
    exit;
}

$username = $input['username'] ?? '';
$descriptor = $input['descriptor'] ?? null;
$foto = $input['foto'] ?? '';

if (!is_string($username) || trim($username) === '') {
    echo json_encode(['ok' => false, 'pesan' => 'Username tidak valid']);
    exit;
}

if (!is_array($descriptor) || count($descriptor) !== 128) {
    echo json_encode(['ok' => false, 'pesan' => 'Descriptor tidak valid']);
    exit;
}

$descriptorFloat = [];
foreach ($descriptor as $v) {
    if (!is_numeric($v)) {
        echo json_encode(['ok' => false, 'pesan' => 'Descriptor berisi data tidak numerik']);
        exit;
    }
    $descriptorFloat[] = (float) $v;
}

// Cek apakah user ada
$stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$row) {
    echo json_encode(['ok' => false, 'pesan' => 'User tidak ditemukan: ' . htmlspecialchars($username)]);
    exit;
}

// Pastikan schema absensi (kolom foto_wajah, face_descriptor) ada
ensureAbsensiSchema($conn);

$fotoPath = simpanFotoWajahDariBase64($username, $foto);
if ($fotoPath === null) {
    echo json_encode(['ok' => false, 'pesan' => 'Gagal menyimpan file foto']);
    exit;
}

$ok = simpanWajahReferensiUser($conn, $username, $descriptorFloat, $fotoPath);
if (!$ok) {
    $err = mysqli_error($conn);
    echo json_encode(['ok' => false, 'pesan' => 'Gagal menyimpan descriptor ke database', 'db_error' => $err]);
    exit;
}

echo json_encode(['ok' => true, 'pesan' => 'Referensi tersimpan untuk ' . $username]);

