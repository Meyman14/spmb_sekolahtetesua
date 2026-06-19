<?php
/**
 * API: simpan foto referensi + face descriptor (pendaftaran wajah pertama kali)
 */
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';
require_once 'includes/face_recognition.php';

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
$descriptor = $input['descriptor'] ?? null;
$fotoBase64 = $input['foto'] ?? '';

if (!is_array($descriptor) || count($descriptor) !== 128) {
    echo json_encode(['ok' => false, 'pesan' => 'Descriptor wajah tidak valid. Pastikan wajah terlihat jelas di kamera.']);
    exit;
}

$descriptorFloat = [];
foreach ($descriptor as $v) {
    if (!is_numeric($v)) {
        echo json_encode(['ok' => false, 'pesan' => 'Descriptor wajah rusak.']);
        exit;
    }
    $descriptorFloat[] = (float) $v;
}

$fotoPath = simpanFotoWajahDariBase64($username, $fotoBase64);
if ($fotoPath === null) {
    echo json_encode(['ok' => false, 'pesan' => 'Gagal menyimpan foto referensi.']);
    exit;
}

if (!simpanWajahReferensiUser($conn, $username, $descriptorFloat, $fotoPath)) {
    echo json_encode(['ok' => false, 'pesan' => 'Gagal menyimpan data wajah ke database.']);
    exit;
}

echo json_encode([
    'ok' => true,
    'pesan' => 'Foto referensi wajah berhasil disimpan. Silakan lakukan absensi.',
]);
