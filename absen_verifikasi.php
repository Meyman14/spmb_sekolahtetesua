<?php
/**
 * API: verifikasi wajah face-api lalu simpan hadir ke daftar_hadir
 */
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';
require_once 'includes/absensi.php';
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
$nama = $_SESSION['nama'] ?? $username;
// Descriptor-based verification removed. Accept capture photo and record attendance.
// (This intentionally disables face matching.)

// optionally save captured photo sent by client (store in uploads/absensi and save to foto_kehadiran)
$fotoData = $input['foto'] ?? null;
$fotoPath = null;
if (is_string($fotoData) && $fotoData !== '') {
    // use new storage location for captures so detail shows today's photo
    if (function_exists('simpanFotoAbsensiDariBase64')) {
        $fotoPath = simpanFotoAbsensiDariBase64($username, $fotoData);
    } else {
        // fallback to legacy function if new one not available
        $fotoPath = simpanFotoAbsenDariBase64($username, $fotoData);
    }
}

$hasil = catatAbsensiHadir($conn, $username, $nama, 'foto', $fotoPath);

echo json_encode([
    'ok' => $hasil['ok'],
    'pesan' => $hasil['pesan'],
    'redirect' => $hasil['ok'] ? 'dashboard.php' : null,
]);
